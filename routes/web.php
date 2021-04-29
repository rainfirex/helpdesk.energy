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
//Route::get('test', 'handler\CHandlerCommentViewer@index');

Route::get('monitoring/{number?}', 'CMonitoring')->name('monitoring');

Route::view('{path}', 'index')->where('path', '([A-z\d-\/_.]+)?');