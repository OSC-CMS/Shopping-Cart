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

@setlocale(LC_TIME, 'en_US');
define('DATE_FORMAT_SHORT', '%d/%m/%Y');  
define('DATE_FORMAT_LONG', '%A %d %B, %Y'); 
define('DATE_FORMAT', 'd/m/Y'); 
define('PHP_DATE_TIME_FORMAT', 'd/m/Y H:i:s'); 
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

define('HTML_PARAMS','dir="ltr" lang="ru"');

define('TITLE', PROJECT_VERSION);

define('HEADER_TITLE_TOP', 'Администрирование');
define('HEADER_TITLE_SUPPORT_SITE', 'Сайт поддержки');
define('HEADER_TITLE_ONLINE_CATALOG', 'Каталог');
define('HEADER_TITLE_ADMINISTRATION', 'Администрация');

define('MALE', 'Мужской');
define('FEMALE', 'Женский');

define('DOB_FORMAT_STRING', 'dd/mm/yyyy');

define('BOX_HEADING_CONFIGURATION','Настройки');
define('BOX_HEADING_CONFIGURATION_MAIN','Основные');
define('BOX_HEADING_ADDONS','Модули');
define('BOX_HEADING_ZONE','Регионы/Налоги');
define('BOX_HEADING_CUSTOMERS','Покупатели');
define('BOX_HEADING_PRODUCTS','Каталог');
define('BOX_HEADING_OTHER','Система');
define('BOX_HEADING_STATISTICS','Статистика');
define('BOX_HEADING_TOOLS','Инструменты');
define('BOX_HEADING_LOGOFF','Выйти');
define('BOX_HEADING_HELP','Помощь');

define('BOX_CONTENT','Информационные страницы');
define('TEXT_ALLOWED', 'Разрешено');
define('TEXT_ACCESS', 'Доступ');
define('BOX_CONFIGURATION', 'Настройки');
define('BOX_CONFIGURATION_1', 'Мой магазин');
define('BOX_CONFIGURATION_2', 'Минимальные');
define('BOX_CONFIGURATION_3', 'Максимальные');
define('BOX_CONFIGURATION_4', 'Картинки');
define('BOX_CONFIGURATION_5', 'Данные покупателя');
define('BOX_CONFIGURATION_6', 'Модули');
define('BOX_CONFIGURATION_7', 'Доставка/Упаковка');
define('BOX_CONFIGURATION_8', 'Вывод товара');
define('BOX_CONFIGURATION_9', 'Склад');
define('BOX_CONFIGURATION_10', 'Отладка');
define('BOX_ERROR_LOG', 'Логи ошибок');
define('BOX_CONFIGURATION_11', 'Кэш');
define('BOX_CONFIGURATION_12', 'Настройка E-Mail');
define('BOX_CONFIGURATION_13', 'Скачивание');
define('BOX_CONFIGURATION_14', 'GZip компрессия');
define('BOX_CONFIGURATION_15', 'Сессии');
define('BOX_CONFIGURATION_17', 'Разное');
define('BOX_CONFIGURATION_22', 'Настройки поиска');
define('BOX_CONFIGURATION_23', 'Яндекс-Маркет');
define('BOX_CONFIGURATION_24', 'Изменение цен');
define('BOX_CONFIGURATION_25', 'Установка модулей');
define('BOX_CONFIGURATION_27', 'Тех. обслуживание');
define('BOX_CONFIGURATION_28', 'Партнерка');
define('BOX_CONFIGURATION_30', 'Настройка блоков');
define('BOX_CONFIGURATION_31', 'ЧПУ URL');
define('BOX_CAMPAIGNS_REPORT2', 'Статистика продаж 2');

