<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

include ('includes/top.php');

//$osTemplate = new osTemplate;

$product_info_query = os_db_query("select * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_status = '1' and p.products_id = '".(int) $_GET['products_id']."' and pd.products_id = p.products_id and pd.language_id = '".(int) $_SESSION['languages_id']."'");
$product_info = os_db_fetch_array($product_info_query);

$products_price = $osPrice->GetPrice($product_info['products_id'], $format = true, 1, $product_info['products_tax_class_id'], $product_info['products_price'], 1);

$products_attributes_query = os_db_query("select count(*) as total from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".(int) $_GET['products_id']."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."'");
$products_attributes = os_db_fetch_array($products_attributes_query);
if ($products_attributes['total'] > 0) {
	$products_options_name_query = os_db_query("select distinct popt.products_options_id, popt.products_options_name from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".(int) $_GET['products_id']."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."' order by popt.products_options_name");
	while ($products_options_name = os_db_fetch_array($products_options_name_query)) {
		$selected = 0;

		$products_options_query = os_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock, pa.attributes_model from ".TABLE_PRODUCTS_ATTRIBUTES." pa, ".TABLE_PRODUCTS_OPTIONS_VALUES." pov where pa.products_id = '".(int) $_GET['products_id']."' and pa.options_id = '".$products_options_name['products_options_id']."' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '".(int) $_SESSION['languages_id']."'");
		while ($products_options = os_db_fetch_array($products_options_query)) {
			$module_content[] = array ('GROUP' => $products_options_name['products_options_name'], 'NAME' => $products_options['products_options_values_name']);

			if ($products_options['options_values_price'] != '0') {

				if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
					$tax_rate = $osPrice->TAX[$product_info['products_tax_class_id']];
					$products_options['options_values_price'] = os_add_tax($products_options['options_values_price'], $osPrice->TAX[$product_info['products_tax_class_id']]);
				}
				if ($_SESSION['customers_status']['customers_status_show_price'] == 1) {
					$module_content[sizeof($module_content) - 1]['NAME'] .= ' ('.$products_options['price_prefix'].$osPrice->Format($products_options['options_values_price'], true,0,true).')';
				}
			}
		}
	}
}

// assign language to template for caching
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('charset', $_SESSION['language_charset']);
$osTemplate->assign('tpl_path', _HTTP_THEMES_C);

$extra_fields_query = osDBquery("
                      SELECT pef.products_extra_fields_status as status, pef.products_extra_fields_name as name, ptf.products_extra_fields_value as value
                      FROM ". TABLE_PRODUCTS_EXTRA_FIELDS ." pef
             LEFT JOIN  ". TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS ." ptf
            ON ptf.products_extra_fields_id=pef.products_extra_fields_id
            WHERE ptf.products_id=". (int) $_GET['products_id'] ." and ptf.products_extra_fields_value<>'' and (pef.languages_id='0' or pef.languages_id='".$_SESSION['languages_id']."')
            ORDER BY products_extra_fields_order");

  while ($extra_fields = os_db_fetch_array($extra_fields_query,true)) {
        if (! $extra_fields['status'])  // show only enabled extra field
           continue;
  
  $extra_fields_data[] = array (
  'NAME' => $extra_fields['name'], 
  'VALUE' => $extra_fields['value']
  );
  
  }

  $osTemplate->assign('extra_fields_data', $extra_fields_data);
  
$image = '';
if ($product_info['products_image'] != '') {
	$image = http_path('images_thumbnail').$product_info['products_image'];
}
if ($_SESSION['customers_status']['customers_status_show_price'] != 0) {
	$tax_rate = $osPrice->TAX[$product_info['products_tax_class_id']];
	// price incl tax
	if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
		$osTemplate->assign('PRODUCTS_TAX_INFO', sprintf(TAX_INFO_INCL, $tax_rate.' %'));
	}
	// excl tax + tax at checkout
	if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
		$osTemplate->assign('PRODUCTS_TAX_INFO', sprintf(TAX_INFO_ADD, $tax_rate.' %'));
	}
	// excl tax
	if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
		$osTemplate->assign('PRODUCTS_TAX_INFO', sprintf(TAX_INFO_EXCL, $tax_rate.' %'));
	}
}
$osTemplate->assign('PRODUCTS_NAME', $product_info['products_name']);
$osTemplate->assign('PRODUCTS_EAN', $product_info['products_ean']);
$osTemplate->assign('PRODUCTS_QUANTITY', $product_info['products_quantity']);
$osTemplate->assign('PRODUCTS_WEIGHT', $product_info['products_weight']);
$osTemplate->assign('PRODUCTS_STATUS', $product_info['products_status']);
$osTemplate->assign('PRODUCTS_ORDERED', $product_info['products_ordered']);
$osTemplate->assign('PRODUCTS_MODEL', $product_info['products_model']);
$osTemplate->assign('PRODUCTS_DESCRIPTION', $product_info['products_description']);
$osTemplate->assign('PRODUCTS_IMAGE', $image);
$osTemplate->assign('PRODUCTS_PRICE', $products_price['formated']);
if (ACTIVATE_SHIPPING_STATUS == 'true') {
	$osTemplate->assign('SHIPPING_NAME', $main->getShippingStatusName($product_info['products_shippingtime']));
	if ($shipping_status['image'] != '')
		$osTemplate->assign('SHIPPING_IMAGE', $main->getShippingStatusImage($product_info['products_shippingtime']));
}
if (SHOW_SHIPPING == 'true')
	$osTemplate->assign('PRODUCTS_SHIPPING_LINK', ' '.SHIPPING_EXCL.'<a href="javascript:newWin=void(window.open(\''.os_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS).'\', \'popup\', \'toolbar=0, width=640, height=600\'))"> '.SHIPPING_COSTS.'</a>');	
		

$discount = 0.00;
if ($_SESSION['customers_status']['customers_status_public'] == 1 && $_SESSION['customers_status']['customers_status_discount'] != '0.00') {
	$discount = $_SESSION['customers_status']['customers_status_discount'];
	if ($product_info['products_discount_allowed'] < $_SESSION['customers_status']['customers_status_discount'])
		$discount = $product_info['products_discount_allowed'];
	if ($discount != '0.00')
		$osTemplate->assign('PRODUCTS_DISCOUNT', $discount.'%');
}

if ($product_info['products_vpe_status'] == 1 && $product_info['products_vpe_value'] != 0.0 && $products_price['plain'] > 0)
	$osTemplate->assign('PRODUCTS_VPE', $osPrice->Format($products_price['plain'] * (1 / $product_info['products_vpe_value']), true).TXT_PER.os_get_vpe_name($product_info['products_vpe']));
$osTemplate->assign('module_content', $module_content);

		$mo_images = os_get_products_mo_images($product_info['products_id']);
        if ($mo_images != false) {
    $osTemplate->assign('PRODUCTS_MO_IMAGES', $mo_images);
            foreach ($mo_images as $img) 
			{
			    if ( is_file( dir_path('images_info') . $img['image_name'] ) )
				{
                   $mo_img[] = array('PRODUCTS_MO_IMAGE' => http_path('images_info') . $img['image_name']);
                   $osTemplate->assign('mo_img', $mo_img);
				}
            }
        }
		//mo_images EOF

// set cache ID
 if (!CacheCheck()) {
	$osTemplate->caching = 0;
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
}
$cache_id = $_SESSION['language'].'_'.$product_info['products_id'];

$osTemplate->display(CURRENT_TEMPLATE.'/module/print_product_info.html', $cache_id);
?>