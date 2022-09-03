<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return response()->json([
        'app_name' => 'Simpoku',
        'version' => env('APP_VERSION')
    ], 200);
});

$router->group(['prefix' => 'api'], function () use ($router) {
    //version 1
    $router->group(['prefix' => 'v1'], function () use ($router) {
        $router->group(['prefix' => 'auth'], function () use ($router) {
            $router->post('/sign-up', 'AuthController@register');
            $router->post('/sign-in', 'AuthController@login');
        });

        $router->group(['prefix' => 'admin'], function () use ($router){
            $router->group(['prefix' => 'specialist'], function () use ($router){
                $router->get('/', 'Admin\\SpecialistController@index');
                $router->post('/', 'Admin\\SpecialistController@index');
            });
        });
    });
});

