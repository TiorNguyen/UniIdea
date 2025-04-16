<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'user') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate nội dung góp ý
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tạo góp ý
        $feedback = Feedback::create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Feedback sent successfully.',
            'data'    => $feedback,
        ], 201);
    }
}
