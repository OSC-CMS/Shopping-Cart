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

	os_db_query("UPDATE ".DB_PREFIX."products SET products_search = 0");

	os_db_query("ALTER TABLE `".DB_PREFIX."products` ADD  `price_currency_code` VARCHAR( 10 ) NOT NULL DEFAULT ''");

	os_db_query("ALTER TABLE `".DB_PREFIX."reviews_description` ADD reviews_text_admin text NOT NULL DEFAULT ''");

	$messageStack->add_session('База успешно обновлена!<br />Выключите и удалите данный плагин!', 'success');
	os_redirect(FILENAME_PLUGINS.'?module=update_112_to_113');
}