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

Route::get('/', "IndexController@index");

Auth::routes();

//注册登录重置密码
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/send_message', 'Auth\RegisterController@sendMessage');
Route::get('/reset_get_code', 'ResetPasswordController@show');
Route::post('/reset_send_code', 'ResetPasswordController@sendCode');
Route::post('/reset_check_code', 'ResetPasswordController@resetCheckCode');
Route::get('/reset_password', 'ResetPasswordController@resetPassword');
Route::post('/new_password_store', 'ResetPasswordController@newPasswordStore');

//创建编辑assessment
Route::get('/assessment_manage', 'AssessmentController@show');
Route::get('/create_assessment/{id}', 'AssessmentController@createAssessment');
Route::put('/create_month_assessment', 'AssessmentController@createMonthAssessment');
Route::get('/get_assessment_detail/{id}', 'AssessmentController@getAssessmentDetail');
Route::post('/get_raters/{id}', 'AssessmentController@getRaters');
Route::get('/get_all_staffs', 'AssessmentController@getAllStaffs');
Route::post('/get_selected_staff_details/{id}', 'AssessmentController@getSelectedStaffDetails');
Route::post('/edit_raters/{id}', 'AssessmentController@editRaters');
Route::get('/get_all_assessments', 'AssessmentController@getAllAssessments');
Route::post('/change_assessment_status/{id}', 'AssessmentController@changeAssessmentStatus');

//评分
Route::get('/score/master', 'GiveScoreController@show');
Route::get('/score/get_your_assessment', 'GiveScoreController@getYourAssessment');
Route::get('/score/get_staff_scores/{id}', 'GiveScoreController@getStaffScores');
Route::post('/score/save_staff_scores/{id}', 'GiveScoreController@saveStaffScores');

//评分查看系统
Route::get('/show_score/index', 'ShowScoreController@index');
Route::get('/get_last_assessment_date', 'ShowScoreController@getLastAssessmentDate');
Route::post('/get_period_all_scores', 'ShowScoreController@getPeriodAllScores');