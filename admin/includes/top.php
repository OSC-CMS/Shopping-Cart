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

@ header('Content-Type: text/html; charset=utf-8');

define('PAGE_PARSE_START_TIME', microtime());
define('_VALID_OS', true);


@ error_reporting(E_ALL & ~E_NOTICE);

$php4_3_10 = (0 == version_compare(phpversion(), "4.3.10"));
define('PHP4_3_10', $php4_3_10);

if (function_exists('ini_set')) 
{
   @ ini_set("max_execution_time", 0);
   @ ini_set("short_open_tag", 1);
   @ ini_set('session.use_trans_sid', 0);
}

if (!isset($PHP_SELF)) $PHP_SELF = $_SERVER['PHP_SELF'];

include (dirname(dirname(dirname(__FILE__))).'/config.php');

if (!defined("DB_PREFIX"))
{
   define('DB_PREFIX', 'os_');
}

define('TABLE_ADDRESS_BOOK', DB_PREFIX.'address_book');
define('TABLE_ADDRESS_FORMAT', DB_PREFIX.'address_format');
define('TABLE_ADMIN_ACCESS', DB_PREFIX.'admin_access');
define('TABLE_CAMPAIGNS', DB_PREFIX.'campaigns');
define('TABLE_CATEGORIES', DB_PREFIX.'categories');
define('TABLE_CATEGORIES_DESCRIPTION', DB_PREFIX.'categories_description');
define('TABLE_CONFIGURATION', DB_PREFIX.'configuration');
define('TABLE_CONFIGURATION_GROUP', DB_PREFIX.'configuration_group');
define('TABLE_COUNTRIES', DB_PREFIX.'countries');
define('TABLE_CURRENCIES', DB_PREFIX.'currencies');
define('TABLE_CUSTOMERS', DB_PREFIX.'customers');
define('TABLE_CUSTOMERS_BASKET', DB_PREFIX.'customers_basket');
define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', DB_PREFIX.'customers_basket_attributes');
define('TABLE_CUSTOMERS_INFO', DB_PREFIX.'customers_info');
define('TABLE_CUSTOMERS_IP', DB_PREFIX.'customers_ip');
define('TABLE_CUSTOMERS_STATUS', DB_PREFIX.'customers_status');
define('TABLE_CUSTOMERS_STATUS_HISTORY', DB_PREFIX.'customers_status_history');
define('TABLE_FORMS', DB_PREFIX.'forms');
define('TABLE_LANGUAGES', DB_PREFIX.'languages');
define('TABLE_MANUFACTURERS', DB_PREFIX.'manufacturers');
define('TABLE_MANUFACTURERS_INFO', DB_PREFIX.'manufacturers_info');
define('TABLE_NEWSLETTERS', DB_PREFIX.'newsletters');
define('TABLE_NEWSLETTERS_HISTORY', DB_PREFIX.'newsletters_history');
define('TABLE_NEWSLETTER_RECIPIENTS', DB_PREFIX.'newsletter_recipients');
define('TABLE_ORDERS', DB_PREFIX.'orders');
define('TABLE_ORDERS_PRODUCTS', DB_PREFIX.'orders_products');
define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', DB_PREFIX.'orders_products_attributes');
define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', DB_PREFIX.'orders_products_download');
define('TABLE_ORDERS_STATUS', DB_PREFIX.'orders_status');
define('TABLE_ORDERS_STATUS_HISTORY', DB_PREFIX.'orders_status_history');
define('TABLE_ORDERS_TOTAL', DB_PREFIX.'orders_total');
define('TABLE_ORDERS_RECALCULATE', DB_PREFIX.'orders_recalculate');
define('TABLE_PERSONAL_OFFERS_BY',DB_PREFIX.'personal_offers_by_customers_status_');
define('TABLE_PRODUCTS', DB_PREFIX.'products');
define('TABLE_PRODUCTS_ATTRIBUTES', DB_PREFIX.'products_attributes');
define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', DB_PREFIX.'products_attributes_download');
define('TABLE_PRODUCTS_CONTENT',DB_PREFIX.'products_content');
define('TABLE_PRODUCTS_DESCRIPTION', DB_PREFIX.'products_description');
define('TABLE_PRODUCTS_NOTIFICATIONS', DB_PREFIX.'products_notifications');
define('TABLE_PRODUCTS_IMAGES', DB_PREFIX.'products_images');
define('TABLE_PRODUCTS_OPTIONS', DB_PREFIX.'products_options');
define('TABLE_PRODUCTS_OPTIONS_VALUES', DB_PREFIX.'products_options_values');
define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', DB_PREFIX.'products_options_values_to_products_options');
define('TABLE_PRODUCTS_TO_CATEGORIES', DB_PREFIX.'products_to_categories');
define('TABLE_PRODUCTS_VPE',DB_PREFIX.'products_vpe');
define('TABLE_PRODUCTS_XSELL',DB_PREFIX.'products_xsell');
define('TABLE_PRODUCTS_XSELL_GROUPS',DB_PREFIX.'products_xsell_grp_name');
define('TABLE_REVIEWS', DB_PREFIX.'reviews');
define('TABLE_REVIEWS_DESCRIPTION', DB_PREFIX.'reviews_description');
define('TABLE_SESSIONS', DB_PREFIX.'sessions');
define('TABLE_SPECIALS', DB_PREFIX.'specials');
define('TABLE_TAX_CLASS', DB_PREFIX.'tax_class');
define('TABLE_TAX_RATES', DB_PREFIX.'tax_rates');
define('TABLE_GEO_ZONES', DB_PREFIX.'geo_zones');
define('TABLE_ZONES_TO_GEO_ZONES', DB_PREFIX.'zones_to_geo_zones');
define('TABLE_WHOS_ONLINE', DB_PREFIX.'whos_online');
define('TABLE_ZONES', DB_PREFIX.'zones');
define('TABLE_PLUGINS', DB_PREFIX.'plugins');
define('TABLE_BOX_ALIGN',DB_PREFIX.'box_align');
define('TABLE_CUSTOMERS_MEMO',DB_PREFIX.'customers_memo');
define('TABLE_CONTENT_MANAGER',DB_PREFIX.'content_manager');
define('TABLE_MEDIA_CONTENT',DB_PREFIX.'media_content');
define('TABLE_MODULE_NEWSLETTER',DB_PREFIX.'module_newsletter');
define('TABLE_CM_FILE_FLAGS', DB_PREFIX.'cm_file_flags');
define('TABLE_COUPON_GV_QUEUE', DB_PREFIX.'coupon_gv_queue');
define('TABLE_COUPON_GV_CUSTOMER', DB_PREFIX.'coupon_gv_customer');
define('TABLE_COUPON_EMAIL_TRACK', DB_PREFIX.'coupon_email_track');
define('TABLE_COUPON_REDEEM_TRACK', DB_PREFIX.'coupon_redeem_track');
define('TABLE_COUPONS', DB_PREFIX.'coupons');
define('TABLE_COUPONS_DESCRIPTION', DB_PREFIX.'coupons_description');
define('TABLE_SERVER_TRACKING', DB_PREFIX.'server_tracking');
define('TABLE_SHIPPING_STATUS', DB_PREFIX.'shipping_status');
define('TABLE_BLACKLIST', DB_PREFIX.'card_blacklist'); 
define('TABLE_CAMPAIGNS_IP',DB_PREFIX.'campaigns_ip');
define('TABLE_LATEST_NEWS', DB_PREFIX.'latest_news');
define('TABLE_SCART', DB_PREFIX.'scart');
define('TABLE_FEATURED', DB_PREFIX.'featured');
define('TABLE_CUSTOMERS_STATUS_ORDERS_STATUS', DB_PREFIX.'customers_status_orders_status');
define('TABLE_MONEYBOOKERS',DB_PREFIX.'payment_moneybookers');
define('TABLE_MONEYBOOKERS_COUNTRIES',DB_PREFIX.'payment_moneybookers_countries');
define('TABLE_MONEYBOOKERS_CURRENCIES',DB_PREFIX.'payment_moneybookers_currencies');
define('TABLE_BANKTRANSFER',DB_PREFIX.'banktransfer');
define('TABLE_NEWSLETTER_TEMP',DB_PREFIX.'module_newsletter_temp_');
define('TABLE_PERSONAL_OFFERS',DB_PREFIX.'personal_offers_by_customers_status_');
define('TABLE_COMPANIES',DB_PREFIX.'companies');
define('TABLE_PERSONS',DB_PREFIX.'persons');

