<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('AFFILIATE_EMAIL_ADDRESS_TITLE' , 'E-Mail Адрес');
define('AFFILIATE_EMAIL_ADDRESS_DESC', 'E-Mail Адрес Партнёрской программы');
define('AFFILIATE_PERCENT_TITLE' , 'Процент с каждой продажи, начисляемый партнёру');
define('AFFILIATE_PERCENT_DESC', 'Процент от суммы оплаченного заказа, начисляемый партнёрам');
define('AFFILIATE_THRESHOLD_TITLE' , 'Минимальная сумма к оплате');
define('AFFILIATE_THRESHOLD_DESC', 'Минимальная сумма партнёрской комиссии к оплате');
define('AFFILIATE_COOKIE_LIFETIME_TITLE' , 'Время хранения cookies');
define('AFFILIATE_COOKIE_LIFETIME_DESC', 'Время (в секундах) хранения cookies. Если посетитель с одного IP адреса сделал клик или покупку, и комиссия с его покупки была зачтена партнёру, то в следующий раз клики и продажи с этого IP будут засчитыватсья только через 7200 секунд (по умолчанию).');
define('AFFILIATE_BILLING_TIME_TITLE' , 'Выписка счетов к оплате');
define('AFFILIATE_BILLING_TIME_DESC', 'По умолчанию стоит 30, это значит, что счета для оплаты комиссий партнёрам выписываются раз в месяц');
define('AFFILIATE_PAYMENT_ORDER_MIN_STATUS_TITLE' , 'Минимальный статус заказа');
define('AFFILIATE_PAYMENT_ORDER_MIN_STATUS_DESC', 'Необходимо для того, чтобы комиссия партнёрам начислялась только за оплаченные заказы, статус ID - 3 или выше. По умолчанию стоит 3 (Выполняется), т.е. заказ уже оплачен и комиссия партнёрам начисляется только за оплаченные заказы.');
define('AFFILIATE_USE_CHECK_TITLE' , 'Оплата партнёрам через WebMoney');
define('AFFILIATE_USE_CHECK_DESC', 'Оплата партнёрских комиссий через WebMoney. При регистрации партнёр указывает свои данные в WebMoney.<br>true - Включено<br>false - Выключено');
define('AFFILIATE_USE_PAYPAL_TITLE' , 'Оплата партнёрам через PayPal');
define('AFFILIATE_USE_PAYPAL_DESC', 'Оплата через систему PayPal.<br>true - Включено<br>false - Выключено');
define('AFFILIATE_USE_BANK_TITLE' , 'Оплата партнёрам переводом на счёт в банке');
define('AFFILIATE_USE_BANK_DESC', 'Оплата партнёрских комиссий через банк.<br>true - Включено<br>false - Выключено');
define('AFFILATE_INDIVIDUAL_PERCENTAGE_TITLE' , 'Индивидуальные проценты для партнёров');
define('AFFILATE_INDIVIDUAL_PERCENTAGE_DESC', 'Позволяет указывать индивидуальные процентны комиссии для партнёров. Например, по умолчанию стоит 10% с продажи для всех зарегистрированных партнёров, а Вы можете наиболее успешным партнёрам давать комиссию 15% с продажи.');
define('AFFILATE_USE_TIER_TITLE' , 'Партнёрская пирамида');
define('AFFILATE_USE_TIER_DESC', 'Партнёры, зарегистрировавшиеся через себя новых партнёров, могут получать комиссию за заказы, оформленные через партнёров, которых он привёл в магазин.');
define('AFFILIATE_TIER_LEVELS_TITLE' , 'Количество уровей пирамиды');
define('AFFILIATE_TIER_LEVELS_DESC', 'Количество уровней, которое учитываются при учёте комиссии.');
define('AFFILIATE_TIER_PERCENTAGE_TITLE' , 'Процент комиссии партнёрской пирамиды');
define('AFFILIATE_TIER_PERCENTAGE_DESC', 'Проценты комиссии для каждого из уровней.<br>Пример: 8.00;5.00;1.00');
?>