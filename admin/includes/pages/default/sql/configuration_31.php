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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=31");

# configuration_group_id 31
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SEO_URL_PRODUCT_GENERATOR', 'false', 31, 1, 'NULL', '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SEARCH_ENGINE_FRIENDLY_URLS', 'false',  31, 2, NULL, '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SEO_URL_PRODUCT_GENERATOR_IMPORT', 'false', 31, 3, 'NULL', '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('SEO_URL_CATEGORIES_GENERATOR', 'false', 31, 4, 'NULL', '', NULL, 'os_cfg_select_option(array(\'true\', \'false\'),');");

?>