<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function studentDashboard(Request $request, $uid)
    {
        Log::info('studentDashboard called', ['uid' => $uid]);

        try {
            // 1) Fetch the student row
            $student = DB::table('students')
                ->where('uid', $uid)
                ->first();

            if (!$student) {
                Log::warning('Student not found', ['uid' => $uid]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Student not found.',
                ], 404);
            }
            Log::info('Student record retrieved', ['id' => $student->id]);

            // 2) Decode institute JSON
            $instData = json_decode($student->institute, true) ?: [];
            $instId   = $instData['institution_id'] ?? null;
            Log::info('Decoded institute JSON', ['instData' => $instData]);

            // 3) Decode course JSON
            $courseData = json_decode($student->course, true) ?: [];
            $progCode   = $courseData['program_code'] ?? null;
            Log::info('Decoded course JSON', ['courseData' => $courseData]);

            // 4) Lookup the institution record
            $institution = $instId
                ? DB::table('institutions')->where('id', $instId)->first()
                : null;
            Log::info('Institution lookup', [
                'requested_id' => $instId,
                'found'        => (bool)$institution,
            ]);

            // 5) Lookup the course record
            $course = $progCode
                ? DB::table('courses')->where('program_code', $progCode)->first()
                : null;
            Log::info('Course lookup', [
                'requested_code' => $progCode,
                'found'          => (bool)$course,
            ]);

            // 6) Build photo URL
            $photoUrl = $student->student_photo
                ? url('assets/student_documents/' . $student->student_photo)
                : null;
            Log::info('Built photo URL', ['url' => $photoUrl]);

            // 7) Fetch notices
            Log::info('Fetching notices', compact('instId', 'progCode', 'student'));
            $notices = DB::table('notices')
                ->when($instId, fn($q) => $q->where('institution_id', $instId))
                ->when($progCode, fn($q) => $q->where('program_code', $progCode))
                ->when($student->current_semester !== null, fn($q) => $q->where('semester', (string) $student->current_semester))
                ->orderBy('created_at', 'desc')
                ->get();
            Log::info('Fetched notices', ['count' => $notices->count()]);

            // 8) Fetch events
            Log::info('Fetching upcoming events', compact('instId'));
            $events = DB::table('events')
                ->when($instId, fn($q) => $q->where('institution_id', $instId))
                ->orderBy('event_date', 'asc')
                ->get();
            Log::info('Fetched events', ['count' => $events->count()]);

            // 9) Return
            return response()->json([
                'status'      => 'success',
                'student'     => $student,
                'photo_url'   => $photoUrl,
                'institution' => $institution,
                'course'      => $course,
                'notices'     => $notices,
                'events'      => $events,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in studentDashboard', [
                'uid'   => $uid,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not load dashboard.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

     /**
     * GET  /api/agent/dashboard/{agent_uid}
     */
    public function agentDashboard(Request $request, string $agent_uid)
{
    Log::info('Agent dashboard request', ['agent_uid' => $agent_uid]);

    // 1) Fetch agent profile
    $agent = DB::table('agent')
        ->select('uid', 'name', 'email', 'mobile', 'designation', 'status')
        ->where('uid', $agent_uid)
        ->first();

    if (! $agent) {
        Log::warning('Agent not found for dashboard', ['agent_uid' => $agent_uid]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Agent not found.',
        ], 404);
    }

    // 2) Fetch all students this agent has registered
    $students = DB::table('agent_student_registrations')
        ->where('agent_uid', $agent_uid)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($s) {
            // ensure we always have a `role_number` property
            if (isset($s->role_number)) {
                $s->role_number = $s->role_number;
            } elseif (isset($s->role_no)) {
                // if your column is actually called role_no, rename it:
                $s->role_number = $s->role_no;
            } else {
                $s->role_number = '';
            }
            return $s;
        });

    // Build counts
    $total    = $students->count();
    $active   = $students->where('status','Active')->count();
    $inactive = $students->where('status','Inactive')->count();

    // Group by institution name
    $byInstitution = $students
        ->groupBy('institution_name')
        ->map(function($group, $instName) {
            return [
                'institution_name' => $instName,
                'count'            => $group->count(),
            ];
        })
        ->values();

    Log::info('Agent dashboard data prepared', [
        'agent_uid'     => $agent_uid,
        'student_count' => $total,
    ]);

    // 3) Return combined payload
    return response()->json([
        'status' => 'success',
        'data'   => [
            'agent'           => $agent,
            'counts'          => [
                'total'    => $total,
                'active'   => $active,
                'inactive' => $inactive,
            ],
            'students'        => $students,
            'by_institution'  => $byInstitution,
        ],
    ], 200);
}

}
