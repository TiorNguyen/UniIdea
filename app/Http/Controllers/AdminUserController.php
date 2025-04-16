<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Kiểm tra tài khoản đang gọi API có role "admin" hay không
        $admin = $request->user();
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Lấy tất cả các user
        $users = User::where('role', 'user')->get();
        return response()->json($users, 200);
    }

    public function destroy(Request $request, $id)
    {
        // Kiểm tra tài khoản admin
        $admin = $request->user();
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot delete admin user'], 403);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
