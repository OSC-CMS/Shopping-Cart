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

$breadcrumb->add(HEADING_TITLE, FILENAME_LANGUAGES);

if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_LANGUAGE, os_href_link(FILENAME_LANGUAGES, 'action=new'));
	$languages_query = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$languages_query_raw = os_db_query("select * from ".TABLE_LANGUAGES." WHERE languages_id = '".(int)$_GET['lID']."'");
	$languages_query = os_db_fetch_array($languages_query_raw);

	$breadcrumb->add($languages_query['name'], os_href_link(FILENAME_LANGUAGES, 'action=new'));
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

<form id="languages" name="languages" action="<?php echo os_href_link(FILENAME_LANGUAGES); ?>" method="post">

	<?php if (isset($_GET['lID']) && !empty($_GET['lID'])) { ?>
		<input type="hidden" name="lID" value="<?php echo $_GET['lID']; ?>">
	<?php } ?>
	<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

	<div class="control-group">
		<label class="control-label" for="name"><?php echo TEXT_INFO_LANGUAGE_NAME; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="name" name="name" data-required="true" value="<?php echo $languages_query['name']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="code"><?php echo TEXT_INFO_LANGUAGE_CODE; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="code" name="code" data-required="true" value="<?php echo $languages_query['code']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="language_charset"><?php echo TEXT_INFO_LANGUAGE_CHARSET; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="language_charset" name="language_charset" data-required="true" value="<?php echo $languages_query['language_charset']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="image"><?php echo TEXT_INFO_LANGUAGE_IMAGE; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="image" name="image" data-required="true" value="<?php echo $languages_query['image']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="directory"><?php echo TEXT_INFO_LANGUAGE_DIRECTORY; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="directory" name="directory" data-required="true" value="<?php echo $languages_query['directory']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="sort_order"><?php echo TEXT_INFO_LANGUAGE_SORT_ORDER; ?></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="sort_order" name="sort_order" value="<?php echo $languages_query['sort_order']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for=""></label>
		<div class="controls">
			<label class="checkbox"><input type="checkbox" name="default" value="on" <?php echo ($languages_query['code'] == DEFAULT_LANGUAGE) ? 'checked' : ''; ?>> <?php echo TEXT_SET_DEFAULT; ?></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="status"><?php echo TABLE_HEADING_STATUS; ?></label>
		<div class="controls">
			<select class="input-block-level" name="status" id="status">
				<option value="1" <?php echo ($languages_query['status'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
				<option value="0" <?php echo (isset($languages_query['status']) && $languages_query['status'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
			</select>
		</div>
	</div>

	<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success ajax-save-form" data-form-action="languages_save" data-reload-page="1" type="submit" value="<?php echo BUTTON_INSERT; ?>">
		<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_LANGUAGES, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
	</div>

</form>

<? } else { ?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6"></div>
		<div class="span6">
			<div class="btn-group pull-right">
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_LANGUAGES, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_NEW_LANGUAGE; ?></a>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_LANGUAGE_NAME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_LANGUAGE_CODE; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_LANGUAGE_CHARSET; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_LANGUAGE_DIRECTORY; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_LANGUAGE_SORT_ORDER; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
<?php
$languages_query_raw = "select * from ".TABLE_LANGUAGES." order by sort_order";
$languages_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $languages_query_raw, $languages_query_numrows);
$languages_query = os_db_query($languages_query_raw);
while ($languages = os_db_fetch_array($languages_query))
{
	if (DEFAULT_LANGUAGE == $languages['code'])
		$name = '<b>'.$languages['name'].' ('.TEXT_DEFAULT.')</b>';
	else
		$name = $languages['name'];
	?>
	<tr>
		<td><?php echo $name; ?></td>
		<td><?php echo $languages['code']; ?></td>
		<td><?php echo $languages['language_charset']; ?></td>
		<td><?php echo $languages['directory']; ?></td>
		<td><?php echo $languages['sort_order']; ?></td>
		<td class="tcenter">
			<?php
			echo '<a '.(($languages['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$languages['languages_id'].'_0_status" data-column="status" data-action="languages_status" data-id="'.$languages['languages_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
			echo '<a '.(($languages['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$languages['languages_id'].'_1_status" data-column="status" data-action="languages_status" data-id="'.$languages['languages_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
		</td>
		<td width="100">
			<div class="btn-group pull-right">
				<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_LANGUAGES, 'page='.$_GET['page'].'&lID='.$languages['languages_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
				<?php if ($languages['code'] != DEFAULT_LANGUAGE) { ?>
					<a class="btn btn-mini" href="#" data-action="languages_delete" data-remove-parent="tr" data-id="<?php echo $languages['languages_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				<?php } ?>
			</div>
		</td>
	</tr>
	<?php } ?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td><?php echo $languages_split->display_count($languages_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LANGUAGES); ?></td>
<td><?php echo $languages_split->display_links($languages_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
</tr>
</table>

<? } ?>

<?php $main->bottom(); ?>