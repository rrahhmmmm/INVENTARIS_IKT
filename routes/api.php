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

Route::get('/ping', function () {
    return response()->json(['message' => 'API siappp']);
});

Route::apiResource('m_terminal', M_terminalController::class);
Route::apiResource('m_divisi', M_divisiController::class);
Route::apiResource('m_subdivisi', M_subdivisiController::class);
Route::apiResource('m_lokasi', M_lokasiController::class);
Route::apiResource('m_model', M_modelController::class);
Route::apiResource('m_status', M_statusController::class);
Route::apiResource('m_indeks', M_indeksController::class);
Route::apiResource('m_klasifikasi', M_klasifikasiController::class);
Route::apiResource('m_role', M_roleController::class);