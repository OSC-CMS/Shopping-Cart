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

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

  require(DIR_FS_ADMIN . 'includes/affiliate_configure.php');
  require(DIR_FS_ADMIN . 'includes/functions/affiliate_functions.php');


  define('FILENAME_AFFILIATE', 'affiliate_affiliates.php');
  define('FILENAME_AFFILIATE_BANNERS', 'affiliate_banners.php');
  define('FILENAME_AFFILIATE_BANNER_MANAGER', 'affiliate_banners.php');
  define('FILENAME_AFFILIATE_CLICKS', 'affiliate_clicks.php');
  define('FILENAME_AFFILIATE_CONTACT', 'affiliate_contact.php');
  define('FILENAME_AFFILIATE_HELP_1', 'affiliate_help1.php');
  define('FILENAME_AFFILIATE_HELP_2', 'affiliate_help2.php');
  define('FILENAME_AFFILIATE_HELP_3', 'affiliate_help3.php');
  define('FILENAME_AFFILIATE_HELP_4', 'affiliate_help4.php');
  define('FILENAME_AFFILIATE_HELP_5', 'affiliate_help5.php');
  define('FILENAME_AFFILIATE_HELP_6', 'affiliate_help6.php');
  define('FILENAME_AFFILIATE_HELP_7', 'affiliate_help7.php');
  define('FILENAME_AFFILIATE_HELP_8', 'affiliate_help8.php');
  define('FILENAME_AFFILIATE_INVOICE', 'affiliate_invoice.php');
  define('FILENAME_AFFILIATE_PAYMENT', 'affiliate_payment.php');
  define('FILENAME_AFFILIATE_POPUP_IMAGE', 'affiliate_popup_image.php');
  define('FILENAME_AFFILIATE_SALES', 'affiliate_sales.php');
  define('FILENAME_AFFILIATE_STATISTICS', 'affiliate_statistics.php');
  define('FILENAME_AFFILIATE_SUMMARY', 'affiliate_summary.php');
  define('FILENAME_AFFILIATE_RESET', 'affiliate_reset.php');
  define('FILENAME_CATALOG_AFFILIATE_PAYMENT_INFO','affiliate_payment.php');
  define('FILENAME_CATALOG_PRODUCT_INFO', 'product_info.php');


  define('TABLE_AFFILIATE', DB_PREFIX.'affiliate_affiliate');
  define('TABLE_AFFILIATE_BANNERS', DB_PREFIX.'affiliate_banners');
  define('TABLE_AFFILIATE_BANNERS_HISTORY', DB_PREFIX.'affiliate_banners_history');
  define('TABLE_AFFILIATE_CLICKTHROUGHS', DB_PREFIX.'affiliate_clickthroughs');
  define('TABLE_AFFILIATE_PAYMENT', DB_PREFIX.'affiliate_payment');
  define('TABLE_AFFILIATE_PAYMENT_STATUS', DB_PREFIX.'affiliate_payment_status');
  define('TABLE_AFFILIATE_PAYMENT_STATUS_HISTORY', DB_PREFIX.'affiliate_payment_status_history');
  define('TABLE_AFFILIATE_SALES', DB_PREFIX.'affiliate_sales');


  require_once(DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin']. '/affiliate.php');
  
  if (isset($_GET['action']))
  {
  if ($_GET['action'] == 'deleteconfirm' && basename($_SERVER['SCRIPT_FILENAME']) == FILENAME_ORDERS && AFFILIATE_DELETE_ORDERS == 'true') {
    $affiliate_oID = os_db_prepare_input($_GET['oID']);
    os_db_query("delete from " . TABLE_AFFILIATE_SALES . " where affiliate_orders_id = '" . os_db_input($affiliate_oID) . "' and affiliate_billing_status != 1");
  }
  }
?>