<?php
/*
	Plugin Name: Обновление БД с 1.0.1 до 1.0.2
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description:
	Author: OSC-CMS
	Author URI: http://osc-cms.com
	Plugin Group: Updates
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

function update_101_to_102_install()
{
	global $messageStack;

	os_db_query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AVISOSMS_EMAIL', '', 17, 20, NULL, '0000-00-00 00:00:00', NULL, NULL);");
	os_db_query("ALTER TABLE ".DB_PREFIX."orders_products_attributes ADD attributes_model varchar(255) NULL DEFAULT '';");

	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_NEWS', 'true', 30, 32, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_FEATURES', 'true', 30, 33, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_NEW', 'true', 30, 34, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'VIS_MAIN_UPCOMING', 'true', 30, 35, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

	$messageStack->add_session('База успешно обновлена!', 'success');
	os_redirect(FILENAME_PLUGINS.'?module=update_101_to_102');
}
?>