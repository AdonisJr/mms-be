<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Log the request for debugging
        Log::info('Fetching all users');

        // Fetch all users and return as JSON
        $users = User::with('tasks')->get();

        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required',
            'type' => 'required',
            'role' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        // If validation fails, return the error messages
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if a user already exists with the same email
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            // Log the error and return a 409 Conflict response
            Log::warning('User creation failed, email already exists', ['email' => $request->email]);

            return response()->json([
                'message' => 'A user with this email already exists.'
            ], 409);
        }

        // Create the new user
        $user = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'type' => $request->type,
            'role' => $request->role,
            'gender' => $request->gender,
            'department' => $request->department,
            'password' => Hash::make($request->password)
        ]);

        Log::info('New user created', ['user_id' => $user->id]);

        // Return the newly created user as JSON
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // If user not found, return a 404 error
        if (!$user) {
            Log::warning('User not found', ['user_id' => $id]);
            return response()->json(['message' => 'User not found'], 404);
        }

        // Log the request for debugging
        Log::info('Fetching user', ['user_id' => $id]);

        // Return the user as JSON
        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Find the user by ID
            $user = User::find($id);

            // If user not found, return a 404 error
            if (!$user) {
                Log::warning('User not found for update', ['user_id' => $id]);
                return response()->json(['message' => 'User not found'], 404);
            }

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'firstname' => 'sometimes|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'lastname' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'gender' => 'sometimes|string',
                'type' => 'sometimes|string',
                'role' => 'sometimes|string',
                'department' => 'nullable|string|max:255',
                'password' => 'nullable|min:8|confirmed',
            ]);

            // If validation fails, return the error messages
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Prepare the data to update
            $updateData = [
                'firstname' => $request->get('firstname', $user->firstname),
                'middlename' => $request->get('middlename', $user->middlename),
                'lastname' => $request->get('lastname', $user->lastname),
                'email' => $request->get('email', $user->email),
                'gender' => $request->get('gender', $user->gender),
                'type' => $request->get('type', $user->type),
                'role' => $request->get('role', $user->role),
                'department' => $request->get('department', $user->department)
            ];

            // If the password is provided, hash it
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Update the user with the new data
            $user->update($updateData);

            Log::info('User updated', ['user_id' => $user->id]);

            // Return the updated user as JSON
            return response()->json($user, 200);
        } catch (\Exception $e) {
            // Log the full exception with stack trace
            Log::error('Error updating user', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => $id
            ]);

            // Return a generic error message
            return response()->json(['message' => 'An error occurred while updating the user', 'error' => $e->getMessage()], 500);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // If user not found, return a 404 error
        if (!$user) {
            Log::warning('User not found for deletion', ['user_id' => $id]);
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete the user
        $user->delete();

        Log::info('User deleted', ['user_id' => $id]);

        // Return a success message
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function changePassword(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // 'confirmed' ensures new_password_confirmation is required
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if the provided current password matches the actual current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

        // Hash and update the new password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return success message
        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function getUserByType($type)
    {
        // Log the request for debugging
        Log::info('Fetching users by type', ['type' => $type]);

        // Check if the type is 'all' to return all users
        if ($type == 'all') {
            $users = User::orderBy('created_at', 'desc')->get(); // Order by created_at in descending order
            return response()->json($users, 200);
        }

        // Fetch users by the provided type and order by descending
        $users = User::with(['tasks', 'preventiveMaintenances'])
            ->where('type', $type)
            ->orderBy('created_at', 'desc') // Order by created_at in descending order
            ->get();

        // Check if any users are found
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found with the specified type'], 404);
        }

        // Return the users as JSON
        return response()->json($users, 200);
    }
}
