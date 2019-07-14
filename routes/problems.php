<?php
/**
 * Created by PhpStorm.
 * User: imyhui
 * Date: 2019/7/14
 * Time: 下午6:34
 */


    Route::post('problems', 'ProblemController@createProblem');
    Route::put('problems/{$probId}', 'ProblemController@updateProblem');
    Route::get('problems/id/{id}', 'ProblemController@getProblemById');
    Route::get('problems/search', 'ProblemController@searchProject');
    Route::get('problems/all', 'ProblemController@getProblemList');
    Route::get('problems/by/{subject}', 'ProblemController@getProblemsBySubject');
    Route::delete('problems/{probId}', 'ProblemController@deleteProblem');