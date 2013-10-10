<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCurrencies extends CartET
{
	/**
	 * Добавление\сохранение валюты
	 */
	public function save($params)
	{
		$action = $params['action'];

		$sql_data_array = array(
			'title' => os_db_prepare_input($params['title']),
			'code' => os_db_prepare_input($params['code']),
			'symbol_left' => os_db_prepare_input($params['symbol_left']),
			'symbol_right' => os_db_prepare_input($params['symbol_right']),
			'decimal_point' => os_db_prepare_input($params['decimal_point']),
			'thousands_point' => os_db_prepare_input($params['thousands_point']),
			'decimal_places' => empty($params['decimal_places']) ? 0 : os_db_prepare_input($params['decimal_places']),
			'value' => os_db_prepare_input($params['value'])
		);

		if ($action == 'new')
			os_db_perform(TABLE_CURRENCIES, $sql_data_array);
		elseif ($action == 'edit')
			os_db_perform(TABLE_CURRENCIES, $sql_data_array, 'update', "currencies_id = '".os_db_input($params['cID'])."'");

		if ($_POST['default'] == 'on')
		{
			os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".os_db_prepare_input($params['code'])."' where configuration_key = 'DEFAULT_CURRENCY'");
		}

		set_default_cache();

		$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление валюты
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$currencies_id = (is_array($params)) ? $params['id'] : $params;

		$currency_query = os_db_query("select currencies_id from ".TABLE_CURRENCIES." where code = '".DEFAULT_CURRENCY."'");
		$currency = os_db_fetch_array($currency_query);
		if ($currency['currencies_id'] == $currencies_id)
		{
			os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
		}

		os_db_query("delete from ".TABLE_CURRENCIES." where currencies_id = '".os_db_input($currencies_id)."'");

		set_default_cache();

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}
?>