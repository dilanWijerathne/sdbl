<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dash;

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
    return view('welcome');
});

//Route::get('/dash', 'Dash@dash_view');
Route::get('/dash', [Dash::class, 'dash_view']);   //   'DataController@open'

Route::get('/applicants', [Dash::class, 'getApplicants']);

Route::get('/details', [Dash::class, 'getDetailsByNic']);







Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
