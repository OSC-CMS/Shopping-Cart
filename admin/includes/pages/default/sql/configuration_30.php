<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( '������ ������  �� �����������.' );

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=30");

# configuration_group_id 30
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_WHATSNEW', 'true',30, 1, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_SPECIALS', 'true',30, 2, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_SEARCH', 'true',30, 3, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_REVIEWS', 'true',30, 4, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_ORDER_HISTORY', 'false',30, 5, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_NEWSLETTER', 'true',30, 6, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_MANUFACTURERS_INFO', 'true',30, 7, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_MANUFACTURERS', 'true',30, 8, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_LOGIN', 'true',30, 9, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_LAST_VIEWED', 'true',30, 11, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_LANGUAGES', 'true',30, 12, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_INFORMATION', 'true',30, 13, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_INFOBOX', 'false',30, 14, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_FEATURED', 'true',30, 15, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_FAQ', 'false',30, 16, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_CURRENCIES', 'true',30, 18, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_CONTENT', 'true',30, 19, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_CATEGORIES', 'true',30, 20, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_CART', 'true',30, 21, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_BEST_SELLERS', 'true',30, 22, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_ARTICLES_NEW', 'true',30, 24, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_ARTICLES', 'true',30, 25, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_AFFILIATE', 'false',30, 26, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_ADD_A_QUICKIE', 'false',30, 28, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_ADMIN', 'true',30, 29, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_DOWNLOADS', 'false',30, 30, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_BOX_NEWS', 'true',  30, 31, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),')");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_MAIN_NEWS', 'true',  30, 32, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),')");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_MAIN_FEATURES', 'true',  30, 33, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),')");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_MAIN_NEW', 'true',  30, 34, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),')");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('VIS_MAIN_UPCOMING', 'true',  30, 35, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),')");
?>