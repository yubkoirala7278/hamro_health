<?php

use App\Http\Controllers\Web\Admin\HomeController;
use App\Http\Controllers\Web\Admin\SchoolController;
use App\Http\Controllers\web\admin\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index'])->name('home');


// school
Route::get('schools/dataTable', [SchoolController::class, 'dataTable'])->name('schools.dataTable');
Route::resources([
    'schools' => SchoolController::class,
]);

// student
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/dataTable', [StudentController::class, 'dataTable'])->name('students.dataTable');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::get('/students/{slug}', [StudentController::class, 'show'])->name('students.show');
Route::get('/students/{slug}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{slug}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{slug}', [StudentController::class, 'destroy'])->name('students.destroy');
