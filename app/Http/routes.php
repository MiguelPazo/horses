<?php

Route::filter('nocache', function ($route, $request, $response) {
    $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');

    return $response;
});

Route::get('/', [
    'after' => 'nocache',
    'uses' => 'Auth\AuthController@getLogin'
]);

Route::controller('/auth', 'Auth\AuthController');

Route::get('/puesta_cero', [
    'as' => 'puesta.cero',
    'uses' => 'ResetController@puestaCero'
]);

Route::group([
    'middleware' => ['auth', 'role'],
    'prefix' => 'commissar',
    'namespace' => 'Commissar',
    'roles' => 'commissar'
], function () {
    Route::get('/assistance/{id}', [
        'as' => 'commissar.assistance',
        'uses' => 'AssistanceController@index'
    ]);

    Route::post('/assistance/{id}/save', [
        'as' => 'commissar.assistance.save',
        'uses' => 'AssistanceController@save'
    ]);

    Route::controller('/', 'CategoryController');
});

Route::group([
    'middleware' => ['auth', 'role'],
    'prefix' => 'oper',
    'namespace' => 'Operator',
    'roles' => 'operator'
], function () {
    Route::get('/agent/list-all', [
        'roles' => ['operator', 'commissar'],
        'as' => 'oper.agent.listall',
        'uses' => 'AgentController@listall'
    ]);

    Route::group([
        'roles' => ['operator', 'commissar']
    ], function () {
        Route::get('/animal/list-parents', [
            'as' => 'oper.animal.listParents',
            'uses' => 'AnimalController@listParents'
        ]);

        Route::get('/animal/info-animal/{id}', [
            'as' => 'oper.animal.infoAnimal',
            'uses' => 'AnimalController@infoAnimal'
        ]);
    });


    Route::resource('/animal', 'AnimalController');
    Route::resource('/agent', 'AgentController');

    Route::post('/animal/{id?}', [
        'roles' => ['operator', 'commissar'],
        'as' => 'oper.animal.store',
        'uses' => 'AnimalController@store'
    ]);
});

Route::group([
    'middleware' => ['auth', 'role'],
    'prefix' => '/admin',
    'namespace' => 'Admin',
    'roles' => 'admin'
], function () {
    Route::get('/', [
        'as' => 'admin.dashboard',
        'uses' => function () {
            return View::make('admin.dashboard');
        }
    ]);

    Route::resource('/tournament', 'TournamentController');

    Route::get('/tournament/{tournament}', [
        'as' => 'admin.tournament.destroy',
        'uses' => 'TournamentController@destroy'
    ]);
    Route::get('/tournaments', [
        'as' => 'admin.tournament.index',
        'uses' => 'TournamentController@index'
    ]);
    Route::get('/tournament/enable/{tournament}', [
        'as' => 'admin.tournament.enable',
        'uses' => 'TournamentController@enable'
    ]);
    Route::get('/tournament/disable/{tournament}', [
        'as' => 'admin.tournament.disable',
        'uses' => 'TournamentController@disable'
    ]);

    Route::get('/tournament/{tournament}/categories/', [
        'as' => 'admin.tournament.category',
        'uses' => 'CategoryController@getIndex'
    ]);

    Route::controller('/category', 'CategoryController');

    Route::resource('/user', 'UserController');
    Route::get('/user/unlock/{user}', [
        'as' => 'admin.user.unlock',
        'uses' => 'UserController@unlock'
    ]);
    Route::get('/user/destroy/{user}', [
        'as' => 'admin.user.destroy',
        'uses' => 'UserController@destroy'
    ]);
});

Route::group([
    'middleware' => ['auth', 'role', 'stage'],
    'prefix' => '/tournament',
    'roles' => 'jury',
    'after' => 'nocache'
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

Route::group([
    'prefix' => '/results'
], function () {
    Route::get('/tournament/{tournament}', [
        'as' => 'tournament.results',
        'uses' => 'ResultsController@index'
    ]);

    Route::get('/tournament/{tournament}/category/{category}', [
        'as' => 'tournament.results.category',
        'uses' => 'ResultsController@category'
    ]);
});
