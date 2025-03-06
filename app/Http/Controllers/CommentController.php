<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseService; // Import the FirebaseService
use App\Models\UserToken;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($service_request_id)
    {
        $comments = Comment::where('service_request_id', $service_request_id)
            ->with('user') // Assuming there's a user relationship
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request data
        Log::info('Incoming request for adding a comment', [
            'request_data' => $request->all()
        ]);

        // Validate the request
        $validator = Validator::make($request->all(), [
            'service_request_id' => 'required|exists:service_requests,id',
            'user_id' => 'required|exists:users,id',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', [
                'errors' => $validator->errors()
            ]);
            return response()->json($validator->errors(), 422);
        }

        // Create comment
        $comment = Comment::create([
            'service_request_id' => $request->service_request_id,
            'user_id' => $request->user_id,
            'comment' => $request->comment,
        ]);

        
        $commentedUser = User::where('id', $request->user_id)->first();
        $generalServiceUsers = User::where('type', 'general_service')->get();
        
        $firebaseService = new FirebaseService();
        if($commentedUser->type !== 'general_service'){
            foreach ($generalServiceUsers as $user) {
                try {
                    // Create a notification entry for the user
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'comment',
                        'message' => $commentedUser->lastname . ', ' . $commentedUser->firstname .' commented on requested id:' . $request->service_request_id,
                        'isRead' => false,
                    ]);
            
                    // Retrieve all Expo push tokens for the user
                    $expoPushTokens = UserToken::where('user_id', $user->id)->pluck('expo_push_token');
            
                    // Log tokens for debugging (optional)
                    Log::info("Expo Push Tokens for User {$user->id}: ", $expoPushTokens->toArray());
            
                    // Send a notification for each Expo push token
                    foreach ($expoPushTokens as $expoPushToken) {
                        if ($expoPushToken) {
                            $firebaseService->sendNotification(
                                $expoPushToken,
                                'comment',
                                'Someone commented on requested services'
                            );
                        } else {
                            Log::warning("Missing Expo push token for User {$user->id}");
                        }
                    }
                } catch (Exception $e) {
                    // Log the error and optionally add the user to failed notifications
                    Log::error("Failed to send notification to user {$user->id}: {$e->getMessage()}");
                    $failedNotifications[] = $user->id;
                }
            }
        }
        

        // Log successful comment creation
        Log::info('New comment added successfully', [
            'comment_id' => $comment->id,
            'comment_data' => $comment
        ]);

        return response()->json($comment, 201);
    }

    /**
     * Show a single comment.
     */
    public function show($id)
    {
        $comment = Comment::with('user')->find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        return response()->json($comment);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment->update(['comment' => $request->comment]);

        Log::info('Comment updated', ['comment_id' => $comment->id]);

        return response()->json($comment);
    }

    /**
     * Delete a comment.
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->delete();

        Log::info('Comment deleted', ['comment_id' => $id]);

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
