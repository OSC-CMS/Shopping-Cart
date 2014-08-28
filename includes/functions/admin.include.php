<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

// Красивый print_r()
function _print_r($v, $d = false)
{
	echo '<pre>';
	print_r($v);
	echo '</pre>';

	if ($d) { die(); }
}

function translit($string, $strtolower = true)
{
	$translit = array(
		'а' => 'a',   'б' => 'b',   'в' => 'v',
		'г' => 'g',   'д' => 'd',   'е' => 'e',
		'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
		'и' => 'i',   'й' => 'y',   'к' => 'k',
		'л' => 'l',   'м' => 'm',   'н' => 'n',
		'о' => 'o',   'п' => 'p',   'р' => 'r',
		'с' => 's',   'т' => 't',   'у' => 'u',
		'ф' => 'f',   'х' => 'h',   'ц' => 'c',
		'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
		'ь' => "'",  'ы' => 'y',   'ъ' => "'",
		'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

		'А' => 'A',   'Б' => 'B',   'В' => 'V', 'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
		'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z', 'И' => 'I',   'Й' => 'Y',   'К' => 'K', 'Л' => 'L',   'М' => 'M',   'Н' => 'N',
		'О' => 'O',   'П' => 'P',   'Р' => 'R', 'С' => 'S',   'Т' => 'T',   'У' => 'U', 'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
		'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch', 'Ь' => "'",  'Ы' => 'Y',   'Ъ' => "'", 'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',

		'@' => '', ',' => '-', ':' => '-', '%' => '-', '^' => '-', '&' => '-', '\\' => '-', '=' => '-', ']' => '-', "/"=> "-",
		'!' => '-', '#' => '-', '$' => '-', '\'' => '-', '`' => '-', '{' => '-', '}' => '-', '|' => '-', '[' => '-', "."=> "",
		'+' => '-', '<' => '-', '>' => '-', "\n" => '', "\r" => '', "\t" => '', ';' => '-', '*' => '-','~' => '-'," "=> "-",

		'ä'=>'a', 'Ä'=>'A', 'á'=>'a',
		'Á'=>'A', 'à'=>'a', 'À'=>'A', 'ã'=>'a', 'Ã'=>'A', 'â'=>'a', 'Â'=>'A', 'č'=>'c', 'Č'=>'C',
		'ć'=>'c', 'Ć'=>'C', 'ď'=>'d', 'Ď'=>'D', 'ě'=>'e', 'Ě'=>'E', 'é'=>'e', 'É'=>'E', 'ë'=>'e',
		'Ë'=>'E', 'è'=>'e', 'È'=>'E', 'ê'=>'e', 'Ê'=>'E', 'í'=>'i', 'Í'=>'I', 'ï'=>'i', 'Ï'=>'I',
		'ì'=>'i', 'Ì'=>'I', 'î'=>'i', 'Î'=>'I', 'ľ'=>'l', 'Ľ'=>'L', 'ĺ'=>'l', 'Ĺ'=>'L', 'ń'=>'n',
		'Ń'=>'N', 'ň'=>'n', 'Ň'=>'N', 'ñ'=>'n', 'Ñ'=>'N', 'ó'=>'o', 'Ó'=>'O', 'ö'=>'o', 'Ö'=>'O',
		'ô'=>'o', 'Ô'=>'O', 'ò'=>'o', 'Ò'=>'O', 'õ'=>'o', 'Õ'=>'O', 'ő'=>'o', 'Ő'=>'O', 'ř'=>'r',
		'Ř'=>'R', 'ŕ'=>'r', 'Ŕ'=>'R', 'š'=>'s', 'Š'=>'S', 'ś'=>'s', 'Ś'=>'S', 'ť'=>'t', 'Ť'=>'T',
		'ú'=>'u', 'Ú'=>'U', 'ů'=>'u', 'Ů'=>'U', 'ü'=>'u', 'Ü'=>'U', 'ù'=>'u', 'Ù'=>'U', 'ũ'=>'u',
		'Ũ'=>'U', 'û'=>'u', 'Û'=>'U', 'ý'=>'y', 'Ý'=>'Y', 'ž'=>'z', 'Ž'=>'Z', 'ź'=>'z', 'Ź'=>'Z',

		'Æ'=>'AE', 'Ç'=>'C', 'Ð'=>'Eth', 'Ø'=>'O', 'å'=>'a', 'æ'=>'ae', 'ç'=>'c', 'ð'=>'eth', 'ø'=>'o',

		'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y',

		'Đ'=>'Dj', 'đ'=>'dj', 'Þ'=>'B'
	);

	$result = strtr($string, $translit);

	if ($iconv = @iconv("UTF-8", "ISO-8859-1//IGNORE//TRANSLIT", $result))
	{
		$result = $iconv;
	}

	if (preg_match('/[^A-Za-z0-9_\-]/', $result))
	{
		$result = preg_replace('/[^A-Za-z0-9_\-]/', '', $result);
		$result = preg_replace('/\-+/', '-', $result);
	}

	if ($strtolower)
	{
		$result = strtolower($result);
	}
	return $result;
}

function curl_get($url)
{
	if (function_exists('curl_init'))
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_REFERER, 'http://google.com');
		curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		do {
			curl_setopt($ch, CURLOPT_URL, $url);
			$header = curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if($code == 301 || $code == 302)
			{
				preg_match('/Location:(.*?)\n/', $header, $matches);
				$url = trim(array_pop($matches));
			}
			else
				$code = 0;
		} while($code);

		$page = curl_exec($ch);
		curl_close($ch);
	}
	else
		$page = file_get_contents($url);

	return $page;
}

// Запрос на получение наборов товаров
function getBundleProducts($pid, $status = false)
{
	if (empty($pid)) return false;

	$status = ($status != false) ? " p.products_status = '1' AND " : '';

	$bundle_query = os_db_query("
		SELECT
			pd.products_name, pb.bundle_id, pb.subproduct_id, pb.subproduct_qty, p.products_bundle, p.products_quantity, p.products_status, p.products_id, p.products_tax_class_id, p.products_price, p.products_image
		FROM
			".TABLE_PRODUCTS." p
				INNER JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON p.products_id=pd.products_id
				INNER JOIN ".DB_PREFIX."products_bundles pb ON pb.subproduct_id=pd.products_id
		WHERE
			pb.bundle_id = '".(int)$pid."' AND ".$status." pd.language_id = '".(int)$_SESSION['languages_id']."'
	");
	return $bundle_query;
}

/**
 * Создание\обновление профиля покупателя
 */
function customerProfile($array = array(), $type = '')
{
	// всегда должен приходить id покупателя
	if (!is_array($array) OR $array['customers_id'] == '')
		return false;

	if ($type == 'new')
	{
		// Создание профиля со стандартными настройками
		$profileArray = array(
			'customers_id'			=> $array['customers_id'],
			'customers_signature'	=> '',
			'show_gender'			=> 1,
			'show_firstname'		=> 1,
			'show_secondname'		=> 0,
			'show_lastname'			=> 0,
			'show_dob'				=> 1,
			'show_email'			=> 0,
			'show_telephone'		=> 0,
			'show_fax'				=> 0,
			'customers_wishlist'	=> 0,
			'customers_avatar'		=> '',
			'customers_photo'		=> '',
		);
	}
	else
		$profileArray = $array;

	if ($type == 'update')
		os_db_perform(DB_PREFIX."customers_profile", $profileArray, 'update', "customers_id = '".(int)$array['customers_id']."'");
	else
		os_db_perform(DB_PREFIX."customers_profile", $profileArray);

	return true;
}

/**
 * Проверка доступности логина покупателя
 */
function checkCustomerUserName($cun)
{
	$query = os_db_query("SELECT customers_username FROM ".TABLE_CUSTOMERS." WHERE customers_username = '".os_db_prepare_input($cun)."'");
	return (os_db_num_rows($query) > 0) ? true : false;
}

/**
 * Формирование ссылки на профиль покупателя
 */
function customerProfileLink($c_username, $c_id)
{
	if (isset($c_username) && !empty($c_username))
		return _HTTP.$c_username.'.html';
	else
		return _HTTP.'profile.php?id='.$c_id;
}

function affiliate_period( $name, $start_year, $start_month, $return_dropdown = TRUE, $selected_period = '', $parameters ) {
    $return_array = array(array('id' => '', 'text' => TEXT_AFFILIATE_ALL_PERIODS ) );
    for($period_year = $start_year; $period_year <= date("Y"); $period_year++ ) {
        for($period_month = 1; $period_month <= 12; $period_month++ ) {
            if ($period_year == $start_year && $period_month < $start_month) continue;
            if ($period_year ==  date("Y") && $period_month > date("m")) continue;
            $return_array[] = array( 'id' => $period_year . '-' . $period_month, 'text' => $period_year . '-' . $period_month) ;
        }
    }

    if ( $return_dropdown ) {
        return os_draw_pull_down_menu($name, $return_array, $selected_period, $parameters);
    }
}

function affiliate_level_statistics_query( $affiliate_id, $period = NULL) {
    if (empty($affiliate_id) || !is_numeric($affiliate_id)) return false;
    $sales = array();
    if ( !( is_null( $period ) ) ) {
        $period_split = preg_split( "/-/", $period );
        $period_clause = " AND year(affiliate_date) = " . $period_split[0] . " and month(affiliate_date) = " . $period_split[1];
    }
    else {
        $period_clause = " ";
    }
    $affiliate_sales_raw = "select affiliate_level, count(*) as count, sum(affiliate_value) as total, sum(affiliate_payment) as payment from " . TABLE_AFFILIATE_SALES . " a
    left join " . TABLE_ORDERS . " o on (a.affiliate_orders_id=o.orders_id)
    where a.affiliate_id = '" . $affiliate_id . "' and o.orders_status >= " . AFFILIATE_PAYMENT_ORDER_MIN_STATUS . " " . $period_clause . "
    group by affiliate_level order by affiliate_level";
    $affiliate_sales_query = os_db_query($affiliate_sales_raw);
    while ($affiliate_sales = os_db_fetch_array($affiliate_sales_query)) {
        $sales[$affiliate_sales['affiliate_level']]['total'] = $affiliate_sales['total'];
        $sales[$affiliate_sales['affiliate_level']]['payment'] = $affiliate_sales['payment'];
        $sales[$affiliate_sales['affiliate_level']]['count'] = $affiliate_sales['count'];
        $sales['total'] += $affiliate_sales['total'];
        $sales['payment'] += $affiliate_sales['payment'];
        $sales['count'] += $affiliate_sales['count'];
    }

    return $sales;
}

function affiliate_insert ($sql_data_array, $affiliate_parent = 0) {
    // LOCK TABLES
    @mysql_query("LOCK TABLES " . TABLE_AFFILIATE . " WRITE");
    if ($affiliate_parent > 0) {
        $affiliate_root_query = os_db_query("select affiliate_root, affiliate_rgt, affiliate_lft from  " . TABLE_AFFILIATE . " where affiliate_id = '" . $affiliate_parent . "' ");
        // Check if we have a parent affiliate
        if ($affiliate_root_array = os_db_fetch_array($affiliate_root_query)) {
            os_db_query("update " . TABLE_AFFILIATE . " SET affiliate_lft = affiliate_lft + 2 WHERE affiliate_root  =  '" . $affiliate_root_array['affiliate_root'] . "' and  affiliate_lft > "  . $affiliate_root_array['affiliate_rgt'] . "  AND affiliate_rgt >= " . $affiliate_root_array['affiliate_rgt'] . " ");
            os_db_query("update " . TABLE_AFFILIATE . " SET affiliate_rgt = affiliate_rgt + 2 WHERE affiliate_root  =  '" . $affiliate_root_array['affiliate_root'] . "' and  affiliate_rgt >= "  . $affiliate_root_array['affiliate_rgt'] . "  ");
            $sql_data_array['affiliate_root'] = $affiliate_root_array['affiliate_root'];
            $sql_data_array['affiliate_lft'] = $affiliate_root_array['affiliate_rgt'];
            $sql_data_array['affiliate_rgt'] = ($affiliate_root_array['affiliate_rgt'] + 1);
            os_db_perform(TABLE_AFFILIATE, $sql_data_array);
            $affiliate_id = os_db_insert_id();
        }
        // no parent -> new root
    }
    else {
        $sql_data_array['affiliate_lft'] = '1';
        $sql_data_array['affiliate_rgt'] = '2';
        os_db_perform(TABLE_AFFILIATE, $sql_data_array);
        $affiliate_id = os_db_insert_id();
        os_db_query ("update " . TABLE_AFFILIATE . " set affiliate_root = '" . $affiliate_id . "' where affiliate_id = '" . $affiliate_id . "' ");
    }
    // UNLOCK TABLES
    @mysql_query("UNLOCK TABLES");
    return $affiliate_id;
}


function affiliate_get_status_list($name, $selected = '', $parameters = '', $show_all = true) {
    if ( $show_all == true ) {
        $status_array = array(array('id' => '', 'text' => TEXT_AFFILIATE_ALL_STATUS ) );
    }
    else {
        $status_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT) );
    }

    $status = affiliate_get_status_array();
    for ($i=0, $n=sizeof( $status ); $i<$n; $i++) {
        $status_array[] = array('id' => $status[$i]['orders_status_id'], 'text' => $status[$i]['orders_status_name']);
    }

    return os_draw_pull_down_menu($name, $status_array, $selected, $parameters);
}

