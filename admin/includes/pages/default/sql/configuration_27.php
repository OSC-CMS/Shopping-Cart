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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=27");

#configuration_group_id 27

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added, use_function) VALUES ('DOWN_FOR_MAINTENANCE', 'false', '27', '1', 'os_cfg_select_option(array(\'true\', \'false\'), ', now(), NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('EXCLUDE_ADMIN_IP_FOR_MAINTENANCE', 'ip-address', '27', '1', NULL , '0000-00-00 00:00:00', NULL , NULL);");

?>