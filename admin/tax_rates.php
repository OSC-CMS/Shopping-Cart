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

$breadcrumb->add(HEADING_TITLE, FILENAME_TAX_RATES);

if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_TAX_RATE, os_href_link(FILENAME_TAX_RATES, 'action=new'));
	$trInfo = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$tax_query_raw = os_db_query("select * from ".TABLE_TAX_CLASS." tc, ".TABLE_TAX_RATES." r left join ".TABLE_GEO_ZONES." z on r.tax_zone_id = z.geo_zone_id where r.tax_class_id = tc.tax_class_id AND r.tax_rates_id = '".(int)$_GET['tID']."'");
	$trInfo = os_db_fetch_array($tax_query_raw);

	$breadcrumb->add($trInfo['tax_class_title'], os_href_link(FILENAME_TAX_RATES, 'page='.$_GET['page'].'&tID='.$_GET['tID'].'&action=edit'));
}

$main->head();
$main->top_menu();
?>


<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

	<form id="rates" name="rates" action="<?php echo os_href_link(FILENAME_TAX_RATES); ?>" method="post">

		<?php if (isset($_GET['tID']) && !empty($_GET['tID'])) { ?>
			<input type="hidden" name="tID" value="<?php echo $_GET['tID']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<div class="control-group">
			<label class="control-label" for="tax_class_id"><?php echo TEXT_INFO_CLASS_TITLE; ?></label>
			<div class="controls">
				<?php echo os_tax_classes_pull_down('name="tax_class_id" id="tax_class_id" class="input-block-level"', $trInfo['tax_class_id']); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="tax_zone_id"><?php echo TEXT_INFO_ZONE_NAME; ?></label>
			<div class="controls">
				<?php echo os_geo_zones_pull_down('name="tax_zone_id" id="tax_zone_id" class="input-block-level"', $trInfo['geo_zone_id']); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="tax_rate"><?php echo TEXT_INFO_TAX_RATE; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="tax_rate" name="tax_rate" value="<?php echo $trInfo['tax_rate']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="tax_description"><?php echo TEXT_INFO_RATE_DESCRIPTION; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="tax_description" name="tax_description" value="<?php echo $trInfo['tax_description']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="tax_priority"><?php echo TEXT_INFO_TAX_RATE_PRIORITY; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="tax_priority" name="tax_priority" value="<?php echo $trInfo['tax_priority']; ?>">
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="tax_saveRates" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_TAX_RATES, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

<? } else { ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span6"></div>
			<div class="span6">
				<div class="btn-group pull-right">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_TAX_RATES, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_NEW_TAX_RATE; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_TAX_RATE_PRIORITY; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_TAX_CLASS_TITLE; ?></th>
				<th><span class="line"></span><?php echo TEXT_INFO_RATE_DESCRIPTION; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_ZONE; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_TAX_RATE; ?></th>
				<th><span class="line"></span><?php echo TEXT_INFO_DATE_ADDED; ?></th>
				<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
		<?php
		$rates_query_raw = "select * from ".TABLE_TAX_CLASS." tc, ".TABLE_TAX_RATES." r left join ".TABLE_GEO_ZONES." z on r.tax_zone_id = z.geo_zone_id where r.tax_class_id = tc.tax_class_id";
		$rates_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $rates_query_raw, $rates_query_numrows);
		$rates_query = os_db_query($rates_query_raw);

		while ($rates = os_db_fetch_array($rates_query))
		{
		?>
		<tr>
			<td><?php echo $rates['tax_priority']; ?></td>
			<td><?php echo $rates['tax_class_title']; ?></td>
			<td><?php echo $rates['tax_class_description']; ?></td>
			<td><?php echo $rates['geo_zone_name']; ?></td>
			<td><?php echo os_display_tax_value($rates['tax_rate']); ?>%</td>
			<td class="tcenter">
				<?php echo $rates['date_added']; ?>
				<?php if ($rates['last_modified']) { ?>
					<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $rates['last_modified']; ?>"></i>
				<?php } ?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_TAX_RATES, 'page='.$_GET['page'].'&tID='.$rates['tax_rates_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="tax_deleteRates" data-remove-parent="tr" data-id="<?php echo $rates['tax_rates_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
	<?php } ?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><?php echo $rates_split->display_count($rates_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?></td>
		<td><?php echo $rates_split->display_links($rates_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
	</tr>
</table>

<? } ?>

<?php $main->bottom(); ?>