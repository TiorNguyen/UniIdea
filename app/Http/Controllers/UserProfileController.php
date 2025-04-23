<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    public function update(Request $request)
    {
        // Lấy thông tin user từ request đã xác thực
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Xử lý upload file avatar nếu có
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarFileName = time() . '-avatar-' . $avatarFile->getClientOriginalName();
            $destinationPath = public_path('avatars');

            // Nếu thư mục avatars chưa tồn tại, tạo mới
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Nếu user đã có avatar, bạn có thể xóa file cũ (nếu muốn)
            if ($user->avatar) {
                $oldAvatarPath = public_path($user->avatar);
                if (File::exists($oldAvatarPath)) {
                    File::delete($oldAvatarPath);
                }
            }

            // Di chuyển file avatar mới vào thư mục
            $avatarFile->move($destinationPath, $avatarFileName);
            $data['avatar'] = 'avatars/' . $avatarFileName;
        }

        // Cập nhật thông tin user (các field khác sẽ được update hoặc thêm mới nếu trước đó null)
        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data'    => $user,
        ], 200);
    }
}
