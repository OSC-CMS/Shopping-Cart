<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

$default = new osTemplate;
$default->assign('session', session_id());
$main_content = '';

if (os_check_categories_status($current_category_id) >= 1)
{
	$error = CATEGORIE_NOT_FOUND;
	include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);
} 
else
{
	if ($category_depth == 'nested')
	{
		$category = $cartet->product->getCategory($current_category_id);

		$getCategoryArray = $cartet->product->getCategory($current_category_id, true);

		if (is_array($getCategoryArray))
		{
			foreach($getCategoryArray AS $c)
			{
				$categories_content = $c['children'];
			}
		}

		$new_products_category_id = $current_category_id;
		include (DIR_WS_MODULES.FILENAME_NEW_PRODUCTS);

		$featured_products_category_id = $current_category_id;
		include (DIR_WS_MODULES.FILENAME_FEATURED);

		$default->assign('CATEGORIES_NAME', $category['categories_name']);
		$default->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);
		$default->assign('CATEGORIES_IMAGE', $category['categories_image']);
		$default->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);
		$default->assign('language', $_SESSION['language']);

		$categories_content = apply_filter('categories_content', $categories_content);
		$default->assign('categories_content', $categories_content);

		// get default template
		if ($category['categories_template'] == '' or $category['categories_template'] == 'default')
		{
			$files = array ();
			if ($dir = opendir(_THEMES_C.'module/categorie_listing/'))
			{
				while (($file = readdir($dir)) !== false)
				{
					if (is_file(_THEMES_C.'module/categorie_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !="."))
					{
						$files[] = $file;
					}
				}
				sort($files);

				closedir($dir);
			}

			$category['categories_template'] = $files[0];
		}

		$getSubcategoriesIds = $cartet->product->getSubcategoriesId($getCategoryArray);

		$listing_sql = $cartet->product->getList(array(
			'categories_id' => $current_category_id,
			'subcategories' => $getSubcategoriesIds,
			'products_status' => 1,
			'category_status' => 1,
		));

		include (DIR_WS_MODULES.'product_listing_subcategories.php');

		$default->caching = 0;
		$main_content = $default->fetch(CURRENT_TEMPLATE.'/module/categorie_listing/'.$category['categories_template']);
		$main_content = apply_filter('main_content', $main_content);
		$osTemplate->assign('main_content', $main_content);
	}
	elseif ($category_depth == 'products' || isset($_GET['manufacturers_id']))
	{
		if (isset($_GET['manufacturers_id']))
		{
			if (isset($_GET['filter_id']) && os_not_null($_GET['filter_id']))
			{
				$listing_sql = $cartet->product->getList(array(
					'distinct' => 1,
					'categories_id' => $current_category_id,
					'manufacturers_id' => $_GET['filter_id'],
					'products_status' => 1,
					'category_status' => 1,
				));
			}
			else
			{
				$manufacturerData = $cartet->product->manufacturerData;

				$listing_sql = $cartet->product->getList(array(
					'categories_id' => $current_category_id,
					'manufacturers_id' => $_GET['manufacturers_id'],
					'products_status' => 1,
					'category_status' => 1,
				));
			}
		}
		else
		{
			if (isset($_GET['filter_id']) && os_not_null($_GET['filter_id']))
			{
				$listing_sql = $cartet->product->getList(array(
					'categories_id' => $current_category_id,
					'manufacturers_id' => $_GET['filter_id'],
					'products_status' => 1,
					'category_status' => 1,
				));
			}
			else
			{
				$listing_sql = $cartet->product->getList(array(
					'categories_id' => $current_category_id,
					'products_status' => 1,
					'category_status' => 1,
				));
			}
		}

		// optional Product List Filter
		if (PRODUCT_LIST_FILTER > 0)
		{
			if (isset($_GET['manufacturers_id']))
				$filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where p.products_status = '1' and c.categories_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '".(int) $_SESSION['languages_id']."' and p.manufacturers_id = '".(int) $_GET['manufacturers_id']."' order by cd.categories_name";
			else
				$filterlist_sql = "select distinct m.manufacturers_id as id, m.manufacturers_name as name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_MANUFACTURERS." m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '".$current_category_id."' order by m.manufacturers_name";

			$filterlist_query = osDBquery($filterlist_sql);
			if (os_db_num_rows($filterlist_query, true) > 1)
			{
				$manufacturer_dropdown = os_draw_form('filter', FILENAME_DEFAULT, 'get');
				if (isset ($_GET['manufacturers_id']))
				{
					$manufacturer_dropdown .= os_draw_hidden_field('manufacturers_id', (int)$_GET['manufacturers_id']);
					$options = array (array ('text' => TEXT_ALL_CATEGORIES));
				}
				else
				{
					$manufacturer_dropdown .= os_draw_hidden_field('cat', $_GET['cat']);
					$options = array (array ('text' => TEXT_ALL_MANUFACTURERS));
				}
				$manufacturer_dropdown .= os_draw_hidden_field('sort', $_GET['sort']);
				$manufacturer_dropdown .= os_draw_hidden_field(os_session_name(), os_session_id());

				$aManufacturers = array();
				while ($filterlist = os_db_fetch_array($filterlist_query, true))
				{
					$options[] = array(
						'id' => $filterlist['id'],
						'text' => $filterlist['name']
					);

					if (isset($_GET['manufacturers_id']))
					{
						$manufacturers_id = os_href_link(FILENAME_DEFAULT, 'manufacturers_id='.$_GET['manufacturers_id'].'&filter_id='.$filterlist['id']);
						$manufacturers_link = os_href_link(FILENAME_DEFAULT, 'manufacturers_id='.$_GET['manufacturers_id'].'&filter_id='.$filterlist['id']);
					}
					else
						$manufacturers_link = os_href_link(FILENAME_DEFAULT, 'cat='.$current_category_id.'&filter_id='.$filterlist['id']);

					$aManufacturers[] = array(
						'manufacturers_id' => $filterlist['id'],
						'manufacturers_name' => $filterlist['name'],
						'manufacturers_link' => $manufacturers_link,
					);
				}

				if (isset($_GET['cat']))
				{
					$all_manufacturers = array(
						'name' => TEXT_ALL_MANUFACTURERS,
						'link' => os_href_link(FILENAME_DEFAULT, 'cat='.$current_category_id),
					);
				}

				$manufacturer_sort = $aManufacturers;

				$manufacturer_dropdown .= os_draw_pull_down_menu('filter_id', $options, $_GET['filter_id'], 'onchange="this.form.submit()"');
				$manufacturer_dropdown .= '</form>'."\n";
			}
		}

		// Get the right image for the top-right
		$image = '';
		if (isset ($_GET['manufacturers_id']))
		{
			$image = osDBquery("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int) $_GET['manufacturers_id']."'");
			$image = os_db_fetch_array($image, true);
			$image = $image['manufacturers_image'];
		}
		elseif ($current_category_id)
		{
			$cat_array = $cartet->product->getCategory($current_category_id);
			$image = $cat_array['categories_image'];
		}

		include (DIR_WS_MODULES.FILENAME_PRODUCT_LISTING);
	}
	else
	{
		$group_check = '';
		if (GROUP_CHECK == 'true')
		{
			$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
		}

		$shop_content_query = osDBquery("SELECT
		content_title,
		content_heading,
		content_text,
		content_file
		FROM ".TABLE_CONTENT_MANAGER."
		WHERE content_group='5'
		".$group_check."
		AND languages_id='".$_SESSION['languages_id']."'");
		$shop_content_data = os_db_fetch_array($shop_content_query,true);

		$default->assign('title', $shop_content_data['content_heading']);
		include (dir_path('includes').FILENAME_CENTER_MODULES);

		if ($shop_content_data['content_file'] != '')
		{
			ob_start();
			if (strpos($shop_content_data['content_file'], '.txt')) echo '<pre>';
			include (_CATALOG.'media/content/'.$shop_content_data['content_file']);
			if (strpos($shop_content_data['content_file'], '.txt')) echo '</pre>';
			$shop_content_data['content_text'] = ob_get_contents();
			ob_end_clean();
		}

		$default->assign('greeting', os_customer_greeting());
		$default->assign('text', $shop_content_data['content_text']);
		$default->assign('language', $_SESSION['language']);

		global $os_action;

		if (isset($os_action['main_content']) && !empty($os_action['main_content']))
		{
			global $os_action_plug;
			global $_plug_name;

			$_box = array();
			foreach ($os_action['main_content'] as $_tag => $priority)
			{
				if (function_exists($_tag))
				{
					$_plug_name = $os_action_plug[ $_tag ];
					$p->name = $os_action_plug[ $_tag ];
					$p->group = $p->info[$p->name]['group'];
					$p->set_dir();
					$_box = $_tag();

					if (!isset($_box['template']))
					{
						if (!empty($_box) && isset($_box['content']) )
						{
							$default->assign('BOX_TITLE', $_box['title']);
							$default->assign('BOX_CONTENT', $_box['content']);
							$_box_value = $default->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
							$osTemplate->assign($_tag, $_box_value);
						}
					}
				}
			}
		}

		// set cache ID
		if (!CacheCheck())
		{
			$default->caching = 0;
			$main_content = $default->fetch(CURRENT_TEMPLATE.'/module/main_content.html');
		}
		else
		{
			$default->caching = 1;
			$default->cache_lifetime = CACHE_LIFETIME;
			$default->cache_modified_check = CACHE_CHECK;
			$cache_id = $_SESSION['language'].$_SESSION['currency'].$_SESSION['customer_id'];
			$main_content = $default->fetch(CURRENT_TEMPLATE.'/module/main_content.html', $cache_id);
		}

		global $os_filter;

		$_main_content = apply_filter('main_content', $main_content);

		$osTemplate->assign('main_content', $_main_content );
		$osTemplate->assign('default', true);
	}
}