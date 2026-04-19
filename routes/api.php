<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // user profile (semua role bisa akses)
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
    Route::middleware('role:admin')->group(function () {

        Route::get('/admin/dashboard', function () {
            return response()->json([
                'message' => 'Admin dashboard'
            ]);
        });

        Route::get('/admin/users', function () {
            return 'List all users';
        });
    });

/*
|--------------------------------------------------------------------------
| DOCTOR ONLY
|--------------------------------------------------------------------------
*/
    Route::middleware('role:doctor')->group(function () {

        Route::get('/doctor/dashboard', function () {
            return response()->json([
                'message' => 'Doctor dashboard'
            ]);
        });

        Route::get('/doctor/appointments', function () {
            return 'Doctor appointments';
        });
    });

/*
|--------------------------------------------------------------------------
| USER / PATIENT ONLY
|--------------------------------------------------------------------------
*/
    Route::middleware('role:user')->group(function () {

        Route::get('/patient/dashboard', function () {
            return response()->json([
                'message' => 'Patient dashboard'
            ]);
        });

        Route::post('/patient/book-appointment', function () {
            return 'Book appointment';
        });
    });

});