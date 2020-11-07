<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\Bdo\ApplicantController;

use App\Http\Controllers\Application;




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




Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'authenticate']);    // 'UserController@authenticate'
Route::get('/open', [DataController::class, 'open']);   //   'DataController@open'

Route::group(['/middleware' => ['jwt.verify']], function () {
    /// '/user', 'UserController@getAuthenticatedUser'
    //  Route::get('/closed',[DataController::class,'closed']);    // '/closed', 'DataController@closed'
});



Route::get('/closed', [DataController::class, 'closed'])->middleware('jwt.verify');
Route::get('/user',  [UserController::class, 'getAuthenticatedUser'])->middleware('jwt.verify');
Route::post('/applicantInitialSubmit',  [ApplicantController::class, 'ApplicantInitialSubmit'])->middleware('jwt.verify');


Route::get('/applicant_nic_check', [DataController::class, 'check_applicant_with_current_banking_data'])->middleware('jwt.verify');



Route::post('/status', [Application::class, 'applicant_status']);
Route::post('/gointo', [Application::class, 'goin_to_open']);
Route::post('/individualaccounttypes', [Application::class, 'individual_account_types']);
Route::get('/cif', [DataController::class, 'create_new_Cif']);
