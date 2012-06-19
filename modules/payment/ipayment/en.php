<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/


  define('MODULE_PAYMENT_IPAYMENT_TEXT_TITLE', 'iPayment');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_DESCRIPTION', 'Credit Card Test Info:<br /><br />CC#: 4111111111111111<br />Expiry: Any');
  define('IPAYMENT_ERROR_HEADING', 'There has been an error processing your credit card:');
  define('IPAYMENT_ERROR_MESSAGE', 'Please check your credit card details!');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER', 'Credit Card Checknumber');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION', '(located at the back of the credit card)');
define('MODULE_PAYMENT_IPAYMENT_TEXT_INFO','');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');

  define('MODULE_PAYMENT_IPAYMENT_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_PAYMENT_IPAYMENT_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_IPAYMENT_ID_TITLE' , 'Account Number');
define('MODULE_PAYMENT_IPAYMENT_ID_DESC' , 'The account number used for the iPayment service');
define('MODULE_PAYMENT_IPAYMENT_STATUS_TITLE' , 'Enable iPayment Module');
define('MODULE_PAYMENT_IPAYMENT_STATUS_DESC' , 'Do you want to accept iPayment payments?');
define('MODULE_PAYMENT_IPAYMENT_PASSWORD_TITLE' , 'User Password');
define('MODULE_PAYMENT_IPAYMENT_PASSWORD_DESC' , 'The user password for the iPayment service');
define('MODULE_PAYMENT_IPAYMENT_USER_ID_TITLE' , 'User ID');
define('MODULE_PAYMENT_IPAYMENT_USER_ID_DESC' , 'The user ID for the iPayment service');
define('MODULE_PAYMENT_IPAYMENT_CURRENCY_TITLE' , 'Transaction Currency');
define('MODULE_PAYMENT_IPAYMENT_CURRENCY_DESC' , 'The currency to use for credit card transactions');
define('MODULE_PAYMENT_IPAYMENT_SORT_ORDER_TITLE' , 'Sort order of display');
define('MODULE_PAYMENT_IPAYMENT_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_IPAYMENT_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_IPAYMENT_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>