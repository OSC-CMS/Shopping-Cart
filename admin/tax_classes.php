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

$breadcrumb->add(HEADING_TITLE, FILENAME_TAX_CLASSES);

if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_TAX_CLASS, os_href_link(FILENAME_COUNTRIES, 'action=new'));
	$tax = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$countries_query_raw = os_db_query("select * from ".TABLE_TAX_CLASS." WHERE tax_class_id = '".(int)$_GET['tID']."'");
	$tax = os_db_fetch_array($countries_query_raw);

	$breadcrumb->add($tax['tax_class_title'], os_href_link(FILENAME_COUNTRIES, 'action=new'));
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

	<form id="tax" name="tax" action="<?php echo os_href_link(FILENAME_TAX_CLASSES); ?>" method="post">

		<?php if (isset($_GET['tID']) && !empty($_GET['tID'])) { ?>
			<input type="hidden" name="tID" value="<?php echo $_GET['tID']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<div class="control-group">
			<label class="control-label" for="tax_class_title"><?php echo TEXT_INFO_CLASS_TITLE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="tax_class_title" name="tax_class_title" data-required="true" value="<?php echo $tax['tax_class_title']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="tax_class_description"><?php echo TEXT_INFO_CLASS_DESCRIPTION; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="tax_class_description" name="tax_class_description" value="<?php echo $tax['tax_class_description']; ?>">
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="tax_save" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_TAX_CLASSES); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

<? } else { ?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6"></div>
		<div class="span6">
			<div class="btn-group pull-right">
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_TAX_CLASSES, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_NEW_TAX_CLASS; ?></a>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_TAX_CLASSES; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_CLASS_DESCRIPTION; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_DATE_ADDED; ?></th>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
	<?php
	$classes_query_raw = "select * from ".TABLE_TAX_CLASS." order by tax_class_title";
	$classes_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $classes_query_raw, $classes_query_numrows);
	$classes_query = os_db_query($classes_query_raw);

	while ($classes = os_db_fetch_array($classes_query))
	{
	?>
		<tr>
			<td><?php echo $classes['tax_class_title']; ?></td>
			<td><?php echo $classes['tax_class_description']; ?></td>
			<td class="tcenter">
				<?php echo $classes['date_added']; ?>
				<?php if ($classes['last_modified']) { ?>
					<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $classes['last_modified']; ?>"></i>
				<?php } ?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_TAX_CLASSES, 'page='.$_GET['page'].'&tID='.$classes['tax_class_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="tax_delete" data-remove-parent="tr" data-id="<?php echo $classes['tax_class_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
	<?php } ?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><?php echo $classes_split->display_count($classes_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES); ?></td>
		<td><?php echo $classes_split->display_links($classes_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
	</tr>
</table>

<? } ?>

<?php $main->bottom(); ?>