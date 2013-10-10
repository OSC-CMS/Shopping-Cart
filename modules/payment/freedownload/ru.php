<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('MODULE_PAYMENT_TYPE_PERMISSION', 'freedownload');
define('MODULE_PAYMENT_FREEDOWNLOAD_TEXT_TITLE', 'Бесплатная загрузка');
define('MODULE_PAYMENT_FREEDOWNLOAD_TEXT_DESCRIPTION', 'Бесплатная загрузка');
define('MODULE_PAYMENT_FREEDOWNLOAD_TEXT_INFO','');
define('MODULE_PAYMENT_FREEDOWNLOAD_ZONE_TITLE' , 'Зона');
define('MODULE_PAYMENT_FREEDOWNLOAD_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');
define('MODULE_PAYMENT_FREEDOWNLOAD_ALLOWED_TITLE' , 'Разрешённые страны');
define('MODULE_PAYMENT_FREEDOWNLOAD_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_PAYMENT_FREEDOWNLOAD_STATUS_TITLE' , 'Разрешить модуль бесплатная загрузка');
define('MODULE_PAYMENT_FREEDOWNLOAD_STATUS_DESC' , 'Вы хотите разрешить использование модуля при оформлении заказов? Данный модуль будет виден на странице выбора способа оплаты только если в корзине находится виртуальный товар (т.е. к товару прикреплён файл) и стоимость товара равна 0.');
define('MODULE_PAYMENT_FREEDOWNLOAD_SORT_ORDER_TITLE' , 'Порядок сортировки');
define('MODULE_PAYMENT_FREEDOWNLOAD_SORT_ORDER_DESC' , 'Порядок сортировки модуля.');
define('MODULE_PAYMENT_FREEDOWNLOAD_ORDER_STATUS_ID_TITLE' , 'Статус заказа');
define('MODULE_PAYMENT_FREEDOWNLOAD_ORDER_STATUS_ID_DESC' , 'Заказы, оформленные с использованием данного модуля оплаты будут принимать указанный статус.');
?>