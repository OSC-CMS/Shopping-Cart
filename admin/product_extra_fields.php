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

$page = ((isset($_GET['page'])) ? '?page='.$_GET['page'] : '');

$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (isset($_POST['remove'])) $action='remove';

if (os_not_null($action)) {
	switch ($action) {
		case 'setflag':
			$sql_data_array = array('products_extra_fields_status' => os_db_prepare_input($_GET['flag']));
			os_db_perform(TABLE_PRODUCTS_EXTRA_FIELDS, $sql_data_array, 'update', 'products_extra_fields_id='.$_GET['id']);
			os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS.$page));
		break;

		case 'add':
			$sql_data_array = array(
				'products_extra_fields_name' => os_db_prepare_input($_POST['field']['name']),
				'languages_id' => os_db_prepare_input ($_POST['field']['language']),
				'products_extra_fields_order' => os_db_prepare_input($_POST['field']['order']),
				'products_extra_fields_group' => os_db_prepare_input($_POST['field']['group'])
			);
			os_db_perform(TABLE_PRODUCTS_EXTRA_FIELDS, $sql_data_array, 'insert');

			os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS.$page));
		break;

		case 'update':
			if (isset($_POST['field']) && !empty($_POST['field']))
			{
				foreach ($_POST['field'] as $key => $val)
				{
					$sql_data_array = array(
						'products_extra_fields_name' => os_db_prepare_input($val['name']),
						'languages_id' =>  os_db_prepare_input($val['language']),
						'products_extra_fields_order' => os_db_prepare_input($val['order']),
						'products_extra_fields_group' =>  os_db_prepare_input($val['group'])
					);
					os_db_perform(TABLE_PRODUCTS_EXTRA_FIELDS, $sql_data_array, 'update', 'products_extra_fields_id = '.$key);
				}
			}
			os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS.$page));
		break;

		case 'remove':
			if ($_POST['mark']) {
			foreach ($_POST['mark'] as $key=>$val) {
			os_db_query("DELETE FROM ".TABLE_PRODUCTS_EXTRA_FIELDS." WHERE products_extra_fields_id=".os_db_input($key));
			os_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." WHERE products_extra_fields_id=".os_db_input($key));
			}
			os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS.$page));
			}
		break;
	}
}

$languages=os_get_languages();
$values[0]=array ('id' =>'0', 'text' => TEXT_ALL_LANGUAGES);
for ($i=0, $n=sizeof($languages); $i<$n; $i++)
{
	$values[$i+1] = array ('id' =>$languages[$i]['id'], 'text' =>$languages[$i]['name']);
}

// Получаем доступные языки
$langs = $cartet->language->get();

if (isset($_POST['save_group']))
{
	$groupData = array(
		'extra_fields_groups_order' => (int)$_POST['extra_fields_groups_order'],
		'extra_fields_groups_status' => (int)$_POST['extra_fields_groups_status']
	);

	if ($_GET['action'] == 'new')
	{
		os_db_perform(DB_PREFIX."products_extra_fields_groups", $groupData);
		$group_id = os_db_insert_id();
	}
	else
	{
		$group_id = $_GET['groups_id'];
		os_db_perform(DB_PREFIX."products_extra_fields_groups", $groupData, 'update', "extra_fields_groups_id = '".(int)$group_id."'");
	}

	foreach($langs AS $lang)
	{
		$groupDataDesc = array(
			'extra_fields_groups_id' => $group_id,
			'extra_fields_groups_name' => $_POST['extra_fields_groups_name'][$lang['languages_id']],
			'extra_fields_groups_languages_id' => $lang['languages_id']
		);

		if ($_GET['action'] == 'new')
			os_db_perform(DB_PREFIX."products_extra_fields_groups_desc", $groupDataDesc);
		else
			os_db_perform(DB_PREFIX."products_extra_fields_groups_desc", $groupDataDesc, 'update', "extra_fields_groups_id = '".(int)$group_id."' and extra_fields_groups_languages_id = '".(int)$lang['languages_id']."'");
	}

	os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'act=groups'));
}


