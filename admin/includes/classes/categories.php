<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/
/*
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
  (c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
  (c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
  (c) 2008	 VamShop (2008/01/01); vamshop.com
*/

defined('_VALID_OS') or die('Access denied!');

class categories
{
	function insert_category($categories_data, $dest_category_id, $action = 'insert') {

		$categories_id = os_db_prepare_input($categories_data['categories_id']);

		$sort_order = os_db_prepare_input($categories_data['sort_order']);
		$categories_status = os_db_prepare_input($categories_data['status']);
		$yml_bid = os_db_prepare_input($categories_data['yml_bid']);
		$yml_cbid = os_db_prepare_input($categories_data['yml_cbid']);
		$customers_statuses_array = os_get_customers_statuses();
        $categories_url = os_db_prepare_input($categories_data['categories_url']);


		$permission = array ();
		for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
			if (isset($customers_statuses_array[$i]['id']))
				$permission[$customers_statuses_array[$i]['id']] = 0;
		}
		if (isset ($categories_data['groups']))
			foreach ($categories_data['groups'] AS $dummy => $b) {
				$permission[$b] = 1;
			}
		if ($permission['all']==1) {
			$permission = array ();
			end($customers_statuses_array);
			for ($i = 0; $n = key($customers_statuses_array), $i < $n+1; $i ++) {
				if (isset($customers_statuses_array[$i]['id']))
					$permission[$customers_statuses_array[$i]['id']] = 1;
			}
		}
		

		$permission_array = array ();

		end($customers_statuses_array);		
		for ($i = 0; $n = key($customers_statuses_array), $i < $n+1; $i ++) {
			if (isset($customers_statuses_array[$i]['id'])) {
				$permission_array = array_merge($permission_array, array ('group_permission_'.$customers_statuses_array[$i]['id'] => $permission[$customers_statuses_array[$i]['id']]));
			}
		}

