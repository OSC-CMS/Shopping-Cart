<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

require_once(dir_path('func_admin') . 'trumbnails_add_funcs.php');
if ($_GET['action'] == 'new_product')
{
	$dir_list = os_array_merge(array('0' => array('id' => '', 'text' => TEXT_SELECT_DIRECTORY)), os_get_files_in_dir(dir_path('images_original'), '', true));
	$file_list = os_array_merge(array('0' => array('id' => '', 'text' => TEXT_SELECT_IMAGE)), os_get_files_in_dir(dir_path('images_original')));

	//echo (PRODUCT_IMAGE_THUMBNAIL_WIDTH + 15);
?>
	<tr>
		<?php if ($pInfo->products_image) { ?>
		<td align="center" width="200">
			<?php echo os_image(http_path('images_thumbnail').$pInfo->products_image, TEXT_STANDART_IMAGE); ?><br /><br />
			<?php echo os_draw_selection_field('del_pic', 'checkbox', $pInfo->products_image); ?> <?php echo TEXT_DELETE; ?>
		</td>
		<?php } else { ?>
		<td align="center" width="200"></td>
		<?php } ?>

		<td class="main">
			<div style="width:100%;position:relative;padding-left:10px;">
				<div style="position:absolute;top:-20px;right:10px;">
					<img src="../images/pixel_trans.gif" id="getListOfProductPrevios_0"/>
				</div>
				<b><?php echo TEXT_PRODUCTS_IMAGE; ?></b> <?php echo ($pInfo->products_image) ? '('.$pInfo->products_image.')' : ''; ?><br />
				<div class="images-input" style="margin-top:10px;max-width:300px;"><?php echo os_draw_file_field('products_image'); ?></div><br />
				<?php echo os_draw_hidden_field('products_previous_image_0', $pInfo->products_image); ?>
				<?php echo TEXT_PRODUCTS_IMAGE_UPLOAD_DIRECTORY; ?><br />
				<?php echo os_draw_pull_down_menu('upload_dir_image_0', $dir_list, dirname($pInfo->products_image)."/"); ?><br /><br />
				<a href="javascript:;" onclick="getListOfProductImages(this, 0, '<?php echo dirname($pInfo->products_image); ?>', '<?php echo $pInfo->products_image; ?>')"><?php echo TEXT_SELECT_IMAGE; ?></a>

				<br /><div id="divOfImages0"></div>
			</div>
		</td>
	</tr>

	<?php
	if (MO_PICS > 0)
	{
		$mo_images = os_get_products_mo_images($pInfo->products_id);
		for ($i = 0; $i < MO_PICS; $i ++)
		{
		?>

		<tr><td colspan="2"><hr style="width:100%; border-top: 1px dashed #4378a1; "></td></tr>

		<tr>
			<?php if ($mo_images[$i]["image_name"]) { ?>
			<td align="center" width="200">
				<?php echo os_image(http_path('images_thumbnail').$mo_images[$i]["image_name"], TEXT_STANDART_IMAGE.' '.($i +1)); ?><br /><br />
				<?php echo os_draw_selection_field('del_mo_pic[]', 'checkbox', $mo_images[$i]["image_name"]); ?> <?php echo TEXT_DELETE; ?>
			</td>
			<?php } else { ?>
			<td align="center" width="200">...</td>
			<?php } ?>

			<td class="main">
				<div style="width:100%;position:relative;padding-left:10px;">
					<div style="position:absolute;top:-20px;right:10px;">
						<img src="../images/pixel_trans.gif" id="getListOfProductPrevios_<?php echo ($i +1); ?>"/>
					</div>
					<b><?php echo TEXT_PRODUCTS_IMAGE.' '.($i +1); ?></b> <?php echo ($mo_images[$i]["image_name"]) ? '('.$mo_images[$i]["image_name"].')' : ''; ?><br />
					<div class="images-input" style="margin-top:10px;max-width:300px;"><?php echo os_draw_file_field('mo_pics_'.$i); ?></div><br />
					<?php echo os_draw_hidden_field('products_previous_image_'.($i +1), $mo_images[$i]["image_name"]); ?>
					<?php echo TEXT_PRODUCTS_IMAGE_UPLOAD_DIRECTORY; ?><br />
					<?php echo os_draw_pull_down_menu('mo_pics_upload_dir_image_'.$i,$dir_list, dirname($mo_images[$i]["image_name"])."/"); ?><br /><br />
					<a href="javascript:;" onclick="getListOfProductImages(this, <?php echo ($i +1); ?>, 
					'<?php echo dirname($mo_images[$i]["image_name"]); ?>', '<?php echo $mo_images[$i]["image_name"]; ?>')"><?php echo TEXT_SELECT_IMAGE; ?></a>

					<br /><div id="divOfImages<?php echo ($i +1); ?>"></div>
					<br />
					<textarea name="mo_text_<?php echo $i; ?>" rows="3" cols="50"><?php echo $mo_images[$i]["text"]; ?></textarea>
				</div>
			</td>
		</tr>
		<?php
		}
	}
}





















?>