<?php

Route::get('/', 'Auth\AuthController@getLogin');

Route::controller('/auth', 'Auth\AuthController');

Route::group([
    'middleware' => 'auth',
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
    Route::get('/save_selection', [
        'as' => 'tournament.save.selection',
        'uses' => 'TournamentController@saveSelection'
    ]);
    Route::get('/save_classify', [
        'as' => 'tournament.save.classify',
        'uses' => 'TournamentController@saveClassify'
    ]);
});