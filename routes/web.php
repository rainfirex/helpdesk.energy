<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('view');
});

Route::view('/monitor-ticket', 'view');

Route::view('/tickets', 'view')->name('tickets');

Route::view('/create-ticket', 'view');

Route::view('/handler-tickets', 'view')->name('handler-tickets');

Route::get('/mailto', 'ControllerMail@send');

Route::get('/handler-tickets/detale-ticket/{id}', function () {
    return redirect(route('handler-tickets'));
});

Route::get('/detale-ticket/{id}', function (){
    return redirect(route('tickets'));
});
