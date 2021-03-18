<?php

use Illuminate\Support\Facades\Route;

Route::get('{any}', function(){
    return view('landing');
})->where('any', '^(?!download)(?!export).*$');

//Route::get('download', 'RecordsController@download');

/*
Route::get('/', 'SinglePageController@index');
Route::get('/records', 'SinglePageController@index');
Route::get('/myRecords', 'SinglePageController@index');
Route::get('/departments', 'SinglePageController@index');
Route::get('/projects', 'SinglePageController@index');
Route::get('/signIn', 'SinglePageController@index');
Route::get('/register', 'SinglePageController@index');
Route::get('/users', 'SinglePageController@index');
*/

Route::get('download', 'RecordsController@download');

Route::get('export1', function (){

});
