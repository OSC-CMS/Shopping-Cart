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

$breadcrumb->add(HEADING_TITLE, FILENAME_COUNTRIES);

if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_COUNTRY, os_href_link(FILENAME_COUNTRIES, 'action=new'));
	$countries_query = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$countries_query_raw = os_db_query("select * from ".TABLE_COUNTRIES." WHERE countries_id = '".(int)$_GET['cID']."'");
	$countries_query = os_db_fetch_array($countries_query_raw);

	$breadcrumb->add($countries_query['countries_name'], os_href_link(FILENAME_COUNTRIES, 'action=new'));
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

	<form id="countries" name="countries" action="<?php echo os_href_link(FILENAME_COUNTRIES); ?>" method="post">

		<?php if (isset($_GET['cID']) && !empty($_GET['cID'])) { ?>
			<input type="hidden" name="cID" value="<?php echo $_GET['cID']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<div class="control-group">
			<label class="control-label" for="countries_name"><?php echo TEXT_INFO_COUNTRY_NAME; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="countries_name" name="countries_name" data-required="true" value="<?php echo $countries_query['countries_name']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="countries_iso_code_2"><?php echo TEXT_INFO_COUNTRY_CODE_2; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="countries_iso_code_2" name="countries_iso_code_2" data-required="true" value="<?php echo $countries_query['countries_iso_code_2']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="countries_iso_code_3"><?php echo TEXT_INFO_COUNTRY_CODE_3; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="countries_iso_code_3" name="countries_iso_code_3" data-required="true" value="<?php echo $countries_query['countries_iso_code_3']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="countries_iso_code_3"><?php echo TEXT_INFO_ADDRESS_FORMAT; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<?php echo os_draw_pull_down_menu('address_format_id', os_get_address_formats()); ?>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="customers_saveCountries" data-reload-page="1" type="submit" value="<?php echo BUTTON_INSERT; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_COUNTRIES, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

<? } else { ?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6"></div>
		<div class="span6">
			<div class="btn-group pull-right">
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_COUNTRIES, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_NEW_COUNTRY; ?></a>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_COUNTRY_NAME; ?></th>
			<th colspan="2"><span class="line"></span><?php echo TABLE_HEADING_COUNTRY_CODES; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
<?php
$countries_query_raw = "select * from ".TABLE_COUNTRIES." order by countries_name";
$countries_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $countries_query_raw, $countries_query_numrows);
$countries_query = os_db_query($countries_query_raw);

while ($countries = os_db_fetch_array($countries_query))
{
	?>
	<tr>
		<td><?php echo $countries['countries_name']; ?></td>
		<td><?php echo $countries['countries_iso_code_2']; ?></td>
		<td><?php echo $countries['countries_iso_code_3']; ?></td>
		<td class="tcenter">
			<?php
			echo '<a '.(($countries['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$countries['countries_id'].'_0_status" data-column="status" data-action="customers_statusCountry" data-id="'.$countries['countries_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
			echo '<a '.(($countries['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$countries['countries_id'].'_1_status" data-column="status" data-action="customers_statusCountry" data-id="'.$countries['countries_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
		</td>
		<td width="100">
			<div class="btn-group pull-right">
				<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_COUNTRIES, 'page='.$_GET['page'].'&cID='.$countries['countries_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
				<a class="btn btn-mini" href="#" data-action="customers_deleteCountry" data-remove-parent="tr" data-id="<?php echo $countries['countries_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
			</div>
		</td>
	</tr>
	<?php
}
?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td class="smallText" valign="top"><?php echo $countries_split->display_count($countries_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
<td class="smallText"><?php echo $countries_split->display_links($countries_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
</tr>
</table>

<? } ?>

<?php $main->bottom(); ?>