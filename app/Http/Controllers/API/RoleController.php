<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;


class RoleController extends Controller
{
    public function assignRole(Request $request)
{
    // Validation Rules
    $validator = Validator::make($request->all(), [
        'institution_id'   => 'required|exists:institutions,id',
        'designation'      => 'required|max:255',
        'name'             => 'required|string|max:255',
        'org_email'        => 'required|email',
        'personal_email'   => 'nullable|email',
        'official_phone'   => 'required|string|max:20',
        'whatsapp_no'      => 'nullable|string|max:20',
        'personal_phone'   => 'nullable|string|max:20',
        'personal_whatsapp'=> 'nullable|string|max:20',
    ]);

    // Return Validation Errors
    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first()
        ], 422);
    }

    try {
        // Fetch the institution details by id
        $institution = DB::table('institutions')
            ->where('id', $request->institution_id)
            ->first();

        if (!$institution) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution not found.'
            ], 404);
        }

        // Check if Org Email already exists within the same institution
        $existingEmail = DB::table('institution_roles')
            ->where('institution_id', $request->institution_id)
            ->where('org_email', $request->org_email)
            ->first();

        if ($existingEmail) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The organization email is already in use within this institution. Please use a different email.'
            ], 409);
        }

        // Check if Official Phone already exists within the same institution
        $existingPhone = DB::table('institution_roles')
            ->where('institution_id', $request->institution_id)
            ->where('official_phone', $request->official_phone)
            ->first();

        if ($existingPhone) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The official phone number is already in use within this institution. Please use a different phone number.'
            ], 409);
        }

        // Generate a strong password with the first name included
        $password = $this->generateStrongPassword($request->name);

        // Insert the new role record including institution data
        DB::table('institution_roles')->insert([
            'institution_id'    => $request->institution_id,
            // Store additional institution data (adjust field names as needed)
            'institution_name'  => $institution->institution_name,
            'institution_short_code' => $institution->institution_short_code,
            'institution_type'  => $institution->type,
            'designation'       => $request->designation,
            'name'              => $request->name,
            'org_email'         => $request->org_email,
            'personal_email'    => $request->personal_email,
            'official_phone'    => $request->official_phone,
            'whatsapp_no'       => $request->whatsapp_no,
            'personal_phone'    => $request->personal_phone,
            'personal_whatsapp' => $request->personal_whatsapp,
            'password'          => bcrypt($password), // Store hashed password
            'plain_password'    => $password,         // Store plain password in separate column
            'status'            => 'Active',          // Setting default status
            'created_at'        => now()->format('Y-m-d h:i:s A'),
            'updated_at'        => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status'             => 'success',
            'message'            => 'Role assigned successfully!',
            'generated_password' => $password
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);
    }
}

    
    /**
     * Generate a strong random password that includes the user's first name.
     */
    private function generateStrongPassword($name)
    {
        // Extract first word from name
        $firstName = explode(" ", trim($name))[0];
    
        // Ensure first name is at least 3 characters long
        $firstName = substr(preg_replace('/[^a-zA-Z]/', '', $firstName), 0, 3);
        
        // Generate a random string with numbers & special characters
        $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*"), 0, 6);
    
        // Capitalize first letter of the name part
        $password = ucfirst(strtolower($firstName)) . $randomString;
    
        return $password;
    }
    
