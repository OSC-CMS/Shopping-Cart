<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiShipping extends CartET
{
	/**
	 * Возвращает установленные модули доставки
	 */
	public function getInstalled($data = array())
	{
		$lang = (isset($data['lang'])) ? $data['lang'] : $_SESSION['language'];

		$shippings = explode(';', MODULE_SHIPPING_INSTALLED);

		$aShippings = array();
		if (is_array($shippings) && !empty($shippings))
		{
			foreach ($shippings as $shipping)
			{
				$shippingMethod = str_replace('.php', '', $shipping);
				$shippingFile = get_path('modules').'shipping/'.$shippingMethod.'/'.$shipping;
				$shippingLangFile = get_path('modules').'shipping/'.$shippingMethod.'/'.$lang.'.php';
				
				if (is_file($shippingFile) && is_file($shippingLangFile))
				{
					require($shippingLangFile);
					$aShippings[$shippingMethod] = array(
						'id' => $shippingMethod,
						'text' => constant(MODULE_SHIPPING_.strtoupper($shippingMethod)._TEXT_TITLE)
					);
				}
			}
		}

		return $aShippings;
	}

	/**
	 * Возвращает название метода доставки
	 */
	public function getName($data = array())
	{
		if (empty($data['method'])) return false;
		$lang = (isset($data['lang'])) ? $data['lang'] : $_SESSION['language'];

		$shippingLangFile = get_path('modules').'shipping/'.$data['method'].'/'.$lang.'.php';
		if (is_file($shippingLangFile))
		{
			require($shippingLangFile);
			return constant(MODULE_SHIPPING_.strtoupper($data['method'])._TEXT_TITLE);
		}
		else
			return false;
	}

	/**
	 * Удаление связи доставки с оплатой
	 */
	public function deleteShipToPay($params)
	{
		if (!isset($params)) return false;
		$s2p_id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("DELETE FROM ".TABLE_SHIP2PAY." WHERE s2p_id = '".(int)$s2p_id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Статус
	 */
	public function statusShipToPay($params)
	{
		if (is_array($params))
		{
			os_db_query("UPDATE ".TABLE_SHIP2PAY." SET ".os_db_prepare_input($params['column'])." = '".(int)$params['status']."' WHERE s2p_id = '".(int)$params['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Сохранение правила доставки к оплате
	 */
	public function save($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$shp_id = os_db_prepare_input($params['shp_id']);
		$s2p_id = (int)$params['s2p_id'];

		if (isset($params['pay_ids']))
		{
			$pay_ids = os_db_prepare_input(implode(";", $params['pay_ids']));
		}

		$dataArray = array(
			'shipment' => os_db_input($shp_id),
			'payments_allowed' => os_db_input($pay_ids),
			'zones_id' => (int)$params['configuration']['zone_id'],
			'status' => (int)$params['status']
		);

		if ($action == 'edit')
			os_db_perform(TABLE_SHIP2PAY, $dataArray, 'update', "s2p_id = '".$s2p_id."'");
		else
			os_db_perform(TABLE_SHIP2PAY, $dataArray);

		$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение статусов доставки
	 */
	public function saveShippingStatus($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];


		$shipping_status_id = os_db_prepare_input($params['oID']);

		$languages = os_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
		{
			if ($languages[$i]['status'] == 1)
			{
				$shipping_status_name_array = $params['shipping_status_name'];
				$language_id = $languages[$i]['id'];

				$sql_data_array = array('shipping_status_name' => os_db_prepare_input($shipping_status_name_array[$language_id]));

				if ($action == 'new')
				{
					if (!os_not_null($shipping_status_id)) {
						$next_id_query = os_db_query("select max(shipping_status_id) as shipping_status_id from ".TABLE_SHIPPING_STATUS."");
						$next_id = os_db_fetch_array($next_id_query);
						$shipping_status_id = $next_id['shipping_status_id'] + 1;
					}

					$insert_sql_data = array(
						'shipping_status_id' => $shipping_status_id,
						'language_id' => $language_id
					);
					$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
					os_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array);
				}
				elseif ($action == 'edit')
				{
					os_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array, 'update', "shipping_status_id = '".os_db_input($shipping_status_id)."' and language_id = '".$language_id."'");
				}
			}
		}

		if ($shipping_status_image = &os_try_upload('shipping_status_image', get_path('images').'shipping_status'))
		{
			// Удаляем старую картинку
			@unlink(get_path('images').'shipping_status/'.$params['shipping_status_image_current']);

			os_db_query("update ".TABLE_SHIPPING_STATUS." set shipping_status_image = '".$shipping_status_image->filename."' where shipping_status_id = '".os_db_input($shipping_status_id)."'");
		}

		if ($params['default'] == 'on')
		{
			os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".os_db_input($shipping_status_id)."' where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
		}

		set_default_cache();



		$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление статуса доставки
	 */
	public function deleteShippingStatus($params)
	{
		if (!isset($params)) return false;
		$status_id = (is_array($params)) ? $params['id'] : $params;

		$shippingStatusImage = "select shipping_status_image from ".TABLE_SHIPPING_STATUS." where shipping_status_id = '".(int)$status_id."' AND language_id = '".$_SESSION['languages_id']."'";
		if (os_db_num_rows($shippingStatusImage) > 0)
		{
			$img = os_db_fetch_array($shippingStatusImage);
			$imgPath = get_path('images').'shipping_status/'.$img['shipping_status_image_current'];
			if (is_file($imgPath))
			{
				@unlink($imgPath);
			}
		}

		os_db_query("delete from ".TABLE_SHIPPING_STATUS." where shipping_status_id = '".(int)$status_id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		set_default_cache();

		return $data;
	}
}
?>