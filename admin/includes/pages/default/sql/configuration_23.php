<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=23");

#configuration_group_id 23, Яндекс-маркет
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_NAME', '', '23', '1', NULL , '0000-00-00 00:00:00', NULL , NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_COMPANY', '', '23', '2', NULL , '0000-00-00 00:00:00', NULL , NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_DELIVERYINCLUDED', 'false', '23', '3', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_AVAILABLE', 'stock', '23', '4', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\', \'stock\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_AUTH_USER', '', '23', '5', NULL , '0000-00-00 00:00:00', NULL , NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_AUTH_PW', '', '23', '6', NULL , '0000-00-00 00:00:00', NULL , NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_REFERER', 'false', '23', '7', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'false\', \'ip\', \'agent\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_STRIP_TAGS', 'true', '23', '8', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_UTF8', 'false', '23', '9', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_VENDOR', 'false', '23', '10', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_REF_ID', '&amp;ref=yml', '23', '11', NULL , '0000-00-00 00:00:00', NULL , NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_REF_IP', 'true', '23', '12', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_USE_CDATA', 'true', '23', '13', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('YML_SALES_NOTES', '', '23', '14', NULL, '0000-00-00 00:00:00', NULL, NULL);");

?>