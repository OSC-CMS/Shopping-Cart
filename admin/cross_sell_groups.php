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

// Получаем доступные языки
$languages = $cartet->language->get();

if ($_POST['save_group'])
{
	$cross_sell_id = os_db_prepare_input($_POST['products_xsell_grp_name_id']);

	foreach($languages AS $lang)
	{
		$cross_sell_name_array = $_POST['cross_sell_group_name'];
		$language_id = $lang['languages_id'];

		$sql_data_array = array(
			'groupname' => os_db_prepare_input($cross_sell_name_array[$language_id])
		);

		if ($_GET['action'] == 'new')
		{
			if (!os_not_null($cross_sell_id))
			{
				$next_id_query = os_db_query("select max(products_xsell_grp_name_id) as products_xsell_grp_name_id from ".TABLE_PRODUCTS_XSELL_GROUPS."");
				$next_id = os_db_fetch_array($next_id_query);
				$cross_sell_id = $next_id['products_xsell_grp_name_id'] + 1;
			}

			$insert_sql_data = array(
				'products_xsell_grp_name_id' => $cross_sell_id,
				'language_id' => $language_id
			);
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array);
		}
		else
		{
			os_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array, 'update', "products_xsell_grp_name_id = '".os_db_input($cross_sell_id)."' and language_id = '".$language_id."'");
		}
	}
	os_redirect(os_href_link(FILENAME_XSELL_GROUPS, 'page='.$_GET['page']));
}

if ($_GET['action'] == 'deleteconfirm')
{
	$oID = os_db_prepare_input($_GET['oID']);

	os_db_query("delete from ".TABLE_PRODUCTS_XSELL_GROUPS." where products_xsell_grp_name_id = '".os_db_input($oID)."'");

	os_redirect(os_href_link(FILENAME_XSELL_GROUPS, 'page='.$_GET['page']));
}

$breadcrumb->add(BOX_ORDERS_XSELL_GROUP, FILENAME_XSELL_GROUPS);

if ($_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_XSELL_GROUP, FILENAME_XSELL_GROUPS);
}
if ($_GET['action'] == 'edit')
{
	$breadcrumb->add(TEXT_INFO_HEADING_EDIT_XSELL_GROUP, FILENAME_XSELL_GROUPS);
}

$main->head();
$main->top_menu();

$crossEdit = array();
if ($_GET['action'] == 'edit')
{
	$crossQuery = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_XSELL_GROUPS." WHERE products_xsell_grp_name_id = '".(int)$_GET['oID']."'");
	while ($cross = os_db_fetch_array($crossQuery))
	{
		$crossEdit[$cross['language_id']] = $cross;
	}
}
?>

<?php if ($_GET['action'] == 'new' OR $_GET['action'] == 'edit') { ?>

	<form method="post" action="">
		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_INFO_HEADING_NEW_XSELL_GROUP; ?></label>
			<div class="controls">
				<ul class="nav nav-tabs default-tabs">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
					<?php } ?>
				</ul>
				<div class="tab-content">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
						<input type="text" name="cross_sell_group_name[<?php echo $lang['languages_id']; ?>]" value="<?php echo $crossEdit[$lang['languages_id']]['groupname']; ?>" />
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<hr>
		<div class="tcenter footer-btn">
			<?php if (isset($_GET['oID'])) { ?>
			<input type="hidden" name="products_xsell_grp_name_id" value="<?php echo $_GET['oID']; ?>">
			<?php } ?>
			<input name="save_group" class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_XSELL_GROUPS, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

<h4><?php echo TEXT_INFO_HEADING_DELETE_XSELL_GROUP; ?></h4>

	<?php echo os_draw_form('status', FILENAME_XSELL_GROUPS, 'page='.$_GET['page'].'&oID='.$_GET['oID'].'&action=deleteconfirm'); ?>
		
		<?php
			$cross_sell_query = os_db_query("select count(*) as count from ".TABLE_PRODUCTS_XSELL." where products_xsell_grp_name_id = '".(int)$_GET['oID']."'");
			$status = os_db_fetch_array($cross_sell_query);

			$remove_status = true;
			if ($status['count'] > 0)
			{
				$remove_status = false;
				echo '<p>'.ERROR_STATUS_USED_IN_ORDERS.'</p>';
			}
			else
			{
				echo '<p>'.TEXT_INFO_DELETE_INTRO.'</p>';
			}
		?>
		<hr>
		<div class="tcenter footer-btn">
			<?php if ($remove_status) { ?>
			<input class="btn btn-danger" type="submit" value="<?php echo BUTTON_DELETE; ?>" />
			<?php } ?>
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_XSELL_GROUPS, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<?php } else { ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span8"></div>
			<div class="span4">
				<div class="pull-right">
					<a class="btn btn-mini btn-info" href="<?php echo os_href_link(FILENAME_XSELL_GROUPS, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list border-radius-top">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_XSELL_GROUP_NAME; ?></th>
				<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
		<?php
		$cross_sell_query_raw = "select products_xsell_grp_name_id, groupname from ".TABLE_PRODUCTS_XSELL_GROUPS." where language_id = '".$_SESSION['languages_id']."' order by products_xsell_grp_name_id";
		$cross_sell_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $cross_sell_query_raw, $cross_sell_query_numrows);
		$cross_sell_query = os_db_query($cross_sell_query_raw);
		while ($cross_sell = os_db_fetch_array($cross_sell_query)) {
		?>
		<tr>
			<td><?php echo $cross_sell['groupname']; ?></td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_XSELL_GROUPS, 'page='.$_GET['page'].'&oID='.$cross_sell['products_xsell_grp_name_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-pencil"></i></a>
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_XSELL_GROUPS, 'page='.$_GET['page'].'&oID='.$cross_sell['products_xsell_grp_name_id'].'&action=delete'); ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>

	<?php echo $cross_sell_split->display_count($cross_sell_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_XSELL_GROUP); ?>
	<?php echo $cross_sell_split->display_links($cross_sell_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
<?php } ?>

<?php $main->bottom(); ?>