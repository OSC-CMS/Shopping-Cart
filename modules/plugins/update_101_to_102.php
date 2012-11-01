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

	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES ('', 'AVISOSMS_EMAIL', '', 17, 20, NULL, '0000-00-00 00:00:00', NULL, NULL);");
	
	$messageStack->add_session('База успешно обновлена!', 'success');
	os_redirect(FILENAME_PLUGINS.'?module=update_101_to_102');
}
?>