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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=2");

# configuration_group_id 2
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_FIRST_NAME_MIN_LENGTH', '2',  2, 1, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_LAST_NAME_MIN_LENGTH', '2',  2, 2, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_DOB_MIN_LENGTH', '10',  2, 3, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_EMAIL_ADDRESS_MIN_LENGTH', '6',  2, 4, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_STREET_ADDRESS_MIN_LENGTH', '5',  2, 5, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_COMPANY_MIN_LENGTH', '2',  2, 6, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_POSTCODE_MIN_LENGTH', '4',  2, 7, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_CITY_MIN_LENGTH', '3',  2, 8, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_STATE_MIN_LENGTH', '2', 2, 9, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_TELEPHONE_MIN_LENGTH', '3',  2, 10, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('ENTRY_PASSWORD_MIN_LENGTH', '5',  2, 11, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('CC_OWNER_MIN_LENGTH', '3',  2, 12, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('CC_NUMBER_MIN_LENGTH', '10',  2, 13, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('REVIEW_TEXT_MIN_LENGTH', '10',  2, 14, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MIN_DISPLAY_BESTSELLERS', '1',  2, 15, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MIN_DISPLAY_ALSO_PURCHASED', '1', 2, 16, NULL, '', NULL, NULL);");
?>