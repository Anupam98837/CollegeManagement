<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function studentSignup(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|digits:10',
                'password' => 'required|string|min:8|confirmed',
            ]);
            if (strlen($request->password) < 8) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The password must be at least 8 characters.',
                ], 422);
            }
            if ($validator->fails()) {
                Log::error('Validation failed.', $validator->errors()->toArray());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()->toArray(), // Converts errors to an associative array
                ], 422);
            }
    
            // Check if email is already taken
            $emailExists1 = DB::table('students_basic_data')->where('email', $request->email)->exists();
            $emailExists2 = DB::table('students')->where('email', $request->email)->exists();

            if ($emailExists1 || $emailExists2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email is already taken.',
                ], 409);
            }
    
            // Hash the password before storing it
            $hashedPassword = Hash::make($request->password);
    
            // Insert student data into the database
            $studentId = DB::table('students_basic_data')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $hashedPassword,
                'plain_password' => $request->password,
                'status' => 'Active', // Default status
                'created_at' => now()->format('Y-m-d h:i:s A'),
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Student signup successful.',
                'student_id' => $studentId,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during signup.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function studentLogin(Request $request)
{
    Log::info('Student login request received', ['request' => $request->all()]);

    // Validate input
    $validator = Validator::make($request->all(), [
        'identifier' => 'required', // Email or phone
        'password'   => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        Log::warning('Validation failed', ['errors' => $validator->errors()->first()]);
        return response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first()
        ], 422);
    }

    $student = DB::table('students')
            ->where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();
    // Retrieve the student record using email or phone from students_basic_data.

    // If not found in basic data, check in the students table.
    if (!$student) {
        // Log::info('Student not found in students_basic_data, checking students table', ['identifier' => $request->identifier]);
        $student = DB::table('students_basic_data')
        ->where('email', $request->identifier)
        ->orWhere('phone', $request->identifier)
        ->first();
    }

    if (!$student) {
        Log::error('Student not found', ['identifier' => $request->identifier]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Student not found.'
        ], 404);
    }

    // Verify password
    if (!Hash::check($request->password, $student->password)) {
        Log::error('Invalid credentials', ['student_id' => $student->id]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }

    Log::info('Password verification successful', ['student_id' => $student->id]);

    // Generate API Token
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);

    // Delete previous tokens for security
    DB::table('personal_access_tokens')->where('tokenable_id', $student->id)->delete();

    // Store the new token
    DB::table('personal_access_tokens')->insert([
        'tokenable_type' => 'students',
        'tokenable_id'   => $student->id,
        'name'           => 'student_auth',
        'token'          => $hashedToken,
        'abilities'      => json_encode(['*']),
        'designation'    => 'Student',
        'created_at'     => now()->format('Y-m-d h:i:s A'),
        'updated_at'     => now()->format('Y-m-d h:i:s A'),
    ]);

    Log::info('Token generated and stored', ['student_id' => $student->id]);

    // Build the student response payload conditionally
    $studentData = [
        'name'        => $student->name,
        'email'       => $student->email,
        'phone'       => $student->phone,
        'designation' => 'student',
    ];

    // Only include 'uid' if it is available in the student record.
    if (isset($student->uid)) {
        $studentData['uid'] = $student->uid;
    }

    return response()->json([
        'status'        => 'success',
        'student_token' => $token,
        'student'       => $studentData
    ], 200);
}


    public function studentLogout(Request $request)
{
    Log::info('Student logout request received', ['token' => $request->token]);

    // Validate token presence
    if (!$request->token) {
        Log::warning('Logout failed: No token provided');
        return response()->json(['status' => 'error', 'message' => 'No token provided'], 400);
    }

    // Hash the token to match stored format
    $hashedToken = hash('sha256', $request->token);

    // Check if token exists
    $tokenExists = DB::table('personal_access_tokens')->where('token', $hashedToken)->first();

    if (!$tokenExists) {
        Log::error('Invalid token during logout', ['token' => $request->token]);
        return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
    }

    // Delete the token from the database
    DB::table('personal_access_tokens')->where('token', $hashedToken)->delete();

    Log::info('Student logged out successfully', ['student_id' => $tokenExists->tokenable_id]);

    return response()->json(['status' => 'success', 'message' => 'Logged out successfully'], 200);
}

    

