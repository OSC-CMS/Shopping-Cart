<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('MODULE_PAYMENT_YANDEX_TEXT_TITLE', 'Яндекс-Деньги');
define('MODULE_PAYMENT_YANDEX_TEXT_DESCRIPTION', 'Информация для оплаты:<br /><br />Номер Яндекс-кошелька: ' . MODULE_PAYMENT_YANDEX_ID . '<br /><br />' . 'Ваш заказ будет выполнен только после получения оплаты!');
define('MODULE_PAYMENT_YANDEX_TEXT_EMAIL_FOOTER', "Информация для оплаты:\n\nНомер нашего Яндекс-кошелька: ". MODULE_PAYMENT_YANDEX_ID . "\n\n" . 'Ваш заказ будет выполнен только после получения оплаты!');
define('MODULE_PAYMENT_YANDEX_TEXT_INFO','');
define('MODULE_PAYMENT_YANDEX_STATUS_TITLE' , 'Разрешить модуль Яндекс-Деньги');
define('MODULE_PAYMENT_YANDEX_STATUS_DESC' , 'Вы хотите разрешить использование модуля при оформлении заказов?');
define('MODULE_PAYMENT_YANDEX_ALLOWED_TITLE' , 'Разрешённые страны');
define('MODULE_PAYMENT_YANDEX_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_PAYMENT_YANDEX_ID_TITLE' , 'Номер кошелька:');
define('MODULE_PAYMENT_YANDEX_ID_DESC' , 'Укажите Ваш номер в Яндекс-деньгах');
define('MODULE_PAYMENT_YANDEX_SORT_ORDER_TITLE' , 'Порядок сортировки');
define('MODULE_PAYMENT_YANDEX_SORT_ORDER_DESC' , 'Порядок сортировки модуля.');
define('MODULE_PAYMENT_YANDEX_ZONE_TITLE' , 'Зона');
define('MODULE_PAYMENT_YANDEX_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');
define('MODULE_PAYMENT_YANDEX_ORDER_STATUS_ID_TITLE' , 'Статус заказа');
define('MODULE_PAYMENT_YANDEX_ORDER_STATUS_ID_DESC' , 'Заказы, оформленные с использованием данного модуля оплаты будут принимать указанный статус.');
?>