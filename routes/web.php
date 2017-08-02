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
//Route::post('/score/save_staff_scores/{id}', 'GiveScoreController@saveStaffScores');
Route::post('/score/save_the_staff_score/{id}', 'GiveScoreController@saveTheStaffScore');


//评分查看系统
Route::get('/show_score/index', 'ShowScoreController@index');
Route::get('/get_last_assessment_date', 'ShowScoreController@getLastAssessmentDate');
Route::post('/get_period_all_scores', 'ShowScoreController@getPeriodAllScores');



//管理员进入员工管理页面
Route::get('/admin', 'BackgroundManagement\HomeController@index');

//员工管理
Route::get('/admin/staff_management', 'BackgroundManagement\StaffManagementController@index');//获取全部员工信息 包括离职的
Route::put('/admin/staff_management/{id}', 'BackgroundManagement\StaffManagementController@update');
//Route::post('/admin/change_is_admin/{id}', 'BackgroundManagement\StaffManagementController@changeIsAdmin');//更改员工的后台权限
//Route::post('/admin/save_staff_department/{id}', 'BackgroundManagement\StaffManagementController@saveStaffDepartment');//后台更改员工的分组
//Route::post('/admin/change_staff_jurisdiction/{id}', 'BackgroundManagement\StaffManagementController@changeStaffJurisdiction');//改变员工的权限
//Route::post('/admin/change_staff_status/{id}', 'BackgroundManagement\StaffManagementController@changeStaffStatus');//改变员工的状态
//Route::post('/admin/change_staff_is_senior_manager/{id}', 'BackgroundManagement\StaffManagementController@changeStaffIsSeniorManager');//改变员工是否是高级管理员

//默认参评人管理
//Route::get('/admin/get_plan_rater', 'BackgroundManagement\RaterManagementController@getPlanRater');
//Route::get('/admin/get_development_rater', 'BackgroundManagement\RaterManagementController@getDevelopmentRater');
Route::get('/admin/raters_management/all_staffs_no_leave_no_selected', 'BackgroundManagement\RaterManagementController@getAllStaffsNoLeave');
Route::put('/admin/raters_management/update_new_raters', 'BackgroundManagement\RaterManagementController@addNewRaters');
Route::get('/admin/raters_management', 'BackgroundManagement\RaterManagementController@index');
Route::put('/admin/raters_management/{id}', 'BackgroundManagement\RaterManagementController@update');
//Route::post('/admin/save_rater_percentage/{id}', 'BackgroundManagement\RaterManagementController@saveRaterPercentage');
//Route::post('/admin/delete_default_rater/{id}', 'BackgroundManagement\RaterManagementController@deleteDefaultRater');
//Route::post('/admin/get_all_staffs_no_leave_no_selected', 'BackgroundManagement\RaterManagementController@getAllStaffsNoLeave');

//每月的考核管理
Route::get('/admin/assessment_management/index', 'BackgroundManagement\TheAssessmentsManagementController@index');
Route::put('/admin/assessment_management/{id}', 'BackgroundManagement\TheAssessmentsManagementController@update');
Route::delete('/admin/assessment_management/{id}', 'BackgroundManagement\TheAssessmentsManagementController@destroy');



//合理化建议
Route::get('/advices/get_all_auth_advices', 'AdvicesController@getAllAuthAdvices');
Route::get('/advices/rater/get_all_suggester_all_advices', 'AdvicesController@getAllSuggesterAdvices');
Route::get('/rater_edit/get_advice_detail/{id}', 'AdvicesController@raterEditGetAdviceDetail');
Route::post('/rater_edit/rater_judge_advice/{id}', "AdvicesController@raterJudgeAdvice");
Route::resource('advices', 'AdvicesController');
