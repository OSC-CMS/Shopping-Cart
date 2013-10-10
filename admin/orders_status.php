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

if ($_POST['save_status'])
{
	$orders_status_id = os_db_prepare_input($_POST['orders_status_id']);

	foreach($languages AS $lang)
	{
		$orders_status_name_array = $_POST['orders_status_name'];
		$language_id = $lang['languages_id'];

		$sql_data_array = array(
			'orders_status_name' => os_db_prepare_input($orders_status_name_array[$language_id])
		);

		if ($_GET['action'] == 'new')
		{
			if (!os_not_null($orders_status_id))
			{
				$next_id_query = os_db_query("select max(orders_status_id) as orders_status_id from ".TABLE_ORDERS_STATUS."");
				$next_id = os_db_fetch_array($next_id_query);
				$orders_status_id = $next_id['orders_status_id'] + 1;
			}

			$insert_sql_data = array(
				'orders_status_id' => $orders_status_id,
				'language_id' => $language_id
			);
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);
		}
		else
		{
			os_db_perform(TABLE_ORDERS_STATUS, $sql_data_array, 'update', "orders_status_id = '".(int)$orders_status_id."' and language_id = '".(int)$language_id."'");
		}
	}

	if ($_POST['default'] == 'on')
	{
		os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".(int)$orders_status_id."' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
		//set_configuration_cache(); 
	}
	os_redirect(os_href_link(FILENAME_ORDERS_STATUS, 'page='.$_GET['page']));
}

if ($_GET['action'] == 'deleteconfirm')
{
	$orders_status_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
	$orders_status = os_db_fetch_array($orders_status_query);
	if ($orders_status['configuration_value'] == $_GET['oID'])
	{
		os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
		//set_configuration_cache(); 
	}

	os_db_query("delete from ".TABLE_ORDERS_STATUS." where orders_status_id = '".(int)$_GET['oID']."'");

	os_redirect(os_href_link(FILENAME_ORDERS_STATUS, 'page='.$_GET['page']));
}

$breadcrumb->add(BOX_ORDERS_STATUS, FILENAME_ORDERS_STATUS);

$statusEdit = array();
if ($_GET['action'] == 'edit' OR $_GET['action'] == 'delete')
{
	$statusQuery = os_db_query("SELECT * FROM ".TABLE_ORDERS_STATUS." WHERE orders_status_id = '".(int)$_GET['oID']."'");
	while ($status = os_db_fetch_array($statusQuery))
	{
		$statusEdit[$status['language_id']] = $status;
	}
}

if ($_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_ORDERS_STATUS);
}
if ($_GET['action'] == 'edit')
{
	$breadcrumb->add($statusEdit[$_SESSION['languages_id']]['orders_status_name']);
}
if ($_GET['action'] == 'delete')
{
	$breadcrumb->add(TEXT_INFO_HEADING_DELETE_ORDERS_STATUS.' "'.$statusEdit[$_SESSION['languages_id']]['orders_status_name'].'"');
}

$main->head();
$main->top_menu();
?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span8"></div>
		<div class="span4">
			<div class="pull-right">
				<a class="btn btn-mini btn-info" href="<?php echo os_href_link(FILENAME_ORDERS_STATUS, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
			</div>
		</div>
	</div>
</div>

<?php if ($_GET['action'] == 'new' OR $_GET['action'] == 'edit') { ?>

	<form method="post" action="">
		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_INFO_ORDERS_STATUS_NAME; ?></label>
			<div class="controls">
				<ul class="nav nav-tabs default-tabs">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
					<?php } ?>
				</ul>
				<div class="tab-content">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
						<input type="text" name="orders_status_name[<?php echo $lang['languages_id']; ?>]" value="<?php echo $statusEdit[$lang['languages_id']]['orders_status_name']; ?>" />
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for=""></label>
			<div class="controls">
				<label class="checkbox"><?php echo os_draw_checkbox_field('default').' '.TEXT_SET_DEFAULT; ?></label>
			</div>
		</div>
		<hr>
		<div class="tcenter footer-btn">
			<?php if (isset($_GET['oID'])) { ?>
			<input type="hidden" name="orders_status_id" value="<?php echo $_GET['oID']; ?>">
			<?php } ?>
			<input name="save_status" class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_ORDERS_STATUS, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php $oID = os_db_prepare_input($_GET['oID']); ?>

	<h5><?php echo TEXT_INFO_DELETE_INTRO; ?> <?php echo $statusEdit[$_SESSION['languages_id']]['orders_status_name']; ?>?</h5>

	<?php echo os_draw_form('status', FILENAME_ORDERS_STATUS, 'page='.$_GET['page'].'&oID='.$oID.'&action=deleteconfirm'); ?>

		<?php
		$status_query = os_db_query("select count(*) as count from ".TABLE_ORDERS." where orders_status = '".(int)$oID."'");
		$status = os_db_fetch_array($status_query);

		$remove_status = true;
		if ($oID == DEFAULT_ORDERS_STATUS_ID)
		{
			$remove_status = false;
			echo '<div class="alert alert-error">'.ERROR_REMOVE_DEFAULT_ORDER_STATUS.'</div>';
		}
		elseif ($status['count'] > 0)
		{
			$remove_status = false;
			echo '<div class="alert alert-error">'.ERROR_STATUS_USED_IN_ORDERS.'</div>';
		}
		else
		{
			$history_query = os_db_query("select count(*) as count from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".(int)$oID."'");
			$history = os_db_fetch_array($history_query);
			if ($history['count'] > 0)
			{
				$remove_status = false;
				echo '<div class="alert alert-error">'.ERROR_STATUS_USED_IN_HISTORY.'</div>';
			}
		}
		?>

		<div class="tcenter footer-btn">
			<?php if ($remove_status) { ?>
			<input class="btn btn-danger" type="submit" value="<?php echo BUTTON_DELETE; ?>" />
			<?php } ?>
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_ORDERS_STATUS, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

<?php } else { ?>

	<table class="table table-condensed table-big-list border-radius-top">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_ORDERS_STATUS; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$orders_status_query_raw = "select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".(int)$_SESSION['languages_id']."' order by orders_status_id";
	$orders_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $orders_status_query_raw, $orders_status_query_numrows);
	$orders_status_query = os_db_query($orders_status_query_raw);

	while ($orders_status = os_db_fetch_array($orders_status_query))
	{
		if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id'])
			$ordersStatus = '<strong>'.$orders_status['orders_status_name'].' ('.TEXT_DEFAULT.')</strong>';
		else
			$ordersStatus = $orders_status['orders_status_name'];
		?>
		<tr>
			<td><?php echo $ordersStatus; ?></td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_ORDERS_STATUS, 'page='.$_GET['page'].'&oID='.$orders_status['orders_status_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-pencil"></i></a>
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_ORDERS_STATUS, 'page='.$_GET['page'].'&oID='.$orders_status['orders_status_id'].'&action=delete'); ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
		<?php
	}
	?>
	</table>

	<?php echo $orders_status_split->display_count($orders_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?>
	<?php echo $orders_status_split->display_links($orders_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>

<?php } ?>

<?php $main->bottom(); ?>