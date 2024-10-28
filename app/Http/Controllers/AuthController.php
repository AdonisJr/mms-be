<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'gender' => 'required|in:male,female',
            'type' => 'required',
            'role' => 'required',
            'department' => 'nullable|string|max:255',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'type' => $request->type,
            'role' => $request->role,
            'gender' => $request->gender,
            'department' => $request->department, // Ensure department is set
            'password' => Hash::make($request->password)
        ]);

        // $token = $user->createToken('token_name')->plainTextToken;

        return response()->json(['user' => $user],200);
    }


    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'expo_push_token' => 'required|string', // Validate the token
        ]);

        // Authenticate user
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create a token for the user
        $token = $user->createToken('token_name')->plainTextToken;

        // Save the expo_push_token to user_tokens table
        UserToken::updateOrCreate(
            ['user_id' => $user->id], // Find by user_id
            ['expo_push_token' => $request->expo_push_token] // Update or create the token
        );

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function getUser(Request $request){
        return response()->json($request->user());
    }
}
