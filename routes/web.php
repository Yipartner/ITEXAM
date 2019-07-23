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

include "problems.php";
Route::get('/', function () {
    return view('welcome');
});

Route::post('/add/user','UserController@addSingleUser');
Route::post('/login','UserController@login');
Route::get('/user','UserController@getUser');
Route::get('/users','UserController@getUsers');
Route::delete('/user','UserController@deleteUsers');
Route::get('/me/papers','PaperController@getMyPapers');


Route::post('/create/blockpaper','PaperController@createWhitePaper');
//Route::post('/paper/problem/add','PaperController@addProblem');
Route::post('/paper/problem/edit','PaperController@updateProblem');
Route::post('/paper/update','PaperController@updatePaperBaseInfo');
Route::post('/paper/finish','PaperController@finishPaper');
Route::get('/paper','PaperController@getPaperInfo');
Route::get('/papers','PaperController@getPaperList');


Route::post('/paper/save','UserPaperController@save');
Route::post('/paper/submit','UserPaperController@submit');
Route::get('/me/paper/status','UserPaperController@getPaperSaveStatus');
Route::get('/me/paper/res','UserPaperController@getUserPaperRes');

Route::get('/paper/user','PaperController@getPaperUser');
Route::post('/paper/user/add','PaperController@addPaperUser');
Route::delete('/paper/user','PaperController@deletePaperUser');