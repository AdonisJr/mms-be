<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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


    public function login(Request $request){
         // Validate the incoming request
         $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
        ]);
        
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            error_log('!user');
            return response()->json(['message' => 'Invalid Credentials', 'status' => 401], 401);
        }

        $token = $user->createToken('token_name')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function getUser(Request $request){
        return response()->json($request->user());
    }
}
