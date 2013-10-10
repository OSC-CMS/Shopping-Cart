<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiOrders extends CartET
{
	/**
	 * Возвращает товары по ID заказа
	 */
	public function getProducts($params)
	{
		if (empty($params)) return false;

		if (is_array($params))
			$order_id = (is_array($params['orders_id'])) ? " orders_id IN (".implode(',', $params['orders_id']).") " : " orders_id = '".(int)$params['orders_id']."' ";
		else
			$order_id = " orders_id = '".(int)$params."' ";

		$orderProductsQuery = os_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS." WHERE ".$order_id."");

		if (os_db_num_rows($orderProductsQuery) > 0)
		{
			// товары заказа
			while($o = os_db_fetch_array($orderProductsQuery))
			{
				if (isset($params['orders_id']) && is_array($params['orders_id']))
					$aOrders[$o['orders_id']][] = $o;
				else
					$aOrders[] = $o;

				$aOrdersId[] = $o['orders_products_id'];
			}

			// атрибуты
			$attributesQuery = os_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id IN (".implode(',', $aOrdersId).")");
			if (os_db_num_rows($attributesQuery) > 0)
			{
				while ($a = os_db_fetch_array($attributesQuery))
					$aAttributesData[] = $a;
			}

			// Собираем в новый массив
			foreach ($aOrders as $k => $order)
			{
				if (is_array($aAttributesData))
				{
					foreach ($aAttributesData as $attr)
					{
						if ($attr['orders_id'] == $order['orders_id'] && $attr['orders_products_id'] == $order['orders_products_id'])
						{
							$aOrders[$k]['attributes'][] = $attr;
						}
					}
				}
			}
			return $aOrders;
		}
		else
			return false;
	}

	/**
	 * Возвращает итого заказа по ID
	 */
	public function getTotal($oID)
	{
		$orderTotalQuery = os_db_query("SELECT * FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id = '".(int)$oID."' ORDER BY sort_order ASC");

		if (os_db_num_rows($orderTotalQuery) > 0)
		{
			$aOrderTotal = array ();
			while ($o = os_db_fetch_array($orderTotalQuery))
			{
				$aOrderTotal[] = $o;
				if ($o['class'] = 'ot_total')
					$total = $o['value'];
			}

			return array
			(
				'data' => $aOrderTotal,
				'total' => $total
			);
		}
		else
			return false;
	}
}
?>