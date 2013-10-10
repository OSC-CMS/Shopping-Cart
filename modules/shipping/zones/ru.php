<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('NUMBER_OF_ZONES',11);

define('MODULE_SHIPPING_ZONES_TEXT_TITLE', 'Тарифы для зоны');
define('MODULE_SHIPPING_ZONES_TEXT_DESCRIPTION', 'зональный базовый тариф');
define('MODULE_SHIPPING_ZONES_TEXT_WAY', 'Доставка до');
define('MODULE_SHIPPING_ZONES_TEXT_UNITS', 'Кг.');
define('MODULE_SHIPPING_ZONES_INVALID_ZONE', 'Для выбранной страны нет возможности доставки ');
define('MODULE_SHIPPING_ZONES_UNDEFINED_RATE', 'Стоимость пересылки сейчас не может быть определена ');

define('MODULE_SHIPPING_ZONES_STATUS_TITLE' , 'Разрешить модуль тарифы для зоны');
define('MODULE_SHIPPING_ZONES_STATUS_DESC' , 'Вы хотите разрешить модуль тарифы для зоны?');
define('MODULE_SHIPPING_ZONES_ALLOWED_TITLE' , 'Разрешённые страны');
define('MODULE_SHIPPING_ZONES_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_SHIPPING_ZONES_TAX_CLASS_TITLE' , 'Налог');
define('MODULE_SHIPPING_ZONES_TAX_CLASS_DESC' , 'Использовать налог.');
define('MODULE_SHIPPING_ZONES_SORT_ORDER_TITLE' , 'Порядок сортировки');
define('MODULE_SHIPPING_ZONES_SORT_ORDER_DESC' , 'Порядок сортировки модуля.');

for ($ii=0;$ii<NUMBER_OF_ZONES;$ii++) {
define('MODULE_SHIPPING_ZONES_COUNTRIES_'.$ii.'_TITLE' , 'Страны зоны '.$ii.'');
define('MODULE_SHIPPING_ZONES_COUNTRIES_'.$ii.'_DESC' , 'Список стран через запятую для зоны '.$ii.'.');
define('MODULE_SHIPPING_ZONES_COST_'.$ii.'_TITLE' , 'Стоимость доставки для '.$ii.' зоны');
define('MODULE_SHIPPING_ZONES_COST_'.$ii.'_DESC' , 'Стоимость доставки для зоны '.$ii.' на базе максимальной стоимость заказа. Например: 3:8.50,7:10.50,... Это значит, что стоимость доставки для заказов, весом до 3 кг. будет стоить 8.50 для покупателей из стран '.$ii.' зоны.');
define('MODULE_SHIPPING_ZONES_HANDLING_'.$ii.'_TITLE' , 'Стоимость использования модуля для '.$ii.' зоны');
define('MODULE_SHIPPING_ZONES_HANDLING_'.$ii.'_DESC' , 'Стоимость использования данного способа доставки.');
}
?>