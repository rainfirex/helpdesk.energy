<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/user-auth/user/{user}/password/{password}', 'ControllerAuth@login');

Route::post('/user-auth/logout', 'ControllerAuth@logout');

Route::post('/create-ticket', 'ControllerTicket@store');

Route::get('/get-tickets/{status}', 'ControllerTicket@index');

Route::get('/get-ticket/{id}', 'ControllerTicket@show');

Route::post('/create-comment', 'ControllerComment@store');

Route::get('/get-comment/{id}', 'ControllerComment@show');

Route::get('/get-comments/{ticket_id}', 'ControllerComment@index');

Route::put('/completed-ticket/{ticket_id}', 'ControllerTicket@update');

Route::get('/check-status-ticket/{number}', 'ControllerTicket@check');

Route::prefix('/handler-tickets')->group(function () {

    Route::get('get-ticket/{id}', 'ControllerHandlerTicket@show');

    Route::get('/get-status-ticket', 'ControllerHandlerStatusTicket@index');

    Route::put('/change-status-ticket', 'ControllerHandlerTicket@changeStatus');

    Route::prefix('comments')->group(function () {

        Route::post('/create', 'ControllerHandlerComment@store');

        Route::get('/get/{id}', 'ControllerHandlerComment@show');
    });

    Route::prefix('page')->group(function () {

        Route::get('count', 'ControllerHandlerTicket@countPage');

        Route::get('{page}/get-all-tickets', 'ControllerHandlerTicket@index');
    });
});
