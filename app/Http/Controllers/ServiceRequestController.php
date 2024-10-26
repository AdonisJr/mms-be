<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Service;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade
use Carbon\Carbon; // Import Carbon

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the service requests.
     */
    // public function index()
    // {
    //     // Fetch all service requests with related service, user, and tasks
    //     $serviceRequests = ServiceRequest::with('service', 'user', 'tasks')->get();

    //     return response()->json($serviceRequests);
    // }

    public function index()
    {
        // Fetch all service requests with related service, user, tasks, and utility workers
        $serviceRequests = ServiceRequest::with([
            'service', 
            'user', 
            'tasks' // Include utility workers assigned to each task
        ])->get();

        return response()->json($serviceRequests);
    }

    /**
     * Store a newly created service request.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'requested_by' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'expected_start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'number_of_personnel' => 'nullable|integer',
            'classification' => 'nullable|in:immediate,short term,minimum term,project'
        ]);

        // Convert dates to Carbon instances if provided
        $data = $request->all();
        if ($request->has('expected_start_date')) {
            $data['expected_start_date'] = Carbon::parse($request->expected_start_date)->format('Y-m-d H:i:s');
        }
        if ($request->has('expected_end_date')) {
            $data['expected_end_date'] = Carbon::parse($request->expected_end_date)->format('Y-m-d H:i:s');
        }

        // Create a new service request
        $serviceRequest = ServiceRequest::create($data);

        return response()->json($serviceRequest, 201);
    }

    /**
     * Display the specified service request.
     */
    public function show($id)
    {
        $serviceRequest = ServiceRequest::with('service', 'user')->find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found'], 404);
        }

        return response()->json($serviceRequest);
    }

    /**
     * Update the specified service request.
     */
    public function update(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found'], 404);
        }

        // Validate the request
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'description' => 'nullable|string',
            'expected_start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'number_of_personnel' => 'nullable|integer',
            'classification' => 'nullable|in:immediate,short term,minimum term,project'
        ]);

        // Get the authenticated user
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Prepare the data for update
        $data = $request->all();
        
        // Handle the dates if provided
        if ($request->has('expected_start_date')) {
            $data['expected_start_date'] = Carbon::parse($request->expected_start_date)->format('Y-m-d H:i:s');
        }
        if ($request->has('expected_end_date')) {
            $data['expected_end_date'] = Carbon::parse($request->expected_end_date)->format('Y-m-d H:i:s');
        }

        // Check if the status is approved or rejected and set the approved_by field
        if ($request->status === 'approved' || $request->status === 'rejected') {
            $data['approved_by'] = $user->id; // Set the user ID as the approver
        }

        // Update the service request
        $serviceRequest->update($data);

        return response()->json($serviceRequest, 200);
    }


    /**
     * Remove the specified service request.
     */
    public function destroy($id)
    {
        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found'], 404);
        }

        // Delete the service request
        $serviceRequest->delete();

        return response()->json(['message' => 'Service request deleted successfully'], 200);
    }

    public function getByCurrentUser()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the user is not authenticated
        if (!$user) {
            // Log the failed attempt to fetch user service requests
            Log::warning('Unauthorized access attempt to fetch service requests');

            // Return a 401 Unauthorized response
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Log the request for debugging
        Log::info('Fetching service requests for user', ['user_id' => $user->id]);

        // Fetch service requests for the authenticated user
        $serviceRequests = ServiceRequest::with('service')->where('requested_by', $user->id)->get();

        // Check if any service requests were found
        if ($serviceRequests->isEmpty()) {
            return response()->json(['message' => 'No service requests found for this user'], 404);
        }

        // Return the service requests as JSON
        return response()->json($serviceRequests, 200);
    }

    // assign task
    // public function assignTask(Request $request, $id)
    // {
    //     $serviceRequest = ServiceRequest::find($id);

    //     if (!$serviceRequest) {
    //         return response()->json(['message' => 'Service request not found'], 404);
    //     }

    //     // Validate the request
    //     $request->validate([
    //         'assigned_to' => 'required|exists:users,id', // Ensure the utility worker exists
    //         'deadline' => 'required|date', // Ensure deadline is a valid date
    //     ]);

    //     // Convert the incoming deadline to MySQL-compatible format using Carbon
    //     $deadline = Carbon::parse($request->deadline)->format('Y-m-d H:i:s');

    //     // Create the task
    //     $task = Task::create([
    //         'service_request_id' => $serviceRequest->id,
    //         'assigned_to' => $request->assigned_to,
    //         'deadline' => $deadline,
    //         'status' => 'pending', // Default status
    //     ]);

    //     return response()->json($task, 201);
    // }

    public function assignTask(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found'], 404);
        }

        // Validate the request
        $request->validate([
            'assigned_to' => 'required|array', // Expecting an array of user IDs
            'assigned_to.*' => 'exists:users,id', // Validate each user ID
            'deadline' => 'required|date', // Ensure deadline is a valid date
        ]);

        // Create the task
        $task = Task::create([
            'service_request_id' => $serviceRequest->id,
            'deadline' => Carbon::parse($request->deadline)->format('Y-m-d H:i:s'),
            'status' => 'pending', // Default status
        ]);

        // Attach the assigned users to the task using the pivot table
        $task->utilityWorkers()->attach($request->assigned_to);

        return response()->json($task, 201);
    }



}
