<?php
use App\Http\Controllers\M_terminalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\M_divisiController;
use App\Http\Controllers\M_subdivisiController;
use App\Http\Controllers\M_lokasiController;
use App\Http\Controllers\M_modelController;
use App\Http\Controllers\M_statusController;
use App\Http\Controllers\M_indeksController;
use App\Http\Controllers\M_klasifikasiController;
use App\Http\Controllers\M_roleController;
use App\Http\Controllers\M_retensiController;
use App\Http\Controllers\M_userController;
use App\Http\Controllers\M_parameterController;


use App\Http\Controllers\AuthController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API siappp']);
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::apiResource('m_subdivisi', M_subdivisiController::class);
Route::get('/m_subdivisi/divisi/{id}', [M_SUBDIVISICONTROLLER::class, 'getByDivisi']);

Route::apiResource('m_lokasi', M_lokasiController::class);
Route::apiResource('m_model', M_modelController::class);
Route::apiResource('m_status', M_statusController::class);
Route::apiResource('m_indeks', M_indeksController::class);
Route::apiResource('m_klasifikasi', M_klasifikasiController::class);
Route::apiResource('m_retensi', M_retensiController::class);

Route::apiResource('m_parameter', M_parameterController::class);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// export api
Route::get('/terminal/export-template', [M_TERMINALCONTROLLER::class, 'exportTemplate']);
Route::get('/terminal/export', [M_TERMINALCONTROLLER::class, 'exportExcel']);
Route::get('/user/export', [M_USERCONTROLLER::class, 'exportExcel']);

Route::get('/divisi/export', [M_DIVISICONTROLLER::class,'exportExcel']);
Route::get('/divisi/export-template', [M_DIVISICONTROLLER::class, 'exportTemplate']);

Route::get('/subdivisi/export', [M_SUBDIVISICONTROLLER::class,'exportExcel']);
Route::get('/subdivisi/export-template', [M_SUBDIVISICONTROLLER::class,'exportTemplate']);

// id divisi for subdiv
Route::get('/m_subdivisi/divisi/{id}', [M_SUBDIVISICONTROLLER::class, 'getByDivisi']);



Route::get('/role/export', [M_ROLECONTROLLER::class,'exportExcel']);


Route::middleware('auth:sanctum', 'role:ADMIN')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    // terminal
    Route::apiResource('m_terminal', M_TERMINALCONTROLLER::class);
    Route::post('/terminal/import', [M_TERMINALCONTROLLER::class, 'importExcel']);
    // divisi
    Route::post('/divisi/import', [M_DIVISICONTROLLER::class, 'importExcel']);
    Route::apiResource('m_divisi', M_divisiController::class);
    // subdivisi
    Route::apiResource('m_subdivisi', M_subdivisiController::class);
    Route::post('/subdivisi/import', [M_SUBDIVISICONTROLLER::class, 'importExcel']);
    // user
    Route::apiResource('m_user', M_userController::class);
    // role
    Route::apiResource('m_role', M_roleController::class);
});