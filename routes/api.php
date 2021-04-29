<?php
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

//Route::get('is-auth', 'ControllerAuth@isAuth')->middleware('auth:api');

Route::get('monitor/number-ticket/{number}', 'user\CTicket@monitor');

Route::prefix('screenshots')->group(function () {
    Route::get('ticket-id/{ticket_id}/get-screenshots', 'CScreenshots@index')->name('screenshots.index');
});

Route::prefix('docs')->group(function () {
    Route::get('ticket-id/{ticket_id}/get-docs', 'CHandlerDocs@index');
});

Route::prefix('auth')->group(function () {
    Route::post('login', 'CAuth@login');
    Route::post('logout', 'CAuth@logout');
});

Route::prefix('user')->group(function() {
    Route::prefix('tickets')->group(function (){
        Route::get('page/{page}/get-tickets', 'user\CTicket@index')->name('user.tickets.index');
        Route::post('create-ticket', 'user\CTicket@store')->name('user.tickets.create');
        Route::get('search/{findText}', 'user\CTicket@search')->name('user.tickets.search');
        Route::get('pages', 'user\CTicket@pages')->name('user.tickets.pages');
        Route::get('id/{id}/get-ticket', 'user\CTicket@show')->name('user.tickets.get-ticket');
        Route::get('id/{id}/status-ticket', 'user\CTicket@status')->name('user.ticket.status-ticket');

        Route::prefix('completed')->group(function(){
            Route::put('ticket-id/{ticket_id}/complete', 'user\CTicketCompleted@complete')->name('user.ticket-complete');
            Route::get('pages', 'user\CTicketCompleted@pages')->name('user.ticket-complete.pages');
            Route::get('page/{page}/get-tickets', 'user\CTicketCompleted@index')->name('user.ticket-complete.index');
        });
    });
    Route::prefix('comments')->group(function (){
        Route::get('ticket-id/{ticket_id}/get-comments', 'user\CComment@index')->name('user.ticket.comments');
        Route::get('comment-id/{id}/get-comment', 'user\CComment@show')->name('user.ticket.comment');
        Route::post('create-comment', 'user\CComment@store')->name('user.ticket.create-comment');
        Route::put('id/{id}/reset-new', 'user\CComment@resetNew')->name('user.ticket.reset-new');
    });
    Route::prefix('categories')->group(function () {
        Route::get('/', 'user\CCategoryTicket@index')->name('user.categories');
    });
});

Route::prefix('handler')->group(function () {
    Route::prefix('handlers')->group(function (){
        Route::get('/', 'handler\CHandlerUser@handlers')->name('handler.handlers');
        Route::put('id/{handlerId}/ticket-id/{ticketId}/assign', 'handler\CHandlerUser@assign')->name('handler.assign');
    });
    Route::prefix('tickets')->group(function (){
        // Заявка
        Route::put('id/{id}/reset-ticket-new', 'handler\CHandlerTicket@resetNew')->name('handler.reset-ticket-new');
        Route::get('id/{id}/get-ticket', 'handler\CHandlerTicket@show')->name('handler.get-ticket');
        Route::get('id/{id}/get-ticket-status', 'handler\CHandlerTicket@status')->name('handler.ticket-status');
        Route::get('get-ticket-statuses', 'handler\CHandlerStatusTicket@index')->name('handler.ticket-statuses');
        Route::put('update-ticket-status', 'handler\CHandlerTicket@changeStatus')->name('handler.ticket-status.update');
        // Список заявок
        Route::get('page/{page}/type/{type}/get-tickets', 'handler\CHandlerTicket@index')->name('handler.get-tickets');
        Route::get('get-type-tickets', 'handler\CHandlerTicket@countTypeTicket')->name('handler.get-type.tickets');
        Route::get('type-ticket/{type}/pages', 'handler\CHandlerTicket@countPage')->name('handler.ticket.pages');
        Route::get('ticket-title/{findText}/find', 'handler\CHandlerTicketFind@index')->name('handler.ticket.find');
    });
    Route::prefix('comments')->group(function () {
        Route::get('ticket-id/{ticket_id}/get-comments', 'handler\CHandlerComment@index')->name('handler.ticket.comments');
        Route::post('create-comment', 'handler\CHandlerComment@store')->name('handler.ticket.create-comment');
        Route::get('comment-id/{id}/get-comment', 'handler\CHandlerComment@show')->name('handler.ticket.comment');
        Route::put('id/{id}/reset-new', 'handler\CHandlerComment@resetNew')->name('handler.ticket.reset-new');
        Route::post('comment-id/{commentId}/create-comment-viewer', 'handler\CHandlerCommentViewer@store');
    });
});