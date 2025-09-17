<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});
Route::get('/register', function () {
    return view('register');
});


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

