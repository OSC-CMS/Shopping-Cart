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

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

  define('MODULE_PAYMENT_LIQPAY_TEXT_TITLE', 'LiqPAY');
  define('MODULE_PAYMENT_LIQPAY_TEXT_PUBLIC_TITLE', 'LiqPAY');
  define('MODULE_PAYMENT_LIQPAY_TEXT_ADMIN_DESCRIPTION', 'Модуль оплаты LiqPAY');
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
  
define('MODULE_PAYMENT_LIQPAY_HELP', '  
  Настройка модуля оплаты LiqPay 

Как настроить LiqPAY.

У Вас должен быть зарегистрирован магазин в системе LiqPAY и у Вас должен быть ID номер магазина и пароль (ключ), которые можно посмотреть на сайте LiqPAY в разделе API, вобщем, там где подключаются магазины.

Настройка модуля оплаты в магазине:

1. В Админке - Модули - Оплата устанавливайте модуль LiqPAY.
2. Указываете свой ID номер магазина.
3. В поле Мерчант пароль (подпись) указываете свой пароль (ключ), указанный на сайте LiqPAY в Ваших настройках.

index.php?modules_page=liqpay_process&modules_type=payment&modules_name=liqpay
');
?>