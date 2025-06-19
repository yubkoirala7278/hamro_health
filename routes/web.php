<?php


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

