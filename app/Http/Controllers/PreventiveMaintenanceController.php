<?php

namespace App\Http\Controllers;

use App\Models\PreventiveMaintenance;
use App\Models\ServiceRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ServiceRequestController;

class PreventiveMaintenanceController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $tasks = PreventiveMaintenance::with('users') // Only load id and name from users
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'scheduled_date_from' => $task->scheduled_date_from,
                    'scheduled_date_to' => $task->scheduled_date_to,
                    'status' => $task->status,
                    'created_by' => $task->created_by,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                    'users' => $task->users->map(function ($user) {
                        return $user->toArray();
                    }),
                ];
            });

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'scheduled_date_from' => 'required|date',
            'scheduled_date_to' => 'required|date',
            'status' => 'required|string',
            'created_by' => 'required|exists:users,id', // Ensure the user creating it exists
            'user_ids' => 'required|array', // Validate user_ids as an array
            'user_ids.*' => 'exists:users,id', // Ensure each user_id exists in users table
        ]);

        // Convert scheduled dates to a format suitable for MySQL
        $validatedData['scheduled_date_from'] = Carbon::parse($validatedData['scheduled_date_from'])->format('Y-m-d H:i:s');
        $validatedData['scheduled_date_to'] = Carbon::parse($validatedData['scheduled_date_to'])->format('Y-m-d H:i:s');

        // Create a new preventive maintenance task
        $preventiveMaintenance = PreventiveMaintenance::create($validatedData);

        // Assign users to the preventive maintenance task
        $preventiveMaintenance->users()->sync($validatedData['user_ids']);

        return response()->json([
            'message' => 'Preventive maintenance created and users assigned successfully.',
            'preventive_maintenance' => $preventiveMaintenance,
        ], 201);
    }


    // Display the specified resource.
    public function show($id)
    {
        $task = PreventiveMaintenance::findOrFail($id);
        return response()->json($task);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        // Find the preventive maintenance task by its ID
        $task = PreventiveMaintenance::findOrFail($id);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'scheduled_date_from' => 'required|date',
            'scheduled_date_to' => 'required|date',
            'status' => 'required|string|max:50',
            'user_ids' => 'nullable|array',  // Accept an array of user IDs
            'user_ids.*' => 'exists:users,id',  // Ensure each user ID exists in the users table
        ]);

        // Update the preventive maintenance task
        $task->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'scheduled_date_from' => $validatedData['scheduled_date_from'],
            'scheduled_date_to' => $validatedData['scheduled_date_to'],
            'status' => $validatedData['status'],
        ]);

        // If user IDs are provided, sync them with the task
        if (isset($validatedData['user_ids'])) {
            $task->users()->sync($validatedData['user_ids']);  // Sync the users with the task
        }

        // Return a success response with the updated task and users
        return response()->json([
            'message' => 'Preventive maintenance task updated successfully!',
            'task' => $task->load('users'),  // Load the associated users
        ]);
    }


    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $task = PreventiveMaintenance::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    // Get preventive maintenance tasks for the authenticated user
    public function getMyPreventiveMaintenanceTasks()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Fetch preventive maintenance tasks with full user data
        $myTasks = PreventiveMaintenance::with(['users' => function ($query) {
            $query->select();
        }])->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        if ($myTasks->isEmpty()) {
            return response()->json(['message' => 'No preventive maintenance tasks found for this user'], 404);
        }

        return response()->json($myTasks, 200);
    }
}