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

$db->query("DELETE FROM ".DB_PREFIX."configuration WHERE configuration_group_id=28");

#configuration_group_id 28

$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_EMAIL_ADDRESS', 'affiliate@hostname.com', '28', '1', NULL, now(), NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_PERCENT', '15.0000', '28', '2', NULL, now(), NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_THRESHOLD', '30.00', '28', '3', NULL, now(), NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_COOKIE_LIFETIME', '7200', '28', '4', NULL, now(), NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_BILLING_TIME', '30', '28', '5', NULL, now(), NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_PAYMENT_ORDER_MIN_STATUS', '3', '28', '6', NULL, now(), NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_USE_CHECK', 'true', '28', '7', NULL, now(), NULL,'os_cfg_select_option(array(\'true\', \'false\'), ');");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_USE_PAYPAL', 'false', '28', '8', NULL, now(), NULL,'os_cfg_select_option(array(\'true\', \'false\'), ');");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_USE_BANK', 'false', '28', '9', NULL, now(), NULL,'os_cfg_select_option(array(\'true\', \'false\'), ');");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILATE_INDIVIDUAL_PERCENTAGE', 'true', '28', '10', NULL, now(), NULL,'os_cfg_select_option(array(\'true\', \'false\'), ');");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILATE_USE_TIER', 'false', '28', '11', NULL, now(), NULL,'os_cfg_select_option(array(\'true\', \'false\'), ');");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_TIER_LEVELS', '0', '28', '12', NULL, now(), NULL, NULL);");
$db->query("INSERT INTO ".DB_PREFIX."configuration VALUES ('', 'AFFILIATE_TIER_PERCENTAGE', '8.00;5.00;1.00', '28', '13', NULL, now(), NULL, NULL);");
?>