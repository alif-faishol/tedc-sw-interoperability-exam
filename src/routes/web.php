<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'user'], function() use ($router) {
    $router->post('/register', 'UserController@register');
    $router->post('/login', 'UserController@login');
});

$router->group(['middleware' => 'auth'], function() use ($router) {
    $router->group(['prefix' => 'property'], function () use ($router) {
        $router->post('/', 'PropertyController@store');
        $router->get('/', 'PropertyController@list');
        $router->get('/{id}', 'PropertyController@detail');
        $router->delete('/{id}', 'PropertyController@delete');
    });

    $router->group(['prefix' => 'unit'], function () use ($router) {
        $router->post('/', 'UnitController@store');
        $router->get('/', 'UnitController@list');
        $router->get('/{id}', 'UnitController@detail');
        $router->delete('/{id}', 'UnitController@delete');
    });

    $router->group(['prefix' => 'unit-group'], function () use ($router) {
        $router->post('/', 'UnitGroupController@store');
        $router->get('/', 'UnitGroupController@list');
        $router->get('/{id}', 'UnitGroupController@detail');
        $router->delete('/{id}', 'UnitGroupController@delete');
    });
});
