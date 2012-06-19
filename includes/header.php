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

  
if ( is_page('product_info') ) 
{
	 add_style('jscript/jquery/plugins/fancybox/jquery.fancybox-1.2.5.css', $HEAD,  'fancybox');
	 add_js('jscript/jquery/plugins/fancybox/jquery.fancybox-1.2.5.pack.js', $HEAD, 'fancybox');
	 
	 add_js_code ('$(document).ready(function() {
		$("a.zoom").fancybox({
		"zoomOpacity"			: true,
		"overlayShow"			: false,
		"zoomSpeedIn"			: 500,
		"zoomSpeedOut"			: 500
	});
	});', $HEAD, 'fancybox');	 
}

    add_js('jscript/jscript_JsHttpRequest.js', $HEAD, 'jshttprequest');
    add_js('jscript/jscript_ajax.js', $HEAD, 'jscript_ajax');

if ( is_file(_THEMES_C.'javascript/general.js.php' ) )	
{
    add_head_file( _THEMES_C.'javascript/general.js.php', $HEAD );
}

if ( is_file(_THEMES_C.'head.html' ) )	
{
    add_head_file( _THEMES_C.'head.html', $HEAD );
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

if (strstr($PHP_SELF, FILENAME_CREATE_GUEST_ACCOUNT )) 
{
   add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
}

if (strstr($PHP_SELF, FILENAME_ACCOUNT_PASSWORD )) 
{
   add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
}

if (strstr($PHP_SELF, FILENAME_ACCOUNT_EDIT )) 
{
   add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
}

if (strstr($PHP_SELF, FILENAME_ADDRESS_BOOK_PROCESS )) 
{
  if (isset($_GET['delete']) == false) 
  {
        add_head_file (dir_path('includes').'form_check.js.php', $HEAD);
  }
}

if (strstr($PHP_SELF, FILENAME_CHECKOUT_SHIPPING_ADDRESS )or strstr($PHP_SELF,FILENAME_CHECKOUT_PAYMENT_ADDRESS)) {
require(dir_path('includes').'form_check.js.php');
?>
<script type="text/javascript"><!--
function check_form_optional(form_name) {
  var form = form_name;

  var firstname = form.elements['firstname'].value;
  var lastname = form.elements['lastname'].value;
  var street_address = form.elements['street_address'].value;

  if (firstname == '' && lastname == '' && street_address == '') {
    return true;
  } else {
    return check_form(form_name);
  }
}
//--></script>
<?php
}

if (strstr($PHP_SELF, FILENAME_ADVANCED_SEARCH )) 
{
   $HEAD[]['js']['action'] = 'js_check_form_advanced_search';
   $HEAD[]['js']['src'] = 'includes/general.js';
}

if (strstr($PHP_SELF, FILENAME_PRODUCT_REVIEWS_WRITE )) 
{
   $HEAD[]['js']['action'] = 'js_checkForm';
}
 
   //фильтруем массив метатегов
   $HEAD = apply_filter('head_array_detail', $HEAD);
   
   //формируем массив метатегов
   $_meta_array = osc_head_array($HEAD);
   unset($HEAD);
   //фильтруем массив метатегов
   $_meta_array = apply_filter('head_array', $_meta_array);
   
   //формирует метатеги из массива
   osc_head_print($_meta_array);
   
   do_action ('head');
   
?>
</head>
<body><?php
  do_action ('body', '');	

$osTemplate->assign('navtrail', $breadcrumb->trail(' &raquo; '));
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
$osTemplate->assign('price_list', os_href_link(FILENAME_CONTENT, 'coID=12', 'SSL'));



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

// Метки для закладок
$link_array = array( 
1 => array('current', ''),
2 => array('current', ''),
3 => array('current', ''),
4 => array('current', ''),
5 => array('current', ''),
6 => array('current', ''),
7 => array('current', '')
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

//прайслист
if ( isset($_GET['coID']) && $_GET['coID']==12)  
{
   $osTemplate->assign('7', $link_array[7][0]);
}
else
{
    $osTemplate->assign('7', $link_array[7][1]);
}

?>