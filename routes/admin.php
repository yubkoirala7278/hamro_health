<?php

use App\Http\Controllers\Web\Admin\HomeController;
use App\Http\Controllers\Web\Admin\SchoolController;
use App\Http\Controllers\web\admin\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index'])->name('home');

// school
Route::middleware(['role:admin'])->group(function () {
    Route::get('schools/dataTable', [SchoolController::class, 'dataTable'])->name('schools.dataTable');
    Route::resource('schools', SchoolController::class);
});

// student
// Route::middleware(['role:school_admin'])->group(function () {
    Route::get('students/dataTable', [StudentController::class, 'dataTable'])->name('students.dataTable');
    Route::resource('students', StudentController::class);
// });
