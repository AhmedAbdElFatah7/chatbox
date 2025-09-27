<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Traits\ApiResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'code'     => rand(100000, 999999),
        ]);
        $token = JWTAuth::fromUser($user);
        return $this->success(
            [
                'user'  => $user,
                'token' => $token,
            ],
            "register successful" , 201
        );
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error('The email or password is incorrect.','error', 401);
        }

        $user = JWTAuth::user();

        if (!$user->is_verified) {
            return $this->error('User is not verified','error', 401);
        }
        return $this->success(
            [
                'user'  => $user,
                'token' => $token,
            ],
            "login successful" , 200
        );
    }
    public function profile()
    {
        return response()->json(JWTAuth::user());
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->success(null, "logout successful" , 200);
    }
}
