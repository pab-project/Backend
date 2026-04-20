<?php

use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\DoctorProfileController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\TimeSlotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES — tidak perlu login
|--------------------------------------------------------------------------
*/
Route::post('/register',         [AuthController::class, 'register']);
Route::post('/login',            [AuthController::class, 'login']);

// Password Reset
Route::post('/forgot-password',  [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password',   [ResetPasswordController::class, 'reset']);

// Lihat semua dokter & jadwal kosong (publik untuk semua)
Route::get('/doctors',              [DoctorController::class, 'index']);
Route::get('/doctors/{id}',         [DoctorController::class, 'show']);
Route::get('/doctors/{id}/slots',   [DoctorController::class, 'slots']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES — wajib login (semua role)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout',           [AuthController::class, 'logout']);
    Route::get('/me',                [AuthController::class, 'me']);
    Route::put('/change-password',   [AuthController::class, 'changePassword']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // ── Manajemen Dokter ──────────────────────────────────────
        Route::get('/doctors',           [DoctorController::class, 'index']);
        Route::post('/doctors',          [DoctorController::class, 'store']);
        Route::put('/doctors/{id}',      [DoctorController::class, 'update']);
        Route::delete('/doctors/{id}',   [DoctorController::class, 'destroy']);

        // ── Manajemen Pasien ──────────────────────────────────────
        Route::get('/patients',          [PatientController::class, 'index']);
        Route::get('/patients/{id}',     [PatientController::class, 'show']);
        Route::put('/patients/{id}',     [PatientController::class, 'update']);
        Route::delete('/patients/{id}',  [PatientController::class, 'destroy']);

        // ── Manajemen Appointment ─────────────────────────────────
        Route::get('/appointments',                      [AppointmentController::class, 'index']);
        Route::patch('/appointments/{id}/approve',       [AppointmentController::class, 'approve']);
        Route::patch('/appointments/{id}/reject',        [AppointmentController::class, 'reject']);

        // ── Rekam Medis ───────────────────────────────────────────
        Route::get('/medical-records',                   [MedicalRecordController::class, 'index']);
        Route::get('/medical-records/{id}',              [MedicalRecordController::class, 'showAdmin']);
    });

    /*
    |--------------------------------------------------------------------------
    | DOCTOR ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:doctor')->prefix('doctor')->group(function () {

        // ── Profil Dokter ─────────────────────────────────────────
        Route::get('/profile',           [DoctorProfileController::class, 'show']);
        Route::put('/profile',           [DoctorProfileController::class, 'update']);

        // ── Jadwal (Time Slots) ───────────────────────────────────
        Route::get('/slots',             [TimeSlotController::class, 'index']);
        Route::post('/slots',            [TimeSlotController::class, 'store']);
        Route::put('/slots/{id}',        [TimeSlotController::class, 'update']);
        Route::delete('/slots/{id}',     [TimeSlotController::class, 'destroy']);

        // ── Appointment Dokter ────────────────────────────────────
        Route::get('/appointments',                      [AppointmentController::class, 'doctorSchedule']);
        Route::patch('/appointments/{id}/done',          [AppointmentController::class, 'markDone']);

        // ── Rekam Medis ───────────────────────────────────────────
        Route::get('/medical-records',   [MedicalRecordController::class, 'doctorRecords']);
        Route::post('/medical-records',  [MedicalRecordController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | PATIENT ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:patient')->prefix('patient')->group(function () {

        // ── Profil Pasien ─────────────────────────────────────────
        Route::get('/profile',           [PatientController::class, 'profile']);
        Route::put('/profile',           [PatientController::class, 'updateProfile']);

        // ── Appointment Pasien ────────────────────────────────────
        Route::get('/appointments',                      [AppointmentController::class, 'myAppointments']);
        Route::post('/appointments',                     [AppointmentController::class, 'store']);
        Route::patch('/appointments/{id}/cancel',        [AppointmentController::class, 'cancel']);

        // ── Rekam Medis Pasien ────────────────────────────────────
        Route::get('/medical-records',           [MedicalRecordController::class, 'myRecords']);
        Route::get('/medical-records/{id}',      [MedicalRecordController::class, 'show']);
    });
});