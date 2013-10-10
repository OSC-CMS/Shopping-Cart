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

$breadcrumb->add(HEADING_TITLE, FILENAME_FEATURED);

$main->head();
$main->top_menu();
?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span8">
			
		</div>
		<div class="span4">
			<div class="btn-group pull-right">
				<?php echo $cartet->html->link(
					BUTTON_NEW_PRODUCTS,
					os_href_link(FILENAME_FEATURED, 'action=new'),
					array(
						'class' => 'ajax-load-page btn btn-mini btn-info',
						'data-load-page' => 'featured&action=new',
						'data-toggle' => 'modal',
						)
				); ?>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_FEATURED_QUANTITY; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_EXPIRES_DATE; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_DATE_ADDED; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
<?php
$featured_query_raw = "select p.products_id, pd.products_name,p.products_tax_class_id, p.products_price, f.featured_id, f.featured_quantity, f.featured_date_added, f.featured_last_modified, f.expires_date, f.date_status_change, f.status from ".TABLE_PRODUCTS." p, ".TABLE_FEATURED." f, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and pd.language_id = '".(int)$_SESSION['languages_id']."' and p.products_id = f.products_id order by f.featured_date_added DESC";
$featured_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $featured_query_raw, $featured_query_numrows);
$featured_query = os_db_query($featured_query_raw);
while ($featured = os_db_fetch_array($featured_query))
{
	?>
	<tr>
		<td><?php echo $featured['products_name']; ?></td>
		<td class="tcenter"><?php echo $featured['featured_quantity']; ?></td>
		<td class="tcenter">
			<?php echo $featured['expires_date']; ?>
			<?php if ($featured['date_status_change']) { ?>
			<i class="icon-edit tt" title="<?php echo TEXT_INFO_STATUS_CHANGE; ?>: <?php echo $featured['date_status_change']; ?>"></i>
			<?php } ?>
		</td>
		<td class="tcenter">
			<?php echo $featured['featured_date_added']; ?>
			<?php if ($featured['featured_last_modified']) { ?>
			<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $featured['featured_last_modified']; ?>"></i>
			<?php } ?>
		</td>
		<td class="tcenter">
			<?php
				echo '<a '.(($featured['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$featured['featured_id'].'_0_status" data-column="status" data-action="featured_status" data-id="'.$featured['featured_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
				echo '<a '.(($featured['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$featured['featured_id'].'_1_status" data-column="status" data-action="featured_status" data-id="'.$featured['featured_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
		</td>
		<td width="100">
			<div class="btn-group pull-right">
				<?php echo $cartet->html->link(
					'<i class="icon-edit"></i>',
					os_href_link(FILENAME_FEATURED, 'fID='.$featured['featured_id'].'&action=edit'),
					array(
						'class' => 'ajax-load-page btn btn-mini',
						'data-load-page' => 'featured&f_id='.$featured['featured_id'].'&action=edit',
						'data-toggle' => 'modal',
						'title' => BUTTON_EDIT,
					)
				); ?>
				<?php echo $cartet->html->link(
					'<i class="icon-trash"></i>',
					os_href_link(FILENAME_FEATURED, 'fID='.$featured['featured_id'].'&action=delete'),
					array(
						'class' => 'ajax-load-page btn btn-mini',
						'data-load-page' => 'featured&f_id='.$featured['featured_id'].'&action=delete',
						'data-toggle' => 'modal',
						'title' => BUTTON_DELETE,
					)
				); ?>
			</div>
		</td>
	</tr>
	<?php
}
?>
</table>
<div class="action-table">
	<div class="pull-right">
		<div class="pagination pagination-mini pagination-right">
			<?php echo $featured_split->display_count($featured_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FEATURED); ?>
			<?php echo $featured_split->display_links($featured_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>

<?php $main->bottom(); ?>