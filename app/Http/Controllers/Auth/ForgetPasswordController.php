<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\OtpMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgetPasswordController extends Controller
{
    use ApiResponse;
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!$user) {
            return $this->error('User not found!','error', 400);
        }
        if (Cache::has('otp_' . $request->email)) {
            return $this->error('OTP already sent!','error', 400);
        }
        $otp = rand(100000, 999999);

        Cache::put('otp_' . $email, $otp, now()->addMinutes(5));

        Mail::to($email)->send(new OtpMail($otp));

        return $this->success([], "OTP sent successfully", 200);
    }

    public function forgetPasswordOtp(Request $request)
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
            return $this->error('OTP not found!','error', 400);
        }

        if ($enteredOtp != $cachedOtp) {
            return $this->error('Invalid OTP!','error', 400);
        }
        Cache::forget('otp_' . $email);

        $user = User::where('email', $email)->first();
        if (!$user) {
            return $this->error('User not found!','error', 400);
        }
        return $this->success([], "otp verified successfully", 200);
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return $this->error(
                "Validation failed",
                422,
                $validator->errors()
            );
        }
        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        return $this->success(null, "Password has been reset successfully");
    }
}
