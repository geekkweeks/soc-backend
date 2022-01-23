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
$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('me', 'AuthController@me');
    $router->get('/client[/{pageNo}/{pageSize}]', 'ClientController@index');
    $router->get('/client/getall', 'ClientController@getall');
    $router->get('/client/{id}', 'ClientController@show');
    $router->post('/client/search', 'ClientController@search');
    $router->post('/client/save', 'ClientController@create');
    $router->put('/client/update/{id}', 'ClientController@update');
    $router->delete('/client/delete/{id}', 'ClientController@destroy');
});

#region subject
$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('/subject[/{pageNo}/{pageSize}]', 'SubjectController@index');
    $router->get('/subject/search', 'SubjectController@search');
    $router->get('/subject/{id}', 'SubjectController@show');
    $router->post('/subject/getbyclient/{clientid}', 'SubjectController@getbyclient');
    $router->post('/subject/save', 'SubjectController@create');
    $router->put('/subject/update/{id}', 'SubjectController@update');
    $router->delete('/subject/delete/{id}', 'SubjectController@destroy');
});
#endregion

#region Feed
$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('/feed[/{pageNo}/{pageSize}]', 'FeedController@index');
    $router->get('/feed/{id}', 'FeedController@show');
    $router->post('/feed/search', 'FeedController@search');
    $router->post('/feed/save', 'FeedController@create');
});
#endregion

#region media
$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('/media/getall', 'MediaController@getall');
    $router->get('/media/{id}', 'MediaController@show');
    $router->get('/media[/{pageNo}/{pageSize}]', 'MediaController@index');
    $router->post('/media/search', 'MediaController@search');
    $router->post('/media/save', 'MediaController@create');
    $router->put('/media/update/{id}', 'MediaController@update');
    $router->delete('/media/delete/{id}', 'MediaController@destroy');
});
#endregion

#region Keyword
$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('/keyword/getall', 'KeywordController@getall');
    $router->get('/keyword/{id}', 'KeywordController@show');
    $router->get('/keyword[/{pageNo}/{pageSize}]', 'KeywordController@index');
    $router->post('/keyword/search', 'KeywordController@search');
    $router->post('/keyword/save', 'KeywordController@create');
    $router->put('/keyword/update/{id}', 'KeywordController@update');
    $router->delete('/keyword/delete/{id}', 'KeywordController@destroy');
});
#endregion

#region Conv typr
$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('/conversationtype', 'ConversationTypeController@index');
});
#endregion

#region talk about
$router->group(['middleware' => 'auth', 'prefix' => 'api'], function ($router) {
    $router->get('/talkabout', 'TalkAboutController@index');
});
#endregion



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
    $client->set('foo', 'bar');
    return 'foo stored as ' . $client->get('foo');
});


$router->get('redisconnect', function () {
    $client = new \Predis\Client();
    $client = $client->incr('p');
    return $client;
});

#endregion