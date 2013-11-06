<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include (_MODULES.'gift_cart.php');

$module = new osTemplate;

if ($_SESSION['cart']->count_contents() > 0)
{
	$hidden_options = '';
	$_SESSION['any_out_of_stock'] = 0;

	$products = $_SESSION['cart']->get_products();
	for ($i = 0, $n = sizeof($products); $i < $n; $i ++)
	{
		// Push all attributes information in an array
		if (isset ($products[$i]['attributes']))
		{
			while (list ($option, $value) = each($products[$i]['attributes']))
			{
				//$hidden_options .= os_draw_hidden_field('id['.$products[$i]['id'].']['.$option.']', $value);
				$attributes = os_db_query("select popt.products_options_name, popt.products_options_type, poval.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock,pa.products_attributes_id,pa.attributes_model
				from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
				where pa.products_id = '".$products[$i]['id']."'
				and pa.options_id = '".$option."'
				and pa.options_id = popt.products_options_id
				and pa.options_values_id = '".$value."'
				and pa.options_values_id = poval.products_options_values_id
				and popt.language_id = '".(int)$_SESSION['languages_id']."'
				and poval.language_id = '".(int)$_SESSION['languages_id']."'");
				$attributes_values = os_db_fetch_array($attributes);

				if ($attributes_values['products_options_type']=='2' || $attributes_values['products_options_type']=='3')
				{
					$hidden_options .= os_draw_hidden_field('id[' . $products[$i]['id'] . '][txt_' . $option . '_'.$value.']',  $products[$i]['attributes_values'][$option]);
					$attr_value = $products[$i]['attributes_values'][$option];
				}
				else
				{
					$hidden_options .= os_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
					$attr_value = $attributes_values['products_options_values_name'];
				}

				$products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
				$products[$i][$option]['options_values_id'] = $value;
				$products[$i][$option]['products_options_values_name'] = $attr_value;
				$products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
				$products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
				$products[$i][$option]['weight_prefix'] = $attributes_values['weight_prefix'];
				$products[$i][$option]['options_values_weight'] = $attributes_values['options_values_weight'];
				$products[$i][$option]['attributes_stock'] = $attributes_values['attributes_stock'];
				$products[$i][$option]['products_attributes_id'] = $attributes_values['products_attributes_id'];
				$products[$i][$option]['products_attributes_model'] = $attributes_values['products_attributes_model'];
			}
		}
	}

	$module->assign('HIDDEN_OPTIONS', $hidden_options);

	$module_content = array ();
	$any_out_of_stock = '';
	$mark_stock = '';

	for ($i = 0, $n = sizeof($products); $i < $n; $i ++)
	{
		if (STOCK_CHECK == 'true')
		{
			$mark_stock = os_check_stock($products[$i]['id'], $products[$i]['quantity']);
			if ($mark_stock)
				$_SESSION['any_out_of_stock'] = 1;
		}

		$image = '';
		if ($products[$i]['image'] != '')
		{
			$image = dir_path('images_thumbnail').$products[$i]['image'];
		}

		if (!is_file($image))
			$image = http_path('images_thumbnail').'../noimage.gif';
		else
			$image = http_path('images_thumbnail').$products[$i]['image'];

		//Bundle
		$products_bundle = '';
		if ($products[$i]['bundle'] == 1)
		{
			$bundle_query = getBundleProducts($products[$i]['id']);

			if (os_db_num_rows($bundle_query) > 0)
			{
				while($bundle_data = os_db_fetch_array($bundle_query))
				{
					$products_bundle_data .= ' - <a href="'.os_href_link(FILENAME_PRODUCT_INFO, os_product_link($bundle_data['products_id'], $bundle_data['products_name'])).'">'.$bundle_data['products_name'].'</a><br />';
				}
			}
			$products_bundle = (!empty($products_bundle_data)) ? $products_bundle_data : '';
		}
		//End of Bundle

		$productIds = str_replace(array('{','}'), '_', $products[$i]['id']);
		$module_content[$i] = array(
			'PRODUCTS_NAME' => $products[$i]['name'].$mark_stock, 
			'PRODUCTS_QTY' => os_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'onchange="updateShoppingCart()" class="sc_qty_'.$productIds.'"').os_draw_hidden_field('products_id[]', $products[$i]['id']).os_draw_hidden_field('old_qty[]', $products[$i]['quantity']), 
			'PRODUCTS_MODEL' => $products[$i]['model'],
			'PRODUCTS_ID' => $productIds,
			'PRODUCTS_SHIPPING_TIME'=>$products[$i]['shipping_time'], 
			'PRODUCTS_TAX' => @number_format($products[$i]['tax'], TAX_DECIMAL_PLACES), 
			'PRODUCTS_TAX_DESCRIPTION' => $products[$i]['tax_description'], 
			'PRODUCTS_IMAGE' => $image, 
			'IMAGE_ALT' => $products[$i]['name'], 
			'BOX_DELETE' => os_draw_checkbox_field('cart_delete[]', $products[$i]['id'], '', 'onchange="updateShoppingCart()"'), 
			'PRODUCTS_LINK' => os_href_link(FILENAME_PRODUCT_INFO, os_product_link($products[$i]['id'], $products[$i]['name'])), 
			'PRODUCTS_PRICE' => $osPrice->Format($products[$i]['price'] * $products[$i]['quantity'], true), 
			'PRODUCTS_SINGLE_PRICE' =>$osPrice->Format($products[$i]['price'], true), 
			'PRODUCTS_SHORT_DESCRIPTION' => get_short_description_cache($products[$i]['id']), 
			'PRODUCTS_BUNDLE' => $products_bundle,
			'ATTRIBUTES' => '',
		);
		$attributes_exist = ((isset ($products[$i]['attributes'])) ? 1 : 0);

		if ($attributes_exist == 1) 
		{
			if (is_array($products[$i]['attributes'])) 
			{
				reset($products[$i]['attributes']);

				while (list ($option, $value) = each($products[$i]['attributes']))
				{
					if (ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true')
					{
						$attribute_stock_check = os_check_stock_attributes($products[$i][$option]['products_attributes_id'], $products[$i]['quantity']);
						if ($attribute_stock_check)
							$_SESSION['any_out_of_stock'] = 1;
					}

					$module_content[$i]['ATTRIBUTES'][] = array(
						'ID' => $products[$i][$option]['products_attributes_id'],
						'MODEL' => os_get_attributes_model(os_get_prid($products[$i]['id']), $products[$i][$option]['products_options_values_name'],$products[$i][$option]['products_options_name']),
						'NAME' => $products[$i][$option]['products_options_name'],
						'VALUE_NAME' => $products[$i][$option]['products_options_values_name'].$attribute_stock_check
					);
				}
			}
		}
	}

	$module->assign('module_content', $module_content);

	$_array = array(
		'img' => 'button_checkout.gif', 
		'href' => os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'),
		'alt' => IMAGE_BUTTON_CHECKOUT, 'code' => ''
	);

	$_array = apply_filter('button_checkout', $_array);

	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}

	$module->assign('BUTTON_CHECKOUT', $_array['code']);

	$total_content = '';

	$total = $_SESSION['cart']->show_total();
	if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$price = $total-$_SESSION['cart']->show_tax(false);
		} else {
			$price = $total;
		}
		$discount = $osPrice->GetDC($price, $_SESSION['customers_status']['customers_status_ot_discount']);
		$total_content = $_SESSION['customers_status']['customers_status_ot_discount'].' % '.SUB_TITLE_OT_DISCOUNT.' -'.os_format_price($discount, $price_special = 1, $calculate_currencies = false).'<br />';
	}

	if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) $total-=$discount;
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) $total-=$discount;
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) @$total-=$discount;
		$total_content .= SUB_TITLE_SUB_TOTAL.' ' .$osPrice->Format($total, true);
	} else {
		$total_content .= TEXT_INFO_SHOW_PRICE_NO;
	}
	if (@$customer_status_value['customers_status_ot_discount'] != 0) {
		$total_content .= TEXT_CART_OT_DISCOUNT.$customer_status_value['customers_status_ot_discount'].'%';
	}

	$orderLink = os_href_link(FILENAME_SHOPPING_CART, 'action=update_product');

	$module->assign('FORM_ACTION', os_draw_form('form_shopping_cart', $orderLink));
	$module->assign('FORM_END', '</form>');
	$module->assign('UST_CONTENT', $_SESSION['cart']->show_tax());
	$module->assign('TOTAL_CONTENT', $total_content);
	$module->assign('language', $_SESSION['language']);
	if (SHOW_SHIPPING == 'true')
	{
		$module->assign('SHIPPING_INFO', $main->getShippingLink());
	}

	$module->caching = 0;
	$module = $module->fetch(CURRENT_TEMPLATE.'/module/order_details.html');

	$osTemplate->assign('MODULE_order_details', $module);

	$_SESSION['allow_checkout'] = 'true';

	if (STOCK_CHECK == 'true')
	{
		if ($_SESSION['any_out_of_stock'] == 1)
		{
			if (STOCK_ALLOW_CHECKOUT == 'true')
			{
				// write permission in session
				$_SESSION['allow_checkout'] = 'true';

				$osTemplate->assign('info_message', OUT_OF_STOCK_CAN_CHECKOUT);

			}
			else
			{
				$_SESSION['allow_checkout'] = 'false';
				$osTemplate->assign('info_message', OUT_OF_STOCK_CANT_CHECKOUT);
			}
		}
		else
			$_SESSION['allow_checkout'] = 'true';
	}

	// minimum/maximum order value
	$checkout = true;
	if ($_SESSION['cart']->show_total() > 0 )
	{
		if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] )
		{
			$_SESSION['allow_checkout'] = 'false';
			$more_to_buy = $_SESSION['customers_status']['customers_status_min_order'] - $_SESSION['cart']->show_total();
			$order_amount = $osPrice->Format($more_to_buy, true);
			$min_order = $osPrice->Format($_SESSION['customers_status']['customers_status_min_order'], true);
			$osTemplate->assign('info_message_1', MINIMUM_ORDER_VALUE_NOT_REACHED_1);
			$osTemplate->assign('info_message_2', MINIMUM_ORDER_VALUE_NOT_REACHED_2);
			$osTemplate->assign('order_amount', $order_amount);
			$osTemplate->assign('min_order', $min_order);
		}

		if  ($_SESSION['customers_status']['customers_status_max_order'] != 0)
		{
			if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] )
			{
				$_SESSION['allow_checkout'] = 'false';
				$less_to_buy = $_SESSION['cart']->show_total() - $_SESSION['customers_status']['customers_status_max_order'];
				$max_order = $osPrice->Format($_SESSION['customers_status']['customers_status_max_order'], true);
				$order_amount = $osPrice->Format($less_to_buy, true);
				$osTemplate->assign('info_message_1', MAXIMUM_ORDER_VALUE_REACHED_1);
				$osTemplate->assign('info_message_2', MAXIMUM_ORDER_VALUE_REACHED_2);
				$osTemplate->assign('order_amount', $order_amount);
				$osTemplate->assign('min_order', $max_order);
			}
		}
	}
	if (@$_GET['info_message'])
		$osTemplate->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));

}
else
{
	// empty cart
	$cart_empty = true;
	if ($_GET['info_message'])
		$osTemplate->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
	$osTemplate->assign('cart_empty', $cart_empty);
	//$osTemplate->assign('BUTTON_CONTINUE', button_continue());
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/shopping_cart.html');
$osTemplate->assign('main_content', $main_content);
?>