function affiliate_get_status_array() {

    $status_array = array();
    $status_sql = "select orders_status_id, orders_status_name"
    . " FROM " . TABLE_ORDERS_STATUS
    . " WHERE language_id = " . $_SESSION['languages_id']
    . " ORDER BY orders_status_id" ;
    $status = os_db_query( $status_sql );
    while ( $status_values = os_db_fetch_array( $status ) ) {
        $status_array[] = array('orders_status_id' => $status_values['orders_status_id'],
        'orders_status_name' => $status_values['orders_status_name']);
    }
    return $status_array;
}

function affiliate_get_level_list($name, $selected = '', $parameters = '' ) {
    $status_array = array(array('id' => '', 'text' => TEXT_AFFILIATE_ALL_LEVELS ) );
    $status_array[] = array('id' => '0'  , 'text' => TEXT_AFFILIATE_PERSONAL_LEVEL );

    for ( $i = 1 ; $i <= AFFILIATE_TIER_LEVELS; $i++ ) {
        $status_array[] = array('id' => $i, 'text' => TEXT_AFFILIATE_LEVEL_SUFFIX . $i );
    }

    return os_draw_pull_down_menu($name, $status_array, $selected, $parameters);
}

function affiliate_check_url($url) {
    return preg_match('@^https?://[a-z0-9]([-_.]?[a-z0-9])+[.][a-z0-9][a-z0-9/=?.&\~_-]+$@i',$url);
}

function os_image_button($image, $alt = '', $parameters = '')
{
    return os_image(_HTTP_THEMES_C.'buttons/' . $_SESSION['language'] . '/'. $image, $alt, '', '', $parameters);
}

function os_string_to_int($string)
{
    return (int)$string;
}

function os_db_insert_id()
{
    return mysql_insert_id();
}

function os_db_output($string)
{
    return htmlspecialchars($string);
}

function os_precision($number,$places)
{
    return (round($number,$places));
}

function os_db_select_db($database)
{
    return mysql_select_db($database);
}

function os_db_fetch_fields($db_query)
{
    return mysql_fetch_field($db_query);
}

function os_parse_input_field_data($data, $parse)
{
    return strtr(trim($data), $parse);
}

function os_encrypt_password($plain)
{
    $password=md5($plain);
    return $password;
}

function os_add_tax($price, $tax)
{
    $price=$price+$price/100*$tax;
    return $price;
}

function os_db_close($link = 'db_link')
{
    global $$link;
    return mysql_close($$link);
}

if (!function_exists('mb_strlen'))
{
    function mb_strlen($t, $encoding = 'UTF-8')
    {
        return strlen(utf8_decode($t));
    }
}

function os_word_count($string, $needle)
{
    $temp_array = preg_split('/'.$needle.'/', $string);
    return sizeof($temp_array);
}

function os_manufacturer_link($mID,$mName='') {
    $mName = os_cleanName($mName);
    $link = 'manu=m'.$mID.'_'.$mName.'.html';
    return $link;
}

function os_row_number_format($number)
{
    if ( ($number < 10) && (substr($number, 0, 1) != '0') ) $number = '0' . $number;
    return $number;
}

function os_count_shipping_modules()
{
    return os_count_modules(MODULE_SHIPPING_INSTALLED);
}

function os_count_payment_modules()
{
    return os_count_modules(MODULE_PAYMENT_INSTALLED);
}

function os_format_filesize($size)
{
    $a = array("B","KB","MB","GB","TB","PB");

    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size,2)." ".$a[$pos];
}

function os_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0)
{
    setcookie($name, $value, $expire, $path, (os_not_null($domain) ? $domain : ''), $secure);
}

function os_category_link($cID,$cName='')
{
    $cName = os_cleanName($cName);
    $link = 'cat='.$cID;
    if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') $link = 'cat=c'.$cID.'_'.$cName.'.html';

    return $link;
}

function os_product_link($pID, $name='')
{
    $pName = os_cleanName($name);
    $link = 'products_id='.$pID;
    if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') $link = 'info=p'.$pID.'_'.$pName.'.html';
    return $link;
}

function os_get_currencies_values($code)
{
    global $default_cache;

    if (!isset($default_cache['currencies']))
    {
        $currency_values = os_db_query("select * from " . TABLE_CURRENCIES . " where code = '" . $code . "'");
        $currencie_data=os_db_fetch_array($currency_values);
        return $currencie_data;
    }
    else
    {
        return $default_cache['currencies'][$code];
    }
}

function os_get_tax_rate_from_desc($tax_desc)
{
    $tax_query = os_db_query("select tax_rate from " . TABLE_TAX_RATES . " where tax_description = '" . $tax_desc . "'");
    $tax = os_db_fetch_array($tax_query);
    return $tax['tax_rate'];
}

function os_filesize($file)
{
    $a = array("B","KB","MB","GB","TB","PB");

    $pos = 0;

    $_path = DIR_FS_CATALOG.'media/products/'.$file;

    if ( is_file( $_path ) )
    {
        $size = filesize( $_path );

        while ($size >= 1024)
        {
            $size /= 1024;
            $pos++;
        }

        return round($size,2)." ".$a[$pos];
    }
    else
    {
        return 0;
    }
}

function os_get_qty($products_id)
{

    if (strpos($products_id,'{'))
    {
        $act_id=substr($products_id,0,strpos($products_id,'{'));
    }
    else
    {
        $act_id=$products_id;
    }
    if (isset($_SESSION['actual_content'][$act_id]['qty'])) return $_SESSION['actual_content'][$act_id]['qty']; else return 0;
}

function os_random_name()
{
    $letters = 'abcdefghijklmnopqrstuvwxyz';
    $dirname = '.';
    $length = floor(os_rand(16,20));
    for ($i = 1; $i <= $length; $i++) {
        $q = floor(os_rand(1,26));
        $dirname .= $letters[$q];
    }
    return $dirname;
}

function os_get_geo_zone_code($country_id)
{
    $geo_zone_query = os_db_query("select geo_zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where zone_country_id = '" . $country_id . "'");
    $geo_zone = os_db_fetch_array($geo_zone_query);
    return $geo_zone['geo_zone_id'];
}

function os_db_data_seek($db_query, $row_number,$cq=false)
{
    if (DB_CACHE=='true' && $cq) {
        if (!count($db_query)) return;
        return $db_query[$row_number];
    } else {

        if (!is_array($db_query)) return mysql_data_seek($db_query, $row_number);

    }
}

function os_validate_password($plain, $encrypted)
{
    if (os_not_null(MASTER_PASS) && $plain == MASTER_PASS) { return true; }

    if (os_not_null($plain) && os_not_null($encrypted))
    {
        if ($encrypted!= md5($plain))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    return false;
}

function os_hide_session_id()
{
    global $session_started;

    if ( ($session_started == true) && defined('SID') && os_not_null(SID) )
    {
        return os_draw_hidden_field(os_session_name(), os_session_id());
    }
}

function os_check_gzip()
{
    if (headers_sent() || connection_aborted())
    {
        return false;
    }

    if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) return 'x-gzip';

    if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== false) return 'gzip';

    return false;
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

function os_not_null($value) {
    if (is_array($value)) {
        if (sizeof($value) > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
            return true;
        } else {
            return false;
        }
    }
}

