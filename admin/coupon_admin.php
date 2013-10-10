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

$breadcrumb->add(HEADING_TITLE, FILENAME_COUPON_ADMIN);

if (isset($_GET['action']) && $_GET['action'] == ('voucherreport' OR 'email'))
{
	$getCouponNameQuery = os_db_query("SELECT * FROM ".TABLE_COUPONS_DESCRIPTION." WHERE coupon_id = '".(int)$_GET['cid']."' AND language_id = '".$_SESSION['languages_id']."'");
	$getCouponName = os_db_fetch_array($getCouponNameQuery);

	if ($_GET['action'] == 'voucherreport')
		$breadcrumb->add(BUTTON_REPORT.': '.$getCouponName['coupon_name'], FILENAME_COUPON_ADMIN);
	else
		$breadcrumb->add(BUTTON_SEND_EMAIL.': '.$getCouponName['coupon_name'], FILENAME_COUPON_ADMIN);
}
elseif (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(BUTTON_INSERT, FILENAME_COUPON_ADMIN);
}

$main->head();
$main->top_menu();
?>

<?php
if ($_GET['action'] == 'voucherreport')
{
	?>
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th><?php echo CUSTOMER_ID; ?></th>
					<th><span class="line"></span><?php echo CUSTOMER_NAME; ?></th>
					<th><span class="line"></span><?php echo TEXT_REDEMPTIONS_CUSTOMER; ?></th>
					<th><span class="line"></span><?php echo TEXT_REDEMPTIONS_TOTAL; ?></th>
					<th><span class="line"></span><?php echo IP_ADDRESS; ?></th>
					<th><span class="line"></span><?php echo REDEEM_DATE; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_ORDER_ID; ?></th>
				</tr>
			</thead>
			<?php
			$cc_query_raw = "select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '".(int)$_GET['cid']."'";
			$cc_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $cc_query_raw, $cc_query_numrows);
			$cc_query = os_db_query($cc_query_raw);

			while ($cc_list = os_db_fetch_array($cc_query))
			{
				$customer_query = os_db_query("select customers_firstname, customers_lastname FROM ".TABLE_CUSTOMERS." WHERE customers_id = '".$cc_list['customer_id']."'");
				$customer = os_db_fetch_array($customer_query);

				$count_customers = os_db_query("SELECT * FROM ".TABLE_COUPON_REDEEM_TRACK." WHERE coupon_id = '".(int)$_GET['cid']."' AND customer_id = '".$cc_list['customer_id']."'");
				?>
				<tr>
					<td><?php echo $cc_list['customer_id']; ?></td>
					<td><?php echo $customer['customers_firstname'].' '.$customer['customers_lastname']; ?></td>
					<td><?php echo os_db_num_rows($count_customers); ?></td>
					<td><?php echo os_db_num_rows($cc_query); ?></td>
					<td><?php echo $cc_list['redeem_ip']; ?></td>
					<td><?php echo os_date_short($cc_list['redeem_date']); ?></td>
					<td><a href="orders.php?oID=<?php echo $cc_list['order_id']; ?>&action=edit" target="_blank"><?php echo $cc_list['order_id']; ?></a></td>
				</tr>
				<?php
			}
			?>
		</table>

	<?php
}
elseif ($_GET['action'] == 'email')
{
	$coupon_query = os_db_query("select coupon_code from ".TABLE_COUPONS." where coupon_id = '".(int)$_GET['cid']."'");
	$coupon_result = os_db_fetch_array($coupon_query);
	?>

	<form id="mail" name="mail" action="<?php echo os_href_link(FILENAME_COUPON_ADMIN); ?>" method="post">

		<input type="hidden" name="cid" value="<?php echo $_GET['cid']; ?>">

		<?php
		$customers = array();
		$customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
		$customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
		$customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
		$mail_query = os_db_query("select customers_email_address, customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." order by customers_lastname");
		while($customers_values = os_db_fetch_array($mail_query))
		{
			$customers[] = array(
				'id' => $customers_values['customers_email_address'],
				'text' => $customers_values['customers_lastname'].', '.$customers_values['customers_firstname'].' ('.$customers_values['customers_email_address'].')'
			);
		}
		?>
		<div class="control-group">
			<label class="control-label" for="customers_email_address"><?php echo TEXT_CUSTOMER; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<select class="input-block-level" id="customers_email_address" name="customers_email_address" data-required="true">
					<?php
					foreach ($customers AS $item)
					{
						echo '<option value="'.$item['id'].'">'.$item['text'].'</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="from"><?php echo TEXT_FROM; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="from" name="from" data-required="true" value="<?php echo EMAIL_FROM; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="subject"><?php echo TEXT_SUBJECT; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="subject" name="subject" data-required="true" value="">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="message"><?php echo TEXT_MESSAGE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<textarea class="input-block-level" id="message" id="message" name="message" data-required="true"></textarea>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="coupon_sendMail" data-reload-page="1" type="submit" value="<?php echo BUTTON_SEND_EMAIL; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_COUPON_ADMIN); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

	</form>

	<?php
}
elseif ($_GET['action'] == ('edit' OR 'new'))
{
	if ($_GET['action'] == 'edit')
	{
		$languages = os_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
		{
			if ($languages[$i]['status']==1)
			{
				$language_id = $languages[$i]['id'];
				$coupon_query = os_db_query("select coupon_name,coupon_description from ".TABLE_COUPONS_DESCRIPTION." where coupon_id = '". $_GET['cid']."' and language_id = '".$language_id."'");
				$coupon = os_db_fetch_array($coupon_query);
				$coupon_name[$language_id] = $coupon['coupon_name'];
				$coupon_desc[$language_id] = $coupon['coupon_description'];
			}
		}
		$coupon_query = os_db_query("select coupon_code, coupon_amount, coupon_type, coupon_minimum_order, coupon_start_date, coupon_expire_date, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories from ".TABLE_COUPONS." where coupon_id = '".$_GET['cid']."'");
		$coupon = os_db_fetch_array($coupon_query);
		$coupon_amount = $coupon['coupon_amount'];
		if ($coupon['coupon_type']=='P') {
			$coupon_amount .= '%';
		}
		if ($coupon['coupon_type']=='S') {
			$coupon_free_ship .= true;
		}
		$coupon_min_order = $coupon['coupon_minimum_order'];
		$coupon_code = $coupon['coupon_code'];
		$coupon_uses_coupon = $coupon['uses_per_coupon'];
		$coupon_uses_user = $coupon['uses_per_user'];
		$coupon_products = $coupon['restrict_to_products'];
		$coupon_categories = $coupon['restrict_to_categories'];
		$coupon_start_date = $coupon['coupon_start_date'];
		$coupon_expire_date = $coupon['coupon_expire_date'];
	}
	?>
	<form id="coupon" name="coupon" action="<?php echo os_href_link('coupon_admin.php', 'action=update&oldaction='.$_GET['action'].'&cid='.$_GET['cid']); ?>" method="post">

		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
		<input type="hidden" name="cid" value="<?php echo $_GET['cid']; ?>">

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
						<label class="control-label" for="coupon_name_<?php echo $lang['languages_id']; ?>"><?php echo COUPON_NAME; ?> <span class="input-required">*</span></label>
						<div class="controls">
							<input class="input-block-level" type="text" id="coupon_name_<?php echo $lang['languages_id']; ?>" name="coupon_name[<?php echo $lang['languages_id']; ?>]" data-required="true" value="<?php echo $coupon_name[$lang['languages_id']]; ?>">
							<span class="help-block"><?php echo COUPON_NAME_HELP; ?></span>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="coupon_desc_<?php echo $lang['languages_id']; ?>"><?php echo COUPON_DESC; ?></label>
						<div class="controls">
							<input class="input-block-level" type="text" id="coupon_desc_<?php echo $lang['languages_id']; ?>" name="coupon_desc[<?php echo $lang['languages_id']; ?>]" value="<?php echo $coupon_desc[$lang['languages_id']]; ?>">
							<span class="help-block"><?php echo COUPON_DESC_HELP; ?></span>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<hr>

		<div class="control-group">
			<label class="control-label" for="coupon_amount"><?php echo COUPON_AMOUNT; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="coupon_amount" name="coupon_amount" data-required="true" value="<?php echo $coupon_amount; ?>">
				<span class="help-block"><?php echo COUPON_AMOUNT_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_min_order"><?php echo COUPON_MIN_ORDER; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="coupon_min_order" name="coupon_min_order" value="<?php echo $coupon_min_order; ?>">
				<span class="help-block"><?php echo COUPON_MIN_ORDER_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for=""></label>
			<div class="controls">
				<label class="checkbox"><input type="checkbox" name="coupon_free_ship" value="<?php echo $coupon_free_ship; ?>" <?php echo ($coupon_free_ship == 1 ? 'checked' : ''); ?>> <?php echo COUPON_FREE_SHIP; ?></label>
				<span class="help-block"><?php echo COUPON_FREE_SHIP_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_code"><?php echo COUPON_CODE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="coupon_code" name="coupon_code" data-required="true" value="<?php echo $coupon_code; ?>">
				<span class="help-block"><?php echo COUPON_CODE_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_uses_coupon"><?php echo COUPON_USES_COUPON; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="coupon_uses_coupon" name="coupon_uses_coupon" value="<?php echo $coupon_uses_coupon; ?>">
				<span class="help-block"><?php echo COUPON_USES_COUPON_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_uses_user"><?php echo COUPON_USES_USER; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="coupon_uses_user" name="coupon_uses_user" value="<?php echo ($coupon_uses_user) ? $coupon_uses_user : 1; ?>">
				<span class="help-block"><?php echo COUPON_USES_USER_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_products"><?php echo COUPON_PRODUCTS; ?> <a href="#" class="btn btn-mini ajax-load-page" data-load-page="coupon&action=products" class="data-toggle"><i class="icon-eye-open"></i></a></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="coupon_products" name="coupon_products" value="<?php echo $coupon_products; ?>">
				<span class="help-block"><?php echo COUPON_PRODUCTS_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_categories"><?php echo COUPON_CATEGORIES; ?> <a href="#" class="btn btn-mini ajax-load-page" data-load-page="coupon&action=categories" class="data-toggle"><i class="icon-eye-open"></i></a></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="coupon_categories" name="coupon_categories" value="<?php echo $coupon_categories; ?>">
				<span class="help-block"><?php echo COUPON_CATEGORIES_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_startdate"><?php echo COUPON_STARTDATE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level formDatetime" type="text" id="coupon_startdate" name="coupon_startdate" data-required="true" data-date-autoclose="true" data-date-format="yyyy-mm-dd" value="<?php echo $coupon_start_date; ?>">
				<span class="help-block"><?php echo COUPON_STARTDATE_HELP; ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="coupon_finishdate"><?php echo COUPON_FINISHDATE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level formDatetime" type="text" id="coupon_finishdate" name="coupon_finishdate" data-required="true" data-date-autoclose="true" data-date-format="yyyy-mm-dd" value="<?php echo $coupon_expire_date; ?>">
				<span class="help-block"><?php echo COUPON_FINISHDATE_HELP; ?></span>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="coupon_saveCoupon" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_COUPON_ADMIN); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<?php } else { ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span6">
				<?php echo os_draw_form('status', FILENAME_COUPON_ADMIN, '', 'get'); ?>
				<?php
				$status_array[] = array('id' => 'Y', 'text' => TEXT_COUPON_ACTIVE);
				$status_array[] = array('id' => 'N', 'text' => TEXT_COUPON_INACTIVE);
				$status_array[] = array('id' => '*', 'text' => TEXT_COUPON_ALL);

				if ($_GET['status']) {
					$status = os_db_prepare_input($_GET['status']);
				} else {
					$status = 'Y';
				}
				echo HEADING_TITLE_STATUS.' '.os_draw_pull_down_menu('status', $status_array, $status, 'onChange="this.form.submit();"');
				?>
				</form>
			</div>
			<div class="span6">
				<div class="pull-right">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link('coupon_admin.php', 'action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TEXT_COUPON_HEAD_NAME; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_AMOUNT; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_CODE; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_PRODUCTS; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_CATEGORIES; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_ORDER; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_CUSTOMER; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_FROM; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_TO; ?></th>
				<th><span class="line"></span><?php echo TEXT_COUPON_HEAD_DATE_ADDED; ?></th>
				<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	if ($status != '*')
		$cc_query_raw = "select * from ".TABLE_COUPONS." c LEFT JOIN ".TABLE_COUPONS_DESCRIPTION." d ON (c.coupon_id = d.coupon_id and d.language_id = '".$_SESSION['languages_id']."') where coupon_active='".os_db_input($status)."' and coupon_type != 'G'";
	else
		$cc_query_raw = "select * from ".TABLE_COUPONS." c LEFT JOIN ".TABLE_COUPONS_DESCRIPTION." d ON (c.coupon_id = d.coupon_id and d.language_id = '".$_SESSION['languages_id']."') where coupon_type != 'G'";

	$cc_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $cc_query_raw, $cc_query_numrows);
	$cc_query = os_db_query($cc_query_raw);
	while ($cc_list = os_db_fetch_array($cc_query))
	{
		if ($cc_list['coupon_type'] == 'P')
			$coupon_amount = $cc_list['coupon_amount'].'%';
		elseif ($cc_list['coupon_type'] == 'S')
			$coupon_amount = TEXT_FREE_SHIPPING;
		else
			$coupon_amount = $cc_list['coupon_amount'];
		?>
		<tr>
			<td><?php echo $cc_list['coupon_name']; ?></td>
			<td><?php echo $coupon_amount; ?></td>
			<td><?php echo $cc_list['coupon_code']; ?></td>
			<td>
				<?php if ($cc_list['restrict_to_products']) {
					echo '<a class="btn btn-mini ajax-load-page" data-load-page="coupon&action=view_products&c_id='.$cc_list['coupon_id'].'" class="data-toggle"><i class="icon-eye-open"></i></A>';
				} ?>
			</td>
			<td>
				<?php if ($cc_list['restrict_to_categories']) {
					echo '<a class="btn btn-mini ajax-load-page" data-load-page="coupon&action=view_categories&c_id='.$cc_list['coupon_id'].'" class="data-toggle"><i class="icon-eye-open"></i></A>';
				} ?>
			</td>
			<td><?php echo $cc_list['uses_per_coupon']; ?></td>
			<td><?php echo $cc_list['uses_per_user']; ?></td>
			<td><?php echo os_date_short($cc_list['coupon_start_date']); ?></td>
			<td><?php echo os_date_short($cc_list['coupon_expire_date']); ?></td>
			<td>
				<?php echo os_date_short($cc_list['date_created']); ?>
				<?php if ($cc_list['date_modified']) { ?>
					<i class="icon-edit tt" title="<?php echo DATE_MODIFIED; ?>: <?php echo $cc_list['date_modified']; ?>"></i>
				<?php } ?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="<?php echo os_href_link('coupon_admin.php','action=email&cid='.$cc_list['coupon_id'],'NONSSL'); ?>" title="<?php echo BUTTON_EMAIL; ?>"><?php echo BUTTON_EMAIL; ?></a></li>
						<li><a href="<?php echo os_href_link('coupon_admin.php','action=voucherreport&cid='.$cc_list['coupon_id'],'NONSSL'); ?>" title="<?php echo BUTTON_REPORT; ?>"><?php echo BUTTON_REPORT; ?></a></li>
						<li class="divider"></li>
						<li><a href="<?php echo os_href_link('coupon_admin.php','action=edit&cid='.$cc_list['coupon_id'],'NONSSL'); ?>" title="<?php echo BUTTON_EDIT; ?>"><?php echo BUTTON_EDIT; ?></a></li>
						<li><a href="#" data-action="coupon_deleteCoupon" data-remove-parent="tr" data-id="<?php echo $cc_list['coupon_id']; ?>" data-confirm="<?php echo TEXT_CONFIRM_DELETE; ?>" title="<?php echo BUTTON_DELETE; ?>"><?php echo BUTTON_DELETE; ?></a></li>
					</ul>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>

	<?php if (is_object($cc_split)) { ?>
		<?php echo $cc_split->display_count($cc_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUPONS); ?>
		<?php echo $cc_split->display_links($cc_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
	<?php } ?>

<?php } ?>

<?php $main->bottom(); ?>