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
Route::get('/rules_down', "IndexController@rulesDown");

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
Route::group(['middleware' => 'auth'], function(){
    Route::get('/assessment/get_all_assessments', 'AssessmentController@getAllAssessments');
    Route::resource('assessment', 'AssessmentController', ['only' => ['index', 'show', 'store', 'update']]);
//    Route::get('/assessment', 'AssessmentController@index');
//    Route::get('/assessment/{id}', 'AssessmentController@show');
////    Route::get('/create_assessment/{id}', 'AssessmentController@createAssessment'); //已经放弃使用
//    Route::post('/assessment', 'AssessmentController@store');
//    Route::put('/assessment/{id}', 'AssessmentController@update');

    //编辑评论人
    Route::get('/raters/all_raters/{id}', 'RaterController@getRaters');
    Route::get('/selected_staff_details/{id}', 'RaterController@getSelectedStaffDetails');
    Route::resource('raters', 'RaterController', ['only' => ['index', 'update']]);
//    Route::get('/raters', 'RaterController@index');
//    Route::put('/raters/{id}', 'RaterController@update');
});



//评分
Route::group(['middleware' => 'auth'], function(){
    Route::get('/score/home', 'GiveScoreController@home');
    Route::resource('score', 'GiveScoreController', ['only' => ['index', 'show', 'update']]);
//    Route::get('/score/', 'GiveScoreController@index');
//    Route::get('/score/{id}', 'GiveScoreController@show');
////Route::post('/score/save_staff_scores/{id}', 'GiveScoreController@saveStaffScores');
//    Route::put('/score/{id}', 'GiveScoreController@update');
});


//评分查看系统
Route::group(['middleware' => 'auth'], function(){
    Route::get('/show_score/get_last_assessment_date', 'ShowScoreController@getLastAssessmentDate');
    Route::get('/show_score/home', 'ShowScoreController@home');
    Route::resource('show_score', 'ShowScoreController', ['only' => ['index']]);
//    Route::get('/show_score', 'ShowScoreController@index');
});






//管理员进入员工管理页面
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function() {
    Route::get('/', 'BackgroundManagement\HomeController@index');

//员工管理
    Route::resource('staff_management', 'BackgroundManagement\StaffManagementController', ['only' => ['index', 'update']]);
//    Route::get('/staff_management', 'BackgroundManagement\StaffManagementController@index');//获取全部员工信息 包括离职的
//    Route::put('/staff_management/{id}', 'BackgroundManagement\StaffManagementController@update');
//Route::post('/admin/change_is_admin/{id}', 'BackgroundManagement\StaffManagementController@changeIsAdmin');//更改员工的后台权限
//Route::post('/admin/save_staff_department/{id}', 'BackgroundManagement\StaffManagementController@saveStaffDepartment');//后台更改员工的分组
//Route::post('/admin/change_staff_jurisdiction/{id}', 'BackgroundManagement\StaffManagementController@changeStaffJurisdiction');//改变员工的权限
//Route::post('/admin/change_staff_status/{id}', 'BackgroundManagement\StaffManagementController@changeStaffStatus');//改变员工的状态
//Route::post('/admin/change_staff_is_senior_manager/{id}', 'BackgroundManagement\StaffManagementController@changeStaffIsSeniorManager');//改变员工是否是高级管理员

//默认参评人管理
//Route::get('/admin/get_plan_rater', 'BackgroundManagement\RaterManagementController@getPlanRater');
//Route::get('/admin/get_development_rater', 'BackgroundManagement\RaterManagementController@getDevelopmentRater');
    Route::get('/raters_management/all_staffs_no_leave_no_selected', 'BackgroundManagement\RaterManagementController@getAllStaffsNoLeave');
    Route::put('/raters_management/update_new_raters', 'BackgroundManagement\RaterManagementController@addNewRaters');
    Route::resource('raters_management', 'BackgroundManagement\RaterManagementController', ['only' => ['index', 'update']]);
//    Route::get('/raters_management', 'BackgroundManagement\RaterManagementController@index');
//    Route::put('/raters_management/{id}', 'BackgroundManagement\RaterManagementController@update');
//Route::post('/admin/save_rater_percentage/{id}', 'BackgroundManagement\RaterManagementController@saveRaterPercentage');
//Route::post('/admin/delete_default_rater/{id}', 'BackgroundManagement\RaterManagementController@deleteDefaultRater');
//Route::post('/admin/get_all_staffs_no_leave_no_selected', 'BackgroundManagement\RaterManagementController@getAllStaffsNoLeave');

//每月的考核管理
    Route::resource('assessment_management', 'BackgroundManagement\TheAssessmentsManagementController', ['only' => ['index', 'update', 'destroy']]);
//    Route::get('/assessment_management', 'BackgroundManagement\TheAssessmentsManagementController@index');
//    Route::put('/assessment_management/{id}', 'BackgroundManagement\TheAssessmentsManagementController@update');
//    Route::delete('/assessment_management/{id}', 'BackgroundManagement\TheAssessmentsManagementController@destroy');
});




//合理化建议
Route::get('/advices/all_auth_advices', 'AdvicesController@getAllAuthAdvices');
Route::get('/advices/rater/all_suggester_all_advices', 'AdvicesController@getAllSuggesterAdvices');
Route::get('/rater_edit/advice_detail/{id}', 'AdvicesController@raterEditGetAdviceDetail');
Route::post('/rater_edit/rater_judge_advice/{id}', "AdvicesController@raterJudgeAdvice");
Route::resource('advices', 'AdvicesController');
