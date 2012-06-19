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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=25");

#configuration_group_id 25, Установка модулей
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALLOW_SQL_BACKUP', 'true', '25', '2', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALLOW_SQL_RESTORE', 'false', '25', '3', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALLOW_FILES_BACKUP', 'true', '25', '4', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALLOW_FILES_RESTORE', 'false', '25', '5', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALLOW_OVERWRITE_MODIFIED', 'false', '25', '6', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALWAYS_DISPLAY_REMOVE_BUTTON', 'false', '25', '9', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ALWAYS_DISPLAY_INSTALL_BUTTON', 'false', '25', '10', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SHOW_PERMISSIONS_COLUMN', 'false', '25', '11', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SHOW_USER_GROUP_COLUMN', 'false', '25', '12', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SHOW_UPLOADER_COLUMN', 'false', '25', '13', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SHOW_UPLOADED_COLUMN', 'false', '25', '14', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SHOW_SIZE_COLUMN', 'false', '25', '15', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('USE_LOG_SYSTEM', 'false', '25', '16', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_UPLOADED_FILESIZE', '524288', '25', '17', NULL , '0000-00-00 00:00:00', NULL , NULL);");
?>