<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TEXT_TITLE_PF', 'Почта России - наложенный платёж');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TEXT_TITLE', 'Почта России - наложенный платёж');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TEXT_DESCRIPTION_PF', 'При этом способе доставки, заказ можно будет оплатить при получении на почте.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_INVALID_ZONE_PF', 'В этот регион доставка наложенным платежом «Почты России» невозможна.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TEXT_WAY_PARCEL_PF', 'Цена доставки посылкой, заказ оплачивается <i>при получени  на почте</i>.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TEXT_WAY_WRAPPER_PF', 'Цена доставки бандеролью, заказ оплачивается <i>при получени  на почте</i>.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TEXT_UNITS_PF', 'кг.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_UNDEFINED_RATE', 'В данный момент доставка наложенным платежом &laquo;Почты России&raquo; невозможна.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_NEED_PF', '');//Заказ будет разбит на X бандерол(ь,и,ей)
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_1_PF', 'бандероль');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_2_PF', 'бандероли');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_5_PF', 'бандеролей');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_NEED_PF', '');//Заказ будет разбит на X посылк(у,и,ок)
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_1_PF', 'посылка');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_2_PF', 'посылки');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_5_PF', 'посылок');


define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_LIMITATION_PF_TITLE', 'Регионы, в которые ПОСЫЛКИ &laquo;наложкой&raquo; не доставляются.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_LIMITATION_PF_DESC', 'Несколько регионов можно указать через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_COST_TITLE', 'Расходы магазина на &laquo;наложку&raquo; за БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_COST_DESC', 'Zx% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число, Z режим: <b>p</b> - процент от стоимости товара, <b>d</b> - процент от стоимости доставки (с учётом суммы за сборку), <b>a (или отсутсвие буквы)</b> - процент от стоимости товара и доставки. <br><i>Указанная сумма (процент) прибавится к стоимости доставки.</i>');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_STATUS_PF_TITLE', 'Разрешить наложенный платёж ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_STATUS_PF_DESC', 'Вы хотите активировать &laquo;наложку&raquo; для ПОСЫЛКИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_STATUS_PF_TITLE', 'Разрешить наложенный платёж БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_STATUS_PF_DESC', 'Вы хотите активировать &laquo;наложку&raquo; для БАНДЕРОЛИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_SORT_ORDER_PF_TITLE', 'Сортировать');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_SORT_ORDER_PF_DESC', 'Положение этого модуля в списке модулей.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TAX_CLASS_PF_TITLE', 'Почта России (&laquo;наложка&raquo;) - Tax Class');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TAX_CLASS_PF_DESC', 'Use the following tax class on the shipping fee.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_COST_TITLE', 'Расходы магазина на &laquo;наложку&raquo; за ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_COST_DESC', 'Zx% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число, Z режим: <b>p</b> - процент от стоимости товара, <b>d</b> - процент от стоимости доставки (с учётом суммы за сборку), <b>a (или отсутсвие буквы)</b> - процент от стоимости товара и доставки. <br><i>Указанная сумма (процент) прибавится к стоимости доставки.</i>');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_3_TITLE', '*3-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_3_TITLE', '*3-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_3_DESC', 'Введите название областей РФ для 3-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_2_TITLE', '*2-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_2_TITLE', '*2-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_2_TITLE', '*2-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_2_DESC', 'Введите название областей РФ для 2-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_1_TITLE', '*1-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_1_TITLE', '*1-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_1_TITLE', '*1-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_1_DESC', 'Введите название областей РФ для 1-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_ISSET_TITLE', '*Сигнальная часть для вычисления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_ISSET_DESC', 'Вы можете ввести несколько сигнальных частей через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_LIMITATION_PF_TITLE', 'Регионы, в которые БАНДЕРОЛИ &laquo;наложкой&raquo; не доставляются.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_LIMITATION_PF_DESC', 'Несколько регионов можно указать через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_SEPARATOR_TITLE', '*Разделитель модели (артикула) товара и &laquo;ключа&raquo; бандероли.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_SEPARATOR_DESC', 'Необходимо указать, каким символом будет отделяться модель (артикул) товара от метки бандероли. Например: <i><font color=#008080>banderol</font><b>-</b><font color=#800040>ART6789B</font></i>');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_2_TITLE', '*2-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_2_DESC', 'Введите название областей РФ для 2-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_1_TITLE', '*1-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_1_TITLE', '*1-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_3_TITLE', '*3-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_4_TITLE', '*4-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_4_DESC', 'Введите название областей РФ для 4-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_4_TITLE', '*4-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_4_TITLE', '*4-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_5_TITLE', '*5-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_5_DESC', 'Введите название областей РФ для 5-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_5_TITLE', '*5-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_5_TITLE', '*5-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_MAXWEIGHT_TITLE', '*Максимальный вес одной БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у бандероли? Если вес будет больше, будет выбрана посылка или заказ будет разбит на несколько бандеролей.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_MAXWEIGHT_TITLE', '*Максимальный вес одной ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у посылки? Если вес будет больше, заказ будет разбит на несколько посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPERS_OR_PARCEL_TITLE', '*Разбивать &laquo;тяжеловесные&raquo; бандероли на несколько штук (иначе использовать посылку)?');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPERS_OR_PARCEL_DESC', '<b>Да</b> - разбиение на несколько бандеролей.<br><b>Нет</b> - переход в разряд посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_REG_TITLE', '*Стоимость оформления ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_REG_TITLE', '*Стоимость оформления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_STATUS_TITLE', 'Разрешить посылки');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_STATUS_DESC', 'Вы хотите активировать ПОСЫЛКИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_STATUS_TITLE', 'Разрешить бандероли');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_STATUS_DESC', 'Вы хотите активировать БАНДЕРОЛИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TAX_CLASS_TITLE', 'Почта России - Tax Class');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_TAX_CLASS_DESC', 'Use the following tax class on the shipping fee.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_SORT_ORDER_PREPAY_TITLE', 'Сортировать');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_SORT_ORDER_PREPAY_DESC', 'Положение этого модуля в списке модулей.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_1_TITLE', '*Страны первого уровня (Беларусь, Узбекистан, Эстония)');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_1_DESC', 'Введите КОДЫ (ISO 2) стран первого уровня (Беларусь, Узбекистан, Эстония).');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_PRICE_1_TITLE', 'Страны первого уровня: таблица стоимости');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_PRICE_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_2_TITLE', '*Остальные страны');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_2_DESC', 'Введите КОДЫ (ISO 2) остальных стран. Если Вы готовы доставлять по всему миру, то введите * (звёздочку), иначе вводите коды.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_PRICE_2_TITLE', 'Остальные страны: таблица стоимости');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_COUNTRY_PRICE_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_INSURANCE_PRICE_TITLE', 'Сумма оценочной стоимости ПОСЫЛКИ (без НП).');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_INSURANCE_PRICE_DESC', '0 - сумма оценки будет равна стоимости заказа с доставкой; x% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_INSURANCE_PRICE_TITLE', 'Сумма оценочной стоимости БАНДЕРОЛИ (без НП).');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_INSURANCE_PRICE_DESC', '0 - сумма оценки будет равна стоимости заказа с доставкой; x% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_INTER_REG_TITLE', 'Стоимость оформления МЕЖДУНАРОДНОЙ ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_INTER_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_INTER_MAXWEIGHT_TITLE', 'Максимальный вес одной МЕЖДУНАРОДНОЙ ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_INTER_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у посылки? Если вес будет больше, заказ будет разбит на несколько посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_FREE_TITLE', 'Сумма для бесплатной доставки ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_FREE_DESC', 'Укажите сумму, при которой доставка будет бесплатной. Если указать 0, то бесплатной доставки не будет.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_FREE_TITLE', 'Сумма для бесплатной доставки БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_FREE_DESC', 'Укажите сумму, при которой доставка будет бесплатной. Если указать 0, то бесплатной доставки не будет.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_INTER_FREE_TITLE', 'Сумма для бесплатной доставки МЕЖДУНАРОДНОЙ ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_INTER_FREE_DESC', 'Укажите сумму, при которой доставка в ДРУГИЕ СТРАНЫ будет бесплатной. Если указать 0, то бесплатной доставки не будет.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_SEPARATOR_TITLE', '*Разделитель модели (артикула) товара и &laquo;ключа&raquo; бандероли.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_SEPARATOR_DESC', 'Необходимо указать, каким символом будет отделяться модель (артикул) товара от метки бандероли. Например: <i><font color=#008080>banderol</font><b>-</b><font color=#800040>ART6789B</font></i>');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_ISSET_TITLE', '*Сигнальная часть для вычисления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_ISSET_DESC', 'Вы можете ввести несколько сигнальных частей через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_1_TITLE', '*1-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_1_DESC', 'Введите название областей РФ для 1-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_2_TITLE', '*2-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_2_TITLE', '*2-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_3_TITLE', '*3-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_3_DESC', 'Введите название областей РФ для 3-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_3_TITLE', '*3-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_3_TITLE', '*3-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_4_TITLE', '*4-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_4_DESC', 'Введите название областей РФ для 4-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_4_TITLE', '*4-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_4_TITLE', '*4-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_5_TITLE', '*5-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_5_DESC', 'Введите название областей РФ для 5-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_5_TITLE', '*5-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_PARCEL_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_5_TITLE', '*5-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_STATES_PRICE_WRAPPER_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_MAXWEIGHT_TITLE', '*Максимальный вес одной БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у бандероли? Если вес будет больше, будет выбрана посылка или заказ будет разбит на несколько бандеролей.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_MAXWEIGHT_TITLE', '*Максимальный вес одной ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у посылки? Если вес будет больше, заказ будет разбит на несколько посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPERS_OR_PARCEL_TITLE', '*Разбивать &laquo;тяжеловесные&raquo; бандероли на несколько штук (иначе использовать посылку)?');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPERS_OR_PARCEL_DESC', '<b>Да</b> - разбиение на несколько бандеролей.<br><b>Нет</b> - переход в разряд посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_REG_TITLE', '*Стоимость оформления ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_PARCEL_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_REG_TITLE', '*Стоимость оформления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_WRAPPER_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPF_ALLOWED_TITLE', 'Страны в которые возможна доставка &laquo;наложкой&raquo;.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_ALLOWED_DESC', 'Укажите коды стран, в которые возможна &laquo;наложка&raquo;, разделяйте запятыми. Например:<i>RU,UA,BY</i>. Для всех стран оставьте поле пустым.');


