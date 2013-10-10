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

$breadcrumb->add(HEADING_TITLE, FILENAME_EXTRA_FIELDS);

$fieldsEdit = array();
// Редактирование
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['field_id']))
{
	$fieldsEdit = $cartet->customers->getFieldById($_GET['field_id']);
	$breadcrumb->add($fieldsEdit['fields_lang'][$_SESSION['languages_id']]['fields_name']);
}
// Добавление
if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(BUTTON_INSERT);
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new') OR $_GET['action'] == 'edit') { ?>

	<?php $languages = $cartet->language->get(); ?>

	<form id="fields" method="post" action="<?php echo os_href_link(FILENAME_EXTRA_FIELDS, 'action=insert'); ?>">

		<?php if ($_GET['action'] == 'edit') { ?>
			<input type="hidden" name="field_id" value="<?php echo $_GET['field_id']; ?>">
		<?php } elseif ($_GET['action'] == 'new') { ?>
			<input type="hidden" name="action" value="new">
		<?php } ?>

		<div class="control-group">
			<label class="control-label" for="">Название</label>
			<div class="controls">
				<ul class="nav nav-tabs default-tabs">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
						<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
					<?php } ?>
				</ul>
				<div class="tab-content">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
						<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
							<input type="text" name="lang[<?php echo $lang['languages_id']; ?>]" value="<?php echo $fieldsEdit['fields_lang'][$lang['languages_id']]['fields_name']; ?>" />
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_FIELD_INPUT_TYPE; ?></label>
			<div class="controls">
				<label class="radio"><input type="radio" name="fields_input_type" value="0" <?php echo ($fieldsEdit['fields_input_type'] == 0 OR empty($fieldsEdit['fields_input_type'])) ? 'checked' : ''; ?>> <?php echo TEXT_INPUT_FIELD; ?></label>
				<label class="radio"><input type="radio" name="fields_input_type" value="1" <?php echo ($fieldsEdit['fields_input_type'] == 1) ? 'checked' : ''; ?>> <?php echo TEXT_TEXTAREA_FIELD; ?></label>
				<label class="radio"><input type="radio" name="fields_input_type" value="2" <?php echo ($fieldsEdit['fields_input_type'] == 2) ? 'checked' : ''; ?>> <?php echo TEXT_RADIO_FIELD; ?></label>
				<label class="radio"><input type="radio" name="fields_input_type" value="3" <?php echo ($fieldsEdit['fields_input_type'] == 3) ? 'checked' : ''; ?>> <?php echo TEXT_CHECK_FIELD; ?></label>
				<label class="radio"><input type="radio" name="fields_input_type" value="4" <?php echo ($fieldsEdit['fields_input_type'] == 4) ? 'checked' : ''; ?>> <?php echo TEXT_DOWN_FIELD; ?></label>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fields_input_value"><?php echo TEXT_FIELD_INPUT_VALUE; ?></label>
			<div class="controls">
				<textarea id="fields_input_value" name="fields_input_value" class="span12 textarea_small"><?php echo $fieldsEdit['fields_input_value']; ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fields_required_status"><?php echo TEXT_FIELD_REQUIRED_STATUS; ?></label>
			<div class="controls">
				<select name="fields_required_status" id="fields_required_status" class="span12">
					<option value="0" <?php echo ($fieldsEdit['fields_required_status'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
					<option value="1" <?php echo ($fieldsEdit['fields_required_status'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fields_size"><?php echo TEXT_FIELD_SIZE; ?></label>
			<div class="controls">
				<input type="text" id="fields_size" name="fields_size" value="<?php echo (empty($fieldsEdit['fields_size']) OR $fieldsEdit['fields_size'] == 0) ? '' : $fieldsEdit['fields_size']; ?>" class="span12" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fields_required_email"><?php echo TEXT_FIELD_STATUS_EMAIL; ?></label>
			<div class="controls">
				<select name="fields_required_email" id="fields_required_email" class="span12">
					<option value="0" <?php echo ($fieldsEdit['fields_required_email'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
					<option value="1" <?php echo ($fieldsEdit['fields_required_email'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fields_status"><?php echo TABLE_HEADING_STATUS; ?></label>
			<div class="controls">
				<select name="fields_status" id="fields_status" class="span12">
					<option value="1" <?php echo ($fieldsEdit['fields_status'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
					<option value="0" <?php echo ($fieldsEdit['fields_status'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
				</select>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="customers_saveField" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_EXTRA_FIELDS); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<?php } else { ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span8">

			</div>
			<div class="span4">
				<div class="pull-right">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_EXTRA_FIELDS, os_get_all_get_params(array ('cID', 'action')).'action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_FIELDS; ?></th>
				<th><span class="line"></span><?php echo TEXT_FIELD_INPUT_TYPE; ?></th>
				<th><span class="line"></span><?php echo TEXT_FIELD_REQUIRED_STATUS; ?></th>
				<th><span class="line"></span><?php echo TEXT_FIELD_SIZE; ?></th>
				<th><span class="line"></span><?php echo TEXT_FIELD_REQUIRED_EMAIL; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$fields_query_raw = "select ce.fields_id, ce.fields_size, ce.fields_input_type, ce.fields_input_value, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_required_email from ".TABLE_EXTRA_FIELDS." ce, ".TABLE_EXTRA_FIELDS_INFO." cei where cei.fields_id=ce.fields_id and cei.languages_id =".(int)$_SESSION['languages_id'];
	$fields_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $fields_query_raw, $manufacturers_query_numrows);
	$fields_query = os_db_query($fields_query_raw);
	while ($fields = os_db_fetch_array($fields_query))
	{
		if ((!isset($_GET['fID']) || (isset($_GET['fID']) && ($_GET['fID'] == $fields['fields_id']))) && !isset($fInfo) && (substr($action, 0, 3) != 'new')) {
			$fInfo = new objectInfo($fields);
		}
	?>
		<tr>
			<td><?php echo $fields['fields_name']; ?></td>
			<td><?php
				switch($fields['fields_input_type'])
				{
					case  0: echo TEXT_INPUT_FIELD; break;
					case  1: echo TEXT_TEXTAREA_FIELD; break;
					case  2: echo TEXT_RADIO_FIELD; break;
					case  3: echo TEXT_CHECK_FIELD; break;
					case  4: echo TEXT_DOWN_FIELD; break;
					default: echo TEXT_INPUT_FIELD;
				}
				?>
			</td>
			<td class="tcenter">
				<?php
					echo '<a '.(($fields['fields_required_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$fields['fields_id'].'_0_fields_required_status" data-column="fields_required_status" data-action="customers_fieldStatus" data-id="'.$fields['fields_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($fields['fields_required_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$fields['fields_id'].'_1_fields_required_status" data-column="fields_required_status" data-action="customers_fieldStatus" data-id="'.$fields['fields_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td class="tcenter"><?php echo $fields['fields_size']; ?></td>
			<td class="tcenter">
				<?php
					echo '<a '.(($fields['fields_required_email'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$fields['fields_id'].'_0_fields_required_email" data-column="fields_required_email" data-action="customers_fieldStatus" data-id="'.$fields['fields_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($fields['fields_required_email'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$fields['fields_id'].'_1_fields_required_email" data-column="fields_required_email" data-action="customers_fieldStatus" data-id="'.$fields['fields_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td class="tcenter">
				<?php
					echo '<a '.(($fields['fields_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$fields['fields_id'].'_0_fields_status" data-column="fields_status" data-action="customers_fieldStatus" data-id="'.$fields['fields_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($fields['fields_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$fields['fields_id'].'_1_fields_status" data-column="fields_status" data-action="customers_fieldStatus" data-id="'.$fields['fields_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_EXTRA_FIELDS, 'page='.$_GET['page'].'&field_id='.$fields['fields_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="customers_deleteField" data-remove-parent="tr" data-id="<?php echo $fields['fields_id']; ?>" data-confirm="<?php echo TEXT_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
		<?php
	}
	?>
	</table>

	<table>
		<tr>
			<td><?php echo $fields_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FIELDS); ?></td>
			<td><?php echo $fields_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
		</tr>
	</table>

<?php } ?>

<?php $main->bottom(); ?>