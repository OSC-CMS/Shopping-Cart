<?php
/*
	Plugin Name: Обновление БД с 1.0.1 до 1.1.0
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description:
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Updates
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

function update_101_to_110_install()
{
	global $messageStack;

	os_db_query("ALTER TABLE ".DB_PREFIX."orders_products_attributes ADD attributes_model varchar(255) NULL DEFAULT '';");
	os_db_query("ALTER TABLE ".DB_PREFIX."articles_description ADD articles_description_short text NULL DEFAULT '';");

	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_NEWS', 'true', 30, 32, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_FEATURES', 'true', 30, 33, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_NEW', 'true', 30, 34, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_UPCOMING', 'true', 30, 35, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

	os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` DROP category_specials");
	os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` DROP authors");
	os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` DROP orders_edit");
	os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` DROP create_account");
	os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` DROP cache");
	os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` DROP print_packingslip");
	os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` DROP print_order");

	os_db_query("ALTER TABLE ".DB_PREFIX."admin_access ADD sms int(1) NOT NULL default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."admin_access ADD menu int(1) NOT NULL default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."admin_access ADD cartet int(1) NOT NULL default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."admin_access ADD `update` int(1) NOT NULL default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."admin_access ADD `content` int(1) NOT NULL default '1';");

	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 327;");
	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 328;");
	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 329;");
	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 371;");
	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 22;");
	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 395;");
	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 122;");
	os_db_query("DELETE FROM `".DB_PREFIX."configuration` WHERE `configuration_id` = 368;");

	os_db_query("ALTER TABLE `".DB_PREFIX."orders` DROP cc_type");
	os_db_query("ALTER TABLE `".DB_PREFIX."orders` DROP cc_owner");
	os_db_query("ALTER TABLE `".DB_PREFIX."orders` DROP cc_number");
	os_db_query("ALTER TABLE `".DB_PREFIX."orders` DROP cc_expires");
	os_db_query("ALTER TABLE `".DB_PREFIX."orders` DROP cc_start");
	os_db_query("ALTER TABLE `".DB_PREFIX."orders` DROP cc_issue");
	os_db_query("ALTER TABLE `".DB_PREFIX."orders` DROP cc_cvv");

	os_db_query("ALTER TABLE ".DB_PREFIX."products ADD products_search int(1) default '0';");
	os_db_query("ALTER TABLE ".DB_PREFIX."products ADD yml_available int(1) default '1';");

	os_db_query("ALTER TABLE  ".DB_PREFIX."latest_news ADD news_image VARCHAR( 255 ) NOT NULL DEFAULT '';");

	os_db_query("ALTER TABLE ".DB_PREFIX."products_extra_fields ADD products_extra_fields_group INT( 11 ) NOT NULL AFTER products_extra_fields_status;");

	os_db_query("CREATE TABLE ".DB_PREFIX."products_extra_fields_groups (
		extra_fields_groups_id int(11) not null auto_increment,
		extra_fields_groups_order int(3) default '0' not null ,
		extra_fields_groups_status tinyint(1) default '1' not null ,
		PRIMARY KEY (extra_fields_groups_id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

	os_db_query("CREATE TABLE ".DB_PREFIX."products_extra_fields_groups_desc (
		extra_fields_groups_desc_id int(11) not null auto_increment,
		extra_fields_groups_id int(11) not null,
		extra_fields_groups_name varchar(255) not null ,
		extra_fields_groups_languages_id int(11) default '0' not null ,
		PRIMARY KEY (extra_fields_groups_desc_id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

	os_db_query("CREATE TABLE ".DB_PREFIX."admin_notes (
		id int NOT NULL auto_increment,
		note text NOT NULL,
		customer int(11) NOT NULL,
		date_added datetime NOT NULL default '0000-00-00 00:00:00',
		status tinyint(1) NOT NULL,
		PRIMARY KEY (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

	os_db_query("CREATE TABLE ".DB_PREFIX."menu (
		`menu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`menu_parent_id` int(11) unsigned NOT NULL DEFAULT '0',
		`menu_url` varchar(255) NOT NULL DEFAULT '',
		`menu_class` varchar(255) NOT NULL DEFAULT '',
		`menu_class_icon` varchar(255) NOT NULL DEFAULT '',
		`menu_position` int(11) unsigned NOT NULL DEFAULT '0',
		`menu_group_id` int(11) unsigned NOT NULL DEFAULT '0',
		`menu_status` tinyint(3) NOT NULL,
	PRIMARY KEY (`menu_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=110 ;");

	os_db_query("CREATE TABLE ".DB_PREFIX."menu_group (
		`group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`group_status` tinyint(3) NOT NULL,
	PRIMARY KEY (`group_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;");

	os_db_query("CREATE TABLE ".DB_PREFIX."menu_lang (
		`lang_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`lang_title` varchar(255) NOT NULL DEFAULT '',
		`lang_type` tinyint(3) NOT NULL DEFAULT '0',
		`lang_type_id` int(11) NOT NULL DEFAULT '0',
		`lang_lang` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`lang_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=117 ;");

	os_db_query("INSERT INTO ".DB_PREFIX."menu (`menu_id`, `menu_parent_id`, `menu_url`, `menu_class`, `menu_class_icon`, `menu_position`, `menu_group_id`, `menu_status`) VALUES
	(1, 100, 'menu.php', '', '', 3, 1, 1),
	(2, 98, 'categories.php', '', '', 1, 1, 1),
	(3, 98, 'products_options.php', '', '', 5, 1, 1),
	(4, 3, 'products_attributes.php', '', '', 1, 1, 1),
	(5, 3, 'new_attributes.php', '', '', 2, 1, 1),
	(6, 98, 'manufacturers.php', '', '', 4, 1, 1),
	(7, 98, 'reviews.php', '', '', 6, 1, 1),
	(8, 104, 'specials.php', '', '', 1, 1, 1),
	(9, 98, 'featured.php', '', '', 2, 1, 1),
	(10, 98, 'products_expected.php', '', '', 3, 1, 1),
	(11, 101, 'articles.php', '', '', 2, 1, 1),
	(12, 11, 'articles_config.php', '', '', 2, 1, 1),
	(13, 11, 'articles_xsell.php', '', '', 1, 1, 1),
	(14, 103, 'orders.php', '', '', 1, 1, 1),
	(15, 105, 'customers.php', '', '', 1, 1, 1),
	(16, 105, 'customers_status.php', '', '', 3, 1, 1),
	(17, 103, 'orders_status.php', '', '', 2, 1, 1),
	(18, 99, 'configuration.php?gID=1', '', '', 1, 1, 1),
	(19, 99, 'configuration.php?gID=2', '', '', 8, 1, 1),
	(20, 99, 'configuration.php?gID=3', '', '', 7, 1, 1),
	(21, 99, 'configuration.php?gID=4', '', '', 13, 1, 1),
	(22, 105, 'configuration.php?gID=5', '', '', 4, 1, 1),
	(23, 48, 'configuration.php?gID=7', '', '', 6, 1, 1),
	(24, 99, 'configuration.php?gID=8', '', '', 15, 1, 1),
	(25, 99, 'configuration.php?gID=9', '', '', 9, 1, 1),
	(26, 55, 'configuration.php?gID=10', '', '', 2, 1, 1),
	(27, 99, 'configuration.php?gID=11', '', '', 12, 1, 1),
	(28, 55, 'error_log.php', '', '', 3, 1, 1),
	(109, 0, 'shop_content.php?coID=7', '', '', 2, 2, 1),
	(30, 99, 'configuration.php?gID=12', '', '', 14, 1, 1),
	(31, 99, 'configuration.php?gID=13', '', '', 3, 1, 1),
	(32, 55, 'configuration.php?gID=14', '', '', 5, 1, 1),
	(33, 99, 'configuration.php?gID=15', '', '', 4, 1, 1),
	(34, 99, 'configuration.php?gID=22', '', '', 5, 1, 1),
	(35, 99, 'configuration.php?gID=24', '', '', 10, 1, 1),
	(36, 99, 'configuration.php?gID=31', '', '', 6, 1, 1),
	(37, 48, 'shipping_status.php', '', '', 5, 1, 1),
	(38, 103, 'products_vpe.php', '', '', 3, 1, 1),
	(39, 89, 'campaigns.php', '', '', 9, 1, 1),
	(40, 98, 'cross_sell_groups.php', '', '', 7, 1, 1),
	(41, 98, 'configuration.php?gID=23', '', '', 11, 1, 1),
	(42, 99, 'configuration.php?gID=27', '', '', 11, 1, 1),
	(43, 99, 'configuration.php?gID=17', '', '', 2, 1, 1),
	(44, 100, 'themes.php', '', '', 1, 1, 1),
	(45, 100, 'configuration.php?gID=30', '', '', 4, 1, 1),
	(46, 100, 'email_manager.php', '', '', 5, 1, 1),
	(47, 100, 'themes_edit.php', '', '', 2, 1, 1),
	(48, 0, 'modules.php', '', 'icon-th-large', 11, 1, 1),
	(49, 48, 'modules.php?set=payment', '', '', 2, 1, 1),
	(50, 48, 'modules.php?set=shipping', '', '', 1, 1, 1),
	(51, 48, 'modules.php?set=ordertotal', '', '', 3, 1, 1),
	(52, 48, 'ship2pay.php', '', '', 4, 1, 1),
	(53, 0, 'plugins.php', '', 'icon-puzzle-piece', 10, 1, 1),
	(108, 0, '/', '', '', 1, 2, 1),
	(55, 0, '', '', 'icon-wrench', 13, 1, 1),
	(56, 55, 'backup.php', '', '', 1, 1, 1),
	(57, 98, 'product_extra_fields.php', '', '', 9, 1, 1),
	(58, 105, 'customer_extra_fields.php', '', '', 2, 1, 1),
	(59, 101, 'content_manager.php', '', '', 4, 1, 1),
	(60, 105, 'module_newsletter.php', '', '', 6, 1, 1),
	(61, 55, 'server_info.php', '', '', 4, 1, 1),
	(62, 101, 'latest_news.php', '', '', 1, 1, 1),
	(63, 101, 'faq.php', '', '', 3, 1, 1),
	(64, 55, 'whos_online.php', '', '', 6, 1, 1),
	(65, 98, 'easypopulate.php', '', '', 12, 1, 1),
	(66, 98, 'csv_backend.php', '', '', 13, 1, 1),
	(67, 98, 'quick_updates.php', '', '', 10, 1, 1),
	(68, 102, 'recover_cart_sales.php', '', '', 1, 1, 1),
	(69, 0, '', '', 'icon-flag', 12, 1, 1),
	(70, 69, 'languages.php', '', '', 1, 1, 1),
	(71, 69, 'currencies.php', '', '', 2, 1, 1),
	(72, 69, 'countries.php', '', '', 3, 1, 1),
	(73, 69, 'zones.php', '', '', 4, 1, 1),
	(74, 69, 'geo_zones.php', '', '', 5, 1, 1),
	(75, 69, 'tax_classes.php', '', '', 6, 1, 1),
	(76, 69, 'tax_rates.php', '', '', 7, 1, 1),
	(77, 104, 'coupon_admin.php', '', '', 2, 1, 1),
	(78, 104, 'coupon_admin.php', '', '', 3, 1, 0),
	(79, 104, 'gv_queue.php', '', '', 6, 1, 1),
	(80, 104, 'gv_mail.php', '', '', 4, 1, 1),
	(81, 104, 'gv_sent.php', '', '', 5, 1, 1),
	(82, 102, 'stats_products_viewed.php', '', '', 5, 1, 1),
	(83, 102, 'stats_products_purchased.php', '', '', 2, 1, 1),
	(84, 105, 'stats_customers.php', '', '', 5, 1, 1),
	(85, 102, 'stats_sales_report.php', '', '', 3, 1, 1),
	(86, 102, 'stats_sales_report2.php', '', '', 4, 1, 1),
	(87, 89, 'stats_campaigns.php', '', '', 10, 1, 1),
	(88, 98, 'stats_stock_warning.php', '', '', 8, 1, 1),
	(89, 0, '', '', 'icon-briefcase', 9, 1, 1),
	(90, 89, 'configuration.php?gID=28', '', '', 1, 1, 1),
	(91, 89, 'affiliate_affiliates.php', '', '', 3, 1, 1),
	(92, 89, 'affiliate_banners.php', '', '', 2, 1, 1),
	(93, 89, 'affiliate_clicks.php', '', '', 7, 1, 1),
	(94, 89, 'affiliate_contact.php', '', '', 8, 1, 1),
	(95, 89, 'affiliate_payment.php', '', '', 6, 1, 1),
	(96, 89, 'affiliate_sales.php', '', '', 5, 1, 1),
	(97, 89, 'affiliate_summary.php', '', '', 4, 1, 1),
	(98, 0, '', '', 'icon-list', 1, 1, 1),
	(99, 0, '', '', 'icon-cogs', 5, 1, 1),
	(100, 0, '', '', 'icon-desktop', 6, 1, 1),
	(101, 0, '', '', 'icon-reorder', 7, 1, 1),
	(102, 0, '', '', 'icon-bar-chart', 8, 1, 1),
	(103, 0, '', '', 'icon-money', 2, 1, 1),
	(104, 0, '', '', 'icon-ticket', 4, 1, 1),
	(105, 0, '', '', 'icon-group', 3, 1, 1),
	(107, 0, 'shop_content.php?coID=4', '', '', 3, 2, 0),
	(106, 53, 'plugins.php', '', '', 1, 1, 1),
	(110, 55, 'sms.php', '', '', 1, 1, 1),
	(111, 103, 'configuration.php?gID=32', '', '', 4, 1, 1),
	(112, 114, 'cartet.php', '', '', 2, 1, 1),
	(113, 114, 'update.php', '', '', 1, 1, 1),
	(114, 0, '', '', 'icon-cog', 14, 1, 1);");

	os_db_query("INSERT INTO ".DB_PREFIX."menu_group (`group_id`, `group_status`) VALUES
	(1, 1),
	(2, 1);");

	os_db_query("INSERT INTO ".DB_PREFIX."menu_lang (`lang_id`, `lang_title`, `lang_type`, `lang_type_id`, `lang_lang`) VALUES
	(1, 'Админка', 1, 1, 1),
	(2, 'Меню', 0, 1, 1),
	(3, 'Категории и товары', 0, 2, 1),
	(4, 'Атрибуты', 0, 3, 1),
	(5, 'Значения', 0, 4, 1),
	(6, 'Установка', 0, 5, 1),
	(7, 'Производители', 0, 6, 1),
	(8, 'Отзывы', 0, 7, 1),
	(9, 'Скидки', 0, 8, 1),
	(10, 'Рекомендуемые товары', 0, 9, 1),
	(11, 'Ожидаемые товары', 0, 10, 1),
	(12, 'Статьи', 0, 11, 1),
	(13, 'Настройка', 0, 12, 1),
	(14, 'Товары-Статьи', 0, 13, 1),
	(15, 'Заказы', 0, 14, 1),
	(16, 'Покупатели', 0, 15, 1),
	(17, 'Группы клиентов', 0, 16, 1),
	(18, 'Статусы заказа', 0, 17, 1),
	(19, 'Настройки', 0, 18, 1),
	(20, 'Минимальные', 0, 19, 1),
	(21, 'Максимальные', 0, 20, 1),
	(22, 'Картинки', 0, 21, 1),
	(23, 'Данные покупателя', 0, 22, 1),
	(24, 'Доставка/Упаковка', 0, 23, 1),
	(25, 'Вывод товара', 0, 24, 1),
	(26, 'Склад', 0, 25, 1),
	(27, 'Отладка', 0, 26, 1),
	(28, 'Кэш', 0, 27, 1),
	(29, 'Логи ошибок', 0, 28, 1),
	(31, 'Настройка E-Mail', 0, 30, 1),
	(32, 'Скачивание', 0, 31, 1),
	(33, 'GZip компрессия', 0, 32, 1),
	(34, 'Сессии', 0, 33, 1),
	(35, 'Настройки поиска', 0, 34, 1),
	(36, 'Изменение цен', 0, 35, 1),
	(37, 'ЧПУ URL', 0, 36, 1),
	(38, 'Время доставки', 0, 37, 1),
	(39, 'Единица упаковки', 0, 38, 1),
	(40, 'Кампании', 0, 39, 1),
	(41, 'Сопутствующие', 0, 40, 1),
	(42, 'Яндекс-Маркет', 0, 41, 1),
	(43, 'Тех. обслуживание', 0, 42, 1),
	(44, 'Разное', 0, 43, 1),
	(45, 'Шаблоны', 0, 44, 1),
	(46, 'Настройка блоков', 0, 45, 1),
	(47, 'Шаблоны писем', 0, 46, 1),
	(48, 'Редактор шаблонов', 0, 47, 1),
	(49, 'Модули', 0, 48, 1),
	(50, 'Модули оплаты', 0, 49, 1),
	(51, 'Модули доставки', 0, 50, 1),
	(52, 'Модули итого', 0, 51, 1),
	(53, 'Доставка-оплата', 0, 52, 1),
	(54, 'Плагины', 0, 53, 1),
	(109, 'Меню сайта', 1, 2, 1),
	(56, 'Инструменты', 0, 55, 1),
	(57, 'Резервное копирование', 0, 56, 1),
	(58, 'Дополнительные поля товаров', 0, 57, 1),
	(59, 'Дополнительные поля покупателей', 0, 58, 1),
	(60, 'Информационные страницы', 0, 59, 1),
	(61, 'Рассылка', 0, 60, 1),
	(62, 'Сервер инфо', 0, 61, 1),
	(63, 'Новости', 0, 62, 1),
	(64, 'Вопросы и ответы', 0, 63, 1),
	(65, 'Кто в оn-line?', 0, 64, 1),
	(66, 'Excel импорт/экспорт', 0, 65, 1),
	(67, 'CSV импорт/Экспорт', 0, 66, 1),
	(68, 'Изменение цен', 0, 67, 1),
	(69, 'Незавершённые заказы', 0, 68, 1),
	(70, 'Локализация', 0, 69, 1),
	(71, 'Языки', 0, 70, 1),
	(72, 'Валюты', 0, 71, 1),
	(73, 'Страны', 0, 72, 1),
	(74, 'Регионы', 0, 73, 1),
	(75, 'Географические зоны', 0, 74, 1),
	(76, 'Виды налогов', 0, 75, 1),
	(77, 'Ставки налогов', 0, 76, 1),
	(78, 'Купоны', 0, 77, 1),
	(79, 'Управление купонами', 0, 78, 1),
	(80, 'Активация сертификатов', 0, 79, 1),
	(81, 'Отправить сертификат', 0, 80, 1),
	(82, 'Отправленные сертификаты', 0, 81, 1),
	(83, 'Просмотренные товары', 0, 82, 1),
	(84, 'Заказанные товары', 0, 83, 1),
	(85, 'Лучшие клиенты', 0, 84, 1),
	(86, 'Статистика продаж', 0, 85, 1),
	(87, 'Статистика продаж 2', 0, 86, 1),
	(88, 'Отчёт по кампаниям', 0, 87, 1),
	(89, 'Склад', 0, 88, 1),
	(90, 'Партнёрка', 0, 89, 1),
	(91, 'Настройки', 0, 90, 1),
	(92, 'Партнёры', 0, 91, 1),
	(93, 'Баннеры', 0, 92, 1),
	(94, 'Клики', 0, 93, 1),
	(95, 'Обратная связь', 0, 94, 1),
	(96, 'Выплаты', 0, 95, 1),
	(97, 'Продажи', 0, 96, 1),
	(98, 'Общая статистика', 0, 97, 1),
	(99, 'Каталог', 0, 98, 1),
	(100, 'Настройки', 0, 99, 1),
	(101, 'Шаблоны', 0, 100, 1),
	(102, 'Контент', 0, 101, 1),
	(103, 'Статистика', 0, 102, 1),
	(104, 'Заказы', 0, 103, 1),
	(105, 'Акции', 0, 104, 1),
	(106, 'Покупатели', 0, 105, 1),
	(107, 'Плагины', 0, 106, 1),
	(111, 'О магазине', 0, 107, 1),
	(113, 'Главная', 0, 108, 1),
	(115, 'Свяжитесь с нами', 0, 109, 1),
	(117, 'СМС уведомления', 0, 110, 1),
	(118, 'Быстрый заказ', 0, 111, 1),
	(119, 'О CartET', 0, 114, 1),
	(120, 'Обновления', 0, 113, 1),
	(121, 'О CartET', 0, 112, 1);");

	os_db_query("CREATE TABLE ".DB_PREFIX."sms (
		`id` int(11) not null auto_increment,
		`name` varchar(255) not null default '',
		`login` varchar(255) not null default '',
		`password` varchar(255) not null default '',
		`password_md5` tinyint(1) NOT NULL,
		`api_id` varchar(255) not null default '',
		`api_key` varchar(255) not null default '',
		`title` varchar(255) not null default '',
		`phone` varchar(255) not null default '',
		`status` varchar(255) not null default '',
		`url` text not null default '',
		PRIMARY KEY (`id`, `name`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

	os_db_query("CREATE TABLE ".DB_PREFIX."sms_setting (
		`sms_id` int(11) not null auto_increment,
		`sms_status` int(1) not null,
		`sms_default_id` int(1) not null,
		`sms_order_admin` int(1) not null,
		`sms_order` int(1) not null,
		`sms_order_change` int(1) not null,
		`sms_register` int(1) not null,
		`sms_fast_order` int(1) not null,
		PRIMARY KEY (`sms_id`, `sms_default_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

	os_db_query("INSERT INTO `".DB_PREFIX."sms_setting` VALUES(1, 0, 0, 1, 1, 1, 0, 0);");

	os_db_query("INSERT INTO `".DB_PREFIX."sms` (`id`, `name`, `login`, `password`, `password_md5`, `api_id`, `api_key`, `title`, `phone`, `status`, `url`) VALUES
	(1, 'avisosms.ru', '', '', 0, '', '', 'cartet', '', '', 'api.avisosms.ru/sms/get/?username={login}&password={password}&destination_address={phone}&source_address={title}&message={text}'),
	(2, 'sms.ru', '', '', 0, '', '', 'cartet', '', '', 'sms.ru/sms/send?api_id={api_id}&to={phone}&text={text}&from={title}'),
	(3, 'infosmska.ru', '', '', 0, '', '', 'cartet', '', '', 'api.infosmska.ru/interfaces/SendMessages.ashx?login={login}&pwd={password}&sender={title}&phones={phone}&message={text}'),
	(4, 'sms-sending.ru', '', '', 0, '', '', 'cartet', '', '', 'lcab.sms-sending.ru/lcabApi/sendSms.php?login={login}&password={password}&txt={text}&to={phone}'),
	(5, 'bytehand.com', '', '', 0, '', '', 'cartet', '', '', 'bytehand.com:3800/send?id={api_id}&key={api_key}&to={phone}&partner=cartet&from={title}&text={text}'),
	(6, 'smsaero.ru', '', '', 1, '', '', 'cartet', '', '', 'gate.smsaero.ru/send/?user={login}&password={password}&to={phone}&text={text}&from={title}'),
	(7, 'prostor-sms.ru', '', '', 0, '', '', 'cartet', '', '', 'api.prostor-sms.ru/messages/v2/send/?phone=%2B{phone}&text={text}&login={login}&password={password}&sender={title}');");

	os_db_query("CREATE TABLE ".DB_PREFIX."sms_notes (
		id int NOT NULL auto_increment,
		order_id int(11) NOT NULL,
		note text NOT NULL default '',
		phone varchar(255) NOT NULL default '',
		date_added datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY (id),
		KEY order_id (order_id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

	$messageStack->add_session('База успешно обновлена!<br />Выключите и удалите данный плагин!', 'success');
	os_redirect(FILENAME_PLUGINS.'?module=update_101_to_110');
}
?>