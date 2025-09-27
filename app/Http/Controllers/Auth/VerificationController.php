<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class VerificationController extends Controller
{
    use ApiResponse;
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return $this->error(
                "Validation failed",
                422,
                $validator->errors()
            );
        }
        $email = $request->email;
        $enteredOtp = $request->otp;

        $cachedOtp = Cache::get('otp_' . $email);

        if (!$cachedOtp) {
            return $this->error('OTP not found!', 'error', 400);
        }

        if ($enteredOtp != $cachedOtp) {
            return $this->error('Invalid OTP!', 'error', 400);
        }
        Cache::forget('otp_' . $email);

        $user = User::where('email', $email)->first();
        $token = JWTAuth::fromUser($user);

            $user->is_verified = true;
            $user->save();
            return $this->success([ 'token' => $token ], "user verified successfully", 200);
    }
}
