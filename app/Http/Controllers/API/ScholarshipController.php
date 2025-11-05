<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ScholarshipController extends Controller
{
    public function addScholarship(Request $request)
{
    // Validate the incoming request.
    $validator = Validator::make($request->all(), [
        'institute_id'      => 'required|string|exists:institutions,id',
        'institute_name'    => 'required|string',
        'course_id'         => 'nullable|string',
        'program_code'      => 'required|string|exists:courses,program_code',
        'program_name'      => 'required|string',
        'intake_type'       => 'required|string|in:General,Lateral',
        'year'              => 'required|string',
        'fee_type'          => 'required|string', // e.g. GEN, EWS, TFW
        // 'student_name'      => 'required|string',
        'student_uid'     => 'required|string',
        'overall_discount'  => 'nullable|string', // can be a percentage or fixed amount
        'sem_wise_discount' => 'nullable|string', // expected as a JSON string like {"1": "500"}
        'one_time_discount' => 'nullable|string', // new discount field
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed in addScholarship', $validator->errors()->toArray());
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    // Enforce mutual exclusion: only one discount type should be provided.
    $discountCount = 0;
    if ($request->filled('overall_discount')) {
        $discountCount++;
    }
    if ($request->filled('sem_wise_discount')) {
        $discountCount++;
    }
    if ($request->filled('one_time_discount')) {
        $discountCount++;
    }
    if ($discountCount > 1) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Only one discount type is allowed. Please choose either overall, semester-wise, or one-time discount.',
        ], 422);
    }

    try {
        // Define criteria to uniquely identify a scholarship record.
        $criteria = [
            'institute_id'  => $request->institute_id,
            'program_code'  => $request->program_code,
            'intake_type'   => $request->intake_type,
            'year'          => $request->year,
            'fee_type'      => $request->fee_type,
            'student_uid' => $request->student_uid,
        ];

        // Check if a scholarship record already exists.
        $existingScholarship = DB::table('scholarships')->where($criteria)->first();

        // Function to format the sem_wise_discount value.
        $formatSemWise = function ($jsonStr) {
            $decoded = json_decode($jsonStr, true);
            if (is_array($decoded)) {
                $formatted = [];
                foreach ($decoded as $semesterKey => $discountValue) {
                    // If the discount value is not an array, wrap it.
                    if (!is_array($discountValue)) {
                        $formatted[$semesterKey] = [
                            'amount' => $discountValue,
                        ];
                    } else {
                        // Ensure the 'semester' key is set.
                        if (!isset($discountValue['semester'])) {
                            $discountValue['semester'] = $semesterKey;
                        }
                        $formatted[$semesterKey] = $discountValue;
                    }
                }
                return json_encode($formatted);
            }
            return null;
        };

        if ($existingScholarship) {
            $updateData = [
                'overall_discount'  => $request->overall_discount ?? $existingScholarship->overall_discount,
                'one_time_discount' => $request->one_time_discount ?? $existingScholarship->one_time_discount,
                'updated_at'        => now()->format('Y-m-d H:i:s'),
            ];

            // If a new sem_wise_discount is provided, override the previous one.
            if ($request->filled('sem_wise_discount')) {
                $formattedNew = [];
                $newDecoded = json_decode($request->sem_wise_discount, true);
                if (is_array($newDecoded)) {
                    foreach ($newDecoded as $semesterKey => $discountValue) {
                        if (!is_array($discountValue)) {
                            $formattedNew[$semesterKey] = [
                                'amount' => $discountValue,
                            ];
                        } else {
                            if (!isset($discountValue['semester'])) {
                                $discountValue['semester'] = $semesterKey;
                            }
                            $formattedNew[$semesterKey] = $discountValue;
                        }
                    }
                    // Override previous sem_wise_discount with the new value.
                    $updateData['sem_wise_discount'] = json_encode($formattedNew);
                } else {
                    \Log::error('Failed to decode new sem_wise_discount', ['sem_wise_discount' => $request->sem_wise_discount]);
                }
            }

            DB::table('scholarships')->where($criteria)->update($updateData);
            return response()->json([
                'status'  => 'success',
                'message' => 'Scholarship updated successfully.',
            ], 200);
        } else {
            $semWiseDiscount = $request->sem_wise_discount;
            if ($request->filled('sem_wise_discount')) {
                $semWiseDiscount = $formatSemWise($request->sem_wise_discount);
            }

            $data = [
                'institute_id'      => $request->institute_id,
                'institute_name'    => $request->institute_name,
                'course_id'         => $request->course_id,
                'program_code'      => $request->program_code,
                'program_name'      => $request->program_name,
                'intake_type'       => $request->intake_type,
                'year'              => $request->year,
                'fee_type'          => $request->fee_type,
                'student_name'      => $request->student_name,
                'student_uid'     => $request->student_uid,
                'overall_discount'  => $request->overall_discount,
                'sem_wise_discount' => $semWiseDiscount,
                'one_time_discount' => $request->one_time_discount,
                'created_at'        => now()->format('Y-m-d H:i:s'),
                'updated_at'        => now()->format('Y-m-d H:i:s'),
            ];

            DB::table('scholarships')->insert($data);
            return response()->json([
                'status'  => 'success',
                'message' => 'Scholarship added successfully.',
            ], 201);
        }
    } catch (\Exception $e) {
        \Log::error('Error in addScholarship', ['error' => $e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while adding the scholarship.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

    

    public function viewScholarship(Request $request)
    {
        // Validate the incoming request parameters.
        $validator = Validator::make($request->all(), [
            'student_uid'     => 'required|string',
        ]);
    
        if ($validator->fails()) {
            \Log::error('Validation failed in viewScholarship', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }
    
        try {
            // Build the search criteria. We include the additional fields for a more precise query.
            $criteria = [
                'student_uid'  => $request->student_uid,
            ];
            
            $scholarship = DB::table('scholarships')->where($criteria)->first();
            
            if (!$scholarship) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Scholarship record not found.',
                ], 404);
            }
            
            return response()->json([
                'status'  => 'success',
                'message' => 'Scholarship record retrieved successfully.',
                'data'    => $scholarship,
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error in viewScholarship', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while retrieving the scholarship record.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    


    public function editScholarship(Request $request)
    {
        // Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'institute_id'      => 'required|string|exists:institutions,id',
            'course_id'         => 'nullable|string',
            'program_code'      => 'required|string|exists:courses,program_code',
            'intake_type'       => 'required|string|in:General,Lateral',
            'year'              => 'required|string',
            'fee_type'          => 'required|string', // e.g., GEN, EWS, TFW
            'student_email'     => 'required|email',
            'overall_discount'  => 'nullable|string', // can be a percentage or fixed amount
            'sem_wise_discount' => 'nullable|string', // expected as JSON string (e.g. {"1":"1000"} or {"1":{"amount":"1000"}})
        ]);
    
        if ($validator->fails()) {
            \Log::error('Validation failed in editScholarship', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        \Log::error($request->overall_discount);
        try {
            // Define criteria to uniquely identify the scholarship record.
            $criteria = [
                'institute_id'  => $request->institute_id,
                'program_code'  => $request->program_code,
                'intake_type'   => $request->intake_type,
                'year'          => $request->year,
                'fee_type'      => $request->fee_type,
                'student_email' => $request->student_email,
            ];
    
            // Retrieve the existing scholarship record.
            $scholarship = DB::table('scholarships')->where($criteria)->first();
            if (!$scholarship) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Scholarship record not found.',
                ], 404);
            }
    
            // Prepare the update data.
            $updateData = [
                'overall_discount' => $request->overall_discount ?? $scholarship->overall_discount,
                'updated_at'       => now()->format('Y-m-d H:i:s'),
            ];
    
            // Process semester-wise discount if provided.
            if ($request->filled('sem_wise_discount')) {
                // Format new sem-wise discount.
                $newDecoded = json_decode($request->sem_wise_discount, true);
                $formattedNew = [];
                if (is_array($newDecoded)) {
                    foreach ($newDecoded as $semesterKey => $discountValue) {
                        if (!is_array($discountValue)) {
                            // Wrap the value if not already an array.
                            $formattedNew[$semesterKey] = [
                                'amount' => $discountValue,
                            ];
                        } else {
                            if (!isset($discountValue['semester'])) {
                                $discountValue['semester'] = $semesterKey;
                            }
                            $formattedNew[$semesterKey] = $discountValue;
                        }
                    }
                }
    
                // Get the existing semester-wise discount as an array.
                $existingSemWise = [];
                if (!empty($scholarship->sem_wise_discount)) {
                    $existingSemWise = json_decode($scholarship->sem_wise_discount, true);
                    if (!is_array($existingSemWise)) {
                        $existingSemWise = [];
                    }
                }
    
                // Merge new discounts with existing (new values override existing keys).
                $mergedSemWise = array_merge($existingSemWise, $formattedNew);
                $updateData['sem_wise_discount'] = json_encode($mergedSemWise);
            }
    
            DB::table('scholarships')->where($criteria)->update($updateData);
            return response()->json([
                'status'  => 'success',
                'message' => 'Scholarship record updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in editScholarship', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the scholarship record.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
