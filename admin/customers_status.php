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

$breadcrumb->add(HEADING_TITLE, FILENAME_CUSTOMERS_STATUS);

if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$statusEdit = $cartet->customers->getCustomerStatusById($_GET['cID']);
	$cInfo = new objectInfo($statusEdit);

	$breadcrumb->add(TEXT_INFO_HEADING_EDIT_CUSTOMERS_STATUS.': '.$cInfo->customers_status_name, FILENAME_CUSTOMERS_STATUS);
}
elseif (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(BUTTON_INSERT, FILENAME_CUSTOMERS_STATUS);
}

$main->head();
$main->top_menu();
?>

<!--
TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO.'<br />'.TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE.' '.$cInfo->customers_status_discount.'%');
TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO.'<br />'.ENTRY_OT_XMEMBER.' '.$customers_status_ot_discount_flag_array[$cInfo->customers_status_ot_discount_flag]['text'].' ('.$cInfo->customers_status_ot_discount_flag.')'.' - '.$cInfo->customers_status_ot_discount.'%');
TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO.'<br />'.ENTRY_GRADUATED_PRICES.' '.$customers_status_graduated_prices_array[$cInfo->customers_status_graduated_prices]['text'].' ('.$cInfo->customers_status_graduated_prices.')' );
TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO.'<br />'.ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES.' '.$customers_status_discount_attributes_array[$cInfo->customers_status_discount_attributes]['text'].' ('.$cInfo->customers_status_discount_attributes.')' );
TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO.'<br />'.ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED.':<b> '.$cInfo->customers_status_payment_unallowed.'</b>');
TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO.'<br />'.ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED.':<b> '.$cInfo->customers_status_shipping_unallowed.'</b>');
TEXT_INFO_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_INTRO.'<br />'.ENTRY_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_DISPLAY.':<b> '.$cInfo->customers_status_accumulated_limit.'</b>');
-->

