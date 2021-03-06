<?php
/*
	Plugin Name: Обновление БД с 1.1.2 до 1.1.3
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description:
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Updates
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

function update_112_to_113_install()
{
	global $messageStack;

	os_db_query("ALTER TABLE ".DB_PREFIX."products ADD yml_manufacturer_warranty int(1) default '0';");
	os_db_query("ALTER TABLE ".DB_PREFIX."products ADD yml_manufacturer_warranty_text varchar(255) default '';");

	os_db_query("ALTER TABLE ".DB_PREFIX."orders_status ADD orders_status_color varchar(7) default '';");

	os_db_query("ALTER TABLE ".DB_PREFIX."orders ADD order_hash CHAR(32);");

	os_db_query("UPDATE ".DB_PREFIX."products SET products_search = 0");

	os_db_query("ALTER TABLE `".DB_PREFIX."products` ADD  `price_currency_code` VARCHAR( 10 ) NOT NULL DEFAULT ''");

	os_db_query("ALTER TABLE `".DB_PREFIX."reviews_description` ADD reviews_text_admin text NOT NULL DEFAULT ''");

	os_db_query("ALTER TABLE ".DB_PREFIX."admin_access ADD cron int(1) NOT NULL default '1';");

	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'USE_ORDERS_HASH', 'false', 17, 19, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

	os_db_query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."cron_tasks` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` varchar(250) DEFAULT NULL,
		`class` varchar(32) DEFAULT NULL,
		`function` varchar(32) DEFAULT NULL,
		`period` int(11) DEFAULT NULL,
		`date_last_run` timestamp NULL DEFAULT NULL,
		`status` tinyint(1) DEFAULT NULL,
		`new` tinyint(1) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id`),
		KEY `period` (`period`),
		KEY `date_last_run` (`date_last_run`),
		KEY `status` (`status`),
		KEY `new` (`new`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;");

	os_db_query("CREATE TABLE ".DB_PREFIX."admin_setting (
		`id` int(11) not null auto_increment,
		`group` varchar(15) NOT NULL DEFAULT '',
		`name` varchar(25) NOT NULL DEFAULT '',
		`value` varchar(255) NOT NULL DEFAULT '',
		`setting_type` TINYINT(5) NOT NULL,
		PRIMARY KEY (`id`,`name`),
		KEY `i_name` (`name`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

	os_db_query("INSERT INTO `".DB_PREFIX."admin_setting` VALUES
	(1, 'index2', 'welcome', '1', '1'),
	(2, 'index2', 'orders', '1', '1'),
	(3, 'index2', 'products', '1', '1'),
	(4, 'index2', 'reviews', '1', '1'),
	(5, 'index2', 'cache', '1', '1'),
	(6, 'index2', 'notes', '1', '1'),
	(7, 'index2', 'customers', '1', '1'),
	(8, 'index2', 'stats', '1', '1'),
	(9, 'index2', 'month_stats', '1', '1');");

	$messageStack->add_session('База успешно обновлена!<br />Выключите и удалите данный плагин!', 'success');
	os_redirect(FILENAME_PLUGINS.'?module=update_112_to_113');
}