define('FILENAME_ACCOUNTING', 'accounting.php');
define('FILENAME_FILE', 'file.php');
define('FILENAME_IMPORT', 'import.php');
define('FILENAME_BACKUP', 'backup.php');
define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
define('FILENAME_CACHE', 'cache.php');
define('FILENAME_CAMPAIGNS', 'campaigns.php');
define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
define('FILENAME_CATALOG_NEWSLETTER', 'newsletter.php');
define('FILENAME_CATEGORIES', 'categories.php');
define('FILENAME_ERROR_LOG', 'error_log.php');
define('FILENAME_CONFIGURATION', 'configuration.php');
define('FILENAME_COUNTRIES', 'countries.php');
define('FILENAME_CURRENCIES', 'currencies.php');
define('FILENAME_CUSTOMERS', 'customers.php');
define('FILENAME_CUSTOMERS_STATUS', 'customers_status.php');
define('FILENAME_DEFAULT', 'index2.php');
define('FILENAME_INDEX', 'index.php');
define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
define('FILENAME_FORMS', 'forms.php');
define('FILENAME_FORM_VALUES', 'form_values.php');
define('FILENAME_GEO_ZONES', 'geo_zones.php');
define('FILENAME_LANGUAGES', 'languages.php');
define('FILENAME_MAIL', 'mail.php');
define('FILENAME_MANUFACTURERS', 'manufacturers.php');
define('FILENAME_MODULES', 'modules.php');
define('FILENAME_ORDERS', 'orders.php');
define('FILENAME_ORDERS_INVOICE', 'invoice.php');
define('FILENAME_ORDERS_PACKINGSLIP', 'packingslip.php');
define('FILENAME_ORDERS_STATUS', 'orders_status.php');
define('FILENAME_ORDERS_EDIT', 'orders_edit.php');
define('FILENAME_POPUP_IMAGE', 'popup_image.php');
define('FILENAME_PRODUCTS_ATTRIBUTES', 'products_attributes.php');
define('FILENAME_PRODUCTS_EXPECTED', 'products_expected.php');
define('FILENAME_REVIEWS', 'reviews.php');
define('FILENAME_SERVER_INFO', 'server_info.php');
define('FILENAME_SHIPPING_MODULES', 'shipping_modules.php');
define('FILENAME_SPECIALS', 'specials.php');
define('FILENAME_STATS_CUSTOMERS', 'stats_customers.php');
define('FILENAME_STATS_PRODUCTS_PURCHASED', 'stats_products_purchased.php');
define('FILENAME_STATS_PRODUCTS_VIEWED', 'stats_products_viewed.php');
define('FILENAME_TAX_CLASSES', 'tax_classes.php');
define('FILENAME_TAX_RATES', 'tax_rates.php');
define('FILENAME_WHOS_ONLINE', 'whos_online.php');
define('FILENAME_ZONES', 'zones.php');
define('FILENAME_START', 'index2.php');
define('FILENAME_STATS_STOCK_WARNING', 'stats_stock_warning.php');
define('FILENAME_NEW_ATTRIBUTES','new_attributes.php');
define('FILENAME_PLUGINS','plugins.php');
define('FILENAME_PLUGINS_PAGE','plugins_page.php');
define('FILENAME_LOGOUT','../logoff.php');
define('FILENAME_LOGIN','../login.php');
define('FILENAME_CREATE_ACCOUNT','create_account.php');
define('FILENAME_CREATE_ACCOUNT_SUCCESS','create_account_success.php');
define('FILENAME_CUSTOMER_MEMO','customer_memo.php');
define('FILENAME_CONTENT_MANAGER','content_manager.php');
define('FILENAME_CONTENT_PREVIEW','content_preview.php');
define('FILENAME_SECURITY_CHECK','security_check.php');
define('FILENAME_PRINT_ORDER','print_order.php');
define('FILENAME_PRINT_PACKINGSLIP','print_packingslip.php');
define('FILENAME_MODULE_NEWSLETTER','module_newsletter.php');
define('FILENAME_GV_QUEUE', 'gv_queue.php');
define('FILENAME_GV_MAIL', 'gv_mail.php');
define('FILENAME_GV_SENT', 'gv_sent.php');
define('FILENAME_COUPON_ADMIN', 'coupon_admin.php');
define('FILENAME_POPUP_MEMO', 'popup_memo.php');
define('FILENAME_SHIPPING_STATUS', 'shipping_status.php');
define('FILENAME_SALES_REPORT','stats_sales_report.php');
define('FILENAME_MODULE_EXPORT','module_export.php');
define('FILENAME_EASY_POPULATE','easypopulate.php');
define('FILENAME_QUICK_UPDATES', 'quick_updates.php');
define('FILENAME_BLACKLIST', 'blacklist.php');
define('FILENAME_PRODUCTS_VPE','products_vpe.php');
define('FILENAME_CAMPAIGNS_REPORT','stats_campaigns.php');
define('FILENAME_XSELL_GROUPS','cross_sell_groups.php');
define('FILENAME_CSV_BACKEND','csv_backend.php');
define('FILENAME_EASYPOPULATE', 'easypopulate.php');
define('FILENAME_LATEST_NEWS', 'latest_news.php');
define('FILENAME_RECOVER_CART_SALES', 'recover_cart_sales.php');
define('FILENAME_FEATURED', 'featured.php');

