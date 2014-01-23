<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*	Copyright (c) 2007 VaM Shop
*---------------------------------------------------------
*/

class spsr
{
	var $code, $title, $description, $icon, $enabled;

	function spsr()
	{
		global $order;

		$this->code = 'spsr';
		$this->title = MODULE_SHIPPING_SPSR_TEXT_TITLE;
		$this->description = MODULE_SHIPPING_SPSR_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_SHIPPING_SPSR_SORT_ORDER;
		$this->icon = '';
		$this->tax_class = MODULE_SHIPPING_SPSR_TAX_CLASS;
		$this->enabled = ((MODULE_SHIPPING_SPSR_STATUS == 'True') ? true : false);

		if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_SPSR_ZONE > 0) )
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_SHIPPING_SPSR_ZONE."' and zone_country_id = '".$order->delivery['country']['id']."' order by zone_id");
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

		$own_zone_id = STORE_ZONE;

		//переключатель Доставка по своему городу
		if (($this->enabled == true) && (MODULE_SHIPPING_SPSR_OWN_CITY_DELIVERY == 'False'))
		{
			if (strtolower(MODULE_SHIPPING_SPSR_FROM_CITY) == strtolower($order->delivery['city']))
			{
				$this->enabled = false;
			}
		}

		//Переключатель Доставка по своему региону
		if (($this->enabled == true) && (MODULE_SHIPPING_SPSR_OWN_REGION_DELIVERY == 'False'))
		{
			if ($own_zone_id == $order->delivery['zone_id'])
			{
				$this->enabled = false;
			}
		}

		//отключение доставки для отдельных городов
		if (($this->enabled == true) && (MODULE_SHIPPING_SPSR_DISABLE_CITIES !== ''))
		{
			$disabled_cities = explode(',',MODULE_SHIPPING_SPSR_DISABLE_CITIES);
			foreach ($disabled_cities as $cityvalue)
			{
				if (strtolower($cityvalue) == strtolower($order->delivery['city']))
				{
					$this->enabled = false;
				}
			}
		}
	}

	// class methods
	function quote($method = '')
	{
		global $order, $cart, $shipping_weight, $own_zone_id;		  

		if ($shipping_weight == 0)
		{
			$shipping_weight = MODULE_SHIPPING_SPSR_DEFAULT_SHIPPING_WEIGHT;
		}

		if ($this->tax_class > 0)
		{
			$this->quotes['tax'] = os_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		}

		//вытаскиваем Region ID города назначения базы
		$region_id = $this->get_spsr_zone_id($order->delivery['zone_id']);

		//вытаскиваем свой Region ID из базы
		$own_cpcr_id = $this->get_spsr_zone_id($own_zone_id);

		//oscommerce дважды запрашивает цену доставки c cpcr.ru - до подтверждения цены доставки (для показа пользователю) и после подтверждения цены доставки (нажатие кнопки "Продолжить"). Х.з. почему, видимо так работает oscommerce. Чтобы не запрашивать дважды кешируем $cost в hidden поле cost.
		if (!isset($_POST['cost']))
		{
			//составление запроса стоимости доставки
			if (isset($_POST['error_tocity']))
				$request='http://cpcr.ru/cgi-bin/postxml.pl?TariffCompute&FromRegion='.$own_cpcr_id.'|0&FromCityName='.iconv("UTF-8","windows-1251", MODULE_SHIPPING_SPSR_FROM_CITY).'&Weight='. $shipping_weight .'&Nature='.MODULE_SHIPPING_SPSR_NATURE.'&Amount=0&Country=209|0&ToCity='.iconv("UTF-8","windows-1251", $_POST['error_tocity']);
			else
				$request='http://cpcr.ru/cgi-bin/postxml.pl?TariffCompute&FromRegion='.$own_cpcr_id.'|0&FromCityName='.iconv("UTF-8","windows-1251", MODULE_SHIPPING_SPSR_FROM_CITY).'&Weight='. $shipping_weight .'&Nature='.MODULE_SHIPPING_SPSR_NATURE.'&Amount=0&Country=209|0&ToRegion='.$region_id.'|0&ToCityName='.iconv("UTF-8","windows-1251", $order->delivery['city']);

			//проверки связи с сервером
			$server_link = false;

			$file_headers = @get_headers($request);
			if (($file_headers[0] !== 'HTTP/1.1 404 Not Found') && ($file_headers!==false))
			{
				$server_link = true;
			}

			//Запрос стоимости с cpcr.ru
			if ($server_link==true)
			{
				$xmlstring= simplexml_load_file($request);
			}
			else
			{
				$title = "<font color=red>Нет связи с сервером cpcr.ru, стоимость доставки не определена.</font>";
				$cost = 0;
			}

			//получение цены доставки
			if ($xmlstring->PayTariff)
			{
				$find_symbols = array(chr(160),'р.',' '); //вместо пробела в стоимости доставки cpcr.ru использует симовл с ascii кодом 160.
				$cost = ceil(str_replace(',','.',str_replace($find_symbols,'',$xmlstring->Total)));
				$title .= 'Доставка в '.$order->delivery['city'].', '.$order->delivery['state'];
				if ($cost>0)
				{
					$title .= '<input type="hidden" name="cost" value="'.$cost.'">';
				}			
			}
			//если $cost уже был определен
		}
		else
		{
			$cost = $_POST['cost'];
			$title .= 'Доставка в '.$order->delivery['city'].', '.$order->delivery['state'];
			if ($cost>0)
			{
				$title .= '<input type="hidden" name="cost" value="'.$cost.'">';
			}	
		}			

		//Обработка ошибки Город не найден
		if ($xmlstring->Error->ToCity && $server_link == true)
		{
			$title .= "<font color=red>Ошибка, город \"".$order->delivery['city']."\" не найден. Либо в названии города допущена ошибка, либо в данный город СПСР доставку не производит.</font><br>";
		}

		//Уточнение названия города, для получения City_Id c сервера cpcr.ru
		if (!$xmlstring->Error->ToCity->City->CityName=='')
		{
			$title .= "<font color=red>Пожалуйста уточните название вашего города:</font><br>";
			if ($xmlstring->Error->ToCity->City)
			{
				foreach ($xmlstring->Error->ToCity->City as $city_value)
				{
					$title .= "<input type=radio name=error_tocity value=\"".$city_value->City_Id."|".$city_value->City_Owner_Id."\" onChange=\"this.form.submit()\">".$city_value->CityName.", ".$city_value->RegionName."<br>";
					//начало код для унификации с калькулятором
					echo "<input type=hidden name=\"".$city_value->City_Id."|".$city_value->City_Owner_Id."\" value=\"".$city_value->CityName.", ".$city_value->RegionName."\">";	
					//конец код для унификации с калькулятором						
				}
			}
		}

		//Обработка ошибки Веса
		if ($xmlstring->Error->Weight)
		{
			$title .= "<br><font color=red>Ошибка! Неправильный формат веса</font>";
		}

		//Оюработка ошибки Оценочной стоимости	
		if ($xmlstring->Error->Amount)
		{
			$title .= "<br><font color=red>Ошибка! Неправильный формат оценочной стоимости</font>";
		}

		if (!isset($own_cpcr_id))
		{
			$title .= "<br><font color=red>Ошибка! Вы не выбрали зону! (Администрирование>Настройки>My store>Zone)</font>";
		}

		//Обработка ошибки Mutex Wait Timeout
		if ($xmlstring->Error['Type']=='Mutex' & $xmlstring->Error['SubType']=='Wait Timeout')
		{
			$title .= "<br><font color=red>Ошибка! cpcr.ru не вернул ответ на запрос. Попробуйте обновить страницу.</font>";
		}

		//Обработка ошибки ComputeTariff CalcError
		if ($xmlstring->Error['Type']=='ComputeTariff' & $xmlstring->Error['SubType']=='CalcError')
		{
			$title .= "<br><font color=red>Ошибка! Ошибка вычисления стоимости доставки.</font>";
		}

		//Обработка ошибки Command Unknown
		if ($xmlstring->Error['Type']=='Command' & $xmlstring->Error['SubType']=='Unknown')
		{
			$title .= "<br><font color=red>Ошибка! Неизвестная команда.</font>";
		}

		//Обработка ошибки Unknown Unknown (прочие ошибки)
		if ($xmlstring->Error['Type'])
		{
			$title .= "<br><font color=red>Неизвестная ошибка, попробуйте позже.</font>";
		}

		//Отображдение отладочной информации
		if (MODULE_SHIPPING_SPSR_DEBUG=='True')
		{
			$title .= "<br>".'$own_zone_id='.$own_zone_id."<br>".
			'$order->delivery[\'zone_id\']='.$order->delivery['zone_id']."<br>".
			'$own_cpcr_id='.$own_cpcr_id."<br>".
			'MODULE_SHIPPING_SPSR_OWN_CITY_DELIVERY='.MODULE_SHIPPING_SPSR_OWN_CITY_DELIVERY."<br>".
			'MODULE_SHIPPING_SPSR_OWN_REGION_DELIVERY='.MODULE_SHIPPING_SPSR_OWN_REGION_DELIVERY."<br>".			
			'$shipping_weight='.$shipping_weight."<br>".
			'MODULE_SHIPPING_SPSR_NATURE='.MODULE_SHIPPING_SPSR_NATURE."<br>".
			'$request='.$request."<br>".
			'$cost='.$cost."<br>".
			'$_POST[\'cost\']='.$_POST['cost'];
			'$xmlstring:'."<br>".
			(is_object($xmlstring)?"<textarea readonly=\"readonly\" rows=\"5\">".$xmlstring->asXML()."</textarea>":'');
		}

		if ($method != '')
			$title = strip_tags($title);

		$this->quotes = array(
			'id' => $this->code,
			'module' => MODULE_SHIPPING_SPSR_TEXT_TITLE,
			'methods' => array(array('id' => $this->code,
			'title' => $title,
			'cost' => $cost))
		);

		if (os_not_null($this->icon)) $this->quotes['icon'] = os_image($this->icon, $this->title);

		return $this->quotes;
	}

	function check()
	{
		if (!isset($this->_check))
		{
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_SHIPPING_SPSR_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_SPSR_STATUS', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_SPSR_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_SPSR_FROM_CITY', 'Москва', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_SPSR_DISABLE_CITIES', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_SPSR_OWN_CITY_DELIVERY', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_SPSR_OWN_REGION_DELIVERY', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_SPSR_DEFAULT_SHIPPING_WEIGHT', '0.5', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_SPSR_NATURE', '3', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_SPSR_DEBUG', 'False', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_SPSR_TAX_CLASS', '0', '6', '0', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_SPSR_ZONE', '0', '6', '0', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_SPSR_SORT_ORDER', '0', '6', '0', now())");

		os_db_query("drop table if exists ".DB_PREFIX."spsr_zones;");
		os_db_query("create table ".DB_PREFIX."spsr_zones (
			id int(11) not null auto_increment,
			zone_id int(11) default '0' not null ,
			spsr_zone_id int(11) default '0' not null,
			PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('1', '22', '53');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('2', '23', '55');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('3', '24', '56');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('4', '25', '54');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('5', '26', '57');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('6', '27', '101');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('7', '28', '19');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('8', '29', '58');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('9', '30', '24');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('10', '31', '59');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('11', '32', '60');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('12', '33', '61');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('13', '34', '62');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('14', '35', '63');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('15', '36', '64');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('16', '37', '65');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('17', '38', '66');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('18', '39', '84');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('19', '40', '67');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('20', '41', '92');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('21', '42', '94');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('22', '43', '3');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('23', '44', '30');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('24', '45', '31');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('25', '46', '51');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('26', '47', '75');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('27', '48', '89');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('28', '49', '4');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('29', '50', '6');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('30', '51', '7');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('31', '52', '8');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('32', '53', '10');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('33', '54', '11');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('34', '55', '12');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('35', '56', '13');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('36', '57', '14');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('37', '58', '17');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('38', '59', '18');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('39', '60', '21');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('40', '61', '22');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('41', '62', '23');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('42', '63', '25');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('43', '64', '27');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('44', '65', '29');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('45', '66', '32');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('46', '67', '33');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('47', '68', '35');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('48', '69', '36');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('49', '70', '38');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('50', '71', '40');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('51', '72', '41');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('52', '73', '43');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('53', '74', '44');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('54', '75', '45');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('55', '76', '46');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('56', '77', '47');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('57', '78', '48');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('58', '79', '49');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('59', '80', '50');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('60', '81', '52');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('61', '82', '68');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('62', '83', '69');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('63', '84', '70');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('64', '85', '71');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('65', '86', '72');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('66', '87', '73');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('67', '88', '74');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('68', '89', '78');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('69', '90', '79');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('70', '91', '80');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('71', '92', '81');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('72', '93', '83');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('73', '94', '87');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('74', '95', '91');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('75', '97', '100');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('76', '100', '16');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('77', '104', '42');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('78', '106', '88');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('79', '107', '90');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('80', '108', '95');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('81', '109', '97');");
		os_db_query("insert into ".DB_PREFIX."spsr_zones values ('82', '110', '99');");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
		os_db_query("drop table if exists ".DB_PREFIX."spsr_zones;");
	}

	function keys()
	{
		return array(
			'MODULE_SHIPPING_SPSR_STATUS',
			'MODULE_SHIPPING_SPSR_FROM_CITY',
			'MODULE_SHIPPING_SPSR_DISABLE_CITIES',
			'MODULE_SHIPPING_SPSR_OWN_CITY_DELIVERY',
			'MODULE_SHIPPING_SPSR_OWN_REGION_DELIVERY',
			'MODULE_SHIPPING_SPSR_DEFAULT_SHIPPING_WEIGHT',
			'MODULE_SHIPPING_SPSR_NATURE',
			'MODULE_SHIPPING_SPSR_DEBUG',
			'MODULE_SHIPPING_SPSR_ALLOWED',
			'MODULE_SHIPPING_SPSR_TAX_CLASS',
			'MODULE_SHIPPING_SPSR_ZONE',
			'MODULE_SHIPPING_SPSR_SORT_ORDER'
		);
	}

	private function get_spsr_zone_id($zone_id)
	{
		$spsr_zone_query = os_db_query("select spsr_zone_id from ".DB_PREFIX."spsr_zones where zone_id = '".$zone_id."'");

		if (os_db_num_rows($spsr_zone_query))
		{
			$spsr_zone = os_db_fetch_array($spsr_zone_query);
			$spsr_zone_id = $spsr_zone['spsr_zone_id'];
			return $spsr_zone_id;
		}
		else
		{
			return false;
		}
	}
}