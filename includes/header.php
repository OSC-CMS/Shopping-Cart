<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$_DOCTYPE = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
//фильтр doctype. позволяет изменять DOCTYPE с помошью плагинов
/*
как использовать в плагинах

add_filter('doctype', 'function_name_filter');

function function_name_filter($value)
{
   $_DOCTYPE = 'ваш новый DOCTYPE';
   return $_DOCTYPE;
}
*/

$_DOCTYPE = apply_filter('doctype', $_DOCTYPE);

//Фильтр тега <html>
/*
как использовать в плагинах

add_filter('html', 'function_name_filter');

function function_name_filter($value)
{
   $_html = '<html ваши_параметры>';
   return $_html;
}
*/

$_HTML = apply_filter('html', '<html '.HTML_PARAMS.'>');


$HEAD = array();

_e($_DOCTYPE);
_e($_HTML);
_e('<head>');

  $HEAD[]['meta']= array('http-equiv' => 'Content-Type',
                         'content' => 'text/html; charset='.$_SESSION['language_charset'] ); 
						 
  $HEAD[]['meta']= array('http-equiv' => 'Content-Style-Type',
                         'content' => 'text/css'); 
  
  include(_MODULES.FILENAME_METATAGS); 
  
  $HEAD[]['base'] = array('href' => (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG);
  
  $HEAD[]['link'] = array('rel' => 'stylesheet',
                          'type' => 'text/css',
						  'href' => http_path('themes_c').'style.css',
						  );
						  
   $HEAD[]['link'] = array('rel' => 'alternate',
                          'type' => 'application/rss+xml',
                          'title' => TEXT_RSS_NEWS,
						  'href' => FILENAME_RSS2. '?feed=news',
						  'group' => 'rss_news'
						  ); 
						  
    $HEAD[]['link'] = array('rel' => 'alternate',
                          'type' => 'application/rss+xml',
                          'title' => TEXT_RSS_ARTICLES,
						  'href' => FILENAME_RSS2. '?feed=articles',
						  'group' => 'rss_articles'); 
						  
    $HEAD[]['link'] = array('rel' => 'alternate',
                          'type' => 'application/rss+xml',
                          'title' => TEXT_RSS_CATEGORIES,
						  'href' => FILENAME_RSS2. '?feed=categories',
						  'group' => 'rss_categories'); 
						  
     $HEAD[]['link'] = array('rel' => 'alternate',
                          'type' => 'application/rss+xml',
                          'title' => TEXT_RSS_NEW_PRODUCTS,
						  'href' => FILENAME_RSS2. '?feed=new_products&amp;limit=10',
						  'group' => 'rss_new_products');  
						  
	$HEAD[]['link'] = array('rel' => 'alternate',
                          'type' => 'application/rss+xml',
                          'title' => TEXT_RSS_FEATURED_PRODUCTS,
						  'href' => FILENAME_RSS2. '?feed=featured&amp;limit=10',
						  'group' => 'rss_featured'); 	
						  
	$HEAD[]['link'] = array('rel' => 'alternate',
                          'type' => 'application/rss+xml',
                          'title' => TEXT_RSS_BEST_SELLERS,
						  'href' => FILENAME_RSS2. '?feed=best_sellers&amp;limit=10',
						  'group' => 'rss_best_sellers'); 

add_js_code ('var SITE_WEB_DIR = "'._HTTP.'";', $HEAD, 'site_web_dir');

// jQuery
add_js(_HTTP.'jscript/jquery/jquery.js', $HEAD,  'jquery');

// jQuery Modal
add_style(_HTTP.'jscript/jquery-modal/jquery.modal.css', $HEAD, 'jquery_modal');
add_js(_HTTP.'jscript/jquery-modal/jquery.modal.min.js', $HEAD, 'jquery_modal');

// jnotifier
add_style(_HTTP.'jscript/jnotifier/css/jnotifier.css', $HEAD, 'jnotifier');
add_js(_HTTP.'jscript/jnotifier/js/jnotifier.src.js', $HEAD, 'jnotifier');

// Search and Auto Completer
add_js(_HTTP.'jscript/autocomplete/jquery.autocomplete-min.js', $HEAD, 'autocomplete');

// Parsley
add_js(_HTTP.'jscript/parsley/i18n/messages.'.$_SESSION['language'].'.js', $HEAD, 'parsley');
add_js(_HTTP.'jscript/parsley/parsley.min.js', $HEAD, 'parsley');

// System JS
add_js(_HTTP.'jscript/jscript_JsHttpRequest.js', $HEAD, 'jshttprequest');
add_js(_HTTP.'jscript/jscript_ajax.js', $HEAD, 'jscript_ajax');
add_js(_HTTP.'jscript/osc_cms.js', $HEAD, 'osc_cms');

if (is_file(_THEMES_C.'javascript/general.js.php'))
{
	add_head_file( _THEMES_C.'javascript/general.js.php', $HEAD );
}
if (strstr($PHP_SELF, FILENAME_CHECKOUT_PAYMENT))
{
	add_head_code ($payment_modules->javascript_validation(), $HEAD );
}
if (strstr($PHP_SELF, FILENAME_CREATE_ACCOUNT))
{
	add_head_file ( dir_path('includes') . 'form_check.js.php', $HEAD);
}
if (strstr($PHP_SELF, FILENAME_CHECKOUT_ALTERNATIVE))
{
	add_head_file ( dir_path('includes').'form_check.js.php', $HEAD);
}
if (strstr($PHP_SELF, FILENAME_CREATE_GUEST_ACCOUNT))
{
	add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
}
if (strstr($PHP_SELF, FILENAME_ACCOUNT_PASSWORD))
{
	add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
}
if (strstr($PHP_SELF, FILENAME_ACCOUNT_EDIT))
{
	add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
}
if (strstr($PHP_SELF, FILENAME_ADDRESS_BOOK_PROCESS))
{
	if (isset($_GET['delete']) == false)
	{
		add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
	}
}
if (strstr($PHP_SELF, FILENAME_CHECKOUT_SHIPPING_ADDRESS )or strstr($PHP_SELF,FILENAME_CHECKOUT_PAYMENT_ADDRESS))
{
	require(dir_path('includes').'form_check.js.php');
}
if (strstr($PHP_SELF, 'affiliate_signup.php'))
{
    add_head_file ( dir_path('includes') . 'form_check.js.php', $HEAD);
}

//фильтруем массив метатегов
$HEAD = apply_filter('head_array_detail', $HEAD);

//формируем массив метатегов
$_meta_array = osc_head_array($HEAD);
unset($HEAD);

//фильтруем массив метатегов
$_meta_array = apply_filter('head_array', $_meta_array);

//формирует метатеги из массива
if (!empty($_meta_array))
{
	foreach ($_meta_array as $_value)
	{
		_e($_value);
	}
}

do_action ('head');
?>
</head>
<body class="no-js">
<?php
do_action ('body', '');	

$osTemplate->assign('navtrail', $breadcrumb->trail());
if (isset($_SESSION['customer_id'])) {

$osTemplate->assign('logoff',os_href_link(FILENAME_LOGOFF, '', 'SSL'));
}
if ( $_SESSION['account_type']=='0') {
$osTemplate->assign('account',os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}
$osTemplate->assign('cart', os_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$osTemplate->assign('checkout', os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$osTemplate->assign('store_name', TITLE);
$osTemplate->assign('login', os_href_link(FILENAME_LOGIN, '', 'SSL'));
$osTemplate->assign('mainpage', os_href_link(FILENAME_DEFAULT, '', 'SSL'));



  if (isset($_GET['error_message']) && os_not_null($_GET['error_message'])) {

$osTemplate->assign('error','
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="headerError">
        <td class="headerError">'. htmlspecialchars(urldecode($_GET['error_message'])).'</td>
      </tr>
    </table>');

  }

  if (isset($_GET['info_message']) && os_not_null($_GET['info_message'])) {

$osTemplate->assign('error','
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="headerInfo">
        <td class="headerInfo">'.htmlspecialchars($_GET['info_message']).'</td>
      </tr>
    </table>');

  }

if ($messageStack->size > 0)
{
	$osTemplate->assign('message', $messageStack->output());
}

// Метки для закладок
$link_array = array( 
1 => array('current', ''),
2 => array('current', ''),
3 => array('current', ''),
4 => array('current', ''),
5 => array('current', ''),
6 => array('current', ''),
);

$link_array = apply_filter('link_array', $link_array);

if (strstr($PHP_SELF, FILENAME_DEFAULT)) 
{
   $osTemplate->assign('1', $link_array[1][0]);
}
else
{
   $osTemplate->assign('1', $link_array[1][1]);
}

if (strstr($PHP_SELF, FILENAME_ACCOUNT) or strstr($PHP_SELF, FILENAME_ACCOUNT_EDIT) or strstr($PHP_SELF, FILENAME_ADDRESS_BOOK)or strstr($PHP_SELF, FILENAME_ADDRESS_BOOK_PROCESS) or strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY) or strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO) or strstr($PHP_SELF, FILENAME_ACCOUNT_PASSWORD) or strstr($PHP_SELF, FILENAME_NEWSLETTER)) 
{
   $osTemplate->assign('2', $link_array[2][0]);
}
else
{
   $osTemplate->assign('2', $link_array[2][1]);
}

if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) 
{
   $osTemplate->assign('3', $link_array[3][0]);
}
else
{
   $osTemplate->assign('3', $link_array[3][1]);
}

if (strstr($PHP_SELF, FILENAME_CHECKOUT_SHIPPING) or strstr($PHP_SELF, FILENAME_CHECKOUT_PAYMENT) or strstr($PHP_SELF, FILENAME_CHECKOUT_CONFIRMATION) or strstr($PHP_SELF, FILENAME_CHECKOUT_SUCCESS)) 
{
   $osTemplate->assign('4', $link_array[4][0]);
}
else
{
   $osTemplate->assign('4', $link_array[4][1]);
}

if (strstr($PHP_SELF, FILENAME_LOGOFF)) 
{
   $osTemplate->assign('5', $link_array[5][0]);
}
else
{
   $osTemplate->assign('5', $link_array[5][1]);
}

if (strstr($PHP_SELF, FILENAME_LOGIN)) 
{
   $osTemplate->assign('6', $link_array[6][0]);
}
else
{
   $osTemplate->assign('6', $link_array[6][1]);
}

?>