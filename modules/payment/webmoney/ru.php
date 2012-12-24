<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

define('MODULE_PAYMENT_WEBMONEY_TEXT_TITLE', 'WebMoney');
define('MODULE_PAYMENT_WEBMONEY_TEXT_DESCRIPTION', 'Информация для оплаты:<br /><br />WM идентификатор: ' . MODULE_PAYMENT_WEBMONEY_WMID . '<br />Кошелёк WMZ: ' . MODULE_PAYMENT_WEBMONEY_WMZ . '<br />Кошелёк WMR: ' . MODULE_PAYMENT_WEBMONEY_WMR . '<br /><br />' . 'Ваш заказ будет выполнен только после получения оплаты!');
define('MODULE_PAYMENT_WEBMONEY_TEXT_EMAIL_FOOTER', "Информация для оплаты:\n\nНаш WM идентификатор: ". MODULE_PAYMENT_WEBMONEY_WMID . "\n\nКошелёк WMZ: ". MODULE_PAYMENT_WEBMONEY_WMZ . "\n\nКошелёк WMR: ". MODULE_PAYMENT_WEBMONEY_WMR . "\n\n" . 'Ваш заказ будет выполнен только после получения оплаты!');
define('MODULE_PAYMENT_WEBMONEY_TEXT_INFO','');
define('MODULE_PAYMENT_WEBMONEY_STATUS_TITLE' , 'Разрешить модуль WebMoney');
define('MODULE_PAYMENT_WEBMONEY_STATUS_DESC' , 'Вы хотите разрешить использование модуля при оформлении заказов?');
define('MODULE_PAYMENT_WEBMONEY_ALLOWED_TITLE' , 'Разрешённые страны');
define('MODULE_PAYMENT_WEBMONEY_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_PAYMENT_WEBMONEY_WMID_TITLE' , 'WM ID:');
define('MODULE_PAYMENT_WEBMONEY_WMID_DESC' , 'Укажите Ваш WM ID');
define('MODULE_PAYMENT_WEBMONEY_WMZ_TITLE' , 'Ваш WMZ кошелёк:');
define('MODULE_PAYMENT_WEBMONEY_WMZ_DESC' , 'Укажите номер Вашего WMZ кошелька');
define('MODULE_PAYMENT_WEBMONEY_WMR_TITLE' , 'Ваш WMR кошелёк:');
define('MODULE_PAYMENT_WEBMONEY_WMR_DESC' , 'Укажите номер Вашего WMR кошелька');
define('MODULE_PAYMENT_WEBMONEY_SORT_ORDER_TITLE' , 'Порядок сортировки');
define('MODULE_PAYMENT_WEBMONEY_SORT_ORDER_DESC' , 'Порядок сортировки модуля.');
define('MODULE_PAYMENT_WEBMONEY_ZONE_TITLE' , 'Зона');
define('MODULE_PAYMENT_WEBMONEY_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');
define('MODULE_PAYMENT_WEBMONEY_ORDER_STATUS_ID_TITLE' , 'Статус заказа');
define('MODULE_PAYMENT_WEBMONEY_ORDER_STATUS_ID_DESC' , 'Заказы, оформленные с использованием данного модуля оплаты будут принимать указанный статус.');
?>