<?php if (isset($_GET['action']) && ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')) { ?>

	<?php

	$customers_status_ot_discount_flag_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_graduated_prices_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_public_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_show_price_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_show_price_tax_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_discount_attributes_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_add_tax_ot_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_fsk18_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_fsk18_display_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_write_reviews_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
	$customers_status_read_reviews_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));

	?>

	<form id="customers_status" method="post" action="<?php echo os_href_link(FILENAME_CUSTOMERS_STATUS, 'action=insert'); ?>">

		<?php if ($_GET['action'] == 'edit') { ?>
			<input type="hidden" name="cID" value="<?php echo $_GET['cID']; ?>">
			<input type="hidden" name="action" value="save">
		<?php } elseif ($_GET['action'] == 'new') { ?>
			<input type="hidden" name="action" value="insert">
		<?php } ?>

		<?php $languages = $cartet->language->get(); ?>

		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="">Название <span class="input-required">*</span></label>
					<div class="controls">
						<ul class="nav nav-tabs default-tabs">
							<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
								<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
							<?php } ?>
						</ul>
						<div class="tab-content">
							<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
								<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
									<input class="input-block-level" type="text" name="lang[<?php echo $lang['languages_id']; ?>]" value="<?php echo $cInfo->status_lang[$lang['languages_id']]; ?>" />
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_IMAGE; ?> <span class="input-required">*</span></label>
					<div class="controls">
						<input type="file" name="customers_status_image">
						<?php echo ($cInfo->customers_status_image != '') ? os_image(GROUP_ICONS_HTTP.$cInfo->customers_status_image, IMAGE_ICON_INFO) : '';?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO.ENTRY_CUSTOMERS_STATUS_PUBLIC.ENTRY_CUSTOMERS_STATUS_PUBLIC; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_public">
							<option value="0" <?php echo ($cInfo->customers_status_public == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_public == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_CUSTOMERS_STATUS_MIN_ORDER; ?></label>
					<div class="controls">
						<input class="input-block-level" type="text" name="customers_status_min_order" value="<?php echo $cInfo->customers_status_min_order; ?>">
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_MIN_ORDER_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_CUSTOMERS_STATUS_MAX_ORDER; ?></label>
					<div class="controls">
						<input class="input-block-level" type="text" name="customers_status_max_order" value="<?php echo $cInfo->customers_status_max_order; ?>">
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_MAX_ORDER_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_CUSTOMERS_STATUS_SHOW_PRICE; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_show_price">
							<option value="0" <?php echo ($cInfo->customers_status_show_price == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_show_price == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_show_price_tax">
							<option value="0" <?php echo ($cInfo->customers_status_show_price_tax == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_show_price_tax == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_CUSTOMERS_STATUS_ADD_TAX; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_add_tax_ot">
							<option value="0" <?php echo ($cInfo->customers_status_add_tax_ot == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_add_tax_ot == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE; ?></label>
					<div class="controls">
						<input class="input-block-level" type="text" name="customers_status_discount" value="<?php echo $cInfo->customers_status_discount; ?>">
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_discount_attributes">
							<option value="0" <?php echo ($cInfo->customers_status_discount_attributes == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_discount_attributes == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_OT_XMEMBER; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_ot_discount_flag">
							<option value="0" <?php echo ($cInfo->customers_status_ot_discount_flag == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_ot_discount_flag == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE; ?></label>
					<div class="controls">
						<input class="input-block-level" type="text" name="customers_status_ot_discount" value="<?php echo $cInfo->customers_status_ot_discount; ?>">
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo ENTRY_GRADUATED_PRICES; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_graduated_prices">
							<option value="0" <?php echo ($cInfo->customers_status_graduated_prices == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_graduated_prices == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO; ?></label>
					<div class="controls">
						<input class="input-block-level" type="text" name="customers_status_payment_unallowed" value="<?php echo $cInfo->customers_status_payment_unallowed; ?>">
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO; ?></label>
					<div class="controls">
						<input class="input-block-level" type="text" name="customers_status_shipping_unallowed" value="<?php echo $cInfo->customers_status_shipping_unallowed; ?>">
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_FSK18_INTRO; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_fsk18">
							<option value="0" <?php echo ($cInfo->customers_fsk18 == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_fsk18 == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_FSK18; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_FSK18_DISPLAY_INTRO; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_fsk18_display">
							<option value="0" <?php echo ($cInfo->customers_fsk18_display == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_fsk18_display == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_FSK18_DISPLAY; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_WRITE_REVIEWS_INTRO; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_write_reviews">
							<option value="0" <?php echo ($cInfo->customers_status_write_reviews == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_write_reviews == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_STATUS_WRITE_REVIEWS; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_READ_REVIEWS_INTRO; ?></label>
					<div class="controls">
						<select class="input-block-level" name="customers_status_read_reviews">
							<option value="0" <?php echo ($cInfo->customers_status_read_reviews == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
							<option value="1" <?php echo ($cInfo->customers_status_read_reviews == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
						</select>
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_STATUS_READ_REVIEWS; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_INTRO; ?></label>
					<div class="controls">
						<input class="input-block-level" type="text" name="customers_status_accumulated_limit" value="<?php echo $cInfo->customers_status_accumulated_limit; ?>">
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_DISPLAY; ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="fields_input_value"><?php echo TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_INTRO; ?></label>
					<div class="controls">
						<?php
						$orders_status_query = os_db_query("select * from ".TABLE_ORDERS_STATUS." where language_id = ".$_SESSION['languages_id']." ORDER BY orders_status_id");

						$checkStatusQuery = os_db_query("select orders_status_id from ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." where customers_status_id = '".$cInfo->customers_status_id."'");
						$checkStatus = array();
						if (os_db_num_rows($checkStatusQuery) > 0)
						{
							while($cs = os_db_fetch_array($checkStatusQuery))
								$checkStatus[] = $cs['orders_status_id'];
						}

						while ($orders_status = os_db_fetch_array($orders_status_query))
						{
							$selected = (in_array($orders_status['orders_status_id'], $checkStatus)) ? 'checked' : '';
							?>
							<label class="checkbox"><input type="checkbox" name="orders_status_<?php echo $orders_status['orders_status_id']; ?>" value="1" <?php echo $selected; ?>> <?php echo $orders_status['orders_status_name']; ?></label>
						<?php } ?>
						<span class="help-block"><?php echo TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_DISPLAY; ?></span>
					</div>
				</div>
				<?php if (isset($_GET['action']) && $_GET['action'] == 'new') { ?>
				<div class="control-group">
					<label class="control-label" for=""><?php echo TEXT_INFO_CUSTOMERS_STATUS_BASE; ?></label>
					<div class="controls">
						<?php echo os_draw_pull_down_menu('customers_base_status', os_get_customers_statuses()); ?>
						<span class="help-block"><?php echo ENTRY_CUSTOMERS_STATUS_BASE; ?></span>
					</div>
				</div>
				<?php } ?>
				<?php if (DEFAULT_CUSTOMERS_STATUS_ID != $cInfo->customers_status_id) { ?>
					<div class="control-group">
						<label class="control-label" for=""></label>
						<div class="controls">
							<label class="checkbox"><?php echo os_draw_checkbox_field('default').' '.TEXT_SET_DEFAULT; ?></label>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="customers_saveCustomersStatus" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CUSTOMERS_STATUS); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

<?php } else { ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span8"></div>
			<div class="span4">
				<div class="pull-right">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_CUSTOMERS_STATUS, 'action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_ICON; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_USERS; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_CUSTOMERS_STATUS; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_TAX_PRICE; ?></th>
				<th colspan="2"><span class="line"></span><?php echo TABLE_HEADING_DISCOUNT; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_CUSTOMERS_GRADUATED; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
<?php
$customers_status_query_raw = "select * from ".TABLE_CUSTOMERS_STATUS." where language_id = '".(int)$_SESSION['languages_id']."' order by customers_status_id";

$customers_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $customers_status_query_raw, $customers_status_query_numrows);
$customers_status_query = os_db_query($customers_status_query_raw);

while ($customers_status = os_db_fetch_array($customers_status_query))
{
	if (((!isset($_GET['cID'])) || ($_GET['cID'] == $customers_status['customers_status_id'])) && (!isset($cInfo)) && (substr(isset($_GET['action'])?$_GET['action']:'', 0, 3) != 'new'))
	{
		$cInfo = new objectInfo($customers_status);
	}

	if ($customers_status['customers_status_id'] == DEFAULT_CUSTOMERS_STATUS_ID)
		$statusName = '<b>'.$customers_status['customers_status_name'].' ('.TEXT_DEFAULT.')</b>';
	else
		$statusName = $customers_status['customers_status_name'];

	if ($customers_status['customers_status_public'] == '1')
		$public = TEXT_PUBLIC;
	else
		$public = '';
	?>
	<tr>
		<td><?php echo ($customers_status['customers_status_image'] != '') ? os_image(GROUP_ICONS_HTTP.$customers_status['customers_status_image'], IMAGE_ICON_INFO) : '';?></td>
		<td><?php echo os_get_status_users($customers_status['customers_status_id']); ?></td>
		<td><?php echo $statusName.' '.$public; ?></td>
		<td>
			<?php
			if ($customers_status['customers_status_show_price'] == '1')
			{
				echo YES.' / ';
				if ($customers_status['customers_status_show_price_tax'] == '1')
					echo TAX_YES;
				else
					echo TAX_NO;
			}
			?>
		</td>
		<td><?php echo $customers_status['customers_status_discount']; ?> %</td>
		<td>
			<?php
			if ($customers_status['customers_status_ot_discount_flag'] == 0)
				echo '<font color="ff0000">'.$customers_status['customers_status_ot_discount'].' %</font>';
			else
				echo $customers_status['customers_status_ot_discount'].' %';
			?>
		</td>
		<td>
			<?php
			if ($customers_status['customers_status_graduated_prices'] == 0)
				echo NO;
			else
				echo YES;
			?>
		</td>
		<td width="100">
			<div class="btn-group pull-right">
				<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_CUSTOMERS_STATUS, 'page='.$_GET['page'].'&cID='.$customers_status['customers_status_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
				<a href="#" class="btn btn-mini ajax-load-page" data-load-page="customers&action=delete_status&s_id=<?php echo $customers_status['customers_status_id']; ?>" class="data-toggle" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
			</div>
		</td>
	</tr>
	<?php } ?>
</table>


<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr >
<td valign="top"><?php echo $customers_status_split->display_count($customers_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS_STATUS); ?></td>
<td><?php echo $customers_status_split->display_links($customers_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
</tr>
</table>

<?php } ?>

<?php $main->bottom(); ?>