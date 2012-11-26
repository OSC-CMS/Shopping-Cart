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
	public function getOrderData($oID)
	{
		global $osPrice;

		$orderProductsQuery = os_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id = '".(int)$oID."'");

		if (os_db_num_rows($orderProductsQuery) > 0)
		{
			// товары заказа
			while($o = os_db_fetch_array($orderProductsQuery))
			{
				$aOrders[] = $o;
				$aOrdersId[] = $o['orders_products_id'];
			}

			// атрибуты
			$attributesQuery = os_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id IN (".implode(',', $aOrdersId).")");
			if (os_db_num_rows($attributesQuery) > 0)
			{
				while ($a = os_db_fetch_array($attributesQuery))
				{
					$aAttributesData[] = $a;
				}
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
	public function getTotalData($oID)
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


public function confirmation()
{
	// скидка
	if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
		$discount = $_SESSION['customers_status']['customers_status_ot_discount'];
	else
		$discount = '0.00';

	// ip покупателя
	if ($_SERVER["HTTP_X_FORWARDED_FOR"])
		$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else
		$customers_ip = $_SERVER["REMOTE_ADDR"];









}
















}
?>