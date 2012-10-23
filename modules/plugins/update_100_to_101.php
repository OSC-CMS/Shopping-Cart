<?php
/*
	Plugin Name: Обновление БД с 1.0.0 до 1.0.1
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description:
	Author: OSC-CMS
	Author URI: http://osc-cms.com
	Plugin Group: Updates
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

function update_100_to_101_install()
{
	global $messageStack;

	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'ACCOUNT_LAST_NAME', 'true', 5, 1, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'SEO_URL_NEWS_GENERATOR', 'false', 31, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'SEO_URL_ARTICLES_GENERATOR', 'false', 31, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'ENTRY_USERNAME_MIN_LENGTH', '3', 2, 3, NULL, '0000-00-00 00:00:00', NULL, NULL);");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'ACCOUNT_PROFILE', 'false', 5, 14, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'ACCOUNT_USER_NAME', 'false', 5, 15, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'ACCOUNT_USER_NAME_REG', 'false', 5, 16, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'USE_IMAGE_SUBMIT', 'false', 17, 19, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
	os_db_query("INSERT INTO `".DB_PREFIX."configuration` VALUES('', 'SKIP_SHIPPING', 'false', 17, 19, NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

	os_db_query("ALTER TABLE ".DB_PREFIX."products ADD stock int(1) default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."products ADD products_reviews int(1) default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."admin_access ADD ajax int(1) NOT NULL default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."products_images ADD `text` text default '';");
	os_db_query("ALTER TABLE ".DB_PREFIX."categories ADD menu tinyint(1) NOT NULL default '1';");
	os_db_query("ALTER TABLE ".DB_PREFIX."orders_products ADD bundle int(1) NOT NULL default '0';");
	os_db_query("ALTER TABLE ".DB_PREFIX."products ADD products_bundle int(1) NOT NULL default '0';");
	os_db_query("ALTER TABLE ".DB_PREFIX."manufacturers ADD manufacturers_page_url VARCHAR(255) NOT NULL DEFAULT  '';");

	os_db_query("CREATE TABLE ".DB_PREFIX."customers_profile (
		customers_id int(11) NOT NULL auto_increment,
		customers_signature varchar(255),
		show_gender tinyint NOT NULL DEFAULT 1,
		show_firstname tinyint NOT NULL DEFAULT 1,
		show_secondname tinyint NOT NULL DEFAULT 0,
		show_lastname tinyint NOT NULL DEFAULT 0,
		show_dob tinyint NOT NULL DEFAULT 1,
		show_email tinyint NOT NULL DEFAULT 0,
		show_telephone tinyint NOT NULL DEFAULT 0,
		show_fax tinyint NOT NULL DEFAULT 0,
		customers_wishlist int(11) NOT NULL DEFAULT 0,
		customers_avatar varchar(255) DEFAULT NULL,
		customers_photo varchar(255) DEFAULT NULL,
		PRIMARY KEY (customers_id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");

	os_db_query("INSERT INTO ".DB_PREFIX."customers_profile VALUES(1, NULL, 1, 1, 0, 0, 1, 0, 0, 0, 0, '', '');");

	os_db_query("CREATE TABLE ".DB_PREFIX."products_bundles (
		bundle_id int(11) NOT NULL,
		subproduct_id int(11) NOT NULL,
		subproduct_qty int(11) NOT NULL,
		PRIMARY KEY (bundle_id,subproduct_id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");

	$messageStack->add_session('База успешно обновлена!', 'success');
	os_redirect(FILENAME_PLUGINS.'?module=update_100_to_101');
}
?>