define('BOX_MODULES', 'Оплата/Доставка/Счета');
define('BOX_PAYMENT', 'Модули оплаты');
define('BOX_SHIPPING', 'Модули доставки');
define('BOX_ORDER_TOTAL', 'Модули итого');
define('BOX_CATEGORIES', 'Категории и Товары');
define('BOX_PRODUCTS_ATTRIBUTES', 'Значения');
define('BOX_MANUFACTURERS', 'Производители');
define('BOX_REVIEWS', 'Отзывы');
define('BOX_CAMPAIGNS', 'Кампании');
define('BOX_XSELL_PRODUCTS', 'Сопутствующие товары');
define('BOX_SPECIALS', 'Скидки');
define('BOX_PRODUCTS_EXPECTED', 'Ожидаемые товары');
define('BOX_CUSTOMERS', 'Покупатели');
define('BOX_ACCOUNTING', 'Доступ админа');
define('BOX_CUSTOMERS_STATUS','Группы клиентов');
define('BOX_ORDERS', 'Заказы');
define('BOX_COUNTRIES', 'Страны');
define('BOX_ZONES', 'Регионы');
define('BOX_GEO_ZONES', 'Географические&nbsp;зоны');
define('BOX_TAX_CLASSES', 'Виды налогов');
define('BOX_TAX_RATES', 'Ставки налогов');
define('BOX_HEADING_REPORTS', 'Отчёты');
define('BOX_PRODUCTS_VIEWED', 'Просмотренные товары');
define('BOX_STOCK_WARNING','Информация о складе');
define('BOX_PRODUCTS_PURCHASED', 'Заказанные товары');
define('BOX_STATS_CUSTOMERS', 'Лучшие клиенты');
define('BOX_STATS_STOCK_WARNING', 'Склад');

define('BOX_BACKUP', 'Резервное копирование');
define('BOX_BANNER_MANAGER', 'Управление баннерми');
define('BOX_CACHE', 'Кэш');
define('BOX_DEFINE_LANGUAGE', 'Языковые константы');
define('BOX_FILE_MANAGER', 'Файл менеджер');
define('BOX_MAIL', 'E-Mail центр');
define('BOX_NEWSLETTERS', 'Почтовые уведомления');
define('BOX_SERVER_INFO', 'Сервер инфо');
define('BOX_WHOS_ONLINE', 'Кто в оn-line?');
define('BOX_TPL_BOXES','Порядок сортировки боксов');
define('BOX_CURRENCIES', 'Валюты');
define('BOX_LANGUAGES', 'Языки');
define('BOX_ORDERS_STATUS', 'Статусы заказа');
define('BOX_ATTRIBUTES_MANAGER','Установка');
define('BOX_ATTRIBUTES','Атрибуты');
define('BOX_SHIPPING_STATUS','Время доставки');
define('BOX_SALES_REPORT','Статистика продаж');
define('BOX_MODULE_EXPORT','Другие модули');
define('BOX_PLUGINS','Плагины');
define('BOX_HEADING_GV_ADMIN', 'Купоны');
define('BOX_GV_ADMIN_QUEUE', 'Активация сертификатов');
define('BOX_GV_ADMIN_MAIL', 'Отправить сертификат');
define('BOX_GV_ADMIN_SENT', 'Отправленные сертификаты');
define('BOX_COUPON_ADMIN','Управление купонами');
define('BOX_IMPORT','CSV импорт/Экспорт');
define('BOX_PRODUCTS_VPE','Единица упаковки');
define('BOX_CAMPAIGNS_REPORT','Отчёт по кампаниям');
define('BOX_ORDERS_XSELL_GROUP','Сопутствующие');
define('BOX_SUPPORT_SITE','Сайт поддержки');
define('BOX_SUPPORT_FAQ','Вопросы и ответы');
define('BOX_SUPPORT_DOC','Документация');
define('BOX_SUPPORT_FORUM','Форум');
define('BOX_HOSTING','Хостинг');

define('TXT_GROUPS','<b>Группы</b>:');
define('TXT_SYSTEM','Система');
define('TXT_CUSTOMERS','Клиенты/Заказы');
define('TXT_PRODUCTS','Товары/Категории');
define('TXT_STATISTICS','Статистика');
define('TXT_TOOLS','Инструменты');
define('TEXT_ACCOUNTING','Доступ админа:');

define('BOX_HEADING_LOCALIZATION', 'Локализация');
define('BOX_HEADING_TEMPLATES','Шаблоны');
define('BOX_HEADING_LOCATION_AND_TAXES', 'Места / Налоги');
define('BOX_HEADING_CATALOG', 'Каталог');
define('BOX_MODULE_NEWSLETTER','Рассылка');

