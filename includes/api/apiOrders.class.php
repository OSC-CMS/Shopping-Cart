<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

class apiOrders extends OscCms
{
	/**
	 * Возвращает информацию о заказе по ID
	 */
	public function getOrderData($oID, $format = true)
	{
		global $osPrice;

		$order_query = os_db_query("SELECT products_id, orders_products_id, products_model, products_name, final_price, products_shipping_time, products_quantity, bundle FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id='".(int) $oID."'");
		$order_data = array ();
		while ($order_data_values = os_db_fetch_array($order_query))
		{
			// attributes
			$attributes_query = os_db_query("SELECT products_options, products_options_values, price_prefix, options_values_price FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id='".$order_data_values['orders_products_id']."'");
			$attributes_data = '';
			$attributes_model = '';
			while ($attributes_data_values = os_db_fetch_array($attributes_query))
			{
				$attributes_data .= '<br />'.$attributes_data_values['products_options'].': '.$attributes_data_values['products_options_values'];
				$attributes_model .= '<br />'.os_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values'],$attributes_data_values['products_options']);
			}

			//Bundle
			$products_bundle_data = '';
			if ($order_data_values['bundle'] == 1)
			{
				$bundle_query = getBundleProducts($order_data_values['products_id']);
				if (os_db_num_rows($bundle_query) > 0)
				{
					while($bundle_data = os_db_fetch_array($bundle_query))
					{
						$products_bundle_data .= $bundle_data['products_name'].'<br />';
					}
				}
			}
			//End of Bundle

			$order_data[] = array
			(
				'PRODUCTS_MODEL' => $order_data_values['products_model'],
				'PRODUCTS_NAME' => $order_data_values['products_name'],
				'PRODUCTS_SHIPPING_TIME' => $order_data_values['products_shipping_time'],
				'PRODUCTS_ATTRIBUTES' => $attributes_data,
				'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model,
				'PRODUCTS_PRICE' => $osPrice->Format($order_data_values['final_price'], $format),
				'PRODUCTS_SINGLE_PRICE' => $osPrice->Format($order_data_values['final_price']/$order_data_values['products_quantity'], $format),
				'PRODUCTS_QTY' => $order_data_values['products_quantity'],
				'PRODUCTS_BUNDLE' => $products_bundle_data
			);
		}

		return $order_data;
	}

}
?>