<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    // --------------------
    // Subject-Type Methods
    // --------------------

    public function addSubjectType(Request $request)
    {
        Log::info('addSubjectType called', ['input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,_id',
            'subject_type'   => 'required|string|max:255|unique:subject_types,subject_type,NULL,id,institution_id,' . $request->institution_id,
        ]);
        if ($validator->fails()) {
            Log::warning('addSubjectType validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            Log::info('Inserting new subject_type', ['institution_id' => $request->institution_id, 'subject_type' => $request->subject_type]);
            DB::table('subject_types')->insert([
                'institution_id' => $request->institution_id,
                'subject_type'   => $request->subject_type,
                'status'         => 'Active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            Log::info('addSubjectType succeeded', ['institution_id' => $request->institution_id, 'subject_type' => $request->subject_type]);

            return response()->json(['status' => 'success', 'message' => 'Subject type added.'], 201);
        } catch (\Exception $e) {
            Log::error('addSubjectType error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not add subject type.', 'error' => $e->getMessage()], 500);
        }
    }

    public function viewSubjectTypes(Request $request)
    {
        Log::info('viewSubjectTypes called', ['input' => $request->all()]);

        $request->validate([
            'institution_id' => 'required|string|exists:institutions,_id'
        ]);

        try {
            Log::info('Fetching subject_types for institution', ['institution_id' => $request->institution_id]);
            $types = DB::table('subject_types')
                ->where('institution_id', $request->institution_id)
                ->get();
            Log::info('viewSubjectTypes succeeded', ['count' => $types->count()]);

            return response()->json(['status' => 'success', 'data' => $types], 200);
        } catch (\Exception $e) {
            Log::error('viewSubjectTypes error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not fetch subject types.', 'error' => $e->getMessage()], 500);
        }
    }

    public function editSubjectType(Request $request, $id)
    {
        Log::info('editSubjectType called', ['id' => $id, 'input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,_id',
            'subject_type'   => 'required|string|max:255|unique:subject_types,subject_type,' . $id . ',id,institution_id,' . $request->institution_id,
        ]);
        if ($validator->fails()) {
            Log::warning('editSubjectType validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json(['status' => 'error', 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            Log::info('Updating subject_type', ['id' => $id, 'institution_id' => $request->institution_id, 'subject_type' => $request->subject_type]);
            $updated = DB::table('subject_types')
                ->where('id', $id)
                ->where('institution_id', $request->institution_id)
                ->update([
                    'subject_type' => $request->subject_type,
                    'updated_at'   => now(),
                ]);

            if (!$updated) {
                Log::warning('editSubjectType found nothing to update', ['id' => $id, 'institution_id' => $request->institution_id]);
                return response()->json(['status' => 'error', 'message' => 'Not found or no change.'], 404);
            }
            Log::info('editSubjectType succeeded', ['id' => $id]);

            return response()->json(['status' => 'success', 'message' => 'Subject type updated.'], 200);
        } catch (\Exception $e) {
            Log::error('editSubjectType error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not update subject type.', 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleSubjectTypeStatus(Request $request, $id)
    {
        Log::info('toggleSubjectTypeStatus called', ['id' => $id, 'input' => $request->all()]);

        $request->validate([
            'institution_id' => 'required|string|exists:institutions,_id'
        ]);

        try {
            Log::info('Fetching subject_type to toggle', ['id' => $id, 'institution_id' => $request->institution_id]);
            $type = DB::table('subject_types')
                ->where('id', $id)
                ->where('institution_id', $request->institution_id)
                ->first();
            if (!$type) {
                Log::warning('toggleSubjectTypeStatus not found', ['id' => $id]);
                return response()->json(['status' => 'error', 'message' => 'Subject type not found.'], 404);
            }

            $new = $type->status === 'Active' ? 'Inactive' : 'Active';
            Log::info('Toggling subject_type status', ['id' => $id, 'from' => $type->status, 'to' => $new]);
            DB::table('subject_types')
                ->where('id', $id)
                ->update(['status' => $new, 'updated_at' => now()]);

            return response()->json(['status' => 'success', 'message' => "Status set to {$new}.", 'new_status' => $new], 200);
        } catch (\Exception $e) {
            Log::error('toggleSubjectTypeStatus error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not toggle status.', 'error' => $e->getMessage()], 500);
        }
    }

    // ---------------
    // Subject Methods
    // ---------------

    public function addSubject(Request $request)
    {
        Log::info('addSubject called', ['input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'institution_id'  => 'required|string|exists:institutions,_id',
            'course_id'       => 'required|string|exists:courses,program_code',
            'year'            => 'required|integer',
            'semester'        => 'required|integer|min:1|max:12',
            'subject_name'    => 'required|string|max:255',
            'subject_code'    => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects', 'subject_code')
                    ->where('institution_id', $request->institution_id)
                    ->where('course_id', $request->course_id),
                    
            ],
            'subject_type_id' => 'required|string|exists:subject_types,id',
        ]);
        if ($validator->fails()) {
            Log::warning('addSubject validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json(['status' => 'error', 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            // Fetch institution & course details
            $inst   = DB::table('institutions')->where('_id', $request->institution_id)->first();
            $course = DB::table('courses')->where('program_code', $request->course_id)->first();

            if (!$inst || !$course) {
                return response()->json(['status' => 'error', 'message' => 'Invalid institution or course.'], 400);
            }

            Log::info('Inserting new subject', [
                'institution_id' => $request->institution_id,
                'course_id'      => $request->course_id,
                'subject_code'   => $request->subject_code
            ]);

            DB::table('subjects')->insert([
                'institution_id'   => $request->institution_id,
                'institution_name' => $inst->institution_name,
                'course_id'        => $request->course_id,
                'course_name'      => $course->program_name,
                'program_duration' => $course->program_duration,
                'program_type'     => $course->program_type,
                'year'             => $request->year,
                'semester'         => $request->semester,
                'subject_name'     => $request->subject_name,
                'subject_code'     => $request->subject_code,
                'subject_type_id'  => $request->subject_type_id,
                'status'           => 'Active',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            Log::info('addSubject succeeded', ['subject_code' => $request->subject_code]);

            return response()->json(['status' => 'success', 'message' => 'Subject added.'], 201);
        } catch (\Exception $e) {
            Log::error('addSubject error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not add subject.', 'error' => $e->getMessage()], 500);
        }
    }

    public function viewSubjects(Request $request)
    {
        Log::info('viewSubjects called', ['input' => $request->all()]);

        // Explicit validator so we can return JSON on failure
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,_id',
            'year'           => 'required|integer|min:1900|max:' . date('Y'),
            'course_id'      => 'sometimes|string|exists:courses,program_code',
            'semester'       => 'sometimes|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            Log::warning('viewSubjects validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Base query
            $q = DB::table('subjects')
                ->where('institution_id', $request->institution_id)
                ->where('year',           $request->year);

            // Optional filters
            if ($request->filled('course_id')) {
                $q->where('course_id', $request->course_id);
                Log::info('  + filtering by course_id', ['course_id' => $request->course_id]);
            }
            if ($request->filled('semester')) {
                $q->where('semester', $request->semester);
                Log::info('  + filtering by semester', ['semester' => $request->semester]);
            }

            // Execute & return
            $subjects = $q->orderBy('created_at', 'desc')->get();
            Log::info('viewSubjects succeeded', ['count' => $subjects->count()]);

            return response()->json([
                'status' => 'success',
                'data'   => $subjects,
            ], 200);

        } catch (\Exception $e) {
            Log::error('viewSubjects error', ['exception' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not fetch subjects.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function editSubject(Request $request, $id)
    {
        Log::info('editSubject called', ['id' => $id, 'input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'institution_id'  => 'required|string|exists:institutions,_id',
            'course_id'       => 'sometimes|string|exists:courses,program_code',
            'year'            => 'required|integer',
            'semester'        => 'required|integer|min:1|max:12',
            'subject_name'    => 'required|string|max:255',
            'subject_code'    => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects', 'subject_code')
                    ->ignore($id, 'id')
                    ->where('institution_id', $request->institution_id)
                    ->where('course_id', $request->course_id ?? DB::table('subjects')->where('id', $id)->value('course_id')),
            ],
            'subject_type_id' => 'required|string|exists:subject_types,id',
        ]);
        if ($validator->fails()) {
            Log::warning('editSubject validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json(['status' => 'error', 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $subj = DB::table('subjects')
                ->where('id', $id)
                ->where('institution_id', $request->institution_id)
                ->first();
            if (!$subj) {
                Log::warning('editSubject not found', ['id' => $id]);
                return response()->json(['status' => 'error', 'message' => 'Subject not found.'], 404);
            }

            Log::info('Updating subject', ['id' => $id, 'subject_code' => $request->subject_code]);
            DB::table('subjects')->where('id', $id)->update([
                'course_id'       => $request->input('course_id', $subj->course_id),
                'year'            => $request->year,
                'semester'        => $request->semester,
                'subject_name'    => $request->subject_name,
                'subject_code'    => $request->subject_code,
                'subject_type_id' => $request->subject_type_id,
                'updated_at'      => now(),
            ]);
            Log::info('editSubject succeeded', ['id' => $id]);

            return response()->json(['status' => 'success', 'message' => 'Subject updated.'], 200);
        } catch (\Exception $e) {
            Log::error('editSubject error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not update subject.', 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleSubjectStatus(Request $request, $id)
    {
        Log::info('toggleSubjectStatus called', ['id' => $id, 'input' => $request->all()]);

        $request->validate([
            'institution_id' => 'required|string|exists:institutions,_id'
        ]);

        try {
            $subj = DB::table('subjects')
                ->where('id', $id)
                ->where('institution_id', $request->institution_id)
                ->first();
            if (!$subj) {
                Log::warning('toggleSubjectStatus not found', ['id' => $id]);
                return response()->json(['status' => 'error', 'message' => 'Subject not found.'], 404);
            }

            $new = $subj->status === 'Active' ? 'Inactive' : 'Active';
            Log::info('Toggling subject status', ['id' => $id, 'from' => $subj->status, 'to' => $new]);
            DB::table('subjects')->where('id', $id)->update([ 'status' => $new, 'updated_at' => now() ]);

            return response()->json(['status' => 'success', 'message' => "Status set to {$new}.", 'new_status' => $new], 200);
        } catch (\Exception $e) {
            Log::error('toggleSubjectStatus error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Could not toggle status.', 'error' => $e->getMessage()], 500);
        }
    }
}