define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_ALLOWED_TITLE', 'Страны в которые возможна доставка по предоплате.');
define('MODULE_SHIPPING_RUSSIANPOSTPREPAY_ALLOWED_DESC', 'Укажите коды стран, в которые возможна доставка по предоплате, разделяйте запятыми. Например:<i>RU,UA,BY</i>. Для всех стран оставьте поле пустым.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_TEXT_TITLE_PF', 'Почта России - наложенный платёж');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TEXT_TITLE', 'Почта России - наложенный платёж');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TEXT_DESCRIPTION_PF', 'При этом способе доставки, заказ можно будет оплатить при получении на почте.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_INVALID_ZONE_PF', 'В этот регион доставка наложенным платежом «Почты России» невозможна.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TEXT_WAY_PARCEL_PF', 'Цена доставки посылкой, заказ оплачивается <i>при получени  на почте</i>.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TEXT_WAY_WRAPPER_PF', 'Цена доставки бандеролью, заказ оплачивается <i>при получени  на почте</i>.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TEXT_UNITS_PF', 'кг.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_UNDEFINED_RATE', 'В данный момент доставка наложенным платежом &laquo;Почты России&raquo; невозможна.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_NEED_PF', '');//Заказ будет разбит на X бандерол(ь,и,ей)
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_1_PF', 'бандероль');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_2_PF', 'бандероли');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_5_PF', 'бандеролей');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_NEED_PF', '');//Заказ будет разбит на X посылк(у,и,ок)
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_1_PF', 'посылка');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_2_PF', 'посылки');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_5_PF', 'посылок');


