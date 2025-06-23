<?php

use App\Http\Controllers\web\admin\ChatController;
use Illuminate\Support\Facades\Route;


// auth
require __DIR__ . '/auth.php';


// admin
Route::middleware(['auth.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/admin.php';
    });


// chat
Route::middleware(['auth', 'role:school_admin'])->prefix('admin')->group(function () {
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{conversationId}', [ChatController::class, 'show']);
    Route::post('chat/{conversationId}/messages', [ChatController::class, 'storeMessage']);
});