<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        // Chỉ cho phép không cần token ở register & login
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    // Đăng ký -> tạo user -> đăng nhập trả token
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'email'     => ['required','email','max:255','unique:users,email'],
            'password'  => ['required','string','min:6'],
            'user_type' => ['nullable','in:1,2'], // 1 = recruiter, 2 = candidate
        ]);

        // User::setPasswordAttribute sẽ tự hash password
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => $data['password'],
            'user_type' => $data['user_type'] ?? 2, // mặc định ứng viên
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'message'      => 'Registered successfully',
            'user'         => $user->only(['id','name','email','user_type']),
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ], 201);
    }

    // Đăng nhập -> trả token
    public function login(Request $request)
    {
        $credentials = $request->only('email','password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    // Lấy thông tin user từ token
    public function me()
    {
        $u = auth('api')->user();
        return response()->json($u ? $u->only(['id','name','email','user_type']) : null);
    }

    // Đăng xuất (vô hiệu hoá token)
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Cấp token mới
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    // Trả format token chuẩn
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
