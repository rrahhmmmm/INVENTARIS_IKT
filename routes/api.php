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
use App\Http\Controllers\T_ARSIPCONTROLLER;
use App\Http\COntrollers\M_JENISNASKAHCONTROLLER;
use App\Http\Controllers\M_TINGKATPENGEMBANGANCONTROLLER;
use App\Http\Controllers\M_KONDISICONTROLLER;
use App\Http\Controllers\M_INSTALCONTROLLER;
use App\Http\Controllers\M_ANGGARANCONTROLLER;
use App\Http\Controllers\M_MERKCONTROLLER;
use App\Http\Controllers\T_INVENTARISCONTROLLER;
use App\Http\Controllers\DashboardInventarisController;

use App\Http\Controllers\AuthController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API siappp']);
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::get('/m_subdivisi/divisi/{id}', [M_SUBDIVISICONTROLLER::class, 'getByDivisi']);

Route::apiResource('m_lokasi', M_lokasiController::class);
Route::apiResource('m_model', M_modelController::class);
Route::apiResource('m_status', M_statusController::class);



Route::apiResource('m_parameter', M_parameterController::class);






// export api

Route::get('/klasifikasi/export', [M_KLASIFIKASICONTROLLER::class, 'exportExcel']);
Route::get('/klasifikasi/export-template', [M_KLASIFIKASICONTROLLER::class, 'exportTemplate']);

Route::get('/terminal/export-template', [M_TERMINALCONTROLLER::class, 'exportTemplate']);
Route::get('/terminal/export', [M_TERMINALCONTROLLER::class, 'exportExcel']);

Route::get('/user/export', [M_USERCONTROLLER::class, 'exportExcel']);

Route::get('/divisi/export', [M_DIVISICONTROLLER::class,'exportExcel']);
Route::get('/divisi/export-template', [M_DIVISICONTROLLER::class, 'exportTemplate']);

Route::get('/indeks/export', [M_indeksController::class,'exportExcel']);
Route::get('/indeks/export-template', [M_indeksController::class, 'exportTemplate']);

Route::get('/retensi/export', [M_RETENSICONTROLLER::class, 'exportExcel']);
Route::get('/retensi/export-template', [M_RETENSIController::class, 'exportTemplate']);

Route::get('/subdivisi/export', [M_SUBDIVISICONTROLLER::class,'exportExcel']);
Route::get('/subdivisi/export-template', [M_SUBDIVISICONTROLLER::class,'exportTemplate']);

// id divisi for subdiv
Route::get('/m_subdivisi/divisi/{id}', [M_SUBDIVISICONTROLLER::class, 'getByDivisi']);
Route::get('m_divisi', [M_divisiController::class, 'index']);

Route::get('/m_divisi/paginated', [M_DIVISICONTROLLER::class, 'indexPaginated']);
Route::get('/m_subdivisi/paginated', [M_SUBDIVISICONTROLLER::class, 'paginated']);




Route::get('/role/export', [M_ROLECONTROLLER::class,'exportExcel']);
Route::get('/role/export-template', [M_ROLECONTROLLER::class,'exportTemplate']);

Route::get('/jenisnaskah/export', [M_JENISNASKAHCONTROLLER::class, 'exportExcel']);
Route::get('/jenisnaskah/export-template', [M_JENISNASKAHCONTROLLER::class, 'exportTemplate']);

// Master Inventaris Export routes
Route::get('/instal/export', [M_INSTALCONTROLLER::class, 'exportExcel']);
Route::get('/instal/export-template', [M_INSTALCONTROLLER::class, 'exportTemplate']);
Route::get('/anggaran/export', [M_ANGGARANCONTROLLER::class, 'exportExcel']);
Route::get('/anggaran/export-template', [M_ANGGARANCONTROLLER::class, 'exportTemplate']);
Route::get('/merk/export', [M_MERKCONTROLLER::class, 'exportExcel']);
Route::get('/merk/export-template', [M_MERKCONTROLLER::class, 'exportTemplate']);

