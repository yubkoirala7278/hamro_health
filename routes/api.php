<?php

use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ChatApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

// protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Phone number apis
    Route::put('user/add-phone-number', [UserController::class, 'addPhoneNumber']);
    Route::put('user/resend-otp', [UserController::class, 'reSendOtp']);
    Route::put('user/verify-otp', [UserController::class, 'verifyOtp']);
});


// chat
Route::middleware(['auth:sanctum'])->prefix('chat')->group(function () {
    Route::get('conversations', [ChatApiController::class, 'getConversations']);
    Route::get('conversations/{conversationId}/messages', [ChatApiController::class, 'getMessages']);
    Route::post('conversations/{conversationId}/messages', [ChatApiController::class, 'sendMessage']);
});