<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

  define('MODULE_PAYMENT_KVITANCIA_TEXT_TITLE', 'Kvitancia SB RF');
  define('MODULE_PAYMENT_KVITANCIA_TEXT_DESCRIPTION', '<br /><strong>Kvitanciy dlia oplaty vy mogete raspechtat na sleduiyschei stranice.</strong><br /><br />Informacia dlia oplaty:<br />' .
                                                         '<br />Nazvanie banka: ' . MODULE_PAYMENT_KVITANCIA_1 .
                                                         '<br />Raschetny schet: ' . MODULE_PAYMENT_KVITANCIA_2 .
                                                         '<br />BIK: ' . MODULE_PAYMENT_KVITANCIA_3 .
                                                         '<br />Kor./schet: ' . MODULE_PAYMENT_KVITANCIA_4 .
                                                         '<br />INN: ' . MODULE_PAYMENT_KVITANCIA_5 .
                                                         '<br />Poluchatel: ' . MODULE_PAYMENT_KVITANCIA_6 .
                                                         '<br />KPP: ' . MODULE_PAYMENT_KVITANCIA_7 .
                                                         '<br /><br />Vash zakaz budet vypolnen tolko posle poluchenia oplaty.<br />');
  define('MODULE_PAYMENT_KVITANCIA_TEXT_EMAIL_FOOTER', str_replace('<br />','\n',MODULE_PAYMENT_KVITANCIA_TEXT_DESCRIPTION));

  define('MODULE_PAYMENT_KVITANCIA_STATUS_TITLE','Razreshit modul Kvitancia SB RF');
  define('MODULE_PAYMENT_KVITANCIA_STATUS_DESC','Razreshit ispolzovanie modulia Kvitancia SB RF?');

  define('MODULE_PAYMENT_KVITANCIA_TEXT_INFO','');

  define('MODULE_PAYMENT_KVITANCIA_1_TITLE','Nazvanie banka');
  define('MODULE_PAYMENT_KVITANCIA_1_DESC','Укажите название банка.');

  define('MODULE_PAYMENT_KVITANCIA_2_TITLE','Расчётный счёт');
  define('MODULE_PAYMENT_KVITANCIA_2_DESC','Укажите Ваш расчетный счет.');

  define('MODULE_PAYMENT_KVITANCIA_3_TITLE','БИК');
  define('MODULE_PAYMENT_KVITANCIA_3_DESC','Укажите БИК.');

  define('MODULE_PAYMENT_KVITANCIA_4_TITLE','Кор./счет');
  define('MODULE_PAYMENT_KVITANCIA_4_DESC','Укажите Кор./счет.');

  define('MODULE_PAYMENT_KVITANCIA_5_TITLE','ИНН');
  define('MODULE_PAYMENT_KVITANCIA_5_DESC','Укажите ИНН.');

  define('MODULE_PAYMENT_KVITANCIA_6_TITLE','Получатель');
  define('MODULE_PAYMENT_KVITANCIA_6_DESC','Укажите получателя платежа.');

  define('MODULE_PAYMENT_KVITANCIA_7_TITLE','КПП');
  define('MODULE_PAYMENT_KVITANCIA_7_DESC','Укажите КПП.');

  define('MODULE_PAYMENT_KVITANCIA_8_TITLE','Назначение платежа');
  define('MODULE_PAYMENT_KVITANCIA_8_DESC','Укажите название платежа.');

  define('MODULE_PAYMENT_KVITANCIA_SORT_ORDER_TITLE','Порядок сортировки');
  define('MODULE_PAYMENT_KVITANCIA_SORT_ORDER_DESC','Укажите порядок сортировки модуля.');

  define('MODULE_PAYMENT_KVITANCIA_ALLOWED_TITLE' , 'Разрешённые страны');
  define('MODULE_PAYMENT_KVITANCIA_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');

  define('MODULE_PAYMENT_KVITANCIA_ZONE_TITLE' , 'Зона');
  define('MODULE_PAYMENT_KVITANCIA_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');

  define('MODULE_PAYMENT_KVITANCIA_ORDER_STATUS_ID_TITLE' , 'Статус заказа');
  define('MODULE_PAYMENT_KVITANCIA_ORDER_STATUS_ID_DESC' , 'Заказы, оформленные с использованием данного модуля оплаты будут принимать указанный статус.');

define('MODULE_PAYMENT_KVITANCIA_NAME_TITLE','Info');
define('MODULE_PAYMENT_KVITANCIA_NAME_DESC','');
define('MODULE_PAYMENT_KVITANCIA_NAME','Name:');
define('MODULE_PAYMENT_KVITANCIA_ADDRESS','Address:');
define('MODULE_PAYMENT_KVITANCIA_ADDRESS_HELP','');

?>