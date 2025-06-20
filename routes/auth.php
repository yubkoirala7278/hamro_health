<?php

use App\Http\Controllers\Web\Auth\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/',[AuthController::class,'index'])->name('home');
Route::post('/login',[AuthController::class,'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth.admin')
    ->name('logout');

