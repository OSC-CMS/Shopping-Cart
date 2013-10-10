<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('HEADING_TITLE', 'Группы клиентов');

define('ENTRY_CUSTOMERS_FSK18','Блокировать покупку товаров FSK18?');
define('ENTRY_CUSTOMERS_FSK18_DISPLAY','Показывать товары FSK18?');
define('ENTRY_CUSTOMERS_STATUS_ADD_TAX','Показывать налог на странице подтверждения заказа');
define('ENTRY_CUSTOMERS_STATUS_MIN_ORDER','Минимальная сумма заказа:');
define('ENTRY_CUSTOMERS_STATUS_MAX_ORDER','Максимальная сумма заказа:');
define('ENTRY_CUSTOMERS_STATUS_BT_PERMISSION','Оплата через банковский перевод');
define('ENTRY_CUSTOMERS_STATUS_CC_PERMISSION','Оплата кредиткой');
define('ENTRY_CUSTOMERS_STATUS_COD_PERMISSION','Оплата наличными');
define('ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES','Скидка');
define('ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED','Укажите неразрешенные методы оплаты');
define('ENTRY_CUSTOMERS_STATUS_PUBLIC','Показывать в магазине бокс инфо (группа станет открытая)<br />');
define('ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED','Укажите неразрешенные методы доставки');
define('ENTRY_CUSTOMERS_STATUS_SHOW_PRICE','Цена');
define('ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX','Цена включая налоги');
define('ENTRY_CUSTOMERS_STATUS_WRITE_REVIEWS','Разрешить этой группе клиентов писать отзывы?');
define('ENTRY_CUSTOMERS_STATUS_READ_REVIEWS','Разрешить этой группе клиентов читать отзывы?');
define('ENTRY_CUSTOMERS_STATUS_READ_REVIEWS_DISPLAY','Разрешить этой группе клиентов читать отзывы?');
define('ENTRY_GRADUATED_PRICES','Цены от количества');
define('ENTRY_NO','Нет');
define('ENTRY_OT_XMEMBER', 'Скидка от общей стоимости заказа?');
define('ENTRY_YES','Да');

define('ERROR_REMOVE_DEFAULT_CUSTOMER_STATUS', 'Группа по умолчанию не может быть удалена. Установите другую группу по умолчанию и попробуйте снова.');
define('ERROR_REMOVE_DEFAULT_CUSTOMERS_STATUS','Вы не можете удалить стандартные группы');
define('ERROR_STATUS_USED_IN_CUSTOMERS', 'Эта группа активна и в ней есть клиенты.');
define('ERROR_STATUS_USED_IN_HISTORY', 'Эта группа используется в истории заказов.');

define('TABLE_HEADING_ACTION','Действие');
define('TABLE_HEADING_CUSTOMERS_GRADUATED','Цена от количества');
define('TABLE_HEADING_CUSTOMERS_STATUS','Группа');
define('TABLE_HEADING_CUSTOMERS_UNALLOW','Запрещённые модули оплаты');
define('TABLE_HEADING_CUSTOMERS_UNALLOW_SHIPPING','Запрещённые модули доставки');
define('TABLE_HEADING_DISCOUNT','Скидка');
define('TABLE_HEADING_TAX_PRICE','Показывать цены / включая налог');

define('TAX_NO','нет');
define('TAX_YES','да');

define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS_STATUS', 'Существующие группы клиентов:');

define('TEXT_INFO_CUSTOMERS_FSK18_DISPLAY_INTRO','FSK18 товары');
define('TEXT_INFO_CUSTOMERS_FSK18_INTRO','FSK18 Заблокирован');
define('TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO','Если цена включает налог, установите налог в = "нет"');
define('TEXT_INFO_CUSTOMERS_STATUS_MIN_ORDER_INTRO','Определите минимальную сумму заказа или оставьте поле пустым.');
define('TEXT_INFO_CUSTOMERS_STATUS_MAX_ORDER_INTRO','Определите максимальную сумму заказа или оставьте поле пустым.');
define('TEXT_INFO_CUSTOMERS_STATUS_BT_PERMISSION_INTRO', 'Разрешить покупателям этой группы оплату через банковский перевод?');
define('TEXT_INFO_CUSTOMERS_STATUS_CC_PERMISSION_INTRO', 'Разрешить покупателям этой группы оплату кредитными картами?');
define('TEXT_INFO_CUSTOMERS_STATUS_COD_PERMISSION_INTRO', 'Разрешить покупателям этой группы оплату наличными?');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO','Скидка для атрибутов товара (Макс. % скидки на единицу товара)');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO','Скидка от общей стоимости заказа');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE', 'Скидка (от 0 до 100%):');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO', 'Укажите скидку от 0 до 100%, которая будет применена к каждому товару.');
define('TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO','Цены от количества');
define('TEXT_INFO_CUSTOMERS_STATUS_IMAGE','Картинка группы');
define('TEXT_INFO_CUSTOMERS_STATUS_NAME','Название группы');
define('TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO','Неразрешенные методы оплаты');
define('TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO','Показывать инфо в магазине?');
define('TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO','Неразрешенные методы доставки');
define('TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO','Показывать цены в магазине');
define('TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO','Показывать цены с налогом или без');
define('TEXT_INFO_CUSTOMERS_STATUS_WRITE_REVIEWS_INTRO','Написание отзывов');
define('TEXT_INFO_CUSTOMERS_STATUS_READ_REVIEWS_INTRO', 'Чтение отзывов');

define('TEXT_INFO_DELETE_INTRO', 'Вы действительно хотите удалить эту группу?');
define('TEXT_INFO_EDIT_INTRO', 'Пожалуйста, внесите необходимые изменения');
define('TEXT_INFO_INSERT_INTRO', 'Создайте новую группу.');

define('TEXT_INFO_HEADING_DELETE_CUSTOMERS_STATUS', 'Удаление группы');
define('TEXT_INFO_HEADING_EDIT_CUSTOMERS_STATUS','Редактирование группы');
define('TEXT_INFO_HEADING_NEW_CUSTOMERS_STATUS', 'Новая группа');

define('TEXT_INFO_CUSTOMERS_STATUS_BASE', 'Цена для группы');
define('ENTRY_CUSTOMERS_STATUS_BASE', 'Какая цена будет показываться данной группе (Посетитель, Покупатель или Оптовый покупатель). Если выбран Админ, то цены не показываются.');


define('TEXT_PUBLIC',', открытая');
define('TABLE_HEADING_ICON','Картинка');
define('TABLE_HEADING_USERS','Клиентов');

define('TEXT_INFO_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_INTRO', 'Накопительный предел');
define('ENTRY_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_DISPLAY','Общая сумма заказов, достигнув которую, покупатель попадает в данную группу');

define('TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_INTRO', 'Накопительные статусы:');
define('TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_DISPLAY', 'Какие именно заказы будут учитываться при подсчёте общей суммы покупок покупателя');

?>