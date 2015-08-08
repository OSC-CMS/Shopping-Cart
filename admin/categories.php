<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require_once ('includes/top.php');

require_once (_CLASS_ADMIN.FILENAME_IMAGEMANIPULATOR);
require_once (_CLASS_ADMIN.'currencies.php');

$currencies = new currencies();

if (@$_GET['function'])
{
	switch ($_GET['function'])
	{
		case 'delete' :
			os_db_query("DELETE FROM ".TABLE_PERSONAL_OFFERS.(int) $_GET['statusID']." WHERE products_id = '".(int) $_GET['pID']."' AND quantity    = '".(int) $_GET['quantity']."'");
		break;
	}

	set_categories_url_cache();
	set_category_cache();
	os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&action=new_product&pID='.(int) $_GET['pID']));
}

if (isset ($_POST['multi_status_on']))
{
	if (is_array($_POST['multi_categories']))
	{
		foreach ($_POST['multi_categories'] AS $category_id)
		{
			$cartet->products->changeCategoriesStatus($category_id, '1');
		}
	}
	if (is_array($_POST['multi_products']))
	{
		foreach ($_POST['multi_products'] AS $product_id)
		{
			$cartet->products->changeProductStatus(array(
				'column' => 'products_status',
				'status' => 1,
				'id' => $product_id,
			));
		}
	}

	set_categories_url_cache();
	set_category_cache();
	os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.os_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
}

if (isset ($_POST['multi_status_off']))
{
	if (is_array($_POST['multi_categories']))
	{
		foreach ($_POST['multi_categories'] AS $category_id)
		{
			$cartet->products->changeCategoriesStatus($category_id, '0');
		}
	}
	if (is_array($_POST['multi_products']))
	{
		foreach ($_POST['multi_products'] AS $product_id)
		{
			$cartet->products->changeProductStatus(array(
				'column' => 'products_status',
				'status' => 0,
				'id' => $product_id,
			));
		}
	}

	set_categories_url_cache();
	set_category_cache();
	os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.os_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
}

if (@$_GET['action']) 
{
	switch ($_GET['action'])
	{
		case 'update_category' :
			$cartet->products->saveCategory($_POST, '', 'update');
			set_categories_url_cache();
			os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath']));
		break;

		case 'insert_category' :
			$cartet->products->saveCategory($_POST, $current_category_id);
			set_categories_url_cache();
			set_category_cache();
			os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath']));
		break;

		case 'update_product' :
			$cartet->products->saveProduct($_POST);

			os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath']));
		break;

		case 'insert_product' :
			$cartet->products->saveProduct($_POST);

			os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath']));
		break;

		case 'edit_crossselling' :
			$cartet->products->saveCrossSelling($_GET);
			set_products_url_cache();
			set_categories_url_cache();
		break;
	}
}


if (is_dir(dir_path('images'))) 
{
	if (!is_writeable(dir_path('images')))
	{
		$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE.' '. dir_path('images'), 'error');
	}	
} 
else 
{
	$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST.' '.dir_path('images'), 'error');
}

$_cPath = '';
if ($_GET['cPath'])
	$_cPath = '?cPath='.$_GET['cPath'];

$breadcrumb->add(BOX_HEADING_PRODUCTS, os_href_link(FILENAME_CATEGORIES.$_cPath));

if (isset($_GET['action']) && ($_GET['action'] == 'new_category' || $_GET['action'] == 'edit_category'))
	include (_MODULES_ADMIN.'new_category.php');
elseif (isset($_GET['action']) && $_GET['action'] == 'new_product')
	include (_MODULES_ADMIN.'new_product.php');
elseif (isset($_GET['action']) && $_GET['action'] == 'edit_crossselling')
	include (_MODULES_ADMIN.'cross_selling.php');
else
{
	$cPath = (isset($_GET['cPath']) && !empty($_GET['cPath'])) ? $_GET['cPath'] : 0;

	include (_MODULES_ADMIN.'categories_view.php');
}

$main->bottom();
?>