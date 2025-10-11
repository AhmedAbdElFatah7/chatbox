<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Friends\FriendController;
use App\Http\Controllers\Chat\ChatController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/send-otp', [ForgetPasswordController::class, 'sendOtp']);
Route::post('/forget-password-otp', [ForgetPasswordController::class, 'forgetPasswordOtp']);
Route::post('/reset-password', [ForgetPasswordController::class, 'resetPassword']);

Route::post('/verify-otp', [VerificationController::class, 'verifyOtp']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/friends/search', [FriendController::class, 'search']);
    Route::post('/friends/send', [FriendController::class, 'sendRequest']);
    Route::post('/friends/accept', [FriendController::class, 'acceptRequest']);
    Route::get('/friends', [FriendController::class, 'myFriends']);
    Route::get('/friends/requests', [FriendController::class, 'friendRequests']);

    // Chat routes
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/conversation', [ChatController::class, 'getConversation']);
    Route::post('/chat/mark-read', [ChatController::class, 'markAsRead']);
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount']);
    Route::get('/chat/conversations', [ChatController::class, 'getRecentConversations']);

});