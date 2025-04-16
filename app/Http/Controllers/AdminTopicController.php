<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminTopicController extends Controller
{
    public function index(Request $request)
    {
        // Chỉ cho phép admin truy cập
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $topics = Topic::orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")->get();
        return response()->json($topics, 200);
    }

    public function show(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $topic = Topic::with('members')->find($id);
        if (!$topic) {
            return response()->json(['message' => 'Topic not found'], 404);
        }

        return response()->json($topic, 200);
    }

    public function updateStatus(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $topic = Topic::find($id);
        if (!$topic) {
            return response()->json(['message' => 'Topic not found'], 404);
        }

        $newStatus = $request->status;
        

        $topic->status = $newStatus;
        $topic->save();

        return response()->json([
            'message' => 'Topic status updated successfully.',
            'data'    => $topic
        ], 200);
    }

    public function awardTopic(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'award' => 'required|in:first,second,third',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm topic theo id
        $topic = Topic::find($id);
        if (!$topic) {
            return response()->json(['message' => 'Topic not found'], 404);
        }

        // Cập nhật trường award
        $topic->award = $request->award;
        $topic->save();

        return response()->json([
            'message' => 'Award updated successfully.',
            'data'    => $topic,
        ], 200);
    }

}
