<?php
/**
 * Created by PhpStorm.
 * User: imyhui
 * Date: 2019/7/14
 * Time: 下午6:34
 */


Route::prefix('problems')->group(function () {

    Route::post('', 'ProblemController@createProblem');
    Route::put('{$probId}', 'ProblemController@updateProblem');
    Route::get('id/{id}', 'ProblemController@getProblemById');
    Route::get('search', 'ProblemController@searchProject');
    Route::get('all', 'ProblemController@getProblemList');
    Route::get('by/{subject}', 'ProblemController@getProblemsBySubject');
    Route::delete('{probId}', 'ProblemController@deleteProblem');
});