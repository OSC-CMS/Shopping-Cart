<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('BOX_INFORMATION_AFFILIATE', 'Партнёрка');
define('BOX_AFFILIATE_INFO', 'Информация о партнёрской программе');
define('BOX_AFFILIATE_SUMMARY', 'Общая статистика');
define('BOX_AFFILIATE_ACCOUNT', 'Изменить данные');
define('BOX_AFFILIATE_CLICKRATE', 'Клики');
define('BOX_AFFILIATE_PAYMENT', 'Выплаты');
define('BOX_AFFILIATE_SALES', 'Продажи');
define('BOX_AFFILIATE_BANNERS', 'Получить HTML-код');
define('BOX_AFFILIATE_CONTACT', 'Свяжитесь с нами');
define('BOX_AFFILIATE_FAQ', 'FAQ');
define('BOX_AFFILIATE_LOGIN', 'Вход / Регистрация');
define('BOX_AFFILIATE_LOGOUT', 'Выйти');

define('ENTRY_AFFILIATE_ACCEPT_AGB', 'Вы должны согласиться с <a target="_new" href="%s">правилами нашей партнёрской программы</a>.');
define('ENTRY_AFFILIATE_AGB_ERROR', '&nbsp;<small><font color="#FF0000">Вы должны согласиться с правилами нашей партнёрской программы</font></small>');
define('ENTRY_AFFILIATE_PAYMENT_CHECK_TEXT', '');
define('ENTRY_AFFILIATE_PAYMENT_CHECK_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_PAYMENT_PAYPAL_TEXT', '');
define('ENTRY_AFFILIATE_PAYMENT_PAYPAL_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_PAYMENT_BANK_NAME_TEXT', '');
define('ENTRY_AFFILIATE_PAYMENT_BANK_NAME_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_PAYMENT_BANK_ACCOUNT_NAME_TEXT', '');
define('ENTRY_AFFILIATE_PAYMENT_BANK_ACCOUNT_NAME_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_PAYMENT_BANK_ACCOUNT_NUMBER_TEXT', '');
define('ENTRY_AFFILIATE_PAYMENT_BANK_ACCOUNT_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_PAYMENT_BANK_BRANCH_NUMBER_TEXT', '');
define('ENTRY_AFFILIATE_PAYMENT_BANK_BRANCH_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_PAYMENT_BANK_SWIFT_CODE_TEXT', '');
define('ENTRY_AFFILIATE_PAYMENT_BANK_SWIFT_CODE_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_COMPANY_TEXT', '');
define('ENTRY_AFFILIATE_COMPANY_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_COMPANY_TAXID_TEXT', '');
define('ENTRY_AFFILIATE_COMPANY_TAXID_ERROR', '&nbsp;<small><font color="#FF0000">обязательно</font></small>');
define('ENTRY_AFFILIATE_HOMEPAGE_TEXT', '&nbsp;<small><font color="#AABBDD">обязательно (http://)</font></small>');
define('ENTRY_AFFILIATE_HOMEPAGE_ERROR', '&nbsp;<small><font color="#FF0000">обязательно (http://)</font></small>');

define('TEXT_AFFILIATE_PERIOD', 'Период: ');
define('TEXT_AFFILIATE_STATUS', 'Статус: ');
define('TEXT_AFFILIATE_LEVEL', 'Уровень: ');
define('TEXT_AFFILIATE_ALL_PERIODS', 'Все периоды');
define('TEXT_AFFILIATE_ALL_STATUS', 'Все статусы');
define('TEXT_AFFILIATE_ALL_LEVELS', 'Все уровни');
define('TEXT_AFFILIATE_PERSONAL_LEVEL', 'Вы');
define('TEXT_AFFILIATE_LEVEL_SUFFIX', 'Уровень ');
define('TEXT_AFFILIATE_NAME', 'Название баннера: ');
define('TEXT_AFFILIATE_INFO', 'Скопируйте код, расположенный ниже и разместите его на своём сайте. Данный код Вы можете размещать в любом месте своего сайта.');
define('TEXT_DISPLAY_NUMBER_OF_CLICKS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> кликов)');
define('TEXT_DISPLAY_NUMBER_OF_SALES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> продаж)');
define('TEXT_DISPLAY_NUMBER_OF_PAYMENTS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> выплат)');
define('TEXT_DELETED_ORDER_BY_ADMIN', 'Удалён администратором');
define('TEXT_AFFILIATE_PERSONAL_LEVEL_SHORT', 'Вы');
define('TEXT_COMMISSION_LEVEL_TIER', 'Уровень: ');
define('TEXT_COMMISSION_RATE_TIER', 'Комиссия: ');
define('TEXT_COMMISSION_TIER_COUNT', 'Продажи: ');
define('TEXT_COMMISSION_TIER_TOTAL', 'Сумма: ');
define('TEXT_COMMISSION_TIER', 'Комиссия: ');

define('EMAIL_PASSWORD_REMINDER_SUBJECT', STORE_NAME . ' - Новый пароль');
define('EMAIL_PASSWORD_REMINDER_BODY', 'Вы запросили новый пароль с адреса ' . $_SERVER['REMOTE_ADDR'] . '.' . "\n\n" . 'Ваш новый пароль:' . "\n\n" . '   %s' . "\n\n");

define('MAIL_AFFILIATE_SUBJECT', 'Партнёрская программа ' . STORE_NAME);
define('MAIL_AFFILIATE_HEADER', 'Спасибо, что присоединились к нашей партнёрской программе!

Ваши данные:
**************************

');
define('MAIL_AFFILIATE_ID', 'Партнёрский код: ');
define('MAIL_AFFILIATE_USERNAME', 'E-mail: ');
define('MAIL_AFFILIATE_PASSWORD', 'Пароль: ');
define('MAIL_AFFILIATE_LINK', 'Вход в систему: ');
define('MAIL_AFFILIATE_FOOTER', 'Надеемся на взаимовыгодное сотрудничество, спасибо!');

define('EMAIL_SUBJECT', 'Партнёрская программа');

define('NAVBAR_TITLE', 'Партнёрская программа');
define('NAVBAR_TITLE_AFFILIATE', 'Вход');
define('NAVBAR_TITLE_BANNERS', 'Баннеры');
define('NAVBAR_TITLE_CLICKS', 'Клики');
define('NAVBAR_TITLE_CONTACT', 'Обратная связь');
define('NAVBAR_TITLE_DETAILS', 'Изменить данные');
define('NAVBAR_TITLE_DETAILS_OK', 'Данные изменены');
//define('NAVBAR_TITLE_FAQ', 'Вопросы и ответы');
define('NAVBAR_TITLE_INFO', 'Информация');
define('NAVBAR_TITLE_LOGOUT', 'Выход');
define('NAVBAR_TITLE_PASSWORD_FORGOTTEN', 'Забыли пароль');
define('NAVBAR_TITLE_PAYMENT', 'Выплаты');
define('NAVBAR_TITLE_SALES', 'Продажи');
define('NAVBAR_TITLE_SIGNUP', 'Регистрация');
define('NAVBAR_TITLESIGNUP_OK', 'Регистрация успешно завершена');
define('NAVBAR_TITLE_SUMMARY', 'Общая статистика');
define('NAVBAR_TITLE_TERMS', 'Условия');

define('IMAGE_BANNERS', 'Баннеры');
define('IMAGE_CLICKTHROUGHS', 'Клики');
define('IMAGE_SALES', 'Продажи');
?>