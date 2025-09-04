<?php
use App\Http\Controllers\M_terminalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\M_divisiController;
use App\Http\Controllers\M_subdivisiController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API siappp']);
});

Route::apiResource('m_terminal', M_terminalController::class);
Route::apiResource('m_divisi', M_divisiController::class);
Route::apiResource('m_subdivisi', M_subdivisiController::class);