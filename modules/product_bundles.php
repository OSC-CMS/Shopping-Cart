<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	AddOn-Modul zur Darstellung von Bundles
*	Erstellt 2006 von API-Solutions Ltd. & Co. KG Div.Omega-Soft - http://www.omega-soft.de
*	Erstellt für für xt:Commerce 3.0.4-SP1 - http://www.xt-commerce.com
*	Copyright (C) 2006 by API-Solutions Ltd. & Co. KG - http://www.api-solutions-ltd.com
*	Version 2.0.2 Stand: 23.05.2006
*	Released under the GNU General Public License  http://www.gnu.org
*
*---------------------------------------------------------
*/

$module_smarty = new osTemplate;

$products_bundle_query = osDBquery("select count(*) as total from ".DB_PREFIX."products_bundles bun, ".TABLE_PRODUCTS_DESCRIPTION." pdes where bun.bundle_id='".$product->data['products_id']."' and pdes.language_id = '".(int)$_SESSION['languages_id']."'");
$products_bundles = os_db_fetch_array($products_bundle_query, true);

if ($products_bundles['total'] > 0)
{
	$bundle_query = getBundleProducts($product->data['products_id'], true);

	while($bundle_data = os_db_fetch_array($bundle_query))
	{
		$image = '';
		if ($bundle_data['products_image'] != '' && is_file(dir_path('images_thumbnail').$bundle_data['products_image']))
			$image = http_path('images_thumbnail').$bundle_data['products_image'];
		else
			$image = http_path('images_thumbnail').'../noimage.gif';

		$bundle_products_price = $osPrice->GetPrice($bundle_data['products_id'], true, $bundle_data['subproduct_qty'], $bundle_data['products_tax_class_id'], $bundle_data['products_price']*$bundle_data['subproduct_qty'], 1, 0, $bundle_data['products_discount_allowed'], $bundle_data['price_currency_code']);

		$products_bundle_data[] = array
		(
			'PLINK' => os_href_link(FILENAME_PRODUCT_INFO, os_product_link($bundle_data['products_id'], $bundle_data['products_name'])),
			'IMAGE' => $image,
			'PRODUCTS_ID' => $bundle_data['products_id'],
			'QTY' => $bundle_data['subproduct_qty'],
			'NAME' => $bundle_data['products_name'],
			'PRICE' => $bundle_products_price['price']['formated'],
			'PRICE_PLAIN' => $bundle_products_price['price']['plain'],
		);

		$bSum += $bundle_products_price['price']['plain'];
	}

	$bSaving = $bSum - $products_price['price']['plain'];
	$bundle_sum = $osPrice->Format($bSum, true);
	$bundle_saving = $osPrice->Format($bSaving, true);
}

$info->assign('PRODUCTS_BUNDLE', $product->data['products_bundle']);
$info->assign('PRODUCTS_BUNDLE_DATA', $products_bundle_data);
$info->assign('PRODUCTS_BUNDLE_SUM', $bundle_sum);
$info->assign('PRODUCTS_BUNDLE_SAVING', $bundle_saving);