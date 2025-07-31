<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChildMedicalController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SchoolController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/schools', [SchoolController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/children/{childId}/medical', [ChildMedicalController::class, 'show']);
    Route::post('/children/{childId}/medicines', [ChildMedicalController::class, 'storeMedicine']);
    Route::post('/children/{childId}/medicines/{medicineId}/given', [ChildMedicalController::class, 'markDoseGiven']);
    Route::post('/children/{childId}/documents', [ChildMedicalController::class, 'uploadDocument']);
    Route::get('/clinic-visits', [ChildMedicalController::class, 'getClinicVisits']);
    Route::get('/medicines', [ChildMedicalController::class, 'getAllMedicines']);
    Route::get('/children/{childId}/medicines/{medicineId}', [ChildMedicalController::class, 'showMedicine']);
    Route::put('/children/{childId}/medicines/{medicineId}', [ChildMedicalController::class, 'updateMedicine']);
    Route::delete('/children/{childId}/medicines/{medicineId}', [ChildMedicalController::class, 'deleteMedicine']);
});
