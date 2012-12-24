<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

define('MODULE_PAYMENT_LIQPAY_TEXT_TITLE', 'LiqPAY');
define('MODULE_PAYMENT_LIQPAY_TEXT_PUBLIC_TITLE', 'LiqPAY');
define('MODULE_PAYMENT_LIQPAY_TEXT_ADMIN_DESCRIPTION', 'Модуль оплаты LiqPAY<br /><br /><b>Как правильно настроить модули: <a href="http://osc-cms.com/docs" target="_blank"><u>здесь</u></a>.</b>');
define('MODULE_PAYMENT_LIQPAY_TEXT_DESCRIPTION', 'После нажатия кнопки Подтвердить заказ Вы перейдёте на сайт платёжной системы для оплаты заказа, после оплаты Ваш заказ будет выполнен.');

define('MODULE_PAYMENT_LIQPAY_STATUS_TITLE' , 'Разрешить модуль LiqPAY');
define('MODULE_PAYMENT_LIQPAY_STATUS_DESC' , 'Вы хотите разрешить использование модуля при оформлении заказов?');
define('MODULE_PAYMENT_LIQPAY_ALLOWED_TITLE' , 'Разрешённые страны');
define('MODULE_PAYMENT_LIQPAY_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_PAYMENT_LIQPAY_ID_TITLE' , 'Мерчант ID');
define('MODULE_PAYMENT_LIQPAY_ID_DESC' , 'Укажите Ваш идентификационныый номер (мерчант id).');
define('MODULE_PAYMENT_LIQPAY_SORT_ORDER_TITLE' , 'Порядок сортировки');
define('MODULE_PAYMENT_LIQPAY_SORT_ORDER_DESC' , 'Порядок сортировки модуля.');
define('MODULE_PAYMENT_LIQPAY_ZONE_TITLE' , 'Зона');
define('MODULE_PAYMENT_LIQPAY_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');
define('MODULE_PAYMENT_LIQPAY_SECRET_KEY_TITLE' , 'Мерчант пароль (подпись)');
define('MODULE_PAYMENT_LIQPAY_SECRET_KEY_DESC' , 'В данной опции укажите пароль (подпись), указанный в настройках на сайте LiqPAY.');
define('MODULE_PAYMENT_LIQPAY_ORDER_STATUS_ID_TITLE' , 'Укажите оплаченный статус заказа');
define('MODULE_PAYMENT_LIQPAY_ORDER_STATUS_ID_DESC' , 'Укажите оплаченный статус заказа.');
?>