$breadcrumb->add(HEADING_TITLE, FILENAME_PRODUCTS_EXTRA_FIELDS);

if (isset($_GET['act']) && $_GET['act'] == 'groups')
{
	$breadcrumb->add(EF_GROUP, FILENAME_PRODUCTS_EXTRA_FIELDS.'?act=groups');
}

$groupEdit = array();
$groupDescEdit = array();
if ($_GET['action'] == 'edit' && isset($_GET['groups_id']) && !empty($_GET['groups_id']))
{
	$groupsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."products_extra_fields_groups WHERE extra_fields_groups_id = '".(int)$_GET['groups_id']."'");
	$groupEdit = os_db_fetch_array($groupsQuery);

	$groupsDescQuery = os_db_query("SELECT * FROM ".DB_PREFIX."products_extra_fields_groups_desc WHERE extra_fields_groups_id = '".(int)$_GET['groups_id']."'");
	while ($groups = os_db_fetch_array($groupsDescQuery))
	{
		$groupDescEdit[$groups['extra_fields_groups_languages_id']] = $groups;
	}

	$breadcrumb->add($groupDescEdit[$_SESSION['languages_id']]['extra_fields_groups_name']);
}

if ($_GET['action'] == 'new')
{
	$breadcrumb->add(EF_GROUP_NEW);
}