define('SQL_CACHEDIR', _CACHE);

if ( is_file( dir_path('catalog').'VERSION' ) )
{
    $_version = @ file_get_contents (dir_path('catalog').'VERSION');
	$_var_array = explode('/', $_version);
	//ревизия
	$_rev = $_var_array[2];
	//версия
    $_version = $_var_array[1];;
}
else
{
    $_version = '3.0.0';
}

define('PROJECT_VERSION', 'OSC-CMS '.$_version);
define('SECURITY_CODE_LENGTH', '6');
define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
define('LOCAL_EXE_ZIP', '/usr/local/bin/zip');
define('LOCAL_EXE_UNZIP', '/usr/local/bin/unzip');

define('BOX_WIDTH', 125);
define('CURRENCY_SERVER_PRIMARY', 'cbr');
define('CURRENCY_SERVER_BACKUP', 'xe');

include (_FUNC.'admin.include.php');
include (_CLASS_ADMIN.'main.php');

include (_CLASS.'db.php');
os_db_connect() or die('Unable to connect to database server!');

include (_FUNC.'cache.php');
include (_FUNC.'general.php');
//include_once( dir_path_admin('func') . 'general.php'); 


/* plugins */
include(_CLASS.'plugins.php');
include (_FUNC.FILENAME_PLUGINS);

