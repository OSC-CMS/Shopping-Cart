<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('MODULE_PAYMENT_YANDEX_MERCHANT_TEXT_TITLE', 'Яндекс.Деньги');
define('MODULE_PAYMENT_YANDEX_MERCHANT_TEXT_DESCRIPTION', 'Яндекс.Деньги (Оплата с кошелька, карточками Visa, MasterCard, Maestro, наличными в терминалах)');
define('MODULE_PAYMENT_YANDEX_MERCHANT_STATUS_TITLE', 'Разрешить модуль Яндекс.Деньги');
define('MODULE_PAYMENT_YANDEX_MERCHANT_STATUS_DESC', 'Вы хотите разрешить использование модуля при оформлении заказов?');
define('MODULE_PAYMENT_YANDEX_MERCHANT_ZONE_TITLE', 'Зона');
define('MODULE_PAYMENT_YANDEX_MERCHANT_ZONE_DESC', 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');
define('MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID_TITLE', 'Статус оплаченного заказа');
define('MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID_DESC', 'Статус, устанавливаемый заказу после успешной оплаты');
define('MODULE_PAYMENT_YANDEX_MERCHANT_SORT_ORDER_TITLE', 'Порядок сортировки');
define('MODULE_PAYMENT_YANDEX_MERCHANT_SORT_ORDER_DESC', 'Порядок сортировки модуля.');
define('MODULE_PAYMENT_YANDEX_MERCHANT_ALLOWED_TITLE', 'Разрешённые страны');
define('MODULE_PAYMENT_YANDEX_MERCHANT_ALLOWED_DESC', 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID_TITLE' , 'ShopID:');
define('MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID_DESC' , 'Идентификатор Контрагента');
define('MODULE_PAYMENT_YANDEX_MERCHANT_SCID_TITLE' , 'scid:');
define('MODULE_PAYMENT_YANDEX_MERCHANT_SCID_DESC' , 'Номер витрины Контрагента');
define('MODULE_PAYMENT_YANDEX_MERCHANT_KEY_TITLE' , 'Секретный ключ:');
define('MODULE_PAYMENT_YANDEX_MERCHANT_KEY_DESC' , 'Секретный ключ который указан в анкете');
define('MODULE_PAYMENT_YANDEX_MERCHANT_TEST_TITLE', 'Тестирование');
define('MODULE_PAYMENT_YANDEX_MERCHANT_TEST_DESC', 'Включить режим отладки');
define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_TITLE','Способ оплаты');
define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_DESC','Укажите через запятую варианты оплаты для выбора покупателем:<br />
PC - Оплата со счета Яндекс.Денег.<br />
AC - Оплата с произвольной банковской карты.<br />
MC - Платеж со счета мобильного телефона.<br />
GP - Оплата наличными через кассы и терминалы.<br />
WM - Оплата с кошелька в системе WebMoney.<br />
SB - Оплата через Сбербанк Онлайн.');

define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_AC', 'Оплата с банковской карты');
define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_PC', 'Оплата со счета Яндекс.Денег');
define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_MC', 'Платеж со счета мобильного телефона');
define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_GP', 'Оплата наличными через кассы и терминалы');
define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_WM', 'Оплата с кошелька в системе WebMoney');
define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_SB', 'Оплата через Сбербанк Онлайн');

define('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_SELECT', 'Выберите метод оплаты');