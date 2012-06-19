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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=3");

# configuration_group_id 3
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_ADDRESS_BOOK_ENTRIES', '5',  3, 1, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_SEARCH_RESULTS', '20',  3, 2, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_ADMIN_PAGE', '20',  3, 3, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_PAGE_LINKS', '5',  3, 4, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_SPECIAL_PRODUCTS', '9', 3, 5, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_NEW_PRODUCTS', '9',  3, 6, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_UPCOMING_PRODUCTS', '10',  3, 7, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_MANUFACTURERS_IN_A_LIST', '0', 3, 8, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_MANUFACTURERS_LIST', '1',  3, 9, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_MANUFACTURER_NAME_LEN', '15',  3, 10, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_NEW_REVIEWS', '6', 3, 11, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_RANDOM_SELECT_REVIEWS', '10',  3, 12, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_RANDOM_SELECT_NEW', '10',  3, 13, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_RANDOM_SELECT_SPECIALS', '10',  3, 14, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_CATEGORIES_PER_ROW', '3',  3, 15, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_PRODUCTS_NEW', '10',  3, 16, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_BESTSELLERS', '10',  3, 17, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_ALSO_PURCHASED', '6',  3, 18, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX', '6',  3, 19, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_ORDER_HISTORY', '10',  3, 20, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('PRODUCT_REVIEWS_VIEW', '5',  3, 21, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_PRODUCTS_QTY', '1000', 3, 22, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_NEW_PRODUCTS_DAYS', '30', 3, 23, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_RANDOM_SELECT_FEATURED', '10',  3, 24, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_FEATURED_PRODUCTS', '9', 3, 25, NULL, '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('MAX_DISPLAY_FAQ', '2', 3, 26, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('MAX_DISPLAY_FAQ_PAGE', '20', 3, 27, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('MAX_DISPLAY_FAQ_ANSWER', '150', 3, 28, 'NULL', '', NULL, NULL);");

# Новости

$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_LATEST_NEWS', '2', 3, 23, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_LATEST_NEWS_PAGE', '20', 3, 24, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_LATEST_NEWS_CONTENT', '200', 3, 25, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_CART', '50', 3, 26, 'NULL', '', NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration (configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('MAX_DISPLAY_SHORT_DESCRIPTION', '80', 3, 27, 'NULL', '', NULL, NULL);");

?>