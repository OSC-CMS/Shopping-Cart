<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class osPrice {
	var $currencies;

	function osPrice($currency, $cGroup) {

		$this->currencies = array ();
		$this->cStatus = array ();
		$this->actualGroup = $cGroup;
		$this->actualCurr = $currency;
		$this->TAX = array ();
		$this->SHIPPING = array();
		$this->showFrom_Attributes = true;

        global $default_cache;
		
		if (!isset($default_cache['currencies']))
	    {
		    		
		    $currencies_query = "SELECT * FROM ".TABLE_CURRENCIES;
		    $currencies_query = osDBquery($currencies_query);
		    while ($currencies = os_db_fetch_array($currencies_query, true)) 
			{
			     $this->currencies[$currencies['code']] = array 
				 (
			        'title' => $currencies['title'], 
			        'symbol_left' => $currencies['symbol_left'], 
			        'symbol_right' => $currencies['symbol_right'], 
			        'decimal_point' => $currencies['decimal_point'], 
			        'thousands_point' => $currencies['thousands_point'], 
			        'decimal_places' => $currencies['decimal_places'], 
			        'value' => $currencies['value']
			     );
		    }
		}
		else
		{
		   if (!empty($default_cache['currencies']))
		   {
			foreach ($default_cache['currencies'] as $code_value => $_value) 
			{
			   $this->currencies[$code_value] = $_value;
			}
		    }
		}
		
		 //cache get_customers_status
		$customers_status_value = get_customers_status($this->actualGroup);
		
		$this->cStatus = array (

		'customers_status_id' => $this->actualGroup, 
		'customers_status_name' => $customers_status_value['customers_status_name'], 
		'customers_status_image' => $customers_status_value['customers_status_image'], 
		'customers_status_public' => $customers_status_value['customers_status_public'], 
		'customers_status_discount' => $customers_status_value['customers_status_discount'], 
		'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'], 
		'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'], 
		'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'], 
		'customers_status_show_price' => $customers_status_value['customers_status_show_price'], 
		'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'], 
		'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'], 
		'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'], 
		'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'], 
		'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes'], 
		'customers_fsk18' => $customers_status_value['customers_fsk18'], 
		'customers_fsk18_display' => $customers_status_value['customers_fsk18_display']
		
		);
        
		if (!isset($default_cache['tax_class_id']))
		{
		$zones_query = osDBquery("SELECT tax_class_id as class FROM ".TABLE_TAX_CLASS);
		while ($zones_data = os_db_fetch_array($zones_query,true)) 
		{
			if (isset($_SESSION['billto']) && isset($_SESSION['sendto'])) 
			{
			    $tax_address_query = os_db_query("select ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $_SESSION['customer_id'] . "' and ab.address_book_id = '" . ($this->content_type == 'virtual' ? $_SESSION['billto'] : $_SESSION['sendto']) . "'");
      		    $tax_address = os_db_fetch_array($tax_address_query);
			    $this->TAX[$zones_data['class']]=os_get_tax_rate($zones_data['class'],$tax_address['entry_country_id'], $tax_address['entry_zone_id']);				
			} 
			else 
			{
			    $this->TAX[$zones_data['class']]=os_get_tax_rate($zones_data['class']);		
			}
		}
		}
		
		else
		{
		   if (isset($default_cache['tax_class_id']) && !empty($default_cache['tax_class_id']))
		   {
		   foreach ($default_cache['tax_class_id'] as $_val => $__val)
		   {
		      $zones_data['class'] = $_val;
			  
		      if (isset($_SESSION['billto']) && isset($_SESSION['sendto'])) 
			  {
			    $tax_address_query = os_db_query("select ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $_SESSION['customer_id'] . "' and ab.address_book_id = '" . ($this->content_type == 'virtual' ? $_SESSION['billto'] : $_SESSION['sendto']) . "'");
      		    $tax_address = os_db_fetch_array($tax_address_query);
			    $this->TAX[$zones_data['class']]=os_get_tax_rate($zones_data['class'],$tax_address['entry_country_id'], $tax_address['entry_zone_id']);				
			  } 
			  else 
			  {
			    $this->TAX[$zones_data['class']]=os_get_tax_rate($zones_data['class']);		
			  }
		   }
		   }
		}

		if ($this->currencies[$this->actualCurr]['symbol_right'])
			$_SESSION['currencySymbol'] = $this->currencies[$this->actualCurr]['symbol_right'];
		elseif ($this->currencies[$this->actualCurr]['symbol_left'])
			$_SESSION['currencySymbol'] = $this->currencies[$this->actualCurr]['symbol_left'];
	}

	/**
	 * Get Price
	 */
	function GetPrice($pID, $format = true, $qty, $tax_class, $pPrice, $vpeStatus = 0, $cedit_id = 0, $discountAllowed = 0) 
	{
		if ($this->cStatus['customers_status_show_price'] == '0')
			return $this->ShowNote($vpeStatus, $vpeStatus);

		if ($cedit_id != 0) 
		{
			$cinfo = os_customer_infos($cedit_id);
			$products_tax = os_get_tax_rate($tax_class, $cinfo['country_id'], $cinfo['zone_id']);
		} 
		else 
		{
			if (isset( $this->TAX[$tax_class]))
				$products_tax = $this->TAX[$tax_class];
			else
				$products_tax = 0;
		}

		if ($this->cStatus['customers_status_show_price_tax'] == '0')
			$products_tax = '';

		if ($pPrice == 0)
			$pPrice = $this->getPprice($pID);

		// Цена с налогом
		$pPrice = $this->AddTax($pPrice, $products_tax);

		// индивидуальная скидка на товар для группы
		$getFormatSpecialGraduated = '';
		if ($this->cStatus['customers_status_graduated_prices'] == '1') 
		{
			if ($sPrice = $this->GetGraduatedPrice($pID, $qty))
			{
				$getPrice = $this->FormatSpecialGraduated($pID, $this->AddTax($sPrice, $products_tax), $pPrice, $format, $vpeStatus, $pID, $discountAllowed);
				$getFormatSpecialGraduated = $getPrice;
			}
		} 
		else 
		{
			if ($sPrice = $this->GetGroupPrice($pID, 1))
			{
				$getPrice = $this->FormatSpecialGraduated($pID, $this->AddTax($sPrice, $products_tax), $pPrice, $format, $vpeStatus, $pID, $discountAllowed);
				$getFormatSpecialGraduated = $getPrice;
			}
		}

		// скидка Специальная на отдельный товар или категорию
		$getFormatSpecial = '';
		if ($sPrice = $this->CheckSpecial($pID)) 
		{
			$getPrice = $this->FormatSpecial($pID, $this->AddTax($sPrice, $products_tax), $pPrice, $format, $vpeStatus);
			$getFormatSpecial = $getPrice;
		}

		// скидка для группы пользователей
		$getFormatSpecialDiscount = '';
		if ($discount = $this->CheckDiscount($pID, $discountAllowed))
		{
			$getPrice = $this->FormatSpecialDiscount($pID, $discount, $pPrice, $format, $vpeStatus);
			$getFormatSpecialDiscount = $getPrice;
		}

		// реальная цена без скидок
		$getDefaultPrice = $this->Format($pPrice, $format, 0, false, $vpeStatus, $pID);

		// цена которая упадет в корзину
		$getPrice = (!empty($getPrice)) ? $getPrice : $getDefaultPrice;

		return array(
			'price' => $getPrice,
			'default' => $getDefaultPrice,
			'special' => $getFormatSpecial,
			'specialGraduated' => $getFormatSpecialGraduated,
			'specialDiscount' => $getFormatSpecialDiscount
		);
	}

	/**
	 * Get Price By Id
	 */
	function getPprice($pID)
	{
		$pQuery = osDBquery("SELECT products_price FROM ".TABLE_PRODUCTS." WHERE products_id='".$pID."'");
		$pData = os_db_fetch_array($pQuery);
		return $pData['products_price'];
	}

	function AddTax($price, $tax) {
		//if (is_array($price)) $sPrice = $price['price'];
		$price = $price + $price / 100 * $tax;
		$price = $this->CalculateCurr($price);
		return round($price, $this->currencies[$this->actualCurr]['decimal_places']);
	}

	function CheckDiscount($pID, $discount = '')
	{
		if ($this->cStatus['customers_status_discount'] != '0.00')
		{
			if ($this->cStatus['customers_status_discount'] < $discount)
				$discount = $this->cStatus['customers_status_discount'];

			if ($discount == '0.00')
				return false;

			return $discount;
		}
		else
			return false;
	}

	function GetGraduatedPrice($pID, $qty) 
	{
	    global $_graduated_price;
	    global $_graduated_personal_offer;
		global $_products_array;
		
		$pID = (int) $pID;
		
		if (empty($pID))
			return;
		
		if (empty($this->actualGroup))
			return;

        $__products_array = $_products_array;
		
	    if (GRADUATED_ASSIGN == 'true')
			if (os_get_qty($pID) > $qty) $qty = os_get_qty($pID);
		
		if (!isset($_graduated_price[$this->actualGroup][$pID][$qty]))
		{
			$sql = '';
			if (!empty($__products_array))
			{
                foreach ($__products_array as $val => $_val)
                {
					if (empty($sql))
						$sql .= $val;
					else
						$sql .= ','.$val;

					$vals[] = $val;            
                }
				
				if (!in_array($pID, $vals))
				{
					if (empty($sql))
						$sql .= $pID;
					else
						$sql .= ','.$pID;

					$vals[] = $pID;
				}

				$sql = 'products_id in ('.$sql.')';
			}
			else
			    $sql = 'products_id in ('.$pID.')';

			$product_query = osDBquery("SELECT products_id, max(quantity) as qty FROM ".TABLE_PERSONAL_OFFERS_BY.$this->actualGroup." WHERE ".$sql ." and quantity<='".$qty."' GROUP BY products_id");

			if (os_db_num_rows($product_query,true)) 
			{
				while ($products = os_db_fetch_array($product_query,true))  
				{
					$_graduated_price[$this->actualGroup][$products['products_id']][$qty] = array('qty' => $products['qty']);
				} 
			}
			else
			{
				$_graduated_price[$this->actualGroup][$pID][$qty] = array('qty' => null);
			}

			if (!empty($vals))
			{
				foreach ($vals as $val)
				{
					if (!isset($_graduated_price[$this->actualGroup][$val][$qty]))
					{
						$_graduated_price[$this->actualGroup][$val][$qty] = array('qty' => null); 
					}					   
				}
			}
		}
		
		$graduated_price_data = $_graduated_price[$this->actualGroup][$pID][$qty];

		if (!empty($graduated_price_data['qty'])) 
		{
			if (!isset($_graduated_personal_offer[$this->actualGroup][$pID][$graduated_price_data['qty']]))
			{
				$graduated_price_query = "SELECT personal_offer FROM ".TABLE_PERSONAL_OFFERS_BY.$this->actualGroup." WHERE products_id='".$pID."' AND quantity='".$graduated_price_data['qty']."'";
				$graduated_price_query = osDBquery($graduated_price_query);
				$_graduated_price_data = os_db_fetch_array($graduated_price_query, true);
				$_graduated_personal_offer[$this->actualGroup][$pID][$graduated_price_data['qty']] = $_graduated_price_data;
			}
			else
			{
				$_graduated_price_data = $_graduated_personal_offer[$this->actualGroup][$pID][$graduated_price_data['qty']];
			}

			$sPrice = $_graduated_price_data['personal_offer'];

			if ($sPrice != 0.00) return $sPrice;
		}
		else
			return;
	}

	function GetGroupPrice($pID, $qty) 
	{
	    global $_graduated_price;
	    global $_graduated_personal_offer;
		global $_products_array;
		
        $pID = (int) $pID;

        $__products_array = $_products_array;

		if (!isset($_graduated_price[$this->actualGroup][$pID][$qty]))
		{
			$sql = '';
			if (!empty($__products_array))
			{
                foreach ($__products_array as $val => $_val)
                {
		           if (empty($sql))  $sql .= $val; else $sql .= ','.$val;
				   $vals[] = $val;            
                }
				
				if (!in_array($pID, $vals))
				{
				     if (empty($sql))  $sql .= $pID; else $sql .= ','.$pID;
					 $vals[] = $pID;
				}
				
				$sql = 'products_id in ('.$sql.')';
			}
			else
			{
			    $sql = 'products_id in ('.$pID.')';
			}

			$product_query = osDBquery("SELECT products_id, max(quantity) as qty FROM ".TABLE_PERSONAL_OFFERS_BY.$this->actualGroup." WHERE ".$sql ." and quantity<='".$qty."' GROUP BY products_id");
				 
	         if (os_db_num_rows($product_query,true)) 
	         {
		           while ($products = os_db_fetch_array($product_query,true))  
                   {
	                    $_graduated_price[$this->actualGroup][$products['products_id']][$qty] = array('qty' => $products['qty']);
                   } 
	         }
		     else
		     {
		          $_graduated_price[$this->actualGroup][$pID][$qty] = array('qty' => null);
		     }
			 
			 if (!empty($vals))
			 {
			    foreach ($vals as $val)
                {
				    if (!isset($_graduated_price[$this->actualGroup][$val][$qty]))
					{
		                $_graduated_price[$this->actualGroup][$val][$qty] = array('qty' => null); 
                    }					   
                }
			 }
		}
		
		$graduated_price_data = $_graduated_price[$this->actualGroup][$pID][$qty];

		if (!empty($graduated_price_data['qty'])) 
		{
		    if (!isset($_graduated_personal_offer[$this->actualGroup][$pID][$graduated_price_data['qty']]))
			{
			   $graduated_price_query = "SELECT personal_offer FROM ".TABLE_PERSONAL_OFFERS_BY.$this->actualGroup." WHERE products_id='".$pID."' AND quantity='".$graduated_price_data['qty']."'";
			   $graduated_price_query = osDBquery($graduated_price_query);
			   $_graduated_price_data = os_db_fetch_array($graduated_price_query, true);
			   $_graduated_personal_offer[$this->actualGroup][$pID][$graduated_price_data['qty']] = $_graduated_price_data;
            }
			else
			{
			   $_graduated_price_data = $_graduated_personal_offer[$this->actualGroup][$pID][$graduated_price_data['qty']];
			}
			
			$sPrice = $_graduated_price_data['personal_offer'];
			
			if ($sPrice != 0.00) return $sPrice;
		} 
		else 
		{
			return;
		}

	}

	function GetOptionPrice($pID, $option, $value) {
		$attribute_price_query = "select pd.products_discount_allowed,pd.products_tax_class_id, p.options_values_price, p.price_prefix, p.options_values_weight, p.weight_prefix from ".TABLE_PRODUCTS_ATTRIBUTES." p, ".TABLE_PRODUCTS." pd where p.products_id = '".$pID."' and p.options_id = '".$option."' and pd.products_id = p.products_id and p.options_values_id = '".$value."'";
		$attribute_price_query = osDBquery($attribute_price_query);
		$attribute_price_data = os_db_fetch_array($attribute_price_query, true);
		$dicount = 0;
		if ($this->cStatus['customers_status_discount_attributes'] == 1 && $this->cStatus['customers_status_discount'] != 0.00) {
			$discount = $this->cStatus['customers_status_discount'];
			if ($attribute_price_data['products_discount_allowed'] < $this->cStatus['customers_status_discount'])
				$discount = $attribute_price_data['products_discount_allowed'];
		}
		$price = $this->Format($attribute_price_data['options_values_price'], false, $attribute_price_data['products_tax_class_id'],true);
		if ($attribute_price_data['weight_prefix'] != '+')
			$attribute_price_data['options_values_weight'] *= -1;
		if ($attribute_price_data['price_prefix'] == '+') {
			$price = $price - $price / 100 * $discount;
		} else {
			$price *= -1;
		}
		return array ('weight' => $attribute_price_data['options_values_weight'], 'price' => $price);
	}

	function ShowNote($vpeStatus, $vpeStatus = 0) {
		if ($vpeStatus == 1)
			return array ('formated' => NOT_ALLOWED_TO_SEE_PRICES, 'plain' => 0);
		return NOT_ALLOWED_TO_SEE_PRICES;
	}

	function CheckSpecial($pID) 
	{
		return get_checkspecial($pID);
		/*$product_query = "select specials_new_products_price from ".TABLE_SPECIALS." where products_id = '".$pID."' and status=1";
		$product_query = osDBquery($product_query);
		$product = os_db_fetch_array($product_query, true);

		return $product['specials_new_products_price'];*/
	}

	function CalculateCurr($price) {
		return $this->currencies[$this->actualCurr]['value'] * $price;
	}

	function calcTax($price, $tax) {
		return $price * $tax / 100;
	}

	function RemoveCurr($price) {

		if (DEFAULT_CURRENCY != $this->actualCurr) {
			return $price * (1 / $this->currencies[$this->actualCurr]['value']);
		} else {
			return $price;
		}

	}

	function RemoveTax($price, $tax) {
		$price = ($price / (($tax +100) / 100));
		return $price;
	}

	function GetTax($price, $tax) {
		$tax = $price - $this->RemoveTax($price, $tax);
		return $tax;
	}
	
	function RemoveDC($price,$dc) {
	
		$price = $price - ($price/100*$dc);
		
		return $price;	
	}
	
	function GetDC($price,$dc) {
		
		$dc = $price/100*$dc;
	
		return $dc;	
	}

	function checkAttributes($pID) {
		if (!$this->showFrom_Attributes) return;
		if ($pID == 0) return;
		
		//$products_attributes_query = "select count(*) as total from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".$pID."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."'";
		//$products_attributes = osDBquery($products_attributes_query);
		//$products_attributes = os_db_fetch_array($products_attributes, true);
		
		$products_attributes = get_check_attributes ($pID);
		if (isset($products_attributes['total']) && $products_attributes['total'] > 0) return ' '.strtolower(FROM).' ';
	}

	function CalculateCurrEx($price, $curr) {
		return $price * ($this->currencies[$curr]['value'] / $this->currencies[$this->actualCurr]['value']);
	}


	function Format($price, $format, $tax_class = 0, $curr = false, $vpeStatus = 0, $pID = 0) {

		if ($curr)
			$price = $this->CalculateCurr($price);

		if ($tax_class != 0)
		{
			$products_tax = $this->TAX[$tax_class];
			if ($this->cStatus['customers_status_show_price_tax'] == '0')
				$products_tax = '';

			$price = $this->AddTax($price, $products_tax);
		}

		if ($format)
		{
			$Pprice = number_format((double)$price, $this->currencies[$this->actualCurr]['decimal_places'], $this->currencies[$this->actualCurr]['decimal_point'], $this->currencies[$this->actualCurr]['thousands_point']);
			//$Pprice = $this->checkAttributes($pID).$this->currencies[$this->actualCurr]['symbol_left'].' <span class="pprice">'.$Pprice.'</span> '.$this->currencies[$this->actualCurr]['symbol_right'];
			$Pprice = $this->checkAttributes($pID).$Pprice;
			if ($vpeStatus == 0) {
				return $Pprice;
			} else {
				return array ('formated' => $Pprice, 'plain' => $price);
			}
		} else {

			return round($price, $this->currencies[$this->actualCurr]['decimal_places']);

		}

	}

	function FormatSpecialDiscount($pID, $discount, $pPrice, $format, $vpeStatus = 0)
	{
		$sPrice = $pPrice - ($pPrice / 100) * $discount;
		if ($format)
		{
			$price = $this->checkAttributes($pID).$this->Format($sPrice, $format);
			if ($vpeStatus == 0)
			{
				return $price;
			}
			else
			{
				return array (
					'formated' => $price,
					'plain' => $sPrice,
					'discount' => $discount
				);
			}
		}
		else
			return round($sPrice, $this->currencies[$this->actualCurr]['decimal_places']);
	}

	function FormatSpecial($pID, $sPrice, $pPrice, $format, $vpeStatus = 0)
	{
		if ($format)
		{
			$price = $this->checkAttributes($pID).$this->Format($sPrice, $format);
			if ($vpeStatus == 0)
			{
				return $price;
			}
			else
			{
				return array ('formated' => $price, 'plain' => $sPrice);
			}
		}
		else
			return round($sPrice, $this->currencies[$this->actualCurr]['decimal_places']);
	}

	function FormatSpecialGraduated($pID, $sPrice, $pPrice, $format, $vpeStatus = 0, $pID, $discount_allowed)
	{
		if ($pPrice == 0)
			return $this->Format($sPrice, $format, 0, false, $vpeStatus);

		if ($discount = $this->CheckDiscount($pID, $discount_allowed))
			$sPrice -= $sPrice / 100 * $discount;

		if ($format)
		{
			if ($sPrice != $pPrice)
				$price = $this->checkAttributes($pID).$this->Format($sPrice, $format);
			else
				$price = $this->Format($sPrice, $format);

			if ($vpeStatus == 0)
			{
				return $price;
			}
			else
			{
				return array (
					'formated' => $price,
					'plain' => $sPrice
				);
			}
		}
		else
			return round($sPrice, $this->currencies[$this->actualCurr]['decimal_places']);
	}

	function get_decimal_places($code) {
		return $this->currencies[$this->actualCurr]['decimal_places'];
	}
	
	//конвертация курсов валют
	/* 
	  $cur_code1 из какой валюты переводить
	  $cur_code2 в какую валюту переводить
	*/
	
	function ConvertCurr ($price, $cur_code1, $cur_code2, $format = false )
    { 

	    if ( isset($this->currencies[$cur_code1]) && isset($this->currencies[$cur_code2]) )
		{
		   //$this->currencies[$this->actualCurr]['value'] * $price
		   if ($cur_code1 == $cur_code2)
		   {
		      return array ('formated' => $this->Format($price, true), 'plain' => $price);
		   }
		   else
		   { 
		      $price = ($price*$this->currencies[$cur_code2]['value'])/$this->currencies[$cur_code1]['value'];
		      return array ('formated' => $this->Format($price, true), 'plain' => $this->Format($price, false));
		   }
		}
		else
		{
	        return array ('formated' => $this->Format($price, true), 'plain' => $price);
		}
	}
}
?>