define('JS_ERROR', 'При заполнении формы Вы допустили ошибки!\nСделайте, пожалуйста, следующие исправления:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* Новый атрибут товара дожен иметь цену\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* Новый атрибут товара дожен иметь ценовой префикс\n');

define('JS_PRODUCTS_NAME', '* Для нового товара должно быть указано наименование\n');
define('JS_PRODUCTS_DESCRIPTION', '* Для нового товара должно быть указано описание\n');
define('JS_PRODUCTS_PRICE', '* Для нового товара должна быть указана цена\n');
define('JS_PRODUCTS_WEIGHT', '* Для нового товара должен быть указан вес\n');
define('JS_PRODUCTS_QUANTITY', '* Для нового товара должно быть указано количество\n');
define('JS_PRODUCTS_MODEL', '* Для нового товара должен быть указан код товара\n');
define('JS_PRODUCTS_IMAGE', '* Для нового товара должна быть картинка\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* Для этого товара должна быть установлена новая цена\n');

define('JS_GENDER', '* Поле \'Пол\' должно быть выбрано.\n');
define('JS_FIRST_NAME', '* Поле \'Имя\' должно содержать не менее ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' символов.\n');
define('JS_LAST_NAME', '* Поле \'Фамилия\' должно содержать не менее ' . ENTRY_LAST_NAME_MIN_LENGTH . ' символов.\n');
define('JS_DOB', '* Поле \'День рождения\' должно иметь формат: xx/xx/xxxx (день/месяц/год).\n');
define('JS_EMAIL_ADDRESS', '* Поле \'E-Mail адрес\' должно содержать не менее ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' символов.\n');
define('JS_ADDRESS', '* Поле \'Адрес\' должно содержать не менее ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' символов.\n');
define('JS_POST_CODE', '* Поле \'Индекс\' должно содержать не менее ' . ENTRY_POSTCODE_MIN_LENGTH . ' символов.\n');
define('JS_CITY', '* Поле \'Город\' должно содержать не менее ' . ENTRY_CITY_MIN_LENGTH . ' символов.\n');
define('JS_STATE', '* Поле \'Регион\' должно быть выбрано.\n');
define('JS_STATE_SELECT', '-- Выберите выше --');
define('JS_ZONE', '* Поле \'Регион\' должно соответствовать выбраной стране.');
define('JS_COUNTRY', '* Поле \'Страна\' дожно быть заполнено.\n');
define('JS_TELEPHONE', '* Поле \'Телефон\' должно содержать не менее ' . ENTRY_TELEPHONE_MIN_LENGTH . ' символов.\n');
define('JS_PASSWORD', '* Поля \'Пароль\' и \'Подтверждение\' должны совпадать и содержать не менее ' . ENTRY_PASSWORD_MIN_LENGTH . ' символов.\n');

define('JS_ORDER_DOES_NOT_EXIST', 'Заказ номер %s не найден!');

define('CATEGORY_PERSONAL', 'Персональные данные');
define('CATEGORY_ADDRESS', 'Адрес');
define('CATEGORY_CONTACT', 'Для контакта');
define('CATEGORY_COMPANY', 'Компания');
define('CATEGORY_OPTIONS', 'Настройки');

define('ENTRY_SECOND_NAME', 'Отчество:');
define('ENTRY_GENDER', 'Пол:');
define('ENTRY_GENDER_ERROR', '&nbsp;<span class="errorText">обязательно</span>');
define('ENTRY_FIRST_NAME', 'Имя:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<span class="errorText">минимум ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' символов</span>');
define('ENTRY_LAST_NAME', 'Фамилия:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<span class="errorText">минимум ' . ENTRY_LAST_NAME_MIN_LENGTH . ' символов</span>');
define('ENTRY_DATE_OF_BIRTH', 'Дата рождения:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<span class="errorText">(пример 21/05/1970)</span>');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail Адрес:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<span class="errorText">минимум ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' символов</span>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<span class="errorText">Вы ввели неверный E-Mail адрес!</span>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<span class="errorText">Данный E-Mail адрес уже зарегистрирован!</span>');
define('ENTRY_COMPANY', 'Компания:');
define('ENTRY_STREET_ADDRESS', 'Адрес:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<span class="errorText">минимум ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' символов</span>');
define('ENTRY_SUBURB', 'Район:');
define('ENTRY_POST_CODE', 'Индекс:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<span class="errorText">минимум ' . ENTRY_POSTCODE_MIN_LENGTH . ' символов</span>');
define('ENTRY_CITY', 'Город:');
define('ENTRY_CITY_ERROR', '&nbsp;<span class="errorText">минимум ' . ENTRY_CITY_MIN_LENGTH . ' символов</span>');
define('ENTRY_STATE', 'Регион:');
define('ENTRY_STATE_ERROR', '&nbsp;<span class="errorText">обязательно</span>');
define('ENTRY_COUNTRY', 'Страна:');
define('ENTRY_TELEPHONE_NUMBER', 'Телефон:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<span class="errorText">минимум ' . ENTRY_TELEPHONE_MIN_LENGTH . ' символов</span>');
define('ENTRY_FAX_NUMBER', 'Факс:');
define('ENTRY_NEWSLETTER', 'Рассылка:');
define('ENTRY_CUSTOMERS_STATUS', 'Статус клиента:');
define('ENTRY_NEWSLETTER_YES', 'Подписан');
define('ENTRY_NEWSLETTER_NO', 'Не подписан');
define('ENTRY_MAIL_ERROR','&nbsp;<span class="errorText">Выберите опцию</span>');
define('ENTRY_PASSWORD','Пароль (сгенерирован)');
define('ENTRY_PASSWORD_ERROR','&nbsp;<span class="errorText">минимум ' . ENTRY_PASSWORD_MIN_LENGTH . ' символов</span>');
define('ENTRY_MAIL_COMMENTS','Дополнительный текст в E-Mail:');

define('ENTRY_MAIL','Отправить письмо с паролем клиенту?');
define('YES','Да');
define('NO','Нет');
define('TEXT_SELECT','Выберите');

define('ICON_CROSS', 'Недействительно');
define('ICON_CURRENT_FOLDER', 'Текущая директория');
define('ICON_DELETE', 'Удалить');
define('ICON_ERROR', 'Ошибка:');
define('ICON_FILE', 'Файл');
define('ICON_FILE_DOWNLOAD', 'Загрузка');
define('ICON_FOLDER', 'Папка');
define('ICON_LOCKED', 'Заблокировать');
define('ICON_PREVIOUS_LEVEL', 'Предыдущий уровень');
define('ICON_PREVIEW', 'Выделить');
define('ICON_STATISTICS', 'Статистика');
define('ICON_SUCCESS', 'Выполнено');
define('ICON_TICK', 'Истина');
define('ICON_UNLOCKED', 'Разблокировать');
define('ICON_WARNING', 'ВНИМАНИЕ');

define('TEXT_RESULT_PAGE', 'Страница %s из %d');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> баннеров)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> стран)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> клиентов)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> валют)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> языков)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> производителей)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> писем)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> заказов)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> статусов заказов)');
define('TEXT_DISPLAY_NUMBER_OF_XSELL_GROUP', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> групп сопутствующих товаров)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_VPE', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> упаковочных единиц)');
define('TEXT_DISPLAY_NUMBER_OF_SHIPPING_STATUS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> статусов доставок)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> товаров)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> ожидаемых товаров)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> отзывов)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> скидок)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> налоговых классов)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> налоговых зон)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> налоговых ставок)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> регионов)');
define('TEXT_DISPLAY_NUMBER_OF_FEATURED', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> рекомендуемых товаров)');

