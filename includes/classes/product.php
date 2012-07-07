<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

class product {

	function product($pID = 0) 
	{
		$this->pID = $pID;
		$this->useStandardImage=true;
		$this->standardImage='../noimage.gif';
		if ($pID = 0) {
			$this->isProduct = false;
			return;
		}

		$group_check = "";
		if (GROUP_CHECK == 'true') {
			$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		$fsk_lock = "";
		if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
			$fsk_lock = ' and p.products_fsk18!=1';
		}

		$product_query = "select * FROM ".TABLE_PRODUCTS." p,
										                                      ".TABLE_PRODUCTS_DESCRIPTION." pd
										                                      where p.products_status = '1'
										                                      and p.products_id = '".$this->pID."'
										                                      and pd.products_id = p.products_id
										                                      ".$group_check.$fsk_lock."
										                                      and pd.language_id = '".(int) $_SESSION['languages_id']."'";

		$product_query = osDBquery($product_query);

		if (!os_db_num_rows($product_query, true)) {
			$this->isProduct = false;
		} else {
			$this->isProduct = true;
			$this->data = os_db_fetch_array($product_query, true);
		}

	}

	function getAttributesCount() 
	{

		$products_attributes_query = osDBquery("select count(*) as total from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".$this->pID."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."'");
		$products_attributes = os_db_fetch_array($products_attributes_query, true);
		return $products_attributes['total'];

	}

	function getReviewsCount() 
	{
		$reviews_query = osDBquery("select count(*) as total from ".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd where r.products_id = '".$this->pID."' and r.status = 1 and r.reviews_id = rd.reviews_id and rd.languages_id = '".$_SESSION['languages_id']."' and rd.reviews_text !=''");
		$reviews = os_db_fetch_array($reviews_query, true);
		return $reviews['total'];
	}


	function getReviews() {

		$data_reviews = array ();

		$reviews_query = osDBquery("select
									                                 r.reviews_rating,
									                                 r.reviews_id,
									                                 r.customers_name,
									                                 r.date_added,
									                                 r.last_modified,
									                                 r.reviews_read,
									                                 r.status, 
									                                 rd.reviews_text
									                                 from ".TABLE_REVIEWS." r,
									                                 ".TABLE_REVIEWS_DESCRIPTION." rd
									                                 where r.products_id = '".$this->pID."'
									                                 and  r.reviews_id=rd.reviews_id
									                                 and r.status = 1
									                                 and rd.languages_id = '".$_SESSION['languages_id']."'
									                                 order by reviews_id DESC");
		if (os_db_num_rows($reviews_query, true)) {
			$row = 0;
			$data_reviews = array ();
			while ($reviews = os_db_fetch_array($reviews_query, true)) {
				$row ++;
				$data_reviews[] = array ('AUTHOR' => $reviews['customers_name'], 'DATE' => os_date_short($reviews['date_added']), 'RATING' => os_image('themes/'.CURRENT_TEMPLATE.'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])), 'TEXT' => os_break_string(nl2br(htmlspecialchars($reviews['reviews_text'])), 60, '-<br />'));
				if ($row == PRODUCT_REVIEWS_VIEW)
					break;
			}
		}
		return $data_reviews;

	}


	function getBreadcrumbModel() 
	{

		if ($this->data['products_model'] != "")
			return $this->data['products_model'];
		return $this->data['products_name'];

	}

	function getBreadcrumbName() {

		return $this->data['products_name'];

	}

	function getAlsoPurchased() {
		global $osPrice;

		$module_content = array ();

		$fsk_lock = "";
		if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
			$fsk_lock = ' and p.products_fsk18!=1';
		}
		$group_check = "";
		if (GROUP_CHECK == 'true') {
			$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		$orders_query = "select
														                                  p.products_fsk18,
														                                  p.products_id,
														                                  p.products_price,
														                                  p.products_tax_class_id,
														                                  p.products_image,
														                                  pd.products_name,
														                                  p.products_vpe,
						                           										  p.products_vpe_status,
						                           										  p.products_vpe_value,
														                                  pd.products_short_description FROM ".TABLE_ORDERS_PRODUCTS." opa, ".TABLE_ORDERS_PRODUCTS." opb, ".TABLE_ORDERS." o, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
														                                  where opa.products_id = '".$this->pID."'
														                                  and opa.orders_id = opb.orders_id
														                                  and opb.products_id != '".$this->pID."'
														                                  and opb.products_id = p.products_id
														                                  and opb.orders_id = o.orders_id
														                                  and p.products_status = '1'
														                                  and pd.language_id = '".(int) $_SESSION['languages_id']."'
														                                  and opb.products_id = pd.products_id
														                                  ".$group_check."
														                                  ".$fsk_lock."
														                                  group by p.products_id order by o.date_purchased desc limit ".MAX_DISPLAY_ALSO_PURCHASED;
		$orders_query = osDBquery($orders_query);
		while ($orders = os_db_fetch_array($orders_query, true)) {

			$module_content[] = $this->buildDataArray($orders);

		}

		return $module_content;

	}

	function getCrossSells() {
		global $osPrice;

		$cs_groups = "SELECT products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." WHERE products_id = '".$this->pID."' GROUP BY products_xsell_grp_name_id";
		$cs_groups = osDBquery($cs_groups);
		$cross_sell_data = array ();
		if (os_db_num_rows($cs_groups, true)>0) {
		while ($cross_sells = os_db_fetch_array($cs_groups, true)) {

			$fsk_lock = '';
			if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
				$fsk_lock = ' and p.products_fsk18!=1';
			}
			$group_check = "";
			if (GROUP_CHECK == 'true') {
				$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
			}

				$cross_query = "select p.products_fsk18,
																														 p.products_tax_class_id,
																								                                                 p.products_id,
																								                                                 p.products_image,
																								                                                 pd.products_name,
																														 						pd.products_short_description,
																								                                                 p.products_fsk18,p.products_price,p.products_vpe,
						                           																									p.products_vpe_status,
						                           																									p.products_vpe_value,
																								                                                 xp.sort_order from ".TABLE_PRODUCTS_XSELL." xp, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
																								                                            where xp.products_id = '".$this->pID."' and xp.xsell_id = p.products_id ".$fsk_lock.$group_check."
																								                                            and p.products_id = pd.products_id and xp.products_xsell_grp_name_id='".$cross_sells['products_xsell_grp_name_id']."'
																								                                            and pd.language_id = '".$_SESSION['languages_id']."'
																								                                            and p.products_status = '1'
																								                                            order by xp.sort_order asc";

			$cross_query = osDBquery($cross_query);
			if (os_db_num_rows($cross_query, true) > 0)
				$cross_sell_data[$cross_sells['products_xsell_grp_name_id']] = array ('GROUP' => os_get_cross_sell_name($cross_sells['products_xsell_grp_name_id']), 'PRODUCTS' => array ());

			while ($xsell = os_db_fetch_array($cross_query, true)) {

				$cross_sell_data[$cross_sells['products_xsell_grp_name_id']]['PRODUCTS'][] = $this->buildDataArray($xsell);
			}

		}
		return $cross_sell_data;
		}
	}
	
	 
	 function getReverseCrossSells() {
	 			global $osPrice;


			$fsk_lock = '';
			if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
				$fsk_lock = ' and p.products_fsk18!=1';
			}
			$group_check = "";
			if (GROUP_CHECK == 'true') {
				$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
			}

			$cross_query = osDBquery("select p.products_fsk18,
																						 p.products_tax_class_id,
																                                                 p.products_id,
																                                                 p.products_image,
																                                                 pd.products_name,
																						 						pd.products_short_description,
																                                                 p.products_fsk18,p.products_price,p.products_vpe,
						                           																p.products_vpe_status,
						                           																p.products_vpe_value,  
																                                                 xp.sort_order from ".TABLE_PRODUCTS_XSELL." xp, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
																                                            where xp.xsell_id = '".$this->pID."' and xp.products_id = p.products_id ".$fsk_lock.$group_check."
																                                            and p.products_id = pd.products_id
																                                            and pd.language_id = '".$_SESSION['languages_id']."'
																                                            and p.products_status = '1'
																                                            order by xp.sort_order asc");


			while ($xsell = os_db_fetch_array($cross_query, true)) {

				$cross_sell_data[] = $this->buildDataArray($xsell);
			}


		return $cross_sell_data;
	 	
	 	
	 	
	 }
	

	function getGraduated() {
		global $osPrice;

		$staffel_query = osDBquery("SELECT
				                                     quantity,
				                                     personal_offer
				                                     FROM
				                                     ".TABLE_PERSONAL_OFFERS_BY.(int) $_SESSION['customers_status']['customers_status_id']."
				                                     WHERE
				                                     products_id = '".$this->pID."'
				                                     ORDER BY quantity ASC");

		$staffel = array ();
		while ($staffel_values = os_db_fetch_array($staffel_query, true)) {
			$staffel[] = array ('stk' => $staffel_values['quantity'], 'price' => $staffel_values['personal_offer']);
		}
		$staffel_data = array ();
		for ($i = 0, $n = sizeof($staffel); $i < $n; $i ++) {
			if ($staffel[$i]['stk'] == 1) {
				$quantity = $staffel[$i]['stk'];
				if ($staffel[$i +1]['stk'] != '')
					$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
			} else {
				$quantity = ' > '.$staffel[$i]['stk'];
				if ($staffel[$i +1]['stk'] != '')
					$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
			}
			$vpe = '';
			if ($product_info['products_vpe_status'] == 1 && $product_info['products_vpe_value'] != 0.0 && $staffel[$i]['price'] > 0) {
				$vpe = $staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount;
				$vpe = $vpe * (1 / $product_info['products_vpe_value']);
				$vpe = $osPrice->Format($vpe, true, $product_info['products_tax_class_id']).TXT_PER.os_get_vpe_name($product_info['products_vpe']);
			}
			$staffel_data[$i] = array ('QUANTITY' => $quantity, 'VPE' => $vpe, 'PRICE' => $osPrice->Format($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $this->data['products_tax_class_id']));
		}

		return $staffel_data;

	}


	function isProduct() {
		return $this->isProduct;
	}
	
	function getBuyNowButton($id, $name) 
	{
		global $PHP_SELF;

		$_array = array(
			'img'	=> 'button_buy_now.gif',
			'href'	=> os_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id='.$id.'&'.os_get_all_get_params(array ('action')), 'NONSSL'),
			'alt'	=> TEXT_BUTTON_BUY." '".$name."'",
			'code'	=> ''
		);

		$_array = apply_filter('button_buy_now', $_array);	

		if (empty($_array['code']))
		{
			$_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
		}

		return $_array['code'];
	}

	function getBuyNowButtonNew($id, $name) 
	{
		global $PHP_SELF;

		$_array = array(
			'img'	=> 'cart_big.gif', 
			'href'	=> os_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id='.$id.'&'.os_get_all_get_params(array ('action')), 'NONSSL'), 
			'alt'	=> TEXT_BUTTON_IN_CART, 
			'code'	=> ''
		);

		$_array = apply_filter('button_cart_big', $_array);	

		if (empty($_array['code']))
		{
			$_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
		}

		return $_array['code'];
	}

	function getVPEtext($product, $price) {
		global $osPrice;
		
		if (!is_array($product))
			$product = $this->data;

		if ($product['products_vpe_status'] == 1 && $product['products_vpe_value'] != 0.0 && $price > 0) {
			return $osPrice->Format($price * (1 / $product['products_vpe_value']), true).TXT_PER.os_get_vpe_name($product['products_vpe']);
		}

		return;

	}
	
	function buildDataArray(&$array, $image='thumbnail') 
    {
		global $osPrice, $main;
            $buy_now = '';
			$buy_now_new = '';
			$tax_rate = '';
			
		    if (isset($osPrice->TAX[$array['products_tax_class_id']]))
			{
			    $tax_rate = $osPrice->TAX[$array['products_tax_class_id']];
			}
	
    		$products_price = $osPrice->GetPrice($array['products_id'], $format = true, 1, $array['products_tax_class_id'], $array['products_price'], 1, 0, $array['products_discount_allowed']);

			if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
			if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
				if (isset($array['products_fsk18']) && $array['products_fsk18'] == '0') { 
					$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
					$buy_now_new = $this->getBuyNowButtonNew($array['products_id'], $array['products_name']); 
			 } 
			} else {
				$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
				$buy_now_new = $this->getBuyNowButtonNew($array['products_id'], $array['products_name']);
			}
			
   	 }

		
			$shipping_status_name = @$main->getShippingStatusName($array['products_shippingtime']);
			$shipping_status_image = @$main->getShippingStatusImage($array['products_shippingtime']);
		
	
		
		return apply_filter ('build_products', array ('PRODUCTS_NAME' => @$array['products_name'], 
		      'PRODUCTS_MODEL'=> @$array['products_model'],
		      'PRODUCTS_QUANTITY'=> @$array['products_quantity'],
				'COUNT'=> @$array['ID'],
				'PRODUCTS_ID'=> @$array['products_id'],
				'PRODUCTS_STOCK'=> @$array['stock'],
				'PRODUCTS_VPE' => @$this->getVPEtext($array, $products_price['plain']), 
				'PRODUCTS_IMAGE' => @$this->productImage($array['products_image'], $image), 
				'PRODUCTS_LINK' => os_href_link(FILENAME_PRODUCT_INFO, os_product_link($array['products_id'], $array['products_name'])), 
				'PRODUCTS_PRICE' => @$products_price['formated'], 
				'PRODUCTS_PRICE_PLAIN' => @$products_price['plain'], 
				'PRODUCTS_TAX_INFO' => @$main->getTaxInfo($tax_rate), 
				'PRODUCTS_SHIPPING_LINK' => @$main->getShippingLink(), 
				'PRODUCTS_BUTTON_BUY_NOW' => @$buy_now,
				'PRODUCTS_BUTTON_BUY_NOW_NEW' => @$buy_now_new,
				'PRODUCTS_SHIPPING_NAME'=> @$shipping_status_name,
				'PRODUCTS_SHIPPING_IMAGE'=> @$shipping_status_image, 
				'PRODUCTS_DESCRIPTION' => @$array['products_description'],
				'PRODUCTS_EXPIRES' => @$array['expires_date'],
				'PRODUCTS_CATEGORY_URL'=> @$array['cat_url'],
				'PRODUCTS_SHORT_DESCRIPTION' => @$array['products_short_description'], 
				'PRODUCTS_FSK18' => @$array['products_fsk18'],
				'PRODUCTS_MANUFACTURER_NAME' => @$array['manufacturers_name'],
				'PRODUCTS_MANUFACTURER_ID' => @$array['manufacturers_id'])
				);		
				

	}
	

	function productImage($name, $type) 
	{

		switch ($type) 
		{
			case 'info' :
				$path = dir_path('images_info');
				$http_path = http_path('images_info');
				break;
			case 'thumbnail' :
				$path = dir_path('images_thumbnail');
				$http_path = http_path('images_thumbnail');
				break;
			case 'popup' :
				$path = dir_path('images_popup');
				$http_path = http_path('images_popup');
				break;
		}

		if ($name == '') 
		{
			if ($this->useStandardImage == 'true' && $this->standardImage != '')
				return $http_path.$this->standardImage;
		} else {
			if (!file_exists($path.$name)) {
				if ($this->useStandardImage == 'true' && $this->standardImage != '')
					$name = $this->standardImage;
			}
			return $http_path.$name;
		}
	}
	
	//удаление товаров по id
	function remove ($_array)
	{
	   if (!empty($_array))
	   {
	       $sql = '';
		   
	       foreach ($_array as $_id)
		   {
		      $_id =  (int)$_id;
			  
			  if (empty($sql)) $sql .= $_id; else $sql .= ','.$_id;            
		   }
		   
		   $sql = 'products_id in ('.$sql.')';
		   
		   os_db_query('DELETE FROM '.TABLE_PRODUCTS.' WHERE '. $sql);
	   }
	}
	
}
?>