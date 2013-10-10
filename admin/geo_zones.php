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

switch ($_GET['action']) {
	case 'insert_zone':
	$geo_zone_name = os_db_prepare_input($_POST['geo_zone_name']);
	$geo_zone_description = os_db_prepare_input($_POST['geo_zone_description']);

	os_db_query("insert into ".TABLE_GEO_ZONES." (geo_zone_name, geo_zone_description, date_added) values ('".os_db_input($geo_zone_name)."', '".os_db_input($geo_zone_description)."', now())");
	$new_zone_id = os_db_insert_id();

	os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$new_zone_id));
	break;

	case 'save_zone':
	$zID = os_db_prepare_input($_GET['zID']);
	$geo_zone_name = os_db_prepare_input($_POST['geo_zone_name']);
	$geo_zone_description = os_db_prepare_input($_POST['geo_zone_description']);

	os_db_query("update ".TABLE_GEO_ZONES." set geo_zone_name = '".os_db_input($geo_zone_name)."', geo_zone_description = '".os_db_input($geo_zone_description)."', last_modified = now() where geo_zone_id = '".os_db_input($zID)."'");

	os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID']));
	break;
}

add_action('head_admin', 'head_zones');

function head_zones ()
{
	if ($_GET['zID']  && (($_GET['saction'] == 'edit') || ($_GET['saction'] == 'new')))
	{
		?>
		<script type="text/javascript"><!--
		function resetZoneSelected(theForm)
		{
			if (theForm.state.value != '')
			{
				theForm.zone_id.selectedIndex = '0';
				if (theForm.zone_id.options.length > 0)
				{
					theForm.state.value = '<?php echo JS_STATE_SELECT; ?>';
				}
			}
		}

		function update_zone(theForm)
		{
			var NumState = theForm.zone_id.options.length;
			var SelectedCountry = "";

			while(NumState > 0)
			{
				NumState--;
				theForm.zone_id.options[NumState] = null;
			}
			SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;
			<?php echo os_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>
		}
		//--></script>
		<?php
	}
}

$breadcrumb->add(HEADING_TITLE, FILENAME_GEO_ZONES);

