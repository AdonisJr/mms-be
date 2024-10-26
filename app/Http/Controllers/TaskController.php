<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function updateTask(Request $request, $id)
    {
        // Validate the request to ensure required fields are provided
        $request->validate([
            'status' => 'required|string', // Ensure the status is provided
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validate the proof image if provided
        ]);

        // Find the task by ID
        $task = Task::find($id);

        // Check if the task exists
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Get the currently authenticated user
        $user = Auth::user();

        // Update the task's status
        $task->status = $request->status;

        // Handle proof image upload if provided
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('proofs', $fileName, 'public');

            // Update the proof field in the task with the file path
            $task->proof = '/storage/' . $filePath;
        }

        // Save the updated task
        $task->save();

        // Return a success response
        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
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
