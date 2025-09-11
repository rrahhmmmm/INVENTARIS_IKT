<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});


Route::get('/register', function () {
    return view('register');
});