$p = new plugins('activ'); // получения списка активных плагинов

/* // plugins */

$main = new main ();

$configuration_query = os_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . '');
while ($configuration = os_db_fetch_array($configuration_query)) 
{
    @ define($configuration['cfgKey'], $configuration['cfgValue']);
}

add_path('themes_c', array('dir'  => dir_path  ('themes').CURRENT_TEMPLATE.'/', 
                           'http' => http_path ('themes').CURRENT_TEMPLATE.'/'));

$db->DB_CACHE = DB_CACHE;
$db->STORE_DB_TRANSACTIONS = STORE_DB_TRANSACTIONS;
$db->DISPLAY_DB_QUERY = DISPLAY_DB_QUERY;
get_cache_all();

function osDBquery($query) 
{
    global $db;
	
	if ($db->DB_CACHE == 'true') 
	{
		$result = os_db_queryCached($query);
	} 
	else 
	{
		$result = os_db_query($query);

	}
	return $result;
}

define('FILENAME_IMAGEMANIPULATOR',IMAGE_MANIPULATOR);

require(_CLASS_ADMIN . 'logger.php');
require(_CLASS_ADMIN . 'shopping_cart.php');
require(_FUNC_ADMIN . 'compatibility.php');
require(_FUNC_ADMIN . 'general.php');
require(_FUNC_ADMIN . 'sessions.php');
require(_FUNC_ADMIN . 'html_output.php');

