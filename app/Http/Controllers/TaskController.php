<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Import Log facade

class TaskController extends Controller
{
    /**
     * Get all tasks with related user, requested_by user, service, and service request
     */
    public function index()
    {
        $tasks = Task::with([
            'serviceRequest.service',      // The service associated with the service request
            'serviceRequest.requested',    // The user who requested the service
            'serviceRequest.approver',     // The user who approved the service
            'utilityWorkers'                // The user to whom the task was assigned
        ])->get();

        return response()->json($tasks);
    }

    /**
     * Show a specific task by its ID
     */
    public function show($id)
    {
        // Find task by ID with related entities
        $task = Task::with([
            'serviceRequest.service',               // Service details
            'serviceRequest.requestedByUser',       // User who requested the service
            'assignedUser'                          // The user the task was assigned to
        ])->find($id);

        // Check if the task exists
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Return the task with all related data
        return response()->json($task);
    }

//     public function updateTask(Request $request, $id)
// {
//     // Log the entire request for debugging purposes
//     Log::info('Received request data:', $request->all());

//     // Validate the request
//     $request->validate([
//         'status' => 'required|string',
//         'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
//     ]);

//     // Check and log proof file details if provided
//     if ($request->hasFile('proof')) {
//         $proof = $request->file('proof');
//         Log::info('Proof file details:', [
//             'original_name' => $proof->getClientOriginalName(),
//             'mime_type' => $proof->getMimeType(),
//             'size' => $proof->getSize(),
//             'temporary_path' => $proof->getRealPath(),
//         ]);
//     } else {
//         Log::warning('No proof image file provided in request.');
//     }

//     // Find the task by ID
//     $task = Task::find($id);
//     if (!$task) {
//         Log::error('Task not found with ID: ' . $id);
//         return response()->json(['message' => 'Task not found'], 404);
//     }

//     // Update the task's status
//     $task->status = $request->status;

//     // Handle proof image upload if provided
//     if ($request->hasFile('proof')) {
//         $fileName = time() . '_' . $proof->getClientOriginalName();
//         $filePath = $proof->storeAs('proofs', $fileName, 'public');
        
//         // Log the storage path
//         Log::info('Proof image saved at:', ['path' => $filePath]);

//         // Update proof field in the task with the file path
//         $task->proof = '/storage/' . $filePath;
//     }

//     // Save the updated task
//     $task->save();

//     // Log a success message
//     Log::info('Task updated successfully', ['task_id' => $task->id]);

//     return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
// }

    public function updateTaskStatus(Request $request, $id)
    {
        // Log the incoming request
        Log::info('Received request to update task status:', $request->all());

        // Validate the request for the status field only
        $request->validate([
            'status' => 'required|string'
        ]);

        // Find the task by ID
        $task = Task::find($id);
        if (!$task) {
            Log::error('Task not found with ID: ' . $id);
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Update the task's status
        $task->status = $request->status;
        $task->save();

        // Log a success message
        Log::info('Task status updated successfully', ['task_id' => $task->id]);

        return response()->json(['message' => 'Task status updated successfully', 'task' => $task]);
    }

    public function uploadProof(Request $request, $id)
    {
        // Log the incoming request
        Log::info('Received request to upload proof:', $request->all());

        // Validate the request for the proof field only
        try {
            $request->validate([
                // 'proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                'proof' => 'required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors specifically for debugging
            Log::error('Validation failed:', $e->errors());
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        }

        // Find the task by ID
        $task = Task::find($id);
        if (!$task) {
            Log::error('Task not found with ID: ' . $id);
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Handle proof image upload
        if ($request->hasFile('proof')) {
            $proof = $request->file('proof');
            $fileName = time() . '_' . $proof->getClientOriginalName();
            $filePath = $proof->storeAs('proofs', $fileName, 'public');

            // Log the storage path
            Log::info('Proof image saved at:', ['path' => $filePath]);

            // Update proof field in the task with the file path
            $task->proof = '/storage/' . $filePath;
            $task->save();

            // Log a success message
            Log::info('Proof image uploaded successfully', ['task_id' => $task->id]);

            return response()->json(['message' => 'Proof image uploaded successfully', 'task' => $task]);
        }

        Log::warning('No proof image file provided in request.');
        return response()->json(['message' => 'No proof image file provided'], 400);
    }




    public function assignedToMe()
    {
        // Get the currently authenticated user using Auth facade
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Fetch tasks where the current user is among the assigned users
        $tasks = Task::with([
            'serviceRequest.service',      // The service associated with the service request
            'serviceRequest.requested',    // The user who requested the service
            'serviceRequest.approver',     // The user who approved the service
            'utilityWorkers.tasks'
        ])
        ->whereHas('utilityWorkers', function ($query) use ($user) {
            $query->where('users.id', $user->id); // Specify the table name for clarity
        })
        ->get();

        return response()->json($tasks);
    }


}