public function ragisterStudent(Request $request)
{
    try {
        $validatedData = $request->validate([
            // Step 1: Basic Details
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'alternative-phone' => 'nullable|digits:10',
            'email' => 'required|email|max:255|unique:students,email',
            'alternative-email' => 'nullable|email|max:255',
            'whatsapp-no' => 'required|digits:10',

            // Step 2: Additional Details
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:100',
            'caste' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'identity_type' => 'required|in:Aadhar,Voter ID,PAN',
            'identity_details' => 'required|string|max:20',

            // Step 3: Address
            'city' => 'required|string|max:255',
            'po' => 'required|string|max:255',
            'ps' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'pin' => 'required|digits:6',

            // Step 4: Parents Details
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'required|string|max:255',
            'father_phone' => 'required|digits:10',
            'father_email' => 'nullable|email|max:255',
            'father_street' => 'required|string|max:255',
            'father_po' => 'required|string|max:255',
            'father_ps' => 'required|string|max:255',
            'father_city' => 'required|string|max:255',
            'father_state' => 'required|string|max:255',
            'father_country' => 'required|string|max:255',
            'father_pincode' => 'required|digits:6',

            'mother_name' => 'required|string|max:255',
            'mother_occupation' => 'required|string|max:255',
            'mother_phone' => 'required|digits:10',
            'mother_email' => 'nullable|email|max:255',
            'mother_street' => 'required|string|max:255',
            'mother_po' => 'required|string|max:255',
            'mother_ps' => 'required|string|max:255',
            'mother_city' => 'required|string|max:255',
            'mother_state' => 'required|string|max:255',
            'mother_country' => 'required|string|max:255',
            'mother_pincode' => 'required|digits:6',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_occupation' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|digits:10',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_address' => 'nullable|string|max:255',
            'guardian_street' => 'nullable|string|max:255',
            'guardian_po' => 'nullable|string|max:255',
            'guardian_ps' => 'nullable|string|max:255',
            'guardian_city' => 'nullable|string|max:255',
            'guardian_state' => 'nullable|string|max:255',
            'guardian_country' => 'nullable|string|max:255',
            'guardian_pincode' => 'nullable|digits:6',

            'institute' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            // Class X Details
            'class_x_exam_name' => 'required|string|max:255',
            'class_x_institution_name' => 'required|string|max:255',
            'class_x_board' => 'required|string|max:100',
            'class_x_data' => 'required|json',
            // Class XII Details
            'class_xii_exam_name' => 'nullable|string|max:255',
            'class_xii_institution_name' => 'nullable|string|max:255',
            'class_xii_board' => 'nullable|string|max:100',
            'class_xii_data' => 'nullable|json',
            // College Details
            'college_name' => 'nullable|string|max:255',
            'college_university' => 'nullable|string|max:255',
            'college_degree' => 'nullable|string|max:255',
            'college_passing_year' => 'nullable|digits:4',
            'college_data' => 'nullable|json',
        ]);

        $classXData = json_decode($request->input('class_x_data'), true);
        $classXIIData = json_decode($request->input('class_xii_data'), true);
        $collegeData = json_decode($request->input('college_data'), true);
        $instituteData = json_decode($request->input('institute'), true);
        $courseData = json_decode($request->input('course'), true);

        // Retrieve the designation from the request header.
        $designation = $request->header('Designation') ?: '';
        \Log::info('Designation from header: ' . $designation);

        // Prepare the data array from the validated data.
        $data = $validatedData;

        // Check for duplicate email.
        $emailExists = DB::table('students')
            ->where('email', $validatedData['email'])
            ->exists();

        if ($emailExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'The email is already registered.',
            ], 400);
        }

        // If designation is admin ragister or principal, generate the role number and password.
        if (in_array(strtolower($designation), ['admin','register', 'principal'])) {
            // Generate the role number in a loop until a unique one is found.
            $uid = '';
            $maxAttempts = 5;
            $attempt = 0;
            \Log::info('Approved');


            do {
                // 1. Extract the first 4 characters from the institute name.
                $instituteName = '';
                if (is_array($instituteData) && isset($instituteData['institution_short_code'])) {
                    $instituteName = strtoupper(substr($instituteData['institution_short_code'], 0, 4));
                    $instituteName = str_pad($instituteName, 4, '0');
                } else {
                    $instituteName = 'UNKN';
                }

                // 2. Generate a random 4-digit number to place before the timestamp.
                $randomNumberBefore = mt_rand(1000, 9999);

                // 3. Current date and time in YYYYMMDDHHMMSS format.
                $timestamp = date('YmdHis');

                // 4. Extract the program code from the course data.
                $programCode = '';
                if (is_array($courseData) && isset($courseData['program_code'])) {
                    $programCode = $courseData['program_code'];
                } else {
                    $programCode = 'XXXX';
                }

                // 5. Generate another random 4-digit number to place after the program code.
                $randomNumberAfter = mt_rand(1000, 9999);

                // Combine all parts to form the role number.
                $uid = $instituteName . $randomNumberBefore . $timestamp . $programCode . $randomNumberAfter;
                $attempt++;
            } while (DB::table('students')->where('uid', $uid)->exists() && $attempt < $maxAttempts);

            // If even after maximum attempts the role number exists, return an error.
            if (DB::table('students')->where('uid', $uid)->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to generate a unique role number, please try again.',
                ], 500);
            }
            if (!empty($uid)) {
                $basicStudent = DB::table('students_basic_data')->where('email', $validatedData['email'])->first();
                if ($basicStudent) {
                    DB::table('students_basic_data')->where('email', $validatedData['email'])->update(['uid' => $uid]);
                }
            }
            // Add the generated role number.
            $data['uid'] = $uid;
            $data['status'] = 'Active';
            $data['designation'] = 'Student';
            $data['created_at'] = now()->format('Y-m-d h:i:s A');
            $data['updated_at'] = now()->format('Y-m-d h:i:s A');
            // Generate a password based on student's first name, DOB, and a 4-digit random number.
            $nameParts = explode(' ', trim($validatedData['name']));
            $firstName = isset($nameParts[0]) ? $nameParts[0] : 'User';
            $dobFormatted = date('dmY', strtotime($validatedData['date_of_birth']));
            $randomDigits = mt_rand(1000, 9999);
            $generatedPassword = $firstName . $dobFormatted . $randomDigits;

            // Store both plain and hashed passwords.
            $data['plain_password'] = $generatedPassword;
            $data['password'] = Hash::make($generatedPassword);
        } else {
            $data['status'] = 'Inactive';
            $basicStudent = DB::table('students_basic_data')->where('email', $validatedData['email'])->first();
            if ($basicStudent && !empty($basicStudent->plain_password)) {
                $data['plain_password'] = $basicStudent->plain_password;
                $data['password'] = Hash::make($basicStudent->plain_password);
            }
        }

        if (
            isset($courseData['intake_type']) &&
            strtolower($courseData['intake_type']) === 'general'
        ) {
            $data['current_semester'] = 1;
        } else {
            $data['current_semester'] = 3;
        }

        // Insert the new student record.
        $success = DB::table('students')->insert($data);
        if ($success) {
            return response()->json([
                'status' => 'success',
                'message' => 'Student registered successfully.',
                'data' => $data
            ], 201);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to register student.'
        ], 500);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

