<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/
/*
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
  (c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
  (c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
  (c) 2008	 VamShop (2008/01/01); vamshop.com
*/

defined( '_VALID_OS' ) or die( 'OSC-CMS error permission.' );

class categories {
	function remove_categories($category_id) {

		$categories = os_get_category_tree($category_id, '', '0', '', true);
		$products = array ();
		$products_delete = array ();

		for ($i = 0, $n = sizeof($categories); $i < $n; $i ++) {
			$product_ids_query = os_db_query("SELECT products_id
						    	                                   FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
						    	                                   WHERE categories_id = '".$categories[$i]['id']."'");
			while ($product_ids = os_db_fetch_array($product_ids_query)) {
				$products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
			}
		}

		reset($products);
		while (list ($key, $value) = each($products)) {
			$category_ids = '';
			for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i ++) {
				$category_ids .= '\''.$value['categories'][$i].'\', ';
			}
			$category_ids = substr($category_ids, 0, -2);

			$check_query = os_db_query("SELECT COUNT(*) AS total
						    	                               FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
						    	                               WHERE products_id = '".$key."'
						    	                               AND categories_id NOT IN (".$category_ids.")");
			$check = os_db_fetch_array($check_query);
			if ($check['total'] < '1') {
				$products_delete[$key] = $key;
			}
		}

		@ os_set_time_limit(0);
		for ($i = 0, $n = sizeof($categories); $i < $n; $i ++) 
		{
			$this->remove_category($categories[$i]['id']);
		}

		reset($products_delete);
		while (list ($key) = each($products_delete)) {
			$this->remove_product($key);
		}
        
        do_action('remove_categories');
	} 
	function remove_category($category_id) 
	{
		$category_image_query = os_db_query("SELECT categories_image FROM ".TABLE_CATEGORIES." WHERE categories_id = '".os_db_input($category_id)."'");
		$category_image = os_db_fetch_array($category_image_query);

		$duplicate_image_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_CATEGORIES." WHERE categories_image = '".os_db_input($category_image['categories_image'])."'");
		$duplicate_image = os_db_fetch_array($duplicate_image_query);

		if ($duplicate_image['total'] < 2) {
			if (file_exists(dir_path('images').'categories/'.$category_image['categories_image'])) {
				@ unlink(dir_path('images').'categories/'.$category_image['categories_image']);
			}
		}

		os_db_query("DELETE FROM ".TABLE_CATEGORIES." WHERE categories_id = '".os_db_input($category_id)."'");
		os_db_query("DELETE FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = '".os_db_input($category_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id = '".os_db_input($category_id)."'");

		if (USE_CACHE == 'true') {
			os_reset_cache_block('categories');
			os_reset_cache_block('also_purchased');
		}
		
        global $categories_id;
		$categories_id = os_db_input($category_id);
		
		do_action('remove_category');
	} 
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

	function move_category($src_category_id, $dest_category_id) 
    {
		$src_category_id = os_db_prepare_input($src_category_id);
		$dest_category_id = os_db_prepare_input($dest_category_id);
		os_db_query("UPDATE ".TABLE_CATEGORIES."
				    	                 SET parent_id     = '".os_db_input($dest_category_id)."', last_modified = now() 
				    	               WHERE categories_id = '".os_db_input($src_category_id)."'");
         global $src_category_id;     
         global $dest_category_id;
                                  
         do_action('move_category');                                  
	}

	// copies a category to new parent category, takes argument to link or duplicate its products
	// arguments are "link" or "duplicate"
	// $copied is an array of ID's that were already newly created, and is used to prevent them from being
	// copied recursively again
	function copy_category($src_category_id, $dest_category_id, $ctype = "link") {
	if (!(in_array($src_category_id, $_SESSION['copied']))) {

			$src_category_id = os_db_prepare_input($src_category_id);
			$dest_category_id = os_db_prepare_input($dest_category_id);

			$ccopy_query = osDBquery("SELECT * FROM ".TABLE_CATEGORIES." WHERE categories_id = '".$src_category_id."'");
			$ccopy_values = os_db_fetch_array($ccopy_query);

			$cdcopy_query = osDBquery("SELECT * FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = '".$src_category_id."'");

			
			$sql_data_array = array ('parent_id'=>os_db_input($dest_category_id),
									'date_added'=>'NOW()',
									'last_modified'=>'NOW()',
									'categories_image'=>$ccopy_values['categories_image'],
									'categories_status'=>$ccopy_values['categories_status'],
									'categories_template'=>$ccopy_values['categories_template'],
									'listing_template'=>$ccopy_values['listing_template'],
									'sort_order'=>$ccopy_values['sort_order'],
									'products_sorting'=>$ccopy_values['products_sorting'],
									'products_sorting2'=>$ccopy_values['products_sorting2']);	
			
			
					$customers_statuses_array = os_get_customers_statuses();

		for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
			if (isset($customers_statuses_array[$i]['id']))
				$sql_data_array = array_merge($sql_data_array, array ('group_permission_'.$customers_statuses_array[$i]['id'] => @$product['group_permission_'.$customers_statuses_array[$i]['id']]));
		}
			
			os_db_perform(TABLE_CATEGORIES, $sql_data_array);

			$new_cat_id = os_db_insert_id();
			$_SESSION['copied'][] = $new_cat_id;
			$get_prod_query = osDBquery("SELECT products_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id = '".$src_category_id."'");
			while ($product = os_db_fetch_array($get_prod_query)) {
				if ($ctype == 'link') {
					$this->link_product($product['products_id'], $new_cat_id);
				}
				elseif ($ctype == 'duplicate') {
					$this->duplicate_product($product['products_id'], $new_cat_id);
				} else {
					die('Undefined copy type!');
				}
			}

			$src_pic = dir_path('images').'categories/'.$ccopy_values['categories_image'];
			if (is_file($src_pic)) {
				$get_suffix = explode('.', $ccopy_values['categories_image']);
				$suffix = array_pop($get_suffix);
				$dest_pic = $new_cat_id.'.'.$suffix;
				@ copy($src_pic, dir_path('images').'categories/'.$dest_pic);
				osDBquery("UPDATE ".DB_PREFIX."categories SET categories_image = '".$dest_pic."' WHERE categories_id = '".$new_cat_id."'");
			}
			while ($cdcopy_values = os_db_fetch_array($cdcopy_query)) {
				osDBquery("INSERT INTO ".TABLE_CATEGORIES_DESCRIPTION." (categories_id, language_id, categories_name, categories_heading_title, categories_description, categories_meta_title, categories_meta_description, categories_meta_keywords) VALUES ('".$new_cat_id."' , '".$cdcopy_values['language_id']."' , '".addslashes($cdcopy_values['categories_name'])."' , '".addslashes($cdcopy_values['categories_heading_title'])."' , '".addslashes($cdcopy_values['categories_description'])."' , '".addslashes($cdcopy_values['categories_meta_title'])."' , '".addslashes($cdcopy_values['categories_meta_description'])."' , '".addslashes($cdcopy_values['categories_meta_keywords'])."')");
			}
			$crcopy_query = osDBquery("SELECT categories_id FROM ".TABLE_CATEGORIES." WHERE parent_id = '".$src_category_id."'");
			while ($crcopy_values = os_db_fetch_array($crcopy_query)) {
				$this->copy_category($crcopy_values['categories_id'], $new_cat_id, $ctype);
			}

		}
        
        do_action('copy_category');
	}
    
	function remove_product($product_id) {
		$product_content_query = os_db_query("SELECT content_file FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id = '".os_db_input($product_id)."'");
		while ($product_content = os_db_fetch_array($product_content_query)) {   
		   		
   		$duplicate_content_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_PRODUCTS_CONTENT." WHERE content_file = '".os_db_input($product_content['content_file'])."' AND products_id != '".os_db_input($product_id)."'");

   		$duplicate_content = os_db_fetch_array($duplicate_content_query);

   		if ($duplicate_content['total'] == 0) {
   			@unlink(DIR_FS_DOCUMENT_ROOT.'media/products/'.$product_content['content_file']);
   		}
         		
   		os_db_query("DELETE FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id = '".os_db_input($product_id)."' AND (content_file = '".$product_content['content_file']."' OR content_file = '')");
   		
		}
	   
		$product_image_query = os_db_query("SELECT products_image FROM ".TABLE_PRODUCTS." WHERE products_id = '".os_db_input($product_id)."'");
		$product_image = os_db_fetch_array($product_image_query);

		$duplicate_image_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_PRODUCTS." WHERE products_image = '".os_db_input($product_image['products_image'])."'");
		$duplicate_image = os_db_fetch_array($duplicate_image_query);

		if ($duplicate_image['total'] < 2) {
			os_del_image_file($product_image['products_image']);
		}
		$mo_images_query = os_db_query("SELECT image_name FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".os_db_input($product_id)."'");
		while ($mo_images_values = os_db_fetch_array($mo_images_query)) {
			$duplicate_more_image_query = os_db_query("SELECT count(*) AS total FROM ".TABLE_PRODUCTS_IMAGES." WHERE image_name = '".$mo_images_values['image_name']."'");
			$duplicate_more_image = os_db_fetch_array($duplicate_more_image_query);
			if ($duplicate_more_image['total'] < 2) {
				os_del_image_file($mo_images_values['image_name']);
			}
		}


		
		os_db_query("DELETE FROM ".TABLE_SPECIALS." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET." WHERE products_id = '".os_db_input($product_id)."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." WHERE products_id = '".os_db_input($product_id)."'");

		$customers_status_array = os_get_customers_statuses();
		for ($i = 0, $n = sizeof($customers_status_array); $i < $n; $i ++) {
			if (isset($customers_statuses_array[$i]['id']))
				os_db_query("delete from ".TABLE_PERSONAL_OFFERS.$customers_statuses_array[$i]['id']." where products_id = '".os_db_input($product_id)."'");
		}

		$product_reviews_query = os_db_query("select reviews_id from ".TABLE_REVIEWS." where products_id = '".os_db_input($product_id)."'");
		while ($product_reviews = os_db_fetch_array($product_reviews_query)) {
			os_db_query("delete from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$product_reviews['reviews_id']."'");
		}

		os_db_query("delete from ".TABLE_REVIEWS." where products_id = '".os_db_input($product_id)."'");

		if (USE_CACHE == 'true') {
			os_reset_cache_block('categories');
			os_reset_cache_block('also_purchased');
		}
		
		global $products_id;
		$products_id = os_db_input($product_id);
		
	} 
	function delete_product($product_id, $product_categories) {

		for ($i = 0, $n = sizeof($product_categories); $i < $n; $i ++) {

      os_db_query("delete from " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " where products_id = " . os_db_input($product_id));

			os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
											              WHERE products_id   = '".os_db_input($product_id)."'
											              AND categories_id = '".os_db_input($product_categories[$i])."'");
		if (($product_categories[$i]) == 0) {
			$this->set_product_startpage($product_id, 0);
										  }
										}
 
		$product_categories_query = os_db_query("SELECT COUNT(*) AS total
								                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
								                                           WHERE products_id = '".os_db_input($product_id)."'");

		$product_categories = os_db_fetch_array($product_categories_query);

		if ($product_categories['total'] == '0') {
			$this->remove_product($product_id);
		}
        
        do_action('delete_product');
	} 

	function insert_product($products_data, $dest_category_id, $action = 'insert') 
	{
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
		
		$sql_data_array = array ('products_quantity' => os_db_prepare_input($products_data['products_quantity']), 
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
		'products_page_url' => os_db_prepare_input($products_data['products_page_url']));
		

		$sql_data_array = array_merge($sql_data_array, $permission_array);
		if (!$products_id || $products_id == '') {
			$new_pid_query = os_db_query("SHOW TABLE STATUS LIKE '".TABLE_PRODUCTS."'");
			$new_pid_query_values = os_db_fetch_array($new_pid_query);
			$products_id = $new_pid_query_values['Auto_increment'];
		}

		if (delete_images_from_db ($products_data, $products_id) > 0){
			$image_to_delete = os_array_merge( array($products_data['del_pic']), $products_data['del_mo_pic']);
			delete_unused_image_files ($image_to_delete);
		}
		if ($products_image = os_try_upload('products_image', dir_path('images_original') . $_POST['upload_dir_image_0'], '777', '')) {
			$products_image->filename = $_POST['upload_dir_image_0'].$products_image->filename;
			$pname_arr = explode('.', $products_image->filename);
			$nsuffix = array_pop($pname_arr);
			$products_image_name = $products_id.'_0.'.$nsuffix;
			$products_image_name = $_POST['upload_dir_image_0'].$products_image_name;
			
			delete_unused_image_file ($products_data['products_previous_image_0']);
			
			if (is_image_unused ($products_image->filename)) 
			{
			    if (is_file(dir_path('images_original').$products_image_name))
				{
          	        unlink(dir_path('images_original').$products_image_name);
					rename(dir_path('images_original').$products_image->filename, dir_path('images_original').$products_image_name);
				}
				else
				{
				   rename(dir_path('images_original').$products_image->filename, dir_path('images_original').$products_image_name);
				}
			} 
			else 
			{
			    if (is_file(dir_path('images_original').$products_image_name))
				{
				   unlink(dir_path('images_original').$products_image_name);
				   rename(dir_path('images_original').$products_image->filename, dir_path('images_original').$products_image_name);
				}
				else
				{
				   rename(dir_path('images_original').$products_image->filename, dir_path('images_original').$products_image_name);
				}
			}
			$sql_data_array['products_image'] = os_db_prepare_input($products_image_name);

			require (get_path('includes_admin').'product_thumbnail_images.php');
			require (get_path('includes_admin').'product_info_images.php');
			require (get_path('includes_admin').'product_popup_images.php');

		} else {
			if (isset($_POST['get_file_image_0']) && $_POST['get_file_image_0'] != "" && is_file( dir_path('images_original') .$_POST['get_file_image_0'])) {
				$products_image_name = $_POST['get_file_image_0'];
				$sql_data_array['products_image'] = os_db_prepare_input($products_image_name);
				require ( get_path('includes_admin') .'product_thumbnail_images.php');
				require ( get_path('includes_admin') .'product_info_images.php');
				require ( get_path('includes_admin') .'product_popup_images.php');
			} else
			$products_image_name = $products_data['products_previous_image_0'];
			
		}
		for ($img = 0; $img < MO_PICS; $img ++) {
			if ($pIMG = & os_try_upload('mo_pics_'.$img, dir_path('images_original') . $_POST['mo_pics_upload_dir_image_'.$img], '777', '')) {
				$pIMG->filename = $_POST['mo_pics_upload_dir_image_'.$img].$pIMG->filename;
				$pname_arr = explode('.', $pIMG->filename);
				$nsuffix = array_pop($pname_arr);
				$products_image_name = $products_id.'_'. ($img +1).'.'.$nsuffix;
				$products_image_name = $_POST['mo_pics_upload_dir_image_'.$img].$products_image_name;
				delete_unused_image_file($products_data['products_previous_image_'. ($img +1)]);
				
				@ os_del_image_file($products_image_name);
				rename(dir_path('images_original').$pIMG->filename, dir_path('images_original').$products_image_name);
				create_MO_PICS ($products_image_name, $img, $action, $products_id, $products_data, $_POST['mo_text_'.$img]);
			} else {
				$mo_field_name='mo_pics_get_file_image_'.$img;
				
				unset($mo_products_image_name);
				
				if (isset($_POST[$mo_field_name]) && $_POST[$mo_field_name] != '' && is_file(dir_path('images_original').$_POST[$mo_field_name])) {
					$mo_products_image_name = $products_data[$mo_field_name];
				} else  {
					$is_cur_image_deleted = false;
					if (@$products_data['del_mo_pic'] != ''){
						$previous_image_name = $products_data['products_previous_image_'. ($img +1)];
						foreach ($products_data['del_mo_pic'] AS $dummy => $val) {
							if ($val == $previous_image_name){
								$is_cur_image_deleted = true;
							}
						}
					}
					if (!$is_cur_image_deleted){
						$mo_products_image_name = $products_data['products_previous_image_'.($img+1)];
					}
				}
				if (isset ($mo_products_image_name) && strlen($mo_products_image_name)>0){
					create_MO_PICS ($mo_products_image_name, $img, $action, $products_id, $products_data, $_POST['mo_text_'.$img]);
				}
			}
		}

		if (isset ($products_data['products_image']) && os_not_null($products_data['products_image']) && ($products_data['products_image'] != 'none')) {
			$sql_data_array['products_image'] = os_db_prepare_input($products_data['products_image']);
		}

		if ($action == 'insert') {
			$insert_sql_data = array ('products_date_added' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_PRODUCTS, $sql_data_array);
			$products_id = os_db_insert_id();
			os_db_query("INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES."
								              SET products_id   = '".$products_id."',
								              categories_id = '".$dest_category_id."'");
		}
		elseif ($action == 'update') {
			$update_sql_data = array ('products_last_modified' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
			
			os_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \''.os_db_input($products_id).'\'');
		}

		$languages = os_get_languages();
		$i = 0;
		$group_query = os_db_query("SELECT customers_status_id
					                               FROM ".TABLE_CUSTOMERS_STATUS."
					                              WHERE language_id = '".(int) $_SESSION['languages_id']."'
					                                AND customers_status_id != '0'");
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
		$group_query = os_db_query("SELECT customers_status_id
					                               FROM ".TABLE_CUSTOMERS_STATUS."
					                              WHERE language_id = '".(int) $_SESSION['languages_id']."'
					                                AND customers_status_id != '0'");
		while ($group_values = os_db_fetch_array($group_query)) {
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
																	                     quantity       = '".$quantity."',
																	                     personal_offer = '".$staffelpreis."'");
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

          if ($_POST['extra_field']) {
            foreach ($_POST['extra_field'] as $key=>$val) {
              if (isset($extra_product_entry[$key])) {
                if ($val == '') os_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " where products_id = " . os_db_input($products_id) . " AND  products_extra_fields_id = " . $key);
                else os_db_query("UPDATE " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " SET products_extra_fields_value = '" . os_db_prepare_input($val) . "' WHERE products_id = " . os_db_input($products_id) . " AND  products_extra_fields_id = " . $key);
              }
              else {
                if ($val != '') os_db_query("INSERT INTO " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " (products_id, products_extra_fields_id, products_extra_fields_value) VALUES ('" . os_db_input($products_id) . "', '" . $key . "', '" . os_db_prepare_input($val) . "')");
              }
            }
          }
		  
		  $_POST['product_id'] = os_db_input($products_id);
		  do_action('insert_product');
	} 
    
	function duplicate_product($src_products_id, $dest_categories_id) {

		$product_query = osDBquery("SELECT *
				    	                                 FROM ".TABLE_PRODUCTS."
				    	                                WHERE products_id = '".os_db_input($src_products_id)."'");

		$product = os_db_fetch_array($product_query);
		if ($dest_categories_id == 0) { $startpage = 1; $products_status = 1; } else { $startpage= 0; $products_status = $product['products_status'];}
		
		$sql_data_array=array('products_quantity'=>$product['products_quantity'],
						'products_to_xml'=>$product['products_to_xml'],
						'products_model'=>$product['products_model'],
						'products_ean'=>$product['products_ean'],
						'products_shippingtime'=>$product['products_shippingtime'],
						'products_sort'=>$product['products_sort'],
						'products_startpage'=>$startpage,
						'products_sort'=>$product['products_sort'],
						'products_price'=>$product['products_price'],
						'products_discount_allowed'=>$product['products_discount_allowed'],
						'products_date_added'=>'now()',
						'products_date_available'=>$product['products_date_available'],
						'products_weight'=>$product['products_weight'],
						'products_status'=>$products_status,
						'products_tax_class_id'=>$product['products_tax_class_id'],
						'manufacturers_id'=>$product['manufacturers_id'],
						'product_template'=>$product['product_template'],
						'options_template'=>$product['options_template'],
						'products_fsk18'=>$product['products_fsk18'],
						);		
						
		$customers_statuses_array = os_get_customers_statuses();

		for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
			if (isset($customers_statuses_array[$i]['id']))
				$sql_data_array = array_merge($sql_data_array, array ('group_permission_'.$customers_statuses_array[$i]['id'] => $product['group_permission_'.$customers_statuses_array[$i]['id']]));

		}
		
		os_db_perform(TABLE_PRODUCTS, $sql_data_array);
		$dup_products_id = os_db_insert_id();
		if ($product['products_image'] != '') {
			$pname_arr = explode('.', $product['products_image']);
			$nsuffix = array_pop($pname_arr);
			$dup_products_image_name = $dup_products_id.'_0'.'.'.$nsuffix;
			osDBquery("UPDATE ".TABLE_PRODUCTS." SET products_image = '".$dup_products_image_name."' WHERE products_id = '".$dup_products_id."'");

			@ copy(dir_path('images_original').'/'.$product['products_image'], dir_path('images_original').'/'.$dup_products_image_name);
			@ copy(dir_path('images_info').'/'.$product['products_image'], dir_path('images_info').'/'.$dup_products_image_name);
			@ copy(dir_path('images_thumbnail').'/'.$product['products_image'], dir_path('images_thumbnail').'/'.$dup_products_image_name);
			@ copy(dir_path('images_popup').'/'.$product['products_image'], dir_path('images_popup').'/'.$dup_products_image_name);

		} else {
			unset ($dup_products_image_name);
		}

		$description_query = os_db_query("SELECT *
				    	                                     FROM ".TABLE_PRODUCTS_DESCRIPTION."
				    	                                    WHERE products_id = '".os_db_input($src_products_id)."'");

		$old_products_id = os_db_input($src_products_id);
		while ($description = os_db_fetch_array($description_query)) {
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

		os_db_query("INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES."
				    	                 SET products_id   = '".$dup_products_id."',
				    	                     categories_id = '".os_db_input($dest_categories_id)."'");

		$mo_images = os_get_products_mo_images($src_products_id);
		if (is_array($mo_images)) {
			foreach ($mo_images AS $dummy => $mo_img) {
				$pname_arr = explode('.', $mo_img['image_name']);
				$nsuffix = array_pop($pname_arr);
				$dup_products_image_name = $dup_products_id.'_'.$mo_img['image_nr'].'.'.$nsuffix;
				@ copy(dir_path('images_original').'/'.$mo_img['image_name'], dir_path('images_original').'/'.$dup_products_image_name);
				@ copy(dir_path('images_info').'/'.$mo_img['image_name'], dir_path('images_info').'/'.$dup_products_image_name);
				@ copy(dir_path('images_thumbnail').'/'.$mo_img['image_name'], dir_path('images_thumbnail').'/'.$dup_products_image_name);
				@ copy(dir_path('images_popup').'/'.$mo_img['image_name'], dir_path('images_popup').'/'.$dup_products_image_name);

				os_db_query("INSERT INTO ".TABLE_PRODUCTS_IMAGES."
								    			                 SET products_id = '".$dup_products_id."',
								    			                     image_nr    = '".$mo_img['image_nr']."',
								    			                     image_name  = '".$dup_products_image_name."'");
			}
		}

		$products_id = $dup_products_id;

		$i = 0;
		$group_query = os_db_query("SELECT customers_status_id
				    	                               FROM ".TABLE_CUSTOMERS_STATUS."
				    	                              WHERE language_id = '".(int) $_SESSION['languages_id']."'
				    	                                AND customers_status_id != '0'");

		while ($group_values = os_db_fetch_array($group_query)) {
			$i ++;
			$group_data[$i] = array ('STATUS_ID' => $group_values['customers_status_id']);
		}

		for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++) {
			if (@$group_data[$col]['STATUS_ID'] != '') {

				$copy_query = os_db_query("SELECT quantity,
								    			                                   personal_offer
								    			                              FROM ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']."
								    			                             WHERE products_id = '".$old_products_id."'");

				while ($copy_data = os_db_fetch_array($copy_query)) {
					os_db_query("INSERT INTO ".TABLE_PERSONAL_OFFERS.$group_data[$col]['STATUS_ID']."
										    				                 SET price_id       = '',
										    				                     products_id    = '".$products_id."',
										    				                     quantity       = '".$copy_data['quantity']."',
										    				                     personal_offer = '".$copy_data['personal_offer']."'");
				}
			}
		}
	} 
	
	function link_product($src_products_id, $dest_categories_id) {
		global $messageStack;
		$check_query = os_db_query("SELECT COUNT(*) AS total
				                                     FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
				                                     WHERE products_id   = '".os_db_input($src_products_id)."'
				                                     AND   categories_id = '".os_db_input($dest_categories_id)."'");
		$check = os_db_fetch_array($check_query);

		if ($check['total'] < '1') {
			os_db_query("INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES."
						                          SET products_id   = '".os_db_input($src_products_id)."',
						                          categories_id = '".os_db_input($dest_categories_id)."'");
						                   
	    if ($dest_categories_id == 0) {
			$this->set_product_status($src_products_id, $products_status);
			$this->set_product_startpage($src_products_id, 1);
	    							   }
		} else {
		}
	} 
	function move_product($src_products_id, $src_category_id, $dest_category_id) {
		$duplicate_check_query = os_db_query("SELECT COUNT(*) AS total
				    	                                         FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
				    	                                        WHERE products_id   = '".os_db_input($src_products_id)."'
				    	                                          AND categories_id = '".os_db_input($dest_category_id)."'");
		$duplicate_check = os_db_fetch_array($duplicate_check_query);

		if ($duplicate_check['total'] < 1) {
			os_db_query("UPDATE ".TABLE_PRODUCTS_TO_CATEGORIES."
						    		                 SET categories_id = '".os_db_input($dest_category_id)."'
						    		                 WHERE products_id   = '".os_db_input($src_products_id)."'");
	      
	        
		if ($dest_category_id == 0) {			
			$this->set_product_status($src_products_id, 1);
			$this->set_product_startpage($src_products_id, 1);
	    							   } 

		if ($src_category_id == 0) {
			 $this->set_product_status($src_products_id, $products_status);
			 $this->set_product_startpage($src_products_id, 0);
	    							   }				    		                 
		}
        
        do_action('move_product');
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
function create_MO_PICS ($mo_products_image_name, $mo_image_number, $performed_action, $products_id, &$products_data, $img_text = ''){
	$absolute_image_number = $mo_image_number+1;
	$mo_img = array ('products_id' => os_db_prepare_input($products_id), 
			'image_nr' => os_db_prepare_input($absolute_image_number), 
			'image_name' => os_db_prepare_input($mo_products_image_name),
			'text' => os_db_prepare_input($img_text));
	$previous_image_name = $products_data['products_previous_image_'.$absolute_image_number];

	
	if ($performed_action == 'insert') {
		os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
	} elseif ($performed_action == 'update' && $previous_image_name) {
		if ($products_data['del_mo_pic']) {
			foreach ($products_data['del_mo_pic'] AS $dummy => $val) {
				if ($val == $previous_image_name){
					os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
				}
				break;
			}
		}
		os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img, 'update', 'image_name = \''.os_db_input($previous_image_name).'\'');
	} elseif (!$previous_image_name){
		os_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img);
	}
	$products_image_name = $mo_products_image_name;
	
	require (get_path('includes_admin').'product_thumbnail_images.php');
	require (get_path('includes_admin').'product_info_images.php');
	require (get_path('includes_admin').'product_popup_images.php');
}

function delete_images_from_db (&$products_data, $product_id) {
	$modifications_count = 0;
	if (@$products_data['del_pic'] != '') {
		os_db_query("UPDATE ".TABLE_PRODUCTS."
							 SET products_image = ''
						   WHERE products_id    = '".os_db_input($product_id)."'");
		$modifications_count++;
	}

	if (@$products_data['del_mo_pic'] != '') {
		foreach ($products_data['del_mo_pic'] AS $dummy => $val) {
			os_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES."
							   WHERE products_id = '".os_db_input($product_id)."'
								 AND image_name  = '".$val."'");
			$modifications_count++;
		}
	}
	return $modifications_count;
}


function delete_image_files ($image_file_names){
	foreach ($image_file_names AS $image_name) {
		@ os_del_image_file($image_name);
	}
}

function delete_unused_image_file ($image_file_name){
	delete_unused_image_files (array($image_file_name));
}

function delete_unused_image_files ($image_file_names){
	$unused_images = get_unused_images($image_file_names);
	delete_image_files ($unused_images);
}

function get_unused_images ($checked_image_file_names){
	$checked_image_file_names = array_unique($checked_image_file_names);

	$unused_images = array();
	foreach ($checked_image_file_names AS $image_name) {
			if (is_image_unused($image_name)){
				$unused_images[] = $image_name;
		} else {
		}
	}
	return $unused_images;
}


function is_image_unused ($image_name){
	if (is_scalar($image_name) && is_string($image_name)){
		if ($image_name != '' && strlen(trim($image_name))>0){
	$image_unsed = (get_count_of_image_usage($image_name) == 0);
	return $image_unsed;
}
	}
	return false;

}

function get_count_of_image_usage ($image_name) {

	$dup_check_query = osDBquery("SELECT COUNT(*) AS total
														FROM ".TABLE_PRODUCTS."
													   WHERE products_image = '".$image_name."'");
	$dup_check = os_db_fetch_array($dup_check_query);
	$product_images_count = $dup_check['total'];

	$dup_check_query = osDBquery("SELECT COUNT(*) AS total
														FROM ".TABLE_PRODUCTS_IMAGES."
													   WHERE image_name = '".$image_name."'");
	$mo_dup_check = os_db_fetch_array($dup_check_query);
	$mo_product_images_count = $mo_dup_check['total'];

	$total_count_of_image_usage = $product_images_count + $mo_product_images_count;
	return $total_count_of_image_usage;
}
?>