define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_LIMITATION_PF_TITLE', 'Регионы, в которые ПОСЫЛКИ &laquo;наложкой&raquo; не доставляются.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_LIMITATION_PF_DESC', 'Несколько регионов можно указать через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_COST_TITLE', 'Расходы магазина на &laquo;наложку&raquo; за БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_COST_DESC', 'Zx% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число, Z режим: <b>p</b> - процент от стоимости товара, <b>d</b> - процент от стоимости доставки (с учётом суммы за сборку), <b>a (или отсутсвие буквы)</b> - процент от стоимости товара и доставки. <br><i>Указанная сумма (процент) прибавится к стоимости доставки.</i>');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_STATUS_PF_TITLE', 'Разрешить наложенный платёж ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_STATUS_PF_DESC', 'Вы хотите активировать &laquo;наложку&raquo; для ПОСЫЛКИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_STATUS_PF_TITLE', 'Разрешить наложенный платёж БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_STATUS_PF_DESC', 'Вы хотите активировать &laquo;наложку&raquo; для БАНДЕРОЛИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPF_SORT_ORDER_PF_TITLE', 'Сортировать');
define('MODULE_SHIPPING_RUSSIANPOSTPF_SORT_ORDER_PF_DESC', 'Положение этого модуля в списке модулей.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_TAX_CLASS_PF_TITLE', 'Почта России (&laquo;наложка&raquo;) - Tax Class');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TAX_CLASS_PF_DESC', 'Use the following tax class on the shipping fee.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_COST_TITLE', 'Расходы магазина на &laquo;наложку&raquo; за ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_COST_DESC', 'Zx% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число, Z режим: <b>p</b> - процент от стоимости товара, <b>d</b> - процент от стоимости доставки (с учётом суммы за сборку), <b>a (или отсутсвие буквы)</b> - процент от стоимости товара и доставки. <br><i>Указанная сумма (процент) прибавится к стоимости доставки.</i>');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_3_TITLE', '*3-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_3_TITLE', '*3-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_3_DESC', 'Введите название областей РФ для 3-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_2_TITLE', '*2-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_2_TITLE', '*2-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_2_TITLE', '*2-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_2_DESC', 'Введите название областей РФ для 2-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_1_TITLE', '*1-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_1_TITLE', '*1-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_1_TITLE', '*1-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_1_DESC', 'Введите название областей РФ для 1-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_ISSET_TITLE', '*Сигнальная часть для вычисления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_ISSET_DESC', 'Вы можете ввести несколько сигнальных частей через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_LIMITATION_PF_TITLE', 'Регионы, в которые БАНДЕРОЛИ &laquo;наложкой&raquo; не доставляются.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_LIMITATION_PF_DESC', 'Несколько регионов можно указать через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_SEPARATOR_TITLE', '*Разделитель модели (артикула) товара и &laquo;ключа&raquo; бандероли.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_SEPARATOR_DESC', 'Необходимо указать, каким символом будет отделяться модель (артикул) товара от метки бандероли. Например: <i><font color=#008080>banderol</font><b>-</b><font color=#800040>ART6789B</font></i>');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_2_TITLE', '*2-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_2_DESC', 'Введите название областей РФ для 2-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_1_TITLE', '*1-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_1_TITLE', '*1-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_3_TITLE', '*3-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_4_TITLE', '*4-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_4_DESC', 'Введите название областей РФ для 4-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_4_TITLE', '*4-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_4_TITLE', '*4-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_5_TITLE', '*5-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_5_DESC', 'Введите название областей РФ для 5-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_5_TITLE', '*5-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_5_TITLE', '*5-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_MAXWEIGHT_TITLE', '*Максимальный вес одной БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у бандероли? Если вес будет больше, будет выбрана посылка или заказ будет разбит на несколько бандеролей.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_MAXWEIGHT_TITLE', '*Максимальный вес одной ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у посылки? Если вес будет больше, заказ будет разбит на несколько посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPERS_OR_PARCEL_TITLE', '*Разбивать &laquo;тяжеловесные&raquo; бандероли на несколько штук (иначе использовать посылку)?');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPERS_OR_PARCEL_DESC', '<b>Да</b> - разбиение на несколько бандеролей.<br><b>Нет</b> - переход в разряд посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_REG_TITLE', '*Стоимость оформления ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_REG_TITLE', '*Стоимость оформления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_STATUS_TITLE', 'Разрешить посылки');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_STATUS_DESC', 'Вы хотите активировать ПОСЫЛКИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_STATUS_TITLE', 'Разрешить бандероли');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_STATUS_DESC', 'Вы хотите активировать БАНДЕРОЛИ?');

