<?php

Route::get('/', 'Auth\AuthController@getLogin');

Route::controller('/auth', 'Auth\AuthController');

Route::group([
    'middleware' => ['auth', 'stage'],
    'prefix' => '/tournament'
], function () {
    Route::get('/selection', [
        'as' => 'tournament.selection',
        'uses' => 'TournamentController@selection'
    ]);
    Route::get('/classify_1', [
        'as' => 'tournament.classify_1',
        'uses' => 'TournamentController@classifyFirst'
    ]);
    Route::get('/classify_2', [
        'as' => 'tournament.classify_2',
        'uses' => 'TournamentController@classifySecond'
    ]);
    Route::get('/result', [
        'as' => 'tournament.result',
        'uses' => 'TournamentController@result'
    ]);

    Route::get('/save_selection', [
        'as' => 'tournament.save.selection',
        'uses' => 'TournamentController@saveSelection'
    ]);
    Route::get('/save_classify_1', [
        'as' => 'tournament.save.classify_1',
        'uses' => 'TournamentController@saveClassify1'
    ]);
    Route::get('/save_classify_2', [
        'as' => 'tournament.save.classify_2',
        'uses' => 'TournamentController@saveClassify2'
    ]);
});