session_name('sid');

$p->require_plugins(); //подключение активных плагинов


if (STORE_SESSIONS != 'mysql') session_save_path(DIR_FS_DOCUMENT_ROOT.SESSION_WRITE_DIRECTORY);


  $cookie_info = os_get_cookie_info();


  if (function_exists('session_set_cookie_params')) {
        session_set_cookie_params(0, $cookie_info['cookie_path'], $cookie_info['cookie_domain']);
  }
  elseif (function_exists('ini_set')) {
        @ini_set('session.cookie_lifetime', '0');
        @ini_set('session.cookie_path', $cookie_info['cookie_path']);
        @ini_set('session.cookie_domain', $cookie_info['cookie_domain']);
  }

  if (isset($_POST[session_name()])) 
  {
      session_id($_POST[session_name()]);
  } 
  elseif ( isset($request_type) && ($request_type == 'SSL') && isset($_GET[session_name()]) ) 
  {
      session_id($_GET[session_name()]);
  }


  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE == 'True') {
        os_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, 
                   $cookie_info['cookie_path'], $cookie_info['cookie_domain']);


    if (isset($_COOKIE['cookie_test'])) {
      session_start();
      $session_started = true;
    }
  } elseif (CHECK_CLIENT_AGENT == 'True') {
    $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
    $spider_flag = false;


    if ($spider_flag == false) {
      session_start();
      $session_started = true;
    }
  } else {
    session_start();
    $session_started = true;
  }


  if ( isset($request_type) && ($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true) ) {
    $ssl_session_id = getenv('SSL_SESSION_ID');
    if (!session_is_registered('SSL_SESSION_ID')) {
      $_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
    }


    if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
      session_destroy();
      os_redirect(os_href_link(FILENAME_SSL_CHECK));
    }
  }


if (SESSION_CHECK_USER_AGENT == 'True') {
        $http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $http_user_agent2 = strtolower(getenv("HTTP_USER_AGENT"));
        $http_user_agent = ($http_user_agent == $http_user_agent2) ? $http_user_agent : $http_user_agent.';'.$http_user_agent2;
        if (!isset($_SESSION['SESSION_USER_AGENT'])) {
                $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
        }


        if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
                session_destroy();
                os_redirect(os_href_link(FILENAME_LOGIN));
        } 
}




  if (SESSION_CHECK_IP_ADDRESS == 'True') {
    $ip_address = os_get_ip_address();
    if (!os_session_is_registered('SESSION_IP_ADDRESS')) {
      $_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
    }


    if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
      session_destroy();
      os_redirect(os_href_link(FILENAME_LOGIN));
    }
  }


//Eciaiaiea ycuea
if(isset($_POST['lang_a']) && !empty($_POST['lang_a']))
{
    $_SESSION['language_admin'] = $_POST['lang_a'];
	$_SESSION['language'] = $_SESSION['language_admin'];
}
 