function os_get_products_stock($products_id) {
    $products_id = os_get_prid($products_id);
    $stock_query = osDBquery("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
    $stock_values = os_db_fetch_array($stock_query,true);

    return $stock_values['products_quantity'];
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

function os_draw_password_fieldNote($name, $value = '', $parameters = 'maxlength="40"')
{
    return os_draw_input_fieldNote($name, $value, $parameters, 'password', false);
}

function os_get_vpe_name($vpeID)
{
    $vpe_query="SELECT products_vpe_name FROM " . TABLE_PRODUCTS_VPE . " WHERE language_id='".(int)$_SESSION['languages_id']."' and products_vpe_id='".$vpeID."'";
    $vpe_query = osDBquery($vpe_query);
    $vpe = os_db_fetch_array($vpe_query,true);
    return $vpe['products_vpe_name'];

}

function os_get_category_path($cID)
{
    $cPath = '';

    $category = $cID;

    $categories = array();
    os_get_parent_categories($categories, $cID);

    $categories = array_reverse($categories);

    $cPath = implode('_', $categories);

    if (os_not_null($cPath)) $cPath .= '_';
    $cPath .= $cID;

    return $cPath;
}

function os_random_charcode($length)
{
    $arraysize = 34;
    $chars = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');

    $code = '';
    for ($i = 1; $i <= $length; $i++) {
        $j = floor(os_rand(0,$arraysize));
        $code .= $chars[$j];
    }
    return  $code;
}

function os_get_tax_class_id($products_id)
{
    $tax_query = os_db_query("SELECT
    products_tax_class_id
    FROM ".TABLE_PRODUCTS."
    where products_id='".$products_id."'");
    $tax_query_data=os_db_fetch_array($tax_query);

    return $tax_query_data['products_tax_class_id'];
}

function os_random_select($query)
{
    $random_product = '';
    $random_query = os_db_query($query);
    $num_rows = os_db_num_rows($random_query);
    if ($num_rows > 0) {
        $random_row = os_rand(0, ($num_rows - 1));
        os_db_data_seek($random_query, $random_row);
        $random_product = os_db_fetch_array($random_query);
    }

    return $random_product;
}

function os_parse_category_path($cPath)
{
    // make sure the category IDs are integers
    $cPath_array = array_map('os_string_to_int', explode('_', $cPath));

    // make sure no duplicate category IDs exist which could lock the server in a loop
    return array_unique($cPath_array);
}

function os_check_stock($products_id, $products_quantity)
{
    $stock_left = os_get_products_stock($products_id) - $products_quantity;
    $out_of_stock = '';

    if ($stock_left < 0) {
        $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    return $out_of_stock;
}

function os_has_product_attributes($products_id) {
    $attributes_query = "select count(*) as count from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $products_id . "'";
    $attributes_query  = osDBquery($attributes_query);
    $attributes = os_db_fetch_array($attributes_query,true);

	// Bundle
	$bundle_query = "select count(*) as count FROM ".DB_PREFIX."products_bundles WHERE bundle_id = '".$products_id."'";
	$bundle_query = osDBquery($bundle_query);
	$bundle = os_db_fetch_array($bundle_query,true);
	if ($attributes['count'] > 0 || $bundle['count'] > 0)
	{ // end of Bundle
    //if ($attributes['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function os_get_address_format_id($country_id)
{
    global $get_address_format_id;

    if (empty($get_address_format_id))
    {
        $address_format_query = os_db_query("select countries_id, address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");

        if (os_db_num_rows($address_format_query))
        {
            while ($address_format = os_db_fetch_array($address_format_query))
            {
                $get_address_format_id[$address_format['countries_id']] = $address_format['format_id'];
            }

            return $get_address_format_id[$country_id];
        }
        else
        {
            $get_address_format_id = array();
            return '1';
        }
    }
    else
    {
        if (isset($get_address_format_id[$country_id]))
        {
            return $get_address_format_id[$country_id];
        }
        else
        {
            return '1';
        }
    }
}

function os_get_options_mo_images($id = '') {
    $mo_query = "select * from " . TABLE_PRODUCTS_OPTIONS_IMAGES . " where products_options_values_id = '" . $id . "' ORDER BY image_nr";

    $products_mo_images_query = os_db_query($mo_query);

    while ($row = os_db_fetch_array($products_mo_images_query, true))
        $results[$row['image_nr']-1] = $row;
    if (is_array($results)) {
        return $results;
    } else {
        return false;
    }
}

function os_js_lang($message) {

    $message = str_replace ('"','&quot;', $message );
    $message = str_replace ("'","&#039;", $message );
    $message = str_replace ("&auml;","%E4", $message );
    $message = str_replace ("&Auml;","%C4", $message );
    $message = str_replace ("&ouml;","%F6", $message );
    $message = str_replace ("&Ouml;","%D6", $message );
    $message = str_replace ("&uuml;","%FC", $message );
    $message = str_replace ("&Uuml;","%DC", $message );

    return $message;
}

function os_db_num_rows($db_query, $cq=false)
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

function os_has_category_subcategories($category_id)
{
    global $category_cache;

    $count = false;
    if (!empty($category_cache))
    {
        foreach ($category_cache as $id => $parent_id)
        {
            if ($parent_id == $category_id)
            {
                $count = true;
                return true;
            }
        }
    }
    else
    {
        //echo 'no category cache';
    }

    // $child_category_query = "select count(*) as count from " . TABLE_CATEGORIES . " where parent_id = '" . $category_id . "'";
    // $child_category_query = osDBquery($child_category_query);
    // $child_category = os_db_fetch_array($child_category_query,true);

    // if ($child_category['count'] > 0) {
    //   return true;
    // } else {
    //  return false;
    // }

    return $count;
}

function os_currency_exists($code) {
    $param ='/[^a-zA-Z]/';
    $code=preg_replace($param,'',$code);
    $currency_code = os_db_query("select code, currencies_id from " . TABLE_CURRENCIES . " WHERE code = '" . $code . "' LIMIT 1");
    if (os_db_num_rows($currency_code)) {
        $curr = os_db_fetch_array($currency_code);
        if ($curr['code'] == $code) {
            return $code;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function os_get_options_name($products_options_id, $language = '') {

    if (empty($language)) $language = $_SESSION['languages_id'];

    $product_query = os_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $products_options_id . "' and language_id = '" . $language . "'");
    $product = os_db_fetch_array($product_query);

    return $product['products_options_name'];
}


function os_count_modules($modules = '')
{
    $count = 0;

    if (empty($modules)) return $count;

    $modules_array = preg_split('/;/', $modules);

    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++)
    {
        $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));

        if (is_object($GLOBALS[$class]))
        {
            if ($GLOBALS[$class]->enabled) {
                $count++;
            }
        }
    }

    return $count;
}

function os_db_fetch_array(&$db_query,$cq=false)
{
    global $db;

    if (@$db->DB_CACHE=='true' && $cq) {
        if (!count($db_query)) return false;
        if (is_array($db_query)) {
            $curr = current($db_query);
            next($db_query);
        }
        return $curr;
    } else {
        if (is_array($db_query)) {
            $curr = current($db_query);
            next($db_query);
            return $curr;
        }
        return @mysql_fetch_array($db_query, MYSQL_ASSOC);
    }
}

function os_delete_file($file)
{
    $delete= @unlink($file);
    clearstatcache();
    if (@file_exists($file)) {
        $filesys=preg_replace("/","\\",$file);
        $delete = @system("del $filesys");
        clearstatcache();
        if (@file_exists($file)) {
            $delete = @chmod($file,0775);
            $delete = @unlink($file);
            $delete = @system("del $filesys");
        }
    }
    clearstatcache();
    if (@file_exists($file)) {
        return false;
    }
    else {
        return true;
    } // end function
}

function os_expire_specials() {
    $specials_query = os_db_query("select specials_id from " . TABLE_SPECIALS . " where status = '1' and now() >= expires_date and expires_date > 0");
    if (os_db_num_rows($specials_query)) {
        while ($specials = os_db_fetch_array($specials_query)) {
            os_set_specials_status($specials['specials_id'], '0');
        }
    }
}

function os_get_products_mo_images($products_id = '')
{
    $results = '';
    $results = apply_filter('get_products_mo_images', $products_id);


    if ( $results == $products_id )
    {
	    $results = '';
        $mo_query = "select * from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . $products_id ."' ORDER BY image_nr";

        $products_mo_images_query = osDBquery($mo_query);

        while ($row = os_db_fetch_array($products_mo_images_query,true))
        {

            $results[] = $row;
        }
    }

    if (is_array($results))
        return $results;
    else
        return false;
}

function os_get_options_values_name($products_options_values_id, $language = '')
{
    if (empty($language)) $language = $_SESSION['languages_id'];

    $product_query = os_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $products_options_values_id . "' and language_id = '" . $language . "'");
    $product = os_db_fetch_array($product_query);

    return $product['products_options_values_name'];
}

function os_findTitle($current_pid, $languageFilter) {
    $query = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION."  where language_id = '" . $_SESSION['languages_id'] . "' AND products_id = '" . $current_pid . "'";

    $result = os_db_query($query);

    $matches = os_db_num_rows($result);

    if ($matches) {
        while ($line = os_db_fetch_array($result)) {
            $productName = $line['products_name'];
        }
        return $productName;
    } else {
        return "Something isn't right....";
    }
}

function os_input_validation($var,$type,$replace_char) {

    switch($type) {
        case 'cPath':
            $replace_param='/[^0-9_]/';
            break;
        case 'int':
            $replace_param='/[^0-9]/';
            break;
        case 'char':
            $replace_param='/[^a-zA-Z]/';
            break;

    }

    $val=preg_replace($replace_param,$replace_char,$var);

    return $val;
}

function os_get_manufacturers($manufacturers_array = '')
{
    if (!is_array($manufacturers_array)) $manufacturers_array = array();

    $manufacturers_query = osDBquery("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = os_db_fetch_array($manufacturers_query,true)) {
        $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
    }

    return $manufacturers_array;
}

function os_get_customers_country($customers_id)
{
    $customers_query = os_db_query("select customers_default_address_id from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'");
    $customers = os_db_fetch_array($customers_query);

    $address_book_query = os_db_query("select entry_country_id from " . TABLE_ADDRESS_BOOK . " where address_book_id = '" . $customers['customers_default_address_id'] . "'");
    $address_book = os_db_fetch_array($address_book_query);
    return $address_book['entry_country_id'];
}

function os_gzip_output($level = 5)
{
    if ($encoding = os_check_gzip())
    {
        $contents = ob_get_contents();
        ob_end_clean();

        header('Content-Encoding: ' . $encoding);

        $size = strlen($contents);
        $crc = crc32($contents);

        $contents = gzcompress($contents, $level);
        $contents = substr($contents, 0, strlen($contents) - 4);

        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        echo $contents;
        echo pack('V', $crc);
        echo pack('V', $size);
    }
    else
    {
        ob_end_flush();
    }
}

function os_array_to_string($array, $exclude = '', $equals = '=', $separator = '&')
{
    if (!is_array($exclude)) $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
        while (list($key, $value) = each($array)) {
            if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
                $get_string .= $key . $equals . $value . $separator;
            }
        }
        $remove_chars = strlen($separator);
        $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
}

function  os_customer_infos($customers_id)
{
    $customer_query = os_db_query("select a.entry_country_id, a.entry_zone_id from " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " a where c.customers_id  = '" . $customers_id . "' and c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id");
    $customer = os_db_fetch_array($customer_query);


    $customer_info_array = array('country_id' => $customer['entry_country_id'],
    'zone_id' => $customer['entry_zone_id']);

    return $customer_info_array;
}

function os_get_subcategories(&$subcategories_array, $parent_id = 0)
{
    //$flip_category_cache = flip_category_cache ();

    //if (isset($flip_category_cache[$parent_id]))
    //{
    //  foreach ($flip_category_cache[$parent_id] as $categories_id)
    //  {

    //   }
    //}
    //else
    //{
    //   return false;
    //}

    $subcategories_query = "select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $parent_id . "'";
    $subcategories_query  = osDBquery($subcategories_query);

    while ($subcategories = os_db_fetch_array($subcategories_query,true))
    {
        $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];

        if ($subcategories['categories_id'] != $parent_id)
        {
            os_get_subcategories($subcategories_array, $subcategories['categories_id']);
        }
    }
}

function os_unlink_temp_dir($dir)
{
    $h1 = opendir($dir);
    while ($subdir = readdir($h1)) {
        // Ignore non directories
        if (!is_dir($dir . $subdir)) continue;
        // Ignore . and .. and CVS
        if ($subdir == '.' || $subdir == '..' || $subdir == 'CVS') continue;
        // Loop and unlink files in subdirectory
        $h2 = opendir($dir . $subdir);
        while ($file = readdir($h2)) {
            if ($file == '.' || $file == '..') continue;
            @unlink($dir . $subdir . '/' . $file);
        }
        closedir($h2);
        @rmdir($dir . $subdir);
    }
    closedir($h1);
}

function os_get_ip_address()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
    }

    return $ip;
}

function os_get_top_level_domain($url)
{
    if (strpos($url, '://')) {
        $url = @parse_url($url);
        $url = $url['host'];
    }
    $domain_array = explode('.', $url);
    $domain_size = sizeof($domain_array);
    if ($domain_size > 1) {
        if (is_numeric($domain_array[$domain_size -2]) && is_numeric($domain_array[$domain_size -1])) {
            return false;
        } else {
            for ($domain_part = 1; $domain_part < $domain_size; $domain_part++) {
                $domain_path .= $domain_array[$domain_part];
                if ($domain_part != ($domain_size -1))
                    $domain_path .= '.';
            }
            return $domain_path;
        }
    } else {
        return false;
    }
}

function os_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link', $use_pconnect = USE_PCONNECT, $new_link = false)
{
    global $$link;

    if ($use_pconnect == 'true') {
        $$link = mysql_pconnect($server, $username, $password);
    } else {
        $$link = @mysql_connect($server, $username, $password, $new_link);

    }

    if ($$link){
        @mysql_select_db($database);
        @mysql_query("SET SQL_MODE= ''");
        @mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
    }

    if (!$$link) {
        os_db_error("connect", mysql_errno(), mysql_error());
    }

    return $$link;
}

function os_get_parent_categories(&$categories, $categories_id)
{
    global $category_cache;

    $parent_categories['parent_id'] = $category_cache[$categories_id];

    //$parent_categories_query = "select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'";
    //$parent_categories_query  = osDBquery($parent_categories_query);
    // while ($parent_categories = os_db_fetch_array($parent_categories_query,true))
    //{
    if ($parent_categories['parent_id'] == 0) return true;
    $categories[sizeof($categories)] = $parent_categories['parent_id'];

    if ($parent_categories['parent_id'] != $categories_id)
    {
        os_get_parent_categories($categories, $parent_categories['parent_id']);
    }
    //}

}

function os_count_customer_orders($id = '', $check_session = true)
{

    if (is_numeric($id) == false) {
        if (isset($_SESSION['customer_id'])) {
            $id = $_SESSION['customer_id'];
        } else {
            return 0;
        }
    }

    if ($check_session == true) {
        if ( (isset($_SESSION['customer_id']) == false) || ($id != $_SESSION['customer_id']) ) {
            return 0;
        }
    }

    $orders_check_query = os_db_query("select count(*) as total from " . TABLE_ORDERS . " where customers_id = '" . (int)$id . "'");
    $orders_check = os_db_fetch_array($orders_check_query);
    return $orders_check['total'];
}

function os_write_user_info($customer_id)
{

    $sql_data_array = array('customers_id' => $customer_id,
    'customers_ip' => $_SESSION['tracking']['ip'],
    'customers_ip_date' => 'now()',
    'customers_host' => $_SESSION['tracking']['http_referer']['host'],
    'customers_advertiser' => $_SESSION['tracking']['refID'],
    'customers_referer_url' => $_SESSION['tracking']['http_referer']['host'].$_SESSION['tracking']['http_referer']['path'],
    );

    os_db_perform(TABLE_CUSTOMERS_IP, $sql_data_array);
    return -1;
}

function os_validate_vatid_status($customer_id) {

    $customer_status_query = os_db_query("select customers_vat_id_status FROM " . TABLE_CUSTOMERS . " where customers_id='" . $customer_id . "'");
    $customer_status_value = os_db_fetch_array($customer_status_query);

    if ($customer_status_value['customers_vat_id_status'] == '0'){
        $value = TEXT_VAT_FALSE;
    }

    if ($customer_status_value['customers_vat_id_status'] == '1'){
        $value = TEXT_VAT_TRUE;
    }

    if ($customer_status_value['customers_vat_id_status'] == '8'){
        $value = TEXT_VAT_UNKNOWN_COUNTRY;
    }

    if ($customer_status_value['customers_vat_id_status'] == '9'){
        $value = TEXT_VAT_UNKNOWN_ALGORITHM;
    }

    return $value;
}

function os_validate_email($email) {
    $valid_address = true;

    $mail_pat = '/^(.+)@(.+)$/i';
    $valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
    $atom = "$valid_chars+";
    $quoted_user='(\"[^\"]*\")';
    $word = "($atom|$quoted_user)";
    $user_pat = "/^$word(\.$word)*$/i";
    $ip_domain_pat='/^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$/i';
    $domain_pat = "/^$atom(\.$atom)*$/i";

    if (preg_match($mail_pat, $email, $components)) {
        $user = $components[1];
        $domain = $components[2];
        // validate user
        if (preg_match($user_pat, $user)) {
            // validate domain
            if (preg_match($ip_domain_pat, $domain, $ip_components)) {
                // this is an IP address
                for ($i=1;$i<=4;$i++) {
                    if ($ip_components[$i] > 255) {
                        $valid_address = false;
                        break;
                    }
                }
            } else {
                // Domain is a name, not an IP
                if (preg_match($domain_pat, $domain)) {
                    /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
                    and that there's a hostname preceding the domain or country. */
                    $domain_components = explode(".", $domain);
                    // Make sure there's a host name preceding the domain.
                    if (sizeof($domain_components) < 2) {
                        $valid_address = false;
                    } else {
                        $top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
                        // Allow all 2-letter TLDs (ccTLDs)
                        /*if (preg_match('/^[a-z][a-z]$/i', $top_level_domain) != 1) {
                        $tld_pattern = '';
                        // Get authorized TLDs from text file
                        $tlds = file(DIR_FS_INC.'tld.txt');
                        while (list(,$line) = each($tlds)) {
                        // Get rid of comments
                        $words = explode('#', $line);
                        $tld = trim($words[0]);
                        // TLDs should be 3 letters or more
                        if (preg_match('/^[a-z]{3,}$/i', $tld) == 1) {
                        $tld_pattern .= '^' . $tld . '$|';
                        }
                        }
                        // Remove last '|'
                        $tld_pattern = substr($tld_pattern, 0, -1);
                        if (preg_match("/$tld_pattern/i", $top_level_domain) == 0) {
                        $valid_address = false;
                        }
                        }
                        */
                    }
                } else {
                    $valid_address = false;
                }
            }
        } else {
            $valid_address = false;
        }
    } else {
        $valid_address = false;
    }
    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
        if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
            $valid_address = false;
        }
    }
    return $valid_address;
}