// Master Inventaris Paginated routes
Route::get('/m_instal/paginated', [M_INSTALCONTROLLER::class, 'indexPaginated']);
Route::get('/m_anggaran/paginated', [M_ANGGARANCONTROLLER::class, 'indexPaginated']);
Route::get('/m_merk/paginated', [M_MERKCONTROLLER::class, 'indexPaginated']);









Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum' )->group(function () {
    Route::get('t_arsip/overdue', [T_ARSIPCONTROLLER::class, 'overdue']);
    Route::get('t_arsip/overdue-musnah', [T_ARSIPCONTROLLER::class, 'overdueMusnah']);
    Route::put('t_arsip/{id}/mark-musnah', [T_ARSIPCONTROLLER::class, 'markAsMusnah']);
    Route::get('/t_arsip/check-nota-dinas', [T_ARSIPCONTROLLER::class, 'checkNotaDinas']);
    Route::get('/arsip/export', [T_ARSIPCONTROLLER::class, 'exportExcel']);


    Route::get('/m_retensi/all', [M_RETENSICONTROLLER::class, 'all']);

    Route::get('m_kondisi', [M_KONDISICONTROLLER::class, 'index']);
    Route::get('/m_kondisi/all', [M_KONDISICONTROLLER::class, 'all']);

    Route::get('m_jenisnaskah', [M_JENISNASKAHCONTROLLER::class, 'index']);
    Route::get('m_jenisnaskah/all', [M_JENISNASKAHCONTROLLER::class, 'all']);
    Route::get('m_jenisnaskah/{id}', [M_JENISNASKAHCONTROLLER::class, 'show']);
    

    Route::get('m_tingkatpengembangan', [M_TINGKATPENGEMBANGANCONTROLLER::class, 'index']);
    Route::get('m_tingkatpengembangan/all', [M_TINGKATPENGEMBANGANCONTROLLER::class, 'all']);
    Route::get('m_tingkatpengembangan/{id}', [M_TINGKATPENGEMBANGANCONTROLLER::class, 'show']);
    Route::apiResource('t_arsip', T_ARSIPCONTROLLER::class);
    
    Route::get('m_klasifikasi', [M_KLASIFIKASICONTROLLER::class, 'index']);
    Route::get('/m_klasifikasi/all', [M_KLASIFIKASICONTROLLER::class, 'all']);
    Route::get('m_klasifikasi/{id}', [M_KLASIFIKASICONTROLLER::class, 'show']);

    Route::get('m_indeks', [M_indeksController::class, 'index']);
    Route::get('/m_indeks/all', [M_indeksController::class, 'all']);
    Route::get('m_indeks/{id}', [M_indeksController::class, 'show']);

    Route::post('/klasifikasi/import', [M_KLASIFIKASICONTROLLER::class, 'importExcel']);
    Route::post('/indeks/import', [M_indekscontroller::class, 'importExcel']);
    Route::post('/retensi/import', [M_RETENSICONTROLLER::class, 'importExcel']);

    Route::apiResource('m_terminal', M_TERMINALCONTROLLER::class);

    // Inventaris Transaction routes
    Route::get('/inventaris/export', [T_INVENTARISCONTROLLER::class, 'exportExcel']);
    Route::get('/inventaris/export-template', [T_INVENTARISCONTROLLER::class, 'exportTemplate']);
    Route::post('/inventaris/import', [T_INVENTARISCONTROLLER::class, 'importExcel']);
    Route::apiResource('t_inventaris', T_INVENTARISCONTROLLER::class);

    // Master data for dropdowns
    Route::get('/m_merk/all', [M_MERKCONTROLLER::class, 'index']);
    Route::get('/m_instal/all', [M_INSTALCONTROLLER::class, 'index']);
    Route::get('/m_anggaran/all', [M_ANGGARANCONTROLLER::class, 'index']);

    // Dashboard Inventaris
    Route::get('/dashboard-inventaris/statistics', [DashboardInventarisController::class, 'getStatistics']);
});