if (!isset($_SESSION['language_admin']) || isset($_GET['language'])) 
{
    include(get_path('class_admin') . 'language.php');
     $lng = new language(isset($_GET['language'])?$_GET['language']:'');
    if (!isset($_GET['language'])) $lng->get_browser_language();
    $_SESSION['language_admin'] = $lng->language['directory'];
    $_SESSION['languages_id'] = $lng->language['id'];
}


require_once (DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin'] .'/lang.php');
require_once (DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin']. '/buttons.php');


$current_page = preg_split('/\?/', basename($_SERVER['PHP_SELF'])); $current_page = $current_page[0]; // for BadBlue(Win32) webserver compatibility
 
if (file_exists(DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin'] .'/'.$current_page)) 
{
    include(DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin'] .'/'.  $current_page);
}


require(_INCLUDES.'write_customers_status.php');
$_SESSION['user_info'] = array();


if (!isset($_SESSION['user_info']['user_ip'])) 
{
    $_SESSION['user_info']['user_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_info']['user_host'] = gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
	if (isset($_GET['ad'])) $_SESSION['user_info']['advertiser'] = $_GET['ad'];
    if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['user_info']['referer_url'] = $_SERVER['HTTP_REFERER'];
}


require(_FUNC_ADMIN . 'localization.php');
require(_CLASS_ADMIN . 'table_block.php');
require(_CLASS_ADMIN . 'box.php');
require(_CLASS_ADMIN . 'message_stack.php');
$messageStack = new messageStack;
require(_CLASS_ADMIN . 'split_page_results.php');
require(_CLASS_ADMIN . 'object_info.php');
require(_CLASS_ADMIN . 'upload.php');


if (isset($_GET['cPath'])) 
{
    $cPath = $_GET['cPath'];
} 
else 
{
    $cPath = '';
}
  if (strlen($cPath) > 0) {
    $cPath_array = explode('_', $cPath);
    $current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
  } else {
    $current_category_id = 0;
  }


  if (!isset($_SESSION['selected_box'])) {
    $_SESSION['selected_box'] = 'configuration';
  }
  if (isset($_GET['selected_box'])) {
    $_SESSION['selected_box'] = $_GET['selected_box'];
  }


  $cache_blocks = array(array('title' => TEXT_CACHE_CATEGORIES, 'code' => 'categories', 'file' => 'categories_box-language.cache', 'multiple' => true),
                        array('title' => TEXT_CACHE_MANUFACTURERS, 'code' => 'manufacturers', 'file' => 'manufacturers_box-language.cache', 'multiple' => true),
                        array('title' => TEXT_CACHE_ALSO_PURCHASED, 'code' => 'also_purchased', 'file' => 'also_purchased-language.cache', 'multiple' => true)
                       );


  if (!defined('DEFAULT_CURRENCY')) {
    $messageStack->add(ERROR_NO_DEFAULT_CURRENCY_DEFINED, 'error');
  }


  if (!defined('DEFAULT_LANGUAGE')) {
    $messageStack->add(ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
  }

  os_get_customers_statuses();

  $pagename = strtok($current_page, '.');
  if (!isset($_SESSION['customer_id'])) 
  {
    os_redirect(os_href_link(FILENAME_LOGIN));
  }


  if (os_check_permission($pagename) == '0') 
  {
    os_redirect(os_href_link(FILENAME_LOGIN));
  }


define('FILENAME_ARTICLES', 'articles.php');
define('FILENAME_ARTICLES_CONFIG', 'articles_config.php');
define('FILENAME_AUTHORS', 'authors.php');
define('FILENAME_ARTICLES_XSELL', 'articles_xsell.php');

define('TABLE_ARTICLES', DB_PREFIX.'articles');
define('TABLE_ARTICLES_DESCRIPTION', DB_PREFIX.'articles_description');
define('TABLE_ARTICLES_TO_TOPICS', DB_PREFIX.'articles_to_topics');
define('TABLE_AUTHORS', DB_PREFIX.'authors');
define('TABLE_AUTHORS_INFO', DB_PREFIX.'authors_info');
define('TABLE_TOPICS', DB_PREFIX.'topics');
define('TABLE_TOPICS_DESCRIPTION', DB_PREFIX.'topics_description');
define('TABLE_ARTICLES_XSELL', DB_PREFIX.'articles_xsell');


require(_FUNC_ADMIN . 'articles.php');


  if (isset($_GET['tPath'])) {
    $tPath = $_GET['tPath'];
  } else {
    $tPath = '';
  }


  if (os_not_null($tPath)) {
    $tPath_array = os_parse_topic_path($tPath);
    $tPath = implode('_', $tPath_array);
    $current_topic_id = $tPath_array[(sizeof($tPath_array)-1)];
  } else {
    $current_topic_id = 0;
  }

require_once(_CLASS_ADMIN.'template.php');

define('FILENAME_STATS_SALES_REPORT2','stats_sales_report2.php');
define('FILENAME_EMAIL_MANAGER','email_manager.php');

define('TABLE_SPECIAL_CATEGORY', DB_PREFIX.'special_category');
define('TABLE_SPECIAL_PRODUCT', DB_PREFIX.'special_product');
define('FILENAME_CATEGORY_SPECIALS', 'category_specials.php');

define('FILENAME_PRODUCTS_OPTIONS', 'products_options.php');
define('TABLE_PRODUCTS_OPTIONS_IMAGES',DB_PREFIX.'products_options_images');

define('FILENAME_SHIP2PAY', 'ship2pay.php');
define('TABLE_SHIP2PAY', DB_PREFIX.'ship2pay');

define('FILENAME_PRODUCTS_EXTRA_FIELDS', 'product_extra_fields.php'); 
define('TABLE_PRODUCTS_EXTRA_FIELDS', DB_PREFIX.'products_extra_fields'); 
define('TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS', DB_PREFIX.'products_to_products_extra_fields'); 

define('FILENAME_FAQ', 'faq.php');
define('TABLE_FAQ', DB_PREFIX.'faq');

define('FILENAME_THEMES','themes.php');
define('FILENAME_THEMES_ADMIN','themes_admin.php');
define('FILENAME_ORDERS_SEND','orders_send.php');
define('FILENAME_MODULE_EDIT','module_edit.php');
define('FILENAME_THEMES_EDIT','themes_edit.php');

require_once('affiliate_top.php');

define('TABLE_EXTRA_FIELDS',DB_PREFIX.'extra_fields');
define('TABLE_EXTRA_FIELDS_INFO',DB_PREFIX.'extra_fields_info');
define('TABLE_CUSTOMERS_TO_EXTRA_FIELDS',DB_PREFIX.'customers_to_extra_fields');
define('FILENAME_EXTRA_FIELDS','customer_extra_fields.php');
 
  if (empty($_GET['gID'])) 
  {
  		   
		
         if (!strstr($PHP_SELF, FILENAME_MODULES)) 
	     {
	         @define(TITLES, TITLE." : ".HEADING_TITLE); 
	     }
         else
           {
              switch (@$_GET['set']){
                    case 'shipping': 
                           define('HEADING_TITLE', HEADING_TITLE_MODULES_SHIPPING);
                           @define(TITLES, TITLE." : ".HEADING_TITLE); 
                    break;
                  
                    case 'ordertotal': 
                           define('HEADING_TITLE', HEADING_TITLE_MODULES_ORDER_TOTAL);
                           @define(TITLES, TITLE." : ".HEADING_TITLE); 
                    break;
                  
                    case 'payment': 
                           define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
                           @define(TITLES, TITLE." : ".HEADING_TITLE); 
                    break;
					
					default:
					     define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
                         @define(TITLES, TITLE." : ".HEADING_TITLE); 
					break;
                  }
           }
  }     
  else
   {
     eval("@define(TITLES, TITLE.\" : \".BOX_CONFIGURATION_".$_GET['gID'].");"); 
     eval("@define(HEAD_T, BOX_CONFIGURATION_".$_GET['gID'].");"); 
   }

?>