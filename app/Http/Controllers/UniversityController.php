<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UniversityController extends Controller
{
    /**
     * Lấy danh sách các trường đại học.
     */
    public function index(Request $request)
    {

        // Eager loading quan hệ faculties
        $universities = University::with('faculties')->get();
        return response()->json($universities, 200);
    }


    /**
     * Tạo mới một trường đại học.
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $data = $validator->validated();

        // Nếu có file ảnh được upload, lưu file trực tiếp vào thư mục public/universities
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Tạo tên file duy nhất bằng cách thêm timestamp vào tên gốc của file
            $fileName = time() . '-' . $file->getClientOriginalName();
            // Đường dẫn đến thư mục public/universities
            $destinationPath = public_path('universities');
            // Di chuyển file vào thư mục chỉ định
            $file->move($destinationPath, $fileName);
            // Lưu lại đường dẫn tương đối, ví dụ: "universities/1636211234-avatar.jpg"
            $data['image'] = 'universities/' . $fileName;
        }
        
        $university = University::create($data);

        return response()->json([
            'message' => 'University created successfully',
            'data'    => $university
        ], 201);
    }

    /**
     * Hiển thị thông tin chi tiết của một trường đại học.
     */
    public function show(Request $request, $id)
    {

        // Eager loading quan hệ faculties
        $university = University::with('faculties')->find($id);
        if (!$university) {
            return response()->json(['message' => 'University not found'], 404);
        }

        return response()->json($university, 200);
    }

    /**
     * Cập nhật thông tin của một trường đại học.
     */
    public function update(Request $request, $id)
    {
        // Kiểm tra quyền admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Tìm trường đại học theo id
        $university = University::find($id);
        if (!$university) {
            return response()->json(['message' => 'University not found'], 404);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lấy dữ liệu đã validate
        $data = $validator->validated();

        // Nếu có file ảnh mới được upload, xử lý việc upload và xóa ảnh cũ
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if (!empty($university->image)) {
                $oldImagePath = public_path($university->image);
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }
            // Upload ảnh mới
            $file = $request->file('image');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $destinationPath = public_path('universities');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $fileName);
            $data['image'] = 'universities/' . $fileName;
        }

        // Cập nhật thông tin (các field như name, description, và image nếu có)
        $university->update($data);

        // Làm mới đối tượng để trả về dữ liệu mới nhất
        $university->refresh();

        return response()->json([
            'message' => 'University updated successfully.',
            'data' => $university,
        ], 200);
    }

    /**
     * Xóa một trường đại học.
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $university = University::find($id);
        if (!$university) {
            return response()->json(['message' => 'University not found'], 404);
        }

        $university->delete();

        return response()->json([
            'message' => 'University deleted successfully'
        ], 200);
    }

}