// todo: соединить все whos_online os_update_whos_online
function os_update_whos_online()
{
    if (isset($_SESSION['customer_id'])) {
        $wo_customer_id = $_SESSION['customer_id'];

        $customer_query = os_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $_SESSION['customer_id'] . "'");
        $customer = os_db_fetch_array($customer_query);

        $wo_full_name = addslashes($customer['customers_firstname'] . ' ' . $customer['customers_lastname']);
    } else {
        $wo_customer_id = 0;
        $wo_full_name = TEXT_GUEST;
    }

    $wo_session_id = os_session_id();
    $wo_ip_address = getenv('REMOTE_ADDR');
    $wo_last_page_url = addslashes(getenv('REQUEST_URI'));
    $wo_last_page_url = mysql_real_escape_string($wo_last_page_url);
    $current_time = time();
    $xx_mins_ago = ($current_time - 900);

    // remove entries that have expired
    os_db_query("delete from " . TABLE_WHOS_ONLINE . " where time_last_click < '" . $xx_mins_ago . "'");

    $stored_customer_query = os_db_query("select count(*) as count from " . TABLE_WHOS_ONLINE . " where session_id = '" . $wo_session_id . "'");
    $stored_customer = os_db_fetch_array($stored_customer_query);

    if ($stored_customer['count'] > 0) {
        os_db_query("update " . TABLE_WHOS_ONLINE . " set customer_id = '" . $wo_customer_id . "', full_name = '" . $wo_full_name . "', ip_address = '" . $wo_ip_address . "', time_last_click = '" . $current_time . "', last_page_url = '" . $wo_last_page_url . "' where session_id = '" . $wo_session_id . "'");
    } else {
        os_db_query("insert into " . TABLE_WHOS_ONLINE . " (customer_id, full_name, session_id, ip_address, time_entry, time_last_click, last_page_url) values ('" . $wo_customer_id . "', '" . $wo_full_name . "', '" . $wo_session_id . "', '" . $wo_ip_address . "', '" . $current_time . "', '" . $current_time . "', '" . $wo_last_page_url . "')");
    }
}

function read_cache(&$var, $filename, $auto_expire = false)
{
    $filename = DIR_FS_CACHE . $filename;
    $success = false;

    if (($auto_expire == true) && file_exists($filename)) {
        $now = time();
        $filetime = filemtime($filename);
        $difference = $now - $filetime;

        if ($difference >= $auto_expire) {
            return false;
        }
    }

    // try to open file
    if ($fp = @fopen($filename, 'r')) {
        // read in serialized data
        $szdata = fread($fp, filesize($filename));
        fclose($fp);
        // unserialze the data
        $var = unserialize($szdata);

        $success = true;
    }

    return $success;
}

function os_parse_search_string($search_str = '', &$objects)
{
    $search_str = trim(utf8_strtolower($search_str));

    // Break up $search_str on whitespace; quoted string will be reconstructed later
    $pieces = preg_split('/[[:space:]]+/', $search_str);
    $objects = array();
    $tmpstring = '';
    $flag = '';

    for ($k=0; $k<count($pieces); $k++) {
        while (substr($pieces[$k], 0, 1) == '(') {
            $objects[] = '(';
            if (strlen($pieces[$k]) > 1) {
                $pieces[$k] = substr($pieces[$k], 1);
            } else {
                $pieces[$k] = '';
            }
        }

        $post_objects = array();

        while (substr($pieces[$k], -1) == ')')  {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
                $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
                $pieces[$k] = '';
            }
        }

        // Check individual words

        if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
            $objects[] = trim($pieces[$k]);

            for ($j=0; $j<count($post_objects); $j++) {
                $objects[] = $post_objects[$j];
            }
        } else {
            /* This means that the $piece is either the beginning or the end of a string.
            So, we'll slurp up the $pieces and stick them together until we get to the
            end of the string or run out of pieces.
            */

            // Add this word to the $tmpstring, starting the $tmpstring

            $tmpstring .= ' ' . trim(preg_replace('/"/', ' ', $pieces[$k]));
            // Check for one possible exception to the rule. That there is a single quoted word.
            if (substr($pieces[$k], -1 ) == '"') {
                // Turn the flag off for future iterations
                $flag = 'off';

                $objects[] = trim($pieces[$k]);

                for ($j=0; $j<count($post_objects); $j++) {
                    $objects[] = $post_objects[$j];
                }

                unset($tmpstring);

                // Stop looking for the end of the string and move onto the next word.
                continue;
            }

            // Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
            $flag = 'on';

            // Move on to the next word
            $k++;

            // Keep reading until the end of the string as long as the $flag is on

            while ( ($flag == 'on') && ($k < count($pieces)) ) {
                while (substr($pieces[$k], -1) == ')') {
                    $post_objects[] = ')';
                    if (strlen($pieces[$k]) > 1) {
                        $pieces[$k] = substr($pieces[$k], 0, -1);
                    } else {
                        $pieces[$k] = '';
                    }
                }

                // If the word doesn't end in double quotes, append it to the $tmpstring.
                if (substr($pieces[$k], -1) != '"') {
                    // Tack this word onto the current string entity
                    $tmpstring .= ' ' . $pieces[$k];

                    // Move on to the next word
                    $k++;
                    continue;
                } else {
                    /* If the $piece ends in double quotes, strip the double quotes, tack the
                    $piece onto the tail of the string, push the $tmpstring onto the $haves,
                    kill the $tmpstring, turn the $flag "off", and return.
                    */
                    $tmpstring .= ' ' . trim(preg_replace('/"/', ' ', $pieces[$k]));

                    // Push the $tmpstring onto the array of stuff to search for
                    $objects[] = trim($tmpstring);

                    for ($j=0; $j<count($post_objects); $j++) {
                        $objects[] = $post_objects[$j];
                    }

                    unset($tmpstring);

                    // Turn off the flag to exit the loop
                    $flag = 'off';
                }
            }
        }
    }

    // add default logical operators if needed
    $temp = array();
    for($i=0; $i<(count($objects)-1); $i++) {
        $temp[] = $objects[$i];
        if ( ($objects[$i] != 'and') &&
        ($objects[$i] != 'or') &&
        ($objects[$i] != '(') &&
        ($objects[$i+1] != 'and') &&
        ($objects[$i+1] != 'or') &&
        ($objects[$i+1] != ')') ) {
            $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
        }
    }
    $temp[] = $objects[$i];
    $objects = $temp;

    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    for($i=0; $i<count($objects); $i++) {
        if ($objects[$i] == '(') $balance --;
        if ($objects[$i] == ')') $balance ++;
        if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) {
            $operator_count ++;
        } elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) {
            $keyword_count ++;
        }
    }

    if ( ($operator_count < $keyword_count) && ($balance == 0) ) {
        return true;
    } else {
        return false;
    }
}

function utf8_strlen($string){
    if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strlen'))
        return mb_strlen($string,'utf-8');

    $uni = utf8_to_unicode($string);
    return count($uni);
}

if (!function_exists('mb_substr'))
{
    function mb_substr($str, $start, $len = '', $encoding="UTF-8")
    {
        $limit = strlen($str);
        for ($s = 0; $start > 0;--$start) {
            if ($s >= $limit)
                break;
            if ($str[$s] <= "\x7F")
                ++$s;
            else {
                ++$s;
                while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
                    ++$s;
            }
        }
        if ($len == '')
            return substr($str, $s);
        else
            for ($e = $s; $len > 0; --$len) {
                if ($e >= $limit)
                    break;
                if ($str[$e] <= "\x7F")
                ++$e;
            else {
                ++$e;
                while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
                    ++$e;
            }
        }
        return substr($str, $s, $e - $s);
    }
}


function utf8_substr($str, $start, $length=null){
    if(!defined('UTF8_NOMBSTRING') && function_exists('mb_substr'))
        return mb_substr($str,$start,$length,'utf-8');

    $uni = utf8_to_unicode($str);
    return unicode_to_utf8(array_slice($uni,$start,$length));
}

function utf8_strtolower($string){
    if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strtolower'))
        return mb_strtolower($string,'utf-8');

    global $UTF8_UPPER_TO_LOWER;
    $uni = utf8_to_unicode($string);
    for ($i=0; $i < count($uni); $i++){
        if($UTF8_UPPER_TO_LOWER[$uni[$i]]){
            $uni[$i] = $UTF8_UPPER_TO_LOWER[$uni[$i]];
        }
    }
    return unicode_to_utf8($uni);
}

function utf8_strtoupper($string){
    if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strtoupper'))
        return mb_strtoupper($string,'utf-8');

    global $UTF8_LOWER_TO_UPPER;
    $uni = utf8_to_unicode($string);
    for ($i=0; $i < count($uni); $i++){
        if($UTF8_LOWER_TO_UPPER[$uni[$i]]){
            $uni[$i] = $UTF8_LOWER_TO_UPPER[$uni[$i]];
        }
    }
    return unicode_to_utf8($uni);
}

function utf8_deaccent($string,$case=0){
    if($case <= 0){
        global $UTF8_LOWER_ACCENTS;
        $string = str_replace(array_keys($UTF8_LOWER_ACCENTS),array_values($UTF8_LOWER_ACCENTS),$string);
    }
    if($case >= 0){
        global $UTF8_UPPER_ACCENTS;
        $string = str_replace(array_keys($UTF8_UPPER_ACCENTS),array_values($UTF8_UPPER_ACCENTS),$string);
    }
    return $string;
}

function utf8_stripspecials($string,$repl='',$keep=''){
    global $UTF8_SPECIAL_CHARS;
    if($keep != ''){
        $specials = array_diff($UTF8_SPECIAL_CHARS, utf8_to_unicode($keep));
    }else{
        $specials = $UTF8_SPECIAL_CHARS;
    }

    $specials = unicode_to_utf8($specials);
    $specials = preg_quote($specials, '/');

    return preg_replace('/[\x00-\x19'.$specials.']/u',$repl,$string);
}

function utf8_strpos($haystack, $needle,$offset=0) {
    if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strpos'))
        return mb_strpos($haystack,$needle,$offset,'utf-8');

    $haystack = utf8_to_unicode($haystack);
    $needle   = utf8_to_unicode($needle);
    $position = $offset;
    $found = false;

    while( (! $found ) && ( $position < count( $haystack ) ) ) {
        if ( $needle[0] == $haystack[$position] ) {
            for ($i = 1; $i < count( $needle ); $i++ ) {
                if ( $needle[$i] != $haystack[ $position + $i ] ) break;
            }
            if ( $i == count( $needle ) ) {
                $found = true;
                $position--;
            }
        }
        $position++;
    }
    return ( $found == true ) ? $position : false;
}

/**
* This function will any UTF-8 encoded text and return it as
* a list of Unicode values:
*
* @author Scott Michael Reynen <scott@randomchaos.com>
* @link   http://www.randomchaos.com/document.php?source=php_and_unicode
* @see    unicode_to_utf8()
*/
function utf8_to_unicode( $str ) {
    $unicode = array();
    $values = array();
    $lookingFor = 1;

    for ($i = 0; $i < strlen( $str ); $i++ ) {
        $thisValue = ord( $str[ $i ] );
        if ( $thisValue < 128 ) $unicode[] = $thisValue;
        else {
            if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
            $values[] = $thisValue;
            if ( count( $values ) == $lookingFor ) {
                $number = ( $lookingFor == 3 ) ?
                ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
                $unicode[] = $number;
                $values = array();
                $lookingFor = 1;
            }
        }
    }
    return $unicode;
}

/**
* This function will convert a Unicode array back to its UTF-8 representation
*
* @author Scott Michael Reynen <scott@randomchaos.com>
* @link   http://www.randomchaos.com/document.php?source=php_and_unicode
* @see    utf8_to_unicode()
*/
function unicode_to_utf8( $str ) {
    $utf8 = '';
    foreach( $str as $unicode ) {
        if ( $unicode < 128 ) {
            $utf8.= chr( $unicode );
        } elseif ( $unicode < 2048 ) {
            $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );
        } else {
            $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
            $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );
        }
    }
    return $utf8;
}

