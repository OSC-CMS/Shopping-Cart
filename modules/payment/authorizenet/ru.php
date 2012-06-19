<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

define('MODULE_PAYMENT_TYPE_PERMISSION', 'cod');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_TITLE', 'Authorize.net');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_DESCRIPTION', 'Credit Card Test Info:<br /><br />CC#: 4111111111111111<br />Expiry: Any');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_TYPE', 'Type:');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least  '.CC_OWNER_MIN_LENGTH.' characters.\n');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_NUMBER', '* The credit card number must be at least '.CC_NUMBER_MIN_LENGTH.' characters.\n');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR_MESSAGE', 'There has been an error processing your credit card. Please try again.');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_DECLINED_MESSAGE', 'Your credit card was declined. Please try another card or contact your bank for more info.');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR', 'Credit Card Error!');
define('MODULE_PAYMENT_AUTHORIZENET_TEXT_INFO', '');
define('TEXT_CCVAL_ERROR_INVALID_DATE', 'The "valid to" date ist invalid.<br />Please correct your information.');
define('TEXT_CCVAL_ERROR_INVALID_NUMBER', 'The "Credit card number", you entered, is invalid.<br />Please correct your information.');
define('TEXT_CCVAL_ERROR_UNKNOWN_CARD', 'The first 4 digits of your Credit Card are: %s<br />If this information is correct, your type of card is not accepted.<br />Please correct your information.');

define('MODULE_PAYMENT_AUTHORIZENET_TXNKEY_TITLE', 'Transaction Key');
define('MODULE_PAYMENT_AUTHORIZENET_TXNKEY_DESC', 'Transaction Key used for encrypting TP data');
define('MODULE_PAYMENT_AUTHORIZENET_TESTMODE_TITLE', 'Transaction Mode');
define('MODULE_PAYMENT_AUTHORIZENET_TESTMODE_DESC', 'Transaction mode used for processing orders');
define('MODULE_PAYMENT_AUTHORIZENET_METHOD_TITLE', 'Transaction Method');
define('MODULE_PAYMENT_AUTHORIZENET_METHOD_DESC', 'Transaction method used for processing orders');
define('MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER_TITLE', 'Customer Notifications');
define('MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER_DESC', 'Should Authorize.Net eMail a receipt to the customer?');
define('MODULE_PAYMENT_AUTHORIZENET_STATUS_TITLE', 'Enable Authorize.net Module');
define('MODULE_PAYMENT_AUTHORIZENET_STATUS_DESC', 'Do you want to accept Authorize.net payments?');
define('MODULE_PAYMENT_AUTHORIZENET_LOGIN_TITLE', 'Login Username');
define('MODULE_PAYMENT_AUTHORIZENET_LOGIN_DESC', 'The login username used for the Authorize.net service');
define('MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID_TITLE', 'Set Order Status');
define('MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
define('MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER_TITLE', 'Sort order of display.');
define('MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_AUTHORIZENET_ZONE_TITLE', 'Payment Zone');
define('MODULE_PAYMENT_AUTHORIZENET_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_AUTHORIZENET_ALLOWED_TITLE', 'Allowed zones');
define('MODULE_PAYMENT_AUTHORIZENET_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this module (e. g. AT,DE (leave empty if you want to allow all zones))');
?>