Route::middleware('auth:sanctum','role:ADMIN')->group(function () {
    

    // master general
    // Route::apiResource('m_terminal', M_TERMINALCONTROLLER::class);
    Route::post('/m_divisi', [M_DIVISICONTROLLER::class,'store']);
    Route::put('/m_divisi/{id}', [M_DIVISICONTROLLER::class,'update']);
    Route::delete('/m_divisi/{id}', [M_DIVISICONTROLLER::class,'destroy']);

    Route::post('/m_subdivisi', [M_SUBDIVISICONTROLLER::class,'store']);
    Route::put('/m_subdivisi/{id}', [M_SUBDIVISICONTROLLER::class,'update']);
    Route::delete('/m_subdivisi/{id}', [M_SUBDIVISICONTROLLER::class,'destroy']);

    Route::post('/terminal/import', [M_TERMINALCONTROLLER::class, 'importExcel']);
    Route::post('/divisi/import', [M_DIVISICONTROLLER::class, 'importExcel']);
    Route::post('/subdivisi/import', [M_SUBDIVISICONTROLLER::class, 'importExcel']);
    Route::apiResource('m_subdivisi', M_subdivisiController::class);
    Route::apiResource('m_user', M_userController::class);
    Route::apiResource('m_role', M_roleController::class);

    // master arsip
    Route::apiResource('m_retensi', M_retensiController::class);

    Route::post('m_klasifikasi', [M_KLASIFIKASICONTROLLER::class, 'store']);
    Route::put('m_klasifikasi/{id}', [M_KLASIFIKASICONTROLLER::class, 'update']);
    Route::delete('m_klasifikasi/{id}', [M_KLASIFIKASICONTROLLER::class, 'destroy']);

    Route::post('m_indeks', [M_indeksController::class, 'store']);
    Route::put('m_indeks/{id}', [M_indeksController::class, 'update']);
    Route::delete('m_indeks/{id}', [M_indeksController::class, 'destroy']);

    Route::post('m_kondisi', [M_KONDISICONTROLLER::class, 'store']);
    Route::put('m_kondisi/{id}', [M_KONDISICONTROLLER::class, 'update']);
    Route::delete('m_kondisi/{id}', [M_KONDISICONTROLLER::class, 'destroy']);

    Route::post('m_tingkatpengembangan', [M_TINGKATPENGEMBANGANCONTROLLER::class,'store']);
    Route::put('m_tingkatpengembangan/{id}', [M_TINGKATPENGEMBANGANCONTROLLER::class,'update']);
    Route::delete('m_tingkatpengembangan/{id}', [M_TINGKATPENGEMBANGANCONTROLLER::class,'destroy']);

    Route::post('m_jenisnaskah', [M_JENISNASKAHCONTROLLER::class, 'store']);
    Route::put('m_jenisnaskah/{id}', [M_JENISNASKAHCONTROLLER::class, 'update']);
    Route::delete('m_jenisnaskah/{id}', [M_JENISNASKAHCONTROLLER::class, 'destroy']);
    Route::post('jenisnaskah/import', [M_JENISNASKAHCONTROLLER::class, 'importExcel']);

    // Master Inventaris CRUD routes
    Route::apiResource('m_instal', M_INSTALCONTROLLER::class);
    Route::post('/instal/import', [M_INSTALCONTROLLER::class, 'importExcel']);

    Route::apiResource('m_anggaran', M_ANGGARANCONTROLLER::class);
    Route::post('/anggaran/import', [M_ANGGARANCONTROLLER::class, 'importExcel']);

    Route::apiResource('m_merk', M_MERKCONTROLLER::class);
    Route::post('/merk/import', [M_MERKCONTROLLER::class, 'importExcel']);
});