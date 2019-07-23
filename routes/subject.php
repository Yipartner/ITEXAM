<?php
/**
 * Created by PhpStorm.
 * User: imyhui
 * Date: 2019/7/23
 * Time: 上午1:22
 */


Route::post('subjects', 'SubjectController@createSubject');
Route::put('subjects/{subId}', 'SubjectController@updateSubject');
Route::get('subjects/id/{id}', 'SubjectController@getSubjectById');
Route::get('subjects/all', 'SubjectController@getSubjectList');
Route::delete('subjects/{subId}', 'SubjectController@deleteSubject');