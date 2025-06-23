<?php

use App\Http\Controllers\web\admin\ChatController;
use App\Http\Controllers\Web\Admin\HomeController;
use App\Http\Controllers\web\admin\MedicalReportController;
use App\Http\Controllers\Web\Admin\SchoolController;
use App\Http\Controllers\web\admin\StudentController;
use Illuminate\Support\Facades\Route;


Route::get('/home', [HomeController::class, 'redirectBasedOnRole'])->name('home');


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


// medical
Route::prefix('/students/{slug}/medical-reports')->group(function () {
    Route::get('/', [MedicalReportController::class, 'medicalReportsIndex'])->name('students.medical_reports.index');
    Route::get('/dataTable', [MedicalReportController::class, 'medicalReportsDataTable'])->name('students.medical_reports.dataTable');
    Route::get('/create', [MedicalReportController::class, 'medicalReportsCreate'])->name('students.medical_reports.create');
    Route::post('/', [MedicalReportController::class, 'medicalReportsStore'])->name('students.medical_reports.store');
    Route::get('/{id}', [MedicalReportController::class, 'medicalReportsShow'])->name('students.medical_reports.show');
    Route::get('/{id}/edit', [MedicalReportController::class, 'medicalReportsEdit'])->name('students.medical_reports.edit');
    Route::put('/{id}', [MedicalReportController::class, 'medicalReportsUpdate'])->name('students.medical_reports.update');
    Route::delete('/{id}', [MedicalReportController::class, 'medicalReportsDestroy'])->name('students.medical_reports.destroy');
});