public function getRoleBasedUsers(Request $request, $institution_id)
{
    try {
        // Check if institution exists
        $institutionExists = DB::table('institutions')->where('id', $institution_id)->exists();

        if (!$institutionExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Institution not found.'
            ], 404);
        }

        // Fetch employees assigned to the institution
        $employees = DB::table('institution_roles')
            ->where('institution_id', $institution_id)
            ->select(
                "*"
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if there are employees assigned
        if ($employees->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No employees assigned to this institution.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Employees fetched successfully.',
            'data' => $employees
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);
    }
}
public function getAllRoles(Request $request)
{
    try {
        // Fetch all roles ordered by creation date (latest first)
        $roles = DB::table('institution_roles')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Roles retrieved successfully.',
            'data'    => $roles,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching roles.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function editRole(Request $request, $id)
{
    // Validation Rules
    $validator = Validator::make($request->all(), [
        'designation' => 'required|max:255',
        'name' => 'required|string|max:255',
        'org_email' => 'required|email',
        'personal_email' => 'nullable|email',
        'official_phone' => 'required|string|max:20',
        'whatsapp_no' => 'nullable|string|max:20',
        'personal_phone' => 'nullable|string|max:20',
        'personal_whatsapp' => 'nullable|string|max:20',
    ]);

    // Return Validation Errors
    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
    }

    try {
        // Check if the role exists
        $role = DB::table('institution_roles')->where('id', $id)->first();

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found.',
            ], 404);
        }

        // Check if Org Email already exists within the same institution (excluding current user)
        $existingEmail = DB::table('institution_roles')
            ->where('institution_id', $request->institution_id)
            ->where('org_email', $request->org_email)
            ->where('id', '!=', $id)
            ->first();

        if ($existingEmail) {
            return response()->json([
                'status' => 'error',
                'message' => 'The organization email is already in use within this institution. Please use a different email.'
            ], 409);
        }

        // Check if Official Phone already exists within the same institution (excluding current user)
        $existingPhone = DB::table('institution_roles')
            ->where('institution_id', $request->institution_id)
            ->where('official_phone', $request->official_phone)
            ->where('id', '!=', $id)
            ->first();

        if ($existingPhone) {
            return response()->json([
                'status' => 'error',
                'message' => 'The official phone number is already in use within this institution. Please use a different phone number.'
            ], 409);
        }

        // Update the role details
        DB::table('institution_roles')
            ->where('id', $id)
            ->update([
                'designation' => $request->designation,
                'name' => $request->name,
                'org_email' => $request->org_email,
                'personal_email' => $request->personal_email,
                'official_phone' => $request->official_phone,
                'whatsapp_no' => $request->whatsapp_no,
                'personal_phone' => $request->personal_phone,
                'personal_whatsapp' => $request->personal_whatsapp,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json(['status' => 'success', 'message' => 'Role updated successfully!'], 200);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}


public function toggleUserStatus($id)
{
    try {
        // Check if the user exists in institution_roles
        $user = DB::table('institution_roles')->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404); // Not Found HTTP status code
        }

        // Determine the new status based on the current status
        $newStatus = $user->status === 'Active' ? 'Inactive' : 'Active';

        // Update the user's status
        DB::table('institution_roles')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User status updated successfully.',
            'new_status' => $newStatus,
        ], 200);
    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the user status.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function institutionRoleLogin(Request $request)
{
    // Validate the input
    $validator = Validator::make($request->all(), [
        'identifier' => 'required', // Can be email or phone
        'password'   => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first()
        ], 422);
    }

    // Check if the user exists using either email or official phone number
    $user = DB::table('institution_roles')
        ->where('org_email', $request->identifier)
        ->orWhere('official_phone', $request->identifier)
        ->first();

    if (!$user) {
        return response()->json([
            'status'  => 'error',
            'message' => 'User not found.'
        ], 404);
    }

    // Verify password
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }

    // Fetch the institution details using the user's institution_id
    $institution = DB::table('institutions')
        ->where('id', $user->institution_id)
        ->first();

    // Generate API Token using Laravel Sanctum logic
    $token = bin2hex(random_bytes(32)); // Generate a secure token
    $hashedToken = hash('sha256', $token); // Hash the token for secure storage

    // Optionally, delete previous tokens for the user
    DB::table('personal_access_tokens')
        ->where('tokenable_id', $user->id)
        ->delete();

    // Store the new token in the database, including the institution_id
    DB::table('personal_access_tokens')->insert([
        'tokenable_type' => 'institution_roles',
        'tokenable_id'   => $user->id,
        'name'           => 'institution_role_auth',
        'token'          => $hashedToken,
        'abilities'      => json_encode(['*']),
        'designation'    => $user->designation,
        'institution_id' => $user->institution_id, // New: storing institution id
        'created_at'     => now()->format('Y-m-d h:i:s A'),
        'updated_at'     => now()->format('Y-m-d h:i:s A'),
    ]);

    return response()->json([
        'status'            => 'success',
        'role_based_token'  => $token, // Return plain token to user
        'user'              => [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->org_email,
            'official_phone'    => $user->official_phone,
            'designation'       => $user->designation,
            'institution_id'    => $user->institution_id,
            'institution_name'  => $institution ? $institution->institution_name : null, // New: institution name
            'institution_type'  => $institution ? $institution->type : null,             // New: institution type
            'status'            => $user->status
        ]
    ], 200);
}


