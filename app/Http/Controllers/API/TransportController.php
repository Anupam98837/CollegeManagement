<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransportController extends Controller
{
    /**
     * Add a new vehicle.
     *
     * Expected request parameters:
     * - institution_id: required, must exist in the institutions table.
     * - vehicle_number: required, string.
     * - vehicle_model: required, string.
     * - driver_name: required, string.
     * - note: optional, string.
     * - driver_phone: required, string.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVehicle(Request $request)
    {
        // Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
            'vehicle_number' => 'required|string|max:50',
            'vehicle_model'  => 'required|string|max:100',
            'driver_name'    => 'required|string|max:100',
            'note'           => 'nullable|string|max:500',
            'driver_phone'   => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            Log::error('Vehicle addition validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the vehicle already exists for this institution using the vehicle number.
            $exists = DB::table('vehicles')
                ->where('institution_id', $request->institution_id)
                ->where('vehicle_number', $request->vehicle_number)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Vehicle already exists for this institution.',
                ], 409);
            }

            // Insert the new vehicle record.
            DB::table('vehicles')->insert([
                'institution_id' => $request->institution_id,
                'vehicle_number' => $request->vehicle_number,
                'vehicle_model'  => $request->vehicle_model,
                'driver_name'    => $request->driver_name,
                'note'           => $request->note,
                'driver_phone'   => $request->driver_phone,
                'status'         => 'Active',
                'created_at'     => now()->format('Y-m-d H:i:s'),
                'updated_at'     => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Vehicle added successfully.',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error adding vehicle.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the vehicle.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View vehicles for a given institution.
     *
     * Expected request parameter:
     * - institution_id: required, must exist in the institutions table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewVehicles(Request $request)
    {
        // Validate the institution_id.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
        ]);

        if ($validator->fails()) {
            Log::error('View vehicles validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Retrieve vehicles for the provided institution.
            $vehicles = DB::table('vehicles')
                ->where('institution_id', $request->institution_id)
                ->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Vehicles retrieved successfully.',
                'data'    => $vehicles,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicles.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching vehicles.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Edit an existing vehicle.
     *
     * Expected request parameters:
     * - vehicle_number: required, string.
     * - vehicle_model: required, string.
     * - driver_name: required, string.
     * - note: optional, string.
     * - driver_phone: required, string.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Vehicle record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function editVehicle(Request $request, $id)
    {
        // Validate incoming data.
        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|max:50',
            'vehicle_model'  => 'required|string|max:100',
            'driver_name'    => 'required|string|max:100',
            'note'           => 'nullable|string|max:500',
            'driver_phone'   => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            Log::error('Edit vehicle validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the vehicle exists.
            $vehicle = DB::table('vehicles')->where('id', $id)->first();
            if (!$vehicle) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Vehicle not found.',
                ], 404);
            }

            // Update the vehicle record.
            DB::table('vehicles')
                ->where('id', $id)
                ->update([
                    'vehicle_number' => $request->vehicle_number,
                    'vehicle_model'  => $request->vehicle_model,
                    'driver_name'    => $request->driver_name,
                    'note'           => $request->note,
                    'driver_phone'   => $request->driver_phone,
                    'updated_at'     => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Vehicle updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating vehicle.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the vehicle.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the status of a vehicle.
     * If current status is 'Active', it becomes 'Inactive' and vice versa.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Vehicle record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleVehicleStatus(Request $request, $id)
    {
        try {
            // Retrieve the vehicle record.
            $vehicle = DB::table('vehicles')->where('id', $id)->first();
            if (!$vehicle) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Vehicle not found.',
                ], 404);
            }

            // Toggle the status.
            $newStatus = $vehicle->status === 'Active' ? 'Inactive' : 'Active';

            // Update the status in the database.
            DB::table('vehicles')
                ->where('id', $id)
                ->update([
                    'status'     => $newStatus,
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'     => 'success',
                'message'    => "Vehicle status updated to {$newStatus} successfully.",
                'new_status' => $newStatus,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error toggling vehicle status.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while toggling the vehicle status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Add a new transport route.
     *
     * Expected request parameters:
     * - institution_id: required, must exist in the institutions table.
     * - route_from: required, string.
     * - route_to: required, string.
     * - route_fare: required, numeric.
     * - period: required, string.
     * - transport_vehicles: optional, string.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTransportRoute(Request $request)
    {
        // Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'institution_id'      => 'required|string|exists:institutions,id',
            'route_from'          => 'required|string|max:255',
            'route_to'            => 'required|string|max:255',
            'route_fare'          => 'required|numeric',
            'period'              => 'required|string|max:100',
            'transport_vehicles'  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Transport route addition validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the transport route already exists for this institution
            // using the same route_from and route_to.
            $exists = DB::table('transport_routes')
                ->where('institution_id', $request->institution_id)
                ->where('route_from', $request->route_from)
                ->where('route_to', $request->route_to)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Transport route already exists for this institution.',
                ], 409);
            }

            // Insert the new transport route record.
            DB::table('transport_routes')->insert([
                'institution_id'     => $request->institution_id,
                'route_from'         => $request->route_from,
                'route_to'           => $request->route_to,
                'route_fare'         => $request->route_fare,
                'period'             => $request->period,
                'transport_vehicles' => $request->transport_vehicles,
                'status'             => 'Active',
                'created_at'         => now()->format('Y-m-d H:i:s'),
                'updated_at'         => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Transport route added successfully.',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding transport route.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the transport route.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View transport routes for a given institution.
     *
     * Expected request parameter:
     * - institution_id: required, must exist in the institutions table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewTransportRoutes(Request $request)
    {
        // Validate the institution_id.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
        ]);

        if ($validator->fails()) {
            Log::error('View transport routes validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Retrieve transport routes for the provided institution.
            $routes = DB::table('transport_routes')
                ->where('institution_id', $request->institution_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Transport routes retrieved successfully.',
                'data'    => $routes,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving transport routes.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching transport routes.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Edit an existing transport route.
     *
     * Expected request parameters:
     * - route_from: required, string.
     * - route_to: required, string.
     * - route_fare: required, numeric.
     * - period: required, string.
     * - transport_vehicles: optional, string.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Transport route record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function editTransportRoute(Request $request, $id)
    {
        // Validate the incoming data.
        $validator = Validator::make($request->all(), [
            'route_from'          => 'required|string|max:255',
            'route_to'            => 'required|string|max:255',
            'route_fare'          => 'required|numeric',
            'period'              => 'required|string|max:100',
            'transport_vehicles'  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Edit transport route validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the transport route exists.
            $route = DB::table('transport_routes')->where('id', $id)->first();
            if (!$route) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Transport route not found.',
                ], 404);
            }

            // Update the transport route.
            DB::table('transport_routes')
                ->where('id', $id)
                ->update([
                    'route_from'         => $request->route_from,
                    'route_to'           => $request->route_to,
                    'route_fare'         => $request->route_fare,
                    'period'             => $request->period,
                    'transport_vehicles' => $request->transport_vehicles,
                    'updated_at'         => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Transport route updated successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating transport route.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the transport route.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the status of a transport route.
     * Automatically toggles between 'Active' and 'Inactive'.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id Transport route record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleTransportRoute(Request $request, $id)
    {
        try {
            // Retrieve the transport route.
            $route = DB::table('transport_routes')->where('id', $id)->first();
            if (!$route) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Transport route not found.',
                ], 404);
            }

            // Toggle the status.
            $newStatus = $route->status === 'Active' ? 'Inactive' : 'Active';

            // Update the status in the database.
            DB::table('transport_routes')
                ->where('id', $id)
                ->update([
                    'status'     => $newStatus,
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'     => 'success',
                'message'    => "Transport route status updated to {$newStatus} successfully.",
                'new_status' => $newStatus,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error toggling transport route status.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while toggling the transport route status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