define('PREVNEXT_BUTTON_PREV', 'Предыдущая');
define('PREVNEXT_BUTTON_NEXT', 'Следующая');

define('TEXT_DEFAULT', 'по умолчанию');
define('TEXT_SET_DEFAULT', 'Установить по умолчанию');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* Обязательно</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'Ошибка: К настоящему времени ни одна валюта не была установлена по умолчанию. Пожалуйста, установите одну из них в: Локализация -> Валюта');

define('TEXT_CACHE_CATEGORIES', 'Бокс категорий');
define('TEXT_CACHE_MANUFACTURERS', 'Бокс кроизводителей');
define('TEXT_CACHE_ALSO_PURCHASED', 'Бокс также заказывают'); 

define('TEXT_NONE', '--нет--');
define('TEXT_TOP', 'Начало');

define('ERROR_DESTINATION_DOES_NOT_EXIST', 'Ошибка: Каталог не существует.');
define('ERROR_DESTINATION_NOT_WRITEABLE', 'Ошибка: Каталог защищён от записи, установите необходимые права доступа.');
define('ERROR_FILE_NOT_SAVED', 'Ошибка: Файл не был загружен.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Ошибка: Нельзя закачивать файлы данного типа.');
define('SUCCESS_FILE_SAVED_SUCCESSFULLY', 'Выполнено: Файл успешно загружен.');
define('WARNING_NO_FILE_UPLOADED', 'Предупреждение: Ни одного файла не загружено.');

