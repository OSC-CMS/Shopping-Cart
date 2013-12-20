<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

@header('Content-Type: text/html; charset=utf-8');
define('PAGE_PARSE_START_TIME', microtime());
@error_reporting(E_ALL & ~E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('_VALID_OS', true);

$_dir = dirname(dirname(__FILE__));

if (file_exists($_dir.'/config.php'))
	require_once ($_dir.'/config.php'); else echo('Error Configure file');

if (!function_exists('get_path'))
	header('Location: install');

require_once (_INCLUDES.'_classes/cartet.class.php');
$cartet = new CartET();

require_once (_CLASS.'db.php');

require_once (_FUNC.'admin.include.php');
require_once (_FUNC.'include.php');

$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

if (!isset($PHP_SELF))
	$PHP_SELF = $_SERVER['PHP_SELF'];

require_once (_INCLUDES.'filenames.php');
require_once (_INCLUDES.'database.php');

define('SQL_CACHEDIR', _CACHE.'database/');
define('SECURITY_CODE_LENGTH', '10');
define('GRADUATED_ASSIGN', 'true');

//подключение к бд
os_db_connect() or die('Unable to connect to database server!');

include(_CLASS.'plugins.php');
include(_FUNC.'plugins.php');

include(_FUNC.'general.php');
include(_FUNC.'cache.php');
include(_FUNC.'button.php');

// Получаем настройки
$configuration_query = os_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION);
while ($configuration = os_db_fetch_array($configuration_query)) 
{
	@define($configuration['cfgKey'], $configuration['cfgValue']);
}

$db->DB_CACHE = DB_CACHE;
$db->STORE_DB_TRANSACTIONS = STORE_DB_TRANSACTIONS;
$db->DISPLAY_DB_QUERY = DISPLAY_DB_QUERY;

function osDBquery($query)
{
	return (DB_CACHE == 'true') ? os_db_queryCached($query) : os_db_query($query);
}

// получения списка активных плагинов
$p = new plugins();
//подключение активных плагинов
$p->require_plugins();

//читаем кэш
get_cache_all();

//удаляем action плагинов
remove_action_array();

//require_once (_LIB . 'phpmailer/class.phpmailer.php');
require _LIB . 'phpmailer/PHPMailerAutoload.php';
//if (EMAIL_TRANSPORT == 'smtp')
//	require_once (_LIB . 'phpmailer/class.smtp.php');

function CacheCheck()
{
	if (USE_CACHE == 'false') return false;
	if (!isset($_COOKIE['sid'])) return false;
	return true;
}

if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') 
{
	if (strlen(getenv('PATH_INFO')) > 1) {
		$GET_array = array ();
		$PHP_SELF = str_replace(getenv('PATH_INFO'), '', $_SERVER['PHP_SELF']);
		$vars = explode('/', substr(getenv('PATH_INFO'), 1));
		for ($i = 0, $n = sizeof($vars); $i < $n; $i ++) {
			if (strpos($vars[$i], '[]')) {
				$GET_array[substr($vars[$i], 0, -2)][] = $vars[$i +1];
			} else {
				$_GET[$vars[$i]] = htmlspecialchars($vars[$i +1]);
				if(get_magic_quotes_gpc()) $_GET[$vars[$i]] = addslashes($_GET[$vars[$i]]);
			}
			$i ++;
		}

		if (sizeof($GET_array) > 0) {
			while (list ($key, $value) = each($GET_array)) {
				$_GET[$key] = htmlspecialchars($value);
				if(get_magic_quotes_gpc()) $_GET[$key] = addslashes($_GET[$key]);
			}
		}
	}
}

