<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

  define('MODULE_PAYMENT_TYPE_PERMISSION', 'bt');

  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE', 'Banktransfer');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION', 'Banktransfer Payments');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK', 'Banktransfer');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER', 'Note: You can download our Fax Confirmation form from here: ' . HTTP_SERVER . DIR_WS_CATALOG . MODULE_PAYMENT_BANKTRANSFER_URL_NOTE . '');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_INFO', 'Please note that Banktransfer Payments are <b>only</b> available from a <b>german</b> bank account!');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER', 'Account Owner:');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER', 'Account Number:');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ', 'Bank Code:');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME', 'Bank:');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX', 'Banktransfer Payment will be confirmed by fax');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO','');

  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR', 'ERROR:');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1', 'Account number and bank code do not fit! Please check again.');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2', 'No plausibility check method available for this bank code!');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_3', 'Account number cannot be verified!');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4', 'Account number cannot be verified! Please check again.');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_5', 'Bank code not found! Please check again.');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_8', 'Incorrect bank code or no bank code entered!');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_9', 'No account number indicated!');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_10', 'No account holder indicated!');

  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE', 'Note:');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE2', 'If you do not want to send your<br />account data over the internet you can download our ');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE3', 'Fax form');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE4', ' and sent it back to us.');

  define('JS_BANK_BLZ', 'Please ente the BLZ or your bank!\n');
  define('JS_BANK_NAME', 'Please enter your name and bank!\n');
  define('JS_BANK_NUMBER', 'Please enter your account number!\n');
  define('JS_BANK_OWNER', 'Please enter the name of the account owner!\n');

  define('MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_TITLE' , 'Use database lookup for Bank Code?');
define('MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_DESC' , 'Do you want to use database lookup for Bank Code? Ensure that the table banktransfer_blz exists and is set up properly!');
define('MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_TITLE' , 'Fax-URL');
define('MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_DESC' , 'The fax-confirmation file. It must located in catalog-dir');
define('MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_TITLE' , 'Allow Fax Confirmation');
define('MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_DESC' , 'Do you want to allow fax confirmation?');
define('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_TITLE' , 'Sort order of display');
define('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
define('MODULE_PAYMENT_BANKTRANSFER_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_BANKTRANSFER_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_BANKTRANSFER_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_PAYMENT_BANKTRANSFER_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_BANKTRANSFER_STATUS_TITLE' , 'Allow Banktransfer Payments');
define('MODULE_PAYMENT_BANKTRANSFER_STATUS_DESC' , 'Do you want to accept banktransfer payments?');
define('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_TITLE' , 'Minimum Orders');
define('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_DESC' , 'Minimum orders for a Customer to view this Option.');
?>