$main->head();
$main->top_menu();
?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span8">
			<div class="btn-group">
				<a class="btn btn-mini <?php echo (!isset($_GET['act'])) ? 'btn-info' : ''; ?>" href="<?php echo os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS); ?>"><?php echo EF_TITLE; ?></a>
				<a class="btn btn-mini <?php echo (isset($_GET['act']) && $_GET['act'] == 'groups') ? 'btn-info' : ''; ?>" href="<?php echo os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'act=groups'); ?>"><?php echo EF_GROUP; ?></a>
			</div>
		</div>
		<div class="span4">
			<div class="btn-group pull-right">
				<?php if (isset($_GET['act']) && $_GET['act'] == 'groups') { ?>
					<a href="<?php echo os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'act=groups&action=new'); ?>" class="btn btn-mini btn-info"><?php echo EF_GROUP_NEW; ?></a>
				<? } else { ?>
					<button type="button" class="btn btn-mini btn-info" data-toggle="collapse" data-target="#add_field"><?php echo SUBHEADING_TITLE; ?></button>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php if (isset($_GET['act']) && $_GET['act'] == 'groups') { ?>

	<?php if ($_GET['action'] == 'new' OR $_GET['action'] == 'edit') { ?>
		<form method="post" action="">
			<?php
			if ($_GET['action'] == 'edit')
			{
				echo '<input type="hidden" name="extra_fields_groups_id" value="'.$groupEdit['extra_fields_groups_id'].'">';
			}
			?>
			<div class="control-group">
				<label class="control-label" for=""><?php echo EF_GROUP_NAME; ?></label>
				<div class="controls">
					<ul class="nav nav-tabs default-tabs">
						<?php $i = 0; foreach ($langs as $lang) { $i++; ?>
							<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content">
						<?php $i = 0; foreach ($langs as $lang) { $i++; ?>
							<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
								<input class="input-block-level" type="text" name="extra_fields_groups_name[<?php echo $lang['languages_id']; ?>]" value="<?php echo $groupDescEdit[$lang['languages_id']]['extra_fields_groups_name']; ?>" />
							</div>
						<?php } ?>
					</div>
					<div class="control-group">
						<label class="control-label" for="extra_fields_groups_order"><?php echo EF_GROUP_ORDER; ?></label>
						<div class="controls">
							<input class="input-block-level" type="text" id="extra_fields_groups_order" name="extra_fields_groups_order" value="<?php echo $groupEdit['extra_fields_groups_order']; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="extra_fields_groups_status"><?php echo EF_GROUP_STATUS; ?></label>
						<div class="controls">
							<select class="input-block-level" name="extra_fields_groups_status" id="extra_fields_groups_status">
								<option value="1" <?php echo ($groupEdit['extra_fields_groups_status'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
								<option value="0" <?php echo (isset($groupEdit['extra_fields_groups_status']) && $groupEdit['extra_fields_groups_status'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<hr>
			<div class="tcenter footer-btn">
				<input name="save_group" class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
				<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'act='.$_GET['act']); ?>"><?php echo BUTTON_CANCEL; ?></a>
			</div>
		</form>

	<? } else { ?>

		<table class="table table-condensed table-big-list border-radius-top">
			<thead>
			<tr>
				<th><?php echo EF_GROUP_NAME; ?></th>
				<th><span class="line"></span><?php echo EF_GROUP_ORDER; ?></th>
				<?php //<th class="tcenter"><span class="line"></span>TABLE_HEADING_STATUS</th> ?>
				<th><span class="line"></span><?php echo TEXT_ACTION; ?></th>
			</tr>
			</thead>
			<?php
			$products_extra_fields_query = os_db_query("SELECT * FROM ".DB_PREFIX."products_extra_fields_groups g LEFT JOIN ".DB_PREFIX."products_extra_fields_groups_desc d ON (g.extra_fields_groups_id = d.extra_fields_groups_id AND d.extra_fields_groups_languages_id = '".(int)$_SESSION['languages_id']."') ORDER BY g.extra_fields_groups_order");
			while ($extra_fields = os_db_fetch_array($products_extra_fields_query)) {
				?>
				<tr>
					<td><?php echo $extra_fields['extra_fields_groups_name']; ?></td>
					<td class="tcenter"><?php echo $extra_fields['extra_fields_groups_order']; ?></td>
					<?php
					/*<td class="tcenter">
						<?php
						echo '<a '.(($extra_fields['extra_fields_groups_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$extra_fields['extra_fields_groups_id'].'_0_extra_fields_groups_status" data-column="extra_fields_groups_status" data-action="products_statusExtraFields" data-id="'.$extra_fields['extra_fields_groups_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
						echo '<a '.(($extra_fields['extra_fields_groups_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$extra_fields['extra_fields_groups_id'].'_1_extra_fields_groups_status" data-column="extra_fields_groups_status" data-action="products_statusExtraFields" data-id="'.$extra_fields['extra_fields_groups_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
						?>
					</td>*/
					?>
					<td width="100">
						<div class="btn-group pull-right">
							<?php
							echo '<a class="btn btn-mini" href="'.os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'act=groups&action=edit&groups_id='.$extra_fields['extra_fields_groups_id']).'" title="'.BUTTON_EDIT.'"><i class="icon-edit"></i></a>';
							echo '<a class="btn btn-mini" href="'.os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'act=groups&action=delete&groups_id='.$extra_fields['extra_fields_groups_id']).'" title="'.BUTTON_DELETE.'"><i class="icon-trash"></i></a>';
							?>
						</div>
					</td>
				</tr>
			<?php } ?>
		</table>

	<?php } ?>

<?php } else { ?>

<?php

$getGroupsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."products_extra_fields_groups g LEFT JOIN ".DB_PREFIX."products_extra_fields_groups_desc d ON (g.extra_fields_groups_id = d.extra_fields_groups_id AND d.extra_fields_groups_languages_id = '".(int)$_SESSION['languages_id']."') ORDER BY g.extra_fields_groups_order");
$groupsList = array();
if (os_db_num_rows($getGroupsQuery) > 0)
{
	while ($getGroup = os_db_fetch_array($getGroupsQuery))
	{
		$groupsList[] = array(
			'id' => $getGroup['extra_fields_groups_id'],
			'text' => $getGroup['extra_fields_groups_name']
		);
		//echo $getGroup['extra_fields_groups_id'].' | '.$getGroup['extra_fields_groups_name'].'<br />';
	}
}
?>

	<div id="add_field" class="collapse">
		<div class="form-horizontal pt10">
			<h4><?php echo SUBHEADING_TITLE; ?></h4>
			<?php echo os_draw_form("add_field", FILENAME_PRODUCTS_EXTRA_FIELDS, 'action=add', 'post'); ?>
			<div class="control-group">
				<label class="control-label" for="comments"><?php echo TABLE_HEADING_FIELDS; ?></label>
				<div class="controls">
					<?php echo os_draw_input_field('field[name]', $field['name'], 'size=30', false, 'text', true); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo TABLE_HEADING_ORDER; ?></label>
				<div class="controls">
					<?php echo os_draw_input_field('field[order]', $field['order'], 'size=5', false, 'text', true); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo TABLE_HEADING_LANGUAGE; ?></label>
				<div class="controls">
					<?php echo os_draw_pull_down_menu('field[language]', $values, '0', ''); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo EF_GROUP; ?></label>
				<div class="controls">
					<?php echo os_draw_pull_down_menu('field[group]', $groupsList); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""></label>
				<div class="controls">
					<input class="btn" type="submit" value="<?php echo BUTTON_INSERT; ?>" />
				</div>
			</div>
			</form>
		</div>
	</div>

	<form name="extra_fields" action="<?php echo os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS,'action=update&page='.$_GET['page']); ?>" method="post">
		<?php echo $action_message; ?>
		<table class="table table-condensed table-big-list" id="tableList">
			<thead>
				<tr>
					<th class="tcenter" width="20"><label id="checkAll"><i class="icon-trash"></i></label></th>
					<th class="tcenter"><span class="line"></span>#</th>
					<th><span class="line"></span><?php echo TABLE_HEADING_FIELDS; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_ORDER; ?></th>
					<th><span class="line"></span><?php echo EF_GROUP; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_LANGUAGE; ?></th>
					<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
				</tr>
			</thead>
		<?php
		$products_extra_fields_query_raw = "select * from ".TABLE_PRODUCTS_EXTRA_FIELDS." order by products_extra_fields_order";

		$products_extra_fields_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_extra_fields_query_raw, $customers_status_query_numrows);
		$products_extra_fields_query = os_db_query($products_extra_fields_query_raw);

		while ($extra_fields = os_db_fetch_array($products_extra_fields_query)) {
		?>
		<tr>
			<td width="30" class="tcenter"><input type="checkbox" name="mark[<?php echo $extra_fields['products_extra_fields_id']; ?>]" value="1"></td>
			<td class="tcenter"><?php echo $extra_fields['products_extra_fields_id']; ?></td>
			<td><?php echo os_draw_input_field('field['.$extra_fields['products_extra_fields_id'].'][name]', $extra_fields['products_extra_fields_name'], 'size=30', false, 'text', true);?></td>
			<td><?php echo os_draw_input_field('field['.$extra_fields['products_extra_fields_id'].'][order]', $extra_fields['products_extra_fields_order'], 'size=5', false, 'text', true);?></td>
			<td><?php echo os_draw_pull_down_menu('field['.$extra_fields['products_extra_fields_id'].'][group]', $groupsList, $extra_fields['products_extra_fields_group'], ''); ?></td>
			<td><?php echo os_draw_pull_down_menu('field['.$extra_fields['products_extra_fields_id'].'][language]', $values, $extra_fields['languages_id'], ''); ?></td>
			<td class="tcenter">
				<?php
					echo '<a '.(($extra_fields['products_extra_fields_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$extra_fields['products_extra_fields_id'].'_0_products_extra_fields_status" data-column="products_extra_fields_status" data-action="products_statusExtraFields" data-id="'.$extra_fields['products_extra_fields_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($extra_fields['products_extra_fields_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$extra_fields['products_extra_fields_id'].'_1_products_extra_fields_status" data-column="products_extra_fields_status" data-action="products_statusExtraFields" data-id="'.$extra_fields['products_extra_fields_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
		</tr>
		<?php } ?>
		</table>
		<hr>
		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_UPDATE; ?>" />
			<input class="btn btn-danger" name="remove" type="submit" value="<?php echo BUTTON_DELETE; ?>" />
		</div>

	</form>
	<?php echo $products_extra_fields_split->display_links($customers_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
<?php } ?>

<?php $main->bottom(); ?>