define('DELETE_ENTRY','Удалить запись?');
define('MENU_PRED','<br><b class="red">Предупреждение:</b><br />');
//Исправить ссылку предупреждения

define('TEXT_PAYMENT_ERROR','Активируйте модули оплаты');
define('TEXT_SHIPPING_ERROR','Активируйте модули доставки');

define('TEXT_NETTO',' налог: ');

define('ENTRY_CID','Номер клиента:');
define('IP','IP заказа:');
define('CUSTOMERS_MEMO','Заметки:');
define('DISPLAY_MEMOS','Показать/написать');
define('TITLE_MEMO','Заметки');
define('ENTRY_LANGUAGE','Язык:');
define('CATEGORIE_NOT_FOUND','Категория не найдена!');

define('IMAGE_RELEASE', 'Активировать сертификат');

define('_JANUARY', 'Январь');
define('_FEBRUARY', 'Февраль');
define('_MARCH', 'Март');
define('_APRIL', 'Апрель');
define('_MAY', 'Май');
define('_JUNE', 'Июнь');
define('_JULY', 'Июль');
define('_AUGUST', 'Август');
define('_SEPTEMBER', 'Сентябрь');
define('_OCTOBER', 'Октябрь');
define('_NOVEMBER', 'Ноябрь');
define('_DECEMBER', 'Декабрь');

define('TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> подарочных сертификатов)');
define('TEXT_DISPLAY_NUMBER_OF_COUPONS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> купонов)');

define('TEXT_VALID_PRODUCTS_LIST', 'Список товаров');
define('TEXT_VALID_PRODUCTS_ID', 'ID товара');
define('TEXT_VALID_PRODUCTS_NAME', 'Название  товара');
define('TEXT_VALID_PRODUCTS_MODEL', 'Модель товара');

define('TEXT_VALID_CATEGORIES_LIST', 'Список категорий');
define('TEXT_VALID_CATEGORIES_ID', 'ID категории');
define('TEXT_VALID_CATEGORIES_NAME', 'Название категории');

define('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT_TITLE', 'Подарочный сертификат');
define('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT_DESC', 'Если Вы не собираетесь отправлять посетителям после регистрации в магазине подарочный сертификат, укажите 0, либо укажите номинал подарочного сертификата, который будут получать посетители после регитсрации в интернет-магазине, например 10.00 или 50.00.');
define('NEW_SIGNUP_DISCOUNT_COUPON_TITLE', 'Код купона');
define('NEW_SIGNUP_DISCOUNT_COUPON_DESC', 'Если Вы не хотите отправлять посетителям после регистрации в магазине купон, не заполняйте данное поле. Если Вы хотите, что б посетитель после регистрации получал купон, укажите код существующего купона, который получит каждый зарегистрированный в интернет-магазине покупатель.');

define('TXT_ALL','Все');

define('BOX_CONFIGURATION_18', 'Vat код');
define('HEADING_TITLE_VAT','Vat код');
define('ENTRY_VAT_ID','Vat код');
define('TEXT_VAT_FALSE','<font color="FF0000">Проверен/Ошибка!</font>');
define('TEXT_VAT_TRUE','<font color="FF0000">Проверен/Всё правильно!</font>');
define('TEXT_VAT_UNKNOWN_COUNTRY','<font color="FF0000">Не проверен/Неизвестная страна!</font>');
define('TEXT_VAT_UNKNOWN_ALGORITHM','<font color="FF0000">Не проверен/Проверка недоступна!</font>');
define('ENTRY_VAT_ID_ERROR', '<font color="FF0000">* Ваш Vat код неправильный!</font>');

