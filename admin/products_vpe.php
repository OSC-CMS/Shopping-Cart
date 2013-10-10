<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

//define('DEFAULT_PRODUCTS_VPE_ID', '1');

require('includes/top.php');

// Получаем доступные языки
$languages = $cartet->language->get();

if ($_POST['save_vpe'])
{
	$products_vpe_id = os_db_prepare_input($_POST['products_vpe_id']);

	foreach($languages AS $lang)
	{
		$products_vpe_name_array = $_POST['products_vpe_name'];
		$language_id = $lang['languages_id'];

		$sql_data_array = array(
			'products_vpe_name' => os_db_prepare_input($products_vpe_name_array[$language_id])
		);

		if ($_GET['action'] == 'new')
		{
			if (!os_not_null($products_vpe_id))
			{
				$next_id_query = os_db_query("select max(products_vpe_id) as products_vpe_id from ".TABLE_PRODUCTS_VPE."");
				$next_id = os_db_fetch_array($next_id_query);
				$products_vpe_id = $next_id['products_vpe_id'] + 1;
			}

			$insert_sql_data = array(
				'products_vpe_id' => $products_vpe_id,
				'language_id' => $language_id
			);
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array);
		}
		else
		{
			os_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array, 'update', "products_vpe_id = '".(int)$products_vpe_id."' and language_id = '".(int)$language_id."'");
		}
	}

	if ($_POST['default'] == 'on')
	{
		os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".(int)$products_vpe_id."' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
		//set_configuration_cache(); 
	}
	os_redirect(os_href_link(FILENAME_PRODUCTS_VPE, 'page='.$_GET['page']));
}

if ($_GET['action'] == 'deleteconfirm')
{
	$products_vpe_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
	$products_vpe = os_db_fetch_array($products_vpe_query);
	if ($products_vpe['configuration_value'] == $_GET['oID'])
	{
		os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
		//set_configuration_cache(); 
	}

	os_db_query("delete from ".TABLE_PRODUCTS_VPE." where products_vpe_id = '".(int)$_GET['oID']."'");

	os_redirect(os_href_link(FILENAME_PRODUCTS_VPE, 'page='.$_GET['page']));
}

$vpeEdit = array();
if ($_GET['action'] == 'edit' OR $_GET['action'] == 'delete')
{
	$vpeQuery = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_VPE." WHERE products_vpe_id = '".(int)$_GET['oID']."'");
	while ($vpe = os_db_fetch_array($vpeQuery))
	{
		$vpeEdit[$vpe['language_id']] = $vpe;
	}
}

$breadcrumb->add(BOX_PRODUCTS_VPE, FILENAME_PRODUCTS_VPE);

if ($_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_PRODUCTS_VPE);
}
if ($_GET['action'] == 'edit')
{
	$breadcrumb->add($vpeEdit[$_SESSION['languages_id']]['products_vpe_name']);
}
if ($_GET['action'] == 'delete')
{
	$breadcrumb->add(TEXT_INFO_HEADING_DELETE_PRODUCTS_VPE.' "'.$vpeEdit[$_SESSION['languages_id']]['products_vpe_name'].'"');
}

$main->head();
$main->top_menu();
?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span8"></div>
		<div class="span4">
			<div class="pull-right">
				<a class="btn btn-mini btn-info" href="<?php echo os_href_link(FILENAME_PRODUCTS_VPE, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
			</div>
		</div>
	</div>
</div>

<?php if ($_GET['action'] == 'new' OR $_GET['action'] == 'edit') { ?>

	<form method="post" action="">
		<div class="control-group">
			<label class="control-label" for="">
				<?php if ($_GET['action'] == 'new') { ?>
				<?php echo TEXT_INFO_HEADING_NEW_PRODUCTS_VPE; ?>
				<?php } else { ?>
				<?php echo TEXT_INFO_HEADING_EDIT_PRODUCTS_VPE; ?>
				<?php } ?>
			</label>
			<div class="controls">
				<ul class="nav nav-tabs default-tabs">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
					<?php } ?>
				</ul>
				<div class="tab-content">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
						<input type="text" name="products_vpe_name[<?php echo $lang['languages_id']; ?>]" value="<?php echo $vpeEdit[$lang['languages_id']]['products_vpe_name']; ?>" />
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
			<input type="hidden" name="products_vpe_id" value="<?php echo $_GET['oID']; ?>">
			<?php } ?>
			<input name="save_vpe" class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_PRODUCTS_VPE, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php $oID = os_db_prepare_input($_GET['oID']); ?>

	<h5><?php echo TEXT_INFO_DELETE_INTRO; ?> <?php echo $vpeEdit[$_SESSION['languages_id']]['products_vpe_name']; ?>?</h5>

	<?php echo os_draw_form('status', FILENAME_PRODUCTS_VPE, 'page='.$_GET['page'].'&oID='.$oID.'&action=deleteconfirm'); ?>

		<?php
		$status_query = os_db_query("select count(*) as count from ".TABLE_ORDERS." where orders_status = '".(int)$oID."'");
		$status = os_db_fetch_array($status_query);

		$remove_status = true;
		if ($oID == DEFAULT_PRODUCTS_VPE_ID)
		{
			$remove_status = false;
			echo '<div class="alert alert-error">'.ERROR_REMOVE_DEFAULT_PRODUCTS_VPE.'</div>';
		}
		?>

		<div class="tcenter footer-btn">
			<?php if ($remove_status) { ?>
			<input class="btn btn-danger" type="submit" value="<?php echo BUTTON_DELETE; ?>" />
			<?php } ?>
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_PRODUCTS_VPE, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

<?php } else { ?>

	<table class="table table-condensed table-big-list border-radius-top">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_PRODUCTS_VPE; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$products_vpe_query_raw = "select products_vpe_id, products_vpe_name from ".TABLE_PRODUCTS_VPE." where language_id = '".$_SESSION['languages_id']."' order by products_vpe_id";
	$products_vpe_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_vpe_query_raw, $products_vpe_query_numrows);
	$products_vpe_query = os_db_query($products_vpe_query_raw);

	while ($products_vpe = os_db_fetch_array($products_vpe_query))
	{
		if (DEFAULT_PRODUCTS_VPE_ID == $products_vpe['products_vpe_id'])
			$vpeStatus = '<strong>'.$products_vpe['products_vpe_name'].' ('.TEXT_DEFAULT.')</strong>';
		else
			$vpeStatus = $products_vpe['products_vpe_name'];
		?>
		<tr>
			<td><?php echo $vpeStatus; ?></td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_PRODUCTS_VPE, 'page='.$_GET['page'].'&oID='.$products_vpe['products_vpe_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-pencil"></i></a>
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_PRODUCTS_VPE, 'page='.$_GET['page'].'&oID='.$products_vpe['products_vpe_id'].'&action=delete'); ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
		<?php
	}
	?>
	</table>

	<?php echo $products_vpe_split->display_count($products_vpe_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_VPE); ?>
	<?php echo $products_vpe_split->display_links($products_vpe_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>

<?php } ?>

<?php $main->bottom(); ?>