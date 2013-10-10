<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

if ($_SESSION['install']['db'])
	define('DB_PREFIX', $_SESSION['install']['db']['prefix']);
else
	define('DB_PREFIX', 'os_');

define('TABLE_ADDRESS_BOOK', DB_PREFIX.'address_book');
define('TABLE_ADDRESS_FORMAT', DB_PREFIX.'address_format');
define('TABLE_BANNERS', DB_PREFIX.'banners');
define('TABLE_BANNERS_HISTORY', DB_PREFIX.'banners_history');
define('TABLE_CAMPAIGNS', DB_PREFIX.'campaigns');
define('TABLE_CATEGORIES', DB_PREFIX.'categories');
define('TABLE_CATEGORIES_DESCRIPTION', DB_PREFIX.'categories_description');
define('TABLE_CONFIGURATION', DB_PREFIX.'configuration');
define('TABLE_CONFIGURATION_GROUP', DB_PREFIX.'configuration_group');
define('TABLE_COUNTER', DB_PREFIX.'counter');
define('TABLE_COUNTER_HISTORY', DB_PREFIX.'counter_history');
define('TABLE_COUNTRIES', DB_PREFIX.'countries');
define('TABLE_CURRENCIES', DB_PREFIX.'currencies');
define('TABLE_CUSTOMERS', DB_PREFIX.'customers');
define('TABLE_CUSTOMERS_BASKET', DB_PREFIX.'customers_basket');
define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', DB_PREFIX.'customers_basket_attributes');
define('TABLE_CUSTOMERS_INFO', DB_PREFIX.'customers_info');
define('TABLE_CUSTOMERS_IP', DB_PREFIX.'customers_ip');
define('TABLE_CUSTOMERS_STATUS', DB_PREFIX.'customers_status');
define('TABLE_CUSTOMERS_STATUS_HISTORY', DB_PREFIX.'customers_status_history');
define('TABLE_LANGUAGES', DB_PREFIX.'languages');
define('TABLE_MANUFACTURERS', DB_PREFIX.'manufacturers');
define('TABLE_MANUFACTURERS_INFO', DB_PREFIX.'manufacturers_info');
define('TABLE_NEWSLETTER_RECIPIENTS', DB_PREFIX.'newsletter_recipients');
define('TABLE_ORDERS', DB_PREFIX.'orders');
define('TABLE_ORDERS_PRODUCTS', DB_PREFIX.'orders_products');
define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', DB_PREFIX.'orders_products_attributes');
define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', DB_PREFIX.'orders_products_download');
define('TABLE_ORDERS_STATUS', DB_PREFIX.'orders_status');
define('TABLE_ORDERS_STATUS_HISTORY', DB_PREFIX.'orders_status_history');
define('TABLE_ORDERS_TOTAL', DB_PREFIX.'orders_total');
define('TABLE_SHIPPING_STATUS', DB_PREFIX.'shipping_status');
define('TABLE_PERSONAL_OFFERS_BY',DB_PREFIX.'personal_offers_by_customers_status_');
define('TABLE_PRODUCTS', DB_PREFIX.'products');
define('TABLE_PRODUCTS_ATTRIBUTES', DB_PREFIX.'products_attributes');
define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', DB_PREFIX.'products_attributes_download');
define('TABLE_PRODUCTS_DESCRIPTION', DB_PREFIX.'products_description');
define('TABLE_PRODUCTS_NOTIFICATIONS', DB_PREFIX.'products_notifications');
define('TABLE_PRODUCTS_IMAGES', DB_PREFIX.'products_images');
define('TABLE_PRODUCTS_OPTIONS', DB_PREFIX.'products_options');
define('TABLE_PRODUCTS_OPTIONS_VALUES', DB_PREFIX.'products_options_values');
define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', DB_PREFIX.'products_options_values_to_products_options');
define('TABLE_PRODUCTS_TO_CATEGORIES', DB_PREFIX.'products_to_categories');
define('TABLE_PRODUCTS_VPE',DB_PREFIX.'products_vpe');
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
define('TABLE_PRODUCTS_XSELL', DB_PREFIX.'products_xsell');
define('TABLE_PRODUCTS_XSELL_GROUPS',DB_PREFIX.'products_xsell_grp_name');
define('TABLE_CONTENT_MANAGER', DB_PREFIX.'content_manager');
define('TABLE_PRODUCTS_CONTENT',DB_PREFIX.'products_content');
define('TABLE_COUPON_GV_CUSTOMER', DB_PREFIX.'coupon_gv_customer');
define('TABLE_COUPON_GV_QUEUE', DB_PREFIX.'coupon_gv_queue');
define('TABLE_COUPON_REDEEM_TRACK', DB_PREFIX.'coupon_redeem_track');
define('TABLE_COUPON_EMAIL_TRACK', DB_PREFIX.'coupon_email_track');
define('TABLE_COUPONS', DB_PREFIX.'coupons');
define('TABLE_COUPONS_DESCRIPTION', DB_PREFIX.'coupons_description');
define('TABLE_BLACKLIST', DB_PREFIX.'card_blacklist');
define('TABLE_CAMPAIGNS_IP',DB_PREFIX.'campaigns_ip');
define('TABLE_LATEST_NEWS', DB_PREFIX.'latest_news');
define('TABLE_FEATURED', DB_PREFIX.'featured');
define('TABLE_ARTICLES', DB_PREFIX.'articles');
define('TABLE_ARTICLES_DESCRIPTION', DB_PREFIX.'articles_description');
define('TABLE_ARTICLES_TO_TOPICS', DB_PREFIX.'articles_to_topics');
define('TABLE_TOPICS', DB_PREFIX.'topics');
define('TABLE_TOPICS_DESCRIPTION', DB_PREFIX.'topics_description');
define('TABLE_ARTICLES_XSELL', DB_PREFIX.'articles_xsell');
define('TABLE_MONEYBOOKERS',DB_PREFIX.'payment_moneybookers');
define('TABLE_MONEYBOOKERS_COUNTRIES',DB_PREFIX.'payment_moneybookers_countries');
define('TABLE_MONEYBOOKERS_CURRENCIES',DB_PREFIX.'payment_moneybookers_currencies');
define('TABLE_NEWSLETTER_TEMP',DB_PREFIX.'module_newsletter_temp_');
define('TABLE_PERSONAL_OFFERS',DB_PREFIX.'personal_offers_by_customers_status_');
define('TABLE_SHIP2PAY',DB_PREFIX.'ship2pay');
define('TABLE_PRODUCTS_EXTRA_FIELDS', DB_PREFIX.'products_extra_fields');
define('TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS', DB_PREFIX.'products_to_products_extra_fields');
define('TABLE_FAQ', DB_PREFIX.'faq');
define('TABLE_HELP', DB_PREFIX.'help');
define('TABLE_COMPANIES', DB_PREFIX.'companies');
define('TABLE_PERSONS', DB_PREFIX.'persons');
define('TABLE_EXTRA_FIELDS',DB_PREFIX.'extra_fields');
define('TABLE_EXTRA_FIELDS_INFO',DB_PREFIX.'extra_fields_info');
define('TABLE_CUSTOMERS_TO_EXTRA_FIELDS',DB_PREFIX.'customers_to_extra_fields');


