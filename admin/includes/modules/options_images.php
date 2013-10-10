<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/
 
defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

$img_query = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_IMAGES." WHERE products_options_values_id='".os_db_prepare_input($_GET['value_id'])."'";
$img_query = os_db_query($img_query);
$_imgData=array();
while ($_img = os_db_fetch_array($img_query)) {
	$_imgData[$_img['image_nr']]=$_img['image_name'];
}

	if ($_imgData[0]!='') {
		echo os_image(http_path('images').'product_options/'.$_imgData[0], 'Image 1').'<br />';
		echo os_draw_selection_field('del_pic', 'checkbox', $_imgData[0]).' '.TABLE_TEXT_DELETE.'<br />';
	}

	echo os_draw_file_field('value_image').'<br>';
	if (MO_PICS > 0) {
		$mo_images = os_get_options_mo_images(os_db_prepare_input($_GET['value_id']));
		for ($i = 0; $i < MO_PICS; $i ++) {
			if ($mo_images[$i]["image_name"]) {
				echo os_image(http_path('images').'product_options/'.$mo_images[$i]["image_name"], 'Image '. ($i +1)).'<br />';
			} 
			echo '<br />'.TEXT_OPTIONS_IMAGE.' '. ($i +1).'<br />'.os_draw_file_field('mo_pics_'.$i).'<br />'.'&nbsp;'.$mo_images[$i]["image_name"].os_draw_hidden_field('products_previous_image_'. ($i +1), $mo_images[$i]["image_name"]);
			if (isset ($mo_images[$i]["image_name"])) {
				echo os_draw_selection_field('del_mo_pic[]', 'checkbox', $mo_images[$i]["image_name"]).' '.TABLE_TEXT_DELETE.'<br />';
			} 
		}
	}

//}
?>