<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_TITLE', '2CheckOut');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_DESCRIPTION', 'Credit Card Test Info:<br /><br />CC#: 4111111111111111<br />Expiry: Any');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_TYPE', 'Type:');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_CREDIT_CARD_OWNER_FIRST_NAME', 'Credit Card Owner First Name:');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_CREDIT_CARD_OWNER_LAST_NAME', 'Credit Card Owner Last Name:');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_CREDIT_CARD_CHECKNUMBER', 'Credit Card Checknumber:');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION', '(located at the back of the credit card)');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_ERROR_MESSAGE', 'There has been an error processing your credit card! Please try again.');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_ERROR', 'Credit Card Error!');
define('MODULE_PAYMENT_PM2CHECKOUT_TEXT_INFO','');
define('MODULE_PAYMENT_PM2CHECKOUT_STATUS_TITLE' , 'Enable 2CheckOut Module');
define('MODULE_PAYMENT_PM2CHECKOUT_STATUS_DESC' , 'Do you want to accept 2CheckOut payments?');
define('MODULE_PAYMENT_PM2CHECKOUT_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_PAYMENT_PM2CHECKOUT_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_PM2CHECKOUT_LOGIN_TITLE' , 'Login/Store Number');
define('MODULE_PAYMENT_PM2CHECKOUT_LOGIN_DESC' , 'Login/Store Number used for the 2CheckOut service');
define('MODULE_PAYMENT_PM2CHECKOUT_TESTMODE_TITLE' , 'Transaction Mode');
define('MODULE_PAYMENT_PM2CHECKOUT_TESTMODE_DESC' , 'Transaction mode used for the 2Checkout service');
define('MODULE_PAYMENT_PM2CHECKOUT_EMAIL_MERCHANT_TITLE' , 'Merchant Notifications');
define('MODULE_PAYMENT_PM2CHECKOUT_EMAIL_MERCHANT_DESC' , 'Should 2CheckOut eMail a receipt to the store owner?');
define('MODULE_PAYMENT_PM2CHECKOUT_SORT_ORDER_TITLE' , 'Sort order of display');
define('MODULE_PAYMENT_PM2CHECKOUT_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_PM2CHECKOUT_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_PM2CHECKOUT_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_PM2CHECKOUT_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_PM2CHECKOUT_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>