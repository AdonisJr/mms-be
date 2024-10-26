<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index()
    {
        // Fetch all services
        $service = Service::orderBy('created_at', 'desc')->get();
        return response()->json($service);
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type_of_service' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        // If validation fails, return the error messages
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create a new service
        $service = Service::create($request->all());

        // Return response
        return response()->json($service, 201);
    }

    /**
     * Display a specific service.
     */
    public function show($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json($service);
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        // Validate the request
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type_of_service' => 'sometimes|required|in:repair,replacement,transfer_of_equipment,installation,general_cleaning,garbage_removal',
            'description' => 'nullable|string',
        ]);

        // Update service
        $service->update($request->all());

        return response()->json($service, 200);
    }

    /**
     * Remove the specified service.
     */
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        // Delete the service
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully'], 200);
    }
}