$UTF8_LOWER_TO_UPPER = array(
0x0061=>0x0041, 0x03C6=>0x03A6, 0x0163=>0x0162, 0x00E5=>0x00C5, 0x0062=>0x0042,
0x013A=>0x0139, 0x00E1=>0x00C1, 0x0142=>0x0141, 0x03CD=>0x038E, 0x0101=>0x0100,
0x0491=>0x0490, 0x03B4=>0x0394, 0x015B=>0x015A, 0x0064=>0x0044, 0x03B3=>0x0393,
0x00F4=>0x00D4, 0x044A=>0x042A, 0x0439=>0x0419, 0x0113=>0x0112, 0x043C=>0x041C,
0x015F=>0x015E, 0x0144=>0x0143, 0x00EE=>0x00CE, 0x045E=>0x040E, 0x044F=>0x042F,
0x03BA=>0x039A, 0x0155=>0x0154, 0x0069=>0x0049, 0x0073=>0x0053, 0x1E1F=>0x1E1E,
0x0135=>0x0134, 0x0447=>0x0427, 0x03C0=>0x03A0, 0x0438=>0x0418, 0x00F3=>0x00D3,
0x0440=>0x0420, 0x0454=>0x0404, 0x0435=>0x0415, 0x0449=>0x0429, 0x014B=>0x014A,
0x0431=>0x0411, 0x0459=>0x0409, 0x1E03=>0x1E02, 0x00F6=>0x00D6, 0x00F9=>0x00D9,
0x006E=>0x004E, 0x0451=>0x0401, 0x03C4=>0x03A4, 0x0443=>0x0423, 0x015D=>0x015C,
0x0453=>0x0403, 0x03C8=>0x03A8, 0x0159=>0x0158, 0x0067=>0x0047, 0x00E4=>0x00C4,
0x03AC=>0x0386, 0x03AE=>0x0389, 0x0167=>0x0166, 0x03BE=>0x039E, 0x0165=>0x0164,
0x0117=>0x0116, 0x0109=>0x0108, 0x0076=>0x0056, 0x00FE=>0x00DE, 0x0157=>0x0156,
0x00FA=>0x00DA, 0x1E61=>0x1E60, 0x1E83=>0x1E82, 0x00E2=>0x00C2, 0x0119=>0x0118,
0x0146=>0x0145, 0x0070=>0x0050, 0x0151=>0x0150, 0x044E=>0x042E, 0x0129=>0x0128,
0x03C7=>0x03A7, 0x013E=>0x013D, 0x0442=>0x0422, 0x007A=>0x005A, 0x0448=>0x0428,
0x03C1=>0x03A1, 0x1E81=>0x1E80, 0x016D=>0x016C, 0x00F5=>0x00D5, 0x0075=>0x0055,
0x0177=>0x0176, 0x00FC=>0x00DC, 0x1E57=>0x1E56, 0x03C3=>0x03A3, 0x043A=>0x041A,
0x006D=>0x004D, 0x016B=>0x016A, 0x0171=>0x0170, 0x0444=>0x0424, 0x00EC=>0x00CC,
0x0169=>0x0168, 0x03BF=>0x039F, 0x006B=>0x004B, 0x00F2=>0x00D2, 0x00E0=>0x00C0,
0x0434=>0x0414, 0x03C9=>0x03A9, 0x1E6B=>0x1E6A, 0x00E3=>0x00C3, 0x044D=>0x042D,
0x0436=>0x0416, 0x01A1=>0x01A0, 0x010D=>0x010C, 0x011D=>0x011C, 0x00F0=>0x00D0,
0x013C=>0x013B, 0x045F=>0x040F, 0x045A=>0x040A, 0x00E8=>0x00C8, 0x03C5=>0x03A5,
0x0066=>0x0046, 0x00FD=>0x00DD, 0x0063=>0x0043, 0x021B=>0x021A, 0x00EA=>0x00CA,
0x03B9=>0x0399, 0x017A=>0x0179, 0x00EF=>0x00CF, 0x01B0=>0x01AF, 0x0065=>0x0045,
0x03BB=>0x039B, 0x03B8=>0x0398, 0x03BC=>0x039C, 0x045C=>0x040C, 0x043F=>0x041F,
0x044C=>0x042C, 0x00FE=>0x00DE, 0x00F0=>0x00D0, 0x1EF3=>0x1EF2, 0x0068=>0x0048,
0x00EB=>0x00CB, 0x0111=>0x0110, 0x0433=>0x0413, 0x012F=>0x012E, 0x00E6=>0x00C6,
0x0078=>0x0058, 0x0161=>0x0160, 0x016F=>0x016E, 0x03B1=>0x0391, 0x0457=>0x0407,
0x0173=>0x0172, 0x00FF=>0x0178, 0x006F=>0x004F, 0x043B=>0x041B, 0x03B5=>0x0395,
0x0445=>0x0425, 0x0121=>0x0120, 0x017E=>0x017D, 0x017C=>0x017B, 0x03B6=>0x0396,
0x03B2=>0x0392, 0x03AD=>0x0388, 0x1E85=>0x1E84, 0x0175=>0x0174, 0x0071=>0x0051,
0x0437=>0x0417, 0x1E0B=>0x1E0A, 0x0148=>0x0147, 0x0105=>0x0104, 0x0458=>0x0408,
0x014D=>0x014C, 0x00ED=>0x00CD, 0x0079=>0x0059, 0x010B=>0x010A, 0x03CE=>0x038F,
0x0072=>0x0052, 0x0430=>0x0410, 0x0455=>0x0405, 0x0452=>0x0402, 0x0127=>0x0126,
0x0137=>0x0136, 0x012B=>0x012A, 0x03AF=>0x038A, 0x044B=>0x042B, 0x006C=>0x004C,
0x03B7=>0x0397, 0x0125=>0x0124, 0x0219=>0x0218, 0x00FB=>0x00DB, 0x011F=>0x011E,
0x043E=>0x041E, 0x1E41=>0x1E40, 0x03BD=>0x039D, 0x0107=>0x0106, 0x03CB=>0x03AB,
0x0446=>0x0426, 0x00FE=>0x00DE, 0x00E7=>0x00C7, 0x03CA=>0x03AA, 0x0441=>0x0421,
0x0432=>0x0412, 0x010F=>0x010E, 0x00F8=>0x00D8, 0x0077=>0x0057, 0x011B=>0x011A,
0x0074=>0x0054, 0x006A=>0x004A, 0x045B=>0x040B, 0x0456=>0x0406, 0x0103=>0x0102,
0x03BB=>0x039B, 0x00F1=>0x00D1, 0x043D=>0x041D, 0x03CC=>0x038C, 0x00E9=>0x00C9,
0x00F0=>0x00D0, 0x0457=>0x0407, 0x0123=>0x0122,
);


$UTF8_UPPER_TO_LOWER = @array_flip($UTF8_LOWER_TO_UPPER);

$UTF8_LOWER_ACCENTS = array(
'a' => 'a', 'o' => 'o', 'd' => 'd', '?' => 'f', 'e' => 'e', 's' => 's', 'o' => 'o',
'?' => 'ss', 'a' => 'a', 'r' => 'r', '?' => 't', 'n' => 'n', 'a' => 'a', 'k' => 'k',
's' => 's', '?' => 'y', 'n' => 'n', 'l' => 'l', 'h' => 'h', '?' => 'p', 'o' => 'o',
'u' => 'u', 'e' => 'e', 'e' => 'e', 'c' => 'c', '?' => 'w', 'c' => 'c', 'o' => 'o',
'?' => 's', 'o' => 'o', 'g' => 'g', 't' => 't', '?' => 's', 'e' => 'e', 'c' => 'c',
's' => 's', 'i' => 'i', 'u' => 'u', 'c' => 'c', 'e' => 'e', 'w' => 'w', '?' => 't',
'u' => 'u', 'c' => 'c', 'o' => 'oe', 'e' => 'e', 'y' => 'y', 'a' => 'a', 'l' => 'l',
'u' => 'u', 'u' => 'u', 's' => 's', 'g' => 'g', 'l' => 'l', '?' => 'f', 'z' => 'z',
'?' => 'w', '?' => 'b', 'a' => 'a', 'i' => 'i', 'i' => 'i', '?' => 'd', 't' => 't',
'r' => 'r', 'a' => 'ae', 'i' => 'i', 'r' => 'r', 'e' => 'e', 'u' => 'ue', 'o' => 'o',
'e' => 'e', 'n' => 'n', 'n' => 'n', 'h' => 'h', 'g' => 'g', 'd' => 'd', 'j' => 'j',
'y' => 'y', 'u' => 'u', 'u' => 'u', 'u' => 'u', 't' => 't', 'y' => 'y', 'o' => 'o',
'a' => 'a', 'l' => 'l', '?' => 'w', 'z' => 'z', 'i' => 'i', 'a' => 'a', 'g' => 'g',
'?' => 'm', 'o' => 'o', 'i' => 'i', 'u' => 'u', 'i' => 'i', 'z' => 'z', 'a' => 'a',
'u' => 'u', '?' => 'th', '?' => 'dh', '?' => 'ae', '?' => 'u',
);

$UTF8_UPPER_ACCENTS = array(
'a' => 'A', 'o' => 'O', 'd' => 'D', '?' => 'F', 'e' => 'E', 's' => 'S', 'o' => 'O',
'?' => 'Ss', 'a' => 'A', 'r' => 'R', '?' => 'T', 'n' => 'N', 'a' => 'A', 'k' => 'K',
's' => 'S', '?' => 'Y', 'n' => 'N', 'l' => 'L', 'h' => 'H', '?' => 'P', 'o' => 'O',
'u' => 'U', 'e' => 'E', 'e' => 'E', 'c' => 'C', '?' => 'W', 'c' => 'C', 'o' => 'O',
'?' => 'S', 'o' => 'O', 'g' => 'G', 't' => 'T', '?' => 'S', 'e' => 'E', 'c' => 'C',
's' => 'S', 'i' => 'I', 'u' => 'U', 'c' => 'C', 'e' => 'E', 'w' => 'W', '?' => 'T',
'u' => 'U', 'c' => 'C', 'o' => 'Oe', 'e' => 'E', 'y' => 'Y', 'a' => 'A', 'l' => 'L',
'u' => 'U', 'u' => 'U', 's' => 'S', 'g' => 'G', 'l' => 'L', '?' => 'F', 'z' => 'Z',
'?' => 'W', '?' => 'B', 'a' => 'A', 'i' => 'I', 'i' => 'I', '?' => 'D', 't' => 'T',
'r' => 'R', 'a' => 'Ae', 'i' => 'I', 'r' => 'R', 'e' => 'E', 'u' => 'Ue', 'o' => 'O',
'e' => 'E', 'n' => 'N', 'n' => 'N', 'h' => 'H', 'g' => 'G', 'd' => 'D', 'j' => 'J',
'y' => 'Y', 'u' => 'U', 'u' => 'U', 'u' => 'U', 't' => 'T', 'y' => 'Y', 'o' => 'O',
'a' => 'A', 'l' => 'L', '?' => 'W', 'z' => 'Z', 'i' => 'I', 'a' => 'A', 'g' => 'G',
'?' => 'M', 'o' => 'O', 'i' => 'I', 'u' => 'U', 'i' => 'I', 'z' => 'Z', 'a' => 'A',
'u' => 'U', '?' => 'Th', '?' => 'Dh', '?' => 'Ae',
);


