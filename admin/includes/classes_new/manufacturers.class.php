<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class manufacturers extends CartET
{

	/**
	 * Получить производителя по ID
	 */
	function getById($m_id)
	{
		$manufacturer = '';
		$manufacturerQuery = os_db_query("SELECT * FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_id = '".(int)$m_id."'");
		if (os_db_num_rows($manufacturerQuery) > 0)
		{
			$manufacturer = os_db_fetch_array($manufacturerQuery);
		}

		return $manufacturer;
	}

	/**
	 * Возвращает массив с описанием производителей на разных языках
	 */
	function getInfoById($m_id, $lang = '')
	{
		$languages = ($lang) ? " AND languages_id = '".(int)$lang."' " : "";
		$manufacturer_query = os_db_query("SELECT * FROM ".TABLE_MANUFACTURERS_INFO." WHERE manufacturers_id = '".(int)$m_id."' ".$languages." ");
		$aManufacturersInfo = array();
		if (os_db_num_rows($manufacturer_query) > 0)
		{
			while ($manufacturer = os_db_fetch_array($manufacturer_query))
			{
				$aManufacturersInfo[$manufacturer['languages_id']] = $manufacturer;
			}
		}

		return $aManufacturersInfo;
	}

	/**
	 * Возвращает количество товаров связанных с производителем
	 */
	function getProductsCount($m_id)
	{
		$productsQuery = os_db_query("SELECT count(products_id) as products_count from ".TABLE_PRODUCTS." where manufacturers_id = '".$m_id."'");
		$products = os_db_fetch_array($productsQuery);
		return $products['products_count'];
	}

	/**
	 * Сохранение производителя
	 */
	public function save($post)
	{
		$languages = os_get_languages();
		$error = false;
		$data = array();
		if (isset($post) && !empty($post))
		{
			$action = (isset($post['manufacturer_id']) && !empty($post['manufacturer_id'])) ? 'save' : 'insert';

			$manufacturers_id = os_db_prepare_input($post['manufacturer_id']);
			$manufacturers_name = os_db_prepare_input($post['manufacturers_name']);
			$manufacturers_page_url = os_db_prepare_input($post['manufacturers_page_url']);

			$sql_data_array = array
			(
				'manufacturers_name' => $manufacturers_name,
				'manufacturers_page_url' => $manufacturers_page_url
			);

			if ($action == 'insert')
			{
				$insert_sql_data = array('date_added' => 'now()');
				$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
				$result = os_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
				if (!$result) { $error = true; }
				$manufacturers_id = os_db_insert_id();
			}
			elseif ($action == 'save')
			{
				$manufacturers_image = $post['manufacturers_image_current'];
				// Удаляем изображение
				if ($post['delete_image'] == 'on')
				{
					$manufacturers_image = '';

					$image_location = get_path('images').'manufacturers/'.$post['manufacturers_image_current'];
					if (is_file($image_location))
						@unlink($image_location);
				}

				$update_sql_data = array('last_modified' => 'now()', 'manufacturers_image' => $manufacturers_image);
				$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
				$result = os_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '".os_db_input($manufacturers_id)."'");
				if (!$result) { $error = true; }
			}

			$dir_manufacturers = get_path('images')."manufacturers";
			if ($manufacturers_image = &os_try_upload('manufacturers_image', $dir_manufacturers))
			{
				$result = os_db_query("update ".TABLE_MANUFACTURERS." set manufacturers_image ='".$manufacturers_image->filename."' where manufacturers_id = '".os_db_input($manufacturers_id)."'");
				if (!$result) { $error = true; }
			}

			for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
			{
				$manufacturers_url_array = $post['manufacturers_url'];

				// BOF manufacturers descriptions + meta tags
				$manufacturers_meta_title_array = $post['manufacturers_meta_title'];
				$manufacturers_meta_keywords_array = $post['manufacturers_meta_keywords'];
				$manufacturers_meta_description_array = $post['manufacturers_meta_description'];
				$manufacturers_description_array = $post['manufacturers_description'];					

				// EOF manufacturers descriptions + meta tags
				$language_id = $languages[$i]['id'];

				$sql_data_array = array('manufacturers_url' => os_db_prepare_input($manufacturers_url_array[$language_id]));

				// BOF manufacturers descriptions + meta tags

				$sql_data_array = array_merge($sql_data_array, array('manufacturers_meta_title' => os_db_prepare_input($manufacturers_meta_title_array[$language_id]),'manufacturers_meta_keywords' => os_db_prepare_input($manufacturers_meta_keywords_array[$language_id]),'manufacturers_meta_description' => os_db_prepare_input($manufacturers_meta_description_array[$language_id]),'manufacturers_description' => os_db_prepare_input($manufacturers_description_array[$language_id]),));

				// EOF manufacturers descriptions + meta tags
				if ($action == 'insert')
				{
					$insert_sql_data = array(
						'manufacturers_id' => $manufacturers_id,
						'languages_id' => $language_id
					);
					$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
					$result = os_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
				}
				elseif ($action == 'save')
				{
					$result = os_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '".os_db_input($manufacturers_id)."' and languages_id = '".$language_id."'");
				}
				if (!$result) { $error = true; }
			}

			if (USE_CACHE == 'true')
			{
				os_reset_cache_block('manufacturers');
			}

			if ($error == false)
			{
				$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
			}
			else
				$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Удаление производителя
	 */
	function delete($post)
	{
		$manufacturers_id = os_db_prepare_input($post['manufacturer_id']);

		// Удаляем изображение
		if ($post['delete_image'] == 'on')
		{
			$manufacturer_query = os_db_query("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int)$manufacturers_id."'");
			$manufacturer = os_db_fetch_array($manufacturer_query);
			$image_location = get_path('images').'manufacturers/'.$manufacturer['manufacturers_image'];
			if (is_file($image_location))
				@unlink($image_location);
		}

		// Удаляем самого производителя
		os_db_query("delete from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int)$manufacturers_id."'");
		os_db_query("delete from ".TABLE_MANUFACTURERS_INFO." where manufacturers_id = '".(int)$manufacturers_id."'");

		// Удаляем товары, если нужно
		if ($post['delete_products'] == 'on')
		{
			$products_query = os_db_query("select products_id from ".TABLE_PRODUCTS." where manufacturers_id = '".(int)$manufacturers_id."'");

			$_remove_products_array = array();

			while ($products = os_db_fetch_array($products_query)) 
			{
				$_remove_products_array[] = $products['products_id'];
			}

			include(_CLASS.'product.php');
			$product = new product();

			$product->remove($_remove_products_array);
		}
		// Либо удаляем производителя у товаров
		else 
		{
			os_db_query("update ".TABLE_PRODUCTS." set manufacturers_id = '' where manufacturers_id = '".(int)$manufacturers_id."'");
		}

		// Обновляем кэш
		if (USE_CACHE == 'true')
		{
			os_reset_cache_block('manufacturers');
		}

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		return $data;
	}

}