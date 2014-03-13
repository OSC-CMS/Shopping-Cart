<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiProduct extends CartET
{
	/**
	 * Массив всех категорий
	 */
	private $categories;

	/**
	 * Дерево категорий
	 */
	private $categories_tree;

	public function __construct()
	{
	}

	/**
	 * Обновление дерева категорий
	 */
	public function updateCategoriesTree()
	{
		unset($this->categories_tree);
	}

	/**
	 * Возвращает производителя по id
	 */
	public function getManufacturer($id)
	{
		if (empty($id)) return false;

		$manufacturers_query = osDBquery("
		SELECT 
			* 
		FROM 
			".TABLE_MANUFACTURERS." m 
				left join ".TABLE_MANUFACTURERS_INFO." mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '".(int)$_SESSION['languages_id']."') 
		WHERE 
			m.manufacturers_id = '".(int)$id."'
		");
   
		if (os_db_num_rows($manufacturers_query, true) > 0)
		{
			$result = os_db_fetch_array($manufacturers_query, true);

			if (os_not_null($result['manufacturers_image']))
				$image = http_path('images').'manufacturers/'.$result['manufacturers_image'];
			else
				$image = '';

			$result['manufacturers_image'] = $image;

			if ($result['manufacturers_url'] != '')
				$url = '<a target="_blank" href="'.os_href_link(FILENAME_REDIRECT, 'action=manufacturer&'.os_manufacturer_link($result['manufacturers_id'],$result['manufacturers_name'])).'">'.sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $result['manufacturers_name']).'</a>';
			else
				$url = '';

			$result['manufacturers_url'] = $url;

			$this->manufacturerData = $result;

			return $result;
		}
	}

	/**
	 * Возвращает дополнительные поля товара
	 */
	public function getProductExtraFields($product_id)
	{
		if (empty($product_id)) return false;

		$extra_fields_query = osDBquery("
		SELECT
			pef.products_extra_fields_name as name, pef.products_extra_fields_group, ptf.products_extra_fields_value as value
		FROM
			".TABLE_PRODUCTS_EXTRA_FIELDS." pef
				LEFT JOIN ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." ptf ON ptf.products_extra_fields_id = pef.products_extra_fields_id
		WHERE
			ptf.products_id = ".(int)$product_id." AND
			ptf.products_extra_fields_value <> '' AND
			pef.products_extra_fields_status = 1 AND
			(pef.languages_id = '0' or pef.languages_id = '".(int)$_SESSION['languages_id']."')
		ORDER BY
			products_extra_fields_order
		");

		$efResult = array();
		if (os_db_num_rows($extra_fields_query, true) > 0)
		{
			while ($extra_fields = os_db_fetch_array($extra_fields_query, true))
			{
				$extra_fields_data[$extra_fields['products_extra_fields_group']][] = array(
					'NAME' => $extra_fields['name'],
					'VALUE' => $extra_fields['value']
				);
			}

			$groupsDescQuery = osDBquery("
			SELECT
				*, d.extra_fields_groups_name as group_name
			FROM
				".DB_PREFIX."products_extra_fields_groups g
					LEFT JOIN ".DB_PREFIX."products_extra_fields_groups_desc d ON (g.extra_fields_groups_id = d.extra_fields_groups_id AND d.extra_fields_groups_languages_id = '".(int)$_SESSION['languages_id']."')
			WHERE
				g.extra_fields_groups_status = 1
			ORDER BY
				g.extra_fields_groups_order ASC
			");

			if (os_db_num_rows($groupsDescQuery, true) > 0)
			{
				while ($groups = os_db_fetch_array($groupsDescQuery, true))
				{
					$groupDescEdit[$groups['extra_fields_groups_id']] = $groups;
				}

				foreach($groupDescEdit AS $gId => $gValue)
				{
					foreach ($extra_fields_data as $fGId => $fValue)
					{
						if ($gId == $fGId)
						{
							$efResult[$gId] = $gValue;
							$efResult[$gId]['values'] = $fValue;
						}
					}
				}
			}
		}

		return $efResult;
	}

	/**
	 * Возвращает заданную категорию
	 */
	public function getCategory($id)
	{
		if (empty($id)) return false;

		if(!isset($this->categories))
			$this->getTree();

		if (array_key_exists($id, $this->categories))
			return $this->categories[$id];
		else
			return false;
	}

	/**
	 * Возвращает активную категорию
	 */
	public function getCurrentCategory($in_path = false)
	{
		global $cPath;
		$category_path = explode('_', $cPath);

		if ($category_path)
		{
			$this_category = array_pop($category_path);
			return ($in_path == true) ? $category_path : $this_category;
		}
		else
			return false;
	}

	/**
	 * Возвращает дерево категорий
	 */
	public function getCategoriesTree()
	{
		if(!isset($this->categories_tree))
			$this->getTree();

		return $this->categories_tree;
	}

	/**
	 * Формируем дерево
	 */
	private function getTree()
	{
		$group_check = (GROUP_CHECK == 'true') ? " AND c.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 " : '';
		$cat_query = osDBquery("SELECT * FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd WHERE c.categories_id = cd.categories_id ".$group_check." AND cd.language_id = '".(int)$_SESSION['languages_id']."'");

		$items = array();
		if (os_db_num_rows($cat_query, true))
		{
			while ($cat = os_db_fetch_array($cat_query, true))
			{
				if (is_file(dir_path('images').'categories/'.$cat['categories_image']))
					$cat['categories_image'] = http_path('images').'categories/'.$cat['categories_image'];

				$cat['categories_link'] = os_href_link(FILENAME_DEFAULT, os_category_link($cat['categories_id'], $cat['categories_name']));

				$items[$cat['categories_id']] = $cat;
			}
		}

		$tree = array();
		$copy = $items;

		if (is_array($copy))
		{
			foreach ($copy as $id => $item)
			{
				if ($item['parent_id'])
					$copy[$item['parent_id']]['children'][] = &$copy[$id];
				else
					$tree[] = &$copy[$id];
			}
		}

		unset($copy);

		$this->categories = $items;
		$this->categories_tree = $tree;
	}

	private function sorting($sorting_data)
	{
		$sortingTypes = array('name', 'price');
		$directionTypes = array('asc', 'desc');

		$sort = ($this->request->get('sort')) ? $this->request->get('sort') : '';
		$direction = ($this->request->get('direction')) ? $this->request->get('direction') : '';

		if (in_array($sort, $sortingTypes) && in_array($direction, $directionTypes))
		{
			$sorting_data = array(
				'products_sorting' => 'products_'.$sort,
				'products_sorting2' => $direction
			);
		}

		return $sorting_data;
	}

	public function getList($params = array())
	{
		// Опции
		$categoryInfo = array();
		$sorting = 'pd.products_name ASC';
		$category = '';
		$manufacturer = '';
		$status = '';
		$distinct = '';
		$limit = '';
		$language = $_SESSION['languages_id'];

		// Если есть категория
		if ($params['categories_id'])
		{
			$categoryInfo = $this->getCategory($params['categories_id']);

			// Сортировка товара
			$sorting_data = $this->sorting($categoryInfo);
			$sorting = ' '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';

			// Выборка товара по определенной категории
			if (is_array($params['subcategories']))
				$category = " AND p2c.categories_id IN (".implode(',', $params['subcategories']).") ";
			else
				$category = " AND p2c.categories_id = '".(int)$params['categories_id']."' ";
		}

		// Фильтруем в категории по производителю
		if ($params['manufacturers_id'])
			$manufacturer = " AND p.manufacturers_id = '".(int)$params['manufacturers_id']."'  AND m.manufacturers_id = '".(int)$params['manufacturers_id']."' ";

		// Статус товара
		if ($params['products_status'])
			$status = " AND p.products_status = '".(int)$params['products_status']."' ";

		// Без дублей
		if ($params['distinct'])
			$distinct = " DISTINCT ";

		// Язык
		if ($params['languages_id'])
			$language = $params['languages_id'];

		// Лимит
		if ($params['limit'])
			$limit = " LIMIT ".(int)$params['limit'];

		// Проверка прав
		$group_check = (GROUP_CHECK == 'true' && !$params['admin']) ? " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 " : '';
		$fsk_lock = ($_SESSION['customers_status']['customers_fsk18_display'] == '0' && !$params['admin']) ? ' AND p.products_fsk18 != 1 ' : '';

		$listing_sql = "
		SELECT ".$distinct."
			p.products_fsk18,
			p.products_shippingtime,
			p.products_model,
			p.products_ean,
			p.products_quantity,
			p.products_image,
			p.products_weight,
			p.stock,
			p.products_id,
			p.manufacturers_id,
			p.products_price,
			p.products_vpe,
			p.products_vpe_status,
			p.products_vpe_value,
			p.products_discount_allowed,
			p.products_tax_class_id,
			p.products_bundle,
			pd.products_name,
			pd.products_short_description,
			pd.products_description,
			m.manufacturers_id,
			m.manufacturers_name
		FROM
			".TABLE_PRODUCTS." p
				LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON (pd.products_id = p.products_id)
				LEFT JOIN ".TABLE_MANUFACTURERS." m ON (p.manufacturers_id = m.manufacturers_id)
				LEFT JOIN ".TABLE_SPECIALS." s ON (p.products_id = s.products_id)
				LEFT JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." p2c ON (p.products_id = p2c.products_id AND pd.products_id = p2c.products_id)
		WHERE
			pd.language_id = '".(int)$language."'
			".$group_check."
			".$fsk_lock."
			".$category."
			".$status."
			".$manufacturer."
			ORDER BY
				".$sorting."
			".$limit."
		";

		return $listing_sql;
	}
}