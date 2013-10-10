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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=15");


# configuration_group_id 15
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SESSION_FORCE_COOKIE_USE', 'True',  15, 2, NULL, '', NULL, 'os_cfg_select_option(array(\'True\', \'False\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SESSION_CHECK_SSL_SESSION_ID', 'False',  15, 3, NULL, '', NULL, 'os_cfg_select_option(array(\'True\', \'False\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SESSION_CHECK_USER_AGENT', 'False',  15, 4, NULL, '', NULL, 'os_cfg_select_option(array(\'True\', \'False\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SESSION_CHECK_IP_ADDRESS', 'False',  15, 5, NULL, '', NULL, 'os_cfg_select_option(array(\'True\', \'False\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SESSION_RECREATE', 'False',  15, 7, NULL, '', NULL, 'os_cfg_select_option(array(\'True\', \'False\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SESSION_TIMEOUT_ADMIN', '14400',  15, 8, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SESSION_TIMEOUT_CATALOG', '1440',  15, 9, NULL, '', NULL, NULL);");

?>