define('MODULE_SHIPPING_RUSSIANPOSTPF_TAX_CLASS_TITLE', 'Почта России - Tax Class');
define('MODULE_SHIPPING_RUSSIANPOSTPF_TAX_CLASS_DESC', 'Use the following tax class on the shipping fee.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_SORT_ORDER_PREPAY_TITLE', 'Сортировать');
define('MODULE_SHIPPING_RUSSIANPOSTPF_SORT_ORDER_PREPAY_DESC', 'Положение этого модуля в списке модулей.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_1_TITLE', '*Страны первого уровня (Беларусь, Узбекистан, Эстония)');
define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_1_DESC', 'Введите КОДЫ (ISO 2) стран первого уровня (Беларусь, Узбекистан, Эстония).');

define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_PRICE_1_TITLE', 'Страны первого уровня: таблица стоимости');
define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_PRICE_1_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_2_TITLE', '*Остальные страны');
define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_2_DESC', 'Введите КОДЫ (ISO 2) остальных стран. Если Вы готовы доставлять по всему миру, то введите * (звёздочку), иначе вводите коды.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_PRICE_2_TITLE', 'Остальные страны: таблица стоимости');
define('MODULE_SHIPPING_RUSSIANPOSTPF_COUNTRY_PRICE_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_INSURANCE_PRICE_TITLE', 'Сумма оценочной стоимости ПОСЫЛКИ (без НП).');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_INSURANCE_PRICE_DESC', '0 - сумма оценки будет равна стоимости заказа с доставкой; x% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_INSURANCE_PRICE_TITLE', 'Сумма оценочной стоимости БАНДЕРОЛИ (без НП).');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_INSURANCE_PRICE_DESC', '0 - сумма оценки будет равна стоимости заказа с доставкой; x% - некий процент от стоимости заказа; x - фиксированная стоимость. x - какое-либо число');

