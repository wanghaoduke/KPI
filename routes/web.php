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
Route::group(['prefix' => 'assessment', 'middleware' => 'auth'], function(){
    Route::get('/get_all_assessments', 'AssessmentController@getAllAssessments');
    Route::get('/', 'AssessmentController@index');
    Route::get('/get_assessment_detail/{id}', 'AssessmentController@show');
//    Route::get('/create_assessment/{id}', 'AssessmentController@createAssessment'); //已经放弃使用
    Route::post('/', 'AssessmentController@store');
    Route::put('/{id}', 'AssessmentController@update');

    //编辑评论人
    Route::get('/raters/all_raters/{id}', 'RaterController@getRaters');
    Route::get('/get_selected_staff_details/{id}', 'RaterController@getSelectedStaffDetails');
    Route::get('/raters', 'RaterController@index');
    Route::put('/raters/{id}', 'RaterController@editRaters');
});



//评分
Route::group(['prefix' => 'score', 'middleware' => 'auth'], function(){
    Route::get('/home', 'GiveScoreController@home');
    Route::get('/', 'GiveScoreController@index');
    Route::get('/{id}', 'GiveScoreController@show');
//Route::post('/score/save_staff_scores/{id}', 'GiveScoreController@saveStaffScores');
    Route::put('/{id}', 'GiveScoreController@update');
});


//评分查看系统
Route::group(['prefix' => 'show_score', 'middleware' => 'auth'], function(){
    Route::get('/get_last_assessment_date', 'ShowScoreController@getLastAssessmentDate');
    Route::get('/home', 'ShowScoreController@home');
    Route::get('/', 'ShowScoreController@index');
});






//管理员进入员工管理页面
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function() {
    Route::get('/', 'BackgroundManagement\HomeController@index');

//员工管理
    Route::get('/staff_management', 'BackgroundManagement\StaffManagementController@index');//获取全部员工信息 包括离职的
    Route::put('/staff_management/{id}', 'BackgroundManagement\StaffManagementController@update');
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
    Route::get('/raters_management', 'BackgroundManagement\RaterManagementController@index');
    Route::put('/raters_management/{id}', 'BackgroundManagement\RaterManagementController@update');
//Route::post('/admin/save_rater_percentage/{id}', 'BackgroundManagement\RaterManagementController@saveRaterPercentage');
//Route::post('/admin/delete_default_rater/{id}', 'BackgroundManagement\RaterManagementController@deleteDefaultRater');
//Route::post('/admin/get_all_staffs_no_leave_no_selected', 'BackgroundManagement\RaterManagementController@getAllStaffsNoLeave');

//每月的考核管理
    Route::get('/assessment_management/index', 'BackgroundManagement\TheAssessmentsManagementController@index');
    Route::put('/assessment_management/{id}', 'BackgroundManagement\TheAssessmentsManagementController@update');
    Route::delete('/assessment_management/{id}', 'BackgroundManagement\TheAssessmentsManagementController@destroy');
});




//合理化建议
Route::get('/advices/get_all_auth_advices', 'AdvicesController@getAllAuthAdvices');
Route::get('/advices/rater/get_all_suggester_all_advices', 'AdvicesController@getAllSuggesterAdvices');
Route::get('/rater_edit/get_advice_detail/{id}', 'AdvicesController@raterEditGetAdviceDetail');
Route::post('/rater_edit/rater_judge_advice/{id}', "AdvicesController@raterJudgeAdvice");
Route::resource('advices', 'AdvicesController');
