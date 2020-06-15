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
$app->post('/forgot/verify-phone-and-send-otp', 'AuthController@verifyPhoneAndSendOtp');
$app->post('/forgot/verify-otp', 'AuthController@verifyOtp');
$app->post('/login', 'AuthController@login');
$app->post('/register', 'AuthController@register');

// Role
$app->group([
    'prefix'    => "/role",
    'namespace' => $namespace
], function () use ($app) {
    $app->get('/all', 'RoleController@getAll');
});

// City
$app->group([
    'prefix'    => "/city",
    'namespace' => $namespace
], function () use ($app) {
    $app->get('/all', 'CityController@getAll');
});

// District
$app->group([
    'prefix'    => "/district",
    'namespace' => $namespace
], function () use ($app) {
    $app->get('/all', 'DistrictController@getAll');
    $app->get('/list-by-city/{id}', 'DistrictController@getListByCity');
});

// Brand
$app->group([
    'prefix'    => "/brand",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'BrandController@create');
    $app->get('/all', 'BrandController@getAll');
    $app->get('/{id}', 'BrandController@getDetail');
    $app->post('/{id}', 'BrandController@update');
    $app->get('/', 'BrandController@getList');
    $app->delete('/delete/{id}', 'BrandController@delete');
});

// Service category
$app->group([
    'prefix'    => "/service-category",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'ServiceCategoryController@create');
    $app->get('/all', 'ServiceCategoryController@getAll');
    $app->get('/{id}', 'ServiceCategoryController@getDetail');
    $app->post('/{id}', 'ServiceCategoryController@update');
    $app->get('/', 'ServiceCategoryController@getList');
    $app->delete('/delete/{id}', 'ServiceCategoryController@delete');
});

// Service
$app->group([
    'prefix'    => "/service",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'ServiceController@create');
    $app->get('/all', 'ServiceController@getAll');
    $app->get('/{id}', 'ServiceController@getDetail');
    $app->post('/{id}', 'ServiceController@update');
    $app->get('/', 'ServiceController@getList');
    $app->delete('/delete/{id}', 'ServiceController@delete');
});

// Customer
$app->group([
    'prefix'    => "/customer",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'CustomerController@create');
    $app->get('/all', 'CustomerController@getAll');
    $app->get('/get-count', 'CustomerController@getCount');
    $app->get('/get-by-phone-and-name', 'CustomerController@getByPhoneOrName');
    $app->get('/{id}', 'CustomerController@getDetail');
    $app->post('/{id}', 'CustomerController@update');
    $app->get('/', 'CustomerController@getList');
    $app->delete('/delete/{id}', 'ServiceController@delete');
});

// Customer type
$app->group([
    'prefix'    => "/customer-type",
    'namespace' => $namespace
], function () use ($app) {
    $app->get('/all', 'CustomerTypeController@getAll');
});

// Order
$app->group([
    'prefix'    => "/order",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'OrderController@create');
    $app->get('/all', 'OrderController@getAll');
    $app->get('/get-count', 'OrderController@getCount');
    $app->get('/{id}', 'OrderController@getDetail');
    $app->post('/{id}', 'OrderController@update');
    $app->get('/', 'OrderController@getList');
});

// Employee
$app->group([
    'prefix'    => "/employee",
    'namespace' => $namespace
], function () use ($app) {
    $app->post('/', 'EmployeeController@create');
    $app->get('/all', 'EmployeeController@getAll');
    $app->get('/get-by-phone-and-name', 'EmployeeController@getByPhoneOrName');
    $app->get('/{id}', 'EmployeeController@getDetail');
    $app->post('/{id}', 'EmployeeController@update');
    $app->get('/', 'EmployeeController@getList');
});

// Report
$app->group([
    'prefix'    => "/report",
    'namespace' => $namespace
], function () use ($app) {
    $app->get('/get-total', 'ReportController@getTotal');
    $app->get('/get-total-by-employee-date', 'ReportController@getTotalByEmployeeAndDate');
    $app->get('/get-total-by-service-date', 'ReportController@getTotalByServiceAndDate');
});



