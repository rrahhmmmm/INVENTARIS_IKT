<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/home', function () {
    return view('home');
});
Route::get('/terminal', function () {
    return view('terminal');
});


Route::get('/register', function () {
    return view('register');
});

Route::get('/role', function () { 
    return view('role');
});

Route::get('/user', function () {
    return view('user');
});

Route::get('/divisi', function () {
    return view('divisi');
});

Route::get('/subdivisi', function () {
    return view('subdivisi');
});