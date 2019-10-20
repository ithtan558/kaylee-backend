<?php

/*
 | Define link Api
----------------------------------------------------------------------------
*/
define('API_REPORT', 'https://api.ch.diqit.io/v1/report/');
define('API_REPORT_STAGE', 'production');

/*
 | define method
----------------------------------------------------------------------------
*/

define('METHOD_POST', 'POST');

/*
 | Define response
----------------------------------------------------------------------------
*/
define('RESPONSE_SUCCESS', true);
define('RESPONSE_FAILED', false);
// Define text
define('RESPONSE_TEXT_SYNC_ORDER_SUCCESS', 'Sync order is successfully');
define('RESPONSE_TEXT_SYNC_ORDER_FAILED', 'Sync order is failed');

/*
 | define order type
----------------------------------------------------------------------------
*/
define('ORDER_TYPE_TAKEAWAY', 'C');
define('ORDER_TYPE_DELIVERY', 'D');
define('ORDER_TYPE_DINEIN', 'I');
define('ORDER_TYPE_EATIN', 'I');

define('ORDER_NAME_TYPE_TAKEAWAY', 'CARRYOUT');
define('ORDER_NAME_TYPE_DELIVERY', 'DELIVERY');
define('ORDER_NAME_TYPE_DINEIN', 'TAKEAWAY');
define('ORDER_NAME_TYPE_EATIN', 'TAKEAWAY');

/*
 | define order type
----------------------------------------------------------------------------
*/
define('DEVICE_POS', 1);
define('DEVICE_DINEIN', 2);
define('DEVICE_ONLINE', 3);

define('DEVICE_NAME_POS', "POS");
define('DEVICE_NAME_DINEIN', "DINEIN");
define('DEVICE_NAME_ONLINE', "ONLINE");

/*
 | define order channel
----------------------------------------------------------------------------
*/
define('CHANNEL_ONLINE', "ONLINE");
define('CHANNEL_CALLCENTER', "CALLCENTER");
define('CHANNEL_OFFLINE', "OFFLINE");

/*
 | define YES, NO for string
----------------------------------------------------------------------------
*/
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

define('ORDER_TAKER_STATUS_SUCCESS', 1);
define('ORDER_TAKER_STATUS_SUCCESS_TEXT', 'Succeed');
define('ORDER_TAKER_STATUS_DOWNLOADING', 2);
define('ORDER_TAKER_STATUS_DOWNLOADING_TEXT', 'Downloading');
define('ORDER_TAKER_STATUS_FAILED', 3);
define('ORDER_TAKER_STATUS_FAILED_TEXT', 'Failed');

define('API_POS_PATTERN', 'https://%s.diqit.io');
define('API_POS_MANAGER_PATTERN', 'https://%s.posmanager.diqit.io');

define('ROLES_ROOT', 1);
define('ROLES_ADMIN', 3);
define('ROLES_USER', 7);

define('ADS_STATUS_SUCCESS', 1);
define('ADS_STATUS_SUCCESS_TEXT', 'Succeed');
define('ADS_STATUS_DOWNLOADING', 2);
define('ADS_STATUS_DOWNLOADING_TEXT', 'Downloading');
define('ADS_STATUS_FAILED', 3);
define('ADS_STATUS_FAILED_TEXT', 'Failed');