function is_ajax_request()
{
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
    {
	    return false;
    }
    return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

function display($template_name, $data = array())
{
    extract($data);
    ob_start();
    include PATH . "themes/{$template_name}.php";
    return ob_get_clean();
}

function run_step($step, $is_submit = false)
{
    require PATH . "pages/{$step['id']}.php";
    $result = step($is_submit);
    return $result;
}

function text_status($value, $condition)
{
    if ($condition)
        return '<span class="positive">'.$value.'</span>';
    else
        return '<span class="negative">'.$value.'</span>';
}

function get_langs()
{
    $dir = PATH . 'languages';
    $dir_context = opendir($dir);

    $list = array();

    while ($next = readdir($dir_context))
    {
        if (in_array($next, array('.', '..'))){ continue; }
        if (strpos($next, '.') === 0){ continue; }
        if (!is_dir($dir.'/'.$next)) { continue; }

        $list[] = $next;
    }

    return $list;
}

function t($l)
{
	global $language;
	return $language[$l];
}

function os_db_connect_installer($server, $username, $password, $link = 'db_link')
{
	global $$link, $db_error;
	$db_error = false;

	$$link = @mysql_connect($server, $username, $password) or $db_error = mysql_error();

	@mysql_query("SET SQL_MODE= ''");
	@mysql_query("SET CHARACTER SET utf8");
	@mysql_query("SET NAMES utf8");
	@mysql_query("SET COLLATION utf8_general_ci");

	return $$link;
}

function os_db_select_db($database)
{
	return mysql_select_db($database);
}

function os_db_query($query, $link = 'db_link')
{
	global $$link;
	global $query_counts;

	$query_counts++;

	$result = mysql_query($query, $$link) or os_db_error($query, mysql_errno(), mysql_error());

	if (!$result)
	{
		os_db_error($query, mysql_errno(), mysql_error());
	}

	return $result;
}
function os_db_error($query, $errno, $error)
{
	return $query."\n".$error;
}

function copy_folder($d1, $d2)
{
	if (is_dir($d1))
	{
		$d = dir( $d1 );
		while (false !== ($entry = $d->read()))
		{
			if ($entry != '.' && $entry != '..')
				@copy_folder("$d1/$entry", "$d2/$entry");
		}
		$d->close();
	}
	else
	{
		$ok = @copy($d1, $d2);
	}
}

function os_get_country_list($name, $selected = '', $parameters = '')
{
	$countries_array = array();
//    Probleme mit register_globals=off -> erstmal nur auskommentiert. Kann u.U. gelС†scht werden.
	$countries = os_get_countriesList();

	for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
		$countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
	}
	//if (is_array($name)) return os_draw_pull_down_menuNote($name, $countries_array, $selected, $parameters);
	return os_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
}