if ((GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4')) 
{
	if (!strstr($PHP_SELF, 'ajax_shopping_cart.php') && !strstr($PHP_SELF, 'index_ajax.php') ) //исключения 
	{
		if (($ini_zlib_output_compression = (int) ini_get('zlib.output_compression')) < 1)
			ob_start('ob_gzhandler');
		else
			ini_set('zlib.output_compression_level', GZIP_LEVEL);
	}
}
// check GET/POST/COOKIE VARS
require (_CLASS.'class.inputfilter.php');
$InputFilter = new InputFilter();
$_GET = $InputFilter->process($_GET);
$_POST = $InputFilter->process($_POST);
$_COOKIE = $InputFilter->process($_COOKIE);
$_SERVER = $InputFilter->process($_SERVER);

// set the top level domains
$http_domain = os_get_top_level_domain(HTTP_SERVER);
$https_domain = os_get_top_level_domain(HTTPS_SERVER);
$cookie_info = os_get_cookie_info();

// include shopping cart class
require (_CLASS.'shopping_cart.php');
// include navigation history class
require (_CLASS.'navigation_history.php');
// some code to solve compatibility issues
require (_FUNC.'compatibility.php');
// define how the session functions will be used
require (_FUNC.'sessions.php');
// set the session name and save path
session_name('sid');
if (STORE_SESSIONS != 'mysql')
	session_save_path(_CATALOG.SESSION_WRITE_DIRECTORY);

// set the session cookie parameters
if (function_exists('session_set_cookie_params'))
{
	session_set_cookie_params(0, $cookie_info['cookie_path'], $cookie_info['cookie_domain']);
}
elseif (function_exists('ini_set'))
{
	ini_set('session.cookie_lifetime', '0');
	ini_set('session.cookie_path', $cookie_info['cookie_path']);
	ini_set('session.cookie_domain', $cookie_info['cookie_domain']);
}

// set the session ID if it exists
if (isset ($_POST[session_name()]))
{
	session_id($_POST[session_name()]);
}
elseif (($request_type == 'SSL') && isset ($_GET[session_name()]))
{
	session_id($_GET[session_name()]);
}

// start the session
$session_started = false;
if (SESSION_FORCE_COOKIE_USE == 'True') {
	os_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, $cookie_info['cookie_path'], $cookie_info['cookie_domain']);

	if (isset ($_COOKIE['cookie_test'])) {
		session_start();
		include (_INCLUDES.'tracking.php');
		$session_started = true;
	}
} else {
	session_start();
	include (_INCLUDES.'tracking.php');
	$session_started = true;
}

// check the Agent
$truncate_session_id = false;
if (get_option('check_client_agent')) 
{

	if (function_exists('os_check_agent') && os_check_agent() == 1) {
		$truncate_session_id = true;
	}
}

// verify the ssl_session_id if the feature is enabled
if (($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true)) {
	$ssl_session_id = getenv('SSL_SESSION_ID');
	if (!session_is_registered('SSL_SESSION_ID')) {
		$_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
	}

	if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
		session_destroy();
		os_redirect(os_href_link(FILENAME_SSL_CHECK));
	}
}

// verify the browser user agent if the feature is enabled
if (SESSION_CHECK_USER_AGENT == 'True') {
	$http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$http_user_agent2 = strtolower(getenv("HTTP_USER_AGENT"));
	$http_user_agent = ($http_user_agent == $http_user_agent2) ? $http_user_agent : $http_user_agent.';'.$http_user_agent2;
	if (!isset ($_SESSION['SESSION_USER_AGENT'])) {
		$_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
	}

	if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
		session_destroy();
		os_redirect(os_href_link(FILENAME_LOGIN));
	}
}

// verify the IP address if the feature is enabled
if (SESSION_CHECK_IP_ADDRESS == 'True') {
	$ip_address = os_get_ip_address();
	if (!isset ($_SESSION['SESSION_IP_ADDRESS'])) {
		$_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
	}

	if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
		session_destroy();
		os_redirect(os_href_link(FILENAME_LOGIN));
	}
}

/*
if (isset($_SESSION['http_host']) && $_SESSION['http_host'] != $_SERVER['HTTP_HOST'])
{
	os_session_destroy();
	unset($_SESSION);
}
else
	$_SESSION['http_host'] = $_SERVER['HTTP_HOST'];
*/

do_action('session_start', '');
add_path('themes_c', array
(
	'dir' => dir_path('themes').CURRENT_TEMPLATE.'/',
	'http' => http_path('themes').CURRENT_TEMPLATE.'/')
);

// set the language
if (!isset ($_SESSION['language']) || isset ($_GET['language'])) {

	include (_CLASS.'language.php');
	$lng = new language(os_input_validation($_GET['language'], 'char', ''));

	if (!isset ($_GET['language']))
		$lng->get_browser_language();

	$_SESSION['language'] = $lng->language['directory'];
	$_SESSION['languages_id'] = $lng->language['id'];
	$_SESSION['language_charset'] = $lng->language['language_charset'];
	$_SESSION['language_code'] = $lng->language['code'];
}

