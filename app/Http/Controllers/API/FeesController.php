<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeesController extends Controller
{

    public function searchFees(Request $request)
    {
        // Validate the incoming request, including fee_type as nullable.
        $request->validate([
            'institute_id' => 'required|string|exists:institutions,id',
            'program_code' => 'required|string',
            'intake_type'  => 'required|string|in:General,Lateral',
            'year'         => 'required|integer',
            'fee_type'     => 'nullable|string', // fee_type is optional
        ]);
    
        try {
            // Start building the query for fees.
            $query = DB::table('fees')
                ->where('institution_id', $request->institute_id)
                ->where('program_code', $request->program_code)
                ->where('intake_type', $request->intake_type)
                ->where('intake_year', $request->year);
    
            // Add fee_type filter if it is provided.
            if ($request->filled('fee_type')) {
                $query->where('fee_type', $request->fee_type);
            }
    
            $fees = $query->get();
    
            if ($fees && !$fees->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data'   => $fees,
                ], 200);
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No fees record found matching the given criteria.',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while searching fees.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function addFeesStructure(Request $request)
    {
        // Validate the incoming request, including institute_id and fees array.
        $request->validate([
            'institute_id'          => 'required|string|exists:institutions,id',
            'fees'                  => 'required|array', // Validate that fees is an array.
            'fees.*.head_of_account'=> 'required|string', // Validate each head of account.
            'fees.*.type'           => 'required|string|in:one-time,semester-wise,other', // Validate each type.
        ]);
    
        try {
            // Fetch institution details using the provided institute_id.
            $institution = DB::table('institutions')
                ->where('id', $request->institute_id)
                ->first();
    
            if (!$institution) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Institution not found.',
                ], 404);
            }
    
            // Check for duplicate fee heads for the given institution.
            $existingHeads = DB::table('fee_structures')
                ->where('institution_id', $request->institute_id)
                ->whereIn('head_of_account', array_column($request->fees, 'head_of_account'))
                ->pluck('head_of_account')
                ->toArray();
    
            // If any head_of_account already exists, return an error.
            $duplicateHeads = array_intersect($existingHeads, array_column($request->fees, 'head_of_account'));
    
            if (!empty($duplicateHeads)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'The following fee heads already exist: ' . implode(', ', $duplicateHeads),
                ], 409); // HTTP 409 Conflict.
            }
    
            // Map the fees to include the institute_id, institution_name, institution_type, and timestamps.
            $feesData = array_map(function ($fee) use ($request, $institution) {
                return [
                    'institution_id'   => $request->institute_id, // Push the institution ID into each fee record.
                    'institution_name' => $institution->institution_name, // From fetched institute data.
                    'institution_type' => $institution->type,             // From fetched institute data.
                    'head_of_account'  => $fee['head_of_account'],
                    'type'             => $fee['type'],
                    'status'           => 'Active',
                    'created_at'       => now()->format('Y-m-d H:i:s'),
                    'updated_at'       => now()->format('Y-m-d H:i:s'),
                ];
            }, $request->fees);
    
            // Insert the fee structures into the database.
            DB::table('fee_structures')->insert($feesData);
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Fee structure added successfully!',
            ], 201); // HTTP 201 Created.
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while saving the fee structure.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    public function viewFeesStructure(Request $request)
    {
        // Validate that institute_id is provided and exists in the institutions table.
        $request->validate([
            'institute_id' => 'required|string|exists:institutions,id',
        ]);
    
        try {
            // Fetch fee structures specific to the provided institute_id.
            $feeStructures = DB::table('fee_structures')
                ->where('institution_id', $request->institute_id)
                ->get();
    
            return response()->json([
                'status' => 'success',
                'data'   => $feeStructures,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching the fee structures.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    public function viewAllFeesStructure(Request $request)
    {
        try {
            // Fetch all fee structures without any filtering.
            $feeStructures = DB::table('fee_structures')->get();
    
            return response()->json([
                'status' => 'success',
                'data'   => $feeStructures,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching all fee structures.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    public function editFeesStructure(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|in:one-time,semester-wise,other', // Validate the type field
        ]);
    
        try {
            $data = [
                'type' => $request->type, // Update only the type field
                'updated_at' => now()->format('Y-m-d h:i:s A'), // Update the timestamp
            ];
    
            $updated = DB::table('fee_structures')
                ->where('id', $id)
                ->update($data);
    
            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Fee structure updated successfully!',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fee structure not found or no changes made.',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the fee structure.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function toggleFeesStructureStatus($id)
    {
        try {
            // Fetch the existing fee structure
            $feeStructure = DB::table('fee_structures')->where('id', $id)->first();
    
            if (!$feeStructure) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fee structure not found.',
                ], 404);
            }
    
            // Toggle the status
            $newStatus = ($feeStructure->status === 'Active') ? 'Inactive' : 'Active';
    
            // Update the status in the database
            DB::table('fee_structures')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()->format('Y-m-d h:i:s A'),
                ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Fee structure status updated successfully!',
                'new_status' => $newStatus,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while toggling the fee structure status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function addFees(Request $request)
    {
        // Validate the incoming request including institution_id
        $request->validate([
            'institution_id' => 'required|string|exists:institutions,id',
            'program_code' => 'required|string|exists:courses,program_code',
            'intake_type' => 'required|string|in:General,Lateral',
            'intake_year' => 'required|string',
            'duration' => 'nullable|integer|min:1',
            'fee_type' => 'required|string|in:GEN,EWS,TFW',
            'one_time_fees' => 'nullable|array',
            'one_time_fees.*' => 'nullable|numeric|min:0',
            'semester_wise_fees' => 'nullable|array',
            'semester_wise_fees.*' => 'nullable|array',
            'semester_wise_fees.*.*' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|array',
            'other_fees.*' => 'nullable|array',
            'other_fees.*.*' => 'nullable|numeric|min:0',
        ]);
    
        Log::info('Received request to add fees:', ['intake_year' => $request->intake_year]);
    
        try {
            // Fetch course details using program_code
            $course = DB::table('courses')->where('program_code', $request->program_code)->first();
    
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Course not found.',
                ], 404);
            }
    
            // Fetch institution details using institution_id
            $institution = DB::table('institutions')->where('id', $request->institution_id)->first();
            if (!$institution) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Institution not found.',
                ], 404);
            }
    
            // Check if the fee structure already exists
            $existing = DB::table('fees')
                ->where('institution_id', $request->institution_id)
                ->where('program_code', $request->program_code)
                ->where('intake_year', $request->intake_year)
                ->where('intake_type', $request->intake_type)
                ->where('fee_type', $request->fee_type)
                ->first();
    
            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fee structure already exists for the given Institution, Program Code, Intake Year, Intake Type, and Fee Type.',
                ], 409);
            }
    
            // Prepare the fee structure data including course and institution details
            $data = [
                'institution_id'       => $request->institution_id,
                'institution_name'     => $institution->institution_name, // adjust field name as needed
                'institution_type'     => $institution->type,             // adjust field name as needed
                'program_code'         => $request->program_code,
                'program_name'         => $course->program_name,
                'program_type'         => $course->program_type,
                'program_duration'     => $course->program_duration,
                'intake_type'          => $request->intake_type,
                'intake_year'          => $request->intake_year,
                'fee_type'             => $request->fee_type,
                'one_time_fees'        => json_encode($request->one_time_fees),
                'semester_wise_fees'   => json_encode($request->semester_wise_fees),
                'other_fees'           => json_encode($request->other_fees),
                'created_at'           => now()->format('Y-m-d H:i:s'),
                'updated_at'           => now()->format('Y-m-d H:i:s'),
            ];
    
            // Insert into the database
            DB::table('fees')->insert($data);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Fee structure saved successfully!',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving the fee structure.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function viewFees(Request $request)
    {
        // Validate that the institution_id is provided and exists in the institutions table.
        $request->validate([
            'institution_id' => 'required|string|exists:institutions,id',
        ]);
    
        try {
            // Retrieve fee structures for the given institution, ordered by creation date (latest first)
            $fees = DB::table('fees')
                ->where('institution_id', $request->institution_id)
                ->orderBy('created_at', 'desc')
                ->get();
    
            return response()->json([
                'status' => 'success',
                'data'   => $fees,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching fees.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    public function editFees(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|string|exists:fees,id', // Ensure ID exists in the `fees` table
            'one_time_fees' => 'nullable|array', // Dynamic one-time fees
            'one_time_fees.*' => 'nullable|numeric|min:0',
            'semester_wise_fees' => 'nullable|array', // Dynamic semester-wise fees
            'semester_wise_fees.*' => 'nullable|array',
            'semester_wise_fees.*.*' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|array', // Other fees mapped to specific semesters
            'other_fees.*' => 'nullable|array',
            'other_fees.*.*' => 'nullable|numeric|min:0',
        ]);
    
        try {
            // Fetch the fee structure by ID
            $id = $request->id;
            $existingFee = DB::table('fees')->where('id', $id)->first();
    
            if (!$existingFee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fee structure not found.',
                ], 404); // HTTP 404 Not Found
            }
    
            // Prepare the updated data
            $data = [
                'one_time_fees' => json_encode($request->one_time_fees), // Encode one-time fees as JSON
                'semester_wise_fees' => json_encode($request->semester_wise_fees), // Encode semester-wise fees as JSON
                'other_fees' => json_encode($request->other_fees), // Encode other fees as JSON
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ];
    
            // Update the fee structure
            $updated = DB::table('fees')
                ->where('id', $id)
                ->update($data);
    
            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Fee structure updated successfully!',
                ], 200); // HTTP 200 OK
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No changes were made to the fee structure.',
                ], 200); // HTTP 200 OK
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the fee structure.',
                'error' => $e->getMessage(),
            ], 500); // HTTP 500 Internal Server Error
        }
    }
    
    public function payFees(Request $request)
{
    \Log::info('payFees request received', ['request_data' => $request->all()]);

    // Validate the incoming request.
    $validator = Validator::make($request->all(), [
        'student_uid'       => 'required|string',
        'institute_id'        => 'required|string|exists:institutions,id',
        'program_code'        => 'required|string|exists:courses,program_code',
        'intake_type'         => 'required|string|in:General,Lateral',
        'intake_year'         => 'required|string',
        'fee_type'            => 'required|string',
        // 'total_sem'           => 'required|string',
        'one_time_fees'       => 'nullable|string',  
        'semester_head'       => 'nullable|string',
        'semester_fees'       => 'nullable|string',
        'all_semester_fees'   => 'nullable|string',  
        'overall_total_fees'  => 'nullable|string', 
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed in payFees', [
            'errors' => $validator->errors()->toArray(),
            'request_data' => $request->all()
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // Retrieve the student record by uid.
        $student = DB::table('students')->where('uid', $request->student_uid)->first();
        if (!$student) {
            \Log::error('Student not found for fee payment', ['student_uid' => $request->student_uid]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Student not found.'
            ], 404);
        }

        // Helper: Generate Transaction ID using student's role_number.
        // Format: [Institute Short Name (4 chars)] + [YYYYMMDDHHMMSS] + [Student Role Number]
        $generateTransactionId = function() use ($student) {
            // Assuming student's role_number is already generated during registration.
            $studentRole = $student->uid;
            // Extract first 4 characters as institute short name.
            $instituteShort = strtoupper(substr($studentRole, 0, 4));
            $timestamp = date('YmdHis');
            return $instituteShort . $timestamp . $studentRole;
        };

        // Define the unique keys for identifying a fee payment record.
        $criteria = [
            'student_uid' => $request->student_uid,
            'institute_id'  => $request->institute_id,
            'program_code'  => $request->program_code,
            'intake_type'   => $request->intake_type,
            'intake_year'   => $request->intake_year,
            'fee_type'      => $request->fee_type,
            // 'total_sem'     => $request->total_sem
        ];
        \Log::info('Criteria built', ['criteria' => $criteria]);

        // Check if a fee payment record already exists.
        $existingPayment = DB::table('student_fees')->where($criteria)->first();
        if ($existingPayment) {
            \Log::info('Existing fee record found', ['existing_payment' => $existingPayment]);
        } else {
            \Log::info('No existing fee record found');
        }
        
        $message = '';
        $now = now()->format('Y-m-d H:i:s');

        // Prepare an array for updates (if needed).
        $updateData = [];

        $fees = DB::table('fees')
            ->where('institution_id', $request->institute_id)
            ->where('program_code', $request->program_code)
            ->where('intake_type', $request->intake_type)
            ->where('intake_year', $request->intake_year)
            ->where('fee_type', $request->fee_type)
            ->get();
            // $semesterFees = json_decode($fees->semester_wise_fees, true);


            \Log::info('semesterFees', ['semesterFees' => $fees->first()->semester_wise_fees]);
            \Log::info('other_fees', ['other_fees' => $fees->first()->other_fees]);

            // Get the first fee record
            $feeRecord = $fees->first();

            // Decode the JSON strings into arrays
            $semesterFees = json_decode($feeRecord->semester_wise_fees, true);
            $otherFees    = json_decode($feeRecord->other_fees, true);

            // Get all semesters (from both semester fees and other fees)
            $allSemesters = [];

            // Collect semesters from semesterFees
            foreach ($semesterFees as $feeType => $semesters) {
                foreach ($semesters as $semester => $amount) {
                    $allSemesters[$semester] = true;
                }
            }

            // Collect semesters from otherFees
            foreach ($otherFees as $semester => $feesData) {
                $allSemesters[$semester] = true;
            }

            $totalFees = [];

            // Loop through each semester key and calculate totals
            foreach ($allSemesters as $semester => $_) {
                $total = 0;
                
                // Sum fees from semester_wise_fees
                foreach ($semesterFees as $feeType => $semesters) {
                    if (isset($semesters[$semester])) {
                        $total += $semesters[$semester];
                    }
                }
                
                // Sum fees from other_fees if present
                if (isset($otherFees[$semester])) {
                    foreach ($otherFees[$semester] as $otherFeeType => $amount) {
                        $total += $amount;
                    }
                }
                
                $totalFees[$semester] = $total;
            }

            

        if ($existingPayment) {
            // ------------------------------
            // Handle one_time_fees (only allowed once)
            // ------------------------------
            if ($request->has('one_time_fees')) {
                \Log::info('Processing one_time_fees payment');
            
                // Get the total required one-time fees from the fee structure.
                $oneTimeFeesJson = $fees->first()->one_time_fees;
                $feeStructureOneTime = json_decode($oneTimeFeesJson, true);
                $totalOneTimeFees = 0;
                foreach ($feeStructureOneTime as $feeAmount) {
                    $totalOneTimeFees += floatval($feeAmount);
                }
                // \Log::info('Total One-Time Fees', ['total' => $totalOneTimeFees]);
            
                // Get existing one-time fee payments from the student's record.
                $existingPayments = [];
                if (isset($existingPayment->one_time_fees) && $existingPayment->one_time_fees) {
                    $existingPayments = json_decode($existingPayment->one_time_fees, true);
                    if (!is_array($existingPayments)) {
                        $existingPayments = [$existingPayments];
                    }
                }
            
                // Sum up the amounts already paid.
                $existingPaidTotal = 0;
                foreach ($existingPayments as $payment) {
                    if (isset($payment['amount'])) {
                        $existingPaidTotal += floatval($payment['amount']);
                    }
                }
            
                // Decode the new one_time fee payment from the request.
                $requestedPayment = json_decode($request->one_time_fees, true);
                $newAmount = floatval($requestedPayment['amount'] ?? 0);
                $newTotalPaid = $existingPaidTotal + $newAmount;
            
                if ($newTotalPaid > $totalOneTimeFees) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'The requested one-time fees payment exceeds the total required fee.'
                    ], 400);
                }
            
                // Determine the next key (as a string) based on the number of existing payments.
                $newKey = (string)(count($existingPayments) + 1);
            
                // Create a new payment entry.
                $newPayment = [
                    'amount'         => $newAmount,
                    'payment_method' => $requestedPayment['payment_method'] ?? null,
                    'date'           => $requestedPayment['date'] ?? null,
                    'transaction_id' => $generateTransactionId(),
                ];
            
                // Append the new payment under the new key.
                $existingPayments[$newKey] = $newPayment;
                $updateData['one_time_fees'] = json_encode($existingPayments);
                \Log::info('Updated one_time_fees with new partial payment', ['one_time_fees' => $updateData['one_time_fees']]);
            
                if ($newTotalPaid == $totalOneTimeFees) {
                    \Log::info('One-time fees are now fully paid.');
                    // Optionally, you can set a flag or send an additional response.
                }
            }
            

            // ------------------------------
            // Handle semester fees: semester_head becomes the key.
            // ------------------------------
                        // ------------------------------
            // Handle semester fees: allow partial payments per semester head.
            // ------------------------------
            if ($request->has('semester_fees')) {
                \Log::info('Processing semester_fees payment');
                if (!$request->has('semester_head')) {
                    \Log::error('semester_head missing when semester_fees provided');
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'semester_head is required when paying semester fees.'
                    ], 422);
                }

                // Decode existing semester fees as an associative array (if any)
                $existingSemesterFees = (isset($existingPayment->semester_fees) && $existingPayment->semester_fees)
                    ? json_decode($existingPayment->semester_fees, true)
                    : [];

                // Determine the semester head key from the request
                $semesterHead = $request->semester_head;

                // Retrieve or initialize the payment record for this semester head.
                $semesterPayments = isset($existingSemesterFees[$semesterHead])
                    ? $existingSemesterFees[$semesterHead]
                    : [];

                // Sum up the total amount already paid for this semester head.
                $existingPaidTotal = 0;
                foreach ($semesterPayments as $key => $payment) {
                    if (isset($payment['fee_details']['amount'])) {
                        $existingPaidTotal += floatval($payment['fee_details']['amount']);
                    }
                }

                // Decode the new payment details.
                $newPaymentDetails = json_decode($request->semester_fees, true);
                $newAmount = floatval($newPaymentDetails['amount'] ?? 0);
                $cumulative = $existingPaidTotal + $newAmount;

                // Retrieve the required total fee for this semester from your fee structure.
                // Assuming $totalFees is an associative array with semester keys.
                if (!isset($totalFees[$semesterHead])) {
                    \Log::error('Total fee requirement not found for semester', ['semester_head' => $semesterHead]);
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Total fee requirement not configured for this semester.'
                    ], 400);
                }
                $requiredTotal = floatval($totalFees[$semesterHead]);

                // Check if the new cumulative payment exceeds the required total.
                if ($cumulative > $requiredTotal) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'The requested semester fee payment exceeds the total required fee for this semester.'
                    ], 400);
                }

                // Determine the next incremental key for the new partial payment.
                $newKey = (string)(count($semesterPayments) + 1);

                // Create a new payment entry.
                $newPayment = [
                    'fee_details'    => $newPaymentDetails,
                    'transaction_id' => $generateTransactionId()
                ];

                // Append the new payment under the new key.
                $semesterPayments[$newKey] = $newPayment;

                // Update the record for the given semester head.
                $existingSemesterFees[$semesterHead] = $semesterPayments;
                $updateData['semester_fees'] = json_encode($existingSemesterFees);
                \Log::info('Updated semester_fees set', ['semester_fees' => $updateData['semester_fees']]);

                // Optionally, log if the semester fee is now fully paid.
                if ($cumulative == $requiredTotal) {
                    \Log::info('Semester fees are now fully paid for semester head: ' . $semesterHead);
                }
            }


            // ------------------------------
            // Handle all_semester_fees (only allowed once)
            // ------------------------------
            if ($request->has('all_semester_fees')) {
                \Log::info('Processing all_semester_fees payment');
                if (isset($existingPayment->all_semester_fees) && $existingPayment->all_semester_fees) {
                    \Log::warning('Attempt to pay all_semester_fees again', ['all_semester_fees' => $existingPayment->all_semester_fees]);
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'All semester fees have already been paid and cannot be paid again.'
                    ], 400);
                } else {
                    $updateData['all_semester_fees'] = $request->all_semester_fees;
                    $updateData['all_semester_fees_transaction'] = $generateTransactionId();
                    \Log::info('all_semester_fees added to updateData', ['all_semester_fees' => $request->all_semester_fees]);
                }
            }

            // ------------------------------
            // Handle overall_total_fees (only allowed once)
            // ------------------------------
            if ($request->has('overall_total_fees')) {
                \Log::info('Processing overall_total_fees payment');
                if (isset($existingPayment->overall_total_fees) && $existingPayment->overall_total_fees) {
                    \Log::warning('Attempt to pay overall_total_fees again', ['overall_total_fees' => $existingPayment->overall_total_fees]);
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Overall total fees have already been paid and cannot be paid again.'
                    ], 400);
                } else {
                    $updateData['overall_total_fees'] = $request->overall_total_fees;
                    $updateData['overall_total_fees_transaction'] = $generateTransactionId();
                    \Log::info('overall_total_fees added to updateData', ['overall_total_fees' => $request->overall_total_fees]);
                }
            }

            if (empty($updateData)) {
                \Log::warning('No new fee information provided', ['request_data' => $request->all()]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No new fee information provided to update.'
                ], 400);
            }

            $updateData['updated_at'] = $now;
            \Log::info('Final updateData prepared', ['updateData' => $updateData]);

            DB::table('student_fees')
                ->where($criteria)
                ->update($updateData);
            \Log::info('Database updated successfully for existing record');

            $message = 'Fee payment record updated successfully.';
        } else {
            // ------------------------------
            // Record does not exist, so we insert a new record.
            // ------------------------------
            \Log::info('Inserting new fee record');
            $insertData = $criteria;

            // For one_time_fees (if provided)
            if ($request->has('one_time_fees')) {
                $oneTimeFees = json_decode($request->one_time_fees, true);
                $newOneTimeFees = [
                    'amount'         => $oneTimeFees['amount'] ?? null,
                    'payment_method' => $oneTimeFees['payment_method'] ?? null,
                    'date'           => $oneTimeFees['date'] ?? null,
                    'transaction_id' => $generateTransactionId(),
                ];
                // Wrap it in an array with key "1"
                $insertData['one_time_fees'] = json_encode(['1' => $newOneTimeFees]);
                \Log::info('Adding one_time_fees to new record', ['one_time_fees' => $request->one_time_fees]);
            }

            // For semester fees, create an associative array with the semester_head as key.
            if ($request->has('semester_fees')) {
                if (!$request->has('semester_head')) {
                    \Log::error('semester_head is required for semester fees on insert');
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'semester_head is required when paying semester fees.'
                    ], 422);
                }
                $insertData['semester_fees'] = json_encode([
                    $request->semester_head => [
                        '1' => [
                            'fee_details'    => json_decode($request->semester_fees, true),
                            'transaction_id' => $generateTransactionId()
                        ]
                    ]
                ]);
                \Log::info('Adding semester_fees to new record', ['semester_fees' => $insertData['semester_fees']]);
            }

            // For all_semester_fees (if provided)
            if ($request->has('all_semester_fees')) {
                $insertData['all_semester_fees'] = $request->all_semester_fees;
                $insertData['all_semester_fees_transaction'] = $generateTransactionId();
                \Log::info('Adding all_semester_fees to new record', ['all_semester_fees' => $request->all_semester_fees]);
            }

            // For overall_total_fees (if provided)
            if ($request->has('overall_total_fees')) {
                $insertData['overall_total_fees'] = $request->overall_total_fees;
                $insertData['overall_total_fees_transaction'] = $generateTransactionId();
                \Log::info('Adding overall_total_fees to new record', ['overall_total_fees' => $request->overall_total_fees]);
            }

            $insertData['created_at'] = $now;
            $insertData['updated_at'] = $now;
            \Log::info('Final insertData prepared', ['insertData' => $insertData]);

            DB::table('student_fees')->insert($insertData);
            \Log::info('New fee record inserted successfully');

            $message = 'Fee payment recorded successfully.';
        }

        \Log::info('payFees operation completed successfully', ['message' => $message]);
        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error in payFees', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while processing the payment.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function getFees(Request $request)
{
    \Log::info('getFees request received', ['request_data' => $request->all()]);

    // Validate the incoming request parameters.
    $validator = Validator::make($request->all(), [
        'student_uid' => 'required|string',
        'institute_id'  => 'required|string|exists:institutions,id',
        'program_code'  => 'required|string|exists:courses,program_code',
        'intake_type'   => 'required|string|in:General,Lateral',
        'intake_year'   => 'required|string',
        'fee_type'      => 'required|string',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed in getFees', [
            'errors'       => $validator->errors()->toArray(),
            'request_data' => $request->all()
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // Build the criteria array based on the incoming parameters.
        $criteria = [
            'student_uid' => $request->student_uid,
            'institute_id'  => $request->institute_id,
            'program_code'  => $request->program_code,
            'intake_type'   => $request->intake_type,
            'intake_year'   => $request->intake_year,
            'fee_type'      => $request->fee_type,
        ];
        \Log::info('Criteria built in getFees', ['criteria' => $criteria]);

        // Retrieve the fee record.
        $fees = DB::table('student_fees')->where($criteria)->first();

        if (!$fees) {
            \Log::warning('No fee record found in getFees', ['criteria' => $criteria]);
            return response()->json([
                'status'  => 'error',
                'message' => 'No fee record found for the given criteria.'
            ], 404);
        }

        \Log::info('Fee record retrieved successfully in getFees', ['fees' => $fees]);
        return response()->json([
            'status' => 'success',
            'data'   => $fees,
        ], 200);
    } catch (\Exception $e) {
        \Log::error('Error in getFees', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while retrieving the fee record.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    
    public function feesPaymentSummary(Request $request)
    {
        // Validate that institute_id is either null or exists in the institutions table.
        $request->validate([
            'institute_id' => 'nullable|string|exists:institutions,id',
        ]);
    
        try {
            // Fetch all fee records; if institute_id is provided, filter by it.
            $feeQuery = DB::table('student_fees');
            if ($request->filled('institute_id')) {
                $feeQuery->where('institute_id', $request->institute_id);
            }
            $fees = $feeQuery->get();
    
            // Fetch students similarly (from the "students" table)
            $studentQuery = DB::table('students');
            if ($request->filled('institute_id')) {
                $studentQuery->where('institute', 'LIKE', '%"institution_id":"' . $request->institute_id . '"%');
            }
            $students = $studentQuery->get();
            $studentCount = count($students);
    
            // Initialize counters for aggregation.
            $totalRecords = count($fees);
            $oneTimeFullPaidCount = 0;
            $semesterFullPaidCount = 0;
            $overallFullPaidCount = 0;
    
            foreach ($fees as &$fee) {
                // Retrieve program details from the courses table using the program_code.
                $program = DB::table('courses')
                    ->where('program_code', $fee->program_code)
                    ->first();
    
                if ($program) {
                    // Assume 'program_duration' represents number of years; multiply by 2 to get semesters.
                    $calculatedSemesterCount = $program->program_duration * 2;
                    $fee->semester_count = $calculatedSemesterCount;
                } else {
                    $fee->semester_count = null;
                    $calculatedSemesterCount = 0;
                }
    
                // Check one_time_fees: decode JSON and verify that an amount exists.
                $oneTimePaid = false;
                if (!empty($fee->one_time_fees)) {
                    $oneTimeData = json_decode($fee->one_time_fees, true);
                    if (isset($oneTimeData['amount']) && floatval($oneTimeData['amount']) > 0) {
                        $oneTimePaid = true;
                        $oneTimeFullPaidCount++;
                    }
                }
                $fee->one_time_paid = $oneTimePaid;
    
                // Check semester fees: decode JSON and count semesters paid.
                $semesterPaid = false;
                $paidSemestersCount = 0;
                if (!empty($fee->semester_fees)) {
                    $semesterData = json_decode($fee->semester_fees, true);
                    if (is_array($semesterData)) {
                        $paidSemestersCount = count($semesterData);
                    }
                }
                // Flag as full if paid semesters equal the calculated semester count.
                if ($fee->semester_count && $paidSemestersCount === $fee->semester_count) {
                    $semesterPaid = true;
                    $semesterFullPaidCount++;
                }
                $fee->paid_semesters_count = $paidSemestersCount;
                $fee->semester_full_paid = $semesterPaid;
    
                // Overall full paid: both one_time and semester fees are fully paid.
                $overallFullPaid = ($oneTimePaid && $semesterPaid);
                if ($overallFullPaid) {
                    $overallFullPaidCount++;
                }
                $fee->overall_full_paid = $overallFullPaid;
            }
    
            // Calculate percentages based on fee records.
            $oneTimeFullPaidPercentage = $totalRecords > 0 ? round(($oneTimeFullPaidCount / $totalRecords) * 100, 2) : 0;
            $semesterFullPaidPercentage = $totalRecords > 0 ? round(($semesterFullPaidCount / $totalRecords) * 100, 2) : 0;
            $overallFullPaidPercentage = $totalRecords > 0 ? round(($overallFullPaidCount / $totalRecords) * 100, 2) : 0;
    
            // Calculate percentages based on the student count.
            $oneTimeFullPaidPercentageByStudent = $studentCount > 0 ? round(($oneTimeFullPaidCount / $studentCount) * 100, 2) : 0;
            $semesterFullPaidPercentageByStudent = $studentCount > 0 ? round(($semesterFullPaidCount / $studentCount) * 100, 2) : 0;
            $overallFullPaidPercentageByStudent = $studentCount > 0 ? round(($overallFullPaidCount / $studentCount) * 100, 2) : 0;
    
            // Prepare an aggregated summary.
            $summary = [
                'total_fee_records' => $totalRecords,
                'student_count' => $studentCount,
                'one_time_full_paid_count' => $oneTimeFullPaidCount,
                'one_time_full_paid_percentage_fee' => $oneTimeFullPaidPercentage,
                'one_time_full_paid_percentage_student' => $oneTimeFullPaidPercentageByStudent,
                'semester_full_paid_count' => $semesterFullPaidCount,
                'semester_full_paid_percentage_fee' => $semesterFullPaidPercentage,
                'semester_full_paid_percentage_student' => $semesterFullPaidPercentageByStudent,
                'overall_full_paid_count' => $overallFullPaidCount,
                'overall_full_paid_percentage_fee' => $overallFullPaidPercentage,
                'overall_full_paid_percentage_student' => $overallFullPaidPercentageByStudent,
            ];
    
            \Log::info('Fee records with aggregated payment summary retrieved successfully.', [
                'fees' => $fees,
                'summary' => $summary
            ]);
    
            return response()->json([
                'status' => 'success',
                'data'   => $fees,
                'summary' => $summary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching fees payment summary.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function feesReport(Request $request)
    {
        // Validate optional filters: institute_id must exist if provided; start_date and end_date must be valid dates.
        $validator = Validator::make($request->all(), [
            'institute_id' => 'nullable|string|exists:institutions,id',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()
            ], 422);
        }
    
        try {
            // Build the base query on student_fees.
            $query = DB::table('student_fees');
            if ($request->filled('institute_id')) {
                $query->where('institute_id', $request->institute_id);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($request->start_date)),
                    date('Y-m-d 23:59:59', strtotime($request->end_date))
                ]);
            } elseif ($request->filled('start_date')) {
                $query->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($request->start_date)));
            } elseif ($request->filled('end_date')) {
                $query->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($request->end_date)));
            }
            $feePayments = $query->orderBy('created_at', 'desc')->get();
    
            $reportRows = [];
            $collectedByInstitute = [];  // To store total collected fees per institute (by institution name).
            $grandTotalCollected = 0;      // To store grand total collected fees.
    
            foreach ($feePayments as $payment) {
                // Fetch institution name from the institutions table using institute_id.
                $inst = DB::table('institutions')->where('id', $payment->institute_id)->first();
                $instituteName = $inst ? $inst->institution_name : ($payment->institute_name ?? '');
    
                // Fetch student name from students table using student_email.
                $student = DB::table('students')->where('uid', $payment->student_uid)->first();
                $studentName = $student ? $student->name : 'N/A';
    
                // Retrieve fee structure from the fees table.
                $feeStructure = DB::table('fees')
                    ->where('institution_id', $payment->institute_id)
                    ->where('program_code', $payment->program_code)
                    ->where('intake_type', $payment->intake_type)
                    ->where('intake_year', $payment->intake_year)
                    ->where('fee_type', $payment->fee_type)
                    ->first();
    
                // Use program_name from the payment record if available, otherwise from feeStructure.
                $programName = isset($payment->program_name) && $payment->program_name
                    ? $payment->program_name
                    : ($feeStructure && isset($feeStructure->program_name) ? $feeStructure->program_name : 'N/A');
    
                // --- ONE-TIME FEES ROW ---
                $oneTimeRequired = 0;
                if (!empty($feeStructure->one_time_fees)) {
                    $oneTimeArr = json_decode($feeStructure->one_time_fees, true);
                    if (is_array($oneTimeArr)) {
                        foreach ($oneTimeArr as $amt) {
                            $oneTimeRequired += floatval($amt);
                        }
                    }
                }
                $paidOneTime = 0;
                if (!empty($payment->one_time_fees)) {
                    $oneTimePaidArr = json_decode($payment->one_time_fees, true);
                    if (is_array($oneTimePaidArr)) {
                        foreach ($oneTimePaidArr as $entry) {
                            if (isset($entry['amount'])) {
                                $paidOneTime += floatval($entry['amount']);
                            }
                        }
                    }
                }
                $pendingOneTime = $oneTimeRequired - $paidOneTime;
                
                // Update totals per institute and grand total using institution name.
                $collectedByInstitute[$instituteName] = isset($collectedByInstitute[$instituteName])
                    ? $collectedByInstitute[$instituteName] + $paidOneTime
                    : $paidOneTime;
                $grandTotalCollected += $paidOneTime;
    
                // Scholarship for one-time: use overall_discount if available; otherwise, one_time_discount.
                $scholarshipOneTime = null;
                $schCriteria = [
                    'institute_id'  => $payment->institute_id,
                    'program_code'  => $payment->program_code,
                    'intake_type'   => $payment->intake_type,
                    'year'          => $payment->intake_year,
                    'fee_type'      => $payment->fee_type,
                    'student_uid' => $payment->student_uid,
                ];
                $scholarship = DB::table('scholarships')->where($schCriteria)->first();
                if ($scholarship) {
                    if (!empty($scholarship->overall_discount)) {
                        $scholarshipOneTime = $scholarship->overall_discount;
                    } elseif (!empty($scholarship->one_time_discount)) {
                        $scholarshipOneTime = $scholarship->one_time_discount;
                    }
                }
                $reportRows[] = [
                    'institution_id'     => $payment->institute_id,
                    'institution_name'   => $instituteName,
                    'student_name'       => $studentName,
                    'category'           => 'One-Time Fees',
                    'semester'           => null,
                    'required'           => $oneTimeRequired,
                    'paid'               => $paidOneTime,
                    'pending'            => $pendingOneTime,
                    'scholarship'        => $scholarshipOneTime,
                    'course_details'     => [
                        'program_code' => $payment->program_code,
                        'program_name' => $programName,
                        'intake_type'  => $payment->intake_type,
                        'intake_year'  => $payment->intake_year,
                        'fee_type'     => $payment->fee_type,
                    ],
                    'payment_created_at' => $payment->created_at,
                    'payment_updated_at' => $payment->updated_at,
                ];
    
                // --- SEMESTER FEES ROWS ---
                $semesterTotals = [];
                // Merge semesters from semester_wise_fees.
                if (!empty($feeStructure->semester_wise_fees)) {
                    $semArr = json_decode($feeStructure->semester_wise_fees, true);
                    if (is_array($semArr)) {
                        foreach ($semArr as $feeHead => $semesters) {
                            if (is_array($semesters)) {
                                foreach ($semesters as $sem => $amt) {
                                    $semesterTotals[$sem] = isset($semesterTotals[$sem])
                                        ? $semesterTotals[$sem] + floatval($amt)
                                        : floatval($amt);
                                }
                            }
                        }
                    }
                }
                // Add other_fees into semester totals.
                if (!empty($feeStructure->other_fees)) {
                    $otherArr = json_decode($feeStructure->other_fees, true);
                    if (is_array($otherArr)) {
                        foreach ($otherArr as $sem => $feesData) {
                            if (is_array($feesData)) {
                                foreach ($feesData as $amt) {
                                    $semesterTotals[$sem] = isset($semesterTotals[$sem])
                                        ? $semesterTotals[$sem] + floatval($amt)
                                        : floatval($amt);
                                }
                            }
                        }
                    }
                }
                // For each semester, compute paid amounts.
                if (!empty($semesterTotals)) {
                    foreach ($semesterTotals as $sem => $requiredSem) {
                        $paidSem = 0;
                        if (!empty($payment->semester_fees)) {
                            $paidSemArr = json_decode($payment->semester_fees, true);
                            if (is_array($paidSemArr) && isset($paidSemArr[$sem])) {
                                foreach ($paidSemArr[$sem] as $entry) {
                                    if (isset($entry['fee_details']['amount'])) {
                                        $paidSem += floatval($entry['fee_details']['amount']);
                                    }
                                }
                            }
                        }
                        $pendingSem = $requiredSem - $paidSem;
                        
                        // Update totals per institute and grand total using institution name.
                        $collectedByInstitute[$instituteName] = isset($collectedByInstitute[$instituteName])
                            ? $collectedByInstitute[$instituteName] + $paidSem
                            : $paidSem;
                        $grandTotalCollected += $paidSem;
                        
                        // Scholarship for semester: if overall_discount exists, check for a discount for this semester; otherwise, if sem_wise_discount exists, use that.
                        $scholarshipSem = null;
                        if ($scholarship) {
                            if (!empty($scholarship->overall_discount)) {
                                $decodedOverall = json_decode($scholarship->overall_discount, true);
                                if (is_array($decodedOverall) && isset($decodedOverall[$sem])) {
                                    $scholarshipSem = $decodedOverall[$sem];
                                }
                            } elseif (!empty($scholarship->sem_wise_discount)) {
                                $decodedSem = json_decode($scholarship->sem_wise_discount, true);
                                if (is_array($decodedSem) && isset($decodedSem[$sem]) && isset($decodedSem[$sem]['amount'])) {
                                    $scholarshipSem = $decodedSem[$sem]['amount'];
                                }
                            }
                        }
                        $reportRows[] = [
                            'institution_id'     => $payment->institute_id,
                            'institution_name'   => $instituteName,
                            'student_name'       => $studentName,
                            'category'           => 'Semester Fees',
                            'semester'           => $sem,
                            'required'           => $requiredSem,
                            'paid'               => $paidSem,
                            'pending'            => $pendingSem,
                            'scholarship'        => $scholarshipSem,
                            'course_details'     => [
                                'program_code' => $payment->program_code,
                                'program_name' => $programName,
                                'intake_type'  => $payment->intake_type,
                                'intake_year'  => $payment->intake_year,
                                'fee_type'     => $payment->fee_type,
                            ],
                            'payment_created_at' => $payment->created_at,
                            'payment_updated_at' => $payment->updated_at,
                        ];
                    }
                }
            }
    
            // Prepare overall summary.
            $summary = [];
            if ($request->filled('institute_id')) {
                $summary['institute_id'] = $request->institute_id;
            }
            // Include collected fees per institute using institution name.
            $summary['collected_fees_per_institute'] = $collectedByInstitute;
            $summary['grand_total_collected'] = $grandTotalCollected;
            if ($request->filled('start_date') || $request->filled('end_date')) {
                $summary['date_range'] = [
                    'start_date' => $request->start_date ?? null,
                    'end_date'   => $request->end_date ?? null,
                ];
            }
    
            return response()->json([
                'status'  => 'success',
                'data'    => $reportRows,
                'summary' => $summary,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error generating fees report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while generating the fees report.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function feesDashboard(Request $request)
{
    // Validate that institute_id is either null or exists in the institutions table.
    $request->validate([
        'institute_id' => 'nullable|string|exists:institutions,id',
    ]);

    try {
        if ($request->filled('institute_id')) {
            // Fetch all fee records for the given institute.
            $feeRecords = DB::table('student_fees')
                ->where('institute_id', $request->institute_id)
                ->get();

            // Calculate fee totals using the helper function.
            $totals = $this->calculateFeesTotals($feeRecords);
            $institution = DB::table('institutions')
                ->where('id', $request->institute_id)
                ->first();

            $data = [
                'institute_id'         => $request->institute_id,
                'institution_name'     => $institution ? $institution->institution_name : null,
                'record_count'         => $feeRecords->count(),
                'total_one_time_paid'  => $totals['totalOneTime'],
                'total_semester_paid'  => $totals['totalSemester'],
                'total_overall_paid'   => $totals['totalOverall'],
                'grand_total_income'   => $totals['grandTotal'],
            ];
        } else {
            // No institute filter provided – fetch all fee records.
            $feeRecords = DB::table('student_fees')->get();

            // Group fee records by institute.
            $grouped = [];
            foreach ($feeRecords as $record) {
                $grouped[$record->institute_id][] = $record;
            }
            $dashboardData = [];

            // Calculate totals for each institute group.
            foreach ($grouped as $instituteId => $records) {
                // Using collect() to work with a Laravel collection if needed.
                $totals = $this->calculateFeesTotals(collect($records));
                $institution = DB::table('institutions')
                    ->where('id', $instituteId)
                    ->first();
                $dashboardData[] = [
                    'institute_id'         => $instituteId,
                    'institution_name'     => $institution ? $institution->institution_name : null,
                    'record_count'         => count($records),
                    'total_one_time_paid'  => $totals['totalOneTime'],
                    'total_semester_paid'  => $totals['totalSemester'],
                    'total_overall_paid'   => $totals['totalOverall'],
                    'grand_total_income'   => $totals['grandTotal'],
                ];
            }
            $data = $dashboardData;
        }
        return response()->json([
            'status'  => 'success',
            'message' => 'Fees dashboard data retrieved successfully.',
            'data'    => $data,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while generating fees dashboard data.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

/**
 * Helper method to calculate fee totals from fee records.
 *
 * This function processes each fee record—summing up:
 * - One-time fees (decoded from the JSON stored in one_time_fees),
 * - Semester fees (iterating over semester_fees partial payments),
 * - Also adds any amount stored in all_semester_fees and overall_total_fees.
 *
 * @param  \Illuminate\Support\Collection|array  $feeRecords
 * @return array
 */
private function calculateFeesTotals($feeRecords)
{
    $totalOneTime = 0;
    $totalSemester = 0;
    $totalOverall = 0;

    foreach ($feeRecords as $record) {
        // Sum one-time fees (assumed to be stored as JSON array with entries having an "amount" field)
        if (!empty($record->one_time_fees)) {
            $oneTimeArr = json_decode($record->one_time_fees, true);
            if (is_array($oneTimeArr)) {
                foreach ($oneTimeArr as $entry) {
                    if (isset($entry['amount'])) {
                        $totalOneTime += floatval($entry['amount']);
                    }
                }
            }
        }

        // Sum semester fees (which might be stored as a JSON object with semester keys and an array of payment entries)
        if (!empty($record->semester_fees)) {
            $semesterArr = json_decode($record->semester_fees, true);
            if (is_array($semesterArr)) {
                foreach ($semesterArr as $semKey => $payments) {
                    if (is_array($payments)) {
                        foreach ($payments as $payment) {
                            if (isset($payment['fee_details']['amount'])) {
                                $totalSemester += floatval($payment['fee_details']['amount']);
                            }
                        }
                    }
                }
            }
        }
        // Include any amount stored in all_semester_fees if provided.
        if (!empty($record->all_semester_fees)) {
            $totalSemester += floatval($record->all_semester_fees);
        }
        // Sum overall total fees if provided.
        if (!empty($record->overall_total_fees)) {
            $totalOverall += floatval($record->overall_total_fees);
        }
    }

    return [
        'totalOneTime' => $totalOneTime,
        'totalSemester' => $totalSemester,
        'totalOverall' => $totalOverall,
        'grandTotal' => $totalOneTime + $totalSemester + $totalOverall,
    ];
}




}
