<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function login (Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password) || $user->role !== 'admin'){
            return response()->json([
                'message' => 'Tài khoản không hợp lệ hoặc không có quyền Admin'
            ],403);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'name' => $user->name
        ],200);
    }

    public function logout (Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Đăng xuất thành công'
        ],200);
    }
}