if (isset($_SESSION['language']) && !isset($_SESSION['language_charset']))
{
	include (_CLASS.'language.php');
	$lng = new language(os_input_validation($_SESSION['language'], 'char', ''));

	$_SESSION['language'] = $lng->language['directory'];
	$_SESSION['languages_id'] = $lng->language['id'];
	$_SESSION['language_charset'] = $lng->language['language_charset'];
	$_SESSION['language_code'] = $lng->language['code'];
}
// include the language translations
require_once (_LANG.$_SESSION['language'].'/lang.php');
require_once (dir_path('lang').$_SESSION['language'].'/db_error.php');
// currency
if (!isset ($_SESSION['currency']) || isset ($_GET['currency']) || ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $_SESSION['currency']))) {

	if (isset ($_GET['currency'])) {
		if (!$_SESSION['currency'] = os_currency_exists($_GET['currency']))
			$_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
	} else {
		$_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
	}
}
if (isset ($_SESSION['currency']) && $_SESSION['currency'] == '') {
	$_SESSION['currency'] = DEFAULT_CURRENCY;
}

// write customers status in session
require (_INCLUDES.'write_customers_status.php');

// testing new price class

require (_CLASS.'main.php');
$main = new main();

require (_CLASS.'price.php');
$osPrice = new osPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

if ($_SESSION['customers_status']['customers_status_id'] != 0) {
	if (!strstr($PHP_SELF, 'login_admin.php') && !strstr($PHP_SELF, 'captcha.php')) {
		if (EXCLUDE_ADMIN_IP_FOR_MAINTENANCE != getenv('REMOTE_ADDR')){
			if (DOWN_FOR_MAINTENANCE=='true' and !strstr($PHP_SELF,DOWN_FOR_MAINTENANCE_FILENAME)) { os_redirect(os_href_link(DOWN_FOR_MAINTENANCE_FILENAME)); }
		}
		// do not let people get to down for maintenance page if not turned on
		if (DOWN_FOR_MAINTENANCE=='false' and strstr($PHP_SELF,DOWN_FOR_MAINTENANCE_FILENAME)) {
			os_redirect(os_href_link(FILENAME_DEFAULT));
		}
	}
}

require (_INCLUDES.FILENAME_CART_ACTIONS);
// create the shopping cart & fix the cart if necesary
if (!is_object($_SESSION['cart'])) {
	$_SESSION['cart'] = new shoppingCart();
}

if (SET_WHOS_ONLINE == 'true')
{
	// include the who's online functions
	os_update_whos_online();
}

// split-page-results
require (_CLASS.'split_page_results.php');

// auto expire special products
os_expire_specials();

require (_CLASS.'product.php');

// new p URLS
if (isset ($_GET['info'])) {
	$site = explode('_', $_GET['info']);
	$pID = $site[0];
	$actual_products_id = (int) str_replace('p', '', $pID);
	$product = new product($actual_products_id);
} // also check for old 3.0.3 URLS
elseif (isset($_GET['products_id'])) {
	$actual_products_id = (int) $_GET['products_id'];
	$product = new product($actual_products_id);

}

if (@!is_object($product)) 
{
	$product = new product();	
}

// new c URLS
if (isset ($_GET['cat'])) {
	$site = explode('_', $_GET['cat']);
	$cID = $site[0];
	$cID = str_replace('c', '', $cID);
	$_GET['cPath'] = os_get_category_path($cID);
}
// new m URLS
if (isset ($_GET['manu'])) {
	$site = explode('_', $_GET['manu']);
	$mID = $site[0];
	$mID = (int)str_replace('m', '', $mID);
	$_GET['manufacturers_id'] = $mID;
}

osc_cms_eval(base64_decode('YWRkX2FjdGlvbignaGVhZCcsICdfbWxvYWQnKTtmdW5jdGlvbiBfbWxvYWQoKXtfZSAoJzxtZXRhIG5hbWU9ImdlbmVyYXRvciIgY29udGVudD0iKGMpIGJ5IE9TQy1DTVMgLCBodHRwOi8vb3NjLWNtcy5jb20iIC8+Jyk7fQ=='));

// calculate category path
if (isset ($_GET['cPath'])) {
	$cPath = os_input_validation($_GET['cPath'], 'cPath', '');
}
elseif (is_object($product) && !isset ($_GET['manufacturers_id'])) {
	if ($product->isProduct()) {
		$cPath = os_get_product_path($actual_products_id);
	} else {
		$cPath = '';
	}
} else {
	$cPath = '';
}

if (os_not_null($cPath)) {
	$cPath_array = os_parse_category_path($cPath);
	$cPath = implode('_', $cPath_array);
	$current_category_id = $cPath_array[(sizeof($cPath_array) - 1)];
} else {
	$current_category_id = 0;
}

