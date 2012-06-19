<?php
/*
#####################################
# OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

define('MODULE_SHIPPING_TABLE_TEXT_TITLE', 'Табличный тариф');
define('MODULE_SHIPPING_TABLE_TEXT_DESCRIPTION', 'Табличный тариф');
define('MODULE_SHIPPING_TABLE_TEXT_WAY', 'Расчёт доставки по таблице');
define('MODULE_SHIPPING_TABLE_TEXT_WEIGHT', 'Вес');
define('MODULE_SHIPPING_TABLE_TEXT_AMOUNT', 'Сумма');

define('MODULE_SHIPPING_TABLE_STATUS_TITLE' , 'Разрешить модуль Табличный тариф');
define('MODULE_SHIPPING_TABLE_STATUS_DESC' , 'Вы хотите разрешить модуль доставки Табличный тариф?');
define('MODULE_SHIPPING_TABLE_ALLOWED_TITLE' , 'Разрешённые страны');
define('MODULE_SHIPPING_TABLE_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_SHIPPING_TABLE_COST_TITLE' , 'Таблица тарифов');
define('MODULE_SHIPPING_TABLE_COST_DESC' , 'Стоимость доставки рассчитывается на основе общего веса заказа или общей стоимости заказа. Например: 25:8.50,50:5.50,и т.д... Это значит, что до 25 доставка будет стоить 8.50, от 25 до 50 будет стоить 5.50 и т.д.');
define('MODULE_SHIPPING_TABLE_MODE_TITLE' , 'Метод расчёта');
define('MODULE_SHIPPING_TABLE_MODE_DESC' , 'Стоимость расчёта доставки исходя из общего веса заказа (weight) или исходя из общей стоимости заказа (price).');
define('MODULE_SHIPPING_TABLE_HANDLING_TITLE' , 'Стоимость использования данного модуля');
define('MODULE_SHIPPING_TABLE_HANDLING_DESC' , 'Стоимость использования данного способа доставки.');
define('MODULE_SHIPPING_TABLE_TAX_CLASS_TITLE' , 'Налог');
define('MODULE_SHIPPING_TABLE_TAX_CLASS_DESC' , 'Использовать налог.');
define('MODULE_SHIPPING_TABLE_ZONE_TITLE' , 'Зона');
define('MODULE_SHIPPING_TABLE_ZONE_DESC' , 'Если выбрана зона, то данный модуль доставки будет виден только покупателям из выбранной зоны.');
define('MODULE_SHIPPING_TABLE_SORT_ORDER_TITLE' , 'Порядок сортировки');
define('MODULE_SHIPPING_TABLE_SORT_ORDER_DESC' , 'Порядок сортировки модуля.');
?>