<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class shoppingCart {
	var $contents, $total, $weight, $cartID, $content_type, $_products, $cartInfo;

	function shoppingCart() {
		$this->reset();
		$this->getCartInfo();
	}

	function restore_contents() {

		if (!isset ($_SESSION['customer_id']))
			return false;

		if (is_array($this->contents)) {
			reset($this->contents);
			while (list ($products_id,) = each($this->contents)) {
				$qty = $this->contents[$products_id]['qty'];
				$product_query = os_db_query("select products_id from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$products_id."'");
				if (!os_db_num_rows($product_query)) {
					os_db_query("insert into ".TABLE_CUSTOMERS_BASKET." (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('".$_SESSION['customer_id']."', '".$products_id."', '".$qty."', '".date('Ymd')."')");
					if (isset ($this->contents[$products_id]['attributes'])) {
						reset($this->contents[$products_id]['attributes']);
						while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {
						           $attr_value = $this->contents[$products_id]['attributes_values'][$option];

							os_db_query("insert into ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." (customers_id, products_id, products_options_id, products_options_value_id, products_options_value_text) values ('".$_SESSION['customer_id']."', '".$products_id."', '".$option."', '".$value."', '" . os_db_input($attr_value) . "')");
						}
					}
				} else {
					os_db_query("update ".TABLE_CUSTOMERS_BASKET." set customers_basket_quantity = customers_basket_quantity+'".$qty."' where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$products_id."'");
				}
			}
		}

		$this->reset(false);

		$products_query = os_db_query("select products_id, customers_basket_quantity from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$_SESSION['customer_id']."'");
		while ($products = os_db_fetch_array($products_query)) {
			$this->contents[$products['products_id']] = array ('qty' => $products['customers_basket_quantity']);
			$attributes_query = os_db_query("select products_options_id, products_options_value_id, products_options_value_text from ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$products['products_id']."'");
			while ($attributes = os_db_fetch_array($attributes_query)) {
				$this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
				if ($attributes['products_options_value_text']!='') {
                                                $this->contents[$products['products_id']]['attributes_values'][$attributes['products_options_id']] = $attributes['products_options_value_text'];
                                            }
			}
		}

		$this->cleanup();
	}

	function reset($reset_database = false) {

		$this->contents = array ();
		$this->total = 0;
		$this->weight = 0;
		$this->content_type = false;

		if (isset ($_SESSION['customer_id']) && ($reset_database == true)) {
			os_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$_SESSION['customer_id']."'");
			os_db_query("delete from ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." where customers_id = '".$_SESSION['customer_id']."'");
		}

		unset ($this->cartID);
		if (isset ($_SESSION['cartID']))
			unset ($_SESSION['cartID']);
	}

	function add_cart($products_id, $qty = '1', $attributes = '', $notify = true) {
		global $new_products_id_in_cart;
       
	   do_action('add_cart');
	   
		$products_id = os_get_uprid($products_id, $attributes);
		
		if ($notify == true) {
			$_SESSION['new_products_id_in_cart'] = $products_id;
		}

		if ($this->in_cart($products_id)) {
			$this->update_quantity($products_id, $qty, $attributes);
		} else {
			$this->contents[] = array ($products_id);
			$this->contents[$products_id] = array ('qty' => $qty);
			if (isset ($_SESSION['customer_id']))
				os_db_query("insert into ".TABLE_CUSTOMERS_BASKET." (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('".$_SESSION['customer_id']."', '".$products_id."', '".$qty."', '".date('Ymd')."')");

			if (is_array($attributes)) {
				reset($attributes);
				while (list ($option, $value) = each($attributes)) {

             $attr_value = NULL;
            $blank_value = FALSE;
            if (strstr($option, 'txt_')) {
              if (trim($value) == NULL)
              {
                $blank_value = TRUE;
              } else {
                $option_1 = substr($option, strlen('txt_'));
                $option_2 = preg_split('/_/', $option_1);
                $option = $option_2[0];
                $attr_value = htmlspecialchars(stripslashes($value), ENT_QUOTES);
                $value = $option_2[1];
                $this->contents[$products_id]['attributes_values'][$option] = String_RusCharsDeCode($attr_value);
              }
            }

			if (!$blank_value)
            {
					$this->contents[$products_id]['attributes'][$option] = $value;
					if (isset ($_SESSION['customer_id']))
						os_db_query("insert into ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." (customers_id, products_id, products_options_id, products_options_value_id, products_options_value_text) values ('".$_SESSION['customer_id']."', '".$products_id."', '".$option."', '".$value."', '" . os_db_input($attr_value) . "')");
				}
				}
			}
		}
		$this->cleanup();

		$this->cartID = $this->generate_cart_id();
		
	}

	function update_quantity($products_id, $quantity = '', $attributes = '') {

		if (empty ($quantity))
			return true; 

		$this->contents[$products_id] = array ('qty' => $quantity);
		if (isset ($_SESSION['customer_id']))
			os_db_query("update ".TABLE_CUSTOMERS_BASKET." set customers_basket_quantity = '".$quantity."' where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$products_id."'");

		if (is_array($attributes)) {
			reset($attributes);
			while (list ($option, $value) = each($attributes)) {

             $attr_value = NULL;
            $blank_value = FALSE;
            if (strstr($option, 'txt_')) {
              if (trim($value) == NULL)
              {
                $blank_value = TRUE;
              } else {
                $option_1 = substr($option, strlen('txt_'));
                $option_2 = preg_split('/_/', $option_1);
                $option = $option_2[0];
                $attr_value = htmlspecialchars(stripslashes($value), ENT_QUOTES);
                $value = $option_2[1];
                $this->contents[$products_id]['attributes_values'][$option] = String_RusCharsDeCode($attr_value);
              }
            }

			if (!$blank_value)
                                    {
				$this->contents[$products_id]['attributes'][$option] = $value;
				if (isset ($_SESSION['customer_id']))
					os_db_query("update ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." set products_options_value_id = '".$value."' where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$products_id."' and products_options_id = '".$option."'");
			}
			}
		}
	}

	function cleanup() {

		$this->_products = array();
		$this->cartInfo = array();
		reset($this->contents);
		while (list ($key,) = each($this->contents)) {
			if (@$this->contents[$key]['qty'] < 1) {
				unset ($this->contents[$key]);
				if (os_session_is_registered('customer_id')) {
					os_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$key."'");
					os_db_query("delete from ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$key."'");
				}
			}
		}
	}

	function count_contents() {
		$total_items = 0;
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list ($products_id,) = each($this->contents)) {
				$total_items += $this->get_quantity($products_id);
			}
		}

		return $total_items;
	}

	function get_quantity($products_id) {
		if (isset ($this->contents[$products_id])) {
			return $this->contents[$products_id]['qty'];
		} else {
			return 0;
		}
	}

	function in_cart($products_id) {
		if (isset ($this->contents[$products_id])) {
			return true;
		} else {
			return false;
		}
	}

	function remove($products_id) 
	{
        do_action('remove_cart');
		
		$this->contents[$products_id]= NULL;
		if (os_session_is_registered('customer_id')) {
			os_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$products_id."'");
			os_db_query("delete from ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." where customers_id = '".$_SESSION['customer_id']."' and products_id = '".$products_id."'");
		}
		$this->cartID = $this->generate_cart_id();   
	}

	function remove_all() {
		$this->reset();
	}

	function get_product_id_list() {
		$product_id_list = '';
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list ($products_id,) = each($this->contents)) {
				$product_id_list .= ', '.$products_id;
			}
		}

		return substr($product_id_list, 2);
	}

	function calculate()
	{
		global $osPrice, $cartet;
		$this->total = 0;
		$this->qty = 0;
		$this->weight = 0;
		$this->tax = array();

		if (!is_array($this->contents))
			return 0;

		reset($this->contents);

		$aProducts = array();
		$aTaxClassIds = array();
		$aAttributes = array();
		while (list ($products_id, $params) = each($this->contents))
		{
			if ($product = get_cart_products_cache(os_get_prid($products_id))) 
			{
				$aTaxClassIds[] = $product['products_tax_class_id'];
				$product['products_quantity'] = $params['qty'];


				if (is_array($params['attributes']) && !empty($params['attributes']))
				{
					$product['attributes'] = $params['attributes'];
					foreach($product['attributes'] AS $option => $value)
					{
						$aAttributes[] = array('products_id' => $product['products_id'], 'option' => $option, 'value' => $value);
					}
				}

				$aProducts[] = $product;
			}
		}

		$getTaxRate = $cartet->price->getTaxRate(array('tax_class_id' => $aTaxClassIds));

		$GetOptionPrice = '';
		if (is_array($aAttributes) && !empty($aAttributes))
		{
			$GetOptionPrice = $cartet->price->GetOptionPrice($aAttributes);
		}

		$aProducts = apply_filter('cart_calculate', $aProducts);

		foreach($aProducts AS $product)
		{
			$qty = $product['products_quantity'];

			$products_price = $osPrice->GetPrice($product['products_id'], false, $qty, $product['products_tax_class_id'], $product['products_price'], 0, 0, $product['products_discount_allowed']);

			$this->total += $products_price['price'] * $qty;
			$this->qty += $qty;
			$this->weight += ($qty * $product['products_weight']);

			$attribute_price = 0;
			if (isset($product['attributes']) && !empty($product['attributes']))
			{
				foreach($product['attributes'] AS $option => $value)
				{
					$values = $GetOptionPrice[$product['products_id'].'_'.$option.'_'.$value];
					$this->weight += $values['weight'] * $qty;
					$this->total += $values['price'] * $qty;
					$this->qty += $qty;
					$attribute_price+=$values['price'];
				}
			}

			if ($product['products_tax_class_id'] != 0)
			{
				if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
				{
					$products_price_tax = $products_price['price'] - ($products_price['price'] / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
					$attribute_price_tax = $attribute_price - ($attribute_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
				}

				$products_tax = $osPrice->TAX[$product['products_tax_class_id']];

				$products_tax_description = $getTaxRate[$product['products_tax_class_id']]['taxName'];//os_get_tax_description($product['products_tax_class_id']);

				if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1')
				{
					if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
					{
						$this->tax[$product['products_tax_class_id']]['value'] += ((($products_price_tax+$attribute_price_tax) / (100 + $products_tax)) * $products_tax)*$qty;
						$this->tax[$product['products_tax_class_id']]['desc'] = TAX_ADD_TAX."$products_tax_description";
					}
					else
					{
						$this->tax[$product['products_tax_class_id']]['value'] += ((($products_price['price']+$attribute_price) / (100 + $products_tax)) * $products_tax)*$qty;
						$this->tax[$product['products_tax_class_id']]['desc'] = TAX_ADD_TAX."$products_tax_description";
					}
				}

				if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
				{
					if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
					{
						$this->tax[$product['products_tax_class_id']]['value'] += (($products_price_tax+$attribute_price_tax) / 100) * ($products_tax)*$qty;
						$this->total+=(($products_price_tax+$attribute_price_tax) / 100) * ($products_tax)*$qty;
						$this->tax[$product['products_tax_class_id']]['desc'] = TAX_NO_TAX."$products_tax_description";
					}
					else
					{
						$this->tax[$product['products_tax_class_id']]['value'] += (($products_price['price']+$attribute_price) / 100) * ($products_tax)*$qty;
						$this->total+= (($products_price['price']+$attribute_price) / 100) * ($products_tax)*$qty;
						$this->tax[$product['products_tax_class_id']]['desc'] = TAX_NO_TAX."$products_tax_description";
					}
				}
			}
		}
	}

	function attributes_price($products_id) 
	{
		global $osPrice, $cartet;

		$attributes_price = '';
		$attributes = $this->contents[$products_id]['attributes'];
		if (isset($attributes))
		{
			$aAttributes = array();
			reset($attributes);
			while (list ($option, $value) = each($attributes))
			{
				$aAttributes[] = array('products_id' => $products_id, 'option' => $option, 'value' => $value);
			}

			$GetOptionPrice = '';
			if (is_array($aAttributes) && !empty($aAttributes))
			{
				$GetOptionPrice = $cartet->price->GetOptionPrice($aAttributes);
			}

			foreach ($aAttributes AS $attr)
			{
				$values = $GetOptionPrice[$products_id.'_'.$option.'_'.$value];
				$attributes_price += $values['price'];
			}
		}
		return $attributes_price;
	}

	function get_products() 
	{
		if (isset($this->_products) && !empty($this->_products))
			return $this->_products;

		global $osPrice, $main;

		if (!is_array($this->contents))
			return false;

		$products_array = array ();
		reset($this->contents);
		while (list ($products_id,) = each($this->contents)) {
			if($this->contents[$products_id]['qty'] != 0 || $this->contents[$products_id]['qty'] !='')
			{
			//$products_query = os_db_query("select p.products_id, pd.products_name,p.products_shippingtime, p.products_image, p.products_model, p.products_price, p.products_discount_allowed, p.products_weight, p.products_tax_class_id from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id='".os_get_prid($products_id)."' and pd.products_id = p.products_id and pd.language_id = '".$_SESSION['languages_id']."'");
			
			
			if ($products = get_products_cache(os_get_prid($products_id))) {
				$prid = $products['products_id'];

				$products_price = $osPrice->GetPrice($products['products_id'], false, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price'], 0, 0, $products['products_discount_allowed']);
				$productsPrice = $products_price['price'] + $this->attributes_price($products_id);

				$products_array[] = array (
				
				'id' => $products_id, 
				'name' => $products['products_name'], 
				'model' => $products['products_model'], 
				'image' => $products['products_image'], 
				'price' => $productsPrice, 
				'real_price' => $products_price['price'],
				'quantity' => $this->contents[$products_id]['qty'], 
				'weight' => $products['products_weight'],
				'shipping_time' => $main->getShippingStatusName($products['products_shippingtime']), 
				'final_price' => $productsPrice, 
				'tax_class_id' => $products['products_tax_class_id'], 
				'bundle' => $products['products_bundle'], 
				'attributes' => @$this->contents[$products_id]['attributes'], 
				'attributes_values' => (isset($this->contents[$products_id]['attributes_values']) ? $this->contents[$products_id]['attributes_values'] : '')
				
				);
			}
			}
		}

		$products_array = apply_filter('cart_get_products', $products_array);

		$this->_products = $products_array;
		return $products_array;
	}

	function getCartInfo()
	{
		if (!empty($this->cartInfo))
			return $this->cartInfo;

		$this->calculate();

		$this->cartInfo = array(
			'show_total' => $this->total,
			'show_weight' => $this->weight,
			'show_quantity' => $this->qty,
		);
		
		return $this->cartInfo;
	}

	// TODO: на удаление
	function show_total() {
		$this->calculate();

		return $this->total;
	}

	// TODO: на удаление
	function show_weight() {
		$this->calculate();

		return $this->weight;
	}

	// TODO: на удаление
	function show_quantity() {
		$this->calculate();

		return $this->qty;
	}

	function show_tax($format = true) {
		global $osPrice;
		$this->calculate();
		$output = "";
		$val=0;
		foreach ($this->tax as $key => $value) {
			if ($this->tax[$key]['value'] > 0 ) {
			$output .= $this->tax[$key]['desc'].": ".$osPrice->Format($this->tax[$key]['value'], true)."<br />";
			$val = $this->tax[$key]['value'];
			}
		}
		if ($format) {
		return $output;
		} else {
			return $val;
		}
	}

	function generate_cart_id($length = 5) {
		return os_create_random_value($length, 'digits');
	}

	function get_content_type() {
		$this->content_type = false;

		if ((DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0)) {
			reset($this->contents);
			while (list ($products_id,) = each($this->contents)) {
				if (isset ($this->contents[$products_id]['attributes'])) {
					reset($this->contents[$products_id]['attributes']);
					while (list (, $value) = each($this->contents[$products_id]['attributes'])) {
						$virtual_check_query = os_db_query("select count(*) as total from ".TABLE_PRODUCTS_ATTRIBUTES." pa, ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad where pa.products_id = '".$products_id."' and pa.options_values_id = '".$value."' and pa.products_attributes_id = pad.products_attributes_id");
						$virtual_check = os_db_fetch_array($virtual_check_query);

						if ($virtual_check['total'] > 0) {
							switch ($this->content_type) {
								case 'physical' :
									$this->content_type = 'mixed';
									return $this->content_type;
									break;

								default :
									$this->content_type = 'virtual';
									break;
							}
						} else {
							switch ($this->content_type) {
								case 'virtual' :
									$this->content_type = 'mixed';
									return $this->content_type;
									break;

								default :
									$this->content_type = 'physical';
									break;
							}
						}
					}
				} else {
					switch ($this->content_type) {
						case 'virtual' :
							$this->content_type = 'mixed';
							return $this->content_type;
							break;

						default :
							$this->content_type = 'physical';
							break;
					}
				}
			}
		} else {
			$this->content_type = 'physical';
		}
		return $this->content_type;
	}

	function unserialize($broken) {
		for (reset($broken); $kv = each($broken);) {
			$key = $kv['key'];
			if (gettype($this-> $key) != "user function")
				$this-> $key = $kv['value'];
		}
	}
	// GV Code Start
	// ------------------------ ICW CREDIT CLASS Gift Voucher Addittion-------------------------------Start
	// amend count_contents to show nil contents for shipping
	// as we don't want to quote for 'virtual' item
	// GLOBAL CONSTANTS if NO_COUNT_ZERO_WEIGHT is true then we don't count any product with a weight
	// which is less than or equal to MINIMUM_WEIGHT
	// otherwise we just don't count gift certificates

	function count_contents_virtual() { // get total number of items in cart disregard gift vouchers
		$total_items = 0;
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list ($products_id,) = each($this->contents)) {
				$no_count = false;
				$gv_query = os_db_query("select products_model from ".TABLE_PRODUCTS." where products_id = '".$products_id."'");
				$gv_result = os_db_fetch_array($gv_query);
				if (preg_match('/^GIFT/', $gv_result['products_model'])) {
					$no_count = true;
				}
				if (NO_COUNT_ZERO_WEIGHT == 1) {
					$gv_query = os_db_query("select products_weight from ".TABLE_PRODUCTS." where products_id = '".os_get_prid($products_id)."'");
					$gv_result = os_db_fetch_array($gv_query);
					if ($gv_result['products_weight'] <= MINIMUM_WEIGHT) {
						$no_count = true;
					}
				}
				if (!$no_count)
					$total_items += $this->get_quantity($products_id);
			}
		}
		return $total_items;
	}
	// ------------------------ ICW CREDIT CLASS Gift Voucher Addittion-------------------------------End
	//GV Code End
}
?>