public function uploadBulkStudents(Request $request)
{
    Log::info('Bulk upload started');

    // 1) Validate that a CSV file is present
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt'
    ]);
    Log::info('CSV file validation passed');

    $file = $request->file('csv_file');
    $path = $file->getRealPath();
    Log::info("Opening CSV file at path: {$path}");

    if (!($handle = fopen($path, 'r'))) {
        Log::error('Unable to open CSV file.');
        return response()->json([
            'status'  => 'error',
            'message' => 'Unable to open CSV file.'
        ], 400);
    }

    $header    = null;
    $inserted  = [];
    $failed    = [];
    $rowNumber = 0;

    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        $rowNumber++;

        // 2) Read header row
        if (!$header) {
            $header = array_map('trim', $row);
            Log::info("Header row read: " . implode(', ', $header));
            continue;
        }

        Log::info("Processing row {$rowNumber}: " . implode(', ', $row));

        // 2a) Pad or trim the $row array so it matches header length
        $row = array_slice(array_pad($row, count($header), ''), 0, count($header));

        $data = array_combine($header, array_map('trim', $row));
        if ($data === false) {
            Log::warning("Row {$rowNumber} column count mismatch after padding");
            $failed[] = [
                'row'     => $rowNumber,
                'message' => 'Column count does not match header count.'
            ];
            continue;
        }

        // 3) Basic validation (flat CSV fields; we'll build JSON below)
        $validator = Validator::make($data, [
            'name'                 => 'required|string|max:255',
            'phone'                => 'required|digits:10',
            'alternative-phone'    => 'nullable|digits:10',
            'email'                => 'required|email|max:255|unique:students,email',
            'alternative-email'    => 'nullable|email|max:255',
            'whatsapp-no'          => 'required|digits:10',

            'date_of_birth'        => 'required|date',
            'place_of_birth'       => 'nullable|string|max:255',
            'religion'             => 'nullable|string|max:100',
            'caste'                => 'nullable|string|max:100',
            'blood_group'          => 'nullable|string|max:10',
            'identity_type'        => 'required|in:Aadhar,Voter ID,PAN',
            'identity_details'     => 'required|string|max:20',

            'city'                 => 'required|string|max:255',
            'po'                   => 'required|string|max:255',
            'ps'                   => 'required|string|max:255',
            'state'                => 'required|string|max:255',
            'country'              => 'required|string|max:255',
            'pin'                  => 'required|digits:6',

            'father_name'          => 'required|string|max:255',
            'father_occupation'    => 'required|string|max:255',
            'father_phone'         => 'required|digits:10',
            'father_email'         => 'nullable|email|max:255',
            'father_street'        => 'required|string|max:255',
            'father_po'            => 'required|string|max:255',
            'father_ps'            => 'required|string|max:255',
            'father_city'          => 'required|string|max:255',
            'father_state'         => 'required|string|max:255',
            'father_country'       => 'required|string|max:255',
            'father_pincode'       => 'required|digits:6',

            'mother_name'          => 'required|string|max:255',
            'mother_occupation'    => 'required|string|max:255',
            'mother_phone'         => 'required|digits:10',
            'mother_email'         => 'nullable|email|max:255',
            'mother_street'        => 'required|string|max:255',
            'mother_po'            => 'required|string|max:255',
            'mother_ps'            => 'required|string|max:255',
            'mother_city'          => 'required|string|max:255',
            'mother_state'         => 'required|string|max:255',
            'mother_country'       => 'required|string|max:255',
            'mother_pincode'       => 'required|digits:6',

            'guardian_name'        => 'nullable|string|max:255',
            'guardian_occupation'  => 'nullable|string|max:255',
            'guardian_phone'       => 'nullable|digits:10',
            'guardian_email'       => 'nullable|email|max:255',
            'guardian_street'      => 'nullable|string|max:255',
            'guardian_po'          => 'nullable|string|max:255',
            'guardian_ps'          => 'nullable|string|max:255',
            'guardian_city'        => 'nullable|string|max:255',
            'guardian_state'       => 'nullable|string|max:255',
            'guardian_country'     => 'nullable|string|max:255',
            'guardian_pincode'     => 'nullable|digits:6',

            'institution_id'       => 'required|string|exists:institutions,id',
            'program_code'         => 'required|string',
            'intake_type'          => 'required|in:General,Lateral',
            'intake_year'          => 'required|digits:4',
            'board'                => 'required|string',
            'fee_type'             => 'required|string',

            // Class X lists (semicolon‐delimited) are required
            'class_x_exam_name'          => 'required|string|max:255',
            'class_x_institution_name'   => 'required|string|max:255',
            'class_x_board'              => 'required|string|max:100',
            'class_x_subjects'           => 'required|string',
            'class_x_fullmarks'          => 'required|string',
            'class_x_marks'              => 'required|string',

            // Class XII may be blank
            'class_xii_exam_name'        => 'nullable|string|max:255',
            'class_xii_institution_name' => 'nullable|string|max:255',
            'class_xii_board'            => 'nullable|string|max:100',
            'class_xii_subjects'         => 'nullable|string',
            'class_xii_fullmarks'        => 'nullable|string',
            'class_xii_marks'            => 'nullable|string',

            // College may be blank
            'college_name'               => 'nullable|string|max:255',
            'college_university'         => 'nullable|string|max:255',
            'college_degree'             => 'nullable|string|max:255',
            'college_passing_year'       => 'nullable|digits:4',
            'college_semesters'          => 'nullable|string',
            'college_fullmarks'          => 'nullable|string',
            'college_marks'              => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning("Row {$rowNumber} validation failed: " . implode(', ', $validator->errors()->all()));
            $failed[] = [
                'row'    => $rowNumber,
                'errors' => $validator->errors()->all()
            ];
            continue;
        }
        Log::info("Row {$rowNumber} passed validation");

        // 4) Build JSON for class_x_data
        $xSubjects  = array_map('trim', explode(';', $data['class_x_subjects']));
        $xFullMarks = array_map('trim', explode(';', $data['class_x_fullmarks']));
        $xMarks     = array_map('trim', explode(';', $data['class_x_marks']));

        if (count($xSubjects) !== count($xFullMarks) || count($xSubjects) !== count($xMarks)) {
            Log::warning("Row {$rowNumber} Class X lists length mismatch");
            $failed[] = [
                'row'     => $rowNumber,
                'message' => 'Class X subject/fullmarks/marks counts do not match'
            ];
            continue;
        }

        $classXArray = [];
        foreach ($xSubjects as $idx => $subj) {
            $full = $xFullMarks[$idx];
            $mark = $xMarks[$idx];
            $perc = (is_numeric($full) && is_numeric($mark) && floatval($full) > 0)
                ? round((floatval($mark) / floatval($full)) * 100, 2) . '%'
                : '0%';
            $classXArray[] = [
                'subject'       => $subj,
                'fullMarks'     => $full,
                'marksObtained' => $mark,
                'percentage'    => $perc
            ];
        }
        Log::info("Row {$rowNumber} built class_x_data JSON");

        // 5) Build JSON for class_xii_data (if any)
        $classXIIArray = [];
        if (!empty($data['class_xii_subjects'])) {
            $xiiSubjects  = array_map('trim', explode(';', $data['class_xii_subjects']));
            $xiiFullMarks = array_map('trim', explode(';', $data['class_xii_fullmarks']));
            $xiiMarks     = array_map('trim', explode(';', $data['class_xii_marks']));

            if (count($xiiSubjects) !== count($xiiFullMarks) || count($xiiSubjects) !== count($xiiMarks)) {
                Log::warning("Row {$rowNumber} Class XII lists length mismatch");
                $failed[] = [
                    'row'     => $rowNumber,
                    'message' => 'Class XII subject/fullmarks/marks counts do not match'
                ];
                continue;
            }

            foreach ($xiiSubjects as $idx => $subj) {
                $full = $xiiFullMarks[$idx];
                $mark = $xiiMarks[$idx];
                $perc = (is_numeric($full) && is_numeric($mark) && floatval($full) > 0)
                    ? round((floatval($mark) / floatval($full)) * 100, 2) . '%'
                    : '0%';
                $classXIIArray[] = [
                    'subject'       => $subj,
                    'fullMarks'     => $full,
                    'marksObtained' => $mark,
                    'percentage'    => $perc
                ];
            }
            Log::info("Row {$rowNumber} built class_xii_data JSON");
        }

        // 6) Build JSON for college_data (if any)
        $collegeArray = [];
        if (!empty($data['college_semesters'])) {
            $semesters = array_map('trim', explode(';', $data['college_semesters']));
            $fulls     = array_map('trim', explode(';', $data['college_fullmarks']));
            $marks     = array_map('trim', explode(';', $data['college_marks']));

            if (count($semesters) !== count($fulls) || count($semesters) !== count($marks)) {
                Log::warning("Row {$rowNumber} College lists length mismatch");
                $failed[] = [
                    'row'     => $rowNumber,
                    'message' => 'College semester/fullmarks/marks counts do not match'
                ];
                continue;
            }

            foreach ($semesters as $idx => $sem) {
                $full = $fulls[$idx];
                $mark = $marks[$idx];
                $perc = (is_numeric($full) && is_numeric($mark) && floatval($full) > 0)
                    ? round((floatval($mark) / floatval($full)) * 100, 2) . '%'
                    : '0%';
                $collegeArray[] = [
                    'semester'      => $sem,
                    'fullMarks'     => $full,
                    'marksObtained' => $mark,
                    'percentage'    => $perc
                ];
            }
            Log::info("Row {$rowNumber} built college_data JSON");
        }

        // 7) Fetch institution metadata by ID
        $inst = DB::table('institutions')
                  ->select('institution_name','type','institution_short_code')
                  ->where('id', $data['institution_id'])
                  ->first();
        if (!$inst) {
            Log::warning("Row {$rowNumber} institution_id not found: {$data['institution_id']}");
            $failed[] = [
                'row'     => $rowNumber,
                'message' => 'Institution not found'
            ];
            continue;
        }
        Log::info("Row {$rowNumber} fetched institution: {$inst->institution_short_code}");

        // 8) Fetch intake metadata for board and fee_type
        $intakeRec = DB::table('intakes')
                    ->where('institute_id', $data['institution_id'])
                    ->where('program_code', $data['program_code'])
                    ->where('intake_type', $data['intake_type'])
                    ->where('year', (int) $data['intake_year'])
                    ->first();

        if (!$intakeRec) {
            Log::warning("Row {$rowNumber} intake not found for program_code: {$data['program_code']}, intake_type: {$data['intake_type']}, intake_year: {$data['intake_year']}");
            $failed[] = [
                'row'     => $rowNumber,
                'message' => 'Intake record not found'
            ];
            continue;
        }

        // 9) Prepare insertion array
        $insertData = [
            'name'                       => $data['name'],
            'phone'                      => $data['phone'],
            'alternative-phone'          => $data['alternative-phone'] ?: null,
            'email'                      => $data['email'],
            'alternative-email'          => $data['alternative-email'] ?: null,
            'whatsapp-no'                => $data['whatsapp-no'],

            'date_of_birth'              => $data['date_of_birth'],
            'place_of_birth'             => $data['place_of_birth'] ?: null,
            'religion'                   => $data['religion'] ?: null,
            'caste'                      => $data['caste'] ?: null,
            'blood_group'                => $data['blood_group'] ?: null,
            'identity_type'              => $data['identity_type'],
            'identity_details'           => $data['identity_details'],

            'city'                       => $data['city'],
            'po'                         => $data['po'],
            'ps'                         => $data['ps'],
            'state'                      => $data['state'],
            'country'                    => $data['country'],
            'pin'                        => $data['pin'],

            'father_name'                => $data['father_name'],
            'father_occupation'          => $data['father_occupation'],
            'father_phone'               => $data['father_phone'],
            'father_email'               => $data['father_email'] ?: null,
            'father_street'              => $data['father_street'],
            'father_po'                  => $data['father_po'],
            'father_ps'                  => $data['father_ps'],
            'father_city'                => $data['father_city'],
            'father_state'               => $data['father_state'],
            'father_country'             => $data['father_country'],
            'father_pincode'             => $data['father_pincode'],

            'mother_name'                => $data['mother_name'],
            'mother_occupation'          => $data['mother_occupation'],
            'mother_phone'               => $data['mother_phone'],
            'mother_email'               => $data['mother_email'] ?: null,
            'mother_street'              => $data['mother_street'],
            'mother_po'                  => $data['mother_po'],
            'mother_ps'                  => $data['mother_ps'],
            'mother_city'                => $data['mother_city'],
            'mother_state'               => $data['mother_state'],
            'mother_country'             => $data['mother_country'],
            'mother_pincode'             => $data['mother_pincode'],

            'guardian_name'              => $data['guardian_name'] ?: null,
            'guardian_occupation'        => $data['guardian_occupation'] ?: null,
            'guardian_phone'             => $data['guardian_phone'] ?: null,
            'guardian_email'             => $data['guardian_email'] ?: null,
            'guardian_street'            => $data['guardian_street'] ?: null,
            'guardian_po'                => $data['guardian_po'] ?: null,
            'guardian_ps'                => $data['guardian_ps'] ?: null,
            'guardian_city'              => $data['guardian_city'] ?: null,
            'guardian_state'             => $data['guardian_state'] ?: null,
            'guardian_country'           => $data['guardian_country'] ?: null,
            'guardian_pincode'           => $data['guardian_pincode'] ?: null,

            // institution JSON
            'institute' => json_encode([
                'institution_id'         => $data['institution_id'],
                'institution_name'       => $inst->institution_name,
                'institution_type'       => $inst->type,
                'institution_short_code' => $inst->institution_short_code
            ]),

            // course JSON from intake
            'course' => json_encode([
                'program_code'    => $data['program_code'],
                'program_name'    => $intakeRec->program_name,
                'program_type'    => $intakeRec->program_type,
                'intake_type'     => $intakeRec->intake_type,
                'intake_year'     => $intakeRec->year,
                'program_duration'=> $intakeRec->program_duration,
                'fee_type'         => $data['fee_type'],
                'board'            => $data['board'],
            ]),

            'class_x_exam_name'          => $data['class_x_exam_name'],
            'class_x_institution_name'   => $data['class_x_institution_name'],
            'class_x_board'              => $data['class_x_board'],
            'class_x_data'               => json_encode($classXArray),

            'class_xii_exam_name'        => $data['class_xii_exam_name'] ?: null,
            'class_xii_institution_name' => $data['class_xii_institution_name'] ?: null,
            'class_xii_board'            => $data['class_xii_board'] ?: null,
            'class_xii_data'             => json_encode($classXIIArray),

            'college_name'               => $data['college_name'] ?: null,
            'college_university'         => $data['college_university'] ?: null,
            'college_degree'             => $data['college_degree'] ?: null,
            'college_passing_year'       => $data['college_passing_year'] ?: null,
            'college_data'               => json_encode($collegeArray),
        ];

        // 10) Handle UID & password generation if header “Designation” exists
        $designation = strtolower($request->header('Designation') ?: '');
        Log::info("Row {$rowNumber} designation header: {$designation}");

        if (in_array($designation, ['admin','register','principal'])) {
            Log::info("Row {$rowNumber} generating UID/password");
            $uid = '';
            $attempt = 0;
            do {
                $instCode = strtoupper(substr($inst->institution_short_code, 0, 4));
                $instCode = str_pad($instCode, 4, '0');
                $rand1    = mt_rand(1000, 9999);
                $ts       = date('YmdHis');
                $progCode = $data['program_code'];
                $rand2    = mt_rand(1000, 9999);
                $uid      = $instCode . $rand1 . $ts . $progCode . $rand2;
                $attempt++;
            } while (DB::table('students')->where('uid', $uid)->exists() && $attempt < 5);

            if (DB::table('students')->where('uid', $uid)->exists()) {
                Log::error("Row {$rowNumber} could not generate unique UID");
                $failed[] = [
                    'row'     => $rowNumber,
                    'message' => 'Could not generate unique UID'
                ];
                continue;
            }
            Log::info("Row {$rowNumber} generated UID: {$uid}");

            // If a basic_data record exists, update its uid
            $basic = DB::table('students_basic_data')
                       ->where('email', $data['email'])
                       ->first();
            if ($basic) {
                DB::table('students_basic_data')
                  ->where('email', $data['email'])
                  ->update(['uid' => $uid]);
                Log::info("Row {$rowNumber} updated students_basic_data with UID: {$uid}");
            }

            $insertData['uid']         = $uid;
            $insertData['status']      = 'Active';
            $insertData['designation'] = 'Student';
            $insertData['created_at']  = now()->format('Y-m-d h:i:s A');
            $insertData['updated_at']  = now()->format('Y-m-d h:i:s A');

            // Generate a password
            $nameParts    = explode(' ', trim($data['name']));
            $firstName    = $nameParts[0] ?? 'User';
            $dobFormatted = date('dmY', strtotime($data['date_of_birth']));
            $randDigits   = mt_rand(1000, 9999);
            $plainPwd     = $firstName . $dobFormatted . $randDigits;
            Log::info("Row {$rowNumber} generated plain password: {$plainPwd}");

            $insertData['plain_password'] = $plainPwd;
            $insertData['password']       = Hash::make($plainPwd);
        }
        else {
            Log::info("Row {$rowNumber} marking as Inactive");
            $insertData['status'] = 'Inactive';

            $basic = DB::table('students_basic_data')
                       ->where('email', $data['email'])
                       ->first();
            if ($basic && !empty($basic->plain_password)) {
                $insertData['plain_password'] = $basic->plain_password;
                $insertData['password']       = Hash::make($basic->plain_password);
                Log::info("Row {$rowNumber} reused existing plain_password");
            }
        }

        // 11) Set current_semester based on intake_type
        if (strtolower($intakeRec->intake_type) === 'general') {
            $insertData['current_semester'] = 1;
            Log::info("Row {$rowNumber} set current_semester to 1");
        } else {
            $insertData['current_semester'] = 3;
            Log::info("Row {$rowNumber} set current_semester to 3");
        }

        // 12) Insert into ‘students’
        $ok = DB::table('students')->insert($insertData);
        if ($ok) {
            Log::info("Row {$rowNumber} inserted successfully");
            $inserted[] = $rowNumber;
        } else {
            Log::error("Row {$rowNumber} database insert failed");
            $failed[] = [
                'row'     => $rowNumber,
                'message' => 'DB insert failed'
            ];
        }
    }

    fclose($handle);
    Log::info('Bulk upload finished', ['inserted' => $inserted, 'failed' => $failed]);

    return response()->json([
        'status'   => 'partial',
        'inserted' => $inserted,
        'failed'   => $failed,
    ], 207);
}

    public function editStudent(Request $request)
{
    try {
        // Validate input data
        $validatedData = $request->validate([
            // Step 1: Basic Details
            'email' => 'required|email|max:255|exists:students,email',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|digits:10',
            'alternative-phone' => 'nullable|digits:10',
            'alternative-email' => 'nullable|email|max:255',
            'whatsapp-no' => 'nullable|digits:10',

            // Step 2: Additional Details
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:100',
            'caste' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'identity_type' => 'nullable|in:Aadhar,Voter ID,PAN',
            'identity_details' => 'nullable|string|max:20',

            // Step 3: Address
            'city' => 'nullable|string|max:255',
            'po' => 'nullable|string|max:255',
            'ps' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'pin' => 'nullable|digits:6',

            // Step 4: Parents Details
            'father_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'father_phone' => 'nullable|digits:10',
            'father_email' => 'nullable|email|max:255',
            'father_street' => 'nullable|string|max:255',
            'father_po' => 'nullable|string|max:255',
            'father_ps' => 'nullable|string|max:255',
            'father_city' => 'nullable|string|max:255',
            'father_state' => 'nullable|string|max:255',
            'father_country' => 'nullable|string|max:255',
            'father_pincode' => 'nullable|digits:6',

            'mother_name' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|digits:10',
            'mother_email' => 'nullable|email|max:255',
            'mother_street' => 'nullable|string|max:255',
            'mother_po' => 'nullable|string|max:255',
            'mother_ps' => 'nullable|string|max:255',
            'mother_city' => 'nullable|string|max:255',
            'mother_state' => 'nullable|string|max:255',
            'mother_country' => 'nullable|string|max:255',
            'mother_pincode' => 'nullable|digits:6',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_occupation' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|digits:10',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_address' => 'nullable|string|max:255',
            'guardian_street'=>'nullable|string|max:255',
            'guardian_po' => 'nullable|string|max:255',
            'guardian_ps' => 'nullable|string|max:255',
            'guardian_city' => 'nullable|string|max:255',
            'guardian_state' => 'nullable|string|max:255',
            'guardian_country' => 'nullable|string|max:255',
            'guardian_pincode' => 'nullable|digits:6',


            'institute' => 'nullable|string|exists:institutions,id',
            'course' => 'nullable|string|max:255',

            // Class X Details
            'class_x_exam_name' => 'nullable|string|max:255',
            'class_x_institution_name' => 'nullable|string|max:255',
            'class_x_board' => 'nullable|string|max:100',
            'class_x_data' => 'nullable|json',
            // Class XII Details
            'class_xii_exam_name' => 'nullable|string|max:255',
            'class_xii_institution_name' => 'nullable|string|max:255',
            'class_xii_board' => 'nullable|string|max:100',
            'class_xii_data' => 'nullable|json',
            // College Details
            'college_name' => 'nullable|string|max:255',
            'college_university' => 'nullable|string|max:255',
            'college_degree' => 'nullable|string|max:255',
            'college_passing_year' => 'nullable|digits:4', // Assuming year is a 4-digit number
            'college_data' => 'nullable|json',
        ]);

        // Prepare update data
        $updateData = array_filter($validatedData, function ($value) {
            return $value !== null && $value !== '';
        });

        if (isset($updateData['class_x_data'])) {
            $updateData['class_x_data'] = json_encode(json_decode($updateData['class_x_data'], true));
        }
        if (isset($updateData['class_xii_data'])) {
            $updateData['class_xii_data'] = json_encode(json_decode($updateData['class_xii_data'], true));
        }
        if (isset($updateData['college_data'])) {
            $updateData['college_data'] = json_encode(json_decode($updateData['college_data'], true));
        }

        // Check if student exists
        $student = DB::table('students')->where('email', $validatedData['email'])->first();
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found.',
            ], 404);
        }

        $data['updated_at'] = now()->format('Y-m-d h:i:s A');
        // Update student data
        $success = DB::table('students')
            ->where('email', $validatedData['email'])
            ->update($updateData);

        if ($success) {
            return response()->json([
                'status' => 'success',
                'message' => 'Student details updated successfully.',
                'updated_data' => $updateData
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No changes were made or failed to update student data.',
        ], 500);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}





    public function getStudentByEmail(Request $request)
    {
        try {
            // Validate the email input
            $validatedData = $request->validate([
                'email' => 'required|email',
            ]);
    
            // Search for the student by email
            $student = DB::table('students')->where('email', $validatedData['email'])->first();
          
    
            if (!$student) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No student found with the provided email.',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'status' => 'success',
                'message' => 'Student retrieved successfully.',
                'data' => $student
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving the student.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewStudentsByInstitute(Request $request)
{
    // Validate that an institute_id is provided.
    $validator = Validator::make($request->all(), [
        'institute_id' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        $instituteId = $request->input('institute_id');
        
        $students = DB::table('students')
            ->where('institute', 'LIKE', '%"institution_id":"' . $instituteId . '"%')
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'No students found for this institute.',
                'data'    => [],
            ], 200);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Students fetched successfully.',
            'data'    => $students,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching students by institute.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}





    public function uploadStudentDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:students,email',
            'student_photo' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'student_identity' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'father_photo' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'father_identity' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'mother_photo' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'mother_identity' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'guardian_photo' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'guardian_identity' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'class_x_marksheet' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'class_xii_marksheet' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'college_marksheet' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            $email = $request->input('email');
            $filePaths = [];
            foreach ($request->files as $key => $file) {
                if ($file) {
                    // Generate a unique name for the file
                    $uniqueFileName = uniqid($key . '_') . '.' . $file->getClientOriginalExtension();
            
                    // Save the file in the public/assets/student_documents directory with the unique name
                    $filePaths[$key] = $file->move(public_path('assets/student_documents'), $uniqueFileName)->getFilename();
                }
            }
            
    
            // Update the student's record with file paths
            DB::table('students')
                ->where('email', $email)
                ->update([
                    'student_photo' => $filePaths['student_photo'] ?? null,
                    'student_identity' => $filePaths['student_identity'] ?? null,
                    'father_photo' => $filePaths['father_photo'] ?? null,
                    'father_identity' => $filePaths['father_identity'] ?? null,
                    'mother_photo' => $filePaths['mother_photo'] ?? null,
                    'mother_identity' => $filePaths['mother_identity'] ?? null,
                    'guardian_photo' => $filePaths['guardian_photo'] ?? null,
                    'guardian_identity' => $filePaths['guardian_identity'] ?? null,
                    'class_x_marksheet' => $filePaths['class_x_marksheet'] ?? null,
                    'class_xii_marksheet' => $filePaths['class_xii_marksheet'] ?? null,
                    'college_marksheet' => $filePaths['college_marksheet'] ?? null,
                    'updated_at' => now()->format('Y-m-d h:i:s A'),
                ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Documents uploaded and stored successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading documents.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function viewStudents()
{
    try {
        // Fetch all student data from the database
        $students = DB::table('students')->get();

        // Check if students exist
        if ($students->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No students found.',
                'data' => [],
            ], 200);
        }

        // Return a view with the students data for a positive response
        return response()->json([
            'status' => 'success',
            'message' => 'Students fetched successfully.',
            'data' => $students,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while fetching students.',
            'error' => $e->getMessage(),
        ], 500);
    }

}

public function updateStudentDocuments(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:students,email',
        'student_photo' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'student_identity' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'father_photo' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'father_identity' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'mother_photo' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'mother_identity' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'guardian_photo' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'guardian_identity' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'class_x_marksheet' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'class_xii_marksheet' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'college_marksheet' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        $email = $request->input('email');
        $student = DB::table('students')->where('email', $email)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found.',
            ], 404);
        }

        $updateData = [];
        foreach ($request->allFiles() as $key => $file) {
            if ($file) {
                // Delete old file if exists
                if (!empty($student->$key)) {
                    $oldFilePath = public_path('assets/student_documents/' . $student->$key);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // Generate a unique name and store the file
                $uniqueFileName = uniqid($key . '_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/student_documents'), $uniqueFileName);
                $updateData[$key] = $uniqueFileName;
            }
        }

        if (!empty($updateData)) {
            $updateData['updated_at'] = now()->format('Y-m-d h:i:s A');
            DB::table('students')->where('email', $email)->update($updateData);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Documents updated successfully.',
            'updated_files' => $updateData,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating documents.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function toggleStudentStatus(Request $request)
{
    // Validate that a student's email is provided.
    $validatedData = $request->validate([
        'email' => 'required|email|exists:students,email',
    ]);

    // Retrieve the student record.
    $student = DB::table('students')->where('email', $validatedData['email'])->first();

    // If the role number is not generated yet, generate it.
    if (empty($student->uid)) {
        // Assume that institute and course data are stored as JSON in the student record.
        $instituteData = json_decode($student->institute, true);
        $courseData    = json_decode($student->course, true);

        $maxAttempts = 5;
        $attempt = 0;
        do {
            // Use the first 4 letters of the institute's name or default to 'UNKN'
            if (is_array($instituteData) && isset($instituteData['institution_short_code'])) {
                $instituteName = strtoupper(substr($instituteData['institution_short_code'], 0, 4));
                $instituteName = str_pad($instituteName, 4, '0');
            } else {
                $instituteName = 'UNKN';
            }

            // Generate a random 4-digit number before the timestamp.
            $randomNumberBefore = mt_rand(1000, 9999);

            // Use the current date and time in YYYYMMDDHHMMSS format.
            $timestamp = date('YmdHis');

            // Extract the program code from course data or default to 'XXXX'
            if (is_array($courseData) && isset($courseData['program_code'])) {
                $programCode = $courseData['program_code'];
            } else {
                $programCode = 'XXXX';
            }

            // Generate another random 4-digit number after the program code.
            $randomNumberAfter = mt_rand(1000, 9999);

            // Combine all parts to form the role number.
            $uid = $instituteName . $randomNumberBefore . $timestamp . $programCode . $randomNumberAfter;
            $attempt++;
        } while (DB::table('students')->where('uid', $uid)->exists() && $attempt < $maxAttempts);

        // If still exists after maximum attempts, return an error.
        if (DB::table('students')->where('uid', $uid)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate a unique role number. Please try again.',
            ], 500);
        }
        
        if (!empty($uid)) {
            $basicStudent = DB::table('students_basic_data')->where('email', $validatedData['email'])->first();
            if ($basicStudent) {
                DB::table('students_basic_data')->where('email', $validatedData['email'])->update(['uid' => $uid]);
            }
        }
        
    
        // Update the student record with the new role number and set status to Active.
        DB::table('students')->where('email', $validatedData['email'])->update([
            'uid' => $uid,
            'status'      => 'Active',
            'updated_at'  => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Role number generated and status set to Active.',
            'uid' => $uid,
        ], 200);
    } else {
        // If a role number already exists, simply toggle the status.
        $newStatus = ($student->status === 'Active') ? 'Inactive' : 'Active';

        DB::table('students')->where('email', $validatedData['email'])->update([
            'status'     => $newStatus,
            'updated_at' => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status'     => 'success',
            'message'    => "Student status updated to {$newStatus} successfully.",
            'new_status' => $newStatus,
        ], 200);
    }
}
public function approveStudentRegistration(Request $request)
{
    try {
        // Validate input: student email, agent email, and agent uid are required; new_email is optional.
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email',
            'agent_email' => 'required|email',
            'agent_uid'   => 'required|string',
            'new_email'   => 'nullable|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()->toArray()
            ], 422);
        }

        // Retrieve the student record from agent_student_registrations using student email, agent email, and agent uid.
        $student = DB::table('agent_student_registrations')
            ->where('email', $request->email)
            ->where('agent_email', $request->agent_email)
            ->where('agent_uid', $request->agent_uid)
            ->first();

        if (!$student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Student record not found for the given agent or agent UID does not match.'
            ], 404);
        }

        // Check if a student with the same identity type and identity details already exists in the main students table.
        if (DB::table('students')
                ->where('identity_type', $student->identity_type)
                ->where('identity_details', $student->identity_details)
                ->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Student already approved.'
            ], 409);
        }

        // Determine which email to use in the update: if new_email is provided, use that.
        if ($request->new_email) {
            // Check if the new email already exists in the main students table.
            if (DB::table('students')->where('email', $request->new_email)->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email already exists.'
                ], 409);
            }
            $targetEmail = $request->new_email;
        } else {
            // Check if the original email already exists in the main students table.
            if (DB::table('students')->where('email', $request->email)->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email already exists.'
                ], 409);
            }
            $targetEmail = $request->email;
        }

        // Decode JSON fields (if needed) for roll number generation.
        $instituteData = json_decode($student->institute, true);
        $courseData    = json_decode($student->course, true);

        // Generate a unique roll number.
        $maxAttempts = 5;
        $attempt = 0;
        $uid = '';
        do {
            if (is_array($instituteData) && isset($instituteData['institution_short_code'])) {
                $instituteName = strtoupper(substr($instituteData['institution_short_code'], 0, 4));
                $instituteName = str_pad($instituteName, 4, '0');
            } else {
                $instituteName = 'UNKN';
            }
            $randomNumberBefore = mt_rand(1000, 9999);
            $timestamp = date('YmdHis');
            if (is_array($courseData) && isset($courseData['program_code'])) {
                $programCode = $courseData['program_code'];
            } else {
                $programCode = 'XXXX';
            }
            $randomNumberAfter = mt_rand(1000, 9999);
            $uid = $instituteName . $randomNumberBefore . $timestamp . $programCode . $randomNumberAfter;
            $attempt++;
        } while (DB::table('students')->where('uid', $uid)->exists() && $attempt < $maxAttempts);

        if (DB::table('students')->where('uid', $uid)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to generate a unique roll number, please try again.',
            ], 500);
        }

        // Generate a password based on student's first name and date of birth.
        $nameParts = explode(' ', trim($student->name));
        $firstName = isset($nameParts[0]) ? $nameParts[0] : 'User';
        // Assume the student's date_of_birth field exists; otherwise, default to a constant date.
        $dob = isset($student->date_of_birth) ? $student->date_of_birth : '1970-01-01';
        $dobFormatted = date('dmY', strtotime($dob));
        $randomDigits = mt_rand(1000, 9999);
        $generatedPassword = $firstName . $dobFormatted . $randomDigits;

        // Prepare update data; update the email with the target email.
        $updateData = [
            'email'           => $targetEmail,
            'uid'             => $uid,
            'plain_password'  => $generatedPassword,
            'password'        => Hash::make($generatedPassword),
            'status'          => 'Active',
            'updated_at'      => now()->format('Y-m-d h:i:s A'),
        ];

        // Update the agent_student_registrations record with the matching agent_uid.
        DB::table('agent_student_registrations')
            ->where('email', $request->email)
            ->where('agent_email', $request->agent_email)
            ->where('agent_uid', $request->agent_uid)
            ->update($updateData);

        // Fetch the updated record.
        $updatedStudent = DB::table('agent_student_registrations')
            ->where('email', $targetEmail)
            ->where('agent_email', $request->agent_email)
            ->where('agent_uid', $request->agent_uid)
            ->first();

        // Insert a copy into the main students table.
        $insertData = (array)$updatedStudent;
        // Remove the primary key if it exists so a new one can be generated.
        if (isset($insertData['id'])) {
            unset($insertData['id']);
        }
        $insertSuccess = DB::table('students')->insert($insertData);

        if ($insertSuccess) {
            return response()->json([
                'status'         => 'success',
                'message'        => 'Student approved successfully.',
                'uid'            => $uid,
                'plain_password' => $generatedPassword,
            ], 200);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to copy student record to main database.',
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred during approval: ' . $e->getMessage(),
        ], 500);
    }
}



