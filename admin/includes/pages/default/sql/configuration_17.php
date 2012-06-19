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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=17");

# configuration_group_id 17
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ACTIVATE_GIFT_SYSTEM', 'false', 17, 2, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SECURITY_CODE_LENGTH', '10', 17, 3, NULL, '2011-2012-12-05 05:01:41', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', '0', 17, 4, NULL, '2011-2012-12-05 05:01:41', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('NEW_SIGNUP_DISCOUNT_COUPON', '', 17, 5, NULL, '2011-2012-12-05 05:01:41', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ACTIVATE_SHIPPING_STATUS', 'true', 17, 6, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('DISPLAY_CONDITIONS_ON_CHECKOUT', 'false',17, 7, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SHOW_IP_LOG', 'false',17, 8, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('GROUP_CHECK', 'false',  17, 9, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ACTIVATE_NAVIGATOR', 'false',  17, 10, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('QUICKLINK_ACTIVATED', 'true',  17, 11, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ACTIVATE_REVERSE_CROSS_SELLING', 'false', 17, 12, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('DISPLAY_REVOCATION_ON_CHECKOUT', 'true', 17, 13, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('REVOCATION_ID', '', 17, 14, NULL, '2011-2012-12-05 05:01:41', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('LOGIN_NUM', '3', '17', '16', NULL, '0000-00-00 00:00:00', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('LOGIN_TIME', '300',  '17', '17', NULL, '0000-00-00 00:00:00', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SET_WHOS_ONLINE', 'false',  '17', '18', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('QUICK_CHECKOUT', 'false',  17, 19, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('USE_REVIEWS_MODERATION', 'false',  '17', '19', NULL, '0000-00-00 00:00:00', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");


?>