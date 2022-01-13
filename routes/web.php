<?php


use Illuminate\Support\Facades\Redis;

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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });


$router->get('/client[/{pageNo}/{pageSize}]', 'ClientController@index');
$router->get('/client/search', 'ClientController@search');
$router->get('/client/{id}', 'ClientController@show');
$router->post('/client/save', 'ClientController@create');
$router->put('/client/update/{id}', 'ClientController@update');
$router->delete('/client/delete/{id}', 'ClientController@destroy');
$router->get('/client/sampleredis/{id}/{total}', 'ClientController@showredis');

#region Media
$router->post('/media[/{pageNo}/{pageSize}]', 'MediaController@index');
$router->post('/media/search', 'MediaController@search');
$router->get('/media/{id}', 'MediaController@show');
$router->post('/media/save', 'MediaController@create');
$router->put('/media/update/{id}', 'MediaController@update');
$router->delete('/media/delete/{id}', 'MediaController@destroy');
#endregion

#region Subject
$router->get('/subject[/{pageNo}/{pageSize}]', 'SubjectController@index');
$router->get('/subject/search', 'SubjectController@search');
$router->get('/subject/{id}', 'SubjectController@show');
$router->post('/subject/save', 'SubjectController@create');
$router->put('/subject/update/{id}', 'SubjectController@update');
$router->delete('/subject/delete/{id}', 'SubjectController@destroy');
#endregion

#region Keyword
$router->get('/keyword[/{pageNo}/{pageSize}]', 'KeywordController@index');
$router->post('/keyword/search', 'KeywordController@search');
$router->get('/keyword/{id}', 'KeywordController@show');
$router->post('/keyword/save', 'KeywordController@create');
$router->put('/keyword/update/{id}', 'KeywordController@update');
$router->delete('/keyword/delete/{id}', 'KeywordController@destroy');
#endregion

#region Feed
$router->get('/feed[/{pageNo}/{pageSize}]', 'FeedController@index');
$router->post('/feed/search', 'FeedController@search');
$router->get('/feed/{id}', 'FeedController@show');
$router->post('/feed/save', 'FeedController@create');
#endregion


#region Users
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});

$router->group(['prefix' => 'api', 'middleware' => ['jwt.auth']], function () use ($router) {
    $router->get('logout', 'AuthController@logout');
});
#endregion

// API with JWT
// $router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
//     $router->get('me', 'AuthController@me');
//     $router->get('/client[/{pageNo}/{pageSize}]', 'ClientController@index');
//     $router->get('/client/{id}', 'ClientController@show');
//     $router->post('/client/save', 'ClientController@create');
//     $router->put('/client/update/{id}', 'ClientController@update');
//     $router->delete('/client/delete/{id}', 'ClientController@destroy');
// });



$router->get('demo[/{name}]', function ($name = null) {
    if (is_null($name))
        return "Default Name";
    return $name;
});


Route::get('/', function () {
    $p = Redis::incr('p');
    return $p;
});

#region redis testing connection
$router->get('redist_test', function () use ($router) {
    $client = new \Predis\Client();
    $client->set('foo','bar');
    return 'foo stored as ' . $client->get('foo');
});


$router->get('redisconnect', function () {
    $client = new \Predis\Client();
    $client = $client->incr('p');
    return $client;
});

#endregion