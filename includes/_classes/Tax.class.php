<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiTax extends CartET
{
	/**
	 * Сохранение\добавление налога
	 */
	public function save($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$dataArray = array(
			'tax_class_title' => os_db_prepare_input($params['tax_class_title']),
			'tax_class_description' => os_db_prepare_input($params['tax_class_description'])
		);

		if ($action == 'edit')
		{
			$dataArray['last_modified'] = 'now()';
			os_db_perform(TABLE_TAX_CLASS, $dataArray, 'update', "tax_class_id = '".(int)$params['tID']."'");
		}
		else
		{
			$dataArray['date_added'] = 'now()';
			os_db_perform(TABLE_TAX_CLASS, $dataArray);
		}

		if ($params['action'] == 'new')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление налога
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$tID = (is_array($params)) ? $params['id'] : $params;

		os_db_query("delete from ".TABLE_TAX_CLASS." where tax_class_id = '".(int)$tID."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение\добавление ставок налога
	 */
	public function saveRates($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$dataArray = array(
			'tax_zone_id' => (int)$params['tax_zone_id'],
			'tax_class_id' => (int)$params['tax_class_id'],
			'tax_rate' => os_db_prepare_input($params['tax_rate']),
			'tax_description' => os_db_prepare_input($params['tax_description']),
			'tax_priority' => (int)$params['tax_priority']
		);

		if ($action == 'edit')
		{
			$dataArray['tax_rates_id'] = (int)$params['tID'];
			$dataArray['last_modified'] = 'now()';
			os_db_perform(TABLE_TAX_RATES, $dataArray, 'update', "tax_rates_id = '".(int)$params['tID']."'");
		}
		else
		{
			$dataArray['date_added'] = 'now()';
			os_db_perform(TABLE_TAX_RATES, $dataArray);
		}

		if ($params['action'] == 'new')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление ставок налога
	 */
	public function deleteRates($params)
	{
		if (!isset($params)) return false;
		$tID = (is_array($params)) ? $params['id'] : $params;

		os_db_query("delete from ".TABLE_TAX_RATES." where tax_rates_id = '".(int)$tID."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}
?>