define('MODULE_SHIPPING_RUSSIANPOSTPF_INTER_REG_TITLE', 'Стоимость оформления МЕЖДУНАРОДНОЙ ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_INTER_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPF_INTER_MAXWEIGHT_TITLE', 'Максимальный вес одной МЕЖДУНАРОДНОЙ ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_INTER_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у посылки? Если вес будет больше, заказ будет разбит на несколько посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_FREE_TITLE', 'Сумма для бесплатной доставки ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_FREE_DESC', 'Укажите сумму, при которой доставка будет бесплатной. Если указать 0, то бесплатной доставки не будет.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_FREE_TITLE', 'Сумма для бесплатной доставки БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_FREE_DESC', 'Укажите сумму, при которой доставка будет бесплатной. Если указать 0, то бесплатной доставки не будет.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_INTER_FREE_TITLE', 'Сумма для бесплатной доставки МЕЖДУНАРОДНОЙ ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_INTER_FREE_DESC', 'Укажите сумму, при которой доставка в ДРУГИЕ СТРАНЫ будет бесплатной. Если указать 0, то бесплатной доставки не будет.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_SEPARATOR_TITLE', '*Разделитель модели (артикула) товара и &laquo;ключа&raquo; бандероли.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_SEPARATOR_DESC', 'Необходимо указать, каким символом будет отделяться модель (артикул) товара от метки бандероли. Например: <i><font color=#008080>banderol</font><b>-</b><font color=#800040>ART6789B</font></i>');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_ISSET_TITLE', '*Сигнальная часть для вычисления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_ISSET_DESC', 'Вы можете ввести несколько сигнальных частей через запятую.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_1_TITLE', '*1-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_1_DESC', 'Введите название областей РФ для 1-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_2_TITLE', '*2-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_2_TITLE', '*2-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_2_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_3_TITLE', '*3-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_3_DESC', 'Введите название областей РФ для 3-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_3_TITLE', '*3-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_3_TITLE', '*3-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_3_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_4_TITLE', '*4-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_4_DESC', 'Введите название областей РФ для 4-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_4_TITLE', '*4-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_4_TITLE', '*4-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_4_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_5_TITLE', '*5-я зона');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_5_DESC', 'Введите название областей РФ для 5-й зоны. ');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_5_TITLE', '*5-я зона: таблица стоимости ПОСЫЛКИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_PARCEL_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_5_TITLE', '*5-я зона: таблица стоимости БАНДЕРОЛИ');
define('MODULE_SHIPPING_RUSSIANPOSTPF_STATES_PRICE_WRAPPER_5_DESC', 'По шаблону: <i>вес:цена,вес:цена</i>. Пример 3:8.50,7:10.50,... и т.д.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_INSURANCE_TITLE', '*Проценты, взимаемые почтой за оценочную стоимость БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_INSURANCE_DESC', 'Введите только цифры');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_MAXWEIGHT_TITLE', '*Максимальный вес одной БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у бандероли? Если вес будет больше, будет выбрана посылка или заказ будет разбит на несколько бандеролей.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_MAXWEIGHT_TITLE', '*Максимальный вес одной ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_MAXWEIGHT_DESC', 'Какой максимальный вес может быть у посылки? Если вес будет больше, заказ будет разбит на несколько посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPERS_OR_PARCEL_TITLE', '*Разбивать &laquo;тяжеловесные&raquo; бандероли на несколько штук (иначе использовать посылку)?');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPERS_OR_PARCEL_DESC', '<b>Да</b> - разбиение на несколько бандеролей.<br><b>Нет</b> - переход в разряд посылок.');

define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_REG_TITLE', '*Стоимость оформления ПОСЫЛКИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_PARCEL_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_REG_TITLE', '*Стоимость оформления БАНДЕРОЛИ.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_WRAPPER_REG_DESC', 'Укажите сумму');

define('MODULE_SHIPPING_RUSSIANPOSTPF_ALLOWED_TITLE', 'Страны в которые возможна доставка &laquo;наложкой&raquo;.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_ALLOWED_DESC', 'Укажите коды стран, в которые возможна &laquo;наложка&raquo;, разделяйте запятыми. Например:<i>RU,UA,BY</i>. Для всех стран оставьте поле пустым.');


define('MODULE_SHIPPING_RUSSIANPOSTPF_ALLOWED_TITLE', 'Страны в которые возможна доставка по предоплате.');
define('MODULE_SHIPPING_RUSSIANPOSTPF_ALLOWED_DESC', 'Укажите коды стран, в которые возможна доставка по предоплате, разделяйте запятыми. Например:<i>RU,UA,BY</i>. Для всех стран оставьте поле пустым.');

?>