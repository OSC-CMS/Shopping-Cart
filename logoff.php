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

include ('includes/top.php');
//$osTemplate = new osTemplate;

$breadcrumb->add(NAVBAR_TITLE_LOGOFF);

if (($_SESSION['account_type'] == 1) && (DELETE_GUEST_ACCOUNT == 'true')) {
	os_db_query("delete from ".TABLE_CUSTOMERS." where customers_id = '".$_SESSION['customer_id']."'");
	os_db_query("delete from ".TABLE_ADDRESS_BOOK." where customers_id = '".$_SESSION['customer_id']."'");
	os_db_query("delete from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".$_SESSION['customer_id']."'");
}

os_session_destroy();
os_redirect('index.php');
unset ($_SESSION['customer_id']);
unset ($_SESSION['customer_default_address_id']);
unset ($_SESSION['customer_first_name']);
unset ($_SESSION['customer_country_id']);
unset ($_SESSION['customer_zone_id']);
unset ($_SESSION['comments']);
unset ($_SESSION['user_info']);
unset ($_SESSION['customers_status']);
unset ($_SESSION['selected_box']);
unset ($_SESSION['navigation']);
unset ($_SESSION['shipping']);
unset ($_SESSION['payment']);
unset ($_SESSION['ccard']);
unset ($_SESSION['gv_id']);
unset ($_SESSION['cc_id']);
$_SESSION['cart']->reset();
require (dir_path('includes').'write_customers_status.php');

require (dir_path('includes').'header.php');
$osTemplate->assign('BUTTON_CONTINUE', button_continue());
$osTemplate->assign('language', $_SESSION['language']);

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/logoff.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_LOGOFF.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_LOGOFF.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');

?>