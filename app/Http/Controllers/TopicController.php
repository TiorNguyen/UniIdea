<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\TopicMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    public function store(Request $request)
    {
        // Chỉ cho phép tài khoản có role "user" thực hiện chức năng này.
        if ($request->user()->role !== 'user') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'topic_name'       => 'required|string|max:255',
            'description'      => 'nullable|string',
            'guidance_teacher' => 'nullable|string|max:255',
            'report_file'      => 'nullable|file|mimes:pdf|max:2048',
            'topic_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'members'          => 'nullable|array',
            'members.*.name'           => 'required_with:members|string|max:255',
            'members.*.student_id'     => 'required_with:members|string|max:50',
            'members.*.university_id'  => 'required_with:members|exists:universities,id',
            'members.*.faculty_id'     => 'required_with:members|exists:faculties,id',
            'members.*.phone'          => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $data = $validator->validated();

        // Xử lý upload file báo cáo (report_file) nếu có
        if ($request->hasFile('report_file')) {
            $pdfFile = $request->file('report_file');
            $pdfFileName = time() . '-report-' . $pdfFile->getClientOriginalName();
            $destinationPath = public_path('topic_files');
            $pdfFile->move($destinationPath, $pdfFileName);
            $data['report_file'] = 'topic_files/' . $pdfFileName;
        }
        
        // Xử lý upload ảnh (topic_image) nếu có
        if ($request->hasFile('topic_image')) {
            $imageFile = $request->file('topic_image');
            $imageFileName = time() . '-image-' . $imageFile->getClientOriginalName();
            $destinationPath = public_path('topic_files');
            $imageFile->move($destinationPath, $imageFileName);
            $data['topic_image'] = 'topic_files/' . $imageFileName;
        }
        
        // Gán thông tin tự động từ user đăng nhập và các thông số mặc định
        $data['user_id'] = $request->user()->id;
        $data['leader_email'] = $request->user()->email;
        $data['submission_year'] = date('Y');
        $data['status'] = 'pending';
        
        // Tạo bản ghi topic
        $topic = Topic::create($data);

        // Nếu có đăng ký thành viên, thêm vào bảng topic_members
        if (isset($data['members']) && is_array($data['members'])) {
            foreach ($data['members'] as $memberData) {
                TopicMember::create([
                    'topic_id'      => $topic->id,
                    'name'          => $memberData['name'],
                    'student_id'    => $memberData['student_id'],
                    'university_id' => $memberData['university_id'],
                    'faculty_id'    => $memberData['faculty_id'],
                    'phone'         => $memberData['phone'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Topic registered successfully.',
            'data'    => $topic,
        ], 201);
    }

    public function index(Request $request)
    {
        // Bắt đầu query lấy các topic có trạng thái approved
        $query = Topic::with(['members.university', 'members.faculty'])
                  ->where('status', 'approved');

        // Nếu có truyền query parameter "year", lọc theo submission_year
        if ($request->has('year') && $request->year != '') {
            $query->where('submission_year', $request->year);
        }

        // Nếu có truyền query parameter "award", lọc theo award
        if ($request->has('award') && $request->award != '') {
            $query->where('award', $request->award);
        }

        // Sắp xếp theo thứ tự giảm dần theo id (hoặc bạn có thể dùng created_at nếu có)
        $topics = $query->orderBy('id', 'desc')->get();

        return response()->json($topics, 200);
    }

    public function show(Request $request, $id)
    {

        $topic = Topic::with('members')->find($id);
        
        if (!$topic) {
            return response()->json(['message' => 'Topic not found'], 404);
        }
        
        return response()->json($topic, 200);
    }

    public function search(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ request body
        $keyword = $request->input('q', '');

        // Tạo query để lấy các topic có status "approved" và có tên chứa từ khóa (không phân biệt chữ hoa/chữ thường)
        $topics = Topic::with('members')
            ->where('status', 'approved')
            ->whereRaw("LOWER(topic_name) LIKE ?", ['%' . strtolower($keyword) . '%'])
            ->orderBy('id', 'desc')
            ->get();    

        // Nếu không tìm thấy topic nào, trả về danh sách rỗng (hoặc bạn có thể trả về thông báo tùy chọn)
        if ($topics->isEmpty()) {
            return response()->json(['message' => 'Topic not found'], 404);
        }

        return response()->json($topics, 200);
    }

}
