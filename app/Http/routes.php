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

$namespace = 'App\Http\Controllers';

$app->get('/', function () use ($app) {
    return $app->version();
});

// Authentication user
$app->get('/user-info', 'AuthController@getUserInfo');
$app->get('/logout', 'AuthController@logout');
$app->post('/login', 'AuthController@login');

// City
$app->group([
    'prefix' => "/city",
    'namespace' => $namespace
], function () use ($app) {
    $app->get('/all', 'CityController@getAll');
});

// District
$app->group([
    'prefix' => "/district",
    'namespace' => $namespace
], function () use ($app) {
    $app->get('/all', 'DistrictController@getAll');
    $app->get('/list-by-city/{id}', 'DistrictController@getListByCity');
});

// Brand
$app->group([
    'prefix' => "/brand",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'BrandController@create');
    $app->get('/all', 'BrandController@getAll');
    $app->get('/{id}', 'BrandController@getDetail');
    $app->post('/{id}', 'BrandController@update');
    $app->get('/', 'BrandController@getList');
});

// Service category
$app->group([
    'prefix' => "/service-category",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'ServiceCategoryController@create');
    $app->get('/all', 'ServiceCategoryController@getAll');
    $app->get('/{id}', 'ServiceCategoryController@getDetail');
    $app->post('/{id}', 'ServiceCategoryController@update');
    $app->get('/', 'ServiceCategoryController@getList');
});

// Service
$app->group([
    'prefix' => "/service",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'ServiceController@create');
    $app->get('/all', 'ServiceController@getAll');
    $app->get('/{id}', 'ServiceController@getDetail');
    $app->post('/{id}', 'ServiceController@update');
    $app->get('/', 'ServiceController@getList');
});


