<?php
/*
*---------------------------------------------------------
*
* CartET - Open Source Shopping Cart Software
* http://www.cartet.org
*
*---------------------------------------------------------
*/

class emspost
{
	var $code, $title, $description, $icon, $enabled;

	function emspost()
	{
		global $order;

		$this->code = 'emspost';
		$this->title = MODULE_SHIPPING_EMSPOST_TEXT_TITLE;
		$this->description = MODULE_SHIPPING_EMSPOST_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_SHIPPING_EMSPOST_SORT_ORDER;
		$this->icon = '';
		$this->tax_class = MODULE_SHIPPING_EMSPOST_TAX_CLASS;
		$this->enabled = ((MODULE_SHIPPING_EMSPOST_STATUS == 'True') ? true : false);

		if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_EMSPOST_ZONE > 0) )
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_SHIPPING_EMSPOST_ZONE."' and zone_country_id = '".$order->delivery['country']['id']."' order by zone_id");
			while ($check = os_db_fetch_array($check_query))
			{
				if ($check['zone_id'] < 1)
				{
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->delivery['zone_id'])
				{
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false)
			{
				$this->enabled = false;
			}
		}
	}

	// class methods
	function quote($method = '')
	{
		global $order, $shipping_weight, $total_count;

		$this->quotes = array(
			'id' => $this->code,
			'module' => MODULE_SHIPPING_EMSPOST_TEXT_TITLE,
			'methods' => array(array('id' => $this->code,
			'title' => MODULE_SHIPPING_EMSPOST_TEXT_NOTE))
		);

		if (os_not_null($this->icon)) $this->quotes['icon'] = os_image($this->icon, $this->title);

		$urlCities = 'http://emspost.ru/api/rest/?method=ems.get.locations&type=cities&plain=true';
		$urlWeight = 'http://emspost.ru/api/rest/?method=ems.get.max.weight';

		// create curl resource
		$ch = curl_init();

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// set url
		curl_setopt($ch, CURLOPT_URL, $urlWeight);
		$outWeight = curl_exec($ch);

		$WeightList = json_decode($outWeight, true);

		foreach ($WeightList as $weight)
		{
			$max_weight = $weight['max_weight'];

			if ($shipping_weight > $max_weight)
			{
				$this->quotes['error']='Превышен максимально возможный вес одного отправления. Разбейте заказ на несколько частей.';
				return $this->quotes;
			}
		}

		//Получаем список городов и регионов
		$urlRussia = 'http://emspost.ru/api/rest/?method=ems.get.locations&type=russia&plain=true';

		// create curl resource
		$ch = curl_init();

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// set url
		curl_setopt($ch, CURLOPT_URL, $urlRussia);
		$outRussia = curl_exec($ch);

		//Вытягиваем регион магазина
		$zones_shop = STORE_ZONE;
		$zones_zones = os_db_query("select zone_id, zone_name from ".TABLE_ZONES." where (zone_id='$zones_shop')");
		$zones_id = os_db_fetch_array($zones_zones);
		$zonesshop = $zones_id['zone_name'];

		//Вытягиваем город получателя
		$tocity = $order->delivery['city'];	

		//Вытягиваем регион получателя 
		$tostate_id = $order->delivery['state']; 
		$tostate_tostate = os_db_query("select zone_id, zone_name from ".TABLE_ZONES." where (zone_name='$tostate_id')");
		$tostate_tostate_id = os_db_fetch_array($tostate_tostate);
		$tostate = $tostate_tostate_id['zone_name'];

		//проверяем город/регион отправителя/получателя
		$RussiaList = json_decode($outRussia, true);

		foreach($RussiaList['rsp']['locations'] as $key=>$val)
		{
			//Ищем исходящий город
			if (in_array(mb_strtoupper(MODULE_SHIPPING_EMSPOST_CITY, "utf-8"), $RussiaList['rsp']['locations'][$key]))
			{
				$from = $RussiaList['rsp']['locations'][$key]['value'];
			}

			if ($from === null)
			{
				if(in_array(mb_strtoupper($zonesshop, "utf-8"), $RussiaList['rsp']['locations'][$key]))
				{
					$from = $RussiaList['rsp']['locations'][$key]['value'];	
				}		
			}

			if(in_array(mb_strtoupper($tocity, "utf-8"),$RussiaList['rsp']['locations'][$key]))
			{ 
				$to = $RussiaList['rsp']['locations'][$key]['value']; 
				$tomessag = 'город: '. $RussiaList['rsp']['locations'][$key]['name']; 
			}

			if ($to === null)
			{
				if(in_array(mb_strtoupper($tostate, "utf-8"),$RussiaList['rsp']['locations'][$key]))
				{
					$to = $RussiaList['rsp']['locations'][$key]['value'];
					$tomessag = 'регион: '. $RussiaList['rsp']['locations'][$key]['name'];
				}
			}
		}

		// Если вдруг ничего не нашлось
		if ($from === null)
		{
			$this->quotes['error']='Доставка из города:  '.MODULE_SHIPPING_EMSPOST_CITY.' не производится! Возможно Вы допустили ошибку в адресе.';
			return $this->quotes;
		}
		else if ($to === null)
		{
			$this->quotes['error']='<center>Доставка в город  '.$tocity.' посредством EMS не производится! Возможно Вы допустили ошибку в адресе.</center>';
			return $this->quotes;
		}
		//----

		$url = 'http://emspost.ru/api/rest?method=ems.calculate&from='.$from.'&to='.$to.'&weight='.$shipping_weight;

		curl_setopt($ch, CURLOPT_URL, $url);
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);

		$contents = $output;
		$contents = $contents;
		$results = json_decode($contents, true);

		if ($results['rsp']['stat'] == 'fail')
		{
			$this->quotes['error'] = 'Ошибка: '.$results['rsp']['err']['msg'];
			return $this->quotes;
		}

		$shPrice = $results['rsp']['price'];
		if (MODULE_SHIPPING_EMSPOST_DCVAL_PERCENT >0)
		{
			$shPrice += $order->info['subtotal']*MODULE_SHIPPING_EMSPOST_DCVAL_PERCENT/100;
		}
		$this->quotes['methods'][key($this->quotes['methods'])]['cost'] = $shPrice + MODULE_SHIPPING_EMSPOST_HANDLING;
		$this->quotes['methods'][key($this->quotes['methods'])]['title'] = 'Цена доставки заказа при <b>предварительной оплате</b>. </br>Указанная стоимость включает в себя расходы по упаковке заказа. </br> Доставка в '.$tomessag. '';
		$dlvr_min = $results['rsp']['term']['min'];
		$dlvr_max = $results['rsp']['term']['max'];
		if (($dlvr_min > 0) AND ( $dlvr_max > 0))

		if ($this->tax_class > 0)
		{
			$this->quotes['tax'] = os_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		}

		return $this->quotes;
	}

	function check()
	{
		if (!isset($this->_check))
		{
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_SHIPPING_EMSPOST_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_EMSPOST_STATUS', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_EMSPOST_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_EMSPOST_CITY', 'Москва', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_EMSPOST_HANDLING', '0', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_EMSPOST_TAX_CLASS', '0', '6', '0', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_EMSPOST_ZONE', '0', '6', '0', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_EMSPOST_SORT_ORDER', '0', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_EMSPOST_DCVAL_PERCENT', '0', '6', '0', now())");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys()
	{
		return array(
			'MODULE_SHIPPING_EMSPOST_STATUS',
			'MODULE_SHIPPING_EMSPOST_CITY',
			'MODULE_SHIPPING_EMSPOST_HANDLING',
			'MODULE_SHIPPING_EMSPOST_ALLOWED',
			'MODULE_SHIPPING_EMSPOST_TAX_CLASS',
			'MODULE_SHIPPING_EMSPOST_ZONE',
			'MODULE_SHIPPING_EMSPOST_SORT_ORDER',
			'MODULE_SHIPPING_EMSPOST_DCVAL_PERCENT'
		);
	}
}