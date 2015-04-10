<?php
/*
	Plugin Name: Обновление БД с 1.1.1 до 1.1.2
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description:
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Updates
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

function update_111_to_112_install()
{
	global $messageStack;

	//os_db_query("ALTER TABLE `".DB_PREFIX."admin_access` ADD `content` int(1) NOT NULL default '1';");

	os_db_query("ALTER TABLE `".DB_PREFIX."menu` CHANGE `menu_position` `menu_position` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
	os_db_query("ALTER TABLE `".DB_PREFIX."menu` CHANGE `menu_group_id` `menu_group_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");

	os_db_query("ALTER TABLE `".DB_PREFIX."menu_lang` CHANGE `lang_type` `lang_type` INT( 11 ) NOT NULL DEFAULT '0';");
	os_db_query("ALTER TABLE `".DB_PREFIX."menu_lang` CHANGE `lang_type_id` `lang_type_id` INT( 11 ) NOT NULL DEFAULT '0';");

	os_db_query("UPDATE ".DB_PREFIX."products SET products_search = 0");

	$messageStack->add_session('База успешно обновлена!<br />Выключите и удалите данный плагин!', 'success');
	os_redirect(FILENAME_PLUGINS.'?module=update_111_to_112');
}