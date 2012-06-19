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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=20");

#configuration_group_id 20
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('CSV_TEXTSIGN', '\"', '20', '1', NULL , '0000-00-00 00:00:00', NULL , NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('CSV_SEPERATOR', '\t', '20', '2', NULL , '0000-00-00 00:00:00', NULL , NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('COMPRESS_EXPORT', 'false', '20', '3', NULL , '0000-00-00 00:00:00', NULL , 'os_cfg_select_option(array(\'true\', \'false\'),');");

?>