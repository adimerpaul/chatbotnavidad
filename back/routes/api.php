<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AsignacionEstudianteController;
use App\Http\Controllers\AtencionManualController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\ReporteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/doctor-horarios/{doctor_id}', [DoctorScheduleController::class, 'horariosPorDoctor']);
    Route::get('/doctores-select', [DoctorController::class, 'select']);

    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\UserController::class, 'me']);


    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy']);
    Route::put('/updatePassword/{user}', [App\Http\Controllers\UserController::class, 'updatePassword']);
    Route::post('/{user}/avatar', [App\Http\Controllers\UserController::class, 'updateAvatar']);

    Route::apiResource('doctors', DoctorController::class);
    Route::post('doctors/{doctor}/schedules', [DoctorScheduleController::class, 'store']);
    Route::put('doctor-schedules/{id}', [DoctorScheduleController::class, 'update']);
    Route::delete('doctor-schedules/{id}', [DoctorScheduleController::class, 'destroy']);
    Route::apiResource('preguntas', PreguntaController::class);
    Route::get('/reportes/resumen', [ReporteController::class, 'resumen']);

    Route::prefix('atencion-clientes')->group(function () {
        Route::get('number', [AtencionManualController::class, 'number']);
    });

    Route::prefix('atencion-manual')->group(function () {
        Route::post('/', [AtencionManualController::class, 'store']);
        Route::delete('{phone}', [AtencionManualController::class, 'destroy']);
    });

    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::put('{id}/cancelar', [AppointmentController::class, 'cancelar']);
        Route::put('{id}', [AppointmentController::class, 'update']);
        Route::post('/reporte-diario', [AppointmentController::class, 'reporteDiario']);
    });

});
Route::get('/reporteDiario', [AppointmentController::class, 'reporteDiario']);
