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

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('/register', 'UserController@register');
    $router->post('/login', 'UserController@login');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'property'], function () use ($router) {
        $router->post('/', 'PropertyController@store');
        $router->get('/', 'PropertyController@list');
        $router->get('/{id}', 'PropertyController@detail');
        $router->delete('/{id}', 'PropertyController@delete');
    });

    $router->group(['prefix' => 'unit'], function () use ($router) {
        $router->post('/', 'UnitController@store');
        $router->get('/', 'UnitController@list');
        $router->get('/for-rent', 'UnitController@forRentList');
        $router->get('/{id}', 'UnitController@detail');
        $router->delete('/{id}', 'UnitController@delete');
    });

    $router->group(['prefix' => 'unit-group'], function () use ($router) {
        $router->post('/', 'UnitGroupController@store');
        $router->get('/', 'UnitGroupController@list');
        $router->get('/{id}', 'UnitGroupController@detail');
        $router->delete('/{id}', 'UnitGroupController@delete');
    });

    $router->group(['prefix' => 'event'], function () use ($router) {
        $router->post('/', 'EventController@store');
        $router->get('/', 'EventController@list');
        $router->put('/{id}', 'EventController@update');
        $router->get('/{id}', 'EventController@detail');
        $router->delete('/{id}', 'EventController@delete');
        $router->post('/{event_id}/buy-ticket', 'EventController@buyTicket');
    });

    $router->group(['prefix' => 'ticket'], function () use ($router) {
        $router->get('/', 'ReservedEventTicketController@list');
        $router->get('/{id}', 'ReservedEventTicketController@detail');
        $router->delete('/{id}', 'ReservedEventTicketController@delete');
    });

    $router->group(['prefix' => 'rent'], function () use ($router) {
        $router->post('/', 'ReservedUnitController@store');
        $router->get('/', 'ReservedUnitController@list');
        $router->get('/{id}', 'ReservedUnitController@detail');
        $router->delete('/{id}', 'ReservedUnitController@delete');
    });
});
