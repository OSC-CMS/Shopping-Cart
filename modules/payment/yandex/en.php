<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('MODULE_PAYMENT_YANDEX_TEXT_TITLE', 'Yandex-Money');
define('MODULE_PAYMENT_YANDEX_TEXT_DESCRIPTION', 'Make payable to:<br /><br />Our Yandex Money ID: ' . MODULE_PAYMENT_YANDEX_ID . '<br /><br />' . 'Your order will not ship until we receive payment!');
define('MODULE_PAYMENT_YANDEX_TEXT_EMAIL_FOOTER', "Make payable to:\n\nOur Yandex Money ID: ". MODULE_PAYMENT_YANDEX_ID . "\n\n" . 'Your order will not ship until we receive payment!');
define('MODULE_PAYMENT_YANDEX_TEXT_INFO','');
define('MODULE_PAYMENT_YANDEX_STATUS_TITLE' , 'Enable Yandex-Money Payment Module');
define('MODULE_PAYMENT_YANDEX_STATUS_DESC' , 'Do you want to accept Yandex-Money payments?');
define('MODULE_PAYMENT_YANDEX_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_YANDEX_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_YANDEX_ID_TITLE' , 'Your Yandex-Money number:');
define('MODULE_PAYMENT_YANDEX_ID_DESC' , 'Yandex-Money Number');
define('MODULE_PAYMENT_YANDEX_SORT_ORDER_TITLE' , 'Sort order of display');
define('MODULE_PAYMENT_YANDEX_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_YANDEX_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_YANDEX_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_YANDEX_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_YANDEX_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>