if (isset($_GET['action']) && $_GET['action'] == 'list')
{
	$zones_query_raw = os_db_query("select * from ".TABLE_GEO_ZONES." WHERE geo_zone_id = '".(int)$_GET['zID']."'");
	$zones = os_db_fetch_array($zones_query_raw);

	$breadcrumb->add($zones['geo_zone_name'], os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID'].'&action=list'));
}
if (isset($_GET['saction']) && $_GET['saction'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_COUNTRY, os_href_link(FILENAME_GEO_ZONES, 'action=new'));
	$zones2geo = array();
}
if (isset($_GET['saction']) && $_GET['saction'] == 'edit')
{
	$zones2geoQuery = os_db_query("select * from ".TABLE_ZONES_TO_GEO_ZONES." WHERE association_id = '".(int)$_GET['sID']."'");
	$zones2geo = os_db_fetch_array($zones2geoQuery);

	$breadcrumb->add(TEXT_INFO_HEADING_EDIT_ZONE, os_href_link(FILENAME_GEO_ZONES, 'action=new'));
}

if (isset($_GET['action']) && $_GET['action'] == 'new_zone')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_ZONE, os_href_link(FILENAME_GEO_ZONES, 'action=new_zone'));
	$geoZone = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'edit_zone')
{
	$geoZoneQuery = os_db_query("select * from ".TABLE_GEO_ZONES." WHERE geo_zone_id = '".(int)$_GET['zID']."'");
	$geoZone = os_db_fetch_array($geoZoneQuery);

	$breadcrumb->add($geoZone['geo_zone_name'], os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID'].'&action=edit_zone'));
}

$main->head();
$main->top_menu();
?>

<?php if ($_GET['action'] == 'list') { ?>

	<?php if ($_GET['saction'] == 'edit' OR $_GET['saction'] == 'new') { ?>

		<form id="zones" name="zones" action="<?php echo os_href_link(FILENAME_GEO_ZONES); ?>" method="post">

			<?php if (isset($_GET['sID']) && !empty($_GET['sID'])) { ?>
				<input type="hidden" name="sID" value="<?php echo $_GET['sID']; ?>">
			<?php } ?>
			<input type="hidden" name="action" value="<?php echo $_GET['saction']; ?>">
			<input type="hidden" name="zID" value="<?php echo $_GET['zID']; ?>">

			<div class="control-group">
				<label class="control-label" for="zone_country_id"><?php echo TEXT_INFO_COUNTRY; ?> <span class="input-required">*</span></label>
				<div class="controls">
					<?php echo os_draw_pull_down_menu('zone_country_id', os_get_countries(TEXT_ALL_COUNTRIES), $zones2geo['zone_country_id'], 'onChange="update_zone(this.form);"'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="zone_id"><?php echo TEXT_INFO_COUNTRY_ZONE; ?> <span class="input-required">*</span></label>
				<div class="controls">
					<?php echo os_draw_pull_down_menu('zone_id', os_prepare_country_zones_pull_down($zones2geo['zone_country_id']), $zones2geo['zone_id']); ?>
				</div>
			</div>

			<hr>

			<div class="tcenter footer-btn">
				<input class="btn btn-success ajax-save-form" data-form-action="customers_saveSubGeoZone" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>">
				<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID'].'&action=list&spage='.$_GET['spage'].'&sID='.$sInfo->association_id); ?>"><?php echo BUTTON_CANCEL; ?></a>
			</div>
		</form>

	<?php } else { ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span6"></div>
			<div class="span6">
				<div class="btn-group pull-right">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID'].'&action=list&spage='.$_GET['spage'].'&saction=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID']); ?>"><?php echo BUTTON_BACK; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_COUNTRY; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_COUNTRY_ZONE; ?></th>
				<th><span class="line"></span><?php echo TEXT_INFO_DATE_ADDED; ?></th>
				<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$zones_query_raw = "select * from ".TABLE_ZONES_TO_GEO_ZONES." a left join ".TABLE_COUNTRIES." c on a.zone_country_id = c.countries_id left join ".TABLE_ZONES." z on a.zone_id = z.zone_id where a.geo_zone_id = ".$_GET['zID']." order by association_id";
	$zones_split = new splitPageResults($_GET['spage'], MAX_DISPLAY_ADMIN_PAGE, $zones_query_raw, $zones_query_numrows);
	$zones_query = os_db_query($zones_query_raw);
	while ($zones = os_db_fetch_array($zones_query))
	{
	?>
		<tr>
			<td><?php echo (($zones['countries_name']) ? $zones['countries_name'] : TEXT_ALL_COUNTRIES); ?></td>
			<td><?php echo (($zones['zone_id']) ? $zones['zone_name'] : PLEASE_SELECT); ?></td>
			<td class="tcenter">
				<?php echo $zones['date_added']; ?>
				<?php if ($zones['last_modified']) { ?>
					<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $zones['last_modified']; ?>"></i>
				<?php } ?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID'].'&action=list&spage='.$_GET['spage'].'&sID='.$zones['association_id'].'&saction=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="customers_deleteSubGeoZone" data-remove-parent="tr" data-id="<?php echo $zones['association_id']; ?>" data-confirm="<?php echo TEXT_INFO_HEADING_DELETE_SUB_ZONE; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
	<?php } ?>
	</table>

	<table border="0" width="100%" cellspacing="2" cellpadding="2">
		<tr>
			<td><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['spage'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
			<td><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['spage'], 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID'].'&action=list', 'spage'); ?></td>
		</tr>
	</table>
	<?php } ?>

<?php } else { ?>

	<?php if ($_GET['action'] == 'edit_zone' OR $_GET['action'] == 'new_zone') { ?>

		<form id="zones" name="zones" action="<?php echo os_href_link(FILENAME_GEO_ZONES); ?>" method="post">

			<?php if (isset($_GET['zID']) && !empty($_GET['zID'])) { ?>
				<input type="hidden" name="zID" value="<?php echo $_GET['zID']; ?>">
			<?php } ?>
			<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

			<div class="control-group">
				<label class="control-label" for="geo_zone_name"><?php echo TEXT_INFO_ZONE_NAME; ?> <span class="input-required">*</span></label>
				<div class="controls">
					<input class="input-block-level" type="text" id="geo_zone_name" name="geo_zone_name" data-required="true" value="<?php echo $geoZone['geo_zone_name']; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="geo_zone_description"><?php echo TEXT_INFO_ZONE_DESCRIPTION; ?> <span class="input-required">*</span></label>
				<div class="controls">
					<input class="input-block-level" type="text" id="geo_zone_description" name="geo_zone_description" data-required="true" value="<?php echo $geoZone['geo_zone_description']; ?>">
				</div>
			</div>

			<hr>

			<div class="tcenter footer-btn">
				<input class="btn btn-success ajax-save-form" data-form-action="customers_saveGeoZone" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>">
				<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$_GET['zID'].'&action=list&spage='.$_GET['spage'].'&sID='.$sInfo->association_id); ?>"><?php echo BUTTON_CANCEL; ?></a>
			</div>
		</form>

	<?php } else { ?>

		<div class="second-page-nav">
			<div class="row-fluid">
				<div class="span6"></div>
				<div class="span6">
					<div class="btn-group pull-right">
						<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'action=new_zone'); ?>"><?php echo BUTTON_INSERT; ?></a>
					</div>
				</div>
			</div>
		</div>

		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th><?php echo TABLE_HEADING_TAX_ZONES; ?></th>
					<th><span class="line"></span><?php echo TEXT_INFO_ZONE_DESCRIPTION; ?></th>
					<th><span class="line"></span><?php echo TEXT_INFO_NUMBER_ZONES; ?></th>
					<th><span class="line"></span><?php echo TEXT_INFO_DATE_ADDED; ?></th>
					<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
				</tr>
			</thead>
		<?php
		$zones_query_raw = "select * from ".TABLE_GEO_ZONES." order by geo_zone_name";
		$zones_split = new splitPageResults($_GET['zpage'], MAX_DISPLAY_ADMIN_PAGE, $zones_query_raw, $zones_query_numrows);
		$zones_query = os_db_query($zones_query_raw);

		$aZones = array();
		$aZonesId = array();
		while ($zones = os_db_fetch_array($zones_query))
		{
			$aZonesId[$zones['geo_zone_id']] = $zones['geo_zone_id'];
			$aZones[] = $zones;
		}

		if (!empty($aZonesId))
		{
		$num_zones_query = os_db_query("select geo_zone_id, count(*) as num_zones from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id IN (".implode(',', $aZonesId).") GROUP BY geo_zone_id");
		$aZonesCount = array();
		if (os_db_num_rows($num_zones_query) > 0)
		{
			while ($num_zones = os_db_fetch_array($num_zones_query))
			{
				$aZonesCount[$num_zones['geo_zone_id']] = $num_zones['num_zones'];
			}
		}

		foreach ($aZones AS $zones)
		{
			?>
			<tr>
				<td><a href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'zpage='.$_GET['zpage'].'&zID='.$zones['geo_zone_id'].'&action=list'); ?>"><i class="icon-folder-close"></i> <?php echo $zones['geo_zone_name']; ?></a></td>
				<td><?php echo $zones['geo_zone_description']; ?></td>
				<td><?php echo ($aZonesCount[$zones['geo_zone_id']]) ? $aZonesCount[$zones['geo_zone_id']] : 0; ?></td>
				<td class="tcenter">
					<?php echo $zones['date_added']; ?>
					<?php if ($zones['last_modified']) { ?>
						<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $zones['last_modified']; ?>"></i>
					<?php } ?>
				</td>
				<td width="100">
					<div class="btn-group pull-right">
						<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_GEO_ZONES, 'page='.$_GET['page'].'&zID='.$zones['geo_zone_id'].'&action=edit_zone'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
						<a class="btn btn-mini" href="#" data-action="customers_deleteGeoZone" data-remove-parent="tr" data-id="<?php echo $zones['geo_zone_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_ZONE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
					</div>
				</td>
			</tr>
		<?php } } ?>
		</table>

		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['zpage'], TEXT_DISPLAY_NUMBER_OF_TAX_ZONES); ?></td>
				<td><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['zpage'], '', 'zpage'); ?></td>
			</tr>
		</table>

	<?php } ?>

<?php } ?>

<?php $main->bottom(); ?>