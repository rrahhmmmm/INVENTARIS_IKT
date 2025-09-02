<?php
use App\Http\Controllers\M_terminalController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['message' => 'API siappp']);
});

Route::apiResource('m_terminal', M_terminalController::class);
