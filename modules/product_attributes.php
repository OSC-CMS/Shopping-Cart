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

$module = new osTemplate;


if ($product->getAttributesCount() > 0) {
	$products_options_name_query = osDBquery("select distinct popt.products_options_id, popt.products_options_name,popt.products_options_type,popt.products_options_length,popt.products_options_rows,popt.products_options_size from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".$product->data['products_id']."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."' order by popt.products_options_name");

	$row = 0;
	$col = 0;
	$products_options_data = array ();
	while ($products_options_name = os_db_fetch_array($products_options_name_query,true)) {
		$selected = 0;
		$products_options_array = array ();

		$products_options_data[$row] = array (
		
		'NAME' => $products_options_name['products_options_name'],
		'TYPE'=>$products_options_name['products_options_type'],
		'ROWS'=>$products_options_name['products_options_rows'],
		'LENGTH'=>$products_options_name['products_options_length'],
		'SIZE'=>$products_options_name['products_options_size'], 
		'ID' => $products_options_name['products_options_id'], 
		'DATA' => ''
		
		);

		$products_options_query = osDBquery("select pov.products_options_values_id,
		                                                 pov.products_options_values_name,
		                                                 pov.products_options_values_description,
		                                                 pov.products_options_values_text,
		                                                 pov.products_options_values_image,
		                                                 pov.products_options_values_link,
		                                                 pa.attributes_model,
		                                                 pa.options_values_price,
		                                                 pa.price_prefix,
		                                                 pa.attributes_stock,
		                                                 pa.attributes_model
		                                                 from ".TABLE_PRODUCTS_ATTRIBUTES." pa,
		                                                 ".TABLE_PRODUCTS_OPTIONS_VALUES." pov
		                                                 where pa.products_id = '".$product->data['products_id']."'
		                                                 and pa.options_id = '".$products_options_name['products_options_id']."'
		                                                 and pa.options_values_id = pov.products_options_values_id
		                                                 and pov.language_id = '".(int) $_SESSION['languages_id']."'
		                                                 order by pa.sortorder");
		$col = 0;
		while ($products_options = os_db_fetch_array($products_options_query,true)) {
			$price = '';
			if ($_SESSION['customers_status']['customers_status_show_price'] == '0') {
				$products_options_data[$row]['DATA'][$col] = array (
				
				'ID' => $products_options['products_options_values_id'], 
				'TEXT' => $products_options['products_options_values_name'],
				'DESCRIPTION' => $products_options['products_options_values_description'], 
				'SHORT_DESCRIPTION' => $products_options['products_options_values_text'], 
				'IMAGE' => $products_options['products_options_values_image'], 
				'LINK' => $products_options['products_options_values_link'], 
				'MODEL' => $products_options['attributes_model'], 
				'STOCK' => $products_options['attributes_stock'], 
				'PRICE' => '', 
				'FULL_PRICE' => '', 
				'PREFIX' => $products_options['price_prefix']
				
				);
			
				$price = '';
				$full_price = '';
			} else {
				if ($products_options['options_values_price'] != '0.00') {
//					$price = $osPrice->Format($products_options['options_values_price'], false, $product->data['products_tax_class_id']);
					$price = $osPrice->GetOptionPrice($product->data['products_id'], $products_options_name['products_options_id'], $products_options['products_options_values_id']);
					$price = $price['price'];
				}
				$products_price = $osPrice->GetPrice($product->data['products_id'], $format = false, 1, $product->data['products_tax_class_id'], $product->data['products_price']);
				if ($_SESSION['customers_status']['customers_status_discount_attributes'] == 1 && $products_options['price_prefix'] == '+')
					$price -= $price / 100 * $discount;				
					$attr_price=$price;
					if ($products_options['price_prefix']=="-") $attr_price=$price*(-1);
					$full_price = $products_price + $attr_price;
					$price_plain = $osPrice->Format($price, false);
					$price = $osPrice->Format($price, true);
					$full_price = $osPrice->Format($full_price, true);
			}
			
			$products_options_data[$row]['DATA'][$col] = array (
			
			'ID' => $products_options['products_options_values_id'], 
			'TEXT' => $products_options['products_options_values_name'],
			'DESCRIPTION' => $products_options['products_options_values_description'], 
			'SHORT_DESCRIPTION' => $products_options['products_options_values_text'], 
			'IMAGE' => $products_options['products_options_values_image'], 
			'LINK' => $products_options['products_options_values_link'], 
			'MODEL' => $products_options['attributes_model'], 
			'STOCK' => $products_options['attributes_stock'], 
			'PRICE' => $price, 
			'PRICE_PLAIN' => $price_plain, 
			'FULL_PRICE' => $full_price, 'PREFIX' => $products_options['price_prefix']
			
			);
			
			
			$col ++;
		}
		$row ++;
	}

}

if ($product->data['options_template'] == '' or $product->data['options_template'] == 'default') {
	$files = array ();
	if ($dir = opendir(_THEMES_C.'module/product_options/')) {
		while (($file = readdir($dir)) !== false) {
			if (is_file(_THEMES_C.'module/product_options/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
				$files[] = array ('id' => $file, 'text' => $file);
			} //if
		} // while
		closedir($dir);
	}
	$product->data['options_template'] = $files[0]['id'];
}

$module->assign('image_dir', http_path('images').'attribute_images/');
$module->assign('language', $_SESSION['language']);
$module->assign('options', $products_options_data);
// set cache ID

	$module->caching = 0;
	$module = $module->fetch(CURRENT_TEMPLATE.'/module/product_options/'.$product->data['options_template']);

$info->assign('MODULE_product_options', $module);
?>