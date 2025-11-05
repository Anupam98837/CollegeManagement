<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HostelController extends Controller
{
    /**
     * Add a new hostel.
     *
     * Expected request parameters:
     * - institution_id: required, must exist in the institutions table.
     * - hostel_name: required, string.
     * - hostel_type: required, string.
     * - hostel_address: required, string.
     * - hostel_fees: required, numeric.
     * - hostel_capacity: required, numeric.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addHostel(Request $request)
    {
        // Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'institution_id'  => 'required|string|exists:institutions,id',
            'hostel_name'     => 'required|string|max:255',
            'hostel_type'     => 'required|string|max:255',
            'hostel_address'  => 'required|string|max:500',
            'hostel_fees'     => 'required|numeric',
            'hostel_capacity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::error('Hostel addition validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the hostel already exists for this institution by hostel_name.
            $exists = DB::table('hostels')
                ->where('institution_id', $request->institution_id)
                ->where('hostel_name', $request->hostel_name)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Hostel already exists for this institution.',
                ], 409);
            }

            // Insert the new hostel record.
            DB::table('hostels')->insert([
                'institution_id'  => $request->institution_id,
                'hostel_name'     => $request->hostel_name,
                'hostel_type'     => $request->hostel_type,
                'hostel_address'  => $request->hostel_address,
                'hostel_fees'     => $request->hostel_fees,
                'hostel_capacity' => $request->hostel_capacity,
                'status'          => 'Active',
                'created_at'      => now()->format('Y-m-d H:i:s'),
                'updated_at'      => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Hostel added successfully.',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding hostel.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the hostel.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View hostels for a given institution.
     *
     * Expected request parameter:
     * - institution_id: required, must exist in the institutions table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewHostels(Request $request)
    {
        // Validate the institution_id.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
        ]);

        if ($validator->fails()) {
            Log::error('View hostels validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Retrieve hostels for the provided institution.
            $hostels = DB::table('hostels')
                ->where('institution_id', $request->institution_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Hostels retrieved successfully.',
                'data'    => $hostels,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving hostels.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching hostels.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Edit an existing hostel.
     *
     * Expected request parameters:
     * - hostel_name: required, string.
     * - hostel_type: required, string.
     * - hostel_address: required, string.
     * - hostel_fees: required, numeric.
     * - hostel_capacity: required, numeric.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Hostel record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function editHostel(Request $request, $id)
    {
        // Validate incoming data.
        $validator = Validator::make($request->all(), [
            'hostel_name'     => 'required|string|max:255',
            'hostel_type'     => 'required|string|max:255',
            'hostel_address'  => 'required|string|max:500',
            'hostel_fees'     => 'required|numeric',
            'hostel_capacity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::error('Edit hostel validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the hostel exists.
            $hostel = DB::table('hostels')->where('id', $id)->first();
            if (!$hostel) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Hostel not found.',
                ], 404);
            }

            // Update the hostel record.
            DB::table('hostels')
                ->where('id', $id)
                ->update([
                    'hostel_name'     => $request->hostel_name,
                    'hostel_type'     => $request->hostel_type,
                    'hostel_address'  => $request->hostel_address,
                    'hostel_fees'     => $request->hostel_fees,
                    'hostel_capacity' => $request->hostel_capacity,
                    'updated_at'      => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Hostel updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating hostel.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the hostel.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the status of a hostel.
     * If current status is 'Active', it becomes 'Inactive' and vice versa.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Hostel record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleHostel(Request $request, $id)
    {
        try {
            // Retrieve the hostel record.
            $hostel = DB::table('hostels')->where('id', $id)->first();
            if (!$hostel) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Hostel not found.',
                ], 404);
            }

            // Toggle the status.
            $newStatus = $hostel->status === 'Active' ? 'Inactive' : 'Active';

            // Update the status in the database.
            DB::table('hostels')
                ->where('id', $id)
                ->update([
                    'status'     => $newStatus,
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'     => 'success',
                'message'    => "Hostel status updated to {$newStatus} successfully.",
                'new_status' => $newStatus,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error toggling hostel status.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while toggling the hostel status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all hostels (without filtering by institution).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllHostels()
    {
        try {
            $hostels = DB::table('hostels')->orderBy('created_at', 'desc')->get();
            return response()->json([
                'status'  => 'success',
                'message' => 'All hostels retrieved successfully.',
                'data'    => $hostels,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving all hostels.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching all hostels.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Add a new room.
     *
     * Expected request parameters:
     * - institution_id: required, must exist in the institutions table.
     * - room_number: required, string.
     * - number_of_beds: required, numeric.
     * - hostel: required, string, must exist in the hostels table.
     * - note: optional, string.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRoom(Request $request)
    {
        // Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'institution_id'  => 'required|string|exists:institutions,id',
            'room_number'     => 'required|string|max:50',
            'number_of_beds'  => 'required|numeric',
            'hostel'          => 'required|string|max:500',
            'note'            => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Room addition validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the room already exists for this institution and hostel using the room number.
            $exists = DB::table('rooms')
                ->where('institution_id', $request->institution_id)
                ->where('hostel', $request->hostel)
                ->where('room_number', $request->room_number)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Room already exists for this hostel in this institution.',
                ], 409);
            }

            // Insert the new room record.
            DB::table('rooms')->insert([
                'institution_id'  => $request->institution_id,
                'room_number'     => $request->room_number,
                'number_of_beds'  => $request->number_of_beds,
                'hostel'          => $request->hostel,
                'note'            => $request->note,
                'status'          => 'Active',
                'created_at'      => now()->format('Y-m-d H:i:s'),
                'updated_at'      => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Room added successfully.',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding room.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the room.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View rooms for a given institution.
     *
     * Expected request parameter:
     * - institution_id: required, must exist in the institutions table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewRooms(Request $request)
    {
        // Validate the institution_id.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
        ]);

        if ($validator->fails()) {
            Log::error('View rooms validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Retrieve rooms for the provided institution.
            $rooms = DB::table('rooms')
                ->where('institution_id', $request->institution_id)
                ->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Rooms retrieved successfully.',
                'data'    => $rooms,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving rooms.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching rooms.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Edit an existing room.
     *
     * Expected request parameters:
     * - room_number: required, string.
     * - number_of_beds: required, numeric.
     * - hostel: required, string, must exist in the hostels table.
     * - note: optional, string.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Room record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function editRoom(Request $request, $id)
    {
        // Validate incoming data.
        $validator = Validator::make($request->all(), [
            'room_number'    => 'required|string|max:50',
            'number_of_beds' => 'required|numeric',
            'hostel'         => 'required|string|max:500',
            'note'           => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Edit room validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the room exists.
            $room = DB::table('rooms')->where('id', $id)->first();
            if (!$room) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Room not found.',
                ], 404);
            }

            // Update the room record.
            DB::table('rooms')
                ->where('id', $id)
                ->update([
                    'room_number'    => $request->room_number,
                    'number_of_beds' => $request->number_of_beds,
                    'hostel'         => $request->hostel,
                    'note'           => $request->note,
                    'updated_at'     => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Room updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating room.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the room.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the status of a room.
     * If current status is 'Active', it becomes 'Inactive' and vice versa.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Room record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleRoom(Request $request, $id)
    {
        try {
            // Retrieve the room record.
            $room = DB::table('rooms')->where('id', $id)->first();
            if (!$room) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Room not found.',
                ], 404);
            }

            // Toggle the status.
            $newStatus = $room->status === 'Active' ? 'Inactive' : 'Active';

            // Update the status in the database.
            DB::table('rooms')
                ->where('id', $id)
                ->update([
                    'status'     => $newStatus,
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'     => 'success',
                'message'    => "Room status updated to {$newStatus} successfully.",
                'new_status' => $newStatus,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error toggling room status.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while toggling the room status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all rooms (without filtering by institution).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRooms()
    {
        try {
            $rooms = DB::table('rooms')->orderBy('created_at', 'desc')->get();
            return response()->json([
                'status'  => 'success',
                'message' => 'All rooms retrieved successfully.',
                'data'    => $rooms,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving all rooms.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching all rooms.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
