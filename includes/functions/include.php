<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

    function os_button($alt = '', $parameters = '') 
    {
        return '<div class="header" id="header_02"><div class="ct">'.$alt.'</div></div>';;
    }

    function create_coupon_code($salt="secret", $length = SECURITY_CODE_LENGTH) 
    {
        $ccid = md5(uniqid("","salt"));
        $ccid .= md5(uniqid("","salt"));
        $ccid .= md5(uniqid("","salt"));
        $ccid .= md5(uniqid("","salt"));
        srand((double)microtime()*1000000); 
        $random_start = @rand(0, (128-$length));
        $good_result = 0;
        while ($good_result == 0) {
            $id1=substr($ccid, $random_start,$length);
            $query = os_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . $id1 . "'");
            if (os_db_num_rows($query) == 0) $good_result = 1;
        }
        return $id1;
    }


    function os_address_format($address_format_id, $address, $html, $boln, $eoln) 
    {
        global $address_format_cache;

        if (empty($address_format_cache))
        {
            $address_format_query = os_db_query("select address_format_id, address_format as format from " . TABLE_ADDRESS_FORMAT );

            while ($address_format = os_db_fetch_array($address_format_query))
            {
                $address_format_cache[$address_format['address_format_id']] = $address_format['format'];
            }

            $address_format['format'] = $address_format_cache[$address_format_id];
        }
        else
        {
            if (isset($address_format_cache[$address_format_id]))
            {
                $address_format['format'] = $address_format_cache[$address_format_id];
            }
            else
            {
                $address_format['format'] = '';
            }
        }

        $company = addslashes($address['company']);
        $firstname = addslashes($address['firstname']);
        $lastname = addslashes($address['lastname']);
        $street = addslashes($address['street_address']);
        $suburb = addslashes($address['suburb']);
        $city = addslashes($address['city']);
        $state = addslashes($address['state']);
        $country_id = $address['country_id'];
        $zone_id = $address['zone_id'];
        $postcode = addslashes($address['postcode']);

        $zip = $postcode;
        $country = os_get_country_name($country_id);
        $state = os_get_zone_name($country_id, $zone_id, $state);

        if ($html) {
            $HR = '<hr />';
            $hr = '<hr />';
            if ( ($boln == '') && ($eoln == "\n") ) { 
                $CR = '<br />';
                $cr = '<br />';
                $eoln = $cr;
            } else {
                $CR = $eoln . $boln;
                $cr = $CR;
            }
        } else {
            $CR = $eoln;
            $cr = $CR;
            $HR = '----------------------------------------';
            $hr = '----------------------------------------';
        }

        $statecomma = '';
        $streets = $street;
        if ($suburb != '') $streets = $street . $cr . $suburb;
        if ($firstname == '') $firstname = addslashes($address['name']);
        if ($country == '') 
        { 
            $country = $address['country'];
        }

        if ($state != '') $statecomma = $state . ', ';

        $fmt = $address_format['format'];
        //plugins filter. фильтруем формат вывода адреса
        $fmt = apply_filter('address_format', $fmt);

        eval("\$address = \"$fmt\";");

        if ( (ACCOUNT_COMPANY == 'true') && (os_not_null($company)) ) {
            $address = $company . $cr . $address;
        }

        $address = stripslashes($address);

        $address = apply_filter('address', $address);

        return $address;
    }


    function os_date_short($raw_date) 
    {
        if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;

        $year = substr($raw_date, 0, 4);
        $month = (int)substr($raw_date, 5, 2);
        $day = (int)substr($raw_date, 8, 2);
        $hour = (int)substr($raw_date, 11, 2);
        $minute = (int)substr($raw_date, 14, 2);
        $second = (int)substr($raw_date, 17, 2);

        if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) 
        {
            return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
        } 
        else 
        {
            return preg_replace('/2037' . '$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
        }
    }

    function os_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) 
    {
        if (strpos($value, '.')) {
            $loop = true;
            while ($loop) {
                if (substr($value, -1) == '0') {
                    $value = substr($value, 0, -1);
                } else {
                    $loop = false;
                    if (substr($value, -1) == '.') {
                        $value = substr($value, 0, -1);
                    }
                }
            }
        }

        if ($padding > 0) {
            if ($decimal_pos = strpos($value, '.')) {
                $decimals = strlen(substr($value, ($decimal_pos+1)));
                for ($i=$decimals; $i<$padding; $i++) {
                    $value .= '0';
                }
            } else {
                $value .= '.';
                for ($i=0; $i<$padding; $i++) {
                    $value .= '0';
                }
            }
        }

        return $value;
    }

    function os_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
        $field = '<input type="' . os_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '"';

        if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
            $field .= ' value="' . os_parse_input_field_data($GLOBALS[$name], array('"' => '&quot;')) . '"';
        } elseif (os_not_null($value)) {
            $field .= ' value="' . os_parse_input_field_data($value, array('"' => '&quot;')) . '"';
        }
        if (os_not_null($parameters)) $field .= ' ' . $parameters;
        $field .= ' />';
        return $field;
    }

    function os_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
        $field = '<select name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '"';

        if (os_not_null($parameters)) $field .= ' ' . $parameters;

        $field .= '>';

        if (empty($default) && isset($GLOBALS[$name])) $default = $GLOBALS[$name];

        for ($i=0, $n=sizeof($values); $i<$n; $i++) 
		{
            $field .= '<option value="' . os_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
            if ($default == $values[$i]['id']) 
			{
                $field .= ' selected="selected"';
            }

            $field .= '>' . os_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
        }
        $field .= '</select>';

        if ($required == true) $field .= TEXT_FIELD_REQUIRED;

        return $field;
    }

    function os_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) 
    {
        $field = '<textarea class="round plugin" name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '" id="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '" cols="' . os_parse_input_field_data($width, array('"' => '&quot;')) . '" rows="' . os_parse_input_field_data($height, array('"' => '&quot;')) . '"';

        if (os_not_null($parameters)) $field .= ' ' . $parameters;

        $field .= '>';

        if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
            $field .= $GLOBALS[$name];
        } elseif (os_not_null($text)) {
            $field .= $text;
        }

        $field .= '</textarea>';

        return $field;
    }


    function os_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
        $selection = '<input type="' . os_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '"';

        if (os_not_null($value)) $selection .= ' value="' . os_parse_input_field_data($value, array('"' => '&quot;')) . '"';

        if ( ($checked == true) || @($GLOBALS[$name] == 'on') || ( (isset($value)) && @($GLOBALS[$name] == $value) ) ) {
            $selection .= ' checked="checked"';
        }

        if (os_not_null($parameters)) $selection .= ' ' . $parameters;

        $selection .= ' />';

        return $selection;
    }


    function os_get_all_get_params($exclude_array = '') 
	{
        global $InputFilter;

        if (!is_array($exclude_array)) $exclude_array = array();

		$get = $_GET;
		
		if ( sizeof($get) > 0 )
		{
		   $get2 = array();
		   
		   foreach ($get as $name => $value)
		   {
		        if ( is_array($value) && count($value) == 1 )
			    {
			       foreach ($value as $num => $value2)
				   {
					  if (is_scalar($value2))
				      $get2[ $name.'['.$num.']' ] =  stripslashes($value2);
				   }
			    }
			    else
			    {
				   if (is_scalar($value))
			       $get2[ $name ] =  stripslashes($value);
			    }
		   }
	   
          $get = $get2; 
		   
		}
		
        $get_url = '';
        if (is_array($get) && (sizeof($get) > 0)) 
		{
            reset($get);
				
            while (list($key, $value) = each($get)) 
			{
               if (  ($key != os_session_name()) && ($key != 'error') && ($key != 'cPath') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) 
				{       
                   $get_url .= $key . '=' . $value . '&';
                }
            }
			
        }
		
        return $get_url;
    }


    function os_get_customers_statuses() {

        $customers_statuses_array = array(array());

        if ($_SESSION['languages_id']=='') 
        {
            $customers_statuses_query = os_db_query("select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '1' order by customers_status_id");
        } 
        else 
        {
            $customers_statuses_query = os_db_query("select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by customers_status_id");
        }

        $i=1;
        while ($customers_statuses = os_db_fetch_array($customers_statuses_query)) {
            $i=$customers_statuses['customers_status_id'];
            $customers_statuses_array[] = array('id' => $customers_statuses['customers_status_id'],
            'text' => $customers_statuses['customers_status_name'],
            'csa_public' => $customers_statuses['customers_status_public'],
            'csa_show_price' => $customers_statuses['customers_status_show_price'],
            'csa_show_price_tax' => $customers_statuses['customers_status_show_price_tax'],
            'csa_image' => $customers_statuses['customers_status_image'],
            'csa_discount' => $customers_statuses['customers_status_discount'],
            'csa_ot_discount_flag' => $customers_statuses['customers_status_ot_discount_flag'],
            'csa_ot_discount' => $customers_statuses['customers_status_ot_discount'],
            'csa_graduated_prices' => $customers_statuses['customers_status_graduated_prices'],
            'csa_cod_permission' => $customers_statuses['customers_status_cod_permission'],
            'csa_cc_permission' => $customers_statuses['customers_status_cc_permission'],
            'csa_bt_permission' => $customers_statuses['customers_status_bt_permission'],
            );
        }

        return $customers_statuses_array;
    }


    function os_get_path($current_category_id = '') 
    {
        global $categories_cache;
        global $cPath_array;

        if (os_not_null($current_category_id)) {
            $cp_size = sizeof($cPath_array);
            if ($cp_size == 0) {
                $cPath_new = $current_category_id;
            } else {
                $cPath_new = '';
                //$last_category_query = "select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $cPath_array[($cp_size-1)] . "'";
                // $last_category_query  = osDBquery($last_category_query);
                $last_category['parent_id'] = $categories_cache[$cPath_array[($cp_size-1)]];

                //$current_category_query = "select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $current_category_id . "'";
                //$current_category_query  = osDBquery($current_category_query);
                //$current_category = os_db_fetch_array($current_category_query,true);

                $current_category['parent_id'] = $categories_cache[$current_category_id];

                if ($last_category['parent_id'] == $current_category['parent_id']) {
                    for ($i=0; $i<($cp_size-1); $i++) {
                        $cPath_new .= '_' . $cPath_array[$i];
                    }
                } else {
                    for ($i=0; $i<$cp_size; $i++) {
                        $cPath_new .= '_' . $cPath_array[$i];
                    }
                }
                $cPath_new .= '_' . $current_category_id;

                if (substr($cPath_new, 0, 1) == '_') {
                    $cPath_new = substr($cPath_new, 1);
                }
            }
        } else {
            $cPath_new = (os_not_null($cPath_array)) ? implode('_', $cPath_array) : '';
        }
        return 'cPath=' . $cPath_new;
    }

    function os_get_uprid($prid, $params) {
        if (is_numeric($prid)) {
            $uprid = $prid;

            if (is_array($params) && (sizeof($params) > 0)) {
                $attributes_check = true;
                $attributes_ids = '';

                reset($params);
                while (list($option, $value) = each($params)) {
                    if (is_numeric($option) && is_numeric($value)) {
                        $attributes_ids .= '{' . (int)$option . '}' . (int)$value;
                    } else {
                        $attributes_check = false;
                        break;
                    }
                }

                if ($attributes_check == true) {
                    $uprid .= $attributes_ids;
                }
            }
        } else {
            $uprid = os_get_prid($prid);

            if (is_numeric($uprid)) {
                if (strpos($prid, '{') !== false) {
                    $attributes_check = true;
                    $attributes_ids = '';

                    $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

                    for ($i=0, $n=sizeof($attributes); $i<$n; $i++) {
                        $pair = explode('}', $attributes[$i]);

                        if (is_numeric($pair[0]) && is_numeric($pair[1])) {
                            $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
                        } else {
                            $attributes_check = false;
                            break;
                        }
                    }

                    if ($attributes_check == true) {
                        $uprid .= $attributes_ids;
                    }
                }
            } else {
                return false;
            }
        }

        return $uprid;
    }

    if (!function_exists('mb_substr')){
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

    function os_in_array($value, $array) {
        if (!$array) $array = array();

        if (function_exists('in_array')) {
            if (is_array($value)) {
                for ($i=0; $i<sizeof($value); $i++) {
                    if (in_array($value[$i], $array)) return true;
                }
                return false;
            } else {
                return in_array($value, $array);
            }
        } else {
            reset($array);
            while (list(,$key_value) = each($array)) {
                if (is_array($value)) {
                    for ($i=0; $i<sizeof($value); $i++) {
                        if ($key_value == $value[$i]) return true;
                    }
                    return false;
                } else {
                    if ($key_value == $value) return true;
                }
            }
        }

        return false;
    }

    function os_image($src, $alt = '', $width = '', $height = '', $parameters = '') {
        if ( (empty($src) || ($src == http_path('images')) || ( $src == http_path('images_thumbnail')))) {
            return false;
        }

        $image = '<img src="' . os_parse_input_field_data($src, array('"' => '&quot;')) . '" alt="' . os_parse_input_field_data($alt, array('"' => '&quot;')) . '"';

        if (os_not_null($alt)) {
            $image .= ' title=" ' . os_parse_input_field_data($alt, array('"' => '&quot;')) . ' "';
        }

        if ( (CONFIG_CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height)) ) {
            if ($image_size = @getimagesize($src)) {
                if (empty($width) && os_not_null($height)) {
                    $ratio = $height / $image_size[1];
                    $width = $image_size[0] * $ratio;
                } elseif (os_not_null($width) && empty($height)) {
                    $ratio = $width / $image_size[0];
                    $height = $image_size[1] * $ratio;
                } elseif (empty($width) && empty($height)) {
                    $width = $image_size[0];
                    $height = $image_size[1];
                }
            } elseif (IMAGE_REQUIRED == 'false') {
                return false;
            }
        }

        if (os_not_null($width) && os_not_null($height)) {
            $image .= ' width="' . os_parse_input_field_data($width, array('"' => '&quot;')) . '" height="' . os_parse_input_field_data($height, array('"' => '&quot;')) . '"';
        }

        if (os_not_null($parameters)) $image .= ' ' . $parameters;

        $image .= ' />';
        return $image;
    }

    function os_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true)
	{
		$param_array = array();
		$params = '';
		$action = '';
		$products_id = '';
		$sort = '';
		$direction = '';
		$filter_id = '';
		$on_page = '';
		$q = '';
		$price_min = '';
		$price_max = '';
		$language = '';
		$currency = '';
		$page_num = '';
		$matches = array();

		if ($page == FILENAME_DEFAULT)
		{
			if (strpos($parameters, 'cat') === false)
			{
				return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
			}
			else
			{
				$categories_id = -1;
				$param_array = explode('&', $parameters);

				for ($i = 0, $n = sizeof($param_array); $i < $n; $i++)
				{
					$parsed_param = explode('=', $param_array[$i]);
					if ($parsed_param[0] === 'cat')
					{
						$pos = strrpos($parsed_param[1], '_');
						if ($pos === false)
						{
							$categories_id = $parsed_param[1];
						}
						else
						{
							if (preg_match('/^c(.*)_/', $parsed_param[1], $matches))
							{
								$categories_id = $matches[1];
							}
						}
					} elseif ($parsed_param[0] === 'action') {
						$action = $parsed_param[1];
					} elseif ($parsed_param[0] === 'BUYproducts_id') {
						$products_id = $parsed_param[1];
					} elseif ($parsed_param[0] === 'sort') {
						$sort = $parsed_param[1];
					} elseif ($parsed_param[0] === 'direction') {
						$direction = $parsed_param[1];
					} elseif ($parsed_param[0] === 'filter_id') {
						$filter_id = $parsed_param[1];
					} elseif ($parsed_param[0] === 'language') {
						$language = $parsed_param[1];
					} elseif ($parsed_param[0] === 'currency') {
						$currency = $parsed_param[1];
					} elseif ($parsed_param[0] === 'q') {
						$q = $parsed_param[1];
					} elseif ($parsed_param[0] === 'price_min') {
						$price_min = $parsed_param[1];
					} elseif ($parsed_param[0] === 'price_max') {
						$price_max = $parsed_param[1];
					}
					elseif ($parsed_param[0] === 'on_page')
					{
						if (os_not_null($parsed_param[1]))
							$on_page = $parsed_param[1];
						else
							$on_page = -1;
					}
					elseif ($parsed_param[0] === 'page')
						$page_num = $parsed_param[1];
				}

				global $categories_url_cache;

				$categories_url = '';
				if (isset($categories_url_cache[$categories_id]))
				{
					$categories_url = $categories_url_cache[$categories_id];
				}

				if ($categories_url == '')
				{
					return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
				}
				else
				{
					if ($connection == 'NONSSL')
					{
						$link = HTTP_SERVER;
					}
					elseif ($connection == 'SSL')
					{
						if (ENABLE_SSL == 'true')
							$link = HTTPS_SERVER ;
						else
							$link = HTTP_SERVER;
					}
					else
					{
						die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
					}

					if ($connection == 'SSL' && ENABLE_SSL == 'true')
						$link .= DIR_WS_HTTPS_CATALOG;
					else
						$link .= DIR_WS_CATALOG;

					if (os_not_null($action)) {
						$params .= '&action=' . $action;
					}

					if (os_not_null($products_id)) {
						$params .= '&BUYproducts_id=' . $products_id;
					}

					if (os_not_null($sort)) {
						$params .= '&sort=' . $sort;
					}

					if (os_not_null($direction)) {
						$params .= '&direction=' . $direction;
					}

					if (os_not_null($filter_id)) {
						$params .= '&filter_id=' . $filter_id;
					}

					if (os_not_null($language)) {
						$params .= '&language=' . $language;
					}

					if (os_not_null($currency)) {
						$params .= '&currency=' . $currency;
					}

					if (os_not_null($q)) {
						$params .= '&q=' . $q;
					}

					if (os_not_null($price_min)) {
						$params .= '&price_min=' . $price_min;
					}

					if (os_not_null($price_max)) {
						$params .= '&price_max=' . $price_max;
					}

					if ($on_page === -1)
						$params .= '&on_page=';
					elseif ($on_page > 0)
						$params .= '&on_page=' . $on_page;

					if (os_not_null($page_num)) {
						$params .= '&page=' . $page_num;
					}

					if (os_not_null($params))
					{
						if (strpos($params, '&') === 0) {
							$params = substr($params, 1);
						}

						$params = str_replace('&', '&amp;', $params);

						$categories_url .= '?' . $params;
					}

					$link_ajax = '';

					if (AJAX_CART == 'true')
					{
						if( os_not_null($parameters) && preg_match("/buy_now/i", $parameters) && $page != 'ajax_shopping_cart.php')
						{
							$link_ajax = '" onclick="doBuyNowGet(\'' . os_href_link( 'ajax_shopping_cart.php', $parameters, $connection, $add_session_id, $search_engine_safe) . '\'); return false;';
						}
					}

					return $link . $categories_url . $link_ajax;
				}
			}
		}
		elseif ($page == FILENAME_PRODUCT_INFO)
		{
			$products_id = -1;
			$action = '';
			$language = '';
			$currency = '';
			$param_array = explode('&', $parameters);

			for ($i = 0, $n = sizeof($param_array); $i < $n; $i++)
			{
				$parsed_param = explode('=', $param_array[$i]);
				if ($parsed_param[0] === 'products_id') {
					$products_id = $parsed_param[1];
				} elseif ($parsed_param[0] === 'action') {
					$action = $parsed_param[1];
				} elseif ($parsed_param[0] === 'language') {
					$language = $parsed_param[1];
				} elseif ($parsed_param[0] === 'currency') {
					$currency = $parsed_param[1];
				}
				elseif ($parsed_param[0] === 'info')
				{
					if (preg_match('/^p(.*)_/', $parsed_param[1], $matches)) {
						$products_id = $matches[1];
					}
				}
			}

			global $products_url_cache;

			$products_page_url = '';
			if (isset($products_url_cache[$products_id]))
			{
				$products_page_url = $products_url_cache[$products_id];
			}

			if ($products_page_url == '')
			{
				return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
			}
			else
			{
				if ($connection == 'NONSSL')
				{
					$link = HTTP_SERVER;
				}
				elseif ($connection == 'SSL')
				{
					if (ENABLE_SSL == 'true')
						$link = HTTPS_SERVER ;
					else
						$link = HTTP_SERVER;
				}
				else
				{
					die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
				}

				if ($connection == 'SSL' && ENABLE_SSL == 'true')
					$link .= DIR_WS_HTTPS_CATALOG;
				else
					$link .= DIR_WS_CATALOG;

				if (os_not_null($action)) {
					$products_page_url .= '?action=' . $action;
				}

				if (os_not_null($language)) {
					$products_page_url .= '?language=' . $language;
				}

				if (os_not_null($currency)) {
					$products_page_url .= '?currency=' . $currency;
				}

				return $link . $products_page_url;
			}
		}
		elseif ($page == FILENAME_ARTICLE_INFO)
		{
			$a_id = -1;
			$param_array = explode('&', $parameters);

			for ($i = 0, $n = sizeof($param_array); $i < $n; $i++)
			{
				$parsed_param = explode('=', $param_array[$i]);
				if ($parsed_param[0] === 'articles_id') {
					$a_id = $parsed_param[1];
				} elseif ($parsed_param[0] === 'language') {
					$language = $parsed_param[1];
				} elseif ($parsed_param[0] === 'currency') {
					$currency = $parsed_param[1];
				}
			}

			global $articles_url_cache;

			$a_url = '';
			if (isset($articles_url_cache[$a_id]))
			{
				$a_url = $articles_url_cache[$a_id];
			}

			if ($a_url == '')
			{
				return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
			}
			else
			{

				if ($connection == 'NONSSL')
				{
					$link = HTTP_SERVER;
				}
				elseif ($connection == 'SSL')
				{
					if (ENABLE_SSL == 'true') {
						$link = HTTPS_SERVER ;
					} else {
						$link = HTTP_SERVER;
					}
				}
				else
				{
					die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
				}

				if ($connection == 'SSL' && ENABLE_SSL == 'true')
					$link .= DIR_WS_HTTPS_CATALOG;
				else
					$link .= DIR_WS_CATALOG;

				if (os_not_null($language)) {
					$a_url .= '?language=' . $language;
				}

				if (os_not_null($currency)) {
					$a_url .= '?currency=' . $currency;
				}

				return $link . $a_url;
			}

		} elseif ($page == FILENAME_NEWS) {

		$n_id = -1;
		$param_array = explode('&', $parameters);

		for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
		$parsed_param = explode('=', $param_array[$i]);
		if ($parsed_param[0] === 'news_id') {
		$n_id = $parsed_param[1];
		} elseif ($parsed_param[0] === 'language') {
		$language = $parsed_param[1];
		} elseif ($parsed_param[0] === 'currency') {
		$currency = $parsed_param[1];
		}
		}

		global $news_url_cache;
		$n_url = '';

		if (isset($news_url_cache[$n_id]))
		{
		$n_url = $news_url_cache[$n_id];
		}

		//$n_url = os_db_query('select news_page_url from ' . TABLE_LATEST_NEWS . ' where news_id="' . $n_id . '"');
		//$n_url = os_db_fetch_array($n_url);
		//$n_url = $n_url['news_page_url'];

		if ($n_url == '') {
		return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
		} else {

		if ($connection == 'NONSSL') {
		$link = HTTP_SERVER;
		} elseif ($connection == 'SSL') {
		if (ENABLE_SSL == 'true') {
		$link = HTTPS_SERVER ;
		} else {
		$link = HTTP_SERVER;
		}
		} else {
		die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
		}

		if ($connection == 'SSL' && ENABLE_SSL == 'true') {
		$link .= DIR_WS_HTTPS_CATALOG;
		} else {
		$link .= DIR_WS_CATALOG;
		}

		if (os_not_null($language)) {
		$n_url .= '?language=' . $language;
		}

		if (os_not_null($currency)) {
		$n_url .= '?currency=' . $currency;
		}

		return $link . $n_url;
		}

		} elseif ($page == FILENAME_ARTICLES) {

		$t_id = -1;
		$page_num = '';
		$param_array = explode('&', $parameters);

		for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
		$parsed_param = explode('=', $param_array[$i]);
		if ($parsed_param[0] === 'tPath') {
		$t_id = $parsed_param[1];
		}
		if ($parsed_param[0] === 'page') {
		$page_num = $parsed_param[1];
		} elseif ($parsed_param[0] === 'language') {
		$language = $parsed_param[1];
		} elseif ($parsed_param[0] === 'currency') {
		$currency = $parsed_param[1];
		}
		}

		global $topics_url_cache;
		$t_url = '';

		if (isset($topics_url_cache[$t_id]))
		{
		$t_url = $topics_url_cache[$t_id];
		}

		//$t_url = os_db_query('select topics_page_url from ' . TABLE_TOPICS . ' where topics_id="' . $t_id . '"');
		//$t_url = os_db_fetch_array($t_url);
		// $t_url = $t_url['topics_page_url'];

		if ($t_url == '') {
		return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
		} else {

		if ($connection == 'NONSSL') {
		$link = HTTP_SERVER;
		} elseif ($connection == 'SSL') {
		if (ENABLE_SSL == 'true') {
		$link = HTTPS_SERVER ;
		} else {
		$link = HTTP_SERVER;
		}
		} else {
		die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
		}

		if ($connection == 'SSL' && ENABLE_SSL == 'true') {
		$link .= DIR_WS_HTTPS_CATALOG;
		} else {
		$link .= DIR_WS_CATALOG;
		}

		if (os_not_null($page_num)) {
		$t_url .= '?page=' . $page_num;
		}

		if (os_not_null($language)) {
		$t_url .= '?language=' . $language;
		}

		if (os_not_null($currency)) {
		$t_url .= '?currency=' . $currency;
		}

		return $link . $t_url;
		}

		}elseif ($page == FILENAME_FAQ)
		{
		/////////
		$faq_id = -1;
		$param_array = explode('&', $parameters);

		for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
		$parsed_param = explode('=', $param_array[$i]);
		if ($parsed_param[0] === 'faq_id') {
		$faq_id = $parsed_param[1];
		} elseif ($parsed_param[0] === 'language') {
		$language = $parsed_param[1];
		} elseif ($parsed_param[0] === 'currency') {
		$currency = $parsed_param[1];
		}
		}
		global $faq_url_cache;

		if (isset($faq_url_cache[$faq_id]))
		{
		$faq_url = $faq_url_cache[$faq_id];
		}

		if (empty($faq_url))
		{
		return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
		}
		else
		{
		return  $faq_url ;
		}
		}

		elseif ($page == FILENAME_CONTENT) {

		$co_id = -1;
		$param_array = explode('&', $parameters);

		for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
		$parsed_param = explode('=', $param_array[$i]);
		if ($parsed_param[0] === 'coID') {
		$co_id = $parsed_param[1];
		} elseif ($parsed_param[0] === 'language') {
		$language = $parsed_param[1];
		} elseif ($parsed_param[0] === 'action') {
		$action = $parsed_param[1];
		} elseif ($parsed_param[0] === 'currency') {
		$currency = $parsed_param[1];
		}
		}

		global $content_url_cache;

		$co_url = '';

		if (isset($content_url_cache[$co_id]))
		{
		$co_url = $content_url_cache[$co_id];
		}
		//$co_url = os_db_query('select content_page_url from ' . TABLE_CONTENT_MANAGER . ' where content_id="' . $co_id . '"');
		//$co_url = os_db_fetch_array($co_url);
		//$co_url = $co_url['content_page_url'];

		if ($co_url == '') {
		return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
		} else {

		if ($connection == 'NONSSL') {
		$link = HTTP_SERVER;
		} elseif ($connection == 'SSL') {
		if (ENABLE_SSL == 'true') {
		$link = HTTPS_SERVER ;
		} else {
		$link = HTTP_SERVER;
		}
		} else {
		die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
		}

		if ($connection == 'SSL' && ENABLE_SSL == 'true') {
		$link .= DIR_WS_HTTPS_CATALOG;
		} else {
		$link .= DIR_WS_CATALOG;
		}

		if (os_not_null($language)) {
		$co_url .= '?language=' . $language;
		}

		if (os_not_null($action)) {
		$co_url .= '?action=' . $action;
		}

		if (os_not_null($currency)) {
		$co_url .= '?currency=' . $currency;
		}

		return $link . $co_url;
		}
		}
		else {
		return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
		}
	}

    // Categories/Products URL end



    // The HTML href link wrapper function
    // Categories/Products URL

    function os_href_link_original($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) 
    {
        global $request_type, $session_started, $http_domain, $https_domain,$truncate_session_id;

        if (!isset($truncate_session_id)) $truncate_session_id = 0;
        if (!os_not_null($page)) {
            die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link!<br /><br />');
        }
        $link = '';

        if ($connection == 'NONSSL') {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == true) {
                $link = HTTPS_SERVER . DIR_WS_CATALOG;
            } else {
                $link = HTTP_SERVER . DIR_WS_CATALOG;
            }
        } else {
            die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');
        }

        if (os_not_null($parameters)) {
            $link .= $page . '?' . $parameters;
            $separator = '&';
        } else {
            $link .= $page;
            $separator = '?';
        }

        while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

        // Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
        if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
            if (defined('SID') && os_not_null(SID)) {
                $sid = SID;
            } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
                if ($http_domain != $https_domain) {
                    $sid = session_name() . '=' . session_id();
                }
            }        
        }

        // remove session if useragent is a known Spider
        if (isset($truncate_session_id)) $sid=NULL;

        if (isset($sid)) {
            $link .= $separator . $sid;
        }

        if ( (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) ) {
            while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);

            $link = str_replace('?', '/', $link);
            $link = str_replace('&', '/', $link);
            $link = str_replace('=', '/', $link);
            $separator = '?';
        }

        $link_ajax = '';

        if (AJAX_CART == 'true') 
        {
            if( os_not_null($parameters) && preg_match("/buy_now/i", $parameters) && $page != 'ajax_shopping_cart.php')
            {
                $_ajax_tmp = os_href_link( 'ajax_shopping_cart.php', $parameters, $connection, $add_session_id, false);
                $_ajax_tmp = str_replace('&','&amp;', $_ajax_tmp);
                $link_ajax = '" onclick="doBuyNowGet(\'' . $_ajax_tmp. '\'); return false;';
            }
        }

        return $link . $link_ajax;
    }

    function os_href_link_admin($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
        global $request_type, $session_started, $http_domain, $https_domain;

        if (!os_not_null($page)) {
            die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link!<br /><br />');
        }

        if ($connection == 'NONSSL') {
            $link = HTTP_SERVER . DIR_WS_CATALOG;
        } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == true) {
                $link = HTTPS_SERVER . DIR_WS_CATALOG;
            } else {
                $link = HTTP_SERVER . DIR_WS_CATALOG;
            }
        } else {
            die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');
        }

        if (os_not_null($parameters)) {
            $link .= $page . '?' . $parameters;
            $separator = '&';
        } else {
            $link .= $page;
            $separator = '?';
        }

        while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

        // Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
        if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
            if (defined('SID') && os_not_null(SID)) {
                $sid = SID;
            } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
                if ($http_domain != $https_domain) {
                    $sid = session_name() . '=' . session_id();
                }
            }
        }

        if (isset($truncate_session_id)) $sid=NULL;

        if (isset($sid)) {
            $link .= $separator . $sid;
        }


        return $link;
    }




    function os_gv_account_update($customer_id, $gv_id) {
        $customer_gv_query = os_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . $customer_id . "'");
        $coupon_gv_query = os_db_query("select coupon_amount from " . TABLE_COUPONS . " where coupon_id = '" . $gv_id . "'");
        $coupon_gv = os_db_fetch_array($coupon_gv_query);
        if (os_db_num_rows($customer_gv_query) > 0) {
            $customer_gv = os_db_fetch_array($customer_gv_query);
            $new_gv_amount = $customer_gv['amount'] + $coupon_gv['coupon_amount'];
            $new_gv_amount = str_replace(",", ".", $new_gv_amount);
            $gv_query = os_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount = '" . $new_gv_amount . "' where customer_id = '" . $customer_id . "'");
        } else {
            $gv_query = os_db_query("insert into " . TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) values ('" . $customer_id . "', '" . $coupon_gv['coupon_amount'] . "')");
        }
    }

    // Categories/Products URL end

    function os_redirect($url) 
    {

        if (AJAX_CART == 'true') {
            global $_GET, $PHP_SELF, $_RESULT;
            if ( strpos( basename($PHP_SELF), 'ajax_shopping_cart.php')!==FALSE ) {
                if ( $url == os_href_link(FILENAME_SSL_CHECK) ||
                $url == os_href_link(FILENAME_LOGIN) ||
                $url == os_href_link(FILENAME_COOKIE_USAGE) ||
                ( $_GET['action'] === 'buy_now' && os_has_product_attributes($_GET['BUYproducts_id']) )
                ) {
                    $_RESULT['ajax_redirect'] = $url;
                }
                return;
            }
        }

        if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on' || getenv('HTTPS') == '1') ) { 
            if (substr($url, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) {
                $url = HTTPS_SERVER . substr($url, strlen(HTTP_SERVER)); 
            }
        }

        header('Location: ' . preg_replace("/[\r\n]+(.*)$/i", "", $url));
        os_exit(); 
    }


    function os_round($number, $precision) {
        if (strpos($number, '.') && (strlen(substr($number, 
        strpos($number, '.')+1)) > $precision)) {
            $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);

            if (substr($number, -1) >= 5) {
                if ($precision > 1) {
                    $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
                } elseif ($precision == 1) {
                    $number = substr($number, 0, -1) + 0.1;
                } else {
                    $number = substr($number, 0, -1) + 1;
                }
            } else {
                $number = substr($number, 0, -1);
            }
        }
        return $number;
    }

    function os_date_long($raw_date) 
    {
        if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;
        $year = (int)substr($raw_date, 0, 4);
        $month = (int)substr($raw_date, 5, 2);
        $day = (int)substr($raw_date, 8, 2);
        $hour = (int)substr($raw_date, 11, 2);
        $minute = (int)substr($raw_date, 14, 2);
        $second = (int)substr($raw_date, 17, 2);
        if (function_exists('os_date_long_translate')) 
            return os_date_long_translate(strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year))); 
        return strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));
    }

    function os_draw_form($name, $action, $method = 'post', $parameters = '') 
    {
        $form = '<form id="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '" action="' . os_parse_input_field_data($action, array('"' => '&quot;')) . '" method="' . os_parse_input_field_data($method, array('"' => '&quot;')) . '"';

        if (os_not_null($parameters)) $form .= ' ' . $parameters;

        if (AJAX_CART == 'true') 
        {
            if( preg_match("/add_product/i", $action) )
            {
                $form .= ' onsubmit="doAddProduct(this); return false;"';
            }
        }

        $form .= '>';

        return $form;
    }

    function os_draw_hidden_field($name, $value = '', $parameters = '') 
    {
	    if ( !is_array($value) )
		{
            $field = '<input type="hidden" name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '" value="';

            if (os_not_null($value)) 
			{
                $field .= os_parse_input_field_data($value, array('"' => '&quot;')) . '"';
            } 
			else 
			{
                $field .= os_parse_input_field_data(@$GLOBALS[$name], array('"' => '&quot;')) . '"';
            }

            if ( os_not_null( $parameters ) ) $field .= ' ' . $parameters;

            $field .= ' />';
        
            return $field;
		}
		else
		{
		    return '';
		}
    }

    function os_get_products_name($product_id, $language = '') 
    {
        if (empty($language)) $language = $_SESSION['languages_id'];
        $product_query = "select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $product_id . "' and language_id = '" . $language . "'";
        $product_query  = osDBquery($product_query);
        $product = os_db_fetch_array($product_query,true);
        return $product['products_name'];
    }

    function os_break_string($string, $len, $break_char = '-') 
    {
        $l = 0;
        $output = '';
        for ($i=0, $n=utf8_strlen($string); $i<$n; $i++) {
            $char = utf8_substr($string, $i, 1);
            if ($char != ' ') {
                $l++;
            } else {
                $l = 0;
            }
            if ($l > $len) {
                $l = 1;
                $output .= $break_char;
            }
            $output .= $char;
        }
        return $output;
    }

    function os_get_cross_sell_name($cross_sell_group, $language_id = '') 
    {

        if (!$language_id) $language_id = $_SESSION['languages_id'];

        $cross_sell_query = os_db_query("select groupname from ".TABLE_PRODUCTS_XSELL_GROUPS." where products_xsell_grp_name_id = '".$cross_sell_group."' and language_id = '".$language_id."'");
        $cross_sell = os_db_fetch_array($cross_sell_query);

        return $cross_sell['groupname'];
    }

    function os_get_zone_name($country_id, $zone_id, $default_zone) 
    {
        $zone_query = os_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_id = '" . $zone_id . "'");
        if (os_db_num_rows($zone_query)) {
            $zone = os_db_fetch_array($zone_query);
            return $zone['zone_name'];
        } else {
            return $default_zone;
        }
    }

    function os_get_zone_code($country_id, $zone_id, $default_zone) 
    {
        $zone_query = os_db_query("select zone_code from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_id = '" . $zone_id . "'");
        if (os_db_num_rows($zone_query)) {
            $zone = os_db_fetch_array($zone_query);
            return $zone['zone_code'];
        } else {
            return $default_zone;
        }
    }

    function os_rand($min = null, $max = null) 
    {
        static $seeded;

        if (!isset($seeded)) {
            mt_srand((double)microtime()*1000000);
            $seeded = true;
        }

        if (isset($min) && isset($max)) {
            if ($min >= $max) {
                return $min;
            } else {
                return mt_rand($min, $max);
            }
        } else {
            return mt_rand();
        }
    }

    function os_draw_password_field($name, $value = '', $parameters = 'maxlength="40"') 
    {
        return os_draw_input_field($name, $value, $parameters, 'password', false);
    }

    function os_draw_radio_field($name, $value = '', $checked = false, $parameters = '') 
    {
        if (is_array($name)) return os_draw_selection_fieldNote($name, 'radio', $value, $checked, $parameters); 
        return os_draw_selection_field($name, 'radio', $value, $checked, $parameters);
    }

    function os_draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') 
    {
        return os_draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
    }

    function os_set_specials_status($specials_id, $status) 
    {
        return os_db_query("update " . TABLE_SPECIALS . " set status = '" . $status . "', date_status_change = now() where specials_id = '" . $specials_id . "'");
    }

    function os_calculate_tax($price, $tax) 
    {
        return $price * $tax / 100;
    }

    function os_get_country_name($country_id) 
    {
        $country_array = os_get_countriesList($country_id);
        return $country_array['countries_name'];
    }

    function os_get_prid($uprid) 
    {
        $pieces = explode('{', $uprid);

        if (is_numeric($pieces[0])) 
        {
            return $pieces[0];
        } 
        else 
        {
            return false;
        }
    }

    function os_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') 
    {
        //return os_image(http_path('images') . $image, '', $width, $height);
    }

    function os_convert_linefeeds($from, $to, $string) 
    {
        return str_replace($from, $to, $string);
    }  

    function os_remove_non_numeric($var) 
    {	  
        $var=preg_replace('/[^0-9]/','',$var);
        return $var;
    }

    function os_set_time_limit($limit) 
    {
        if (!get_cfg_var('safe_mode')) 
        {
            set_time_limit($limit);
        }
    }

    function os_browser_detect($component) 
    {

        return stristr($_SERVER['HTTP_USER_AGENT'], $component);
    }

    function os_get_countries_with_iso_codes($countries_id) 
    {
        return os_get_countriesList($countries_id, true);
    }

?>