define('ERROR_GIF_MERGE','Отсутствует GDlib GIF-поддержка, соеденить картинки неудалось');
define('ERROR_GIF_UPLOAD','Отсутствует GDlib Gif-поддержка, обработка картинки GIF неудалась');

define('TEXT_REFERER','Реферер: ');

define('IMAGE_ICON_INFO','');
define('ERROR_IMAGE_DIRECTORY_CREATE', 'Ошибка: Ошибка при создании директории ');
define('TEXT_IMAGE_DIRECTORY_CREATE', 'Информация: Создана директория ');

define('BOX_EASY_POPULATE','Excel импорт/экспорт');
define('BOX_CATALOG_QUICK_UPDATES', 'Изменение цен');

define('BOX_CATALOG_LATEST_NEWS', 'Новости');
define('IMAGE_NEW_NEWS_ITEM', 'Добавить новость');

define('TABLE_HEADING_CUSTOMERS', 'Последние покупатели');
define('TABLE_HEADING_NEWS', 'Последние новости');
define('TABLE_HEADING_THEMES', 'Последние шаблоны');
define('TABLE_HEADING_CACHE', 'Контроль кэша');
define('TABLE_CACHE_SIZE', 'Общий размер кэша:');
define('TABLE_CACHE_CLEAN', 'Очистить кэш');

define('TABLE_HEADING_LASTNAME', 'Фамилия');
define('TABLE_HEADING_FIRSTNAME', 'Имя');
define('TABLE_HEADING_DATE', 'Дата');

define('TABLE_HEADING_ORDERS', 'Последние заказы');
define('TABLE_HEADING_CUSTOMER', 'Покупатель');
define('TABLE_HEADING_NUMBER', 'Номер заказа');
define('TABLE_HEADING_ORDER_TOTAL', 'Сумма');
define('TABLE_HEADING_STATUS', 'Статус');

define('TABLE_HEADING_SUMMARY_PRODUCTS', 'Последние товары');
define('TABLE_HEADING_PRODUCT_NAME', 'Товары');
define('TABLE_HEADING_PRODUCT_PRICE', 'Стоимость');

define('BOX_TOOLS_RECOVER_CART', 'Незавершённые заказы');

define('BOX_FEATURED', 'Рекомендуемые товары');

define('TEXT_HEADER_DEFAULT','Главная');
define('TEXT_HEADER_SUPPORT','Поддержка');
define('TEXT_HEADER_SHOP','Магазин');
define('TEXT_HEADER_LOGOFF','Выйти');
define('TEXT_HEADER_HELP','Помощь');
define('TEXT_HEADER_HELP_F','Справка');

define('BOX_CACHE_FILES', 'Контроль кэша');

define('BOX_HEADING_ARTICLES', 'Статьи');
define('BOX_TOPICS_ARTICLES', 'Статьи/Разделы');
define('BOX_ARTICLES_CONFIG', 'Настройка');
define('BOX_ARTICLES_AUTHORS', 'Авторы');
define('BOX_ARTICLES_REVIEWS', 'Отзывы'); 
define('BOX_ARTICLES_XSELL', 'Товары-Статьи');
define('IMAGE_NEW_TOPIC', 'Новый раздел');
define('IMAGE_NEW_ARTICLE', 'Новая статья');
define('TEXT_DISPLAY_NUMBER_OF_AUTHORS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> авторов)'); 

define('TEXT_SUMMARY_STAT','Статистика');
define('TEXT_SUMMARY_STAT_TEXT','Статистика продаж');

define('TEXT_SUMMARY_CUSTOMERS','Покупатели');
define('TEXT_SUMMARY_ORDERS','Заказы');
define('TEXT_SUMMARY_PRODUCTS','Товары');
define('TEXT_SUMMARY_NEWS','Новости');
define('TEXT_SUMMARY_CACHE','Кэш');

define('TEXT_SUMMARY_MODULES','Модули');
define('BOX_SALES_REPORT2','Статистика продаж 2');

