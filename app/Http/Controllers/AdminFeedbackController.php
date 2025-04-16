<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    public function index(Request $request)
    {
        // Chỉ cho phép admin truy cập
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Lấy tất cả feedback (có thể nạp quan hệ user nếu muốn)
        $feedbacks = Feedback::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($feedbacks, 200);
    }


    public function destroy(Request $request, $id)
    {
        // Chỉ cho phép admin truy cập
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $feedback = Feedback::find($id);
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found'], 404);
        }

        $feedback->delete();
        return response()->json(['message' => 'Feedback deleted successfully.'], 200);
    }
}
