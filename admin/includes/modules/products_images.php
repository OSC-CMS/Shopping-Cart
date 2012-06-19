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

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

require_once(dir_path('func_admin') . 'trumbnails_add_funcs.php');
if ($_GET['action'] == 'new_product') {

$dir_list = os_array_merge(array('0' => array('id' => '', 'text' => TEXT_SELECT_DIRECTORY)),os_get_files_in_dir(dir_path('images_original'), '', true));
$file_list = os_array_merge(array('0' => array('id' => '', 'text' => TEXT_SELECT_IMAGE)),os_get_files_in_dir(dir_path('images_original')));

	if ($pInfo->products_image) {
		echo '<tr><td colspan="4"><table><tr><td align="center" class="main" width="'. (PRODUCT_IMAGE_THUMBNAIL_WIDTH + 15).'">'.os_image( http_path('images_thumbnail') .$pInfo->products_image, TEXT_STANDART_IMAGE).'</td>';
	}
	echo '<td class="main">'.TEXT_PRODUCTS_IMAGE.'<br />'.os_draw_file_field('products_image').'<br />'.'&nbsp;'.$pInfo->products_image.os_draw_hidden_field('products_previous_image_0', $pInfo->products_image);
	echo '<br />' . TEXT_PRODUCTS_IMAGE_UPLOAD_DIRECTORY . '<br />' . os_draw_pull_down_menu('upload_dir_image_0',$dir_list, dirname($pInfo->products_image).'/');
	echo '<br /><br />' . TEXT_PRODUCTS_IMAGE_GET_FILE . '<br />' . os_draw_pull_down_menu('get_file_image_0',$file_list,$pInfo->products_image);
	if ($pInfo->products_image != '') {
		echo '</tr><tr><td align="center" class="main" valign="middle">'.os_draw_selection_field('del_pic', 'checkbox', $pInfo->products_image).' '.TEXT_DELETE.'</td></tr></table>';
	} else {
		echo '</td></tr>';
	}

	if (MO_PICS > 0) {
		$mo_images = os_get_products_mo_images($pInfo->products_id);
		for ($i = 0; $i < MO_PICS; $i ++) 
		{

			if ($mo_images[$i]["image_name"]) {
				echo '<tr><td colspan="4"><table><tr><td align="center" class="main" width="'. (PRODUCT_IMAGE_THUMBNAIL_WIDTH + 15).'">'.os_image(http_path('images_thumbnail') .$mo_images[$i]["image_name"], TEXT_STANDART_IMAGE . ' '. ($i +1)).'</td>';
			} else {
				echo '<tr>';
			}
			echo '<td class="main">'.TEXT_PRODUCTS_IMAGE.' '. ($i +1).'<br />'.os_draw_file_field('mo_pics_'.$i).'<br />'.'&nbsp;'.$mo_images[$i]["image_name"].os_draw_hidden_field('products_previous_image_'. ($i +1), $mo_images[$i]["image_name"]);
	echo '<br />' . TEXT_PRODUCTS_IMAGE_UPLOAD_DIRECTORY . '<br />' . os_draw_pull_down_menu('mo_pics_upload_dir_image_'.$i,$dir_list, dirname($mo_images[$i]["image_name"]).'/');
	echo '<br /><br />' . TEXT_PRODUCTS_IMAGE_GET_FILE . '<br />' . os_draw_pull_down_menu('mo_pics_get_file_image_'.$i,$file_list,$mo_images[$i]["image_name"]);
			if (isset ($mo_images[$i]["image_name"])) {
				echo '</tr><tr><td align="center" class="main" valign="middle">'.os_draw_selection_field('del_mo_pic[]', 'checkbox', $mo_images[$i]["image_name"]).' '.TEXT_DELETE.'</td></tr></table>';
			} else {
				echo '</td></tr>';
			}
		}
	}

}
?>