<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

define('MODULE_PAYMENT_ROBOXCHANGE_TEXT_TITLE', 'roboXchange.net (WebMoney, Yandex-Money, E-gold, MoneyMail, RuPay, INOCard)');
define('MODULE_PAYMENT_ROBOXCHANGE_TEXT_DESCRIPTION', 'roboXchange.net (WebMoney, Yandex-Money, E-gold, MoneyMail, RuPay, INOCard)');
define('MODULE_PAYMENT_ROBOXCHANGE_STATUS_TITLE','Allow to use roboXchange.net module');
define('MODULE_PAYMENT_ROBOXCHANGE_STATUS_DESC','Install<br />
1) Register with http://roboxchange.net as merchant using the currency wich is equal to your shop default currency.<br />
2) At http://roboxchange.net site in "Administration" define:<br />
"Password #1" any<br />
"Password #2" any<br />
"Result URL" http://~your-shop~/robox.php<br />
"method of query to Result URL" POST<br />
"Success URL" http://~your-shop~/checkout_process.php<br />
"method of query to Success URL" POST<br />
"Fail URL" http://~your-shop~/checkout_payment.php<br />
"method of query to Fail URL" POST<br />
3) Define in your shop admin, this module settings: login for http://roboxchange.net and Passwords 1 and 2.<br /><br />Allow to use roboXchange.net module');
  define('MODULE_PAYMENT_ROBOXCHANGE_ALLOWED_TITLE' , 'Allowed Zones');
  define('MODULE_PAYMENT_ROBOXCHANGE_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
  define('MODULE_PAYMENT_ROBOXCHANGE_LOGIN_TITLE','Login');
  define('MODULE_PAYMENT_ROBOXCHANGE_LOGIN_DESC','Your login in roboxchange cash register');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1_TITLE','Password 1');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1_DESC','Your password 1 in roboxchange cash register');
  define('MODULE_PAYMENT_ROBOXCHANGE_SORT_ORDER_TITLE','Sort order of display');
  define('MODULE_PAYMENT_ROBOXCHANGE_SORT_ORDER_DESC','Sort order of display. Lowest is displayed first.');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD2_TITLE','Password 2');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD2_DESC','Your password 1 in roboxchange cash register');
  define('MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS_TITLE','Set Order Status');
  define('MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS_DESC','Set the status of orders made with this payment module to this value');
  
?>