<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.1
#####################################
*/

  define('MODULE_PAYMENT_RBKMONEY_TEXT_TITLE', 'RBK Money');
  define('MODULE_PAYMENT_RBKMONEY_TEXT_PUBLIC_TITLE', 'RBK Money');
  define('MODULE_PAYMENT_RBKMONEY_TEXT_ADMIN_DESCRIPTION', 'Модуль оплаты RBK Money<br />Как правильно настроить модуль читайте.');
  define('MODULE_PAYMENT_RBKMONEY_TEXT_DESCRIPTION', 'После нажатия кнопки Подтвердить заказ Вы перейдёте на сайт платёжной системы для оплаты заказа, после оплаты Ваш заказ будет выполнен.');
  
define('MODULE_PAYMENT_RBKMONEY_STATUS_TITLE' , 'Разрешить модуль RBK Money');
define('MODULE_PAYMENT_RBKMONEY_STATUS_DESC' , 'Вы хотите разрешить использование модуля при оформлении заказов?');
define('MODULE_PAYMENT_RBKMONEY_ALLOWED_TITLE' , 'Разрешённые страны');
define('MODULE_PAYMENT_RBKMONEY_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
define('MODULE_PAYMENT_RBKMONEY_SHOP_ID_TITLE' , 'ID сайта:');
define('MODULE_PAYMENT_RBKMONEY_SHOP_ID_DESC' , 'Укажите номер (ID магазина) Вашего магазина в RBK Money.');
define('MODULE_PAYMENT_RBKMONEY_SORT_ORDER_TITLE' , 'Порядок сортировки');
define('MODULE_PAYMENT_RBKMONEY_SORT_ORDER_DESC' , 'Порядок сортировки модуля.');
define('MODULE_PAYMENT_RBKMONEY_ZONE_TITLE' , 'Зона');
define('MODULE_PAYMENT_RBKMONEY_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');
define('MODULE_PAYMENT_RBKMONEY_SECRET_KEY_TITLE' , 'Секретное слово');
define('MODULE_PAYMENT_RBKMONEY_SECRET_KEY_DESC' , 'В данной опции укажите Ваше секретное слово, указанное в опции Секретное на сайте RBK Money.');
define('MODULE_PAYMENT_RBKMONEY_ORDER_STATUS_ID_TITLE' , 'Укажите оплаченный статус заказа');
define('MODULE_PAYMENT_RBKMONEY_ORDER_STATUS_ID_DESC' , 'Укажите оплаченный статус заказа.');
define('MODULE_PAYMENT_RBKMONEY_HELP',  
  'Как настроить RBK Money.
1. У Вас должен быть зарегистрирован магазин в системе RBK Money и у Вас должен быть ID номер магазина.
2. На сайте RBK Money в настройках магазина нужно заполнить пару опций в форме:
Оповещение о платеже: - http://ваш-магазин.ру/rbkmoney.php
Секретное слово - Укажите любой набор букв, цифр и запишите, значение опции Секретное слово нужно будет указать в Админки - Модули - Оплата - rbkmoney.
3. Заполняйте оставшиеся поля и нажимайте Сохранить.

Настройка модуля оплаты в магазине:

1. В Админке - Модули - Оплата устанавливайте модуль rbkmoney.
2. Указываете свой ID номер магазина.
3. В поле Секретное слово указываете своё секретное слово, указанное в настройках магазина на сайте RBK Money.

Всё, модуль должен работать.');
?>