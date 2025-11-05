<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function registerAgent(Request $request)
    {
        try {
            // Validation Rules
            $validator = Validator::make($request->all(), [
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|max:255',
                'mobile'         => 'required|digits:10',
                'whatsapp'       => 'required|digits:10',
                'street'         => 'required|string|max:255',
                'postOffice'     => 'required|string|max:255',
                'policeStation'  => 'required|string|max:255',
                'city'           => 'required|string|max:255',
                'state'          => 'required|string|max:255',
                'country'        => 'required|string|max:255',
                'pincode'        => 'required|digits:6',
                'pan'            => 'required|string|max:10',
                'panCard'        => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'aadharCard'     => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for agent registration', [
                    'errors' => $validator->errors(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            
            // Check if the email already exists
            $emailExists = DB::table('agent')->where('email', $request->email)->exists();
            if ($emailExists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'The email is already registered.',
                ], 409);
            }

            // Handle file uploads with unique names
            $panCard = $request->file('panCard');
            $aadharCard = $request->file('aadharCard');

            $panCardUniqueName = uniqid('pan_') . '.' . $panCard->getClientOriginalExtension();
            $aadharCardUniqueName = uniqid('aadhar_') . '.' . $aadharCard->getClientOriginalExtension();

            $panCard->move(public_path('assets/agent_documents'), $panCardUniqueName);
            $aadharCard->move(public_path('assets/agent_documents'), $aadharCardUniqueName);

            $panCardPath = 'assets/agent_documents/' . $panCardUniqueName;
            $aadharCardPath = 'assets/agent_documents/' . $aadharCardUniqueName;

            // Generate password: name + current date/time + random 4-digit number
            $plainPassword = $request->name . now()->format('YmdHis') . rand(1000, 9999);
            $hashedPassword = Hash::make($plainPassword);

            // Generate UID using first name, current date/time, random string and number
            $firstName = explode(' ', trim($request->name))[0];
            $uid = strtoupper($firstName) . now()->format('YmdHis') . strtoupper(Str::random(4)) . rand(100, 999);

            $agentData = [
                'uid'              => $uid,
                'name'             => $request->name,
                'email'            => $request->email,
                'mobile'           => $request->mobile,
                'whatsapp'         => $request->whatsapp,
                'street'           => $request->street,
                'post_office'      => $request->postOffice,
                'police_station'   => $request->policeStation,
                'city'             => $request->city,
                'state'            => $request->state,
                'country'          => $request->country,
                'pincode'          => $request->pincode,
                'pan'              => $request->pan,
                'pan_card_path'    => $panCardPath,
                'aadhar_card_path' => $aadharCardPath,
                'designation'      => "Agent",
                'status'           => 'Active',
                'password'         => $hashedPassword,
                'plain_password'   => $plainPassword,
                'created_at'       => now()->format('Y-m-d h:i:s A'),
                'updated_at'       => now()->format('Y-m-d h:i:s A'),
            ];

            $agentId = DB::table('agent')->insertGetId($agentData);

            Log::info('Agent registered successfully', [
                'agent_id' => $agentId,
                'uid'      => $uid,
                'name'     => $request->name,
                'email'    => $request->email,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Agent registered successfully',
                'data'    => [
                    'agent_id'      => $agentId,
                    'uid'           => $uid,
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'plain_password'=> $plainPassword,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in agent registration', [
                'error'       => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'An unexpected error occurred',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllAgents()
    {
        try {
            $agents = DB::table('agent')->get();

            if ($agents->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No agents found',
                    'data' => [],
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Agents fetched successfully',
                'data' => $agents,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching agents', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function agentLogin(Request $request)
    {
        Log::info('Agent login request received', ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'identifier' => 'required',
            'password'   => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for agent login', [
                'errors' => $validator->errors()->first()
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $agent = DB::table('agent')
            ->where('email', $request->identifier)
            ->orWhere('mobile', $request->identifier)
            ->first();

        if (!$agent) {
            Log::error('Agent not found', ['identifier' => $request->identifier]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Agent not found.'
            ], 404);
        }

        if (!Hash::check($request->password, $agent->password)) {
            Log::error('Invalid credentials for agent', ['agent_id' => $agent->id]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        Log::info('Agent login successful', ['agent_id' => $agent->id]);

        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        DB::table('personal_access_tokens')
            ->where('tokenable_id', $agent->id)
            ->where('tokenable_type', 'agent')
            ->delete();

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'agent',
            'tokenable_id'   => $agent->id,
            'name'           => 'agent_auth',
            'token'          => $hashedToken,
            'abilities'      => json_encode(['*']),
            'designation'    => 'Agent',
            'created_at'     => now()->format('Y-m-d h:i:s A'),
            'updated_at'     => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status'      => 'success',
            'agent_token' => $token,
            'agent'       => [
                'id'          => $agent->id,
                'uid'         => $agent->uid,
                'name'        => $agent->name,
                'email'       => $agent->email,
                'mobile'      => $agent->mobile,
                'designation' => 'Agent',
            ]
        ], 200);
    }

    public function agentLogout(Request $request)
    {
        Log::info('Agent logout request received', ['token' => $request->token]);

        if (!$request->token) {
            Log::warning('Agent logout failed: No token provided');
            return response()->json([
                'status'  => 'error',
                'message' => 'No token provided'
            ], 400);
        }

        $hashedToken = hash('sha256', $request->token);

        $tokenRecord = DB::table('personal_access_tokens')
            ->where('token', $hashedToken)
            ->where('tokenable_type', 'agent')
            ->first();

        if (!$tokenRecord) {
            Log::error('Invalid token during agent logout', ['token' => $request->token]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid token'
            ], 401);
        }

        DB::table('personal_access_tokens')
            ->where('token', $hashedToken)
            ->delete();

        Log::info('Agent logged out successfully', ['agent_id' => $tokenRecord->tokenable_id]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function ragisterStudentByAgent(Request $request)
    {
        try {
            $validatedData = $request->validate([
                // Basic Details
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:10',
                'alternative-phone' => 'nullable|digits:10',
                'email' => 'required|email|max:255',
                'alternative-email' => 'nullable|email|max:255',
                'whatsapp-no' => 'required|digits:10',

                // Additional Details
                'date_of_birth' => 'required|date',
                'place_of_birth' => 'nullable|string|max:255',
                'religion' => 'nullable|string|max:100',
                'caste' => 'nullable|string|max:100',
                'blood_group' => 'nullable|string|max:10',
                'identity_type' => 'required|in:Aadhar,Voter ID,PAN',
                'identity_details' => 'required|string|max:20',

                // Address
                'city' => 'required|string|max:255',
                'po' => 'required|string|max:255',
                'ps' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'pin' => 'required|digits:6',

                // Parents Details
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

            $designation = $request->header('Designation') ?: '';
            \Log::info('Designation from header: ' . $designation);

            $data = $validatedData;

            if (in_array(strtolower($designation), ['admin', 'register', 'principal'])) {
                $roleNumber = '';
                $maxAttempts = 5;
                $attempt = 0;
                \Log::info('Generating role number for student registration via agent');
  
                do {
                    $instituteName = '';
                    if (is_array($instituteData) && isset($instituteData['institution_name'])) {
                        $instituteName = strtoupper(substr($instituteData['institution_name'], 0, 4));
                        $instituteName = str_pad($instituteName, 4, '0');
                    } else {
                        $instituteName = 'UNKN';
                    }
  
                    $randomNumberBefore = mt_rand(1000, 9999);
                    $timestamp = date('YmdHis');
  
                    $programCode = '';
                    if (is_array($courseData) && isset($courseData['program_code'])) {
                        $programCode = $courseData['program_code'];
                    } else {
                        $programCode = 'XXXX';
                    }
  
                    $randomNumberAfter = mt_rand(1000, 9999);
  
                    $roleNumber = $instituteName . $randomNumberBefore . $timestamp . $programCode . $randomNumberAfter;
                    $attempt++;
                } while (DB::table('agent_student_registrations')->where('role_number', $roleNumber)->exists() && $attempt < $maxAttempts);
  
                if (DB::table('agent_student_registrations')->where('role_number', $roleNumber)->exists()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Failed to generate a unique role number, please try again.',
                    ], 500);
                }
  
                $data['role_number'] = $roleNumber;
                $data['status'] = 'Active';
                $data['designation'] = 'Student';
  
                $nameParts = explode(' ', trim($validatedData['name']));
                $firstName = isset($nameParts[0]) ? $nameParts[0] : 'User';
                $dobFormatted = date('dmY', strtotime($validatedData['date_of_birth']));
                $randomDigits = mt_rand(1000, 9999);
                $generatedPassword = $firstName . $dobFormatted . $randomDigits;
  
                $data['plain_password'] = $generatedPassword;
                $data['password'] = Hash::make($generatedPassword);
            } else {
                $data['status'] = 'Inactive';
            }

            $data['agent_email'] = $request->header('Agent-Email') ?? '';
            $data['agent_phone'] = $request->header('Agent-Phone') ?? '';
            $data['agent_name'] = $request->header('Agent-Name') ?? '';
        
            if (!empty($data['agent_email'])) {
                $agentRecord = DB::table('agent')->where('email', $data['agent_email'])->first();
                $data['agent_uid'] = $agentRecord ? $agentRecord->uid : null;
            } else {
                $data['agent_uid'] = null;
            }

            if (DB::table('agent_student_registrations')
                    ->where('identity_type', $data['identity_type'])
                    ->where('identity_details', $data['identity_details'])
                    ->where('agent_uid', $data['agent_uid'])
                    ->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Student identity details already used by you.',
                ], 409);
            }

            $data['created_at'] = now()->format('Y-m-d h:i:s A');
            $data['updated_at'] = now()->format('Y-m-d h:i:s A');

            $success = DB::table('agent_student_registrations')->insert($data);
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

    public function getAgentStudents(Request $request)
{
    try {
        $agentUid = $request->query('agent_uid');
        $instituteId = $request->query('institute_id');

        // Build the query on the agent_student_registrations table.
        $query = DB::table('agent_student_registrations');

        // Filter by agent_uid if provided.
        if ($agentUid) {
            $query->where('agent_uid', $agentUid);
        }

        // Filter by institution id if provided.
        if ($instituteId) {
            $query->where('institute', 'LIKE', '%"institution_id":"' . $instituteId . '"%');
        }

        // Order and get the students.
        $students = $query->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Agent registered students fetched successfully',
            'data'    => $students,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching agent student registrations', [
            'error'       => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching the student records.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Get agent details by agent UID.
     */
    public function getAgentById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'agent_uid' => 'required|string|exists:agent,uid',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for getAgentById', [
                    'errors' => $validator->errors(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $agentUid = $request->agent_uid;
            $agent = DB::table('agent')->where('uid', $agentUid)->first();

            if (!$agent) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Agent not found.',
                ], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Agent fetched successfully.',
                'data'    => $agent,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching agent by UID', [
                'error'       => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'An unexpected error occurred.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change agent password by agent UID.
     */
    public function changeAgentPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_uid'        => 'required|string|exists:agent,uid',
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for changeAgentPassword', [
                'errors' => $validator->errors(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $agentUid = $request->agent_uid;
            $agent = DB::table('agent')->where('uid', $agentUid)->first();

            if (!$agent) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Agent not found.',
                ], 404);
            }

            if (!Hash::check($request->current_password, $agent->password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Current password is incorrect.',
                ], 401);
            }

            if (Hash::check($request->new_password, $agent->password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'New password cannot be the same as the current password.',
                ], 422);
            }

            $newHashedPassword = Hash::make($request->new_password);

            DB::table('agent')->where('uid', $agentUid)->update([
                'password'       => $newHashedPassword,
                'plain_password' => $request->new_password,
                'updated_at'     => now()->format('Y-m-d h:i:s A'),
            ]);

            Log::info('Agent password changed successfully', ['agent_uid' => $agentUid]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Password updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error changing agent password', [
                'error'       => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while changing the password.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Edit agent details by agent UID.
     */
    public function editAgent(Request $request)
{
    try {
        // Base validation for agent_uid
        $validator = Validator::make($request->all(), [
            'agent_uid' => 'required|string|exists:agent,uid',
        ]);

        // if ($validator->fails()) {
        //     Log::warning('Validation failed for editAgent', [
        //         'errors' => $validator->errors(),
        //     ]);
        //     return response()->json([
        //         'status'  => 'error',
        //         'message' => 'Validation failed',
        //         'errors'  => $validator->errors(),
        //     ], 422);
        // }

        $agentUid = $request->agent_uid;

        // If an image file is provided, process only image update
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $uniqueFileName = uniqid('agent_img_') . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('assets/agent_documents'), $uniqueFileName);
            $updateData = [
                'image'      => 'assets/agent_documents/' . $uniqueFileName,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ];
        } else {
            // Otherwise validate and update other fields
            $validator = Validator::make($request->all(), [
                'name'          => 'nullable|string|max:255',
                'email'         => 'nullable|email|max:255',
                'mobile'        => 'nullable|digits:10',
                'whatsapp'      => 'nullable|digits:10',
                'street'        => 'nullable|string|max:255',
                'postOffice'    => 'nullable|string|max:255',
                'policeStation' => 'nullable|string|max:255',
                'city'          => 'nullable|string|max:255',
                'state'         => 'nullable|string|max:255',
                'country'       => 'nullable|string|max:255',
                'pincode'       => 'nullable|digits:6',
                'pan'           => 'nullable|string|max:10',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }
            $updateData = $request->except('agent_uid', 'image');
            $updateData['updated_at'] = now()->format('Y-m-d h:i:s A');

            // Check if updated email is unique (if provided)
            if (isset($updateData['email'])) {
                $emailExists = DB::table('agent')
                    ->where('email', $updateData['email'])
                    ->where('uid', '!=', $agentUid)
                    ->exists();
                if ($emailExists) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'The email is already used by another agent.',
                    ], 409);
                }
            }
        }

        $success = DB::table('agent')->where('uid', $agentUid)->update($updateData);
        if ($success) {
            Log::info('Agent updated successfully', ['agent_uid' => $agentUid]);
            return response()->json([
                'status'       => 'success',
                'message'      => 'Agent details updated successfully.',
                'updated_data' => $updateData,
            ], 200);
        }
        return response()->json([
            'status'  => 'error',
            'message' => 'No changes were made or failed to update agent data.',
        ], 500);
    } catch (\Exception $e) {
        Log::error('Error editing agent', [
            'error'       => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while editing agent.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
