<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Illuminate\Support\Facades\View;

class ForgetPasswordController extends Controller
{
    use ApiResponse;
public function sendOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $email = $request->email;
    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json(['error' => 'User not found!'], 400);
    }

    $otp = rand(100000, 999999);
    Cache::put('otp_' . $email, $otp, now()->addMinutes(5));

    try {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));
        $apiInstance = new TransactionalEmailsApi(null, $config);
        $htmlContent = View::make('emails.otp', ['otp' => $otp])->render();
        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => 'Your OTP Code',
            'sender' => ['email' => env('MAIL_FROM_ADDRESS'), 'name' => env('MAIL_FROM_NAME')],
            'to' => [['email' => $email]],
            'htmlContent' => $htmlContent,
        ]);

        $apiInstance->sendTransacEmail($sendSmtpEmail);

        return response()->json(['message' => 'OTP sent successfully'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to send OTP',
            'details' => $e->getMessage()
        ], 500);
    }
}

    public function forgetPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors(),
                "Validation failed",
                422
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
        if (!$user) {
            return $this->error('User not found!', 'error', 400);
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
