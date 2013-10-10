<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

global $db;

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=1");


$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_NAME', 'Название магазина',  1, 1, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_OWNER', 'Название компании', 1, 2, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_OWNER_EMAIL_ADDRESS', 'admin@admin.com', 1, 3, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('EMAIL_FROM', 'CartET admin@admin.com',  1, 4, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_COUNTRY', '176',  1, 6, NULL, '', 'os_get_country_name', 'os_cfg_pull_down_country_list(');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_ZONE', '98', 1, 7, NULL, '', 'os_cfg_get_zone_name', 'os_cfg_pull_down_zone_list(');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('EXPECTED_PRODUCTS_SORT', 'desc',  1, 8, NULL, '', NULL, 'os_cfg_select_option(array(\'asc\', \'desc\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('EXPECTED_PRODUCTS_FIELD', 'date_expected',  1, 9, NULL, '', NULL, 'os_cfg_select_option(array(\'products_name\', \'date_expected\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('USE_DEFAULT_LANGUAGE_CURRENCY', 'false', 1, 10, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SEARCH_ENGINE_FRIENDLY_URLS', 'false',  16, 12, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('DISPLAY_CART', 'true',  1, 13, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ADVANCED_SEARCH_DEFAULT_OPERATOR', 'and', 1, 15, NULL, '', NULL, 'os_cfg_select_option(array(\'and\', \'or\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_NAME_ADDRESS', 'Store Name\nAddress\nCountry\nPhone',  1, 16, NULL, '', NULL, 'os_cfg_textarea(');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SHOW_COUNTS', 'false',  1, 17, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN', '0',  1, 20, NULL, '', 'os_get_customers_status_name', 'os_cfg_pull_down_customers_status_list(');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('DEFAULT_CUSTOMERS_STATUS_ID_GUEST', '1',  1, 21, NULL, '', 'os_get_customers_status_name', 'os_cfg_pull_down_customers_status_list(');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('DEFAULT_CUSTOMERS_STATUS_ID', '2',  1, 23, NULL, '', 'os_get_customers_status_name', 'os_cfg_pull_down_customers_status_list(');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALLOW_ADD_TO_CART', 'false',  1, 24, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('CURRENT_TEMPLATE', 'default', 1, 26, NULL, '', NULL, 'os_cfg_pull_down_template_sets(');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ADMIN_TEMPLATE', 'default', 1, 26, NULL, '', NULL, 'os_cfg_pull_down_admin_template_sets(');");

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('PRICE_IS_BRUTTO', 'false', 1, 27, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('PRICE_PRECISION', '4', 1, 28, NULL, '', NULL, '');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('AJAX_CART', 'false', 1, 31, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENABLE_TABS', 'true', 1, 32, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MASTER_PASS', '', 1, 33, NULL, '', NULL, '');");

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_TELEPHONE', '<br />(123) 123-45-67<br /> (456) 456-78-90<br />  (789) 234-56-78<br />', 1, 3, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_ICQ', '123-456-789', 1, 3, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('STORE_SKYPE', 'Skype_name', 1, 3, NULL, '', NULL, NULL);");

?>