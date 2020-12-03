<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\Bdo\ApplicantController;

use App\Http\Controllers\Application;

use App\Http\Controllers\Dash;
use App\Http\Controllers\Multimedia;
use App\Http\Controllers\Stat;
use App\Http\Controllers\Communication;



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
Route::post('/account', [DataController::class, 'create_account']);

Route::post('/new_applicant', [Application::class, 'new_customer']);

Route::get('/inapp', [Dash::class, 'create_new_Cif_inapp']);


Route::get('/applicants', [Dash::class, 'getApplicants']);

Route::post('/upload_img', [Multimedia::class, 'upload_nic']);

Route::post('/sign_application', [Multimedia::class, 'sign']);

Route::post('/reviewed', [Dash::class, 'reviewed']);


// retreive to dashboard rendering
Route::get('/applicant_details_by_nic', [Dash::class, 'item_view']);
Route::get('/grab_branches', [Dash::class, 'grab_branches']);


Route::get('/comment', [Communication::class, 'comment']);
Route::get('/comments_application', [Communication::class, 'get_comments_of_application']);


Route::get('/message', [Communication::class, 'message']);
Route::get('/message_get_ref', [Communication::class, 'message_get_ref']);
Route::get('/message_get_bdo', [Communication::class, 'message_get_bdo']);


// status
Route::get('/get_progress', [Stat::class, 'get_progress_completed']);
Route::get('/get_progress_rejected', [Stat::class, 'get_progress_rejected']);
