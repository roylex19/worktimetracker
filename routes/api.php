<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function (){
    Route::post('signIn', 'SignInController');
    Route::post('signOut', 'SignOutController');
    Route::post('register', 'RegisterController');
    Route::get('me', 'MeController');
});

Route::get('departments/list', 'Api\DepartmentsController@showList');

Route::group([
    'middleware' => ['auth:api'],
    'namespace' => 'Api'
], function () {
    Route::get('records', 'RecordsController@show');
    Route::get('records/{id}', 'RecordsController@showDepartment');
    Route::get('records/user/{id}', 'RecordsController@showMy');
    Route::post('records', 'RecordsController@create');
    Route::patch('records/{id}', 'RecordsController@update');
    Route::delete('records/{id}', 'RecordsController@delete');

    Route::get('users/list', 'UsersController@showList');
    Route::get('users', 'UsersController@show');
    Route::post('users', 'UsersController@create');
    Route::patch('users/{id}', 'UsersController@update');
    Route::patch('users/{id}/password', 'UsersController@updatePassword');
    Route::patch('users/{id}/admin', 'UsersController@updateAdminRule');
    Route::patch('users/{id}/deactivate', 'UsersController@deactivate');
    Route::delete('users/{id}', 'UsersController@delete');

    Route::get('departments', 'DepartmentsController@show');
    Route::post('departments', 'DepartmentsController@create');
    Route::patch('departments/{id}', 'DepartmentsController@update');
    Route::delete('departments/{id}', 'DepartmentsController@delete');

    Route::get('projects/list', 'ProjectsController@showList');
    Route::get('projects/listRecent', 'ProjectsController@showRecentList');
    Route::get('projects/{query}', 'ProjectsController@showSearch');
    Route::get('projects', 'ProjectsController@show');
    Route::post('projects', 'ProjectsController@create');
    Route::patch('projects/{id}', 'ProjectsController@update');
    Route::delete('projects/{id}', 'ProjectsController@delete');

    Route::get('departmentsViews/{id}', 'DepartmentsViewsController@show');
    Route::post('departmentsViews', 'DepartmentsViewsController@create');
    Route::patch('departmentsViews/{id}', 'DepartmentsViewsController@update');
    Route::delete('departmentsViews/{id}', 'DepartmentsViewsController@delete');

    Route::get('reportTracker', 'ReportTrackerController@show');

    Route::get('export', 'RecordsController@export');
    Route::get('exportGeneral', 'RecordsController@exportGeneral');




});
Route::get('users/{id}/hours', 'Api\UserPageController@show');

//<auth
Route::get('/oauth/getToken', 'AuthController@getToken')->name('getToken');
Route::get('/oauth/callback', 'AuthController@handleCallback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
//auth>