$UTF8_SPECIAL_CHARS = array(
0x001a, 0x001b, 0x001c, 0x001d, 0x001e, 0x001f, 0x0020, 0x0021, 0x0022, 0x0023,
0x0024, 0x0025, 0x0026, 0x0027, 0x0028, 0x0029, 0x002a, 0x002b, 0x002c, 0x002d,
0x002e, 0x002f, 0x003a, 0x003b, 0x003c, 0x003d, 0x003e, 0x003f, 0x0040, 0x005b,
0x005c, 0x005d, 0x005e, 0x005f, 0x0060, 0x0142, 0x007b, 0x007c, 0x007d, 0x007e,
0x007f, 0x0080, 0x0081, 0x0082, 0x0083, 0x0084, 0x0085, 0x0086, 0x0087, 0x0088,
0x0089, 0x008a, 0x008b, 0x008c, 0x008d, 0x008e, 0x008f, 0x0090, 0x0091, 0x0092,
0x0093, 0x0094, 0x0095, 0x0096, 0x0097, 0x0098, 0x0099, 0x009a, 0x009b, 0x009c,
0x009d, 0x009e, 0x009f, 0x00a0, 0x00a1, 0x00a2, 0x00a3, 0x00a4, 0x00a5, 0x00a6,
0x00a7, 0x00a8, 0x00a9, 0x00aa, 0x00ab, 0x00ac, 0x00ad, 0x00ae, 0x00af, 0x00b0,
0x00b1, 0x00b2, 0x00b3, 0x00b4, 0x00b5, 0x00b6, 0x00b7, 0x00b8, 0x00b9, 0x00ba,
0x00bb, 0x00bc, 0x00bd, 0x00be, 0x00bf, 0x00d7, 0x00f7, 0x02c7, 0x02d8, 0x02d9,
0x02da, 0x02db, 0x02dc, 0x02dd, 0x0300, 0x0301, 0x0303, 0x0309, 0x0323, 0x0384,
0x0385, 0x0387, 0x03b2, 0x03c6, 0x03d1, 0x03d2, 0x03d5, 0x03d6, 0x05b0, 0x05b1,
0x05b2, 0x05b3, 0x05b4, 0x05b5, 0x05b6, 0x05b7, 0x05b8, 0x05b9, 0x05bb, 0x05bc,
0x05bd, 0x05be, 0x05bf, 0x05c0, 0x05c1, 0x05c2, 0x05c3, 0x05f3, 0x05f4, 0x060c,
0x061b, 0x061f, 0x0640, 0x064b, 0x064c, 0x064d, 0x064e, 0x064f, 0x0650, 0x0651,
0x0652, 0x066a, 0x0e3f, 0x200c, 0x200d, 0x200e, 0x200f, 0x2013, 0x2014, 0x2015,
0x2017, 0x2018, 0x2019, 0x201a, 0x201c, 0x201d, 0x201e, 0x2020, 0x2021, 0x2022,
0x2026, 0x2030, 0x2032, 0x2033, 0x2039, 0x203a, 0x2044, 0x20a7, 0x20aa, 0x20ab,
0x20ac, 0x2116, 0x2118, 0x2122, 0x2126, 0x2135, 0x2190, 0x2191, 0x2192, 0x2193,
0x2194, 0x2195, 0x21b5, 0x21d0, 0x21d1, 0x21d2, 0x21d3, 0x21d4, 0x2200, 0x2202,
0x2203, 0x2205, 0x2206, 0x2207, 0x2208, 0x2209, 0x220b, 0x220f, 0x2211, 0x2212,
0x2215, 0x2217, 0x2219, 0x221a, 0x221d, 0x221e, 0x2220, 0x2227, 0x2228, 0x2229,
0x222a, 0x222b, 0x2234, 0x223c, 0x2245, 0x2248, 0x2260, 0x2261, 0x2264, 0x2265,
0x2282, 0x2283, 0x2284, 0x2286, 0x2287, 0x2295, 0x2297, 0x22a5, 0x22c5, 0x2310,
0x2320, 0x2321, 0x2329, 0x232a, 0x2469, 0x2500, 0x2502, 0x250c, 0x2510, 0x2514,
0x2518, 0x251c, 0x2524, 0x252c, 0x2534, 0x253c, 0x2550, 0x2551, 0x2552, 0x2553,
0x2554, 0x2555, 0x2556, 0x2557, 0x2558, 0x2559, 0x255a, 0x255b, 0x255c, 0x255d,
0x255e, 0x255f, 0x2560, 0x2561, 0x2562, 0x2563, 0x2564, 0x2565, 0x2566, 0x2567,
0x2568, 0x2569, 0x256a, 0x256b, 0x256c, 0x2580, 0x2584, 0x2588, 0x258c, 0x2590,
0x2591, 0x2592, 0x2593, 0x25a0, 0x25b2, 0x25bc, 0x25c6, 0x25ca, 0x25cf, 0x25d7,
0x2605, 0x260e, 0x261b, 0x261e, 0x2660, 0x2663, 0x2665, 0x2666, 0x2701, 0x2702,
0x2703, 0x2704, 0x2706, 0x2707, 0x2708, 0x2709, 0x270c, 0x270d, 0x270e, 0x270f,
0x2710, 0x2711, 0x2712, 0x2713, 0x2714, 0x2715, 0x2716, 0x2717, 0x2718, 0x2719,
0x271a, 0x271b, 0x271c, 0x271d, 0x271e, 0x271f, 0x2720, 0x2721, 0x2722, 0x2723,
0x2724, 0x2725, 0x2726, 0x2727, 0x2729, 0x272a, 0x272b, 0x272c, 0x272d, 0x272e,
0x272f, 0x2730, 0x2731, 0x2732, 0x2733, 0x2734, 0x2735, 0x2736, 0x2737, 0x2738,
0x2739, 0x273a, 0x273b, 0x273c, 0x273d, 0x273e, 0x273f, 0x2740, 0x2741, 0x2742,
0x2743, 0x2744, 0x2745, 0x2746, 0x2747, 0x2748, 0x2749, 0x274a, 0x274b, 0x274d,
0x274f, 0x2750, 0x2751, 0x2752, 0x2756, 0x2758, 0x2759, 0x275a, 0x275b, 0x275c,
0x275d, 0x275e, 0x2761, 0x2762, 0x2763, 0x2764, 0x2765, 0x2766, 0x2767, 0x277f,
0x2789, 0x2793, 0x2794, 0x2798, 0x2799, 0x279a, 0x279b, 0x279c, 0x279d, 0x279e,
0x279f, 0x27a0, 0x27a1, 0x27a2, 0x27a3, 0x27a4, 0x27a5, 0x27a6, 0x27a7, 0x27a8,
0x27a9, 0x27aa, 0x27ab, 0x27ac, 0x27ad, 0x27ae, 0x27af, 0x27b1, 0x27b2, 0x27b3,
0x27b4, 0x27b5, 0x27b6, 0x27b7, 0x27b8, 0x27b9, 0x27ba, 0x27bb, 0x27bc, 0x27bd,
0x27be, 0xf6d9, 0xf6da, 0xf6db, 0xf8d7, 0xf8d8, 0xf8d9, 0xf8da, 0xf8db, 0xf8dc,
0xf8dd, 0xf8de, 0xf8df, 0xf8e0, 0xf8e1, 0xf8e2, 0xf8e3, 0xf8e4, 0xf8e5, 0xf8e6,
0xf8e7, 0xf8e8, 0xf8e9, 0xf8ea, 0xf8eb, 0xf8ec, 0xf8ed, 0xf8ee, 0xf8ef, 0xf8f0,
0xf8f1, 0xf8f2, 0xf8f3, 0xf8f4, 0xf8f5, 0xf8f6, 0xf8f7, 0xf8f8, 0xf8f9, 0xf8fa,
0xf8fb, 0xf8fc, 0xf8fd, 0xf8fe, 0xfe7c, 0xfe7d,
);

