<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class specials extends CartET
{
	/**
	 * Получить скидку по ID для товара
	 */
	public function getById($s_id, $lang = '')
	{
		$lang = (!empty($lang)) ? $lang : $_SESSION['languages_id'];
		$product_query = os_db_query("
		SELECT 
			*
		FROM
			".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_SPECIALS." s 
		WHERE 
			p.products_id = pd.products_id AND 
			pd.language_id = '".(int)$lang."' AND 
			p.products_id = s.products_id AND 
			s.specials_id = '".(int)$s_id."'
		");
		$product = os_db_fetch_array($product_query);

		return $product;
	}

	/**
	 * Получить скидку по ID для категории товара
	 */
	public function getCategoryById($s_id, $lang = '')
	{
		$lang = (!empty($lang)) ? $lang : $_SESSION['languages_id'];

		$category_query = os_db_query("
		SELECT 
			* 
		FROM 
			".TABLE_SPECIAL_CATEGORY.", ".TABLE_CATEGORIES_DESCRIPTION." 
		WHERE 
			categ_id = categories_id AND 
			language_id = '".(int)$lang."' AND 
			special_id = '".(int)$s_id."'
		");

		$category = os_db_fetch_array($category_query);

		return $category;
	}

	/**
	 * Изменить статус товара
	 */
	public function status($post)
	{
		if (is_array($post))
		{
			os_db_query("UPDATE ".TABLE_SPECIALS." SET status = '".(int)$post['status']."', date_status_change = now() WHERE specials_id = '".(int)$post['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Изменить статус категорий товара
	 */
	public function statusCategories($post)
	{
		if (is_array($post))
		{
			$query = os_db_query("UPDATE ". TABLE_SPECIAL_CATEGORY." SET status = '".(int)$post['status']."' WHERE special_id = '".(int)$post['id']."'");

			$specials_query = os_db_query("SELECT product_id FROM ".TABLE_SPECIAL_PRODUCT." WHERE special_id = '".(int)$post['id']."'");
			while($specials = os_db_fetch_array($specials_query))
			{
				$product_id[] = $specials['product_id'];
			}

			os_db_query("UPDATE ". TABLE_SPECIALS. " SET status = '".(int)$post['status']."' WHERE products_id in (".implode(", ", $product_id).")");

			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Добавить\обновить скидку товара
	 */
	public function save($post)
	{
		$error = false;
		$data = array();
		if (isset($post) && !empty($post))
		{
			// Обновляем или добавляем
			$action = (isset($post['specials_id']) && !empty($post['specials_id'])) ? 'save' : 'new';

			// Если у нас есть налоги
			if (PRICE_IS_BRUTTO == 'true' && substr($post['specials_price'], -1) != '%')
			{
				$tax_query = os_db_query("SELECT tr.tax_rate from ".TABLE_TAX_RATES." tr, ".TABLE_PRODUCTS." p  WHERE tr.tax_class_id = p. products_tax_class_id  and p.products_id = '".(int)$post['products_id']."'");
				$tax = os_db_fetch_array($tax_query);
				$post['specials_price'] = ($post['specials_price'] / ($tax['tax_rate'] + 100) * 100);
			}

			// Если добавляем новый товар, то нужно получить его цену
			if ($action == 'new')
			{
				$getProductPriceQuery = os_db_query("SELECT products_price FROM ".TABLE_PRODUCTS." WHERE products_id = '".(int)$post['products_id']."'");
				$getProductPrice = os_db_fetch_array($getProductPriceQuery);
				$post['products_price'] = $getProductPrice['products_price'];
			}

			// Если добавляем процент
			if (substr($post['specials_price'], -1) == '%')
			{
				$post['specials_price'] = ($post['products_price'] - (($post['specials_price'] / 100) * $post['products_price']));
			}

			$sql_data_array = array
			(
				'products_id' => (int)$post['products_id'],
				'specials_quantity' => (int)$post['specials_quantity'],
				'specials_new_products_price' => os_db_prepare_input($post['specials_price']),
				'expires_date' => os_db_prepare_input($post['expires_date']),
				'status' => (int)$post['status'],
			);

			// Добавляем
			if ($action == 'new')
			{
				$insert_sql_data = array('specials_date_added' => 'now()');
				$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
				$result = os_db_perform(TABLE_SPECIALS, $sql_data_array);
				if (!$result) { $error = true; }
			}
			// Обновляем
			elseif ($action == 'save')
			{
				$insert_sql_data = array('specials_last_modified' => 'now()');
				$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
				$result = os_db_perform(TABLE_SPECIALS, $sql_data_array, 'update', "specials_id = '".(int)$post['specials_id']."'");
				if (!$result) { $error = true; }
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
	 * Добавить\обновить скидку категории товара
	 */
	public function saveCategory($post)
	{
		// Обновляем или добавляем
		$action = (isset($post['special_id']) && !empty($post['special_id'])) ? 'save' : 'new';

		$categ_id = (int)$post['categ_id'];
		$specials_id = ($action == 'save') ? (int)$post['special_id'] : '';
		$specials_price = os_db_prepare_input($post['specials_price']);
		$expires_date = os_db_prepare_input($post['expire_date']);
		$status = (int)$post['status'];
		$override = os_db_prepare_input($post['override']);
		$discount_type = substr($specials_price, -1) == '%' ? "p" : "f";
		$specials_price = sprintf("%0.2f", $specials_price);

		// Массив для обновления\добавления
		$updateCatQuery = array(
			'categ_id' => $categ_id,
			'discount' => os_db_prepare_input($specials_price),
			'special_last_modified' => 'now()',
			'expire_date' => $expires_date,
			'status' => $status,
			'discount_type' => $discount_type
		);

		// Обновляем категории
		if ($action == 'save')
		{
			os_db_perform(TABLE_SPECIAL_CATEGORY, $updateCatQuery, 'update', "special_id = '".$specials_id."'");
		}
		else
		{
			$query = os_db_query("SELECT special_id FROM ".TABLE_SPECIAL_CATEGORY." WHERE categ_id = '".$categ_id."'");
			if (os_db_num_rows($query) < 1)
			{
				os_db_perform(TABLE_SPECIAL_CATEGORY, $updateCatQuery);
				$specials_id = mysql_insert_id();
			}
		}

		// Перезаписываем все скидки новой
		if($override == "y")
		{
			if ($action == 'save')
			{
				$specials_query = os_db_query("
				SELECT 
					p.products_id, p.products_price 
				FROM 
					".TABLE_PRODUCTS." p, ".TABLE_SPECIAL_CATEGORY." sc, ".TABLE_PRODUCTS_TO_CATEGORIES." ptc
				WHERE 
					ptc.categories_id = sc.categ_id AND 
					sc.special_id = '".(int)$specials_id."' AND 
					p.products_id = ptc.products_id
				");
			}
			else
			{
				$specials_query = os_db_query("
				SELECT 
					p.products_id, p.products_price 
				FROM 
					".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." c
				WHERE 
					c.categories_id = '".(int)$categ_id."' AND 
					p.products_id = c.products_id
				");
			}

			while($specials = os_db_fetch_array($specials_query))
			{
				$product_id = (int)$specials['products_id'];
				$new_price = $discount_type == "p" ? $specials['products_price'] - ($specials['products_price'] * $specials_price / 100) : $specials['products_price'] - $specials_price;
				$new_price = sprintf("%0.2f", $new_price);

				// Проверяем уже добавленные скидки
				$product_query = os_db_query("SELECT product_id FROM ". TABLE_SPECIAL_PRODUCT. " WHERE product_id = '".$product_id."' AND special_id = '".(int)$specials_id."'");

				// Если нету, то просто добавляем новые товары категории
				if(os_db_num_rows($product_query) < 1)
				{
					$newProductToSpecial = array(
						'special_id' => $specials_id,
						'product_id' => $product_id,
					);
					os_db_perform(TABLE_SPECIAL_PRODUCT, $newProductToSpecial);

					$newProduct = array(
						'products_id' => $product_id,
						'specials_new_products_price' => $new_price,
						'expires_date' => $expires_date,
						'status' => $status
					);
					os_db_perform(TABLE_SPECIALS, $newProduct);
				}
				// Если есть, то обновляем данные
				else
				{
					$newProduct = array(
						'specials_new_products_price' => $new_price,
						'expires_date' => $expires_date,
						'specials_last_modified' => 'now()'
					);

					os_db_perform(TABLE_SPECIALS, $newProduct, 'update', "products_id = '".$product_id."'");
				}
			}
		}
		else
		{
			if ($action == 'save')
			{
				$query = os_db_query("
				SELECT 
					sp.product_id, p.products_price 
				FROM 
					".TABLE_SPECIAL_PRODUCT." sp, ".TABLE_PRODUCTS." p 
				WHERE 
					sp.special_id = '".(int)$specials_id."' and 
					p.products_id = sp.product_id
				");
			}
			else
			{
				$query = os_db_query("
				SELECT 
					ptc.products_id, p.products_price 
				FROM 
					(".TABLE_PRODUCTS_TO_CATEGORIES." ptc, ".TABLE_PRODUCTS." p) 
						LEFT JOIN ".TABLE_SPECIALS." s on (s.products_id = ptc.products_id)
				WHERE 
					ptc.categories_id = '".$categ_id."' and 
					p.products_id = ptc.products_id and 
					s.products_id IS NULL
				");
			}

			while($specials = os_db_fetch_array($query))
			{
				$product_id = ($action == 'save') ? (int)$specials['product_id'] : (int)$specials['products_id'];
				$new_price = $discount_type == "p" ? $specials['products_price'] - ($specials['products_price'] * $specials_price / 100) : $specials['products_price'] - $specials_price;
				$new_price = sprintf("%0.2f", $new_price);

				$newProduct = array(
					'specials_new_products_price' => $new_price,
					'expires_date' => $expires_date
				);

				if ($action == 'save')
				{
					$newProduct['specials_last_modified'] = 'now()';
					os_db_perform(TABLE_SPECIALS, $newProduct, 'update', "products_id = '".$product_id."'");
				}
				else
				{
					$newProduct['products_id'] = $product_id;
					os_db_perform(TABLE_SPECIALS, $newProduct);
				}

				if ($action == 'new')
				{
					os_db_query("INSERT INTO ".TABLE_SPECIAL_PRODUCT." values (null, '".(int)$specials_id."', '".$product_id."')");
				}

			}
		}

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удалить скидку у товара
	 */
	public function delete($post)
	{
		$product_query = os_db_query("SELECT products_id FROM ".TABLE_SPECIALS." WHERE specials_id = '".(int)$post['specials_id']."'");
		$product = os_db_fetch_array($product_query);

		os_db_query("DELETE FROM ".TABLE_SPECIAL_PRODUCT." WHERE product_id = '".(int)$product['products_id']."'");

		os_db_query("DELETE FROM ".TABLE_SPECIALS." WHERE specials_id = '".os_db_input($post['specials_id'])."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		return $data;
	}

	/**
	 * Удалить скидку у категории товара
	 */
	public function deleteCategory($post)
	{
		$special_id = (int)$post['special_id'];
		$product_id = array();
		$product_id[] = 0;
		$specials_query = os_db_query("SELECT product_id FROM ".TABLE_SPECIAL_PRODUCT." WHERE special_id = '".$special_id."'");
		while($specials = os_db_fetch_array($specials_query))
		{
			$product_id[] = $specials['product_id'];
		}

		$product_id = implode(", ", $product_id);

		os_db_query("delete FROM ".TABLE_SPECIALS." WHERE products_id in (".$product_id.")");
		os_db_query("delete FROM ".TABLE_SPECIAL_CATEGORY." WHERE special_id = '".$special_id."'");
		os_db_query("delete FROM ".TABLE_SPECIAL_PRODUCT." WHERE special_id = '".$special_id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		return $data;
	}
}
?>