<?php

Route::get('/', 'Auth\AuthController@getLogin');

Route::controller('/auth', 'Auth\AuthController');

Route::get('/puesta_cero', [
    'as' => 'puesta.cero',
    'uses' => 'ResetController@puestaCero'
]);

Route::get('/unlock', [
    'as' => 'unlock',
    'uses' => 'ResetController@unlock'
]);

Route::group([
    'middleware' => 'auth',
    'prefix' => 'oper',
    'namespace' => 'Operator'
], function () {
    Route::get('/', [
        'as' => 'operator.assistance',
        'uses' => 'AssistanceController@index'
    ]);

    Route::post('/save', [
        'as' => 'operator.assistance.save',
        'uses' => 'AssistanceController@save'
    ]);
});

Route::group([
    'middleware' => 'auth',
    'prefix' => '/admin',
    'namespace' => 'Admin'
], function () {
    Route::get('/', [
        'as' => 'admin.dashboard',
        'uses' => function () {
            return View::make('admin.dashboard');
        }
    ]);

    Route::resource('/tournament', 'TournamentController');
    Route::get('/tournament/enable/{tournament}', [
        'as' => 'admin.tournament.enable',
        'uses' => 'TournamentController@enable'
    ]);
    Route::get('/tournament/disable/{tournament}', [
        'as' => 'admin.tournament.disable',
        'uses' => 'TournamentController@disable'
    ]);

    Route::get('/tournament/{tournament}/categories', [
        'as' => 'admin.tournament.category',
        'uses' => 'CategoryController@getIndex'
    ]);

    Route::controller('/category', 'CategoryController');

    Route::resource('/user', 'UserController');
    Route::get('/user/unlock/{user}', [
        'as' => 'admin.user.unlock',
        'uses' => 'UserController@unlock'
    ]);
});

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

Route::get('/result', [
    'as' => 'tournament.result',
    'uses' => 'ResultsController@index'
]);