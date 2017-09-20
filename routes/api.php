<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace'  => 'App\Http\Controllers\Api\V1',
    'middleware' => [

    ],
], function ($api) {

    $api->post('auth/login', 'AuthController@login');
    $api->post('auth/register', 'AuthController@register');

    $api->group(['middleware' => 'api.auth'], function ($api) {

        $api->get('auth', 'AuthController@detail');
        $api->delete('auth', 'AuthController@logout');
        $api->patch('auth', 'AuthController@update');
        $api->post('auth/update_password', 'AuthController@update_password');
    });

});