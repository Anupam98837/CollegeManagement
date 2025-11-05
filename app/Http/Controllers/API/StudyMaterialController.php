<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StudyMaterialController extends Controller
{
    // 1) Add new study material
    public function addStudyMaterial(Request $request)
    {
        Log::info('addStudyMaterial called', $request->all());

        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,_id',
            'course_id'      => 'required|string|exists:courses,program_code',
            'year'           => 'required|integer|min:1900|max:' . date('Y'),
            'semester'       => 'required|integer|min:1|max:12',
            'subject_id'     => 'required|string|exists:subjects,_id',
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'material'       => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'is_public'      => 'nullable|in:Yes,No',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in addStudyMaterial', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }
        Log::info('Validation passed in addStudyMaterial');

        // look up institution, course, subject
        $inst = DB::table('institutions')->where('_id',$request->institution_id)->first();
        Log::info('Fetched institution', ['institution' => $inst]);

        $course = DB::table('courses')->where('program_code',$request->course_id)->first();
        Log::info('Fetched course', ['course' => $course]);

        $subj = DB::table('subjects')->where('id',$request->subject_id)->first();
        Log::info('Fetched subject', ['subject' => $subj]);

        $filePath = null;
        if ($request->hasFile('material')) {
            $file = $request->file('material');
            $name = uniqid('mat_') .'.'. $file->getClientOriginalExtension();
            $file->move(public_path('assets/study_material'), $name);
            $filePath = 'assets/study_material/'.$name;
            Log::info('Uploaded file', ['path' => $filePath]);
        } else {
            Log::info('No file uploaded for this material');
        }

        $insertData = [
            'institution_id'   => $request->institution_id,
            'institution_name' => $inst->institution_name,
            'course_id'        => $request->course_id,
            'course_name'      => $course->program_name,
            'year'             => $request->year,
            'semester'         => $request->semester,
            'subject_id'       => $request->subject_id,
            'subject_name'     => $subj->subject_name,
            'title'            => $request->title,
            'description'      => $request->description,
            'file_path'        => $filePath,
            'is_public'        => $request->input('is_public','No'),
            'status'           => 'Active',
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
        Log::info('Inserting study_material record', $insertData);

        DB::table('study_materials')->insert($insertData);

        Log::info('Study material inserted successfully');

        return response()->json([
            'status'  => 'success',
            'message' => 'Study material added successfully.',
        ], 201);
    }

    // 2) View / filter study materials
    public function viewStudyMaterials(Request $request)
    {
        Log::info('viewStudyMaterials called', $request->all());

        $query = DB::table('study_materials')->select('*');

        foreach (['institution_id','course_id','year','semester','subject_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
                Log::info("Filtering by {$field}", [$field => $request->input($field)]);
            }
        }

        $materials = $query->orderBy('created_at','desc')->get();
        Log::info('Retrieved materials', ['count' => $materials->count()]);

        return response()->json([
            'status'  => 'success',
            'data'    => $materials,
        ], 200);
    }

    // 3) Edit a study material
    public function editStudyMaterial(Request $request, $id)
    {
        Log::info('editStudyMaterial called', ['id' => $id, 'input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'year'        => 'required|integer|min:1900|max:' . date('Y'),
            'semester'    => 'required|integer|min:1|max:12',
            'subject_id'     => 'required|string|exists:subjects,_id',
            'material'    => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in editStudyMaterial', $validator->errors()->toArray());
            return response()->json([
                'status'=>'error',
                'errors'=>$validator->errors(),
            ],422);
        }
        Log::info('Validation passed in editStudyMaterial');

        $record = DB::table('study_materials')->where('id',$id)->first();
        if (!$record) {
            Log::error('editStudyMaterial: record not found', ['id' => $id]);
            return response()->json(['status'=>'error','message'=>'Record not found.'],404);
        }
        Log::info('Found existing record', ['record' => $record]);

        $update = [
            'title'       => $request->title,
            'description' => $request->description,
            'year'        => $request->year,
            'semester'    => $request->semester,
            'subject_id'  => $request->subject_id,
            'updated_at'  => now(),
        ];

        if ($request->hasFile('material')) {
            if ($record->file_path && file_exists(public_path($record->file_path))) {
                Log::info('Deleting old file', ['path' => $record->file_path]);
                @unlink(public_path($record->file_path));
            }
            $file = $request->file('material');
            $name = uniqid('mat_') .'.'. $file->getClientOriginalExtension();
            $file->move(public_path('assets/study_material'), $name);
            $update['file_path'] = 'assets/study_material/'.$name;
            Log::info('Uploaded new file for edit', ['path' => $update['file_path']]);
        } else {
            Log::info('No new file uploaded on edit');
        }

        Log::info('Updating study_material record', ['id' => $id, 'update' => $update]);
        DB::table('study_materials')->where('id',$id)->update($update);
        Log::info('Study material updated successfully', ['id' => $id]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Study material updated successfully.',
        ], 200);
    }

    // 4) Toggle public / not-public
    public function toggleStudyMaterialStatus($id)
    {
        Log::info('toggleStudyMaterialStatus called', ['id' => $id]);

        $mat = DB::table('study_materials')->where('id',$id)->first();
        if (!$mat) {
            Log::error('toggleStudyMaterialStatus: record not found', ['id' => $id]);
            return response()->json(['status'=>'error','message'=>'Record not found.'],404);
        }
        Log::info('Current is_public', ['is_public' => $mat->is_public]);

        $new = $mat->is_public === 'Yes' ? 'No' : 'Yes';
        DB::table('study_materials')->where('id',$id)->update([
            'is_public' => $new,
            'updated_at'=> now(),
        ]);
        Log::info('Toggled is_public', ['id' => $id, 'new_status' => $new]);

        return response()->json([
            'status'     => 'success',
            'new_status' => $new,
        ], 200);
    }

    /**
 * GET  /api/agent/{agent_uid}/dashboard
 */
public function agentDashboard(Request $request, $agentUid)
{
    Log::info('Agent dashboard request', ['agent_uid' => $agentUid]);

    // 1) Fetch agent profile
    $agent = DB::table('agent')
        ->select('id', 'uid', 'name', 'email', 'mobile', 'designation', 'status', 'created_at')
        ->where('uid', $agentUid)
        ->first();

    if (!$agent) {
        Log::warning('Agent not found for dashboard', ['agent_uid' => $agentUid]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Agent not found.',
        ], 404);
    }

    // 2) Fetch students registered by this agent
    $students = DB::table('agent_student_registrations')
        ->select('name', 'role_number', 'status', 'created_at')
        ->where('agent_uid', $agentUid)
        ->orderBy('created_at', 'desc')
        ->get();

    Log::info('Dashboard data ready', [
        'agent_uid'     => $agentUid,
        'student_count' => $students->count(),
    ]);

    // 3) Return combined payload
    return response()->json([
        'status' => 'success',
        'data'   => [
            'agent'    => $agent,
            'students' => $students,
        ],
    ], 200);
}

}