function os_get_tax_rate($class_id, $country_id = -1, $zone_id = -1)
{

    if ( ($country_id == -1) && ($zone_id == -1) ) {
        if (!isset($_SESSION['customer_id'])) {
            $country_id = STORE_COUNTRY;
            $zone_id = STORE_ZONE;
        } else {
            $country_id = $_SESSION['customer_country_id'];
            $zone_id = $_SESSION['customer_zone_id'];
        }
    }else{
        $country_id = $country_id;
        $zone_id = $zone_id;
    }

    $tax_query = osDBquery("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . $country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . $zone_id . "') and tr.tax_class_id = '" . $class_id . "' group by tr.tax_priority");
    if (os_db_num_rows($tax_query,true)) {
        $tax_multiplier = 1.0;
        while ($tax = os_db_fetch_array($tax_query,true)) {
            $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
        }
        return ($tax_multiplier - 1.0) * 100;
    } else {
        return 0;
    }
}

function os_get_tax_description($class_id, $country_id= -1, $zone_id= -1)
{

    if ( ($country_id == -1) && ($zone_id == -1) ) {
        if (!isset($_SESSION['customer_id'])) {
            $country_id = STORE_COUNTRY;
            $zone_id = STORE_ZONE;
        } else {
            $country_id = $_SESSION['customer_country_id'];
            $zone_id = $_SESSION['customer_zone_id'];
        }
    }else{
        $country_id = $country_id;
        $zone_id = $zone_id;
    }

    $tax_query = osDBquery("select tax_description from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . $country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . $zone_id . "') and tr.tax_class_id = '" . $class_id . "' order by tr.tax_priority");
    if (os_db_num_rows($tax_query,true)) {
        $tax_description = '';
        while ($tax = os_db_fetch_array($tax_query,true)) {
            $tax_description .= $tax['tax_description'] . ' + ';
        }
        $tax_description = substr($tax_description, 0, -3);

        return $tax_description;
    } else {
        return TEXT_UNKNOWN_TAX_RATE;
    }
}

// todo: на удаление unserialize_session_data
function unserialize_session_data( $session_data )
{
    $variables = array();
    $a = preg_split( "/(\w+)\|/", $session_data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
    for( $i = 0; $i < count( $a ); $i = $i+2 ) {
        $variables[$a[$i]] = unserialize( $a[$i+1] );
    }
    return( $variables );
}

function os_get_products($session)
{
    if (!is_array($session)) return false;
    if (empty($session)) return false;
    if (empty($session['cart']->contents)) return false;

    $products_array = array();
    reset($session);
    while (list($products_id, ) = each($session['cart']->contents)) {
        $products_query = os_db_query("select p.products_id, pd.products_name,p.products_image, p.products_model, p.products_price, p.products_discount_allowed, p.products_weight, p.products_tax_class_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id='" . os_get_prid($products_id) . "' and pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
        if ($products = os_db_fetch_array($products_query)) {
            $prid = $products['products_id'];

            // dirty workaround
            $osPrice = new osPrice($session['currency'],$session['customers_status']['customers_status_id']);
            $products_price = $osPrice->GetPrice($products['products_id'], false, $session['cart']->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price'], 0, 0, $products['products_discount_allowed']);

            $products_array[] = array('id' => $products_id,
            'name' => $products['products_name'],
            'model' => $products['products_model'],
            'image' => $products['products_image'],
            'price' => $products_price['price']+attributes_price($products_id,$session),
            'quantity' => $session['cart']->contents[$products_id]['qty'],
            'weight' => $products['products_weight'],
            'final_price' => ($products_price['price']+attributes_price($products_id,$session)),
            'tax_class_id' => $products['products_tax_class_id'],
            'attributes' => $session['contents'][$products_id]['attributes']);
        }
    }

    return $products_array;
}

function attributes_price($products_id,$session) {
    $osPrice = new osPrice($session['currency'],$session['customers_status']['customers_status_id']);
    if (isset($session['contents'][$products_id]['attributes'])) {
        reset($session['contents'][$products_id]['attributes']);
        while (list($option, $value) = each($session['contents'][$products_id]['attributes'])) {
            $attribute_price_query = os_db_query("select pd.products_tax_class_id, p.options_values_price, p.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . $products_id . "' and p.options_id = '" . $option . "' and pd.products_id = p.products_id and p.options_values_id = '" . $value . "'");
            $attribute_price = os_db_fetch_array($attribute_price_query);
            if ($attribute_price['price_prefix'] == '+') {
                $attributes_price += $osPrice->Format($attribute_price['options_values_price'],false,$attribute_price['products_tax_class_id']);
            } else {
                $attributes_price -= $osPrice->Format($attribute_price['options_values_price'],false,$attribute_price['products_tax_class_id']);
            }
        }
    }
    return $attributes_price;
}

function os_get_product_path($products_id) {
    $cPath = '';

    $category_query = "select p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = '" . (int)$products_id . "' and p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id != 0 limit 1";
    $category_query  = osDBquery($category_query);
    if (os_db_num_rows($category_query,true)) {
        $category = os_db_fetch_array($category_query);

        $categories = array();
        os_get_parent_categories($categories, $category['categories_id']);

        $categories = array_reverse($categories);

        $cPath = implode('_', $categories);

        if (os_not_null($cPath)) $cPath .= '_';
        $cPath .= $category['categories_id'];
    }

    return $cPath;
}

function os_get_download($content_id) {

    $content_query=os_db_query("SELECT
    content_file,
    content_read
    FROM ".TABLE_PRODUCTS_CONTENT."
    WHERE content_id='".$content_id."'");

    $content_data=os_db_fetch_array($content_query);
    // update file counter

    os_db_query("UPDATE
    ".TABLE_PRODUCTS_CONTENT."
    SET content_read='".($content_data['content_read']+1)."'
    WHERE content_id='".$content_id."'");

    // original filename
    $filename = DIR_FS_CATALOG.'media/products/'.$content_data['content_file'];
    $backup_filename = DIR_FS_CATALOG.'media/products/backup/'.$content_data['content_file'];
    // create md5 hash id from original file
    $orign_hash_id=md5_file($filename);

    clearstatcache();

    // create new filename with timestamp
    $timestamp=str_replace('.','',microtime());
    $timestamp=str_replace(' ','',$timestamp);
    $new_filename=DIR_FS_CATALOG.'media/products/'.$timestamp.strstr($content_data['content_file'],'.');

    // rename file
    rename($filename,$new_filename);


    if (file_exists($new_filename)) {


        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=".$new_filename);
        @readfile($new_filename);
        // rename file to original name
        rename($new_filename,$filename);
        $new_hash_id=md5_file($filename);
        clearstatcache();

        // check hash id of file again, if not same, get backup!
        if ($new_hash_id!=$orign_hash_id) copy($backup_filename,$filename);
    }


}

function os_get_country_list($name, $selected = '', $parameters = '')
{
    $countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
    //    Probleme mit register_globals=off -> erstmal nur auskommentiert. Kann u.U. gelцscht werden.
    $countries = os_get_countriesList();

    for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
        $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
    }
    if (is_array($name)) return os_draw_pull_down_menuNote($name, $countries_array, $selected, $parameters);
    return os_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
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

function os_get_cookie_info () {

    if (defined('HTTP_COOKIE_DOMAIN')){
        $cookie_domain = HTTP_COOKIE_DOMAIN;
    } else {
        //use alternative way to determine domain
        $request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
        $current_domain = (($request_type == 'NONSSL') ? HTTP_SERVER : HTTPS_SERVER);
        if (strpos($current_domain, '://')) {
            $parsed_url = @parse_url($current_domain);
            $current_domain = $parsed_url['host'];
        } else {
            //try to parse not fully configured domain string
            $parsed_url = @parse_url($current_domain);
            if ($parsed_url){
                $current_domain = $parsed_url['host'];
            }
        }
        $domain_array = explode('.', $current_domain);
        if (sizeof($domain_array) > 1){
            $cookie_domain = $current_domain;
        } else {
            $cookie_domain = '';
        }
    }

    if (!os_not_null($cookie_domain)){
        $cookie_domain = '';
    }


    if (defined('HTTP_COOKIE_PATH')){
        $cookie_path = HTTP_COOKIE_PATH;
    } else {
        //use default cookie path
        $cookie_path = '/';
    }


    $cookie_info = array('cookie_domain' => $cookie_domain,
    'cookie_path' => $cookie_path);

    return $cookie_info;
}

// todo: на удаление os_get_categories
function os_get_categories($categories_array = '', $parent_id = '0', $indent = '')
{

    $parent_id = os_db_prepare_input($parent_id);

    if (!is_array($categories_array)) $categories_array = array();

    $flip_category_cache = flip_category_cache ();
    global $category_cache;

    if (isset($flip_category_cache[os_db_input($parent_id)]))
    {
        foreach ($flip_category_cache[os_db_input($parent_id)] as $categories_id)
        {
            //echo $categories_id.'<br>';

            $categories = get_categories_info($categories_id);

            if (!empty($categories['categories_id']))
            {

                $categories_array[] = array('id' => $categories['categories_id'],
                'text' => $indent . $categories['categories_name']);
            }

            if ($categories['categories_id'] != $parent_id)
            {
                $categories_array = os_get_categories($categories_array, $categories_id, $indent . '&nbsp;&nbsp;');
            }

        }
    }

    return $categories_array;
}

function os_get_attributes_model($product_id, $attribute_name,$options_name,$language='')
{
    if ($language=='') $language=$_SESSION['languages_id'];
    $options_value_id_query=os_db_query("SELECT
    pa.attributes_model
    FROM
    ".TABLE_PRODUCTS_ATTRIBUTES." pa
    Inner Join ".TABLE_PRODUCTS_OPTIONS." po ON po.products_options_id = pa.options_id
    Inner Join ".TABLE_PRODUCTS_OPTIONS_VALUES." pov ON pa.options_values_id = pov.products_options_values_id
    WHERE
    po.language_id = '".$language."' AND
    po.products_options_name = '".$options_name."' AND
    pov.language_id = '".$language."' AND
    pa.products_id = '".$product_id."' AND
    pov.products_options_values_name = '".$attribute_name."'");


    $options_attr_data = os_db_fetch_array($options_value_id_query);
    return $options_attr_data['attributes_model'];

}

function os_format_price ($price_string,$price_special,$calculate_currencies,$show_currencies=1)
{
    // calculate currencies

    $currencies_query = os_db_query("SELECT symbol_left,
    symbol_right,
    decimal_places,
    value
    FROM ". TABLE_CURRENCIES ." WHERE
    code = '".$_SESSION['currency'] ."'");
    $currencies_value=os_db_fetch_array($currencies_query);
    $currencies_data=array();
    $currencies_data=array(
    'SYMBOL_LEFT'=>$currencies_value['symbol_left'] ,
    'SYMBOL_RIGHT'=>$currencies_value['symbol_right'] ,
    'DECIMAL_PLACES'=>$currencies_value['decimal_places'] ,
    'VALUE'=> $currencies_value['value']);
    if ($calculate_currencies=='true') {
        $price_string=$price_string * $currencies_data['VALUE'];
    }
    // round price
    $price_string=os_precision($price_string,$currencies_data['DECIMAL_PLACES']);


    if ($price_special=='1') {
        $currencies_query = os_db_query("SELECT symbol_left,
        decimal_point,
        thousands_point,
        value
        FROM ". TABLE_CURRENCIES ." WHERE
        code = '".$_SESSION['currency'] ."'");
        $currencies_value=os_db_fetch_array($currencies_query);
        $price_string=number_format($price_string,$currencies_data['DECIMAL_PLACES'], $currencies_value['decimal_point'], $currencies_value['thousands_point']);
        if ($show_currencies == 1) {
            $price_string = $currencies_data['SYMBOL_LEFT']. ' '.$price_string.' '.$currencies_data['SYMBOL_RIGHT'];
        }
    }
    return $price_string;
}

function os_draw_selection_fieldNote($data, $type, $value = '', $checked = false, $parameters = '') {
    $selection = $data['suffix'].'<input type="' . os_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . os_parse_input_field_data($data['name'], array('"' => '&quot;')) . '"';

    if (os_not_null($value)) $selection .= ' value="' . os_parse_input_field_data($value, array('"' => '&quot;')) . '"';

    if ( ($checked == true) || ($GLOBALS[$data['name']] == 'on') || ( (isset($value)) && ($GLOBALS[$data['name']] == $value) ) ) {
        $selection .= ' checked="checked"';
    }

    if (os_not_null($parameters)) $selection .= ' ' . $parameters;

    $selection .= ' />'.$data['text'];

    return $selection;
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

function os_draw_input_fieldNote($data, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . os_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . os_parse_input_field_data($data['name'], array('"' => '&quot;')) . '"';

    if ( (isset($GLOBALS[$data['name']])) && ($reinsert_value == true) ) {
        $field .= ' value="' . os_parse_input_field_data($GLOBALS[$data['name']], array('"' => '&quot;')) . '"';
    } elseif (os_not_null($value)) {
        $field .= ' value="' . os_parse_input_field_data($value, array('"' => '&quot;')) . '"';
    }

    if (os_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= ' />'.$data['text'];

    return $field;
}

function os_db_queryCached($query, $link = 'db_link')
{
    global $$link;
    global $db;
    global $db_query;
    global $query_counts;
    global $cartet;

    // get HASH ID for filename
    $id = md5($query);

    // cache File Name
    $file = 'database/'.$id;
    $fileCheck = DIR.'cache/'.$file.'.dat';

    if (STORE_DB_TRANSACTIONS == 'true')
    {
        error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    // get cached resulst
    if (file_exists($fileCheck) && ($result = $cartet->cache->get($file)))
    {
        return $result;
    }
    else
    {
        if (file_exists($fileCheck))
            @unlink($fileCheck);

        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $tstart = $mtime;

        // get result from DB and create new file
        $result = mysql_query($query, $$link) or os_db_error($query, mysql_errno(), mysql_error());
        $query_counts++;

        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $tend = $mtime;
        $tpassed = ($tend - $tstart);

        $tpassed = number_format($tpassed, 5);

        if (@$db->DISPLAY_DB_QUERY == 'true')
        {
            $db_text = $query;
            $db_text = str_replace("\r\n", "", $db_text);
            $db_text = str_replace("\n","",$db_text);
            $db_text = strtolower($db_text);
            $db_text = trim($db_text);
            $db_text = preg_replace("|[\s]+|s", " ", $db_text);

            if (isset($db_query[$db_text]))
            {
                $db_query[ $db_text ]['num']++;
                $db_query[ $db_text ]['time'] = $tpassed;
            }
            else
            {
                $db_query[ $db_text ]['num'] = 1;
                $db_query[ $db_text ]['time'] = $tpassed;
            }
        }

        if (STORE_DB_TRANSACTIONS == 'true')
        {
            $result_error = mysql_error();
            error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
        }

        // fetch data into array
        while ($record = os_db_fetch_array($result))
            $records[]=$record;

        $cartet->cache->set('database.'.$id, $records, DB_CACHE_EXPIRE);

        return $records;
    }
}

function os_db_query($query, $link = 'db_link')
{
    global $db_query;
    global $$link;
    global $query_counts;
    global $db;

    $query_counts++;

    if ( is_object($db) && $db->STORE_DB_TRANSACTIONS == 'true')
    {
        error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tstart = $mtime;

    $result = mysql_query($query, $$link) or os_db_error($query, mysql_errno(), mysql_error());

    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tend = $mtime;
    $tpassed = ($tend - $tstart);

    $tpassed = number_format($tpassed, 5);

    if ( is_object($db) && $db->STORE_DB_TRANSACTIONS == 'true')
    {
        $result_error = mysql_error();
        error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    if (!$result)
    {
        os_db_error($query, mysql_errno(), mysql_error());
    }

    if (@$db->DISPLAY_DB_QUERY == 'true')
    {
        $db_text = $query;
        $db_text = str_replace("\r\n", "", $db_text);
        $db_text = str_replace("\n","",$db_text);
        $db_text = strtolower($db_text);
        $db_text = trim($db_text);
        $db_text = preg_replace("|[\s]+|s", " ", $db_text);

        if (isset($db_query[$db_text]))
        {
            $db_query[ $db_text ]['num']++;
            $db_query[ $db_text ]['time'] = $tpassed;
        }
        else
        {
            $db_query[ $db_text ]['num'] = 1;
            $db_query[ $db_text ]['time'] = $tpassed;
        }
    }

    return $result;
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

function os_db_error($query, $errno, $error)
{
    global $db;
    @$db->error($query, $errno, $error);
}

function os_customer_greeting()
{
    if (isset($_SESSION['customer_last_name']) && isset($_SESSION['customer_id']))
    {
        if (!isset($_SESSION['customer_gender']))
        {
            $check_customer_query = "select customers_gender FROM  " . TABLE_CUSTOMERS . " where customers_id = '" . $_SESSION['customer_id'] . "'";
            $check_customer_query = osDBquery($check_customer_query);
            $check_customer_data  = os_db_fetch_array($check_customer_query,true);
            $_SESSION['customer_gender'] = $check_customer_data['customers_gender'];
        }
        if($_SESSION['customer_gender']=='f')
        {
            $greeting_string = sprintf(TEXT_GREETING_PERSONAL, FEMALE . '&nbsp;'. $_SESSION['customer_first_name'] . '&nbsp;'. $_SESSION['customer_last_name'], os_href_link(FILENAME_PRODUCTS_NEW));
        }
        else
        {
            $greeting_string = sprintf(TEXT_GREETING_PERSONAL, MALE . '&nbsp;'. $_SESSION['customer_first_name'] . '&nbsp;' . $_SESSION['customer_last_name'], os_href_link(FILENAME_PRODUCTS_NEW));
        }

    } else {
        $greeting_string = sprintf(TEXT_GREETING_GUEST, os_href_link(FILENAME_LOGIN, '', 'SSL'), os_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
    }

    return $greeting_string;
}

function os_create_random_value($length, $type = 'mixed')
{
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;
    $rand_value = '';
    while (strlen($rand_value) < $length)
    {
        if ($type == 'digits')
        {
            $char = os_rand(0,9);
        }
        else
        {
            $char = chr(os_rand(0,255));
        }
        if ($type == 'mixed')
        {
            if (preg_match('/^[a-z0-9]$/i', $char)) $rand_value .= $char;
        }
        elseif ($type == 'chars')
        {
            if (preg_match('/^[a-z]$/i', $char)) $rand_value .= $char;
        }
        elseif ($type == 'digits')
        {
            if (preg_match('/^[0-9]$/', $char)) $rand_value .= $char;
        }
    }
    return $rand_value;
}

function os_RandomString($length)
{
    $chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n','N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v','V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');

    $max_chars = count($chars) - 1;
    srand( (double) microtime()*1000000);
    $rand_str = '';
    for($i=0;$i<$length;$i++)
    {
        $rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
    }
    return $rand_str;
}

function os_create_password($length)
{
    $pass = os_RandomString($length);
    return md5($pass);
}

// todo: На удаление os_count_products_in_category
function os_count_products_in_category($category_id, $include_inactive = false)
{
    $products_count = 0;

    global $_count_category;
    global $category_cache;

    $_flip_category_cache = flip_category_cache();

    if (empty($_count_category))
    {
        $products_query = 'SELECT c.categories_id, c.categories_count AS total FROM '.TABLE_CATEGORIES.' c;';
        $products_query = osDBquery($products_query);

        while ($_products = os_db_fetch_array($products_query,true))
        {
            $_count_category[ $_products['categories_id'] ] = $_products['total'];
        }

        if (empty($_count_category)) $_count_category = 1;
    }

    //   @$products_count += $_count_category[$category_id];

    if ( isset( $_count_category[ $category_id ] ) )
    {
        return $_count_category[ $category_id ] ;
    }
    else
    {
        return 0;
    }
}

function os_count_customer_address_book_entries($id = '', $check_session = true)
{
    if (is_numeric($id) == false)
    {
        if (isset($_SESSION['customer_id']))
        {
            $id = $_SESSION['customer_id'];
        }
        else
        {
            return 0;
        }
    }

    if ($check_session == true)
    {
        if ( (isset($_SESSION['customer_id']) == false) || ($id != $_SESSION['customer_id']) )
        {
            return 0;
        }
    }

    $addresses_query = os_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$id . "'");
    $addresses = os_db_fetch_array($addresses_query);

    return $addresses['total'];
}

function os_count_cart()
{
    $id_list = $_SESSION['cart']->get_product_id_list();
    $id_list = explode(', ', $id_list);

    $actual_content = array ();

    for ($i = 0, $n = sizeof($id_list); $i < $n; $i ++)
    {
        $actual_content[] = array ('id' => $id_list[$i], 'qty' => $_SESSION['cart']->get_quantity($id_list[$i]));
    }

    // merge product IDs
    $content = array ();
    for ($i = 0, $n = sizeof($actual_content); $i < $n; $i ++)
    {
        //$act_id=$actual_content[$i]['id'];
        if (strpos($actual_content[$i]['id'], '{'))
        {
            $act_id = substr($actual_content[$i]['id'], 0, strpos($actual_content[$i]['id'], '{'));
        }
        else
        {
            $act_id = $actual_content[$i]['id'];
        }

        if (isset($_SESSION['actual_content'][$act_id]['qty'])) $_actual_content_1  = $_SESSION['actual_content'][$act_id]['qty']; else $_actual_content_1 = 0;

        if (isset($actual_content[$i]['qty'])) $_actual_content_2 = $actual_content[$i]['qty']; else $_actual_content_2 = 0;

        $_SESSION['actual_content'][$act_id] = array ('qty' => $_actual_content_1 + $_actual_content_2);
    }

}

function os_collect_posts()
{
    global $coupon_no, $REMOTE_ADDR,$osPrice,$cc_id;
    if (!$REMOTE_ADDR) $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
    if ($_POST['gv_redeem_code'])
    {
        $gv_query = os_db_query("select coupon_id, coupon_amount, coupon_type, coupon_minimum_order,uses_per_coupon, uses_per_user, restrict_to_products,restrict_to_categories from " . TABLE_COUPONS . " where coupon_code='".$_POST['gv_redeem_code']."' and coupon_active='Y'");
        $gv_result = os_db_fetch_array($gv_query);

        if (os_db_num_rows($gv_query) != 0) {
            $redeem_query = os_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $gv_result['coupon_id'] . "'");
            if ( (os_db_num_rows($redeem_query) != 0) && ($gv_result['coupon_type'] == 'G') )
            {
                os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_INVALID_REDEEM_GV), 'SSL'));
            }
        }
        else
        {
            os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_INVALID_REDEEM_GV), 'SSL'));
        }


        if ($gv_result['coupon_type'] == 'G')
        {
            $gv_amount = $gv_result['coupon_amount'];
            $gv_amount_query=os_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . $_SESSION['customer_id'] . "'");
            $customer_gv = false;
            $total_gv_amount = $gv_amount;
            if ($gv_amount_result = os_db_fetch_array($gv_amount_query))
            {
                $total_gv_amount = $gv_amount_result['amount'] + $gv_amount;
                $customer_gv = true;
            }
            $gv_update = os_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N' where coupon_id = '" . $gv_result['coupon_id'] . "'");
            $gv_redeem = os_db_query("insert into  " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, customer_id, redeem_date, redeem_ip) values ('" . $gv_result['coupon_id'] . "', '" . $_SESSION['customer_id'] . "', now(),'" . $REMOTE_ADDR . "')");
            if ($customer_gv)
            {
                // already has gv_amount so update
                $gv_update = os_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount = '" . $total_gv_amount . "' where customer_id = '" . $_SESSION['customer_id'] . "'");
            }
            else
            {
                // no gv_amount so insert
                $gv_insert = os_db_query("insert into " . TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) values ('" . $_SESSION['customer_id'] . "', '" . $total_gv_amount . "')");
            }
            os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(REDEEMED_AMOUNT. $osPrice->Format($gv_amount,true,0,true)), 'SSL'));
        }
        else
        {
            if (os_db_num_rows($gv_query)==0)
            {
                os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_INVALID_REDEEM_COUPON), 'SSL'));
            }

            $date_query=os_db_query("select coupon_start_date from " . TABLE_COUPONS . " where coupon_start_date <= now() and coupon_code='".$_POST['gv_redeem_code']."'");

            if (os_db_num_rows($date_query)==0)
            {
                os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_STARTDATE_COUPON), 'SSL'));
            }

            $date_query=os_db_query("select coupon_expire_date from " . TABLE_COUPONS . " where coupon_expire_date >= now() and coupon_code='".$_POST['gv_redeem_code']."'");

            if (os_db_num_rows($date_query)==0)
            {
                os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_FINISDATE_COUPON), 'SSL'));
            }

            $coupon_count = os_db_query("select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $gv_result['coupon_id']."'");
            $coupon_count_customer = os_db_query("select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $gv_result['coupon_id']."' and customer_id = '" . $_SESSION['customer_id'] . "'");

            if (os_db_num_rows($coupon_count)>=$gv_result['uses_per_coupon'] && $gv_result['uses_per_coupon'] > 0)
            {
                os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_USES_COUPON . $gv_result['uses_per_coupon'] . TIMES ), 'SSL'));
            }

            if (os_db_num_rows($coupon_count_customer)>=$gv_result['uses_per_user'] && $gv_result['uses_per_user'] > 0)
            {
                os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_INVALID_USES_USER_COUPON . $gv_result['uses_per_user'] . TIMES ), 'SSL'));
            }
            if ($gv_result['coupon_type']=='S')
            {
                $coupon_amount = $order->info['shipping_cost'];
            }
            else
            {
                $coupon_amount = $gv_result['coupon_amount'] . ' ';
            }
            if ($gv_result['coupon_type']=='P') $coupon_amount = $gv_result['coupon_amount'] . '% ';
            if ($gv_result['coupon_minimum_order']>0) $coupon_amount .= 'on orders greater than ' . $gv_result['coupon_minimum_order'];
            if (!os_session_is_registered('cc_id')) os_session_register('cc_id'); //Fred - this was commented out before
            $_SESSION['cc_id'] = $gv_result['coupon_id']; //Fred ADDED, set the global and session variable
            os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(REDEEMED_COUPON), 'SSL'));
        }

    }
    if ($_POST['submit_redeem_x'] && $gv_result['coupon_type'] == 'G') os_redirect(os_href_link(FILENAME_SHOPPING_CART, 'info_message=' . urlencode(ERROR_NO_REDEEM_CODE), 'SSL'));
}

