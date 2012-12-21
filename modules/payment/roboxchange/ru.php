<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

  define('MODULE_PAYMENT_ROBOXCHANGE_TEXT_TITLE', 'Оплата "электронными деньгами" (WebMoney, Яндекс.Деньги, E-gold, MoneyMail, RuPay, INOCard)');
  define('MODULE_PAYMENT_ROBOXCHANGE_TEXT_DESCRIPTION', 'Roboxchange (WebMoney, Яндекс.Деньги, E-gold, MoneyMail, RuPay, INOCard)');
  
  define('MODULE_PAYMENT_ROBOXCHANGE_STATUS_TITLE','Разрешить модуль roboxchange');
  define('MODULE_PAYMENT_ROBOXCHANGE_STATUS_DESC','Установка<br />
1) Зарегистрируйтесь на сайте http://roboxchange.net как продавец, указав валюту, которая эквивалентна валюте по умолючанию Вашего магазина, затем отправьте заявку администрации на активацию Вашего логина, только после активации Вы можете пользоваться сервисом roboXchange.net.<br />
2) На сайте http://roboxchange.net в разделе "Администрирование" укажите:<br />
"пароль #1" любой<br />
"пароль #2" любой<br />
"Result URL" http://ваш-сайт/process.php?payment=roboxchange<br />
"метод отсылки в Result URL" POST<br />
"Success URL" http://ваш-сайт/checkout_process.php<br />
"метод отсылки в Success URL" POST<br />
"Fail URL" http://ваш-сайт/checkout_payment.php<br />
"метод отсылки в Fail URL" POST<br />
3) Укажите в Админке - Модули - Оплата, в настройках модуля: логин для входа в http://roboxchange.net и пароли 1 и 2.<br /><br />Разрешить использование модуля roboxchange.');
  define('MODULE_PAYMENT_ROBOXCHANGE_ALLOWED_TITLE' , 'Разрешённые страны');
  define('MODULE_PAYMENT_ROBOXCHANGE_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');
  define('MODULE_PAYMENT_ROBOXCHANGE_LOGIN_TITLE','Ваш логин');
  define('MODULE_PAYMENT_ROBOXCHANGE_LOGIN_DESC','Ваш логин в системе roboxchange cash register');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1_TITLE','Пароль номер 1');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1_DESC','Ваш первый пароль в roboxchange cash register');
  define('MODULE_PAYMENT_ROBOXCHANGE_SORT_ORDER_TITLE','Порядок сортировки');
  define('MODULE_PAYMENT_ROBOXCHANGE_SORT_ORDER_DESC','Порядок сортировки модуля.');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD2_TITLE','Пароль номер 2');
  define('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD2_DESC','Ваш второй пароль в roboxchange cash register');
  define('MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS_ID_TITLE','Статус оплаченного заказа');
  define('MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS_ID_DESC','Статус, устанавливаемый заказу после успешной оплаты');
  define('MODULE_PAYMENT_ROBOXCHANGE_ZONE_TITLE' , 'Зона');
  define('MODULE_PAYMENT_ROBOXCHANGE_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');

  define('MODULE_PAYMENT_ROBOXCHANGE_TEST_TITLE','Режим работы');
  define('MODULE_PAYMENT_ROBOXCHANGE_TEST_DESC','test - для тестирования работы модуля, production - для полноценного приёма оплаты.');
  
?>