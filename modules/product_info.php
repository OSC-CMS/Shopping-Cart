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

$info = new osTemplate;
$info->assign('tpl_path', _HTTP_THEMES_C);
$group_check = '';



if (!is_object($product) || !$product->isProduct()) 
{ // product not found in database
	$error = TEXT_PRODUCT_NOT_FOUND;
	include (_MODULES.FILENAME_ERROR_HANDLER);
} 
else 
{
	if (ACTIVATE_NAVIGATOR == 'true')
		include (_MODULES.'product_navigator.php');

	os_db_query("update ".TABLE_PRODUCTS_DESCRIPTION." set products_viewed = products_viewed+1 where products_id = '".$product->data['products_id']."' and language_id = '".$_SESSION['languages_id']."'");

		$products_price = $osPrice->GetPrice($product->data['products_id'], $format = true, 1, $product->data['products_tax_class_id'], $product->data['products_price'], 1, 0, $product->data['products_discount_allowed']);


		if ($_SESSION['customers_status']['customers_status_show_price'] != '0') 
		{
		
		 $_array = array('img' => 'button_in_cart.gif', 
                         'href' => '', 
                         'alt' => IMAGE_BUTTON_IN_CART, 
                         'code' => '');
	
	     $_array = apply_filter('button_in_cart', $_array);
	
	     if (empty($_array['code']))
	     {
			 $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	     }
	
			// fsk18
			if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
				if ($product->data['products_fsk18'] == '0') {
					$info->assign('ADD_QTY', os_draw_input_field('products_qty', '1', 'size="3"').' '.os_draw_hidden_field('products_id', $product->data['products_id']));
					
					$info->assign('ADD_CART_BUTTON', $_array['code']);
				}
			} else {
				$info->assign('ADD_QTY', os_draw_input_field('products_qty', '1', 'size="3"').' '.os_draw_hidden_field('products_id', $product->data['products_id']));
				$info->assign('ADD_CART_BUTTON', $_array['code']);
			}
		}

		if ($product->data['products_fsk18'] == '1') {
			$info->assign('PRODUCTS_FSK18', 'true');
		}
		if (ACTIVATE_SHIPPING_STATUS == 'true') {
			$info->assign('SHIPPING_NAME', $main->getShippingStatusName($product->data['products_shippingtime']));
			$info->assign('SHIPPING_IMAGE', $main->getShippingStatusImage($product->data['products_shippingtime']));
		}
		
		$info->assign('FORM_ACTION', $_fancy_js.os_draw_form('cart_quantity', os_href_link(FILENAME_PRODUCT_INFO, os_get_all_get_params(array ('action')).'action=add_product')));
		$info->assign('FORM_END', '</form>');
		$info->assign('PRODUCTS_PRICE', $products_price['formated']);
		$info->assign('PRODUCTS_PRICE_PLAIN', $products_price['plain']);
		
		if ($product->data['products_vpe_status'] == 1 && $product->data['products_vpe_value'] != 0.0 && $products_price['plain'] > 0)
			$info->assign('PRODUCTS_VPE', $osPrice->Format($products_price['plain'] * (1 / $product->data['products_vpe_value']), true).TXT_PER.os_get_vpe_name($product->data['products_vpe']));
		$info->assign('PRODUCTS_ID', $product->data['products_id']);
		$info->assign('PRODUCTS_NAME', $product->data['products_name']);
		if ($_SESSION['customers_status']['customers_status_show_price'] != 0) {
			// price incl tax
			$tax_rate = $osPrice->TAX[$product->data['products_tax_class_id']];				
			$tax_info = $main->getTaxInfo($tax_rate);
			$info->assign('PRODUCTS_TAX_INFO', $tax_info);
			$info->assign('PRODUCTS_SHIPPING_LINK',$main->getShippingLink());
		}
		$info->assign('PRODUCTS_MODEL', $product->data['products_model']);
		$info->assign('PRODUCTS_EAN', $product->data['products_ean']);
		$info->assign('PRODUCTS_QUANTITY', $product->data['products_quantity']);
		$info->assign('PRODUCTS_STOCK', $product->data['stock']);
		$info->assign('PRODUCTS_WEIGHT', $product->data['products_weight']);
		$info->assign('PRODUCTS_ORDERED', $product->data['products_ordered']);
      $info->assign('PRODUCTS_PRINT', '<img src="'._HTTP_THEMES_C.'buttons/'.$_SESSION['language'].'/print.gif"  style="cursor:pointer" onclick="javascript:window.open(\''.os_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=0, scrollbars=yes, width=640, height=600\')" alt="" />');
		$info->assign('PRODUCTS_DESCRIPTION', stripslashes($product->data['products_description']));
		$image = '';

		$info->assign('ASK_PRODUCT_QUESTION', '<img src="'._HTTP_THEMES_C.'buttons/'.$_SESSION['language'].'/button_ask_a_question.gif" style="cursor:pointer" onclick="javascript:window.open(\''.os_href_link(FILENAME_ASK_PRODUCT_QUESTION, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')" alt="" />');

		if ($product->data['products_keywords'] != '')
		{
			$products_tags = explode (",", $product->data['products_keywords']);

			foreach ($products_tags as $tags)
			{
				$tags_data[] = array(
					'NAME' => trim($tags),
					'LINK' => os_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords='.trim($tags))
				);
			}
			$info->assign('tags_data', $tags_data);
		}
/*$cat_query = osDBquery("SELECT
                                 categories_name
                                 FROM ".TABLE_CATEGORIES_DESCRIPTION." 
                                 WHERE categories_id='".$current_category_id."'
                                 and language_id = '".(int) $_SESSION['languages_id']."'"
                                 );
$cat_data = os_db_fetch_array($cat_query, true);*/
	
$cat_data = get_categories_info ($current_category_id);	

   $manufacturer_query = osDBquery("select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$_SESSION['languages_id'] . "'), " . TABLE_PRODUCTS . " p  where p.products_id = '" . $product->data['products_id'] . "' and p.manufacturers_id = m.manufacturers_id");
      $manufacturer = os_db_fetch_array($manufacturer_query,true);

		$info->assign('CATEGORY', $cat_data['categories_name']);
      $info->assign('MANUFACTURER',$manufacturer['manufacturers_name']);

		if ($product->data['products_image'] != '')
			$image = dir_path('images_info').$product->data['products_image'];
			
       $_check_image = 'true';
	   
	   if (!file_exists($image)) 
	   {
	      $image = http_path('images_info').'../noimage.gif';
		  $_check_image = 'false';
		  $image_pop = '';
	   }
	   else 
	   {
	       $image = http_path('images_info').$product->data['products_image'];
		   $image_pop = http_path('images_popup').$product->data['products_image'];
       }
	   
		$info->assign('PRODUCTS_IMAGE', $image);
		
		if ($_check_image=='true')
		{
		     $_products_image_block = '<a href="'.$image_pop.'" title="'.$product->data['products_name'].'" class="zoom" target="_blank" rel="gallery-plants"><img src="'.$image.'"  alt="'.$product->data['products_name'].'" /></a>';
		}
		else
		{
			 $_products_image_block = '<img src="'.$image.'"  alt="'.$product->data['products_name'].'" />';
		}
		
		$_products_image_block = apply_filter('products_image_block', $_products_image_block);
		$info->assign('PRODUCTS_IMAGE_BLOCK', $_products_image_block);
		
		$info->assign('PRODUCTS_POPUP_IMAGE', $image_pop);
		
		//mo_images - by Novalis@eXanto.de
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
			$connector = '/';
		}else{
			$connector = '&';
		}
		$products_popup_link = os_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID=0');
if (!is_file(dir_path('images_popup').$product->data['products_image'])) $products_popup_link = '';
$info->assign('PRODUCTS_POPUP_LINK', $products_popup_link);

		$mo_images = os_get_products_mo_images($product->data['products_id']);
         
        if ($mo_images != false) 
		{
            $info->assign('PRODUCTS_MO_IMAGES', $mo_images);

            foreach ($mo_images as $img) 
			{
                $products_mo_popup_link = http_path('images_popup') . $img['image_name'];
if (!file_exists(dir_path('images_popup').$img['image_name'])) $products_mo_popup_link = '';

				// moimage text
				if (!empty($img['text']))
					$image_text = $img['text'];
				else
					$image_text = $product->data['products_name'];

                 if ( is_file( dir_path('images_info') . $img['image_name'] ) )
				 {
                 $_PRODUCTS_MO = array(
                'PRODUCTS_MO_IMAGE' => http_path('images_info') . $img['image_name'],
				'PRODUCTS_MO_TEXT' => $image_text,
                'PRODUCTS_MO_POPUP_IMAGE' => $products_mo_popup_link,
                'PRODUCTS_MO_IMAGE_BLOCK' => '<a href="'.$products_mo_popup_link.'" title="'.$image_text.'" class="thumbnail" target="_blank"><img src="'.http_path('images_info') . $img['image_name'].'" alt="'.$image_text.'" /></a>',
				'PRODUCTS_MO_POPUP_LINK' => $products_mo_popup_link);
				
				$_PRODUCTS_MO = apply_filter('products_mo_image_block', $_PRODUCTS_MO);
				 
				 $mo_img[] = $_PRODUCTS_MO;
				}
       
            }
		
			$info->assign('mo_img', $mo_img);
        }
		
		//mo_images EOF
		$discount = 0.00;
		if ($_SESSION['customers_status']['customers_status_public'] == 1 && $_SESSION['customers_status']['customers_status_discount'] != '0.00') 
		{
			$discount = $_SESSION['customers_status']['customers_status_discount'];
			if ($product->data['products_discount_allowed'] < $_SESSION['customers_status']['customers_status_discount'])
				$discount = $product->data['products_discount_allowed'];
			if ($discount != '0.00')
				$info->assign('PRODUCTS_DISCOUNT', $discount.'%');
		}

		include (_MODULES.'product_attributes.php');
		include (_MODULES.'product_reviews.php');

		if (os_not_null($product->data['products_url']))
			$info->assign('PRODUCTS_URL', sprintf(TEXT_MORE_INFORMATION, os_href_link(FILENAME_REDIRECT, 'action=product&id='.$product->data['products_id'], 'NONSSL', true, false)));

		if ($product->data['products_date_available'] > date('Y-m-d H:i:s')) {
			$info->assign('PRODUCTS_DATE_AVIABLE', sprintf(TEXT_DATE_AVAILABLE, os_date_long($product->data['products_date_available'])));

		} else {
			if ($product->data['products_date_added'] != '0000-00-00 00:00:00')
			{
			    $_padd  = sprintf(TEXT_DATE_ADDED, os_date_long($product->data['products_date_added']));
				$_padd = apply_filter('products_added', $_padd);
				$info->assign('PRODUCTS_ADDED', $_padd);
			}

		}

		if ($_SESSION['customers_status']['customers_status_graduated_prices'] == 1)
			include (_MODULES.FILENAME_GRADUATED_PRICE);

                      $extra_fields_query = osDBquery("
                      SELECT pef.products_extra_fields_status as status, pef.products_extra_fields_name as name, ptf.products_extra_fields_value as value
                      FROM ". TABLE_PRODUCTS_EXTRA_FIELDS ." pef
             LEFT JOIN  ". TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS ." ptf
            ON ptf.products_extra_fields_id=pef.products_extra_fields_id
            WHERE ptf.products_id=". $product->data['products_id'] ." and ptf.products_extra_fields_value<>'' and (pef.languages_id='0' or pef.languages_id='".$_SESSION['languages_id']."')
            ORDER BY products_extra_fields_order");

  while ($extra_fields = os_db_fetch_array($extra_fields_query,true)) {
        if (! $extra_fields['status'])  // show only enabled extra field
           continue;
  
  $extra_fields_data[] = array (
  'NAME' => $extra_fields['name'], 
  'VALUE' => $extra_fields['value']
  );
  
  }

  $info->assign('extra_fields_data', $extra_fields_data);

		include(_MODULES.FILENAME_PRODUCTS_MEDIA);
		include(_MODULES.FILENAME_ALSO_PURCHASED_PRODUCTS);
		include(_MODULES.FILENAME_CROSS_SELLING);
	
	if ($product->data['product_template'] == '' or $product->data['product_template'] == 'default') 
	{
		$files = array ();
		if ($dir = opendir(_THEMES_C.'module/product_info/')) 
		{
			while ($file = readdir($dir)) 
			{
				if (is_file(_THEMES_C.'module/product_info/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) 
				{
					$files[] = $file;
				} //if
			} // while
			
			sort($files);
			closedir($dir);
		}
		$product->data['product_template'] = $files[0];
	}

$i = count($_SESSION['tracking']['products_history']);
	if ($i > 6) {
		array_shift($_SESSION['tracking']['products_history']);
		$_SESSION['tracking']['products_history'][6] = $product->data['products_id'];
		$_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
	} else {
		$_SESSION['tracking']['products_history'][$i] = $product->data['products_id'];
		$_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
	}
	
	$info->assign('language', $_SESSION['language']);
	
		//plugins
	if (isset($os_action['products_info']) && !empty($os_action['products_info']))
	{
	   foreach ($os_action['products_info'] as $_info => $_pr)
	   {
	      if (function_exists($_info))
		  {
		  	 $p->name = $os_action_plug[$_info];	
			 $p->group = $p->info[$p->name]['group'];
			 $p->set_dir();
			 
		     $_products_info_val = $_info();
			 
			 if (isset($_products_info_val['name']) && $_products_info_val['value'])
			 {
			     $info->assign($_products_info_val['name'] , $_products_info_val['value']);
			 }
		  }
	   }
	}
	//---////plugins
	// set cache ID
	
	if (!CacheCheck()) 
	{
		$info->caching = 0;
		$product_info = $info->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product->data['product_template']);
	} 
	else 
	{
		$info->caching = 1;
		$info->cache_lifetime = CACHE_LIFETIME;
		$info->cache_modified_check = CACHE_CHECK;
		$cache_id = $product->data['products_id'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		$product_info = $info->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product->data['product_template'], $cache_id);
	}

}
$osTemplate->assign('main_content', $product_info);
?>