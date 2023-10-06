<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('Courses');
});

Route::get('/course', function () {
    return view('Course');
});

Route::get('/courses', function () {
    return view('Courses');
});

Route::get('/groups', function () {
    return view('Groups');
});

Route::get('/login', function () {
    return view('Login');
});

Route::get('/students', function () {
    return view('Students');
});
