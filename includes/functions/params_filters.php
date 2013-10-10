<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

	function get_params_listing_sql($listing_sql, $categories_id,  $selected_blocks)
	{
		if( count($selected_blocks) == 0) return $listing_sql; 
		// p.products_statuses_id, 

		$sql = "select p.products_fsk18, 
					p.products_shippingtime, 
					p.products_model, 
					p.products_ean, 
					p.products_status, 
					pd.products_name, 
					m.manufacturers_name, 
					p.products_quantity, 
					p.products_image, 
					p.products_weight, 
					pd.products_short_description, 
					pd.products_description, 
					p.products_id, 
					p.manufacturers_id, 
					p.products_price, 
					p.products_vpe, 
					p.products_vpe_status, 
					p.products_vpe_value, 
					p.products_discount_allowed, 
					p.products_tax_class_id 
				from products p 
				left join products_description pd on pd.products_id = p.products_id
				left join products_to_categories p2c on p2c.products_id = p.products_id
				left join manufacturers m on p.manufacturers_id = m.manufacturers_id ";
		$tables = 0;
		$wheres = array();
		
		$price_min = -1;
		if(isset($_GET['price_min']) && intval($_GET['price_min']) != 0 )
		{
			$wheres[] = " p.products_price >= '".intval($_GET['price_min'])."' ";;
		}
		$price_max = -1;
		if(isset($_GET['price_max']) && intval($_GET['price_max']) != 0 )
		{
			$wheres[] = " p.products_price <= '".intval($_GET['price_max'])."' ";;
		}
		
		$wheres[] = "  p.products_status = '1' and p2c.categories_id = '".(int) $_GET['cat']."' ";
		$wheres[] = " pd.language_id = '1' ";
		
			if(count($wheres) > 0){
				$sql .= " WHERE ".join(" AND ", $wheres);
			}
			$sql .= " ORDER BY pd.products_name ASC";
//			print $sql;
			return $sql;
	}

?>