<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

?>
<table class="table table-striped table-condensed table-content well-table">
	<tbody>
	<?php
	$reviews_query_raw = os_db_query("select * from ".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd where r.reviews_id = rd.reviews_id order by r.reviews_id desc limit 20");
	while ($reviews = os_db_fetch_array($reviews_query_raw))
	{
		?>
		<tr>
			<td><a target="_blank" href="<?php echo FILENAME_CATEGORIES; ?>?pID=<?php echo $reviews['products_id']; ?>&action=new_product"><?php echo os_get_products_name($reviews['products_id']); ?></a></td>
			<td><?php echo $reviews['customers_name']; ?></td>
			<td><?php echo os_image(http_path('icons_admin').'stars_'.$reviews['reviews_rating'].'.gif'); ?></td>
			<td><?php echo os_date_short($reviews['date_added']); ?></td>
			<td class="tcenter">
				<?php
				echo '<a '.(($reviews['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$reviews['reviews_id'].'_0_status" data-column="status" data-action="reviews_status" data-id="'.$reviews['reviews_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
				echo '<a '.(($reviews['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$reviews['reviews_id'].'_1_status" data-column="status" data-action="reviews_status" data-id="'.$reviews['reviews_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
		</tr>
		<tr>
			<td colspan="6"><div class="table-big-text"><?php echo nl2br(os_db_output(os_break_string($reviews['reviews_text'], 20))); ?></div></td>
		</tr>
	<?php } ?>
	</tbody>
</table>