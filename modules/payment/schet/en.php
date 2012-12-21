<?php
/*
#####################################
# OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

  define('MODULE_PAYMENT_SCHET_TEXT_TITLE', 'Oplata po schetu');
  define('MODULE_PAYMENT_SCHET_TEXT_DESCRIPTION', '<br /><strong>Счёт Вы сможете распечатать на следующей странице.</strong><br /><br />Информация для оплаты:<br />' .
                                                         '<br />Поставщик: ' . MODULE_PAYMENT_SCHET_1 .
                                                         '<br />Адрес: ' . MODULE_PAYMENT_SCHET_2 .
                                                         '<br />Телефон: ' . MODULE_PAYMENT_SCHET_3 .
                                                         '<br />Факс: ' . MODULE_PAYMENT_SCHET_4 .
                                                         '<br />Р/c: ' . MODULE_PAYMENT_SCHET_5 .
                                                         '<br />Название банка: ' . MODULE_PAYMENT_SCHET_6 .
                                                         '<br />К/c: ' . MODULE_PAYMENT_SCHET_7 .
                                                         '<br />БИК: ' . MODULE_PAYMENT_SCHET_8 .
                                                         '<br />ИНН: ' . MODULE_PAYMENT_SCHET_9 .
                                                         '<br />КПП: ' . MODULE_PAYMENT_SCHET_10 .
                                                         '<br />ОГРН: ' . MODULE_PAYMENT_SCHET_11 .
                                                         '<br />ОКПО: ' . MODULE_PAYMENT_SCHET_12 .
                                                         '<br /><br />Ваш заказ будет выполнен только после получения оплаты.<br />');
  define('MODULE_PAYMENT_SCHET_TEXT_EMAIL_FOOTER', str_replace('<br />','\n',MODULE_PAYMENT_SCHET_TEXT_DESCRIPTION));

  define('MODULE_PAYMENT_SCHET_STATUS_TITLE','Разрешить модуль Оплата по счёту');
  define('MODULE_PAYMENT_SCHET_STATUS_DESC','Разрешить использование модуля Оплата по счёту при оформлении заказа в магазине?');

  define('MODULE_PAYMENT_SCHET_TEXT_INFO','');

  define('MODULE_PAYMENT_SCHET_1_TITLE','Поставщик');
  define('MODULE_PAYMENT_SCHET_1_DESC','Укажите название организации.');

  define('MODULE_PAYMENT_SCHET_2_TITLE','Адрес');
  define('MODULE_PAYMENT_SCHET_2_DESC','Укажите адрес организации.');

  define('MODULE_PAYMENT_SCHET_3_TITLE','Телефон');
  define('MODULE_PAYMENT_SCHET_3_DESC','Укажите телефон.');

  define('MODULE_PAYMENT_SCHET_4_TITLE','Факс');
  define('MODULE_PAYMENT_SCHET_4_DESC','Укажите факс.');

  define('MODULE_PAYMENT_SCHET_5_TITLE','Р/с');
  define('MODULE_PAYMENT_SCHET_5_DESC','Укажите р/с.');

  define('MODULE_PAYMENT_SCHET_6_TITLE','Название банка');
  define('MODULE_PAYMENT_SCHET_6_DESC','Укажите название банка.');

  define('MODULE_PAYMENT_SCHET_7_TITLE','К/c');
  define('MODULE_PAYMENT_SCHET_7_DESC','Укажите к/c.');

  define('MODULE_PAYMENT_SCHET_8_TITLE','БИК');
  define('MODULE_PAYMENT_SCHET_8_DESC','Укажите БИК.');

  define('MODULE_PAYMENT_SCHET_9_TITLE','ИНН');
  define('MODULE_PAYMENT_SCHET_9_DESC','Укажите ИНН.');

  define('MODULE_PAYMENT_SCHET_10_TITLE','КПП');
  define('MODULE_PAYMENT_SCHET_10_DESC','Укажите КПП.');

  define('MODULE_PAYMENT_SCHET_11_TITLE','ОГРН');
  define('MODULE_PAYMENT_SCHET_11_DESC','Укажите ОГРН.');

  define('MODULE_PAYMENT_SCHET_12_TITLE','ОКПО');
  define('MODULE_PAYMENT_SCHET_12_DESC','Укажите ОКПО.');

  define('MODULE_PAYMENT_SCHET_SORT_ORDER_TITLE','Порядок сортировки');
  define('MODULE_PAYMENT_SCHET_SORT_ORDER_DESC','Укажите порядок сортировки модуля.');

  define('MODULE_PAYMENT_SCHET_ALLOWED_TITLE' , 'Разрешённые страны');
  define('MODULE_PAYMENT_SCHET_ALLOWED_DESC' , 'Укажите коды стран, для которых будет доступен данный модуль (например RU,DE (оставьте поле пустым, если хотите что б модуль был доступен покупателям из любых стран))');

  define('MODULE_PAYMENT_SCHET_ZONE_TITLE' , 'Зона');
  define('MODULE_PAYMENT_SCHET_ZONE_DESC' , 'Если выбрана зона, то данный модуль оплаты будет виден только покупателям из выбранной зоны.');

  define('MODULE_PAYMENT_SCHET_ORDER_STATUS_ID_TITLE' , 'Статус заказа');
  define('MODULE_PAYMENT_SCHET_ORDER_STATUS_ID_DESC' , 'Заказы, оформленные с использованием данного модуля оплаты будут принимать указанный статус.');

  define('MODULE_PAYMENT_SCHET_J_NAME_TITLE' , 'Info');
  define('MODULE_PAYMENT_SCHET_J_NAME_DESC' , '');

  define('MODULE_PAYMENT_SCHET_J_NAME' , 'Company:');
  define('MODULE_PAYMENT_SCHET_J_NAME_IP' , '');
  define('MODULE_PAYMENT_SCHET_J_INN' , 'INN:');
  define('MODULE_PAYMENT_SCHET_J_KPP' , 'KPP:');
  define('MODULE_PAYMENT_SCHET_J_OGRN' , 'OGRN:');
  define('MODULE_PAYMENT_SCHET_J_OKPO' , 'OKPO:');
  define('MODULE_PAYMENT_SCHET_J_RS' , 'R/s:');
  define('MODULE_PAYMENT_SCHET_J_BANK_NAME' , 'Bank name:');
  define('MODULE_PAYMENT_SCHET_J_BANK_NAME_HELP' , '');
  define('MODULE_PAYMENT_SCHET_J_BIK' , 'BIK:');
  define('MODULE_PAYMENT_SCHET_J_KS' , 'K/s:');
  define('MODULE_PAYMENT_SCHET_J_ADDRESS' , 'Postal Address:');
  define('MODULE_PAYMENT_SCHET_J_ADDRESS_HELP' , '');
  define('MODULE_PAYMENT_SCHET_J_YUR_ADDRESS' , 'Billing address');
  define('MODULE_PAYMENT_SCHET_J_FAKT_ADDRESS' , 'Shipping address');
  define('MODULE_PAYMENT_SCHET_J_TELEPHONE' , 'Phone');
  define('MODULE_PAYMENT_SCHET_J_FAX' , 'Fax');
  define('MODULE_PAYMENT_SCHET_J_EMAIL' , 'Email');
  define('MODULE_PAYMENT_SCHET_J_DIRECTOR' , 'Director');
  define('MODULE_PAYMENT_SCHET_J_ACCOUNTANT' , 'Accountant');

?>