require (dir_path('class').'shipping.php');
$shippingModules = new shipping;

// include the breadcrumb class and start the breadcrumb trail
require (_CLASS.'breadcrumb.php');
$breadcrumb = new breadcrumb;

//$breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
$breadcrumb->add(HEADER_TITLE_CATALOG, HTTP_SERVER . DIR_WS_CATALOG);

// add category names or the manufacturer name to the breadcrumb trail
if (isset ($cPath_array)) {
	for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i ++) {
		if (GROUP_CHECK == 'true') {
			$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		} else {
			$group_check='';
		}
		$categories_query = osDBquery("select
		cd.categories_name
		from ".TABLE_CATEGORIES_DESCRIPTION." cd,
		".TABLE_CATEGORIES." c
		where cd.categories_id = '".$cPath_array[$i]."'
		and c.categories_id=cd.categories_id
		".$group_check."
		and cd.language_id='".(int) $_SESSION['languages_id']."'");
		if (os_db_num_rows($categories_query,true) > 0) {
			$categories = os_db_fetch_array($categories_query,true);

			$breadcrumb->add($categories['categories_name'], os_href_link(FILENAME_DEFAULT, os_category_link($cPath_array[$i], $categories['categories_name'])));
		} else {
			break;
		}
	}
		if (isset($_GET['manufacturers_id']) OR isset($_GET['filter_id']))
		{
			$mID = (isset($_GET['filter_id']) ? $_GET['filter_id'] : $_GET['manufacturers_id']);
			$getManufacturer = $cartet->product->getManufacturer($mID);
		}
}
elseif (isset($_GET['manufacturers_id']) OR isset($_GET['filter_id']))
{
	$mID = (isset($_GET['filter_id']) ? $_GET['filter_id'] : $_GET['manufacturers_id']);

	$getManufacturer = $cartet->product->getManufacturer($mID);

	if (isset($_GET['manufacturers_id']))
	{
		if ($getManufacturer['manufacturers_page_url'] != '')
			$breadcrumb->add($getManufacturer['manufacturers_name'], os_href_link($getManufacturer['manufacturers_page_url']));
		else
			$breadcrumb->add($getManufacturer['manufacturers_name'], os_href_link(FILENAME_DEFAULT, os_manufacturer_link((int) $_GET['manufacturers_id'], $manufacturers['manufacturers_name'])));
	}
}

// add the products model/name to the breadcrumb trail
if ($product->isProduct()) {
	$breadcrumb->add($product->getBreadcrumbName(), os_href_link(FILENAME_PRODUCT_INFO, os_product_link($product->data['products_id'], $product->data['products_name'])));
}


// initialize the message stack for output messages
require (_CLASS.'message_stack.php');
$messageStack = new messageStack;


/*if (isset ($_SESSION['customer_id']))
{
	$account_type_query = os_db_query("SELECT
	account_type,
	customers_default_address_id
	FROM
	".TABLE_CUSTOMERS."
	WHERE customers_id = '".(int) $_SESSION['customer_id']."'");
	$account_type = os_db_fetch_array($account_type_query);

	// check if zone id is unset bug #0000169
	if (!isset ($_SESSION['customer_country_id']))
	{
		$zone_query = os_db_query("SELECT  entry_country_id
		FROM ".TABLE_ADDRESS_BOOK."
		WHERE customers_id='".(int) $_SESSION['customer_id']."'
		and address_book_id='".$account_type['customers_default_address_id']."'");

		$zone = os_db_fetch_array($zone_query);
		$_SESSION['customer_country_id'] = $zone['entry_country_id'];
	}
	$_SESSION['account_type'] = $account_type['account_type'];
}
else
{
	$_SESSION['account_type'] = '0';
}*/

// modification for nre graduated system
unset ($_SESSION['actual_content']);
os_count_cart();

// include the articles functions
require(_FUNC . 'articles.php');

