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
    return $router->app->version();
});
$router->get('/client[/{pageNo}/{pageSize}]', 'ClientController@index');
$router->get('/client/{id}', 'ClientController@show');
$router->post('/client/save', 'ClientController@create');
$router->put('/client/update/{id}', 'ClientController@update');
$router->delete('/client/delete/{id}', 'ClientController@destroy');

#region Users
$router->group(['middleware' => 'auth','prefix' => 'api'], function ($router) 
{
    $router->get('me', 'AuthController@me');
});
$router->group(['prefix' => 'api'], function () use ($router) 
{
   $router->post('register', 'AuthController@register');
   $router->post('login', 'AuthController@login');
});
#endregion

$router->post('/login', 'AuthController@login');
$router->get('demo[/{name}]', function ($name = null) {
    if(is_null($name))
        return "Default Name";
    return $name;
});
