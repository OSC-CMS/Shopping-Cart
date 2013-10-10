<?php 
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');
require(_MODULES_ADMIN.'new_attributes_config.php');

if (isset($cPathID) && $_POST['action'] == 'change')
{
	include(_MODULES_ADMIN.'new_attributes_change.php');
	os_redirect('./'.FILENAME_CATEGORIES.'?cPath='.$cPathID.'&pID='.$_POST['current_product_id']);
}

if (empty($_POST['action']))
	foreach (array('action', 'current_product_id', 'cpath') as $k)
if (empty($_POST[$k]) && !empty($_GET[$k]))
	$_POST[$k] = $_GET[$k];

switch($_POST['action'])
{
	case 'edit':
		if ($_POST['copy_product_id'] != 0)
		{
			$attrib_query = os_db_query("SELECT products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = " . $_POST['copy_product_id']);
			while ($attrib_res = os_db_fetch_array($attrib_query))
			{
				os_db_query("INSERT into ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder) VALUES ('" . $_POST['current_product_id'] . "', '" . $attrib_res['options_id'] . "', '" . $attrib_res['options_values_id'] . "', '" . $attrib_res['options_values_price'] . "', '" . $attrib_res['price_prefix'] . "', '" . $attrib_res['attributes_model'] . "', '" . $attrib_res['attributes_stock'] . "', '" . $attrib_res['options_values_weight'] . "', '" . $attrib_res['weight_prefix'] . "','" . $attrib_res['sortorder'] ."')");
			}
		}
		$pageTitle = TITLE_EDIT.': ' . os_findTitle($_POST['current_product_id'], $languageFilter);
		include( dir_path_admin('modules').'new_attributes_include.php');
	break;

	case 'change':
		$pageTitle = TITLE_UPDATED;
		include( dir_path_admin('modules').'new_attributes_change.php');
		include( dir_path_admin('modules').'new_attributes_select.php');
	break;

	default:
		$pageTitle = TITLE_EDIT;
		include( dir_path_admin('modules').'new_attributes_select.php');
	break;
}

$main->bottom();
?>