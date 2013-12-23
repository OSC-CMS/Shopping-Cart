<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiPrice extends CartET
{
	/**
	 * Получение цены и веса атрибута
	 */
	public function GetOptionPrice($params = array())
	{
		if (empty($params)) return false;

		global $osPrice;

		$sql = '';
		$i = 0;
		foreach($params AS $item)
		{
			$i++;
			if ($i > 1) $sql .= ' OR ';

			$sql .= " (pd.products_id = p.products_id AND p.products_id = '".(int)$item['products_id']."' AND p.options_id = '".(int)$item['option']."' AND p.options_values_id = '".(int)$item['value']."') ";
		}

		$attribute_price_query = "
			SELECT 
				pd.products_id, pd.products_discount_allowed, pd.products_tax_class_id, p.options_values_price, 
				p.price_prefix, p.options_values_weight, p.weight_prefix, p.options_id, p.options_values_id 
			FROM 
				".TABLE_PRODUCTS_ATTRIBUTES." p, ".TABLE_PRODUCTS." pd 
			WHERE 
				".$sql." 
				
		";
		$attribute_price_query = os_db_query($attribute_price_query);

		$discount = 0;
		if ($osPrice->cStatus['customers_status_discount_attributes'] == 1 && $osPrice->cStatus['customers_status_discount'] != 0.00)
		{
			$discount = $osPrice->cStatus['customers_status_discount'];
		}

		$result = array();
		while($attribute_price_data = os_db_fetch_array($attribute_price_query))
		{
			if ($attribute_price_data['products_discount_allowed'] < $osPrice->cStatus['customers_status_discount'])
				$discount = $attribute_price_data['products_discount_allowed'];

			$price = $osPrice->Format($attribute_price_data['options_values_price'], false, $attribute_price_data['products_tax_class_id'], true);

			if ($attribute_price_data['weight_prefix'] != '+')
				$attribute_price_data['options_values_weight'] *= -1;

			if ($attribute_price_data['price_prefix'] == '+')
				$price = $price - $price / 100 * $discount;
			else
				$price *= -1;

			$result[$attribute_price_data['products_id'].'_'.$attribute_price_data['options_id'].'_'.$attribute_price_data['options_values_id']] = array(
				'weight' => $attribute_price_data['options_values_weight'],
				'price' => $price
			);
		}

		return $result;
	}

	/**
	 * Получение ставок налога и их названий
	 */
	public function getTaxRate($params = array())
	{
		if (empty($params['tax_class_id'])) return false;

		$class_id = $params['tax_class_id'];

		$country_id = ($params['entry_country_id']) ? $params['entry_country_id'] : -1;
		$zone_id = ($params['entry_zone_id']) ? $params['entry_zone_id'] : -1;

		if (($country_id == -1) && ($zone_id == -1))
		{
			if (!isset($_SESSION['customer_id']))
			{
				$country_id = STORE_COUNTRY;
				$zone_id = STORE_ZONE;
			}
			else
			{
				$country_id = $_SESSION['customer_country_id'];
				$zone_id = $_SESSION['customer_zone_id'];
			}
		}
		else
		{
			$country_id = $country_id;
			$zone_id = $zone_id;
		}

		$tax_query = osDBquery("
			select 
				sum(tax_rate) as tax_rate, tr.tax_class_id, tax_description
			from 
				".TABLE_TAX_RATES." tr 
					left join ".TABLE_ZONES_TO_GEO_ZONES." za on (tr.tax_zone_id = za.geo_zone_id) 
					left join ".TABLE_GEO_ZONES." tz on (tz.geo_zone_id = tr.tax_zone_id) 
			where 
				(za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '".(int)$country_id."') and 
				(za.zone_id is null or za.zone_id = '0' or za.zone_id = '".(int)$zone_id."') 
				AND tr.tax_class_id IN (".implode(',', $class_id).") 
			group by 
				tr.tax_priority
		");

		if (os_db_num_rows($tax_query, true))
		{
			$tax_multiplier = 1.0;
			$return = array();
			while ($tax = os_db_fetch_array($tax_query))
			{
				$tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
				$return[$tax['tax_class_id']] = array(
					'taxId' => ($tax_multiplier - 1.0) * 100,
					'taxName' => $tax['tax_description']
				);
			}
			
			return $return;
		}
		else
			return 0;
	}
}
?>