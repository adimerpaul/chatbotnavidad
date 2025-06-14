<?php

use App\Http\Controllers\AsignacionEstudianteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\UserController::class, 'me']);


    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy']);
    Route::put('/updatePassword/{user}', [App\Http\Controllers\UserController::class, 'updatePassword']);
    Route::post('/{user}/avatar', [App\Http\Controllers\UserController::class, 'updateAvatar']);

    Route::get('/cursos', [App\Http\Controllers\CursoController::class, 'index']);
    Route::post('/cursos', [App\Http\Controllers\CursoController::class, 'store']);
    Route::put('/cursos/{curso}', [App\Http\Controllers\CursoController::class, 'update']);
    Route::delete('/cursos/{curso}', [App\Http\Controllers\CursoController::class, 'destroy']);

    Route::get('/estudiantes', [App\Http\Controllers\EstudianteController::class, 'index']);
    Route::post('/estudiantes', [App\Http\Controllers\EstudianteController::class, 'store']);
    Route::put('/estudiantes/{estudiante}', [App\Http\Controllers\EstudianteController::class, 'update']);
    Route::delete('/estudiantes/{estudiante}', [App\Http\Controllers\EstudianteController::class, 'destroy']);

    Route::get('/docentes', [App\Http\Controllers\DocenteController::class, 'index']);
    Route::post('/docentes', [App\Http\Controllers\DocenteController::class, 'store']);
    Route::put('/docentes/{docente}', [App\Http\Controllers\DocenteController::class, 'update']);
    Route::delete('/docentes/{docente}', [App\Http\Controllers\DocenteController::class, 'destroy']);

    Route::get('/asignaciones', [App\Http\Controllers\AsignacionController::class, 'index']);
    Route::post('/asignaciones', [App\Http\Controllers\AsignacionController::class, 'store']);
    Route::put('/asignaciones/{asignacion}', [App\Http\Controllers\AsignacionController::class, 'update']);
    Route::get('/asignaciones/{id}', [App\Http\Controllers\AsignacionController::class, 'show']);
//    Route::delete('/asignaciones/{asignacion}', [App\Http\Controllers\AsignacionController::class, 'destroy']);
//    Route::delete('/asignacion-estudiantes/{asignacion}/{estudiante}', [AsignacionEstudianteController::class, 'destroy']);
    Route::delete('/asignacion-estudiantes-by-id/{id}', [App\Http\Controllers\AsignacionEstudianteController::class, 'destroyById']);
//    miscursos
    Route::get('/misCursos', [App\Http\Controllers\CursoController::class, 'misCursos']);


    Route::get('/asignacion-estudiantes', [App\Http\Controllers\AsignacionEstudianteController::class, 'index']);
    Route::post('/asignacion-estudiantes', [App\Http\Controllers\AsignacionEstudianteController::class, 'store']);
    Route::delete('/asignacion-estudiantes/{asignacionEstudiante}', [App\Http\Controllers\AsignacionEstudianteController::class, 'destroy']);

});
