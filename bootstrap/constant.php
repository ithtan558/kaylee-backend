<?php

// Define response
define('RESPONSE_SUCCESS', true);
define('RESPONSE_FAILED', false);

// Define YES, NO for string
define('STRING_YES', "Y");
define('STRING_NO', "N");

define('STATUS_ACTIVE', 1);
define('STATUS_INACTIVE', 0);

// Define structure API response key
define('STT_CODE_KEY', 'stt_code');
define('RESPONSE_KEY', 'response');
define('MESSAGE_KEY', 'message');
define('DATA_KEY', 'data');
define('STATUS_KEY', 'status');

// Dir Upload file
define('DIR_UPLOAD', '/upload/images/');

/*
 | define order status MMAP
----------------------------------------------------------------------------
*/
define('ORDER_STATUS_ORDERED', 1);
define('ORDER_STATUS_WAITING', 2);
define('ORDER_STATUS_FINISHED', 3);
define('ORDER_STATUS_NOT_PAID', 4);
define('ORDER_STATUS_CANCEL', 5);
define('ORDER_STATUS_ACCEPTED', 6);
define('ORDER_STATUS_REFUND', 7);

/*
 | define payment method
----------------------------------------------------------------------------
*/
define('PAYMENT_METHOD_CASH', 1);

/*
 | define start and end time
----------------------------------------------------------------------------
*/
define('START_TIME', '08:00');
define('END_TIME', '22:00');

/*
 | define role id
----------------------------------------------------------------------------
*/
define('ROLE_SUPERADMIN', 1);
define('ROLE_MANAGER', 2);
define('ROLE_BRAND_MANAGER', 3);
define('ROLE_EMPLOYEE', 4);

/*
 | define type customer
----------------------------------------------------------------------------
*/
define('CUSTOMER_VIP', 1);
define('CUSTOMER_NORMAL', 2);

/*
 | define name auto when register at first time
----------------------------------------------------------------------------
*/
define('NAME_REGISTER', 'Chi nhánh của bạn');

/*
 | define default otp
----------------------------------------------------------------------------
*/
define('DEFAULT_NUMBER_OTP', '0000');

/*
 | define code for errors
----------------------------------------------------------------------------
*/
define('ERRORS', [
    'first_name'                            => 100001,
    'last_name'                             => 100002,
    'cart_customer.last_name'               => 100002,
    'phone'                                 => 100003,
    'password'                              => 100004,
    'account'                               => 100005,
    'otp'                                   => 100006,
    'user_id'                               => 100007,
    'name'                                  => 100008,
    'cart_supplier_information.name'        => 100008,
    'city_id'                               => 100009,
    'cart_supplier_information.city_id'     => 100009,
    'district_id'                           => 100010,
    'cart_supplier_information.district_id' => 100010,
    'start_time'                            => 100011,
    'end_time'                              => 100012,
    'image'                                 => 100013,
    'role_id'                               => 100014,
    'brand_id'                              => 100015,
    'cart_items'                            => 100016,
    'cart_employee'                         => 100017,
    'code'                                  => 100018,
    'brand_ids'                             => 100019,
    'time'                                  => 100020,
    'price'                                 => 100021,
    'email'                                 => 100022,
    'id'                                    => 100023,
    'start_date'                            => 100024,
    'end_date'                              => 100025,
    'supplier_id'                           => 100026,
    'cart_supplier_information.address'     => 100027,
    'cart_supplier_information.wards_id'    => 100028,
    'cart_customer.name'                    => 100029,
]);

/*
 | define path for image
----------------------------------------------------------------------------
*/
define('URL_IMAGE_CMS', "http://cms.kaylee.vn");
define('PATH_IMAGE_SUPPLIER', URL_IMAGE_CMS . '/upload/');
define('PATH_IMAGE_ADS', URL_IMAGE_CMS . '/upload/ads/');

define('URL', "http://" . $_SERVER['SERVER_NAME']);
define('PATH_IMAGE', URL . '/upload/images/');

/*
 | define status for reservation
----------------------------------------------------------------------------
*/
define('RESERVATION_BOOKED', 1);
define('RESERVATION_CAME', 2);
define('RESERVATION_ORDERED', 3);
define('RESERVATION_CANCELED', 4);

/*
 | define status for notification
----------------------------------------------------------------------------
*/
define('NOTIFICATION_NOT_READ', 1);
define('NOTIFICATION_READ', 2);

/*
 | define prefix number phone for notification
----------------------------------------------------------------------------
*/
define('NUMBER_PREFIXES', ['0812', '0813', '0814', '0815', '0816', '0817', '0818', '0819', '0909', '0908']);

// Define TYPE for OTP
define('TYPE_OTP_REGISTER', 1);
define('TYPE_OTP_FORGOT', 2);

// Define link OTP
define('LINK_OTP', 'https://cloudsms.vietguys.biz:4438/api/index.php');
define('USERNAME', 'kaylee');
define('PASSWORD', '3j2sv');
define('FROM', 'KAYLEE');
define('TYPE', 8);
define('JSON', 1);
