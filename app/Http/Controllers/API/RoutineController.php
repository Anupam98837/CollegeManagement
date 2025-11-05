<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RoutineController extends Controller
{
    /**
     * Add a new routine entry.
     */
    public function addRoutine(Request $request)
    {
        Log::info('[addRoutine] Received payload', $request->all());

        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|exists:institutions,_id',
            'program_code'   => 'required|string|exists:courses,program_code',
            'semester'       => 'required|integer|min:1',
            'day_of_week'    => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
            'subject_code'   => 'required|string|exists:subjects,subject_code',
            'faculty_id'     => 'nullable|exists:institution_roles,id'
        ]);

        if ($validator->fails()) {
            Log::warning('[addRoutine] Validation failed', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'institution_id' => $request->institution_id,
                'program_code'   => $request->program_code,
                'semester'       => $request->semester,
                'day_of_week'    => $request->day_of_week,
                'start_time'     => $request->start_time,
                'end_time'       => $request->end_time,
                'subject_code'   => $request->subject_code,
                'faculty_id'     => $request->faculty_id,
                'status'         => 'Active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
            Log::info('[addRoutine] Inserting routine', $data);

            DB::table('routines')->insert($data);

            Log::info('[addRoutine] Insert successful');

            return response()->json([
                'status'  => 'success',
                'message' => 'Routine added successfully.',
            ], 201);

        } catch (\Exception $e) {
            Log::error('[addRoutine] Exception on insert', ['exception' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to add routine.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch all routines for an institution (optionally filtered by program/semester).
     */
    public function viewRoutines(Request $request)
    {
        Log::info('[viewRoutines] Querying routines with', $request->all());

        $request->validate([
            'institution_id' => 'required|exists:institutions,_id',
            'program_code'   => 'nullable|exists:courses,program_code',
            'semester'       => 'nullable|integer|min:1',
        ]);

        $query = DB::table('routines')->where('institution_id', $request->institution_id);
        Log::info('[viewRoutines] Base query for institution_id='.$request->institution_id);

        if ($request->filled('program_code')) {
            $query->where('program_code', $request->program_code);
            Log::info('[viewRoutines] Applied filter program_code='.$request->program_code);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
            Log::info('[viewRoutines] Applied filter semester='.$request->semester);
        }

        $data = $query->orderBy('day_of_week')
                      ->orderBy('start_time')
                      ->get();

        Log::info('[viewRoutines] Retrieved '.count($data).' routines');

        return response()->json([
            'status'  => 'success',
            'message' => 'Routines retrieved successfully.',
            'data'    => $data,
        ], 200);
    }

    /**
     * Edit an existing routine entry.
     */
    public function editRoutine(Request $request, $id)
    {
        Log::info("[editRoutine] Updating routine id={$id}", $request->all());

        $validator = Validator::make($request->all(), [
            'day_of_week'  => 'sometimes|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'   => 'sometimes|date_format:H:i',
            'end_time'     => 'sometimes|date_format:H:i|after:start_time',
            'subject_code' => 'sometimes|string|exists:subjects,subject_code',
            'faculty_id'   => 'nullable|exists:institution_roles,id',
        ]);

        if ($validator->fails()) {
            Log::warning('[editRoutine] Validation failed', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $routine = DB::table('routines')->where('id', $id)->first();
            if (! $routine) {
                Log::warning("[editRoutine] Routine id={$id} not found");
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Routine not found.',
                ], 404);
            }

            $updates = array_merge($validator->validated(), ['updated_at' => now()]);
            Log::info("[editRoutine] Applying updates", $updates);

            DB::table('routines')->where('id', $id)->update($updates);

            Log::info("[editRoutine] Update successful for id={$id}");

            return response()->json([
                'status'  => 'success',
                'message' => 'Routine updated successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('[editRoutine] Exception on update', ['exception' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update routine.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle a routine’s Active/Inactive status.
     */
    public function toggleRoutineStatus($id)
    {
        Log::info("[toggleRoutineStatus] Toggling status for id={$id}");

        try {
            $routine = DB::table('routines')->where('id', $id)->first();
            if (! $routine) {
                Log::warning("[toggleRoutineStatus] Routine id={$id} not found");
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Routine not found.',
                ], 404);
            }

            $new = $routine->status === 'Active' ? 'Inactive' : 'Active';
            Log::info("[toggleRoutineStatus] Changing status from {$routine->status} to {$new}");

            DB::table('routines')->where('id', $id)->update([
                'status'     => $new,
                'updated_at' => now(),
            ]);

            Log::info("[toggleRoutineStatus] Status toggled for id={$id}");

            return response()->json([
                'status'  => 'success',
                'message' => "Routine status set to {$new}.",
            ], 200);

        } catch (\Exception $e) {
            Log::error('[toggleRoutineStatus] Exception toggling status', ['exception' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to toggle status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a routine entry.
     */
    public function deleteRoutine($id)
    {
        Log::info("[deleteRoutine] Deleting routine id={$id}");

        try {
            $deleted = DB::table('routines')->where('id', $id)->delete();
            if (! $deleted) {
                Log::warning("[deleteRoutine] Routine id={$id} not found or already deleted");
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Routine not found or already deleted.',
                ], 404);
            }

            Log::info("[deleteRoutine] Deleted routine id={$id}");
            return response()->json([
                'status'  => 'success',
                'message' => 'Routine deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('[deleteRoutine] Exception on delete', ['exception' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete routine.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
