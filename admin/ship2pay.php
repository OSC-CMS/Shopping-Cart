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

function os_p2s_get_moduleinfo($module_type) 
{
	if($module_type == "shipping")
	{
		$files = explode(';',MODULE_SHIPPING_INSTALLED);
	}
	elseif($module_type == "payment")
	{
		$files = explode(';', MODULE_PAYMENT_INSTALLED);
	}

	$installed_modules = array();

	foreach($files as $file)
	{
		if (!empty($file))
		{
			$fr = substr($file, 0, strrpos($file, '.'));

			if (is_file(_MODULES.$module_type.'/'.$fr.'/'.$_SESSION['language_admin'].'.php'))
			{
				include (_MODULES.$module_type.'/'.$fr.'/'.$_SESSION['language_admin'].'.php');
			}

			if (is_file(_MODULES.$module_type.'/'.$fr.'/'.$fr.'.php'))
			{
				include (_MODULES.$module_type.'/'.$fr.'/'.$fr.'.php');
			}

			$class = substr($file, 0, strrpos($file, '.'));
			if (os_class_exists($class))
			{
				$module = new $class;
				$installed_modules[$file] = $module->title;
			}
		}
	}

	return ($installed_modules);
}

function os_p2s_module_name($modules) 
{
	global $payment_modules;
	$files = explode(';',$modules);
	$names = '';
	foreach($files as $file)
	{
		$names .= $payment_modules[$file].', ';
	}
	return(rtrim($names, ', '));
}

$shipping_modules = os_p2s_get_moduleinfo('shipping');
$payment_modules = os_p2s_get_moduleinfo('payment');


$breadcrumb->add(HEADING_TITLE, FILENAME_SHIP2PAY);

if ($_GET['action'] == 'edit')
	$breadcrumb->add(TEXT_INFO_HEADING_EDIT_SHP2PAY, FILENAME_SHIP2PAY);
elseif ($_GET['action'] == 'new')
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_SHP2PAY, FILENAME_SHIP2PAY);

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

	<form id="s2p" name="s2p" action="<?php echo FILENAME_SHIP2PAY; ?>" method="post">

		<?php if (isset($_GET['s2p_id']) && !empty($_GET['s2p_id'])) { ?>
			<input type="hidden" name="s2p_id" value="<?php echo $_GET['s2p_id']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<?php
		if ($_GET['action'] == 'edit')
		{
			$getS2PQuery = os_db_query("SELECT * FROM ".TABLE_SHIP2PAY." WHERE s2p_id = '".(int)$_GET['s2p_id']."'");
			$getS2P = os_db_fetch_array($getS2PQuery);
			$allowed = explode(';', $getS2P['payments_allowed']);
		}
		else
		{
			$getS2P = array();
			$allowed = array();
		}

		$ship_menu = array();
		foreach($shipping_modules as $file => $title)
		{
			$ship_menu[] = array('id' => $file, 'text' => $title);
		}

		?>
		<div class="control-group">
			<label class="control-label" for="content_text"><?php echo TEXT_INFO_SHIPMENT; ?></label>
			<div class="controls">
				<?php echo os_draw_pull_down_menu("shp_id", $ship_menu); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_text"><?php echo TEXT_INFO_ZONE; ?></label>
			<div class="controls">
				<?php echo os_cfg_pull_down_zone_classes($getS2P['zones_id'], 'zone_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_text"><?php echo TEXT_INFO_PAYMENTS; ?></label>
			<div class="controls">
				<?php
				foreach($payment_modules as $file => $title)
				{
					echo '<label class="checkbox">'.os_draw_checkbox_field('pay_ids[]', $file, (in_array($file, $allowed) ? true : false)).' '.$title.'</label>';
				}
				?>
				<span class="help-block"><?php echo TEXT_INFO_PAYMENTS_ALLOWED; ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="status"><?php echo TABLE_HEADING_STATUS; ?></label>
			<div class="controls">
				<select class="input-block-level" name="status" id="status">
					<option value="1" <?php echo ($getS2P['status'] == 1 OR !isset($getS2P['status'])) ? 'selected' : ''; ?>><?php echo YES; ?></option>
					<option value="0" <?php echo ($getS2P['status'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
				</select>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="shipping_save" data-reload-page="1" type="submit" value="<?php echo BUTTON_INSERT; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_SHIP2PAY, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<? } else { ?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6"></div>
		<div class="span6">
			<div class="btn-group pull-right">
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_SHIP2PAY, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_SHIPMENT; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ZONE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_PAYMENTS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
	<?php
	$s2p_query_raw = "select s2p_id, shipment, payments_allowed, zones_id, status from ".TABLE_SHIP2PAY;
	$s2p_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $s2p_query_raw, $s2p_query_numrows);
	$s2p_query = os_db_query($s2p_query_raw);
	if (os_db_num_rows($s2p_query) > 0)
	{
		while ($s2p = os_db_fetch_array($s2p_query))
		{
		?>
		<tr>
			<td><?php echo $shipping_modules[$s2p['shipment']]; ?></td>
			<td><?php echo os_get_zone_class_title($s2p['zones_id']); ?></td>
			<td><?php echo os_p2s_module_name($s2p['payments_allowed']); ?></td>
			<td class="tcenter">
			<?php
			echo '<a '.(($s2p['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$s2p['s2p_id'].'_0_status" data-column="status" data-action="shipping_statusShipToPay" data-id="'.$s2p['s2p_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
			echo '<a '.(($s2p['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$s2p['s2p_id'].'_1_status" data-column="status" data-action="shipping_statusShipToPay" data-id="'.$s2p['s2p_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_SHIP2PAY, 'page='.$_GET['page'].'&s2p_id='.$s2p['s2p_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="shipping_deleteShipToPay" data-remove-parent="tr" data-id="<?php echo $s2p['s2p_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
		<?php
		}
	}
	else
	{
		echo '<tr><td colspan="5" class="tcenter">'.TEXT_EMPTY_ERROR.'</td></tr>';
	} ?>
</table>


<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><?php echo $s2p_split->display_count($s2p_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAYMENTS); ?></td>
		<td><?php echo $s2p_split->display_links($s2p_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
	</tr>
</table>

<?php } ?>

<?php $main->bottom(); ?>