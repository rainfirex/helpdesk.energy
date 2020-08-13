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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('screenshots')->group(function () {

    Route::get('/id/{ticket_id}/get', 'ControllerScreenshots@index');

});

Route::prefix('/auth')->group(function () {

    Route::post('/login', 'ControllerAuth@login');

    Route::post('/logout', 'ControllerAuth@logout');

});

Route::prefix('/user')->group(function() {

    Route::prefix('tickets')->group(function (){

        Route::get('/find/{find}', 'user\ControllerTicketFind@index');

        Route::post('/create', 'user\ControllerTicket@store');

        Route::get('/pages', 'user\ControllerTicket@countPage');

        Route::get('/page/{page}/get', 'user\ControllerTicket@index');

        Route::get('/id/{id}/get', 'user\ControllerTicket@show');

        Route::get('/check-status/number/{number}', 'user\ControllerTicket@check');

        Route::get('/id/{id}/state', 'user\ControllerTicket@state');

        Route::prefix('completed')->group(function(){

            Route::put('/id/{ticket_id}/set', 'user\ControllerTicket@update');

            Route::get('/page/{page}/get', 'user\ControllerTicketCompleted@index');

            Route::get('/pages', 'user\ControllerTicketCompleted@countPage');

        });

    });

    Route::prefix('comments')->group(function (){

        Route::post('/create', 'user\ControllerComment@store');

        Route::get('/{id}/get', 'user\ControllerComment@show');

        Route::get('/ticket/{ticket_id}/get', 'user\ControllerComment@index');

    });

});

Route::prefix('/handler')->group(function () {

    Route::prefix('tickets')->group(function (){

        Route::get('/count-type', 'handler\ControllerHandlerTicket@countTypeTicket');

        Route::put('/id/{id}/reset-new', 'handler\ControllerHandlerTicket@resetNew');

        Route::get('/id/{id}/get', 'handler\ControllerHandlerTicket@show');

        Route::get('/page/{page}/type/{type}/get', 'handler\ControllerHandlerTicket@index');

        Route::get('/type/{type}/pages', 'handler\ControllerHandlerTicket@countPage');

        Route::get('/find/{findText}', 'handler\ControllerHandlerTicketFind@index');

        Route::get('/id/{id}/state', 'handler\ControllerHandlerTicket@state');

        Route::put('/change-status', 'handler\ControllerHandlerTicket@changeStatus');

        Route::prefix('status')->group(function (){

            Route::get('/gets', 'handler\ControllerHandlerStatusTicket@index');

        });
    });

    Route::prefix('comments')->group(function () {

        Route::post('/create', 'handler\ControllerHandlerComment@store');

        Route::get('/{id}/get', 'handler\ControllerHandlerComment@show');

        Route::get('/ticket/{ticket_id}/get', 'handler\ControllerHandlerComment@index');
    });

});
