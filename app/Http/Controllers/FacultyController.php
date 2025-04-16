<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        
        // Lấy tất cả các khoa kèm theo thông tin của trường
        $faculties = Faculty::with('university')->get();
        return response()->json($faculties, 200);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Validate dữ liệu đầu vào, bắt buộc phải có university_id và name.
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tạo mới khoa với dữ liệu đã được validate
        $faculty = Faculty::create($validator->validated());

        return response()->json([
            'message' => 'Faculty created successfully',
            'data'    => $faculty
        ], 201);
    }

    public function show(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $faculty = Faculty::with('university')->find($id);
        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        return response()->json($faculty, 200);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $faculty = Faculty::find($id);
        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'university_id' => 'sometimes|required|exists:universities,id',
            'name'          => 'sometimes|required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cập nhật thông tin khoa với dữ liệu đã được validate
        $faculty->update($validator->validated());

        return response()->json([
            'message' => 'Faculty updated successfully',
            'data'    => $faculty
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $faculty = Faculty::find($id);
        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        $faculty->delete();

        return response()->json([
            'message' => 'Faculty deleted successfully'
        ], 200);
    }
}