                $sql_data_array = array ('sort_order' => $sort_order, 'categories_status' => $categories_status, 'products_sorting' => os_db_prepare_input($categories_data['products_sorting']), 'products_sorting2' => os_db_prepare_input($categories_data['products_sorting2']), 'categories_template' => os_db_prepare_input($categories_data['categories_template']), 'listing_template' => os_db_prepare_input($categories_data['listing_template']), 'yml_bid' => $yml_bid, 'yml_cbid' => $yml_cbid, 'categories_url' => $categories_url);
		$sql_data_array = array_merge($sql_data_array,$permission_array);
		if ($action == 'insert') {
			$insert_sql_data = array ('parent_id' => $dest_category_id, 'date_added' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_CATEGORIES, $sql_data_array);
			$categories_id = os_db_insert_id();
		}
		elseif ($action == 'update') {
			$update_sql_data = array ('last_modified' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
			os_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \''.$categories_id.'\'');
		}
		os_set_groups($categories_id, $permission_array);
		$languages = os_get_languages();
		foreach ($languages AS $lang) {
			$categories_name_array = $categories_data['name'];
			$sql_data_array = array ('categories_name' => os_db_prepare_input($categories_data['categories_name'][$lang['id']]), 'categories_heading_title' => os_db_prepare_input($categories_data['categories_heading_title'][$lang['id']]), 'categories_description' => os_db_prepare_input($categories_data['categories_description'][$lang['id']]), 'categories_meta_title' => os_db_prepare_input($categories_data['categories_meta_title'][$lang['id']]), 'categories_meta_description' => os_db_prepare_input($categories_data['categories_meta_description'][$lang['id']]), 'categories_meta_keywords' => os_db_prepare_input($categories_data['categories_meta_keywords'][$lang['id']]));


			if ($action == 'insert') {
				$insert_sql_data = array ('categories_id' => $categories_id, 'language_id' => $lang['id']);
				$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
				os_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
			}
			elseif ($action == 'update') {
				os_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \''.$categories_id.'\' and language_id = \''.$lang['id'].'\'');
			}
		}

		if ($categories_image = & os_try_upload('categories_image', dir_path('images').'categories/')) {
			$cname_arr = explode('.', $categories_image->filename);
			$cnsuffix = array_pop($cname_arr);
			$categories_image_name = $categories_id.'.'.$cnsuffix;
			@unlink(dir_path('images').'categories/'.$categories_image_name);
			@rename(dir_path('images').'categories/'.$categories_image->filename, dir_path('images').'categories/old_'.$categories_image_name);
			require (get_path('includes_admin').'category_thumbnail_images.php');      
			@unlink(dir_path('images').'categories/old_'.$categories_image_name);

			os_db_query("UPDATE ".TABLE_CATEGORIES."
						    		                 SET categories_image = '".os_db_input($categories_image_name)."'
						    		               WHERE categories_id = '".(int) $categories_id."'");
		}

		if ($categories_data['del_cat_pic'] == 'yes') {
			@ unlink(dir_path('images').'categories/'.$categories_data['categories_previous_image']);
			os_db_query("UPDATE ".TABLE_CATEGORIES."
						    		                 SET categories_image = ''
						    		               WHERE categories_id    = '".(int) $categories_id."'");
		}
        
        global $categories_id;
                
        do_action('insert_category');

	} 

	function set_category_recursive($categories_id, $status = "0") {
	os_db_query("UPDATE ".TABLE_CATEGORIES." SET categories_status = '".$status."' WHERE categories_id = '".$categories_id."'");
		$q = "select ptc.products_id from ".TABLE_PRODUCTS_TO_CATEGORIES." as ptc";
		$q .= " where ptc.categories_id='".$categories_id."';";
		$q_data = os_db_query($q);
		while ($products = os_db_fetch_array($q_data)) {
			$this->set_product_status($products['products_id'], $status);
		}
		
		// look for deeper categories and go rekursiv
		$categories_query = os_db_query("SELECT categories_id FROM ".TABLE_CATEGORIES." WHERE parent_id='".$categories_id."'");
		while ($categories = os_db_fetch_array($categories_query)) {
			$this->set_category_recursive($categories['categories_id'], $status);
		}
	}
	
	function set_category_xml_recursive($categories_id, $status = "0") {

	os_db_query("UPDATE ".TABLE_CATEGORIES." SET yml_enable = '".$status."' WHERE categories_id = '".$categories_id."'");
		$q = "select ptc.products_id from ".TABLE_PRODUCTS_TO_CATEGORIES." as ptc";
		$q .= " where ptc.categories_id='".$categories_id."';";
		$q_data = os_db_query($q);
		while ($products = os_db_fetch_array($q_data)) {
			$this->set_product_xml_status($products['products_id'], $status);
		}
		
		// look for deeper categories and go rekursiv
		$categories_query = os_db_query("SELECT categories_id FROM ".TABLE_CATEGORIES." WHERE parent_id='".$categories_id."'");
		while ($categories = os_db_fetch_array($categories_query)) {
			$this->set_category_recursive($categories['categories_id'], $status);
		}

	}
	// ----------------------------------------------------------------------------------------------------- //

	function insert_product($products_data, $dest_category_id, $action = 'insert') 
	{
		global $cartet;

		/*_print_r($_FILES);
		die();*/

		$products_id = os_db_prepare_input($products_data['products_id']);
        $products_page_url = os_db_prepare_input($products_data['products_page_url']);
		$products_date_available = os_db_prepare_input($products_data['products_date_available']);
		$products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';
 
         if ($products_data['products_startpage'] == 1 ) {
         	$products_status = 1;
         } else {
         	$products_status = os_db_prepare_input($products_data['products_status']);
         	}
         	
         if ($products_data['products_startpage'] == 0 ) {
 			$products_status = os_db_prepare_input($products_data['products_status']);
         }
         
		if (PRICE_IS_BRUTTO == 'true' && $products_data['products_price']) {
			$products_data['products_price'] = round(($products_data['products_price'] / (os_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100), PRICE_PRECISION);
		}
		$customers_statuses_array = os_get_customers_statuses();

		$permission = array ();
		for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
			if (isset($customers_statuses_array[$i]['id']))
				$permission[$customers_statuses_array[$i]['id']] = 0;
		}
		if (isset ($products_data['groups']))
			foreach ($products_data['groups'] AS $dummy => $b) {
				$permission[$b] = 1;
			}
		if (@$permission['all']==1) {
			$permission = array ();
			end($customers_statuses_array);
			for ($i = 0; $n = key($customers_statuses_array), $i < $n+1; $i ++) {
				if (isset($customers_statuses_array[$i]['id']))
					$permission[$customers_statuses_array[$i]['id']] = 1;
			}
		}
		$permission_array = array ();
		end($customers_statuses_array);		
		for ($i = 0; $n = key($customers_statuses_array), $i < $n+1; $i ++) {
			if (isset($customers_statuses_array[$i]['id'])) {
				$permission_array = array_merge($permission_array, array ('group_permission_'.$customers_statuses_array[$i]['id'] => $permission[$customers_statuses_array[$i]['id']]));
			}
		}

		$sql_data_array = array(
			'products_quantity' => os_db_prepare_input($products_data['products_quantity']),
			'products_to_xml' => os_db_prepare_input($products_data['products_to_xml']),
			'products_model' => os_db_prepare_input($products_data['products_model']),
			'products_ean' => os_db_prepare_input($products_data['products_ean']),
			'products_price' => os_db_prepare_input($products_data['products_price']),
			'products_sort' => os_db_prepare_input($products_data['products_sort']),
			'products_shippingtime' => os_db_prepare_input($products_data['shipping_status']),
			'products_discount_allowed' => os_db_prepare_input($products_data['products_discount_allowed']),
			'products_date_available' => $products_date_available,
			'products_weight' => os_db_prepare_input($products_data['products_weight']),
			'products_status' => $products_status,
			'products_startpage' => os_db_prepare_input($products_data['products_startpage']),
			'products_reviews' => os_db_prepare_input($products_data['products_reviews']),
			'products_search' => os_db_prepare_input($products_data['products_search']),
			'products_startpage_sort' => os_db_prepare_input($products_data['products_startpage_sort']),
			'products_tax_class_id' => os_db_prepare_input($products_data['products_tax_class_id']),
			'product_template' => os_db_prepare_input($products_data['info_template']),
			'options_template' => os_db_prepare_input($products_data['options_template']),
			'manufacturers_id' => os_db_prepare_input($products_data['manufacturers_id']),
			'products_fsk18' => os_db_prepare_input($products_data['fsk18']),
			'products_vpe_value' => os_db_prepare_input($products_data['products_vpe_value']),
			'products_vpe_status' => os_db_prepare_input(@$products_data['products_vpe_status']),
			'products_vpe' => os_db_prepare_input($products_data['products_vpe']),
			'yml_bid' => os_db_prepare_input($products_data['yml_bid']),
			'yml_cbid' => os_db_prepare_input($products_data['yml_cbid']),
			'yml_available' => os_db_prepare_input($products_data['yml_available']),
			'products_page_url' => os_db_prepare_input($products_data['products_page_url']),
			'products_bundle' => os_db_prepare_input($products_data['products_bundle'])
		);
		

		$sql_data_array = array_merge($sql_data_array, $permission_array);
		if (!$products_id || $products_id == '') {
			$new_pid_query = os_db_query("SHOW TABLE STATUS LIKE '".TABLE_PRODUCTS."'");
			$new_pid_query_values = os_db_fetch_array($new_pid_query);
			$products_id = $new_pid_query_values['Auto_increment'];
		}

		// удаление изображений
		if (!empty($_POST['image_delete']) OR !empty($_POST['images_delete']))
		{
			$cartet->products->deleteImages(array(
				'image_delete' => $_POST['image_delete'],
				'images_delete' => $_POST['images_delete'],
				'products_id' => $products_id
			));
		}

		// загрузка с компьютера
		if (!empty($_FILES['images']))
		{
			$images_array = reArrayFiles($_FILES['images']);
			$img = 0;
			foreach($images_array as $images)
			{
				$img++;
				$ext = pathinfo($images["name"], PATHINFO_EXTENSION);
				$cFile = $products_id.'_'.translit(urldecode(pathinfo($images["name"], PATHINFO_FILENAME)));
				$new_file = $cFile.'.'.$ext;

				while (file_exists(dir_path('images_original').$new_file))
				{
					$new_base = pathinfo($new_file, PATHINFO_FILENAME);
					if(preg_match('/_([0-9]+)$/', $new_base, $parts))
						$new_file = $cFile.'_'.($parts[1]+1).'.'.$ext;
					else
						$new_file = $cFile.'_1.'.$ext;
				}

				if (move_uploaded_file($images["tmp_name"], dir_path('images_original').$new_file))
				{
					$products_image_name = $new_file;

					// если нет основной картинки, то создаем
					if ($img == 1 && empty($_POST['main_image']))
					{
						$sql_data_array['products_image'] = os_db_prepare_input($products_image_name);

						require (get_path('includes_admin').'product_thumbnail_images.php');
						require (get_path('includes_admin').'product_info_images.php');
						require (get_path('includes_admin').'product_popup_images.php');
					}
					// иначе, это доп. картинки
					else
					{
						$products_image_name = $new_file;
						create_MO_PICS($products_image_name, $img, $action, $products_id, $products_data);
					}
				}
			}
		}

		// загрузка по ссылке
		if (!empty($_POST['images_urls']))
		{
			$img = 0;
			foreach($_POST['images_urls'] AS $img_url)
			{
				$img++;
				$products_image_name = downloadImage($img_url, dir_path('images_original'), $products_id);
				create_MO_PICS($products_image_name, $img, $action, $products_id, $products_data);
			}
		}

		if ($action == 'insert')
		{
			$insert_sql_data = array ('products_date_added' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_PRODUCTS, $sql_data_array);
			$products_id = os_db_insert_id();

			//Bundle
			if ($products_data['products_bundle'] == '1')
			{
				os_db_query("DELETE FROM ".DB_PREFIX."products_bundles WHERE bundle_id = '".$products_id."'");
				if (isset($_POST['bundles']))
				{
					$arr = $_POST['bundles'];
					for($i = 0; $i < count($arr['id']); $i++)
					{
						os_db_query("INSERT INTO ".DB_PREFIX."products_bundles (bundle_id, subproduct_id, subproduct_qty) VALUES ('".os_db_input($products_id)."', '".os_db_input($arr['id'][$i])."', '".os_db_input($arr['qty'][$i])."')");
					}
				}

			}
			// Bundle

			os_db_query("INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES." SET products_id   = '".$products_id."', categories_id = '".$dest_category_id."'");
		}
		elseif ($action == 'update') {
			$update_sql_data = array ('products_last_modified' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
			
			os_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \''.os_db_input($products_id).'\'');

			// Bundle
			if ($products_data['products_bundle'] == '1')
			{
				os_db_query("DELETE FROM ".DB_PREFIX."products_bundles WHERE bundle_id = '" . $products_id . "'");
				if (isset($_POST['bundles']))
				{
					$arr = $_POST['bundles'];
					for($i = 0; $i < count($arr['id']); $i++)
					{
						os_db_query("INSERT INTO ".DB_PREFIX."products_bundles (bundle_id, subproduct_id, subproduct_qty) VALUES ('".os_db_input($products_id)."', '".os_db_input($arr['id'][$i])."', '".os_db_input($arr['qty'][$i])."')");
					}
				}
			}
			// Bundle
		}

		$languages = os_get_languages();
		$i = 0;
		$group_query = os_db_query("SELECT customers_status_id FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id = '".(int) $_SESSION['languages_id']."' AND customers_status_id != '0'");
		while ($group_values = os_db_fetch_array($group_query)) {
			$i ++;
			$group_data[$i] = array ('STATUS_ID' => $group_values['customers_status_id']);
		}
		for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++) {
			if (@$group_data[$col]['STATUS_ID'] != '') {
				$personal_price = os_db_prepare_input($products_data['products_price_'.$group_data[$col]['STATUS_ID']]);
				if ($personal_price == '' || $personal_price == '0.0000') {
					$personal_price = '0.00';
				} else {
					if (PRICE_IS_BRUTTO == 'true') {
						$personal_price = ($personal_price / (os_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100);
					}
					$personal_price = os_round($personal_price, PRICE_PRECISION);
				}

				if ($action == 'insert') {

					os_db_query("DELETE FROM ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']." WHERE products_id = '".$products_id."'
												                 AND quantity    = '1'");

					$insert_array = array ();
					$insert_array = array ('personal_offer' => $personal_price, 'quantity' => '1', 'products_id' => $products_id);
					os_db_perform(TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID'], $insert_array);

				} else {

					os_db_query("UPDATE ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']."
												                 SET personal_offer = '".$personal_price."'
												               WHERE products_id = '".$products_id."'
												                 AND quantity    = '1'");

				}
			}
		}

		$i = 0;
		$group_query = os_db_query("SELECT customers_status_id FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id = '".(int) $_SESSION['languages_id']."' AND customers_status_id != '0'");
		while ($group_values = os_db_fetch_array($group_query))
		{
			$i ++;
			$group_data[$i] = array ('STATUS_ID' => $group_values['customers_status_id']);
		}
		for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++) {
			if (@$group_data[$col]['STATUS_ID'] != '') {
				$quantity = os_db_prepare_input($products_data['products_quantity_staffel_'.$group_data[$col]['STATUS_ID']]);
				$staffelpreis = os_db_prepare_input($products_data['products_price_staffel_'.$group_data[$col]['STATUS_ID']]);
				if (PRICE_IS_BRUTTO == 'true') {
					$staffelpreis = ($staffelpreis / (os_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100);
				}
				$staffelpreis = os_round($staffelpreis, PRICE_PRECISION);

				if ($staffelpreis != '' && $quantity != '') {
					if ($quantity <= 1)
						$quantity = 2;
					$check_query = os_db_query("SELECT quantity
														                               FROM ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']."
														                              WHERE products_id = '".$products_id."'
														                                AND quantity    = '".$quantity."'");

					if (os_db_num_rows($check_query) < 1) {
						os_db_query("INSERT INTO ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']."
																	                 SET price_id       = '',
																	                     products_id    = '".$products_id."',
																	                     quantity       = '".$quantity."', personal_offer = '".$staffelpreis."'");
					}
				}
			}
		}
		foreach ($languages AS $lang) {
			$language_id = $lang['id'];
			$sql_data_array = array ('products_name' => os_db_prepare_input($products_data['products_name'][$language_id]), 'products_description' => os_db_prepare_input($products_data['products_description_'.$language_id]), 'products_short_description' => os_db_prepare_input($products_data['products_short_description_'.$language_id]), 'products_keywords' => os_db_prepare_input($products_data['products_keywords'][$language_id]), 'products_url' => os_db_prepare_input($products_data['products_url'][$language_id]), 'products_meta_title' => os_db_prepare_input($products_data['products_meta_title'][$language_id]), 'products_meta_description' => os_db_prepare_input($products_data['products_meta_description'][$language_id]), 'products_meta_keywords' => os_db_prepare_input($products_data['products_meta_keywords'][$language_id]));

			if ($action == 'insert') {
				$insert_sql_data = array ('products_id' => $products_id, 'language_id' => $language_id);
				$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
				os_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
			}
			elseif ($action == 'update') {
				os_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \''.os_db_input($products_id).'\' and language_id = \''.$language_id.'\'');
			}
		}
		
          $extra_fields_query = os_db_query("SELECT * FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " WHERE products_id = " . os_db_input($products_id));
          while ($products_extra_fields = os_db_fetch_array($extra_fields_query)) {
            $extra_product_entry[$products_extra_fields['products_extra_fields_id']] = $products_extra_fields['products_extra_fields_value'];
          }




          if ($_POST['extra_field'])
          {
            foreach ($_POST['extra_field'] as $key=>$val)
            {
              if (isset($extra_product_entry[$key]))
              {
                if ($val == '')
	                os_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " where products_id = " . os_db_input($products_id) . " AND  products_extra_fields_id = " . $key);
                else
	                os_db_query("UPDATE " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " SET products_extra_fields_value = '" . os_db_prepare_input($val) . "' WHERE products_id = " . os_db_input($products_id) . " AND  products_extra_fields_id = " . $key);
              }
              else
              {
                if ($val != '')
	                os_db_query("INSERT INTO " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " (products_id, products_extra_fields_id, products_extra_fields_value) VALUES ('" . os_db_input($products_id) . "', '" . $key . "', '" . os_db_prepare_input($val) . "')");
              }
            }
          }


		// Новые доп. поля
		$efName = $_POST['efName'];
		$efValue = $_POST['efValue'];
		$efGroup = $_POST['efGroup'];
		if (is_array($efName) && is_array($efValue))
		{
			$efId = '';
			foreach($efName as $i => $name)
			{
				$value = trim($efValue[$i]);
				if (!empty($name) && !empty($value))
				{
					$extra_fields_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_EXTRA_FIELDS." WHERE products_extra_fields_name = '".os_db_prepare_input($name)."' LIMIT 1");
					$extra_fields = os_db_fetch_array($extra_fields_query);
					$efId = $extra_fields['products_extra_fields_id'];

					if (!os_db_num_rows($extra_fields_query))
					{
						$sql_data_array = array(
							'products_extra_fields_name' => os_db_prepare_input($name),
							'products_extra_fields_order' => 0,
							'products_extra_fields_status' => 1,
							'products_extra_fields_group' => (int)$efGroup[$i],
							'languages_id' => (int)$_SESSION['languages_id']
						);
						os_db_perform(TABLE_PRODUCTS_EXTRA_FIELDS, $sql_data_array);
						$efId = os_db_insert_id();
					}

					if ($value != '')
						os_db_query("REPLACE INTO ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." SET products_id = '".(int)$products_id."', products_extra_fields_id = '".(int)$efId."', products_extra_fields_value = '".os_db_prepare_input($value)."'");
					else
						os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." where products_id = '".(int)$products_id."' AND products_extra_fields_id = ".$efId);
				}
			}
		}

		  $_POST['product_id'] = os_db_input($products_id);
		  do_action('insert_product');
	} 

	function set_product_status($products_id, $status) {
		if ($status == '1') {
			return os_db_query("update ".TABLE_PRODUCTS." set products_status = '1', products_last_modified = now() where products_id = '".$products_id."'");
		}
		elseif ($status == '0') {
			return os_db_query("update ".TABLE_PRODUCTS." set products_status = '0', products_last_modified = now() where products_id = '".$products_id."'");
		} else {
			return -1;
		}
	}
	function set_product_xml_status($products_id, $status) {
		if ($status == '1') {
			return os_db_query("update ".TABLE_PRODUCTS." set products_to_xml = '1', products_last_modified = now() where products_id = '".$products_id."'");
		}
		elseif ($status == '0') {
			return os_db_query("update ".TABLE_PRODUCTS." set products_to_xml = '0', products_last_modified = now() where products_id = '".$products_id."'");
		} else {
			return -1;
		}
	}
	
	function set_product_startpage($products_id, $status) {
		if ($status == '1') {
			return os_db_query("update ".TABLE_PRODUCTS." set products_startpage = '1', products_last_modified = now() where products_id = '".$products_id."'");
		}
		elseif ($status == '0') {
			return os_db_query("update ".TABLE_PRODUCTS." set products_startpage = '0', products_last_modified = now() where products_id = '".$products_id."'");
		} else {
			return -1;
		}
	}

	function count_category_products($category_id, $include_deactivated = false) {
		$products_count = 0;
		if ($include_deactivated) {
			$products_query = os_db_query("select count(*) as total from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where p.products_id = p2c.products_id and p2c.categories_id = '".$category_id."'");
		} else {
			$products_query = os_db_query("select count(*) as total from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '".$category_id."'");
		}

		$products = os_db_fetch_array($products_query);

		$products_count += $products['total'];

		$childs_query = os_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id = '".$category_id."'");
		if (os_db_num_rows($childs_query)) {
			while ($childs = os_db_fetch_array($childs_query)) {
				$products_count += $this->count_category_products($childs['categories_id'], $include_deactivated);
			}
		}
		return $products_count;
	}

	function count_category_childs($category_id) {
		$categories_count = 0;
		$categories_query = os_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id = '".$category_id."'");
		while ($categories = os_db_fetch_array($categories_query)) {
			$categories_count ++;
			$categories_count += $this->count_category_childs($categories['categories_id']);
		}
		return $categories_count;
	}
	
	
	function edit_cross_sell($cross_data) {
		
		if ($cross_data['special'] == 'add_entries') {

				if (isset ($cross_data['ids'])) {
					foreach ($cross_data['ids'] AS $pID) {

						$sql_data_array = array ('products_id' => $cross_data['current_product_id'], 'xsell_id' => $pID,'products_xsell_grp_name_id'=>$cross_data['group_name'][$pID]);
						$check_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_XSELL." WHERE products_id='".$cross_data['current_product_id']."' and xsell_id='".$pID."'");
						if (!os_db_num_rows($check_query)) os_db_perform(TABLE_PRODUCTS_XSELL, $sql_data_array);
					}
				}

			}
			if ($cross_data['special'] == 'edit') {

				if (isset ($cross_data['ids'])) {
					foreach ($cross_data['ids'] AS $pID) {
						os_db_query("DELETE FROM ".TABLE_PRODUCTS_XSELL." WHERE ID='".$pID."'");
					}
				}
				if (isset ($cross_data['sort'])) {
					foreach ($cross_data['sort'] AS $ID => $sort) {
						os_db_query("UPDATE ".TABLE_PRODUCTS_XSELL." SET sort_order='".$sort."',products_xsell_grp_name_id='".$cross_data['group_name'][$ID]."' WHERE ID='".$ID."'");
					}
				}
			}
	}
}

function create_MO_PICS($mo_products_image_name, $mo_image_number, $action, $products_id, &$products_data, $img_text = '')
{
	$absolute_image_number = $mo_image_number+1;
	$mo_img = array(
		'products_id' => os_db_prepare_input($products_id),
		'image_nr' => os_db_prepare_input($absolute_image_number),
		'image_name' => os_db_prepare_input($mo_products_image_name),
		'text' => os_db_prepare_input($img_text)
	);

	$previous_image_name = $products_data['products_previous_image_'.$absolute_image_number];

	if ($action == 'insert')
	{
		os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
	}
	elseif ($action == 'update' && $previous_image_name)
	{
		if ($products_data['del_mo_pic'])
		{
			foreach ($products_data['del_mo_pic'] AS $dummy => $val)
			{
				if ($val == $previous_image_name)
				{
					os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
				}
				break;
			}
		}

		os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img, 'update', 'image_name = \''.os_db_input($previous_image_name).'\'');

	}
	elseif (!$previous_image_name)
	{
		os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
	}

	$products_image_name = $mo_products_image_name;

	require (get_path('includes_admin').'product_thumbnail_images.php');
	require (get_path('includes_admin').'product_info_images.php');
	require (get_path('includes_admin').'product_popup_images.php');
}

function delete_images_from_db (&$products_data, $product_id)
{
	$modifications_count = 0;
	if (@$_POST['image_delete'] != '')
	{
		os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_image = '' WHERE products_id = '".os_db_input($product_id)."'");
		$modifications_count++;
	}

	if (@$_POST['images_delete'] != '')
	{
		foreach ($_POST['images_delete'] AS $val)
		{
			os_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".(int)$product_id."' AND image_name  = '".os_db_prepare_input($val)."'");
			$modifications_count++;
		}
	}

	return $modifications_count;
}


function delete_image_files ($image_file_names)
{
	foreach ($image_file_names AS $image_name)
	{
		@os_del_image_file($image_name);
	}
}

function delete_unused_image_file ($image_file_name)
{
	delete_unused_image_files (array($image_file_name));
}

function delete_unused_image_files ($image_file_names)
{
	$unused_images = get_unused_images($image_file_names);
	delete_image_files ($unused_images);
}

function get_unused_images ($checked_image_file_names)
{
	$checked_image_file_names = array_unique($checked_image_file_names);

	$unused_images = array();
	foreach ($checked_image_file_names AS $image_name)
	{
		if (is_image_unused($image_name))
		{
			$unused_images[] = $image_name;
		}
	}
	return $unused_images;
}


function is_image_unused ($image_name)
{
	if (is_scalar($image_name) && is_string($image_name))
	{
		if ($image_name != '' && strlen(trim($image_name))>0)
		{
			$image_unsed = (get_count_of_image_usage($image_name) == 0);
			return $image_unsed;
		}
	}
	return false;
}

function get_count_of_image_usage ($image_name)
{
	$dup_check_query = osDBquery("SELECT COUNT(*) AS total FROM ".TABLE_PRODUCTS." WHERE products_image = '".$image_name."'");
	$dup_check = os_db_fetch_array($dup_check_query);
	$product_images_count = $dup_check['total'];

	$dup_check_query = osDBquery("SELECT COUNT(*) AS total FROM ".TABLE_PRODUCTS_IMAGES." WHERE image_name = '".$image_name."'");
	$mo_dup_check = os_db_fetch_array($dup_check_query);
	$mo_product_images_count = $mo_dup_check['total'];

	$total_count_of_image_usage = $product_images_count + $mo_product_images_count;
	return $total_count_of_image_usage;
}

function reArrayFiles(&$file_post) {

	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);

	for ($i=0; $i<$file_count; $i++) {
		foreach ($file_keys as $key) {
			$file_ary[$i][$key] = $file_post[$key][$i];
		}
	}

	return $file_ary;
}
?>