// calculate topic path
if (isset($_GET['tPath'])) {
	$tPath = $_GET['tPath'];
} elseif (isset($_GET['articles_id'])) {
	$tPath = os_get_article_path($_GET['articles_id']);
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

// add topic names to the breadcrumb trail
if (isset($tPath_array)) {
	for ($i=0, $n=sizeof($tPath_array); $i<$n; $i++) {
		$topics_query = osDBquery("select topics_name from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . (int)$tPath_array[$i] . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");
		if (os_db_num_rows($topics_query,true) > 0) {
			$topics = os_db_fetch_array($topics_query,true);

			$SEF_parameter = '';
			if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
				$SEF_parameter = '&category='.os_cleanName($topics['topics_name']);

			$breadcrumb->add($topics['topics_name'], os_href_link(FILENAME_ARTICLES, 'tPath=' . implode('_', array_slice($tPath_array, 0, ($i+1))).$SEF_parameter));
		} else {
			break;
		}
	}
}

/**
 * Статьи
 */
if (isset($_GET['articles_id']))
{
	$article = $cartet->articles->getById($_GET['articles_id']);

	if (!empty($article))
	{
		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&article='.os_cleanName($article['articles_name']);

		$breadcrumb->add($article['articles_name'], os_href_link(FILENAME_ARTICLE_INFO, 'articles_id='.$_GET['articles_id'].$SEF_parameter));
	}
}

/**
 * Новости
 */
if (strstr($PHP_SELF, FILENAME_NEWS))
{
	$breadcrumb->add(NAVBAR_TITLE_NEWS, os_href_link(FILENAME_NEWS));
}

if (isset($_GET['news_id']))
{
	$newsDataArray = $cartet->news->getById($_GET['news_id']);

	if (!empty($newsDataArray))
	{
		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&headline='.os_cleanName($newsDataArray['headline']);

		$breadcrumb->add($newsDataArray['headline'], os_href_link(FILENAME_NEWS, 'news_id='.$newsDataArray['news_id'].$SEF_parameter, 'NONSSL'));
	}
}

if (isset($_SESSION['tracking']['http_referer'])) 
{
	$_query = isset($_SESSION['tracking']['http_referer']['query'])?$_SESSION['tracking']['http_referer']['query'] : '';
	$_host = isset($_SESSION['tracking']['http_referer']['host'])?$_SESSION['tracking']['http_referer']['host'] : '';
	$_scheme = isset($_SESSION['tracking']['http_referer']['scheme'])?$_SESSION['tracking']['http_referer']['scheme'] : '';

	$html_referer = $_scheme . '://' . $_host . $_SESSION['tracking']['http_referer']['path'] . '?' . $_query;
}

require_once(_CLASS.'template.php');
require_once(_INCLUDES.'affiliate_top.php');
require(_FUNC.'customers_extra_fields.php');

define('TAX_DECIMAL_PLACES','2');

define('_HTTP_THEMES_C', http_path ('themes').CURRENT_TEMPLATE.'/');
define('_THEMES_C', dir_path  ('themes').CURRENT_TEMPLATE.'/');

$osTemplate = new osTemplate;

$default = new osTemplate;

if (isset($os_action['box']) && !empty($os_action['box']))
{   
	global $os_action_plug;
	global $_plug_name;

	$_box = array();

	foreach ($os_action['box'] as $_tag => $priority)
	{
		if (function_exists($_tag))
		{
			$_plug_name = $os_action_plug[$_tag];
			$p->name = $os_action_plug[$_tag];
			$p->group = $p->info[$p->name]['group'];
			$p->set_dir();

			if ($p->info[$p->name]['status'] == '1')
			{
				$_box = $_tag();
			}

			if (!isset($_box['template']))
			{
				if (!empty($_box) && isset($_box['content']) && !empty($_box['title']) && !empty($_box['content']) )
				{
					$default->assign('BOX_TITLE', $_box['title']);
					$default->assign('BOX_CONTENT', $_box['content']);
					$_box_value = $default->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
					$osTemplate->assign($_tag, $_box_value);
				}
			}
			else {}
		}
	}
}
/*  //создание блоков */

if (!isset($_GET['no_box']) && @$_GET['no_box']!='true') 
{
	require (_THEMES_C.'source/boxes.php');
}


$default = new osTemplate;

if (!empty($os_rewrite_action['box']))
{
	foreach ($os_rewrite_action['box'] as $action_name => $action_name_rewrite)
	{
		if (isset($osTemplate->_tpl_vars[$action_name]))
		{
			if (function_exists($action_name_rewrite))
			{
				$_box_array = $action_name_rewrite();

				if (is_array($_box_array))
				{
					$default->assign('BOX_TITLE', $_box_array['title']);
					$default->assign('BOX_CONTENT', $_box_array['content']);
					$_box_value = $default->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
					$osTemplate->assign($action_name, $_box_value);
				}
			}

		}
	}

}

if(!defined('DIR_WS_ICONS')) define('DIR_WS_ICONS', HTTP_SERVER.DIR_WS_CATALOG.'media/icons/');

?>