<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

  define('MODULE_PAYMENT_WORLDPAY_TEXT_TITLE', 'Secure Credit Card Payment');
  define('MODULE_PAYMENT_WORLDPAY_TEXT_DESC', 'Worldpay Payment Module');
  define('MODULE_PAYMENT_WORLDPAY_TEXT_INFO','');
  define('MODULE_PAYMENT_WORLDPAY_STATUS_TITLE', 'Enable WorldPay Module');
  define('MODULE_PAYMENT_WORLDPAY_STATUS_DESC', 'Do you want to accept WorldPay payments?');

  define('MODULE_PAYMENT_WORLDPAY_ID_TITLE', 'Worldpay Installation ID');
  define('MODULE_PAYMENT_WORLDPAY_ID_DESC', 'Your WorldPay Select Junior ID');

  define('MODULE_PAYMENT_WORLDPAY_MODE_TITLE', 'Mode');
  define('MODULE_PAYMENT_WORLDPAY_MODE_DESC', 'The mode you are working in (100 = Test Mode Accept, 101 = Test Mode Decline, 0 = Live');

  define('MODULE_PAYMENT_WORLDPAY_USEMD5_TITLE', 'Use MD5');
  define('MODULE_PAYMENT_WORLDPAY_USEMD5_DESC', 'Use MD5 encyption for transactions? (1 = Yes, 0 = No)');

  define('MODULE_PAYMENT_WORLDPAY_MD5KEY_TITLE', 'Use MD5');
  define('MODULE_PAYMENT_WORLDPAY_MD5KEY_DESC', 'Use MD5 encyption for transactions? (1 = Yes, 0 = No)');

  define('MODULE_PAYMENT_WORLDPAY_SORT_ORDER_TITLE', 'Sort order of display.');
  define('MODULE_PAYMENT_WORLDPAY_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');

  define('MODULE_PAYMENT_WORLDPAY_USEPREAUTH_TITLE', 'Use Pre-Authorisation?');
  define('MODULE_PAYMENT_WORLDPAY_USEPREAUTH_DESC', 'Do you want to pre-authorise payments? Default=False. You need to request this from WorldPay before using it.');

  define('MODULE_PAYMENT_WORLDPAY_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_WORLDPAY_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');

  define('MODULE_PAYMENT_WORLDPAY_PREAUTH_TITLE', 'Pre-Auth');
  define('MODULE_PAYMENT_WORLDPAY_PREAUTH_DESC', 'The mode you are working in (A = Pay Now, E = Pre Auth). Ignored if Use PreAuth is False.');

  define('MODULE_PAYMENT_WORLDPAY_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_WORLDPAY_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');

define('MODULE_PAYMENT_WORLDPAY_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_WORLDPAY_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

?>