public function institutionRoleLogout(Request $request)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'token' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
    }

    // Hash the provided token
    $hashedToken = hash('sha256', $request->token);

    // Delete the token from the database
    $deleted = DB::table('personal_access_tokens')->where('token', $hashedToken)->delete();

    if ($deleted) {
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully.'], 200);
    }

    return response()->json(['status' => 'error', 'message' => 'Token not found or already expired.'], 404);
}

/**
 * Return all "Faculty" roles for a given institution
 */
public function getFacultyByInstitution(Request $request, $institution_id)
{
    // Validate that the institution exists
    if (! DB::table('institutions')->where('id', $institution_id)->exists()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Institution not found.',
        ], 404);
    }

    try {
        // Fetch all roles for this institution where designation is Faculty
        $faculties = DB::table('institution_roles')
            ->where('institution_id', $institution_id)
            ->where('designation', 'Faculty')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Faculty roles retrieved successfully.',
            'data'    => $faculties,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching faculty roles.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
/**
 * Assign or update courses & semesters (and optionally students) to a Faculty member,
 * including institute details.
 */
public function assignFacultyCourses(Request $request)
{
    Log::info('assignFacultyCourses called', ['input' => $request->all()]);

    // 1) Validate
    $validator = Validator::make($request->all(), [
        'institution_id'   => 'required|string|max:255',
        'faculty_id'       => 'required|string|max:255',
        'assigned_by'      => 'required|string|max:255',
        'courses'          => 'required|array|min:1',
        'courses.*'        => 'required|array|min:1',
        'courses.*.*'      => 'integer|min:1',
        'students'         => 'nullable|array',
        'students.*'       => 'integer|exists:students,id',
    ]);

    if ($validator->fails()) {
        Log::warning('assignFacultyCourses validation failed', [
            'errors' => $validator->errors()->toArray()
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // 2) Fetch institution by its string ID
        $institution = DB::table('institutions')
            ->where('id', $request->institution_id)
            ->first();
        if (! $institution) {
            Log::error('assignFacultyCourses institution not found', [
                'institution_id' => $request->institution_id
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution not found.',
            ], 404);
        }
        Log::info('assignFacultyCourses fetched institution', [
            'institution' => $institution
        ]);

        // 3) Fetch faculty role by its string ID, and ensure it belongs to that institution
        $faculty = DB::table('institution_roles')
            ->where('id', $request->faculty_id)
            ->where('institution_id', $request->institution_id)
            ->where('designation', 'Faculty')
            ->first();
        if (! $faculty) {
            Log::error('assignFacultyCourses faculty not found or wrong designation', [
                'institution_id' => $request->institution_id,
                'faculty_id'     => $request->faculty_id,
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Faculty not found for this institution.',
            ], 404);
        }
        Log::info('assignFacultyCourses fetched faculty', [
            'faculty' => $faculty
        ]);

        // 4) Prepare payload
        $now = now()->format('Y-m-d h:i:s A');
        $payload = [
            'institution_id'        => $request->institution_id,
            'institution_name'      => $institution->institution_name,
            'institution_short_code'=> $institution->institution_short_code,
            'institution_type'      => $institution->type,

            'faculty_id'   => $request->faculty_id,
            'faculty_name' => $faculty->name,
            'faculty_email'=> $faculty->org_email,

            'courses'      => json_encode($request->courses),
            'students'     => $request->filled('students')
                              ? json_encode($request->students)
                              : null,

            'assigned_by'  => $request->assigned_by,
            'updated_at'   => $now,
        ];
        Log::info('assignFacultyCourses prepared payload', [
            'payload' => $payload
        ]);

        // 5) Insert or update
        $exists = DB::table('faculty_course_assignments')
            ->where('institution_id', $request->institution_id)
            ->where('faculty_id',     $request->faculty_id)
            ->exists();

        if ($exists) {
            DB::table('faculty_course_assignments')
                ->where('institution_id', $request->institution_id)
                ->where('faculty_id',     $request->faculty_id)
                ->update($payload);
            $action = 'updated';
        } else {
            $payload['created_at'] = $now;
            DB::table('faculty_course_assignments')->insert($payload);
            $action = 'inserted';
        }

        Log::info("assignFacultyCourses record {$action}", [
            'institution_id' => $request->institution_id,
            'faculty_id'     => $request->faculty_id,
            'created_at'     => $payload['created_at'] ?? null
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Faculty courses {$action} successfully.",
            'data'    => array_merge($payload, [
                'created_at' => $exists ? null : $payload['created_at']
            ]),
        ], 200);

    } catch (\Exception $e) {
        Log::error('assignFacultyCourses exception', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while assigning courses.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

/**
 * Get all courses with their semesters for a given institution, faculty and year
 */
public function getFacultyCoursesByYear(Request $request)
{
    // 1) Bring the route faculty_id into the payload
    $request->merge(['faculty_id' => $request->route('faculty_id')]);

    // 2) Validation
    $validator = Validator::make($request->all(), [
        'institution_id' => 'required|exists:institutions,id',
        'faculty_id'     => 'required|exists:institution_roles,id',
        'year'           => 'required|integer|min:1900|max:' . date('Y'),
    ]);
    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    // 3) Query using a string‐prefix match instead of whereYear()
    $year = $request->input('year');
    $assignment = DB::table('faculty_course_assignments')
        ->where('institution_id', $request->input('institution_id'))
        ->where('faculty_id',     $request->input('faculty_id'))
        ->where('created_at',      'like', $year . '%')
        ->first();

    if (! $assignment) {
        return response()->json([
            'status'  => 'error',
            'message' => 'No course assignment found for that institution, faculty and year.',
        ], 404);
    }

    // 4) Decode and return
    $courses  = json_decode($assignment->courses, true);
    $students = $assignment->students ? json_decode($assignment->students, true) : [];

    return response()->json([
        'status' => 'success',
        'data'   => [
            'institution_id'         => $assignment->institution_id,
            'faculty_id'             => $assignment->faculty_id,
            'courses_with_semesters' => $courses,
            'students'               => $students,
            'assigned_by'            => $assignment->assigned_by,
            'created_at'             => $assignment->created_at,
            'updated_at'             => $assignment->updated_at,
        ],
    ], 200);
}
/**
 * Fetch students by program code, current semester and intake year.
 */
/**
 * Fetch students by program code, current semester, intake year—and only if
 * the given faculty has that program/semester assigned.
 */
public function getStudentsByProgramSemesterYear(Request $request, $programCode, $semester, $year)
{
    // 0) Entry log
    Log::info('getStudentsByProgramSemesterYear called', compact('programCode','semester','year'));

    // 1) Validate route parameters
    $validator = Validator::make(
        ['program_code'     => $programCode,
         'current_semester' => $semester,
         'year'             => $year],
        [
            'program_code'     => 'required|string',
            'current_semester' => 'required|integer|min:1',
            'year'             => 'required|digits:4',
        ]
    );
    if ($validator->fails()) {
        Log::warning('Validation failed in getStudentsByProgramSemesterYear', [
            'errors' => $validator->errors()->toArray(),
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first(),
            'errors'  => $validator->errors()->toArray(),
        ], 422);
    }

    try {
        // 2) Build and execute the student query
        $semester = (int) $semester;

        Log::info('Building student query', compact('programCode','semester','year'));
        $students = DB::table('students')
            ->where('course', 'LIKE', '%"program_code":"' . $programCode . '"%')
            ->where('course', 'LIKE', '%"intake_year":"'   . $year        . '"%')
            ->where('current_semester', $semester)
            ->get();

        // 3) Log actual count
        $count = $students->count();
        Log::info('Fetched students', ['count' => $count]);

        // 4) Return based on emptiness
        if ($students->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No students found for the given criteria.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Students fetched successfully.',
            'data'    => $students,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error in getStudentsByProgramSemesterYear', [
            'error'       => $e->getMessage(),
            'programCode' => $programCode,
            'semester'    => $semester,
            'year'        => $year,
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching students.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function recordAttendance(Request $request)
{
    Log::info('recordAttendance called', $request->all());

    $validator = Validator::make($request->all(), [
        'institution_id' => 'required|string',
        'faculty_id'     => 'required|string',
        'program_code'   => 'required|string',
        'semester'       => 'required|integer|min:1',
        'year'           => 'required|digits:4',
        'date'           => 'required|date',
        'attendance'     => 'required|array',
        'attendance.*.student_id' => 'required|string',
        'attendance.*.present'    => 'required|boolean',
    ]);

    if ($validator->fails()) {
        Log::warning('Validation failed in recordAttendance', ['errors' => $validator->errors()->toArray()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors()->toArray(),
        ], 422);
    }

    $data = $validator->validated();

    // separate present and absent student IDs
    $presentIds = [];
    $absentIds  = [];
    foreach ($data['attendance'] as $entry) {
        if ($entry['present']) {
            $presentIds[] = $entry['student_id'];
        } else {
            $absentIds[] = $entry['student_id'];
        }
    }

    try {
        // upsert one record per date with arrays of present/absent IDs
        DB::table('attendance')->updateOrInsert(
            [
                'institution_id' => $data['institution_id'],
                'faculty_id'     => $data['faculty_id'],
                'program_code'   => $data['program_code'],
                'semester'       => $data['semester'],
                'year'           => $data['year'],
                'date'           => $data['date'],
            ],
            [
                'present_ids' => json_encode($presentIds),
                'absent_ids'  => json_encode($absentIds),
                'updated_at'  => now(),
                // if you have timestamps enabled, you can set created_at on insert:
                'created_at'  => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Attendance recorded successfully.',
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error in recordAttendance', ['error' => $e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to record attendance.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function getAttendanceByDate(Request $request)
    {
        Log::info('getAttendanceByDate called', $request->all());

        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string',
            'faculty_id'     => 'required|string',
            'program_code'   => 'required|string',
            'semester'       => 'required|integer|min:1',
            'year'           => 'required|digits:4',
            'date'           => 'required|date',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed in getAttendanceByDate', [
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()->toArray(),
            ], 422);
        }

        $data = $validator->validated();
        $semester = (int) $data['semester'];

        try {
            $record = DB::table('attendance')
                ->where('institution_id', $data['institution_id'])
                ->where('faculty_id',     $data['faculty_id'])
                ->where('program_code',   $data['program_code'])
                ->where('semester',       $semester)
                ->where('year',           $data['year'])
                ->where('date',           $data['date'])
                ->first();

            if (! $record) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No attendance record found for that date.',
                ], 404);
            }

            $presentIds = json_decode($record->present_ids, true) ?? [];
            $absentIds  = json_decode($record->absent_ids, true)  ?? [];

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'present_ids'   => $presentIds,
                    'absent_ids'    => $absentIds,
                    'total_present' => count($presentIds),
                    'total_absent'  => count($absentIds),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in getAttendanceByDate', [
                'error'  => $e->getMessage(),
                'inputs' => $data,
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch attendance.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getAttendanceReport(Request $request)
    {
        Log::info('getAttendanceReport started', $request->all());
    
        // 1) Validate inputs
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
            'program_code'   => 'required|string',
            'semester'       => 'required|integer|min:1',
            'year'           => 'required_without_all:start_date,end_date|digits:4',
            'start_date'     => 'required_without:year|date',
            'end_date'       => 'required_with:start_date|date|after_or_equal:start_date',
            'faculty_id'     => 'nullable|string|exists:institution_roles,id',
        ]);
        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }
        Log::info('Validation passed');
    
        // 2) Extract parameters
        $ins   = $request->institution_id;
        $code  = $request->program_code;
        $sem   = (int)$request->semester;
        $year  = $request->year;
        $facId = $request->faculty_id;
        $start = $request->start_date;
        $end   = $request->end_date;
        Log::info('Parameters', compact('ins','code','sem','year','facId','start','end'));
    
        // 3) Lookup faculty if provided
        $facultyInfo = null;
        if ($facId) {
            $facultyInfo = DB::table('institution_roles')
                ->where('id', $facId)
                ->select('id','name','org_email as email','designation')
                ->first();
            Log::info('Fetched facultyInfo', (array)$facultyInfo);
        } else {
            Log::info('No faculty_id provided; skipping faculty lookup');
        }
    
        // 4) Fetch attendance rows
        $attQuery = DB::table('attendance')
            ->where('institution_id', $ins)
            ->where('program_code',   $code)
            ->where('semester',       $sem);
        if ($start && $end) {
            $attQuery->whereBetween('date', [$start, $end]);
            Log::info('Filtering attendance between dates', ['start'=>$start,'end'=>$end]);
        } else {
            $attQuery->whereYear('date', $year);
            Log::info('Filtering attendance by year', ['year'=>$year]);
        }
        if ($facId) {
            $attQuery->where('faculty_id', $facId);
            Log::info('Filtering attendance by faculty_id', ['faculty_id'=>$facId]);
        }
        $attendances   = $attQuery->get(['id','date','faculty_id','present_ids']);
        $totalSessions = $attendances->count();
        Log::info('Fetched attendances', ['count'=>$totalSessions]);
    
        // 5) Build studentId → presentCount map
        $presentCounts = [];
        foreach ($attendances as $att) {
            $ids = json_decode($att->present_ids, true) ?: [];
            foreach ($ids as $sid) {
                $sidStr = (string)$sid;
                $presentCounts[$sidStr] = ($presentCounts[$sidStr] ?? 0) + 1;
            }
        }
        Log::info('Built presentCounts map', $presentCounts);
    
        // 6) Build sessions map if needed
        $sessionsByStudent = [];
        if (! $facId) {
            $facultyIds = $attendances->pluck('faculty_id')->unique()->filter()->values()->all();
            $facultyMap = DB::table('institution_roles')
                ->whereIn('id', $facultyIds)
                ->select('id','name','org_email as email','designation')
                ->get()
                ->keyBy('id');
            Log::info('Built facultyMap', ['map'=>$facultyMap->toArray()]);
    
            foreach ($attendances as $att) {
                $ids = json_decode($att->present_ids, true) ?: [];
                $sessionInfo = [
                    'attendance_id' => $att->id,
                    'date'          => $att->date,
                    'faculty'       => $facultyMap->get($att->faculty_id),
                ];
                foreach ($ids as $sid) {
                    $sidStr = (string)$sid;
                    $sessionsByStudent[$sidStr][] = $sessionInfo;
                }
            }
            Log::info('Built sessionsByStudent map', ['sessionsByStudent'=>$sessionsByStudent]);
        }
    
        // 7) Fetch students
        $students = DB::table('students')
            ->where('course',           'LIKE', '%"program_code":"'.$code.'"%')
            ->where('current_semester', $sem)
            ->get();
        Log::info('Fetched students', ['count'=>$students->count()]);
    
        // 8) Compile report
        $report = [];
        foreach ($students as $stu) {
            $stuId = (string)$stu->id;
            $presentCount = $presentCounts[$stuId] ?? 0;
            $percent = $totalSessions
                ? round(($presentCount / $totalSessions) * 100, 2)
                : 0.00;
    
            $row = [
                'student'               => $stu,
                'total_sessions'        => $totalSessions,
                'present_count'         => $presentCount,
                'attendance_percentage' => $percent,
            ];
            if (! $facId) {
                $row['sessions'] = $sessionsByStudent[$stuId] ?? [];
            }
            $report[] = $row;
            Log::info('Compiled report row', [
                'student_id'   => $stuId,
                'presentCount' => $presentCount,
                'percentage'   => $percent
            ]);
        }
    
        Log::info('getAttendanceReport completed', ['rows'=>count($report)]);
        return response()->json([
            'status'  => 'success',
            'faculty' => $facultyInfo,
            'data'    => $report,
        ], 200);
    }
    








}
