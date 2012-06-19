<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/


  define('MODULE_PAYMENT_WEBMONEY_TEXT_TITLE', 'WebMoney');
  define('MODULE_PAYMENT_WEBMONEY_TEXT_DESCRIPTION', 'Make payable to:<br /><br />Our WM ID: ' . MODULE_PAYMENT_WEBMONEY_WMID . '<br />WMZ number: ' . MODULE_PAYMENT_WEBMONEY_WMZ . '<br />WMR number: ' . MODULE_PAYMENT_WEBMONEY_WMR . '<br /><br />' . 'Your order will not ship until we receive payment!');
  define('MODULE_PAYMENT_WEBMONEY_TEXT_EMAIL_FOOTER', "Make payable to:\n\nOur WM ID: ". MODULE_PAYMENT_WEBMONEY_WMID . "\n\nWMZ number: ". MODULE_PAYMENT_WEBMONEY_WMZ . "\n\nWMR number: ". MODULE_PAYMENT_WEBMONEY_WMR . "\n\n" . 'Your order will not ship until we receive payment!');
define('MODULE_PAYMENT_WEBMONEY_TEXT_INFO','');
  define('MODULE_PAYMENT_WEBMONEY_STATUS_TITLE' , 'Enable WebMoney Payment Module');
define('MODULE_PAYMENT_WEBMONEY_STATUS_DESC' , 'Do you want to accept WebMoney payments?');
define('MODULE_PAYMENT_WEBMONEY_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_WEBMONEY_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_WEBMONEY_WMID_TITLE' , 'Your WM ID number:');
define('MODULE_PAYMENT_WEBMONEY_WMID_DESC' , 'WM ID Number');
define('MODULE_PAYMENT_WEBMONEY_WMZ_TITLE' , 'Your WMZ number:');
define('MODULE_PAYMENT_WEBMONEY_WMZ_DESC' , 'WMZ Number');
define('MODULE_PAYMENT_WEBMONEY_WMR_TITLE' , 'Your WMR number:');
define('MODULE_PAYMENT_WEBMONEY_WMR_DESC' , 'WMR Number');
define('MODULE_PAYMENT_WEBMONEY_SORT_ORDER_TITLE' , 'Sort order of display');
define('MODULE_PAYMENT_WEBMONEY_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_WEBMONEY_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_WEBMONEY_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_WEBMONEY_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_WEBMONEY_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>