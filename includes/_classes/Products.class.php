<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiProducts extends CartET
{
	public function getProduct($params)
	{
		if (empty($params)) return false;

		$language_id = (!empty($params['language_id'])) ? (int)$params['language_id'] : (int)$_SESSION['languages_id'];
		$product_id = (int)$params['product_id'];

		$group_check = (GROUP_CHECK == 'true') ? " p.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 AND " : '';
		$fsk_lock = ($_SESSION['customers_status']['customers_fsk18_display'] == '0') ? ' p.products_fsk18! = 1 AND ' : '';

		$product_query = "
		SELECT
			*
		FROM
			".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
		WHERE
			p.products_status = '1' AND
			p.products_id = '".$product_id."' AND
			pd.products_id = p.products_id AND
			".$group_check.$fsk_lock."
			pd.language_id = '".$language_id."'
		";

		$product_query = osDBquery($product_query);

		if (!os_db_num_rows($product_query, true))
			return false;
		else
			return os_db_fetch_array($product_query);
	}


	public function getCategories($categories_array = '', $parent_id = '0', $indent = '')
	{
		$parent_id = os_db_prepare_input($parent_id);

		if (!is_array($categories_array)) $categories_array = array();

		$flip_category_cache = flip_category_cache();

		if (isset($flip_category_cache[os_db_input($parent_id)]))
		{
			foreach ($flip_category_cache[os_db_input($parent_id)] as $categories_id)
			{
				$categories = get_categories_info($categories_id);

				if (!empty($categories['categories_id']))
				{
					$categories_array[] = array(
						'id' => $categories['categories_id'],
						'text' => $indent.$categories['categories_name']
					);
				}

				if ($categories['categories_id'] != $parent_id)
				{
					$categories_array = $this->getCategories($categories_array, $categories_id, $indent.'&nbsp;&nbsp;&nbsp;');
				}
			}
		}

		return $categories_array;
	}

	public function getProductsByCategoryId($c_id, $lang = '')
	{
		$lang = (!empty($lang)) ? $lang : $_SESSION['languages_id'];

		$product_query = os_db_query("
		SELECT 
			*
		FROM
			".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
		WHERE 
			p2c.categories_id = '".(int)$c_id."' AND 
			p.products_id = pd.products_id AND 
			p.products_id = p2c.products_id AND 
			pd.language_id = '".(int)$lang."'
		");

		$product_array = array();
		if (os_db_num_rows($product_query) > 0)
		{
			while ($product = os_db_fetch_array($product_query))
			{
				$product_array[] = $product;
			}
		}
		return $product_array;
	}

	/**
	 * Поиск товара по названию
	 */
	public function searchByName($data = array())
	{
		$lang = (!empty($data['lang'])) ? $data['lang'] : $_SESSION['languages_id'];
		$limit = 50;

		$query = strval(preg_replace('/[^\p{L}\p{Nd}\d\s_\-\.\%\s]/ui', '', $data['query']));

		$searchQuery = os_db_query("
			SELECT 
				distinct * 
			FROM 
				".TABLE_PRODUCTS." p 
					LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd on pd.products_id = p.products_id 
			WHERE 
				pd.products_name LIKE '%".mysql_real_escape_string($query)."%' AND 
				pd.language_id = '".(int)$lang."' 
			ORDER BY 
				pd.products_name DESC limit ".$limit."
		");

		while ($p = os_db_fetch_array($searchQuery, true))
		{
			$products_name[] = $p['products_name'];
			$products_data[] = $p;
		}

		$res['query'] = $query;
		$res['suggestions'] = $products_name;
		$res['data'] = $products_data;
		return $res;
	}

	/**
	 * Статусы категории
	 */
	public function changeCategoryStatus($post)
	{
		if (is_array($post))
		{
			$result = os_db_query("UPDATE ".TABLE_CATEGORIES." SET ".os_db_prepare_input($post['column'])." = '".(int)$post['status']."' WHERE categories_id = '".(int)$post['id']."'");
			if ($result)
				$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
			else
				$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Статусы товаров
	 */
	public function changeProductStatus($post)
	{
		if (is_array($post))
		{
			$result = os_db_query("UPDATE ".TABLE_PRODUCTS." SET ".os_db_prepare_input($post['column'])." = '".(int)$post['status']."' WHERE products_id = '".(int)$post['id']."'");
			if ($result)
				$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
			else
				$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Назначение или снятие атрибутов к товару
	 */
	public function setAttributes($post)
	{
		if (is_array($post))
		{
			// Удаляем атрибуты-файлы
			$delete_sql = os_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".(int)$post['current_product_id']."'");
			while($delete_res = os_db_fetch_array($delete_sql)) 
			{
				os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." WHERE products_attributes_id = '".$delete_res['products_attributes_id']."'");
			}

			// Удаляем все атрибуты товара
			os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".(int)$post['current_product_id']."'" );

			// Обрабатываем запрос
			if (is_array($post['attributes']) && !empty($post['attributes']))
			{
				$sqlAttribytes = array();
				foreach ($post['attributes'] AS $id => $values)
				{
					$query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WHERE products_options_values_id = '".(int)$id."'");
					while ($line = os_db_fetch_array($query))
					{
						$optionsID = $line['products_options_id'];
					}

					$value_price = $values['price'];

					if (PRICE_IS_BRUTTO=='true')
					{
						$value_price= ($value_price / ((os_get_tax_rate(os_get_tax_class_id($post['current_product_id']))) + 100) * 100);
					}
					$value_price = os_round($value_price, PRICE_PRECISION);

					$sqlAttribytes = array(
						'products_id' => (int)$post['current_product_id'],
						'options_id' => $optionsID,
						'options_values_id' => (int)$id,
						'options_values_price' => $value_price,
						'price_prefix' => $values['prefix'],
						'attributes_model' => $values['model'],
						'attributes_stock' => $values['stock'],
						'options_values_weight' => $values['weight'],
						'weight_prefix' => $values['weight_prefix'],
						'sortorder' => $values['sortorder'],
					);
					os_db_perform(TABLE_PRODUCTS_ATTRIBUTES, $sqlAttribytes);

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
				$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
			}
			else
				$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Изменить статус дополнительных полей
	 */
	public function statusExtraFields($post)
	{
		if (is_array($post))
		{
			os_db_query("UPDATE ".TABLE_PRODUCTS_EXTRA_FIELDS." SET products_extra_fields_status = '".(int)$post['status']."' WHERE products_extra_fields_id = '".(int)$post['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Массовое сохранение категорий и товаров
	 */
	public function saveList($params)
	{
		if (is_array($params))
		{
			// сохраняем категории
			if (is_array($params['categories']))
			{
				foreach ($params['categories'] AS $catId => $values)
				{
					$sqlDataCat = array
					(
						'sort_order' => (int)$values['sort_order'],
					);
					os_db_perform(TABLE_CATEGORIES, $sqlDataCat, 'update', "categories_id = '".(int)$catId."'");

					$sqlDataCatDesc = array
					(
						'categories_name' => os_db_prepare_input($values['categories_name']),
					);
					os_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sqlDataCatDesc, 'update', "categories_id = '".(int)$catId."' AND language_id = '".$_SESSION['languages_id']."'");
				}
				$this->product->updateCategoriesTree();
			}
			// сохраняем товары
			if (is_array($params['products']))
			{
				foreach ($params['products'] AS $prodId => $values)
				{
					$sqlDataCat = array
					(
						'products_price' => os_db_prepare_input($values['products_price']),
						'products_sort' => (int)$values['products_sort'],
						'products_shippingtime' => (int)$values['products_shippingtime'],
						'products_model' => os_db_prepare_input($values['products_model']),
					);
					if (STOCK_CHECK == 'true')
					{
						$sqlDataCat['products_quantity'] = $values['products_quantity'];
					}
					os_db_perform(TABLE_PRODUCTS, $sqlDataCat, 'update', "products_id = '".(int)$prodId."'");

					$sqlDataCatDesc = array
					(
						'products_name' => os_db_prepare_input($values['products_name']),
					);
					os_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sqlDataCatDesc, 'update', "products_id = '".(int)$prodId."' AND language_id = '".$_SESSION['languages_id']."'");
				}
			}

			$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Массовое перемещение категорий и товаров
	 */
	public function multiMove($params)
	{
		if (is_array($params['multi_categories']) && os_not_null($params['move_to_category_id']))
		{
			foreach ($params['multi_categories'] AS $category_id)
			{
				if ($category_id != $params['move_to_category_id'])
				{
					$this->moveCategory($category_id, $params['move_to_category_id']);
				}
			}
		}

		if (is_array($params['multi_products']) && os_not_null($params['move_to_category_id']) && os_not_null($params['cPath']))
		{
			foreach ($params['multi_products'] AS $product_id)
			{
				$this->moveProduct($product_id, $params['move_to_category_id']);
			}
		}

		set_products_url_cache();
		set_categories_url_cache();
		set_category_cache();

		$data = array('msg' => 'Успешно перенесено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Перемещение категории
	 */
	public function moveCategory($src_category_id, $dest_category_id)
	{
		os_db_query("UPDATE ".TABLE_CATEGORIES." SET parent_id = '".(int)$dest_category_id."', last_modified = now() WHERE categories_id = '".(int)$src_category_id."'");

		$this->product->updateCategoriesTree();
		do_action('move_category');
	}

	/**
	 * Перемещение товара
	 */
	public function moveProduct($src_products_id, $dest_category_id)
	{
		$duplicate_check_query = os_db_query("SELECT COUNT(*) AS total FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".(int)$src_products_id."' AND categories_id = '".(int)$dest_category_id."'");
		$duplicate_check = os_db_fetch_array($duplicate_check_query);

		if ($duplicate_check['total'] < 1)
		{
			os_db_query("UPDATE ".TABLE_PRODUCTS_TO_CATEGORIES." SET categories_id = '".(int)$dest_category_id."' WHERE products_id = '".(int)$src_products_id."'");

			/*if ($dest_category_id == 0)
			{
				$this->changeProductStatus(array('id' => $src_products_id, 'status' => 1, 'column' => 'products_status'));
				$this->changeProductStatus(array('id' => $src_products_id, 'status' => 1, 'column' => 'products_startpage'));
			}

			if ($src_category_id == 0)
			{
				$this->changeProductStatus(array('id' => $src_products_id, 'status' => 0, 'column' => 'products_status'));
				$this->changeProductStatus(array('id' => $src_products_id, 'status' => 0, 'column' => 'products_startpage'));
			}*/
		}

		do_action('move_product');
	}

	/**
	 * Массовое копирование категорий и товаров
	 */
	public function multiCopy($params)
	{
		if (is_array($params['multi_categories']) && (is_array($params['dest_cat_ids'])))
		{
			foreach ($params['multi_categories'] AS $category_id)
			{
				if (is_array($params['dest_cat_ids']))
				{
					foreach ($params['dest_cat_ids'] AS $dest_category_id)
					{
						if ($params['copy_as'] == 'link')
							$this->copyCategory($category_id, $dest_category_id, 'link');
						elseif ($params['copy_as'] == 'duplicate')
							$this->copyCategory($category_id, $dest_category_id, 'duplicate');
						else
							$data = array('msg' => 'Copy type not specified.', 'type' => 'error');
					}
				}
			}
		}

		if (is_array($params['multi_products']) && (is_array($params['dest_cat_ids'])))
		{
			foreach ($params['multi_products'] AS $product_id)
			{
				if (is_array($params['dest_cat_ids']))
				{
					foreach ($params['dest_cat_ids'] AS $dest_category_id)
					{
						if ($params['copy_as'] == 'link')
							$this->linkProduct($product_id, $dest_category_id);
						elseif ($params['copy_as'] == 'duplicate')
							$this->duplicateProduct(array('product_id' => $product_id, 'categories_id' => $dest_category_id));
						else
							$data = array('msg' => 'Copy type not specified.', 'type' => 'error');
					}
				}
			}
		}

		set_products_url_cache();
		set_categories_url_cache();
		set_category_cache();

		$data = array('msg' => 'Успешно скопировано!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Копирование категории
	 */
	public function copyCategory($src_category_id, $dest_category_id, $ctype = "link")
	{
		if (!(in_array($src_category_id, $_SESSION['copied'])))
		{
			$ccopy_query = os_db_query("SELECT * FROM ".TABLE_CATEGORIES." WHERE categories_id = '".(int)$src_category_id."'");
			$ccopy_values = os_db_fetch_array($ccopy_query);

			$cdcopy_query = os_db_query("SELECT * FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = '".(int)$src_category_id."'");

			$sql_data_array = array(
				'parent_id' => (int)$dest_category_id,
				'date_added' => 'NOW()',
				'last_modified' => 'NOW()',
				'categories_image' => $ccopy_values['categories_image'],
				'categories_status' => $ccopy_values['categories_status'],
				'categories_template' => $ccopy_values['categories_template'],
				'listing_template' => $ccopy_values['listing_template'],
				'sort_order' => $ccopy_values['sort_order'],
				'products_sorting' => $ccopy_values['products_sorting'],
				'products_sorting2' => $ccopy_values['products_sorting2']
			);

			$customers_statuses_array = os_get_customers_statuses();

			for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++)
			{
				if (isset($customers_statuses_array[$i]['id']))
					$sql_data_array = array_merge($sql_data_array, array('group_permission_'.$customers_statuses_array[$i]['id'] => $product['group_permission_'.$customers_statuses_array[$i]['id']]));
			}

			os_db_perform(TABLE_CATEGORIES, $sql_data_array);

			$new_cat_id = os_db_insert_id();
			$_SESSION['copied'][] = $new_cat_id;
			$get_prod_query = os_db_query("SELECT products_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id = '".(int)$src_category_id."'");
			while ($product = os_db_fetch_array($get_prod_query)) {
				if ($ctype == 'link')
					$this->linkProduct($product['products_id'], $new_cat_id);
				elseif ($ctype == 'duplicate')
					$this->duplicateProduct(array('product_id' => $product['products_id'], 'categories_id' => $new_cat_id));
				else
					die('Undefined copy type!');
			}

			$src_pic = dir_path('images').'categories/'.$ccopy_values['categories_image'];
			if (is_file($src_pic))
			{
				$get_suffix = explode('.', $ccopy_values['categories_image']);
				$suffix = array_pop($get_suffix);
				$dest_pic = $new_cat_id.'.'.$suffix;
				@copy($src_pic, dir_path('images').'categories/'.$dest_pic);
				os_db_query("UPDATE ".DB_PREFIX."categories SET categories_image = '".$dest_pic."' WHERE categories_id = '".(int)$new_cat_id."'");
			}

			while ($cdcopy_values = os_db_fetch_array($cdcopy_query))
			{
				os_db_query("INSERT INTO ".TABLE_CATEGORIES_DESCRIPTION." (categories_id, language_id, categories_name, categories_heading_title, categories_description, categories_meta_title, categories_meta_description, categories_meta_keywords) VALUES ('".(int)$new_cat_id."' , '".$cdcopy_values['language_id']."' , '".addslashes($cdcopy_values['categories_name'])."' , '".addslashes($cdcopy_values['categories_heading_title'])."' , '".addslashes($cdcopy_values['categories_description'])."' , '".addslashes($cdcopy_values['categories_meta_title'])."' , '".addslashes($cdcopy_values['categories_meta_description'])."' , '".addslashes($cdcopy_values['categories_meta_keywords'])."')");
			}

			$crcopy_query = os_db_query("SELECT categories_id FROM ".TABLE_CATEGORIES." WHERE parent_id = '".(int)$src_category_id."'");
			while ($crcopy_values = os_db_fetch_array($crcopy_query))
			{
				$this->copyCategory($crcopy_values['categories_id'], $new_cat_id, $ctype);
			}

		}

		$this->product->updateCategoriesTree();
		do_action('copy_category');
	}

	/**
	 * Ссылка на товар
	 */
	public function linkProduct($src_products_id, $dest_categories_id)
	{
		if (empty($src_products_id)) return false;

		$check_query = os_db_query("SELECT COUNT(*) AS total FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".(int)$src_products_id."' AND categories_id = '".(int)$dest_categories_id."'");
		$check = os_db_fetch_array($check_query);

		if ($check['total'] < '1')
		{
			os_db_query("INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES." SET products_id = '".os_db_input($src_products_id)."', categories_id = '".os_db_input($dest_categories_id)."'");

			/*if ($dest_categories_id == 0)
			{
				$this->changeProductStatus(array('id' => $src_products_id, 'status' => 1, 'column' => 'products_status'));
				$this->changeProductStatus(array('id' => $src_products_id, 'status' => 1, 'column' => 'products_startpage'));
			}*/
		}
	}

	/**
	 * Копирование товара
	 */
	public function duplicateProduct($params)
	{
		if (empty($params)) return false;

		$src_products_id = $params['product_id'];
		$dest_categories_id = $params['categories_id'];

		$product_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." WHERE products_id = '".(int)$src_products_id."'");
		$product = os_db_fetch_array($product_query);

		$sql_data_array = array();
		foreach($product AS $k => $v)
		{
			if ($k != 'products_id' && $k != 'products_page_url')
			{
				$sql_data_array[$k] = $v;
			}
		}

		os_db_perform(TABLE_PRODUCTS, $sql_data_array);
		$dup_products_id = os_db_insert_id();

		if ($product['products_image'] != '')
		{
			$pname_arr = explode('.', $product['products_image']);
			$nsuffix = array_pop($pname_arr);
			$dup_products_image_name = $dup_products_id.'_0'.'.'.$nsuffix;
			os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_image = '".$dup_products_image_name."' WHERE products_id = '".$dup_products_id."'");

			@copy(dir_path('images_original').'/'.$product['products_image'], dir_path('images_original').'/'.$dup_products_image_name);
			@copy(dir_path('images_info').'/'.$product['products_image'], dir_path('images_info').'/'.$dup_products_image_name);
			@copy(dir_path('images_thumbnail').'/'.$product['products_image'], dir_path('images_thumbnail').'/'.$dup_products_image_name);
			@copy(dir_path('images_popup').'/'.$product['products_image'], dir_path('images_popup').'/'.$dup_products_image_name);
		}
		else
		{
			unset($dup_products_image_name);
		}

		$description_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = '".(int)$src_products_id."'");

		$old_products_id = os_db_input($src_products_id);
		while ($description = os_db_fetch_array($description_query))
		{
			os_db_query("INSERT INTO ".TABLE_PRODUCTS_DESCRIPTION."
						    		                 SET products_id                = '".$dup_products_id."',
						    		                     language_id                = '".$description['language_id']."',
						    		                     products_name              = '".addslashes($description['products_name'])."',
						    		                     products_description       = '".addslashes($description['products_description'])."',
						    		                     products_keywords          = '".addslashes($description['products_keywords'])."',
						    		                     products_short_description = '".addslashes($description['products_short_description'])."',
						    		                     products_meta_title        = '".addslashes($description['products_meta_title'])."',
						    		                     products_meta_description  = '".addslashes($description['products_meta_description'])."',
						    		                     products_meta_keywords     = '".addslashes($description['products_meta_keywords'])."',
						    		                     products_url               = '".$description['products_url']."',
						    		                     products_viewed            = '0'");
		}

		os_db_query("INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES." SET products_id = '".$dup_products_id."', categories_id = '".os_db_input($dest_categories_id)."'");

		$mo_images = os_get_products_mo_images($src_products_id);
		if (is_array($mo_images))
		{
			foreach ($mo_images AS $dummy => $mo_img)
			{
				$pname_arr = explode('.', $mo_img['image_name']);
				$nsuffix = array_pop($pname_arr);
				$dup_products_image_name = $dup_products_id.'_'.$mo_img['image_nr'].'.'.$nsuffix;
				@copy(dir_path('images_original').'/'.$mo_img['image_name'], dir_path('images_original').'/'.$dup_products_image_name);
				@copy(dir_path('images_info').'/'.$mo_img['image_name'], dir_path('images_info').'/'.$dup_products_image_name);
				@copy(dir_path('images_thumbnail').'/'.$mo_img['image_name'], dir_path('images_thumbnail').'/'.$dup_products_image_name);
				@copy(dir_path('images_popup').'/'.$mo_img['image_name'], dir_path('images_popup').'/'.$dup_products_image_name);

				os_db_query("INSERT INTO ".TABLE_PRODUCTS_IMAGES." SET products_id = '".$dup_products_id."', image_nr = '".$mo_img['image_nr']."', image_name = '".$dup_products_image_name."'");
			}
		}

		// Копирование доп. полей
		$extraFieldsQuery = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." WHERE products_id = ".$src_products_id."");
		if (os_db_num_rows($extraFieldsQuery) > 0)
		{
			while ($extraFields = os_db_fetch_array($extraFieldsQuery))
			{
				$sql_data_array = array(
					'products_id' => $dup_products_id,
					'products_extra_fields_id' => os_db_prepare_input($extraFields['products_extra_fields_id']),
					'products_extra_fields_value' => os_db_prepare_input($extraFields['products_extra_fields_value'])
				);
				os_db_perform(TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS, $sql_data_array);
			}
		}

		$i = 0;
		$group_query = os_db_query("SELECT customers_status_id FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id = '".(int)$_SESSION['languages_id']."' AND customers_status_id != '0'");

		while ($group_values = os_db_fetch_array($group_query))
		{
			$i ++;
			$group_data[$i] = array ('STATUS_ID' => $group_values['customers_status_id']);
		}

		for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++)
		{
			if ($group_data[$col]['STATUS_ID'] != '')
			{
				$copy_query = os_db_query("SELECT quantity, personal_offer FROM ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']." WHERE products_id = '".$old_products_id."'");

				while ($copy_data = os_db_fetch_array($copy_query))
				{
					os_db_query("INSERT INTO ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']." SET price_id = '', products_id = '".$dup_products_id."', quantity = '".$copy_data['quantity']."', personal_offer = '".$copy_data['personal_offer']."'");
				}
			}
		}

		$data = array('msg' => 'Скопировано!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Массовое удаление категорий и товаров
	 */
	public function multiDelete($params)
	{
		if (is_array($params['multi_categories']))
		{
			foreach ($params['multi_categories'] AS $category_id)
			{
				$this->deleteCategories($category_id);
			}
		}

		if (is_array($params['multi_products']) && is_array($params['multi_products_categories']))
		{
			foreach ($params['multi_products'] AS $product_id)
			{
				$this->deleteProducts($product_id, $params['multi_products_categories'][$product_id]);
			}
		}

		set_products_url_cache();
		set_categories_url_cache();

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление категорий с товарами
	 */
	public function deleteCategories($category_id)
	{
		$categories = os_get_category_tree($category_id, '', '0', '', true);
		$products = array ();
		$products_delete = array ();

		for ($i = 0, $n = sizeof($categories); $i < $n; $i ++)
		{
			$product_ids_query = os_db_query("SELECT products_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id = '".$categories[$i]['id']."'");
			while ($product_ids = os_db_fetch_array($product_ids_query))
			{
				$products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
			}
		}

		reset($products);
		while (list ($key, $value) = each($products))
		{
			$category_ids = '';
			for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i ++)
			{
				$category_ids .= '\''.$value['categories'][$i].'\', ';
			}
			$category_ids = substr($category_ids, 0, -2);

			$check_query = os_db_query("SELECT COUNT(*) AS total FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".$key."' AND categories_id NOT IN (".$category_ids.")");
			$check = os_db_fetch_array($check_query);
			if ($check['total'] < '1')
			{
				$products_delete[$key] = $key;
			}
		}

		@os_set_time_limit(0);
		for ($i = 0, $n = sizeof($categories); $i < $n; $i ++)
		{
			$this->deleteCategory($categories[$i]['id']);
		}

		reset($products_delete);
		while (list ($key) = each($products_delete))
		{
			$this->deleteProduct($key);
		}

		do_action('remove_categories');
	}

	/**
	 * Удаление категории
	 */
	public function deleteCategory($category_id)
	{
		$category_image_query = os_db_query("SELECT categories_image FROM ".TABLE_CATEGORIES." WHERE categories_id = '".os_db_input($category_id)."'");
		$category_image = os_db_fetch_array($category_image_query);

		$duplicate_image_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_CATEGORIES." WHERE categories_image = '".os_db_input($category_image['categories_image'])."'");
		$duplicate_image = os_db_fetch_array($duplicate_image_query);

		if ($duplicate_image['total'] < 2) {
			if (file_exists(dir_path('images').'categories/'.$category_image['categories_image'])) {
				@unlink(dir_path('images').'categories/'.$category_image['categories_image']);
			}
		}

		os_db_query("DELETE FROM ".TABLE_CATEGORIES." WHERE categories_id = '".os_db_input($category_id)."'");
		os_db_query("DELETE FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = '".os_db_input($category_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id = '".os_db_input($category_id)."'");

		if (USE_CACHE == 'true')
		{
			os_reset_cache_block('categories');
			os_reset_cache_block('also_purchased');
		}

		global $categories_id;
		$categories_id = os_db_input($category_id);

		$this->product->updateCategoriesTree();
		do_action('remove_category');
	}

	/**
	 * Удаление товаров
	 */
	public function deleteProducts($product_id, $product_categories)
	{
		for ($i = 0, $n = sizeof($product_categories); $i < $n; $i ++)
		{
			os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".(int)$product_id."' AND categories_id = '".(int)$product_categories[$i]."'");
			/*if (($product_categories[$i]) == 0) {
				$this->set_product_startpage($product_id, 0);
			}*/
		}

		$product_categories_query = os_db_query("SELECT COUNT(*) AS total FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".(int)$product_id."'");
		$product_categories = os_db_fetch_array($product_categories_query);
		if ($product_categories['total'] == '0')
		{
			$this->deleteProduct($product_id);
		}

		do_action('delete_product');
	}

	/**
	 * Удаление товара
	 */
	public function deleteProduct($product_id)
	{
		// Файл информационных страниц
		$product_content_query = os_db_query("SELECT content_file FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id = '".os_db_input($product_id)."'");
		while ($product_content = os_db_fetch_array($product_content_query)) {

			$duplicate_content_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_PRODUCTS_CONTENT." WHERE content_file = '".os_db_input($product_content['content_file'])."' AND products_id != '".os_db_input($product_id)."'");

			$duplicate_content = os_db_fetch_array($duplicate_content_query);

			if ($duplicate_content['total'] == 0)
			{
				@unlink(DIR_FS_DOCUMENT_ROOT.'media/products/'.$product_content['content_file']);
			}

			os_db_query("DELETE FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id = '".os_db_input($product_id)."' AND (content_file = '".$product_content['content_file']."' OR content_file = '')");

		}

		// Изображения
		$product_image_query = os_db_query("SELECT products_image FROM ".TABLE_PRODUCTS." WHERE products_id = '".os_db_input($product_id)."'");
		$product_image = os_db_fetch_array($product_image_query);

		// Проверяем дубликаты в копиях товара
		$duplicate_image_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_PRODUCTS." WHERE products_image = '".os_db_input($product_image['products_image'])."'");
		$duplicate_image = os_db_fetch_array($duplicate_image_query);

		if ($duplicate_image['total'] < 2)
		{
			os_del_image_file($product_image['products_image']);
		}

		$mo_images_query = os_db_query("SELECT image_name FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".os_db_input($product_id)."'");
		while ($mo_images_values = os_db_fetch_array($mo_images_query))
		{
			$duplicate_more_image_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_PRODUCTS_IMAGES." WHERE image_name = '".$mo_images_values['image_name']."'");
			$duplicate_more_image = os_db_fetch_array($duplicate_more_image_query);
			if ($duplicate_more_image['total'] < 2)
			{
				os_del_image_file($mo_images_values['image_name']);
			}
		}

		// Разные связи
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_SPECIALS." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".DB_PREFIX."products_bundles WHERE bundle_id = '".os_db_input($product_id)."'");

		// Скидки групп
		$customers_status_array = os_get_customers_statuses();
		for ($i = 0, $n = sizeof($customers_status_array); $i < $n; $i ++)
		{
			if (isset($customers_statuses_array[$i]['id']))
				os_db_query("delete from ".TABLE_PERSONAL_OFFERS.$customers_statuses_array[$i]['id']." where products_id = '".os_db_input($product_id)."'");
		}

		// Отзывы
		$product_reviews_query = os_db_query("select reviews_id from ".TABLE_REVIEWS." where products_id = '".os_db_input($product_id)."'");
		while ($product_reviews = os_db_fetch_array($product_reviews_query))
		{
			os_db_query("delete from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$product_reviews['reviews_id']."'");
		}

		os_db_query("delete from ".TABLE_REVIEWS." where products_id = '".os_db_input($product_id)."'");

		if (USE_CACHE == 'true')
		{
			os_reset_cache_block('categories');
			os_reset_cache_block('also_purchased');
		}

		global $products_id;
		$products_id = os_db_input($product_id);

		//do_action('remove_product');
	}

	/**
	 * Удаление изображений
	 */
	public function deleteImages($params)
	{
		if (!empty($params['image_delete']))
		{
			os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_image = '' WHERE products_id = '".(int)$params['products_id']."'");
			$this->deleteImageFile($params['image_delete']);
		}

		if (is_array($params['images_delete']) && !empty($params['images_delete']))
		{
			foreach ($params['images_delete'] AS $img)
			{
				os_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".(int)$params['products_id']."' AND image_name  = '".os_db_prepare_input($img)."'");
				$this->deleteImageFile($img);
			}
		}
	}

	/**
	 * Удаление файлов изображений
	 */
	public function deleteImageFile($params)
	{
		if (is_file(dir_path('images_popup').$params))
		{
			unlink(dir_path('images_popup').$params);
		}

		if (is_file(dir_path('images_original').$params))
		{
			unlink(dir_path('images_original').$params);
		}

		if (is_file(dir_path('images_thumbnail').$params))
		{
			unlink(dir_path('images_thumbnail').$params);
		}

		if (is_file(dir_path('images_info').$params))
		{
			unlink(dir_path('images_info').$params);
		}
	}

	/**
	 * Сохранение данных изображения через быстрое редактирование
	 */
	public function saveImageValue($params)
	{
		if (empty($params['pk']) OR empty($params['name'])) return false;

		$mo_img[$params['name']] = os_db_prepare_input($params['value']);
		$result = os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img, 'update', "image_id = '".(int)$params['pk']."'");

		if ($result)
			$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Установка главной картинки товара
	 */
	public function setMainImage($params)
	{
		$_products = os_db_query('select products_id, products_image from '.DB_PREFIX.'products where products_id='.(int)$params['products_id'].';');
		$_pro = os_db_fetch_array($_products, false);

		$main_image= '';
		if (!empty($_pro['products_image']))
		{
			$main_image = $_pro['products_image'];
		}

		$main_image = @trim($main_image);

		$__products = os_db_query('select products_id, image_name from '.DB_PREFIX.'products_images where image_id='.(int)$params['image_id'].' limit 1;');
		$pro = os_db_fetch_array($__products, false);

		if (!empty($pro['image_name'])) 
		{
			os_db_query('update '.DB_PREFIX.'products set products_image="'.$pro['image_name'].'" where products_id='.(int)$params['products_id'].';'); 

			if (!empty($main_image))
				os_db_query('update '.DB_PREFIX.'products_images set image_name="'.$main_image.'" where image_id='.(int)$params['image_id'].' limit 1;');
			else
				os_db_query('delete from '.DB_PREFIX.'products_images where image_id='.(int)$params['image_id']);

			$data = array('msg' => 'Главная картинка установлена!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Главная картинка не установлена!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Загрузка изображений из ПС
	 */
	public function getImages($params)
	{
		$params['product_name'] = str_replace(' ', '+', $params['product_name']);

		$start = 0;
		if (isset($params['start']))
		{
			$start = intval($params['start']);
		}

		$url = 'http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q='.urlencode($params['product_name']).'&start='.$start.'&rsz=8';

		if (function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			$page = curl_exec($ch);
			curl_close($ch);
		}
		else
			$page = file_get_contents($url);

		$result = json_decode($page);

		$images = array();
		if ($result)
		{
			foreach($result->responseData->results as $m)
			{
				$images[] = urldecode(str_replace('%2520', '%20', $m->url));
			}
		}

		return $images;
	}

	/**
	 * Загрузка описания доп. полей
	 */
	public function getEfInfo($params)
	{
		$keywords = str_replace(' ', '+', $params['keyword']);

		$search = "http://market.yandex.ru/search.xml?text=$keywords&nopreciser=1";

		$url = curl_get($search);

		if(preg_match_all('/<h3 class="b-offers__title"><a href="(.*?)" class="b-offers__name">/ui', $url, $matches))
			$product_url = 'http://market.yandex.ru'.reset($matches[1]);
		else
			return false;

		$url = curl_get($product_url);

		if (preg_match_all('/<ul class="b-vlist b-vlist_type_mdash b-vlist_type_friendly">(.*?)/ui', $url, $matches))
		{
			if (preg_match_all('/<p class="b-model-friendly__title"><a href="(.*?)">/ui', $url, $matches))
			{
				$options_url = 'http://market.yandex.ru'.reset($matches[1]);

				$options_page = curl_get($options_url);
				preg_match_all('/<th class="b-properties__label b-properties__label-title"><span>(.*?)<\/span><\/th><td class="b-properties__value">(.*?)<\/td>/ui', $options_page, $matches, PREG_SET_ORDER);

				$options = array();
				foreach($matches as $m)
				{
					$options[] = array('name' => trim($m[1]), 'value' => trim($m[2]));
				}
				$result['options'] = $options;
			}
		}

		return $result;
	}

	/**
	 * Массовое редактирование товаров
	 */
	public function saveQuickUpdates($params)
	{
		if (empty($params)) return false;

		$i = 0;
		$group_query = os_db_query("SELECT customers_status_id FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id = '".(int) $_SESSION['languages_id']."' AND customers_status_id != '0'");
		while ($group_values = os_db_fetch_array($group_query))
		{
			$i++;
			$group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
		}

		if (is_array($params))
		{
			foreach ($params AS $prodId => $values)
			{
				$sqlDataCat = array
				(
					'products_price' => os_db_prepare_input($values['products_price']),
					'products_sort' => (int)$values['products_sort'],
					'products_shippingtime' => (int)$values['products_shippingtime'],
				);
				if (STOCK_CHECK == 'true')
				{
					$sqlDataCat['products_quantity'] = $values['products_quantity'];
				}
				os_db_perform(TABLE_PRODUCTS, $sqlDataCat, 'update', "products_id = '".(int)$prodId."'");

				for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++)
				{
					if (@$group_data[$col]['STATUS_ID'] != '')
					{
						$personal_price = os_db_prepare_input($values['products_price_'.$group_data[$col]['STATUS_ID']]);
						if ($personal_price == '' || $personal_price == '0.0000')
						{
							$personal_price = '0.00';
						}
						else
						{
							if (PRICE_IS_BRUTTO == 'true')
							{
								$personal_price = ($personal_price / (os_get_tax_rate($values['products_tax_class_id']) + 100) * 100);
							}
							$personal_price = os_round($personal_price, PRICE_PRECISION);
						}

						os_db_query("UPDATE ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']." SET personal_offer = '".$personal_price."' WHERE products_id = '".(int)$prodId."' AND quantity = '1'");
					}
				}
			}

			$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}
}