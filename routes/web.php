<?php

use Illuminate\Support\Facades\Route;
use App\Models\M_terminal;
use App\Http\Controllers\DashboardInventarisController;

Route::get('/', function () {
    return view('login');
});
Route::get('/register', function () {
    return view('register');
});
Route::get('arsip', function () {
    return view('arsip');
})->name('arsip');


    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/terminal', function () {
        return view('terminal');
    })->name('terminal');

    Route::get('/register', function () {
        return view('register');
    })->name('register');

    Route::get('/role', function () { 
        return view('role');
    })->name('role');

    Route::get('/user', function () {
        return view('user');
    })->name('user');

    Route::get('/divisi', function () {
        return view('divisi');
    })->name('divisi');

    Route::get('/subdivisi', function () {
        return view('subdivisi');
    })->name('subdivisi');

    Route::get('/parameter', function () {
        return view('parameter');
    })->name('parameter');

    Route::get('/klasifikasi', function () {
        return view('klasifikasi');
    })->name('klasifikasi');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/indeks', function () {
        return view('indeks');
    })->name('indeks');

    Route::get('/retensi', function () {
        return view('retensi');
    })->name('retensi');

    Route::get('/inventaris', function () {
        return view('inventaris');
    })->name('inventaris');

    Route::get('/portal/terminal/{id}', function ($NAMA_TERMINAL) {
        return view('Tinven', ['nama_terminal' => $NAMA_TERMINAL]);
    })->name('portal.terminal');

    
    Route::get('/portal/terminal/{id}', function ($id) {
        $terminal = M_terminal::findOrFail($id);
        return view('Tinven', [
            'id_terminal' => $terminal->ID_TERMINAL,
            'nama_terminal' => $terminal->NAMA_TERMINAL
        ]);
    })->name('portal.terminal');

    Route::get('/jenisnaskah', function () {
        return view('jenisnaskahdinas');
    })->name('jenisnaskah');

    Route::get('/jenispengembangan', function () {
        return view ('jenispengembangan');
    })->name('jenispengembangan');

    Route::get('/kondisi', function () {
        return view ('kondisi');
    })->name('kondisi');

    Route::get('/coba', function () {
        return view ('cobacoba');
    });

    // Master Inventaris routes
    Route::get('/instal', function () {
        return view('instal');
    })->name('instal');

    Route::get('/anggaran', function () {
        return view('anggaran');
    })->name('anggaran');

    Route::get('/merk', function () {
        return view('merk');
    })->name('merk');

    Route::get('/dashboard-inventaris', [DashboardInventarisController::class, 'index'])->name('dashboard-inventaris');