function os_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
	$field = '<select name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '"';

	if (os_not_null($parameters)) $field .= ' ' . $parameters;

	$field .= '>';

	if (empty($default) && isset($GLOBALS[$name])) $default = $GLOBALS[$name];

	for ($i=0, $n=sizeof($values); $i<$n; $i++) {
		$field .= '<option value="' . os_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
		if ($default == $values[$i]['id']) {
			$field .= ' selected="selected"';
		}

		$field .= '>' . os_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
	}
	$field .= '</select>';

	if ($required == true) $field .= TEXT_FIELD_REQUIRED;

	return $field;
}

function os_get_countriesList($countries_id = '', $with_iso_codes = false)
{
	$countries_array = array();
	if (os_not_null($countries_id)) {
		if ($with_iso_codes == true) {
			$countries = os_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . $countries_id . "' and status = '1' order by countries_name");
			$countries_values = os_db_fetch_array($countries);
			$countries_array = array('countries_name' => $countries_values['countries_name'],
				'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
				'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
		} else {
			$countries = os_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $countries_id . "' and status = '1'");
			$countries_values = os_db_fetch_array($countries);
			$countries_array = array('countries_name' => $countries_values['countries_name']);
		}
	} else {
		$countries = os_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " where status = '1' order by countries_name");

		while ($countries_values = os_db_fetch_array($countries)) {
			$countries_array[] = array('countries_id' => $countries_values['countries_id'],
				'countries_name' => $countries_values['countries_name']);
		}
	}

	return $countries_array;
}

function os_not_null($value)
{
	if (is_array($value))
	{
		if (sizeof($value) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

function os_db_fetch_array(&$db_query,$cq=false)
{
	if (is_array($db_query))
	{
		$curr = current($db_query);
		next($db_query);
		return $curr;
	}

	return mysql_fetch_assoc($db_query);
}

function os_parse_input_field_data($data, $parse)
{
	return strtr(trim($data), $parse);
}

function os_db_prepare_input($string)
{
	if (is_string($string))
	{
		return trim(stripslashes($string));
	}
	elseif (is_array($string))
	{
		reset($string);
		while (list($key, $value) = each($string))
		{
			$string[$key] = os_db_prepare_input($value);
		}
		return $string;
	}
	else
	{
		return $string;
	}
}

function os_db_num_rows($db_query,$cq=false)
{
	if (DB_CACHE=='true' && $cq)
	{
		if (!count($db_query)) return false;
		return count($db_query);
	}
	else
	{
		if (!is_array($db_query)) return mysql_num_rows($db_query);
	}
}

function os_draw_pull_down_menuNote($data, $values, $default = '', $parameters = '', $required = false) {
	$field = '<select name="' . os_parse_input_field_data($data['name'], array('"' => '&quot;')) . '"';

	if (os_not_null($parameters)) $field .= ' ' . $parameters;

	$field .= '>';

	if (empty($default) && isset($GLOBALS[$data['name']])) $default = $GLOBALS[$data['name']];

	for ($i=0, $n=sizeof($values); $i<$n; $i++) {
		$field .= '<option value="' . os_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
		if ($default == $values[$i]['id']) {
			$field .= ' selected="selected"';
		}

		$field .= '>' . os_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
	}
	$field .= '</select>'.$data['text'];

	if ($required == true) $field .= TEXT_FIELD_REQUIRED;

	return $field;
}

function os_encrypt_password($plain)
{
	$password=md5($plain);
	return $password;
}

function os_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link')
{
	reset($data);

	if ($action == 'insert') {
		$query = 'insert into ' . $table . ' (';
		while (list($columns, ) = each($data)) {
			$query .= $columns . ', ';
		}
		$query = substr($query, 0, -2) . ') values (';
		reset($data);
		while (list(, $value) = each($data)) {
			$value = (is_Float($value)) ? sprintf("%.F",$value) : (string)($value);
			switch ($value) {
				case 'now()':
					$query .= 'now(), ';
					break;
				case 'null':
					$query .= 'null, ';
					break;
				default:
					$query .= '\'' . os_db_input($value) . '\', ';
					break;
			}
		}
		$query = substr($query, 0, -2) . ')';
	} elseif ($action == 'update') {
		$query = 'update ' . $table . ' set ';
		while (list($columns, $value) = each($data)) {
			$value = (is_Float($value)) ? sprintf("%.F",$value) : (string)($value);
			switch ($value) {
				case 'now()':
					$query .= $columns . ' = now(), ';
					break;
				case 'null':
					$query .= $columns .= ' = null, ';
					break;
				default:
					$query .= $columns . ' = \'' . os_db_input($value) . '\', ';
					break;
			}
		}
		$query = substr($query, 0, -2) . ' where ' . $parameters;
	}

	return os_db_query($query, $link);
}

function os_db_input($string, $link = 'db_link')
{
	global $$link;

	if (function_exists('mysql_real_escape_string'))
	{
		return mysql_real_escape_string($string, $$link);
	}
	elseif (function_exists('mysql_escape_string'))
	{
		return mysql_escape_string($string);
	}
	return addslashes($string);
}