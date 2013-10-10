<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Access denied!');

// Удаляем атрибуты-файлы
$delete_sql = os_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".(int)$_POST['current_product_id']."'");
while($delete_res = os_db_fetch_array($delete_sql)) 
{
	os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." WHERE products_attributes_id = '".$delete_res['products_attributes_id']."'");
}

// Удаляем все атрибуты товара
os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".(int)$_POST['current_product_id']."'" );

// Обрабатываем запрос
if (is_array($_POST['attributes']) && !empty($_POST['attributes']))
{
	foreach ($_POST['attributes'] AS $id => $values)
	{
		$cv_id = (int)$id;

		$query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." where products_options_values_id = '".$cv_id ."'");
		while ($line = os_db_fetch_array($query))
		{
			$optionsID = $line['products_options_id'];
		}

		$value_price = $values['price'];

		if (PRICE_IS_BRUTTO=='true')
		{
			$value_price= ($value_price / ((os_get_tax_rate(os_get_tax_class_id($_POST['current_product_id']))) + 100) * 100);
		}
		$value_price = os_round($value_price, PRICE_PRECISION);

		$value_prefix = $values['prefix'];
		$value_sortorder = $values['sortorder'];
		$value_weight_prefix = $values['weight_prefix'];
		$value_model = $values['model'];
		$value_stock = $values['stock'];
		$value_weight = $values['weight'];

		os_db_query("INSERT INTO ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix ,attributes_model, attributes_stock, options_values_weight, weight_prefix,sortorder) VALUES ('".(int)$_POST['current_product_id']."', '".$optionsID."', '".$cv_id."', '".$value_price."', '".$value_prefix."', '".$value_model."', '".$value_stock."', '".$value_weight."', '".$value_weight_prefix."','".$value_sortorder."')") or die(mysql_error());

		// Если это атрибут-файл
		if ($values['download_file'] != '')
		{
			// Получаем ID добавленного атрибута
			$products_attributes_id = os_db_insert_id();

			$value_download_file = $values['download_file'];
			$value_download_expire = $values['download_expire'];
			$value_download_count = $values['download_count'];

			os_db_query("INSERT INTO ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." (products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount) VALUES ('".$products_attributes_id."', '".$value_download_file."', '".$value_download_expire."', '".$value_download_count."')") or die(mysql_error());
		}
	}
}
?>