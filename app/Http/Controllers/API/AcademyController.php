<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademyController extends Controller
{
    public function addCampus(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'campus_name' => 'required|string|max:255',
            'campus_id' => 'required|string|max:100',
        ]);

        // Return validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the campus_id already exists
            $exists = DB::table('campuses')->where('campus_id', $request->campus_id)->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Campus ID already exists.',
                ], 409); // Conflict HTTP status code
            }

            // Insert campus data into the database
            DB::table('campuses')->insert([
                'campus_name' => $request->campus_name,
                'campus_id' => $request->campus_id,
                'status' => 'Active',
                'created_at' => now()->format('Y-m-d h:i:s A'),
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Campus added successfully.',
            ], 201);

        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding the campus.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getCampuses()
    {
        try {
            // Fetch all campuses from the database
            $campuses = DB::table('campuses')->select('*')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Campuses retrieved successfully.',
                'data' => $campuses,
            ], 200);

        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving campuses.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function editCampus(Request $request, $campus_id)
{
    // Validation rules
    $validator = Validator::make($request->all(), [
        'campus_name' => 'required|string|max:255',
    ]);

    // Return validation errors
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Check if the campus exists
        $campus = DB::table('campuses')->where('campus_id', $campus_id)->first();

        if (!$campus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Campus not found.',
            ], 404); // Not Found HTTP status code
        }

        // Update the campus data in the database
        DB::table('campuses')
            ->where('campus_id', $campus_id)
            ->update([
                'campus_name' => $request->campus_name,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Campus updated successfully.',
        ], 200);

    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the campus.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function toggleCampusStatus(Request $request, $campus_id)
{
    try {
        // Validate the status value
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status value.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if the campus exists
        $campus = DB::table('campuses')->where('campus_id', $campus_id)->first();

        if (!$campus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Campus not found.',
            ], 404); // Not Found HTTP status code
        }

        // Update the campus status
        DB::table('campuses')
            ->where('campus_id', $campus_id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => "Campus status updated to {$request->status} successfully.",
        ], 200);

    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the campus status.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function addInstitution(Request $request)
{
    // Validation rules
    $validator = Validator::make($request->all(), [
        'campus_id' => 'required|exists:campuses,campus_id',
        'institution_name' => 'required|string|max:255',
        'institution_short_code' => 'required|string|max:10|unique:institutions,institution_short_code',
        'type' => 'required|in:School,College',
        'street' => 'required|string|max:255',
        'po' => 'required|string|max:255',
        'ps' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'state' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'pincode' => 'required|digits:6',
        'url' => 'nullable|url|max:255',
        'contact_no' => 'nullable|digits_between:10,15',
        'email_id' => 'nullable|email|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed.', $validator->errors()->toArray());
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/institution_image'), $fileName);
            $logoPath = 'assets/institution_image/' . $fileName;
        }

        DB::table('institutions')->insert([
            'campus_id' => $request->campus_id,
            'institution_name' => $request->institution_name,
            'institution_short_code' => strtoupper($request->institution_short_code),
            'type' => $request->type,
            'street' => $request->street,
            'po' => $request->po,
            'ps' => $request->ps,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'pincode' => $request->pincode,
            'url' => $request->url,
            'contact_no' => $request->contact_no,
            'email_id' => $request->email_id,
            'status' => 'Active',
            'logo' => $logoPath,
            'created_at' => now()->format('Y-m-d h:i:s A'),
            'updated_at' => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Institution added successfully.',
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while adding the institution.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function updateInstitutionLogo(Request $request, $institutionId)
{
    \Log::info('Starting logo update process for institution ID: ' . $institutionId);

    // Validate the logo file
    $validator = Validator::make($request->all(), [
        'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed during logo update.', $validator->errors()->toArray());
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        \Log::info('Validation passed. Fetching institution details...');

        // Check if the institution exists
        $institution = DB::table('institutions')->where('id', $institutionId)->first();

        if (!$institution) {
            \Log::warning('Institution not found with ID: ' . $institutionId);
            return response()->json([
                'status' => 'error',
                'message' => 'Institution not found.',
            ], 404);
        }

        \Log::info('Institution found. Checking for existing logo...');

        // Delete old logo if it exists
        if (!empty($institution->logo)) {
            $oldLogoPath = public_path($institution->logo);
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
                \Log::info('Old logo deleted: ' . $institution->logo);
            } else {
                \Log::info('Old logo path exists in DB but file not found: ' . $institution->logo);
            }
        } else {
            \Log::info('No old logo set in the database.');
        }

        // Store new logo
        \Log::info('Storing new logo file...');
        $file = $request->file('logo');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/institution_image'), $fileName);
        $logoPath = 'assets/institution_image/' . $fileName;

        \Log::info('New logo stored at path: ' . $logoPath);

        // Update logo path in the database
        DB::table('institutions')
            ->where('id', $institutionId)
            ->update([
                'logo' => $logoPath,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        \Log::info('Logo path updated in database successfully for institution ID: ' . $institutionId);

        return response()->json([
            'status' => 'success',
            'message' => 'Institution logo updated successfully.',
            'logo_path' => $logoPath,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Exception occurred while updating logo: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the logo.',
            'error' => $e->getMessage(),
        ], 500);
    }
}




    public function viewInstitutions(Request $request)
{
    try {
        // Fetch institutions with optional filtering
        $institutions = DB::table('institutions')
            ->select('*')
            ->get();

        // Check if no institutions exist
        if ($institutions->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No institutions found.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Institutions retrieved successfully.',
            'data' => $institutions,
        ], 200);
    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while fetching institutions.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function viewInstitutionById($id)
{
    try {
        // Adjust the column name if your primary key is "id" or "_id"
        $institution = DB::table('institutions')->where('_id', $id)->first();
        
        if (!$institution) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution not found.'
            ], 404);
        }
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Institution retrieved successfully.',
            'data'    => $institution
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while retrieving the institution.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function editInstitution(Request $request, $institutionId)
{
    // Validation rules
    $validator = Validator::make($request->all(), [
        'campus_id' => 'required|exists:campuses,campus_id', // Check if the campus_id exists in campuses table
        'institution_name' => 'required|string|max:255',
        'type' => 'required|in:School,College',
        'street' => 'required|string|max:255',
        'po' => 'required|string|max:255',
        'ps' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'state' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'pincode' => 'required|digits:6',
        'url' => 'nullable|url|max:255',
        'contact_no' => 'nullable|digits_between:10,15',
        'email_id' => 'nullable|email|max:255',
    ]);

    // Return validation errors
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Check if the institution exists
        $institution = DB::table('institutions')->where('_id', $institutionId)->first();

        if (!$institution) {
            return response()->json([
                'status' => 'error',
                'message' => 'Institution not found.',
            ], 404); // Not Found HTTP status code
        }

        // Update institution data
        DB::table('institutions')
            ->where('_id', $institutionId)
            ->update([
                'campus_id' => $request->campus_id,
                'institution_name' => $request->institution_name,
                'type' => $request->type,
                'street' => $request->street,
                'po' => $request->po,
                'ps' => $request->ps,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'pincode' => $request->pincode,
                'url' => $request->url,
                'contact_no' => $request->contact_no,
                'email_id' => $request->email_id,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Institution updated successfully.',
        ], 200);

    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the institution.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function toggleInstitutionStatus($id)
{
    try {
        // Check if the institution exists
        $institution = DB::table('institutions')->where('_id', $id)->first();

        if (!$institution) {
            return response()->json([
                'status' => 'error',
                'message' => 'Institution not found.',
            ], 404); // Not Found HTTP status code
        }

        // Determine the new status based on the current status
        $newStatus = $institution->status === 'Active' ? 'Inactive' : 'Active';

        // Update the institution's status
        DB::table('institutions')
            ->where('_id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Institution status updated successfully.',
            'new_status' => $newStatus,
        ], 200);
    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the institution status.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function mergeInstitutionCourses(Request $request)
{
    // Validate that an institution_id is provided (exists in the institutions table)
    // and course_ids is provided as an array of valid course codes (strings).
    $validator = Validator::make($request->all(), [
        'institution_id' => 'required|exists:institutions,_id',
        'course_ids'     => 'required|array',
        'course_ids.*'   => 'required|string|exists:courses,program_code',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed.', $validator->errors()->toArray());

        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // Fetch institution details using the provided institution ID.
        $institution = DB::table('institutions')->where('_id', $request->institution_id)->first();
        if (!$institution) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution not found.',
            ], 404);
        }

        $mergedData = [];
        $now = now()->format('Y-m-d H:i:s');

        // Loop through each provided course code.
        foreach ($request->course_ids as $courseCode) {
            // Retrieve course details by matching program_code.
            $course = DB::table('courses')->where('program_code', $courseCode)->first();
            if (!$course) {
                // This should not occur due to validation.
                continue;
            }

            // Prepare a merged record.
            $mergedData[] = [
                'institution_id'    => $request->institution_id,
                'institution_name'  => $institution->institution_name,
                'institution_type'  => $institution->type, // adjust if your column name differs
                'course_id'         => $course->program_code, // using program_code as the identifier
                'program_code'      => $course->program_code,
                'program_duration'  => $course->program_duration,
                'program_name'      => $course->program_name,
                'program_type'      => $course->program_type,
                'board'             => $course->board,
                'status'            =>'Active',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        // Insert the merged records into the table.
        DB::table('institution_course')->insert($mergedData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institution and course details merged and inserted successfully.',
            'data'    => $mergedData,
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while merging institution courses.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
public function viewInstitutionCourses(Request $request)
{
    try {
        // Optionally filter by institution_id if provided
        $query = DB::table('institution_course')->select('*');
        if ($request->has('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }
        $records = $query->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Institution courses retrieved successfully.',
            'data'    => $records,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while retrieving institution courses.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function editInstitutionCourses(Request $request, $id)
{
    // Validate the input - here we allow updating the course association by providing a new course_id (program_code)
    $validator = Validator::make($request->all(), [
        'course_id' => 'required|string|exists:courses,program_code',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed for editInstitutionCourses.', $validator->errors()->toArray());
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // Check if the merged institution course record exists
        $record = DB::table('institution_course')->where('id', $id)->first();
        if (!$record) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution course record not found.',
            ], 404);
        }

        // Fetch new course details using provided course_id (program_code)
        $course = DB::table('courses')->where('program_code', $request->course_id)->first();
        if (!$course) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Course not found.',
            ], 404);
        }

        // Prepare updated data based on the new course details
        $data = [
            'course_id'         => $course->program_code,  
            'program_code'      => $course->program_code,
            'program_duration'  => $course->program_duration,
            'program_name'      => $course->program_name,
            'program_type'      => $course->program_type,
            'board'             => $course->board,
            'updated_at'        => now()->format('Y-m-d H:i:s'),
        ];

        // Update the merged record
        DB::table('institution_course')->where('id', $id)->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institution course record updated successfully.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while updating the institution course record.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
public function deleteInstitutionCourses($id)
{
    try {
        \Log::info('Attempting to delete institution course record', ['id' => $id]);

        // Check if the merged institution course record exists
        $record = DB::table('institution_course')->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution course record not found.',
            ], 404);
        }

        // Delete the record from the database
        DB::table('institution_course')->where('id', $id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Institution course record deleted successfully.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while deleting the institution course record.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}




public function addCourseType(Request $request)
    {
        // Define validation rules.
        $validator = Validator::make($request->all(), [
            'course_type'                    => 'required|string|max:255',
            'required_qualification_x'   => 'nullable|string|max:255',
            'required_qualification_xii'     => 'nullable|string|max:255',
            'required_qualification_college' => 'nullable|string|max:255',
        ]);

        // Return validation errors if any.
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Optionally, check if the course type already exists.
            $exists = DB::table('course_types')
                        ->where('course_type', $request->course_type)
                        ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Course type already exists. Please use a unique course type.',
                ], 409); // 409 Conflict
            }

            // Insert the course type data into the database.
            DB::table('course_types')->insert([
                'course_type'                    => $request->course_type,
                'required_qualification_x'   => $request->required_qualification_x,
                'required_qualification_xii'     => $request->required_qualification_xii,
                'required_qualification_college' => $request->required_qualification_college,
                'status'                            => "Active",
                'created_at'                     => now()->format('Y-m-d h:i:s A'),
                'updated_at'                     => now()->format('Y-m-d h:i:s A'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Course type added successfully.',
            ], 201);
        } catch (\Exception $e) {
            // Return error response if something goes wrong.
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the course type.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function viewCourseTypes(Request $request)
    {
        try {
            // Fetch all course types from the database
            $courseTypes = DB::table('course_types')->select('*')->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Course types retrieved successfully.',
                'data'    => $courseTypes,
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while retrieving course types.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function editCourseType(Request $request, $id)
    {
        // Define validation rules.
        $validator = Validator::make($request->all(), [
            'course_type'                    => 'required|string|max:255',
            'required_qualification_x'       => 'nullable|string|max:255',
            'required_qualification_xii'     => 'nullable|string|max:255',
            'required_qualification_college' => 'nullable|string|max:255',
        ]);

        // Return validation errors if any.
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the course type exists.
            $courseType = DB::table('course_types')->where('id', $id)->first();

            if (!$courseType) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Course type not found.',
                ], 404);
            }

            // Update the course type data.
            DB::table('course_types')->where('id', $id)->update([
                'course_type'                    => $request->course_type,
                'required_qualification_x'       => $request->required_qualification_x,
                'required_qualification_xii'     => $request->required_qualification_xii,
                'required_qualification_college' => $request->required_qualification_college,
                'updated_at'                     => now()->format('Y-m-d h:i:s A'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Course type updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Return error response.
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the course type.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleCourseType(Request $request, $id)
    {
        // Validate the status value.
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid status value.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the course type exists.
            $courseType = DB::table('course_types')->where('id', $id)->first();

            if (!$courseType) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Course type not found.',
                ], 404);
            }

            // Update the course type status.
            DB::table('course_types')->where('id', $id)->update([
                'status'     => $request->status,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => "Course type status updated to {$request->status} successfully.",
            ], 200);
        } catch (\Exception $e) {
            // Return error response.
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the course type status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function addBoard(Request $request)
    {
        // Validate only the board name
        $validator = Validator::make($request->all(), [
            'board' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }
        $boardName = strtoupper($request->board);

        // Check if the board already exists in the database.
        $exists = DB::table('boards')->where('board_name', $boardName)->exists();
        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Board already added.',
            ], 409); // 409 Conflict
        }

        try {
            // Insert the new board into the boards table
            DB::table('boards')->insert([
                'board_name' => $request->board,
                'created_at' => now()->format('Y-m-d h:i:s A'),
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Board added successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the board.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function viewBoards()
    {
        try {
            // Retrieve all boards from the database
            $boards = DB::table('boards')->select('*')->get();

            if ($boards->isEmpty()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'No boards found.',
                    'data'    => [],
                ], 200);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Boards retrieved successfully.',
                'data'    => $boards,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while retrieving boards.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function addCourse(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'board' => 'required|string|max:255',
            'program_type' => 'required|string|max:255',
            'program_name' => 'required|string|max:255',
            'program_duration' => 'required|string|max:255',
            'program_code' => 'required|string|max:50|unique:courses,program_code',
            // 'course_type' => 'required|in:General,Lateral',
        ]);

        // Return validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
               // Check if the program code already exists
    $exists = DB::table('courses')->where('program_code', $request->program_code)->exists();

    if ($exists) {
        return response()->json([
            'status' => 'error',
            'message' => 'Program code already exists. Please use a unique program code.',
        ], 409); // 409 Conflict
    }

    $isProgramExistsInBoard = DB::table('courses')
            ->where('program_name', $request->program_name)
            ->where('board', $request->board)
            ->exists();

    if ($isProgramExistsInBoard) {
        return response()->json([
            'status' => 'error',
            'message' => 'Course already exists.',
        ], 409); // 409 Conflict
    }

    // Insert course data into the database
    DB::table('courses')->insert([
        'board' => $request->board,
        'program_type' => $request->program_type,
        'program_name' => $request->program_name,
        'program_duration' => $request->program_duration,
        'program_code' => $request->program_code,
        // 'course_type' => $request->course_type, 
        'status' => 'Active',
        'created_at' => now()->format('Y-m-d h:i:s A'),
        'updated_at' => now()->format('Y-m-d h:i:s A'),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Course added successfully.',
    ], 201);

        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding the course.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewCourses()
{
    try {
        // Fetch all courses from the database
        $courses = DB::table('courses')->select('*')->get();

        if ($courses->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No courses found.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Courses retrieved successfully.',
            'data' => $courses,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while retrieving courses.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function editCourse(Request $request, $courseId)
{
    // Validation rules
    $validator = Validator::make($request->all(), [
        'board' => 'required|string|max:255',
        'programType' => 'required|string|max:255',
        'programName' => 'required|string|max:255',
       'programDuration' =>'required|string|max:255',
    ]);
    
    // Log::info('Edit Course Request Data:', $request->all());

    // Return validation errors
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Check if the course exists
        $course = DB::table('courses')->where('id', $courseId)->first();

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found.',
            ], 404); // 404 Not Found
        }

        // Update course data in the database
        DB::table('courses')->where('id', $courseId)->update([
            'board' => $request->board,
            'program_type' => $request->programType,
            'program_name' => $request->programName,
            'program_duration' => $request->programDuration,
            'updated_at' => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Course updated successfully.',
        ], 200);

    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the course.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function toggleCourseStatus(Request $request, $courseId)
{
    try {
        // Validate the status value
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status value.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if the course exists
        $course = DB::table('courses')->where('id', $courseId)->first();

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found.',
            ], 404); // 404 Not Found
        }

        // Update the course status
        DB::table('courses')
            ->where('id', $courseId)
            ->update([
                'status' => $request->status,
                'updated_at' => now()->format('Y-m-d h:i:s A'),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => "Course status updated to {$request->status} successfully.",
        ], 200);

    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the course status.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function coursesSemister(Request $request)
{
    Log::info('coursesSemister called', ['input' => $request->all()]);

    $validator = Validator::make($request->all(), [
        'institute_id' => 'required|string|exists:institutions,_id',
        'year'         => 'required|integer|min:1900|max:' . date('Y'),
    ]);

    if ($validator->fails()) {
        Log::warning('coursesSemister validation failed', ['errors' => $validator->errors()->toArray()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $instituteId = $request->input('institute_id');
    $year        = $request->input('year');
    Log::info('coursesSemister validation passed', compact('instituteId','year'));

    try {
        // Fetch all intakes for that institute and year
        $allIntakes = DB::table('intakes')
            ->where('institute_id', $instituteId)
            ->where('year', $year)
            ->get();

        // Prioritize GENERAL; if none, fall back to LATERAL
        $generalIntakes = $allIntakes->where('intake_type', 'General');
        $intakesToProcess = $generalIntakes->isNotEmpty()
            ? $generalIntakes
            : $allIntakes->where('intake_type', 'Lateral');

        $result = [];
        foreach ($intakesToProcess as $intake) {
            $studentCount = DB::table('students')
                ->where('institute_id', $instituteId)
                ->where('program_code', $intake->program_code)
                ->where('intake_type', $intake->intake_type)
                ->whereYear('created_at', $year)
                ->count();

            $totalSemesters = intval($intake->program_duration) * 2;
            $startSem       = $intake->intake_type === 'Lateral' ? 3 : 1;
            $semesters      = [];
            for ($s = $startSem; $s <= $totalSemesters; $s++) {
                $semesters[] = $s;
            }

            $result[] = [
                'program_code'     => $intake->program_code,
                'program_name'     => $intake->program_name,
                'program_type'     => $intake->program_type,    // ← now included
                'program_duration' => $intake->program_duration,
                'intake_type'      => $intake->intake_type,
                'student_count'    => $studentCount,
                'semesters'        => $semesters,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error in coursesSemister', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred fetching courses & semesters.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


public function addIntake(Request $request)
{
    // Validation rules (including institute_id)
    $validator = Validator::make($request->all(), [
        'institute_id'      => 'required|string|exists:institutions,id',
        'program_code'      => 'required|string|exists:courses,program_code',
        'year'              => 'required|integer|min:' . (date('Y') - 3) . '|max:' . date('Y'), 
        'year_duration'     => 'required|integer|min:1|max:5',
        'starting_semester' => 'required|integer|min:1|max:8',
        'ending_semester'   => 'required|integer|min:1|max:8',
        'ending_year'       => 'required|integer|gte:year|lte:' . (date('Y') + 5),
        'intake_type'       => 'required|string|in:General,Lateral',
        'gen_intake'        => 'required|integer|min:0',
        'gen_intake_id'     => 'required|string|max:255',
        'ews_intake'        => 'nullable|integer|min:0',
        'ews_intake_id'     => 'nullable|string|max:255',
        'tfw_intake'        => 'nullable|integer|min:0',
        'tfw_intake_id'     => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed', ['errors' => $validator->errors()->toArray()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    // Fetch course details using program_code
    $course = DB::table('courses')->where('program_code', $request->program_code)->first();

    if (!$course) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Course not found.',
        ], 404);
    }

    // Fetch institution details using institute_id
    $institution = DB::table('institutions')->where('id', $request->institute_id)->first();
    if (!$institution) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Institution not found.',
        ], 404);
    }

    // Check if an intake with the same institute_id, program_code, and intake_type already exists
    $existingIntake = DB::table('intakes')
        ->where('institute_id', $request->institute_id)
        ->where('program_code', $request->program_code)
        ->where('intake_type', $request->intake_type)
        ->where('year', $request->year)
        ->first();

    if ($existingIntake) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Intake already added.',
        ], 409);
    }

    // Calculate total intake
    $totalIntake = ($request->gen_intake ?? 0)
                 + ($request->ews_intake ?? 0)
                 + ($request->tfw_intake ?? 0);

    try {
        // Insert intake data into the database, including institute_id
        DB::table('intakes')->insert([
            'institute_id'       => $request->institute_id,
            'institution_name'   => $institution->institution_name, 
            'institution_type'   => $institution->type,              
            'program_code'       => $request->program_code,
            'program_type'       => $course->program_type,
            'program_name'       => $course->program_name,
            'program_duration'   => $course->program_duration,
            'year'               => $request->year,
            'year_duration'      => $request->year_duration,
            'starting_semester'  => $request->starting_semester,
            'ending_semester'    => $request->ending_semester,
            'ending_year'        => $request->ending_year,
            'intake_type'        => $request->intake_type,
            'gen_intake'         => $request->gen_intake,
            'gen_intake_id'      => $request->gen_intake_id,
            'ews_intake'         => $request->ews_intake,
            'ews_intake_id'      => $request->ews_intake_id,
            'tfw_intake'         => $request->tfw_intake,
            'tfw_intake_id'      => $request->tfw_intake_id,
            'total_intake'       => $totalIntake,
            'status'             => 'Active',
            'created_at'         => now()->format('Y-m-d H:i:s'),
            'updated_at'         => now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Intake added successfully with Intake ID.',
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while adding the intake.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}



public function viewIntakes($program_code, Request $request)
{
    // Ensure institute_id is provided (e.g., via query parameter)
    $institute_id = $request->query('institute_id');
    if (!$institute_id) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Institution ID is required.',
        ], 422);
    }

    try {
        // Fetch intakes for the given program_code and institute_id
        $intakes = DB::table('intakes')
            ->where('program_code', $program_code)
            ->where('institute_id', $institute_id)
            ->orderBy('year', 'desc')
            ->get();

        if ($intakes->isEmpty()) {
            Log::warning('No intakes found in the database.', [
                'program_code' => $program_code,
                'institute_id' => $institute_id
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'No intakes found.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Intakes retrieved successfully.',
            'data'    => $intakes,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching intakes:', ['error' => $e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching intakes.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


public function viewAllIntakes(Request $request)
{
    try {
        $query = DB::table('intakes')->orderBy('year', 'desc');
        // If institute_id is provided, filter by it
        if ($request->has('institute_id')) {
            $query->where('institute_id', $request->institute_id);
        }
        $intakes = $query->get();

        if ($intakes->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No intakes found.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Intakes retrieved successfully.',
            'data'    => $intakes,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching all intakes:', ['error' => $e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching all intakes.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


public function editIntake(Request $request, $intakeId)
{
    // Validation rules (including institute_id to verify ownership)
    $validator = Validator::make($request->all(), [
        'institute_id'      => 'required|string|exists:institutions,id',
        'year'              => 'required|integer|min:' . (date('Y') - 3) . '|max:' . date('Y'),
        'year_duration'     => 'required|integer|min:1|max:5',
        'starting_semester' => 'required|integer|min:1|max:8',
        'ending_semester'   => 'required|integer|min:1|max:8|gte:starting_semester',
        'ending_year'       => 'required|integer|gte:year|lte:' . (date('Y') + 5),
        'gen_intake'        => 'required|integer|min:1',
        'ews_intake'        => 'nullable|integer|min:1',
        'tfw_intake'        => 'nullable|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // Verify that the intake exists and belongs to the specified institution
        $intake = DB::table('intakes')
            ->where('id', $intakeId)
            ->where('institute_id', $request->institute_id)
            ->first();

        if (!$intake) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Intake not found for the specified institution.',
            ], 404);
        }

        $totalIntake = ($request->gen_intake ?? 0)
                     + ($request->ews_intake ?? 0)
                     + ($request->tfw_intake ?? 0);

        // Update the intake data
        DB::table('intakes')
            ->where('id', $intakeId)
            ->where('institute_id', $request->institute_id)
            ->update([
                'year'              => $request->year,
                'year_duration'     => $request->year_duration,
                'starting_semester' => $request->starting_semester,
                'ending_semester'   => $request->ending_semester,
                'ending_year'       => $request->ending_year,
                'gen_intake'        => $request->gen_intake,
                'ews_intake'        => $request->ews_intake,
                'tfw_intake'        => $request->tfw_intake,
                'total_intake'      => $totalIntake,
                'updated_at'        => now()->format('Y-m-d H:i:s'),
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Intake updated successfully.',
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error updating intake:', [
            'intake_id' => $intakeId,
            'error'     => $e->getMessage(),
        ]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while updating the intake.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


public function toggleIntakeStatus(Request $request, $intakeId)
{
    try {
        // Validate status and institute_id
        $validator = Validator::make($request->all(), [
            'status'       => 'required|in:Active,Inactive',
            'institute_id' => 'required|string|exists:institutions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid input.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Verify the intake belongs to the provided institution
        $intake = DB::table('intakes')
            ->where('id', $intakeId)
            ->where('institute_id', $request->institute_id)
            ->first();

        if (!$intake) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Intake not found for the given institution.',
            ], 404);
        }

        // Update the intake status
        DB::table('intakes')
            ->where('id', $intakeId)
            ->where('institute_id', $request->institute_id)
            ->update([
                'status'     => $request->status,
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Intake status updated to {$request->status} successfully.",
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error toggling intake status:', [
            'intake_id' => $intakeId,
            'error'     => $e->getMessage(),
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while updating the intake status.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function viewIntakesByInstitution(Request $request)
{
    // Validate that institute_id is provided and exists in the institutions table.
    $request->validate([
        'institute_id' => 'required|string|exists:institutions,id',
    ]);

    try {
        // Query the intakes table filtering by the provided institute_id.
        $intakes = DB::table('intakes')
            ->where('institute_id', $request->institute_id)
            ->orderBy('year', 'desc')
            ->get();

        if ($intakes->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No intakes found for the given institution.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Intakes retrieved successfully.',
            'data'    => $intakes,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching intakes by institution:', ['error' => $e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching intakes.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function createNotice(Request $request)
{
    // Validation rules
    $validator = Validator::make($request->all(), [
        'title'          => 'required|string|max:255',
        'message'        => 'required|string',
        'institution_id' => 'required|string|exists:institutions,id',
        'program_code'   => 'required|string|exists:courses,program_code',
        'semester'       => 'required|integer|min:1|max:12',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        // Lookup the institution and course
        $institution = DB::table('institutions')
            ->where('id', $request->institution_id)
            ->first();

        $course = DB::table('courses')
            ->where('program_code', $request->program_code)
            ->first();

        // Insert notice into database, including names
        DB::table('notices')->insert([
            'title'            => $request->title,
            'message'          => $request->message,
            'institution_id'   => $request->institution_id,
            'institution_name' => $institution->institution_name,
            'program_code'     => $request->program_code,
            'program_name'     => $course->program_name,
            'semester'         => $request->semester,
            'created_at'       => now()->format('Y-m-d h:i:s A'),
            'updated_at'       => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Notice created successfully.',
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while creating the notice.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function viewNotices(Request $request)
{
    Log::info('viewNotices called', [
        'institution_id' => $request->input('institution_id'),
        'program_code'   => $request->input('program_code'),
        'semester'       => $request->input('semester'),
        'year'           => $request->input('year'),
    ]);

    try {
        $query = DB::table('notices')->select('*');

        // Filter by institution
        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
            Log::info('Applied institution filter', ['institution_id' => $request->institution_id]);
        }

        // Filter by program
        if ($request->filled('program_code')) {
            $query->where('program_code', $request->program_code);
            Log::info('Applied program filter', ['program_code' => $request->program_code]);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
            Log::info('Applied semester filter', ['semester' => $request->semester]);
        }

        // Filter by year of creation (string match)
        if ($request->filled('year')) {
            $year = $request->year;
            $query->where('created_at', 'like', "{$year}%");
            Log::info('Applied LIKE‐year filter', ['year' => $year]);
        }

        $notices = $query->orderBy('created_at', 'desc')->get();
        Log::info('Notices query executed', ['count' => $notices->count()]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Notices fetched successfully.',
            'data'    => $notices,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error in viewNotices', ['exception' => $e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching notices.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}




public function editNotice(Request $request, $noticeId)
{
    $validator = Validator::make($request->all(), [
        'title'   => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        $notice = DB::table('notices')->where('id', $noticeId)->first();

        if (!$notice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notice not found.',
            ], 404);
        }

        DB::table('notices')->where('id', $noticeId)->update([
            'title'      => $request->title,
            'message'    => $request->message,
            'updated_at' => now()->format('Y-m-d h:i:s A'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notice updated successfully.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the notice.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function deleteNotice($noticeId)
{
    try {
        $notice = DB::table('notices')->where('id', $noticeId)->first();

        if (!$notice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notice not found.',
            ], 404);
        }

        DB::table('notices')->where('id', $noticeId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notice deleted successfully.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while deleting the notice.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function addEvent(Request $request)
{
    Log::info('addEvent called', ['input' => $request->all()]);

    // 1. Validation
    Log::info('Validating request');
    $validator = Validator::make($request->all(), [
        'institution_id' => 'required|string|exists:institutions,_id',
        'title'          => 'required|string|max:255',
        'description'    => 'required|string',
        'event_date'     => 'required|date',
        'image'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }
    Log::info('Validation passed');

    try {
        // 2. Institution lookup
        Log::info('Looking up institution', ['institution_id' => $request->institution_id]);
        $inst = DB::table('institutions')
                  ->where('_id', $request->institution_id) // make sure this matches your column
                  ->first();

        if (!$inst) {
            Log::error('Institution not found', ['institution_id' => $request->institution_id]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Institution not found.',
            ], 404);
        }
        Log::info('Institution found', ['institution_name' => $inst->institution_name]);

        // 3. Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            Log::info('Processing uploaded image');
            $file = $request->file('image');
            $name = uniqid('event_') .'.'. $file->getClientOriginalExtension();
            $destination = public_path('assets/event_assets');
            Log::info('Moving file', ['src' => $file->getPathname(), 'dest' => "$destination/$name"]);
            $file->move($destination, $name);
            $imagePath = 'assets/event_assets/'.$name;
            Log::info('Image stored', ['image_path' => $imagePath]);
        } else {
            Log::info('No image uploaded');
        }

        // 4. Insert into events table
        Log::info('Inserting new event record', [
            'institution_id'   => $request->institution_id,
            'institution_name' => $inst->institution_name,
            'title'            => $request->title,
            'event_date'       => $request->event_date,
            'image_path'       => $imagePath,
        ]);

        DB::table('events')->insert([
            'institution_id'   => $request->institution_id,
            'institution_name' => $inst->institution_name,
            'title'            => $request->title,
            'description'      => $request->description,
            'event_date'       => $request->event_date,
            'image_path'       => $imagePath,
            'status'           => 'Active',
            'created_at'       => now()->format('Y-m-d h:i:s A'),
            'updated_at'       => now()->format('Y-m-d h:i:s A'),
        ]);

        Log::info('Event inserted successfully');
        return response()->json([
            'status'  => 'success',
            'message' => 'Event added successfully.',
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error adding event', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred adding the event.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


public function viewEvents(Request $request)
{
    try {
        $query = DB::table('events')->select('*');

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('event_date', $request->date);
        }

        $events = $query->orderBy('event_date','desc')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Events retrieved successfully.',
            'data'    => $events,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error fetching events', ['error'=>$e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while fetching events.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function editEvent(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'title'       => 'required|string|max:255',
        'description' => 'required|string',
        'event_date'  => 'required|date',
        'image'       => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        $event = DB::table('events')->where('id',$id)->first();
        if (!$event) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Event not found.',
            ], 404);
        }

        $update = [
            'title'       => $request->title,
            'description' => $request->description,
            'event_date'  => $request->event_date,
            'updated_at'  => now()->format('Y-m-d h:i:s A'),
        ];

        if ($request->hasFile('image')) {
            if ($event->image_path && file_exists(public_path($event->image_path))) {
                @unlink(public_path($event->image_path));
            }
            $file = $request->file('image');
            $name = uniqid('event_') .'.'. $file->getClientOriginalExtension();
            $file->move(public_path('assets/event_assets'), $name);
            $update['image_path'] = 'assets/event_assets/'.$name;
        }

        DB::table('events')->where('id',$id)->update($update);

        return response()->json([
            'status'  => 'success',
            'message' => 'Event updated successfully.',
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error editing event', ['error'=>$e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while updating the event.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

/**
 * Toggle an event's status between Active/Inactive.
 */
public function toggleEvent(Request $request, $id)
{
    try {
        $event = DB::table('events')->where('id',$id)->first();
        if (!$event) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Event not found.',
            ], 404);
        }

        $newStatus = $event->status === 'Active' ? 'Inactive' : 'Active';

        DB::table('events')
          ->where('id',$id)
          ->update([
              'status'     => $newStatus,
              'updated_at' => now()->format('Y-m-d h:i:s A'),
          ]);

        return response()->json([
            'status'     => 'success',
            'message'    => "Event status toggled to {$newStatus}.",
            'new_status' => $newStatus,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error toggling event status', ['error'=>$e->getMessage()]);
        return response()->json([
            'status'  => 'error',
            'message' => 'An error occurred while toggling status.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


}