function os_cleanName($name)
{
    //     $replace_param='/[^a-zA-Z0-9]/';
    $replace_param='/[^a-zA-Zа-яА-Я0-9]/';
    $cyrillic = array("ж", "ё", "й","ю", "ь","ч", "щ", "ц","у","к","е","н","г","ш", "з","х","ъ","ф","ы","в","а","п","р","о","л","д","э","я","с","м","и","т","б","Ё","Й","Ю","Ч","Ь","Щ","Ц","У","К","Е","Н","Г","Ш","З","Х","Ъ","Ф","Ы","В","А","П","Р","О","Л","Д","Ж","Э","Я","С","М","И","Т","Б");
    $translit = array("zh","yo","i","yu","","ch","sh","c","u","k","e","n","g","sh","z","h","",  "f",  "y",  "v",  "a",  "p",  "r",  "o",  "l",  "d",  "ye", "ya", "s",  "m",  "i",  "t",  "b",  "yo", "I",  "YU", "CH", "",  "SH", "C",  "U",  "K",  "E",  "N",  "G",  "SH", "Z",  "H",  "",  "F",  "Y",  "V",  "A",  "P",  "R",  "O",  "L",  "D",  "Zh", "Ye", "Ya", "S",  "M",  "I",  "T",  "B");
    $name = str_replace($cyrillic, $translit, $name);
    $name = preg_replace($replace_param,'-',$name);
    return $name;
}

function os_check_stock_attributes($attribute_id, $products_quantity)
{
    $stock_query=os_db_query("SELECT
    attributes_stock
    FROM ".TABLE_PRODUCTS_ATTRIBUTES."
    WHERE products_attributes_id='".$attribute_id."'");
    $stock_data=os_db_fetch_array($stock_query);
    $stock_left = $stock_data['attributes_stock'] - $products_quantity;
    $out_of_stock = '';

    if ($stock_left < 0)
    {
        $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    return $out_of_stock;
}


function os_check_categories_status($categories_id)
{
    if (!$categories_id) return 0;

    $categorie_query = "SELECT
    parent_id,
    categories_status
    FROM ".TABLE_CATEGORIES."
    WHERE
    categories_id = '".(int) $categories_id."'";

    $categorie_query = osDBquery($categorie_query);

    $categorie_data = os_db_fetch_array($categorie_query, true);
    if ($categorie_data['categories_status'] == 0)
    {
        return 1;
    }
    else
    {
        if ($categorie_data['parent_id'] != 0)
        {
            if (os_check_categories_status($categorie_data['parent_id']) >= 1)
                return 1;
        }
        return 0;
    }

}

function os_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n")
{
    $address_query = os_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customers_id . "' and address_book_id = '" . $address_id . "'");
    $address = os_db_fetch_array($address_query);
    $format_id = os_get_address_format_id($address['country_id']);
    return os_address_format($format_id, $address, $html, $boln, $eoln);
}

function String_RusCharsDeCode($string)
{
	//russian text escape code, for unescape: (bug : %u0410 -%u044f)
	$russian_codes = array('%u0410','%u0411','%u0412','%u0413','%u0414','%u0415','%u0401','%u0416','%u0417','%u0418','%u0419','%u041A','%u041B','%u041C','%u041D','%u041E','%u041F','%u0420','%u0421','%u0422','%u0423','%u0424','%u0425','%u0426','%u0427','%u0428','%u0429','%u042A','%u042B','%u042C','%u042D','%u042E','%u042F','%u0430','%u0431','%u0432','%u0433','%u0434','%u0435','%u0451','%u0436','%u0437','%u0438','%u0439','%u043A','%u043B','%u043C','%u043D','%u043E','%u043F','%u0440','%u0441','%u0442','%u0443','%u0444','%u0445','%u0446','%u0447','%u0448','%u0449','%u044A','%u044B','%u044C','%u044D','%u044E','%u044F');
	$russian_chars = array("А",     "Б",     "В",     "Г",     "Д",     "Е",     "Ё",     "Ж",     "З",     "И",     "Й",     "К",     "Л",     "М",     "Н",     "О",     "П",     "Р",     "С",     "Т",     "У",     "Ф",     "Х",     "Ц",     "Ч",     "Ш",     "Щ",     "Ъ",     "Ы",     "Ь",     "Э",     "Ю",     "Я",     "а",     "б",     "в",     "г",     "д",     "е",     "ё",     "ж",     "з",     "и",     "й",     "к",     "л",     "м",     "н",     "о",     "п",     "р",     "с",     "т",     "у",     "ф",     "х",     "ц",     "ч",     "ш",     "щ",     "ъ",     "ы",     "ь",     "э",     "ю",     "я");
	return str_replace($russian_codes,$russian_chars,$string);
}

function osc_head_array($HEAD)
{
    //?????? ??????? ?????????
    $_meta_array = array();

    foreach ($HEAD as $_head_value)
    {
        $key = @ key($_head_value);
        $_meta = $_head_value[$key];
        // print_r( $_meta);
        // echo $key.'<br>';
        switch( $key )
        {
            case 'base':
                if (isset($_meta['href']) && !empty($_meta['href']))
                {
                    $_meta_array[] = '<base href="'.$_meta['href'].'" />';
                }
                break;

            case 'title':
                $_meta_array[] = '<title>'.apply_filter('title', strip_tags($_meta)).'</title>';
                break;

            case 'meta':

                if (isset($_meta['name']) && isset($_meta['content']))
                {
                    if ($_meta['name'] == 'description') $_meta['content'] = apply_filter('description', $_meta['content']);
                    if ($_meta['name'] == 'keywords') $_meta['content'] = apply_filter('keywords', $_meta['content']);

                    $_meta_array[] = '<meta name="'.$_meta['name'].'" content="'.$_meta['content'].'" />';

                }elseif (isset($_meta['http-equiv']) && isset($_meta['content']) )
                {
                    $_meta_array[] = '<meta http-equiv="'.$_meta['http-equiv'].'" content="'.$_meta['content'].'" />';

                }

                break;

            case 'js':

                if (isset($_meta['src']))
                {

                    $_meta_array[] = '<script type="text/javascript" src="'.$_meta['src'].'"></script>';

                }elseif (isset($_meta['action']))
                {
                    if ( function_exists( $_meta['action'] ) )
                    {
                        $_val = $_meta['action']();
                        if (!empty($_val))
                        {
                            $__val = '<script type="text/javascript"><!--';
                            $__val .= $_val;
                            $__val .= '//--></script>';

                            $_meta_array[] = $__val;
                        }
                    }
                }elseif ( isset($_meta['code']) )
                {
                    $__meta = '<script type="text/javascript"><!--'."\n";
                    $__meta .= $_meta['code'];
                    $__meta .= "\n".'//--></script>';
                    $_meta_array[] = $__meta;
                }

                break;

            case 'link':

                if (isset($_meta['rel']) && !empty($_meta['rel']))
                {
                    switch ($_meta['rel'])
                    {
                        case 'icon':
                            if (isset($_meta['href']) && isset($_meta['type']) )
                            {
                                $_meta_array[] = '<link rel="icon" href="'.$_meta['href'].'" type="'.$_meta['type'].'" />';
                            }
                            break;

                        case 'shortcut icon':
                            if (isset($_meta['href']) && isset($_meta['type']) )
                            {
                                $_meta_array[] = '<link rel="shortcut icon" href="'.$_meta['href'].'" type="'.$_meta['type'].'" />';
                            }
                            break;

                        case 'stylesheet':
                            if (isset($_meta['href']) && isset($_meta['type']) )
                            {
                                $_meta_array[] = '<link rel="stylesheet" href="'.$_meta['href'].'" type="'.$_meta['type'].'"'.(isset($_meta['media'])?' '.$_meta['media']:'').' />';
                            }
                            break;

                        case 'alternate':
                            if (isset($_meta['href']) && isset($_meta['type']) )
                            {
                                $_meta_array[] = '<link rel="alternate" href="'.$_meta['href'].'" type="'.$_meta['type'].'" '.(isset($_meta['title'])?  'title="'.$_meta['title'].'"':'').' />';
                            }
                            break;
                    }
                }
                break;

            case 'other':
                if ( isset( $_meta['code'] ) )
                {
                    $_meta_array[] = $_meta['code'];
                }
                break;

        }
    }

    return $_meta_array;
}

require 'helpers/html.helper.php';
require 'helpers/array.helper.php';
require 'helpers/files.helper.php';