define('TEXT_PHP_MAILER_ERROR','Не удалось отправить email.<br />');
define('TEXT_PHP_MAILER_ERROR1','Ошибка: ');
define('BOX_TOOLS_EMAIL_MANAGER','Шаблоны писем');
define('BOX_CATEGORY_SPECIALS', 'Категории со скидками');
define('TEXT_DISPLAY_NUMBER_OF_SPECIAL_CATEGORY', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> категорий со скидками)');
define('IMAGE_ICON_STATUS_GREEN', 'Активна');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'Активизировать');
define('IMAGE_ICON_STATUS_RED', 'Неактивна');
define('IMAGE_ICON_STATUS_RED_LIGHT', 'Сделать неактивной');
define('TEXT_IMAGE_NONEXISTENT','Нет картинки!');
define('TEXT_TOGGLE_EDITOR', 'Включить/Выключить HTML-редактор');
define('WARNING_MODULES_SORT_ORDER','ВНИМАНИЕ: Значение опции порядок сортировки у модулей не должно повторяться!');

define('BOX_PRODUCTS_OPTIONS', 'Названия');

define('BOX_MODULES_SHIP2PAY','Доставка-оплата');
define('TEXT_DISPLAY_NUMBER_OF_PAYMENTS','Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> зависимостей)');

define('BOX_PRODUCT_EXTRA_FIELDS','Дополнительные поля товаров');
define('TEXT_EDIT_FIELDS','Редактировать дополнительные поля товаров.');
define('TEXT_ADD_FIELDS','Добавить дополнительные поля товаров.');

define('BOX_CATALOG_FAQ', 'Вопросы и ответы');

require_once(DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin']. '/affiliate.php');

define('BOX_HEADING_CUSTOMER_EXTRA_FIELDS', 'Дополнительные поля покупателей');
define('ENTRY_EXTRA_FIELDS_ERROR', 'Поле %s должно содержать как минимум %d символов');
define('TEXT_DISPLAY_NUMBER_OF_FIELDS', 'Показано <b>%d</b> - <b>%d</b> (всего <b>%d</b> полей)');
define('BOX_HELP','Помощь');
define('BOX_THEMES','Шаблоны');
define('BOX_THEMES_URL','Шаблоны');
define('BOX_THEMES_ADMIN','Админка');
define('BOX_ORDERS_SEND','Проверка заказов');
define('TEXT_THEMES_MENU','Шаблоны');
define('BOX_THEMES_MENU','Шаблоны');
define('TEXT_THEMES','Шаблоны');
define('TEXT_CACHE','Кэш');

define('TEXT_THEMES_EDIT','Редактор шаблонов');
define('BOX_TEXT_THEMES_EDIT','Редактор шаблонов');

define('MENU_QUICK','Быстрый вызов');
define('MENU_QUICK_CATEGORY','Новая категория');
define('MENU_QUICK_PRODUCT','Новый продукт');

define('TEXT_YES','Да');
define('TEXT_NO','Нет');
define('BOX_PARAMETERS', 'Параметры');
define('BOX_PARAMETERS_EXPORT', 'Импорт/экспорт параметров');

define('TEXT_EDIT_E', 'Редактировать в визуальном HTML редакторе');
define('TEXT_EDIT_CODE', 'Посмотреть код');
define('TEXT_ADMIN_PANEL', 'Админка');

function os_date_long_translate($date_string) 
{
   $eng = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
   $loc = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье", "Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
   return str_replace($eng, $loc, $date_string);
}

function os_date_raw($date, $reverse = false) 
{
   if ($reverse) 
   {
       return substr($date, 3, 2) . substr($date, 0, 2) . substr($date, 6, 4);
   } 
   else 
   {
       return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2); 
   }
}

define('TEXT_MEMORY_USAGE', 'Потребление памяти: ');

define('PARSE_TIME','Время генерации:');
define('QUERIES','запросов');

define('TEXT_SETTING', 'Настройки');
define('TEXT_CLOSE', 'Закрыть');
define('TEXT_SAVE', 'Сохранить');

define('TEXT_ERROR_PERMISSION', 'Доступ запрещен!');



?>