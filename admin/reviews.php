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

$breadcrumb->add(HEADING_TITLE, FILENAME_REVIEWS);

$main->head();
$main->top_menu();
?>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_REVIEW_AUTHOR; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_RATING; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_LAST_MODIFIED; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_REVIEW_READ; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
<?php
$reviews_query_raw = "select * from ".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd where r.reviews_id = rd.reviews_id ORDER BY r.reviews_id DESC";
$reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $reviews_query_raw, $reviews_query_numrows);
$reviews_query = os_db_query($reviews_query_raw);
while ($reviews = os_db_fetch_array($reviews_query))
{
	?>
	<tr class="default">
		<td><a target="_blank" href="<?php echo FILENAME_CATEGORIES; ?>?pID=<?php echo $reviews['products_id']; ?>&action=new_product"><?php echo os_get_products_name($reviews['products_id']); ?></a></td>
		<td><?php echo $reviews['customers_name']; ?></td>
		<td class="tcenter"><?php echo os_image(http_path('icons_admin').'stars_'.$reviews['reviews_rating'].'.gif'); ?></td>
		<td class="tcenter"><?php echo os_date_short($reviews['date_added']); ?></td>
		<td class="tcenter"><?php echo os_date_short($reviews['last_modified']); ?></td>
		<td class="tcenter"><?php echo $reviews['reviews_read']; ?></td>
		<td class="tcenter">
		<?php
			echo '<a '.(($reviews['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$reviews['reviews_id'].'_0_status" data-column="status" data-action="reviews_status" data-id="'.$reviews['reviews_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
			echo '<a '.(($reviews['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$reviews['reviews_id'].'_1_status" data-column="status" data-action="reviews_status" data-id="'.$reviews['reviews_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
		?>
		</td>
		<td width="100">
			<div class="btn-group pull-right">
				<?php echo $cartet->html->link(
					'<i class="icon-edit"></i>',
					os_href_link(FILENAME_REVIEWS, 'rID='.$reviews['reviews_id'].'&action=edit'),
					array(
						'class' => 'ajax-load-page btn btn-mini',
						'data-load-page' => 'reviews&r_id='.$reviews['reviews_id'].'&action=edit',
						'data-toggle' => 'modal',
						'title' => BUTTON_EDIT,
					)
				); ?>
				<?php echo $cartet->html->link(
					'<i class="icon-trash"></i>',
					os_href_link(FILENAME_REVIEWS, 'rID='.$reviews['reviews_id'].'&action=delete'),
					array(
						'class' => 'ajax-load-page btn btn-mini',
						'data-load-page' => 'reviews&r_id='.$reviews['reviews_id'].'&action=delete',
						'data-toggle' => 'modal',
						'title' => BUTTON_DELETE,
					)
				); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<div class="table-big-text">
				<?php echo nl2br(os_db_output(os_break_string($reviews['reviews_text'], 20))); ?>
			</div>
			<?php if ($reviews['reviews_text_admin']) { ?>
				<div class="well well-small">
					<?php echo nl2br(os_db_output(os_break_string($reviews['reviews_text_admin'], 20))); ?>
				</div>
			<?php } ?>
		</td>
	</tr>
	<?php
}
?>
</table>

<div class="action-table">
	<div class="pull-right">
		<div class="pagination pagination-mini pagination-right">
			<?php echo $reviews_split->display_count($reviews_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
			<?php echo $reviews_split->display_links($reviews_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>

<?php $main->bottom(); ?>