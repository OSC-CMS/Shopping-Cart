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

$breadcrumb->add(HEADING_TITLE, FILENAME_ZONES);

if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_ZONE, os_href_link(FILENAME_ZONES, 'action=new'));
	$zones_query = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$zones_query_raw = os_db_query("select * from ".TABLE_ZONES." z, ".TABLE_COUNTRIES." c where z.zone_country_id = c.countries_id AND z.zone_id = '".(int)$_GET['cID']."'");
	$zones_query = os_db_fetch_array($zones_query_raw);

	$breadcrumb->add($zones_query['zone_name'], os_href_link(FILENAME_ZONES, 'action=new'));
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

	<form id="zones" name="zones" action="<?php echo os_href_link(FILENAME_ZONES); ?>" method="post">

		<?php if (isset($_GET['cID']) && !empty($_GET['cID'])) { ?>
			<input type="hidden" name="cID" value="<?php echo $_GET['cID']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<div class="control-group">
			<label class="control-label" for="zone_name"><?php echo TEXT_INFO_ZONES_NAME; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="zone_name" name="zone_name" data-required="true" value="<?php echo $zones_query['zone_name']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="zone_code"><?php echo TEXT_INFO_ZONES_CODE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="zone_code" name="zone_code" data-required="true" value="<?php echo $zones_query['zone_code']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="zone_country_id"><?php echo TEXT_INFO_COUNTRY_NAME; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<?php echo os_draw_pull_down_menu('zone_country_id', os_get_countries(), $zones_query['countries_id']); ?>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="customers_saveZones" data-reload-page="1" type="submit" value="<?php echo BUTTON_INSERT; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_ZONES, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

<? } else { ?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6"></div>
		<div class="span6">
			<div class="btn-group pull-right">
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_ZONES, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_NEW_ZONE; ?></a>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_COUNTRY_NAME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ZONE_NAME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ZONE_CODE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
	<?php
	$zones_query_raw = "select * from ".TABLE_ZONES." z, ".TABLE_COUNTRIES." c where z.zone_country_id = c.countries_id order by c.countries_name, z.zone_name";
	$zones_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $zones_query_raw, $zones_query_numrows);
	$zones_query = os_db_query($zones_query_raw);

	while ($zones = os_db_fetch_array($zones_query))
	{
		?>
		<tr>
			<td><?php echo $zones['countries_name']; ?></td>
			<td><?php echo $zones['zone_name']; ?></td>
			<td><?php echo $zones['zone_code']; ?></td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_ZONES, 'page='.$_GET['page'].'&cID='.$zones['zone_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="customers_deleteZone" data-remove-parent="tr" data-id="<?php echo $zones['zone_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
	<?php } ?>
</table>
	
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ZONES); ?></td>
<td><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
</tr>
</table>

<?php } ?>

</table>
<?php $main->bottom(); ?>