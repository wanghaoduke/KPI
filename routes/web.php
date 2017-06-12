<?php

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
    $title1 = '首页';
    $title2 = null;
    $titleLink1 = '/';
    $titleLink2 = null;
    return view('kpiIndex', compact('title1', 'title2', 'titleLink1', 'titleLink2'));
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/send_message', 'Auth\RegisterController@sendMessage');
Route::get('/reset_get_code', 'ResetPasswordController@show');
Route::post('/reset_send_code', 'ResetPasswordController@sendCode');
Route::post('/reset_check_code', 'ResetPasswordController@resetCheckCode');
Route::get('/reset_password', 'ResetPasswordController@resetPassword');
Route::post('/new_password_store', 'ResetPasswordController@newPasswordStore');
Route::get('/assessment_manage', 'AssessmentController@show');
Route::get('/create_assessment/{id}', 'AssessmentController@createAssessment');
Route::put('/create_month_assessment', 'AssessmentController@createMonthAssessment');
Route::get('/get_assessment_detail/{id}', 'AssessmentController@getAssessmentDetail');
Route::post('/get_raters/{id}', 'AssessmentController@getRaters');
Route::get('/get_all_staffs', 'AssessmentController@getAllStaffs');
Route::post('/get_selected_staff_details/{id}', 'AssessmentController@getSelectedStaffDetails');
Route::post('/edit_raters/{id}', 'AssessmentController@editRaters');
