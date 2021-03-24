<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
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

$app->get('/', function () use ($app) {
    return $app->version();
});

// Authentication user
$app->get('/user-info', 'AuthController@getUserInfo');
$app->get('/logout', 'AuthController@logout');
$app->post('/forgot/verify-phone-and-send-otp', 'AuthController@verifyPhoneAndSendOtp');
$app->post('/forgot/verify-otp', 'AuthController@verifyOtp');
$app->post('/register/verify-otp', 'AuthController@verifyOtpForRegister');
$app->post('/forgot/update-password', 'AuthController@updatePassword');
$app->post('/login', 'AuthController@login');
$app->post('/register', 'AuthController@register');
$app->post('/update', 'AuthController@update');

// Role
$app->group([
    'prefix'     => "/role",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/all', 'RoleController@getAll');
});

// City
$app->group([
    'prefix'     => "/city",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/all', 'CityController@getAll');
});

// District
$app->group([
    'prefix'     => "/district",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/all', 'DistrictController@getAll');
    $app->get('/list-by-city/{id}', 'DistrictController@getListByCity');
});

// Wards
$app->group([
    'prefix'     => "/wards",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/all', 'WardsController@getAll');
    $app->get('/list-by-district/{id}', 'WardsController@getListByDistrict');
});

// Brand
$app->group([
    'prefix'     => "/brand",
    'middleware' => ['jwt.auth']
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
    'prefix'     => "/service-category",
    'middleware' => ['jwt.auth']
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
    'prefix'     => "/service",
    'middleware' => ['jwt.auth']
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
    'prefix'     => "/customer",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->post('/', 'CustomerController@create');
    $app->get('/all', 'CustomerController@getAll');
    $app->get('/get-count', 'CustomerController@getCount');
    $app->get('/get-by-phone-and-name', 'CustomerController@getByPhoneOrName');
    $app->get('/{id}', 'CustomerController@getDetail');
    $app->post('/{id}', 'CustomerController@update');
    $app->get('/', 'CustomerController@getList');
    $app->delete('/delete/{id}', 'CustomerController@delete');
});

// Customer type
$app->group([
    'prefix'     => "/customer-type",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/all', 'CustomerTypeController@getAll');
});

// Order
$app->group([
    'prefix'     => "/order",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->post('/', 'OrderController@create');
    $app->get('/reason-cancel', 'OrderReasonCancelController@getAll');
    $app->get('/all', 'OrderController@getAll');
    $app->get('/get-count', 'OrderController@getCount');
    $app->get('/{id}', 'OrderController@getDetail');
    $app->post('/{id}', 'OrderController@update');
    $app->get('/', 'OrderController@getList');
    $app->post('/update-status/{id}', 'OrderController@updateStatus');
});

// Order Supplier
$app->group([
    'prefix'     => "/supplier/order",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->post('/', 'OrderController@createSupplier');
    $app->post('/{id}', 'OrderController@updateSupplier');
});

// Employee
$app->group([
    'prefix'     => "/employee",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->post('/', 'EmployeeController@create');
    $app->get('/all', 'EmployeeController@getAll');
    $app->get('/get-by-phone-and-name', 'EmployeeController@getByPhoneOrName');
    $app->get('/{id}', 'EmployeeController@getDetail');
    $app->post('/{id}', 'EmployeeController@update');
    $app->get('/', 'EmployeeController@getList');
    $app->delete('/delete/{id}', 'EmployeeController@delete');
});

// Report
$app->group([
    'prefix'     => "/report",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/get-total', 'ReportController@getTotal');
    $app->get('/get-total-by-employee-date', 'ReportController@getTotalByEmployeeAndDate');
    $app->get('/get-total-by-service-date', 'ReportController@getTotalByServiceAndDate');
});

// Content
$app->group([
    'prefix'    => "/content"
], function () use ($app) {
    $app->get('/{slug}', 'ContentController@getDetail');
});

// Supplier
$app->group([
    'prefix'     => "/supplier",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/', 'SupplierController@getList');
    $app->get('/{id}', 'SupplierController@getDetail');
});

// Product category
$app->group([
    'prefix'     => "/product-category",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->post('/', 'ProductCategoryController@create');
    $app->get('/all', 'ProductCategoryController@getAll');
    $app->get('/{id}', 'ProductCategoryController@getDetail');
    $app->post('/{id}', 'ProductCategoryController@update');
    $app->get('/', 'ProductCategoryController@getList');
    $app->delete('/delete/{id}', 'ProductCategoryController@delete');
});

// Product
$app->group([
    'prefix'     => "/product",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->post('/', 'ProductController@create');
    $app->get('/all', 'ProductController@getAll');
    $app->get('/{id}', 'ProductController@getDetail');
    $app->post('/{id}', 'ProductController@update');
    $app->get('/', 'ProductController@getList');
    $app->delete('/delete/{id}', 'ProductController@delete');
});

// Notification
$app->group([
    'prefix'     => "/notification",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/count-not-read', 'NotificationController@getCount');
    $app->post('/update-status', 'NotificationController@updateStatus');
    $app->get('/test', 'NotificationController@testNotificati1on');
    $app->get('/test-topic', 'NotificationController@testTopic');
    $app->get('/', 'NotificationController@getList');
    $app->get('/{id}', 'NotificationController@getDetail');
    $app->delete('/delete/all', 'NotificationController@deleteAll');
    $app->delete('/delete/{id}', 'NotificationController@delete');
});

// Commission
$app->group([
    'prefix'     => "/commission",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/detail', 'CommissionController@detail');
    $app->get('/product/list-order', 'CommissionController@getListProduct');
    $app->get('/service/list-order', 'CommissionController@getListService');
    $app->get('/setting', 'CommissionController@getDetailSetting');
    $app->post('/setting/update', 'CommissionController@updateSetting');
});

// Reservation
$app->group([
    'prefix'     => "/reservation",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->post('/', 'ReservationController@create');
    $app->get('/all', 'ReservationController@getAll');
    $app->get('/{id}', 'ReservationController@getDetail');
    $app->post('/{id}', 'ReservationController@update');
    $app->post('/update-status/{id}', 'ReservationController@updateStatus');
    $app->get('/', 'ReservationController@getList');
    $app->delete('/delete/{id}', 'ReservationController@delete');
});

// Version
$app->group([
    'prefix'     => "/version",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/', 'VersionController@getDetail');
});

// Campaign
$app->group([
    'prefix'     => "/campaign",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/all', 'CampaignController@getAll');
});

// Ads
$app->group([
    'prefix'     => "/ads",
    'middleware' => ['jwt.auth']
], function () use ($app) {
    $app->get('/all', 'AdsController@getAll');
});
