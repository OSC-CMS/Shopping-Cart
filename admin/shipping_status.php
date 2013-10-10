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

$breadcrumb->add(BOX_SHIPPING_STATUS, FILENAME_SHIPPING_STATUS);

if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit'))
{
	if ($_GET['action'] == 'new')
	{
		$breadcrumb->add(TEXT_INFO_HEADING_NEW_SHIPPING_STATUS, FILENAME_SHIPPING_STATUS.'?action=new');
		$shipping_status = array();
	}
	if ($_GET['action'] == 'edit')
	{
		$shipping_status_query = os_db_query("select * from ".TABLE_SHIPPING_STATUS." where shipping_status_id = '".(int)$_GET['oID']."'");
		while ($status = os_db_fetch_array($shipping_status_query))
		{
			$shipping_status[$status['language_id']] = $status;
		}

		$breadcrumb->add($shipping_status[$_SESSION['languages_id']]['shipping_status_name'], FILENAME_SHIPPING_STATUS.'?action=new');
	}
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

	<form id="status" name="status" action="<?php echo os_href_link(FILENAME_SHIPPING_STATUS); ?>" method="post" enctype="multipart/form-data">

		<?php if (isset($_GET['oID']) && !empty($_GET['oID'])) { ?>
			<input type="hidden" name="oID" value="<?php echo $_GET['oID']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<?php $languages = $cartet->language->get(); ?>

		<ul class="nav nav-tabs default-tabs">
			<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
				<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
			<?php } ?>
		</ul>
		<div class="tab-content">
			<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
				<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
					<div class="control-group">
						<label class="control-label" for="shipping_status_name_<?php echo $lang['languages_id']; ?>"><?php echo TEXT_INFO_SHIPPING_STATUS_NAME; ?> <span class="input-required">*</span></label>
						<div class="controls">
							<input class="input-block-level" type="text" id="shipping_status_name_<?php echo $lang['languages_id']; ?>" name="shipping_status_name[<?php echo $lang['languages_id']; ?>]" data-required="true" value="<?php echo $shipping_status[$lang['languages_id']]['shipping_status_name']; ?>">
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="control-group">
			<label class="control-label" for="shipping_status_image"><?php echo TEXT_INFO_SHIPPING_STATUS_IMAGE; ?></label>
			<div class="controls">
				<input type="file" id="shipping_status_image" name="shipping_status_image">
				<?php if ($shipping_status[$lang['languages_id']]['shipping_status_image']) { ?>
					<input type="hidden" name="shipping_status_image_current" value="<?php echo $shipping_status[$lang['languages_id']]['shipping_status_image']; ?>">
					<br />
					<img src="<?php echo http_path('images').'shipping_status/'.$shipping_status[$lang['languages_id']]['shipping_status_image']; ?>" alt="">
				<?php } ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for=""></label>
			<div class="controls">
				<label class="checkbox"><input type="checkbox" name="default" value="on"> <?php echo TEXT_SET_DEFAULT; ?></label>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="shipping_saveShippingStatus" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_SHIPPING_STATUS, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<? } else { ?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6"></div>
		<div class="span6">
			<div class="btn-group pull-right">
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_SHIPPING_STATUS, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_SHIPPING_STATUS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
<?php
$shipping_status_query_raw = "select * from ".TABLE_SHIPPING_STATUS." where language_id = '".$_SESSION['languages_id']."' order by shipping_status_id";
$shipping_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $shipping_status_query_raw, $shipping_status_query_numrows);
$shipping_status_query = os_db_query($shipping_status_query_raw);
while ($shipping_status = os_db_fetch_array($shipping_status_query))
{
	if (!empty($shipping_status['shipping_status_image']))
		$img = '<img src="'.http_path('images').'shipping_status/'.$shipping_status['shipping_status_image'].'" alt="">';
	else
		$img = '';
	?>
	<tr>
		<?php
		if (DEFAULT_SHIPPING_STATUS_ID == $shipping_status['shipping_status_id'])
			$name = '<b>'.$shipping_status['shipping_status_name'].' ('.TEXT_DEFAULT.')</b>';
		else
			$name = $shipping_status['shipping_status_name'];
		?>
		<td><?php echo $img; ?></td>
		<td><?php echo $name; ?></td>
		<td width="100">
			<div class="btn-group pull-right">
				<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_SHIPPING_STATUS, 'page='.$_GET['page'].'&oID='.$shipping_status['shipping_status_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
				<?php if ($shipping_status['shipping_status_id'] != DEFAULT_SHIPPING_STATUS_ID) { ?>
					<a class="btn btn-mini" href="#" data-action="shipping_deleteShippingStatus" data-remove-parent="tr" data-id="<?php echo $shipping_status['shipping_status_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				<?php } ?>
			</div>
		</td>
	</tr>
	<?php
}
?>
</table>

<table border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td><?php echo $shipping_status_split->display_count($shipping_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SHIPPING_STATUS); ?></td>
		<td><?php echo $shipping_status_split->display_links($shipping_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
	</tr>
</table>

<?php } ?>



<table border="0" cellspacing="0" cellpadding="2">
<?php
$heading = array();
$contents = array();
switch ($_GET['action'])
{
	case 'edit':
		$heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_EDIT_SHIPPING_STATUS.'</b>');

		$contents = array('form' => os_draw_form('status', FILENAME_SHIPPING_STATUS, 'page='.$_GET['page'].'&oID='.$oInfo->shipping_status_id .'&action=save', 'post', 'enctype="multipart/form-data"'));
		$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

		$shipping_status_inputs_string = '';
		$languages = os_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
		$shipping_status_inputs_string .= '<br />'.os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif').'&nbsp;'.os_draw_input_field('shipping_status_name['.$languages[$i]['id'].']', os_get_shipping_status_name($oInfo->shipping_status_id, $languages[$i]['id']));
		}
		}
		$contents[] = array('text' => '<br />'.TEXT_INFO_SHIPPING_STATUS_IMAGE.'<br />'.os_draw_file_field('shipping_status_image',$oInfo->shipping_status_image));
		$contents[] = array('text' => '<br />'.TEXT_INFO_SHIPPING_STATUS_NAME.$shipping_status_inputs_string);
		if (DEFAULT_SHIPPING_STATUS_ID != $oInfo->shipping_status_id) $contents[] = array('text' => '<br />'.os_draw_checkbox_field('default').' '.TEXT_SET_DEFAULT);
		$contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="'.BUTTON_UPDATE.'"/>'.BUTTON_UPDATE.'</button></span> <a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_SHIPPING_STATUS, 'page='.$_GET['page'].'&oID='.$oInfo->shipping_status_id).'"><span>'.BUTTON_CANCEL.'</span></a>');
	break;

	case 'delete':
		$heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_DELETE_SHIPPING_STATUS.'</b>');

		$contents = array('form' => os_draw_form('status', FILENAME_SHIPPING_STATUS, 'page='.$_GET['page'].'&oID='.$oInfo->shipping_status_id .'&action=deleteconfirm'));
		$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
		$contents[] = array('text' => '<br /><b>'.$oInfo->shipping_status_name.'</b>');
		if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="'.BUTTON_DELETE.'"/>'.BUTTON_DELETE.'</button></span> <a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_SHIPPING_STATUS, 'page='.$_GET['page'].'&oID='.$oInfo->shipping_status_id).'"><span>'.BUTTON_CANCEL.'</span></a>');
	break;

}
?>
</table>
<?php $main->bottom(); ?>