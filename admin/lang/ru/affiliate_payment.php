<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('HEADING_TITLE', 'Выплаты партнёрам');
define('HEADING_TITLE_SEARCH', 'Поиск:');
define('HEADING_TITLE_STATUS','Статус:');

define('TEXT_ALL_PAYMENTS','Все выплаты');
define('TEXT_NO_PAYMENT_HISTORY', 'Нет архива выплат');


define('TABLE_HEADING_ACTION', 'Действие');
define('TABLE_HEADING_STATUS', 'Статус');
define('TABLE_HEADING_AFILIATE_NAME', 'Партнёр');
define('TABLE_HEADING_PAYMENT','Сумма (с налогом)');
define('TABLE_HEADING_NET_PAYMENT','Сумма (без налога)');
define('TABLE_HEADING_DATE_BILLED','Дата');
define('TABLE_HEADING_NEW_VALUE', 'Новый статус');
define('TABLE_HEADING_OLD_VALUE', 'Старый статус');
define('TABLE_HEADING_AFFILIATE_NOTIFIED', 'Партнёр уведомлён');
define('TABLE_HEADING_DATE_ADDED', 'Дата');

define('TEXT_DATE_PAYMENT_BILLED','Оплачено:');
define('TEXT_DATE_ORDER_LAST_MODIFIED','Последние изменения:');
define('TEXT_AFFILIATE_PAYMENT','Сумма:');
define('TEXT_AFFILIATE_BILLED','Дата:');
define('TEXT_AFFILIATE','Партнёр:');
define('TEXT_INFO_DELETE_INTRO','Вы действительно хотите удалить этот платёж?');
define('TEXT_DISPLAY_NUMBER_OF_PAYMENTS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> выплат)');

define('TEXT_AFFILIATE_PAYING_POSSIBILITIES','Способы оплаты:');
define('TEXT_AFFILIATE_PAYMENT_CHECK','Webmoney:');
define('TEXT_AFFILIATE_PAYMENT_CHECK_PAYEE','Номер Z кошелька:');
define('TEXT_AFFILIATE_PAYMENT_PAYPAL','Система PayPal:');
define('TEXT_AFFILIATE_PAYMENT_PAYPAL_EMAIL','Email партнёра в системе PayPal:');
define('TEXT_AFFILIATE_PAYMENT_BANK_TRANSFER','Перевод на счёт в банке:');
define('TEXT_AFFILIATE_PAYMENT_BANK_NAME','Название банка:');
define('TEXT_AFFILIATE_PAYMENT_BANK_ACCOUNT_NAME','Получатель платежа:');
define('TEXT_AFFILIATE_PAYMENT_BANK_ACCOUNT_NUMBER','Номер счета:');
define('TEXT_AFFILIATE_PAYMENT_BANK_BRANCH_NUMBER','ABA/BSB номер:');
define('TEXT_AFFILIATE_PAYMENT_BANK_SWIFT_CODE','SWIFT Код:');

define('TEXT_INFO_HEADING_DELETE_PAYMENT','Удалить выплату');

define('IMAGE_AFFILIATE_BILLING','Выписать счета к оплате');

define('ERROR_PAYMENT_DOES_NOT_EXIST','Нет новых счетов');


define('SUCCESS_BILLING','Ваши партнёры получили уведомления');
define('SUCCESS_PAYMENT_UPDATED','Статус выплаты успешно изменён');

define('PAYMENT_STATUS','Статус выплаты');
define('PAYMENT_NOTIFY_AFFILIATE', 'Уведомить партнёра');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Выплаты по партнёрской программе!');
define('EMAIL_TEXT_AFFILIATE_PAYMENT_NUMBER', 'Номер счёта:');
define('EMAIL_TEXT_INVOICE_URL', 'Подробности:');
define('EMAIL_TEXT_PAYMENT_BILLED', 'Дата:');
define('EMAIL_TEXT_STATUS_UPDATE', 'Статус Вашей выплаты в нашей партнёрской программе был изменён.' . "\n\n" . 'Новый статус: %s' . "\n\n" . 'Если у Вас возникли какие-либо вопросы, задайте нам их в ответном письме.' . "\n");
define('EMAIL_TEXT_NEW_PAYMENT', 'Выписан счёт к оплате Ваших комиссионных, заработанных в нашей партнёрской программе, как только мы оплатим счёт, Вы получите дополнительное уведомление' . "\n");
?>