public function changeStudentEmailByUid(Request $request)
{
    // Validate that both uid and new_email are provided, and new_email is unique.
    $validator = Validator::make($request->all(), [
        'uid'       => 'required|string',
        'new_email' => 'required|email|max:255|unique:students,email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors()->toArray(),
        ], 422);
    }

    try {
        // Find the student by uid
        $student = DB::table('students')->where('uid', $request->uid)->first();

        if (!$student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Student not found with the provided uid.',
            ], 404);
        }

        // Retrieve the current email and previous_emails (if any)
        $currentEmail = $student->email;
        $previousEmails = [];
        if (!empty($student->previous_emails)) {
            $previousEmails = json_decode($student->previous_emails, true) ?: [];
        }
        
        // Add the current email to the beginning of the array
        if ($currentEmail) {
            array_unshift($previousEmails, $currentEmail);
        }
        // Keep only the last 3 emails
        $previousEmails = array_slice($previousEmails, 0, 3);

        // Update the student's email and previous_emails field
        DB::table('students')
            ->where('uid', $request->uid)
            ->update([
                'email'           => $request->new_email,
                'previous_emails' => json_encode($previousEmails),
                'updated_at'      => now()->format('Y-m-d h:i:s A'),
            ]);
        DB::table('students_basic_data')
            ->where('uid', $request->uid)
            ->update([
                'email'           => $request->new_email,
                'previous_emails' => json_encode($previousEmails),
                'updated_at'      => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Student email updated successfully.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while updating the email.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function changeStudentPassword(Request $request)
{
    // Validate inputs – note that 'uid' is now optional if 'email' is provided.
    $validator = Validator::make($request->all(), [
        'uid'              => 'nullable|string',
        'email'            => 'nullable|email',
        'current_password' => 'required|string',
        'new_password'     => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors()->toArray(),
        ], 422);
    }

    try {
        // First try to find the student using uid.
        $student = null;
        if ($request->filled('uid')) {
            $student = DB::table('students')->where('uid', $request->uid)->first();
        }

        // If not found by uid and an email was provided, try to find the student by email.
        if (!$student && $request->filled('email')) {
            $student = DB::table('students')->where('email', $request->email)->first();
        }

        if (!$student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Student not found with the provided uid or email.',
            ], 404);
        }

        // Check that current password is correct
        if (!Hash::check($request->current_password, $student->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Current password is incorrect.',
            ], 401);
        }

        // Check new password is not same as current
        if (Hash::check($request->new_password, $student->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'New password cannot be the same as the current password.',
            ], 422);
        }

        // Retrieve previous passwords from the student record (stored as JSON)
        $previousPasswords = [];
        if (!empty($student->previous_passwords)) {
            $previousPasswords = json_decode($student->previous_passwords, true) ?: [];
        }

        // Check new password against previous passwords
        foreach ($previousPasswords as $oldPasswordHash) {
            if (Hash::check($request->new_password, $oldPasswordHash)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You have used that password before. Please choose a different password.',
                ], 422);
            }
        }

        // Update previous passwords: Prepend the current password and keep only the last 3 entries.
        array_unshift($previousPasswords, $student->password);
        $previousPasswords = array_slice($previousPasswords, 0, 3);

        // Hash the new password
        $newHashedPassword = Hash::make($request->new_password);

        // Update the student record
        DB::table('students')
            ->where('id', $student->id)
            ->update([
                'password'           => $newHashedPassword,
                'plain_password'     => $request->new_password,
                'previous_passwords' => json_encode($previousPasswords),
                'updated_at'         => now()->format('Y-m-d h:i:s A'),
            ]);
        DB::table('students_basic_data')
            ->where('uid', $request->uid)
            ->update([
                'password'           => $newHashedPassword,
                'plain_password'     => $request->new_password,
                'previous_passwords' => json_encode($previousPasswords),
                'updated_at'         => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Password updated successfully.',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while updating the password.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

/**
 * Promote student(s) by incrementing their current_semester by 1.
 * 
 * Request payload:
 * - student_ids (optional): array of student IDs to promote.
 *    If omitted or empty, *all* students will be promoted.
 */
public function promoteStudents(Request $request)
{
    \Log::info('PromoteStudents: request received', ['input' => $request->all()]);

    // Validate input: student_uids should be an array of strings matching existing uids
    $validator = Validator::make($request->all(), [
        'student_uids'    => 'sometimes|array',
        'student_uids.*'  => 'string|exists:students,uid',
    ]);
    if ($validator->fails()) {
        \Log::warning('PromoteStudents: validation failed', ['errors' => $validator->errors()->toArray()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }
    \Log::info('PromoteStudents: validation passed');

    // Build query
    $query = DB::table('students');
    if ($request->filled('student_uids')) {
        $uids = $request->input('student_uids');
        \Log::info('PromoteStudents: filtering by UIDs', ['student_uids' => $uids]);
        $query->whereIn('uid', $uids);
    } else {
        \Log::info('PromoteStudents: no UIDs provided, promoting all students');
    }

    // Perform the promotion
    $rows = $query->increment('current_semester');
    \Log::info('PromoteStudents: incremented semester', ['rows_affected' => $rows]);

    $message = $request->filled('student_uids')
        ? "Promoted " . count($request->input('student_uids')) . " student(s)."
        : "Promoted all students.";

    \Log::info('PromoteStudents: response prepared', ['message' => $message]);

    return response()->json([
        'status'        => 'success',
        'message'       => $message,
        'rows_affected' => $rows,
    ], 200);
}




}
