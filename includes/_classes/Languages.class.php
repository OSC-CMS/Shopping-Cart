<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiLanguages extends CartET
{
	/**
	 * Добавление\сохранение языка
	 */
	public function save($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$dataArray = array(
			'name' => os_db_prepare_input($params['name']),
			'code' => os_db_prepare_input($params['code']),
			'image' => os_db_prepare_input($params['image']),
			'directory' => os_db_prepare_input($params['directory']),
			'sort_order' => (int)$params['sort_order'],
			'language_charset' => os_db_prepare_input($params['language_charset']),
			'status' => (int)$params['status']
		);

		if ($action == 'edit')
		{
			os_db_perform(TABLE_LANGUAGES, $dataArray, 'update', "languages_id = '".(int)$params['lID']."'");
		}
		else
		{
			os_db_perform(TABLE_LANGUAGES, $dataArray);
			$lid = os_db_insert_id();
			$this->addDBLang($lid);
		}

		if ($params['default'] == 'on')
		{
			os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".os_db_input($code)."' where configuration_key = 'DEFAULT_LANGUAGE'");
		}

		set_default_cache();

		$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Статус
	 */
	public function status($params)
	{
		if (is_array($params))
		{
			os_db_query("UPDATE ".TABLE_LANGUAGES." SET ".os_db_prepare_input($params['column'])." = '".(int)$params['status']."' WHERE languages_id = '".(int)$params['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Удаление языка
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$lID = (is_array($params)) ? $params['id'] : $params;

		$lng_query = os_db_query("select languages_id from ".TABLE_LANGUAGES." where code = '".DEFAULT_CURRENCY."'");
		$lng = os_db_fetch_array($lng_query);
		if ($lng['languages_id'] == $lID)
		{
			os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
		}

		$this->deleteDBLang($lID);

		set_default_cache();

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	public function addDBLang($insert_id)
	{
		$categories_query = os_db_query("select c.categories_id, cd.categories_name from ".TABLE_CATEGORIES." c left join ".TABLE_CATEGORIES_DESCRIPTION." cd on c.categories_id = cd.categories_id where cd.language_id = '".$_SESSION['languages_id']."'");
		while ($categories = os_db_fetch_array($categories_query)) {
			os_db_query("insert into ".TABLE_CATEGORIES_DESCRIPTION." (categories_id, language_id, categories_name) values ('".$categories['categories_id']."', '".$insert_id."', '".os_db_input($categories['categories_name'])."')");
		}

		$products_query = os_db_query("select p.products_id, pd.products_name, pd.products_description, pd.products_url from ".TABLE_PRODUCTS." p left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id where pd.language_id = '".$_SESSION['languages_id']."'");
		while ($products = os_db_fetch_array($products_query)) {
			os_db_query("insert into ".TABLE_PRODUCTS_DESCRIPTION." (products_id, language_id, products_name, products_description, products_url) values ('".$products['products_id']."', '".$insert_id."', '".os_db_input($products['products_name'])."', '".os_db_input($products['products_description'])."', '".os_db_input($products['products_url'])."')");
		}

		$products_options_query = os_db_query("select products_options_id, products_options_name from ".TABLE_PRODUCTS_OPTIONS." where language_id = '".$_SESSION['languages_id']."'");
		while ($products_options = os_db_fetch_array($products_options_query)) {
			os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS." (products_options_id, language_id, products_options_name) values ('".$products_options['products_options_id']."', '".$insert_id."', '".os_db_input($products_options['products_options_name'])."')");
		}

		$products_options_values_query = os_db_query("select products_options_values_id, products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where language_id = '".$_SESSION['languages_id']."'");
		while ($products_options_values = os_db_fetch_array($products_options_values_query)) {
			os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS_VALUES." (products_options_values_id, language_id, products_options_values_name) values ('".$products_options_values['products_options_values_id']."', '".$insert_id."', '".os_db_input($products_options_values['products_options_values_name'])."')");
		}

		$manufacturers_query = os_db_query("select m.manufacturers_id, mi.manufacturers_url from ".TABLE_MANUFACTURERS." m left join ".TABLE_MANUFACTURERS_INFO." mi on m.manufacturers_id = mi.manufacturers_id where mi.languages_id = '".$_SESSION['languages_id']."'");
		while ($manufacturers = os_db_fetch_array($manufacturers_query)) {
			os_db_query("insert into ".TABLE_MANUFACTURERS_INFO." (manufacturers_id, languages_id, manufacturers_url) values ('".$manufacturers['manufacturers_id']."', '".$insert_id."', '".os_db_input($manufacturers['manufacturers_url'])."')");
		}

		$orders_status_query = os_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$_SESSION['languages_id']."'");
		while ($orders_status = os_db_fetch_array($orders_status_query)) {
			os_db_query("insert into ".TABLE_ORDERS_STATUS." (orders_status_id, language_id, orders_status_name) values ('".$orders_status['orders_status_id']."', '".$insert_id."', '".os_db_input($orders_status['orders_status_name'])."')");
		}

		$shipping_status_query = os_db_query("select shipping_status_id, shipping_status_name from ".TABLE_SHIPPING_STATUS." where language_id = '".$_SESSION['languages_id']."'");
		while ($shipping_status = os_db_fetch_array($shipping_status_query)) {
			os_db_query("insert into ".TABLE_SHIPPING_STATUS." (shipping_status_id, language_id, shipping_status_name) values ('".$shipping_status['shipping_status_id']."', '".$insert_id."', '".os_db_input($shipping_status['shipping_status_name'])."')");
		}

		$xsell_grp_query = os_db_query("select products_xsell_grp_name_id,xsell_sort_order, groupname from ".TABLE_PRODUCTS_XSELL_GROUPS." where language_id = '".$_SESSION['languages_id']."'");
		while ($xsell_grp = os_db_fetch_array($xsell_grp_query))
		{
			os_db_query("insert into ".TABLE_PRODUCTS_XSELL_GROUPS." (products_xsell_grp_name_id,xsell_sort_order, language_id, groupname) values ('".$xsell_grp['products_xsell_grp_name_id']."','".$xsell_grp['xsell_sort_order']."', '".$insert_id."', '".os_db_input($xsell_grp['groupname'])."')");
		}

		$customers_status_query = os_db_query("SELECT DISTINCT customers_status_id FROM ".TABLE_CUSTOMERS_STATUS);
		while ($data=os_db_fetch_array($customers_status_query))
		{
			$customers_status_data_query=os_db_query("SELECT * FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id='".$data['customers_status_id']."'");

			$group_data = os_db_fetch_array($customers_status_data_query);
			$c_data = array(
				'customers_status_id'=>$data['customers_status_id'],
				'language_id'=>$insert_id,
				'customers_status_name'=>$group_data['customers_status_name'],
				'customers_status_public'=>$group_data['customers_status_public'],
				'customers_status_image'=>$group_data['customers_status_image'],
				'customers_status_discount'=>$group_data['customers_status_discount'],
				'customers_status_ot_discount_flag'=>$group_data['customers_status_ot_discount_flag'],
				'customers_status_ot_discount'=>$group_data['customers_status_ot_discount'],
				'customers_status_graduated_prices'=>$group_data['customers_status_graduated_prices'],
				'customers_status_show_price'=>$group_data['customers_status_show_price'],
				'customers_status_show_price_tax'=>$group_data['customers_status_show_price_tax'],
				'customers_status_add_tax_ot'=>$group_data['customers_status_add_tax_ot'],
				'customers_status_payment_unallowed'=>$group_data['customers_status_payment_unallowed'],
				'customers_status_shipping_unallowed'=>$group_data['customers_status_shipping_unallowed'],
				'customers_status_discount_attributes'=>$group_data['customers_status_discount_attributes']
			);

			os_db_perform(TABLE_CUSTOMERS_STATUS, $c_data);
		}
	}

	public function deleteDBLang($lID)
	{
		os_db_query("delete from ".TABLE_CATEGORIES_DESCRIPTION." where language_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." where language_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS." where language_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS_VALUES." where language_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_MANUFACTURERS_INFO." where languages_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_ORDERS_STATUS." where language_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_LANGUAGES." where languages_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_CONTENT_MANAGER." where languages_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_PRODUCTS_CONTENT." where languages_id = '".(int)$lID."'");
		os_db_query("delete from ".TABLE_CUSTOMERS_STATUS." where language_id = '".(int)$lID."'");
	}
}
?>