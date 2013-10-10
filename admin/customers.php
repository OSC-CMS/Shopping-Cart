<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require ('includes/top.php');

$customers_statuses_array = os_get_customers_statuses();

if (isset($_GET['special']) && $_GET['special'] == 'remove_memo') 
{
	$mID = os_db_prepare_input($_GET['mID']);
	os_db_query("DELETE FROM ".TABLE_CUSTOMERS_MEMO." WHERE memo_id = '".$mID."'");
	os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID='.(int) $_GET['cID'].'&action=edit'));
}

if (isset($_GET['action']) && $_GET['action'] == 'new_order')
{
	$customers1_query = os_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".(int)$_GET['cID']."'");
	$customers1 = os_db_fetch_array($customers1_query);

	$customers_query = os_db_query("select * from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int)$_GET['cID']."'");
	$customers = os_db_fetch_array($customers_query);

	$country_query = os_db_query("select countries_name from ".TABLE_COUNTRIES." where status='1' and countries_id = '".$customers['entry_country_id']."'");
	$country = os_db_fetch_array($country_query);

	$stat_query = os_db_query("select * from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$customers1[customers_status]."' ");
	$stat = os_db_fetch_array($stat_query);

	$sql_data_array = array(
		'customers_id' => os_db_prepare_input($customers['customers_id']),
		'customers_cid' => os_db_prepare_input($customers1['customers_cid']),
		'customers_vat_id' => os_db_prepare_input($customers1['customers_vat_id']),
		'customers_status' => os_db_prepare_input($customers1['customers_status']),
		'customers_status_name' => os_db_prepare_input($stat['customers_status_name']),
		'customers_status_image' => os_db_prepare_input($stat['customers_status_image']),
		'customers_status_discount' => os_db_prepare_input($stat['customers_status_discount']),
		'customers_name' => os_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_secondname'].' '.$customers['entry_lastname']),
		'customers_company' => os_db_prepare_input($customers['entry_company']),
		'customers_street_address' => os_db_prepare_input($customers['entry_street_address']),
		'customers_suburb' => os_db_prepare_input($customers['entry_suburb']),
		'customers_city' => os_db_prepare_input($customers['entry_city']),
		'customers_postcode' => os_db_prepare_input($customers['entry_postcode']),
		'customers_state' => os_db_prepare_input($customers['entry_state']),
		'customers_country' => os_db_prepare_input($country['countries_name']),
		'customers_telephone' => os_db_prepare_input($customers1['customers_telephone']),
		'customers_email_address' => os_db_prepare_input($customers1['customers_email_address']),
		'customers_address_format_id' => '5',
		'customers_ip' => '0',
		'delivery_name' => os_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_secondname'].' '.$customers['entry_lastname']),
		'delivery_company' => os_db_prepare_input($customers['entry_company']),
		'delivery_street_address' => os_db_prepare_input($customers['entry_street_address']),
		'delivery_suburb' => os_db_prepare_input($customers['entry_suburb']),
		'delivery_city' => os_db_prepare_input($customers['entry_city']),
		'delivery_postcode' => os_db_prepare_input($customers['entry_postcode']),
		'delivery_state' => os_db_prepare_input($customers['entry_state']),
		'delivery_country' => os_db_prepare_input($country['countries_name']),
		'delivery_address_format_id' => '5',
		'billing_name' => os_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_secondname'].' '.$customers['entry_lastname']),
		'billing_company' => os_db_prepare_input($customers['entry_company']),
		'billing_street_address' => os_db_prepare_input($customers['entry_street_address']),
		'billing_suburb' => os_db_prepare_input($customers['entry_suburb']),
		'billing_city' => os_db_prepare_input($customers['entry_city']),
		'billing_postcode' => os_db_prepare_input($customers['entry_postcode']),
		'billing_state' => os_db_prepare_input($customers['entry_state']),
		'billing_country' => os_db_prepare_input($country['countries_name']),
		'billing_address_format_id' => '5',
		'payment_method' => 'cod',
		'comments' => '',
		'last_modified' => 'now()',
		'date_purchased' => 'now()',
		'orders_status' => '1',
		'orders_date_finished' => '',
		'currency' => DEFAULT_CURRENCY,
		'currency_value' => '1.0000',
		'account_type' => '0',
		'payment_class' => 'cod',
		'shipping_method' => SHIPPING_FLAT,
		'shipping_class' => 'flat_flat',
		'customers_ip' => '',
		'language' => $_SESSION['language']
	);

	$insert_sql_data = array ('currency_value' => '1.0000');
	$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
	os_db_perform(TABLE_ORDERS, $sql_data_array);
	$orders_id = os_db_insert_id();

	$sql_data_array = array ('orders_id' => $orders_id, 'title' => ORDER_TOTAL, 'text' => '0', 'value' => '0', 'class' => 'ot_total');

	$insert_sql_data = array ('sort_order' => MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER);
	$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
	os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

	$sql_data_array = array ('orders_id' => $orders_id, 'title' => ORDER_SUBTOTAL, 'text' => '0', 'value' => '0', 'class' => 'ot_subtotal');

	$insert_sql_data = array ('sort_order' => MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER);
	$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
	os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

	os_redirect(os_href_link(FILENAME_ORDERS, 'oID='.$orders_id.'&action=edit'));
}

$breadcrumb->add(HEADING_TITLE, FILENAME_CUSTOMERS);

if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['cID']))
{
	$customers_query = os_db_query("select c.payment_unallowed, c.shipping_unallowed, c.customers_gender, c.customers_vat_id, c.customers_status, c.member_flag, c.customers_firstname, c.customers_secondname,c.customers_cid, c.customers_lastname, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_state, a.entry_zone_id, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter, c.customers_username, c.customers_default_address_id from ".TABLE_CUSTOMERS." c left join ".TABLE_ADDRESS_BOOK." a on c.customers_default_address_id = a.address_book_id where a.customers_id = c.customers_id and c.customers_id = '".(int)$_GET['cID']."'");

	$customers = os_db_fetch_array($customers_query);
	$cInfo = new objectInfo($customers);
	$newsletter_array = array(array('id' => '1', 'text' => ENTRY_NEWSLETTER_YES), array('id' => '0', 'text' => ENTRY_NEWSLETTER_NO));

	// Запрос на выбору данных о покупателе
	if (ACCOUNT_PROFILE == 'true')
	{		
		$profileQuery = os_db_query("SELECT * FROM ".DB_PREFIX."customers_profile WHERE customers_id = '".(int)$_GET['cID']."'");
		$profile = os_db_fetch_array($profileQuery);
	}

	$breadcrumb->add($cInfo->customers_lastname.' '.$cInfo->customers_firstname, FILENAME_CUSTOMERS.'?page='.$_GET['page'].'&cID='.$_GET['cID'].'&action=edit');
}
elseif ($_GET['action'] == 'new')
{
	$breadcrumb->add(BUTTON_CREATE_ACCOUNT, FILENAME_CUSTOMERS.'?action=new');
	$cInfo = array();
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')) { ?>

<?php
$avatarImg = (!empty($profile['customers_avatar'])) ? $profile['customers_avatar'] : 'noavatar.gif';
$avatar = http_path('images').'avatars/'.$avatarImg;
?>

<?php if ($_GET['action'] != 'new') { ?>
<div class="row-fluid page-header">
	<div class="span8">
		<img src="<?php echo $avatar; ?>" class="avatar img-circle">
		<h3 class="name"><?php echo $cInfo->customers_lastname; ?> <?php echo $cInfo->customers_secondname; ?> <?php echo $cInfo->customers_firstname; ?></h3>
		<span class="area">
			<?php if ($customers_statuses_array[$cInfo->customers_status]['csa_image'] != '') { echo '<img src="'.GROUP_ICONS_HTTP.$customers_statuses_array[$cInfo->customers_status]['csa_image'].'" alt="">'; } ?>
			<a href="#" class="ajax-load-page" data-load-page="customers&action=editstatus&c_status=<?php echo $cInfo->customers_status; ?>&c_id=<?php echo $_GET['cID']; ?>" class="data-toggle" title="<?php echo BUTTON_STATUS; ?>"><?php if ($_GET['action'] == 'edit') { echo $customers_statuses_array[$cInfo->customers_status]['text']; } ?></a>
		</span>
	</div>
	<div class="span4">
		<?php if ($_GET['cID'] != 1) { ?>
			<?php if ($cInfo->customers_status == 0) { ?>
				<a class="btn btn-success" href="<?php echo os_href_link(FILENAME_ACCOUNTING, os_get_all_get_params(array ('cID', 'action')).'cID='.$_GET['cID']); ?>"><?php echo BUTTON_ACCOUNTING; ?></a>
				<hr>
			<?php } ?>
		<?php } ?>

		<?php
			$getOrdersTotal = os_db_query("SELECT sum(op.final_price) ordersum, COUNT(DISTINCT o.orders_id) orders FROM ".TABLE_CUSTOMERS." c, ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_ORDERS." o WHERE c.customers_id = '".(int)$_GET['cID']."' AND c.customers_id = o.customers_id AND o.orders_id = op.orders_id");
			$total = os_db_fetch_array($getOrdersTotal);

			// Отзывы
			$reviews_query = os_db_query("select count(reviews_id) number_of_reviews from ".TABLE_REVIEWS." where customers_id = '".(int)$_GET['cID']."'");
			$reviews = os_db_fetch_array($reviews_query);
		?>

		<a href="/admin/orders.php?cID=<?php echo $_GET['cID']; ?>">Orders: <?php echo $total['orders']; ?></a><br />
		Total: <?php echo $total['ordersum']; ?><br />
		Reviews: <?php echo $reviews['number_of_reviews']; ?>

	</div>
</div>
<?php } ?>

<form id="customers" name="customers" action="<?php echo os_href_link(FILENAME_CUSTOMERS); ?>" method="post">
	<?php if ($_GET['action'] == 'edit') { ?>
	<input type="hidden" name="default_address_id" value="<?php echo $cInfo->customers_default_address_id; ?>">
	<input type="hidden" name="cID" value="<?php echo $_GET['cID']; ?>">
	<input type="hidden" name="status" value="<?php echo $cInfo->customers_status; ?>">
	<?php } elseif ($_GET['action'] == 'new') { ?>
	<input type="hidden" name="action" value="new">
	<?php } ?>

		<ul class="nav nav-tabs default-tabs">
			<li class="active"><a href="#personal" data-toggle="tab"><?php echo CATEGORY_PERSONAL; ?></a></li>
			<?php if (ACCOUNT_PROFILE == 'true') { ?>
			<li><a href="#profile" data-toggle="tab">Profile</a></li>
			<?php } ?>
			<li><a href="#other" data-toggle="tab">Other</a></li>
			<?php $extraFields = $cartet->customers->getExtraFields($_GET['cID'], $_SESSION['languages_id']); ?>
			<?php if (!empty($extraFields)) { ?>
			<li><a href="#extra_fields" data-toggle="tab"><?php echo CATEGORY_EXTRA_FIELDS ?></a></li>
			<?php } ?>
			<?php if ($_GET['action'] != 'new') { ?>
			<li><a href="#ip_log" data-toggle="tab"><?php echo BUTTON_IPLOG; ?></a></li>
			<?php } ?>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="personal"><div class="pt10">

				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label class="control-label" for="status"><?php echo ENTRY_CUSTOMERS_STATUS; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('status', $customers_statuses_array, (isset($cInfo->customers_status) ? $cInfo->customers_status : 2), 'id="status"'); ?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="csID"><?php echo ENTRY_CID; ?></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="csID" name="csID" value="<?php echo $cInfo->customers_cid; ?>">
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="customers_firstname"><?php echo ENTRY_FIRST_NAME; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_firstname" name="customers_firstname" data-required="true" value="<?php echo $cInfo->customers_firstname; ?>">
							</div>
						</div>

						<?php if (ACCOUNT_SECOND_NAME == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="customers_secondname"><?php echo ENTRY_SECOND_NAME; ?></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_secondname" name="customers_secondname" value="<?php echo $cInfo->customers_secondname; ?>">
							</div>
						</div>
						<?php } ?>

						<div class="control-group">
							<label class="control-label" for="customers_lastname"><?php echo ENTRY_LAST_NAME; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_lastname" name="customers_lastname" data-required="true" value="<?php echo $cInfo->customers_lastname; ?>">
							</div>
						</div>

						<?php if (ACCOUNT_DOB == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="customers_dob"><?php echo ENTRY_DATE_OF_BIRTH; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level formDatetime" type="text" id="customers_dob" name="customers_dob" data-required="true" data-date-autoclose="true" data-date-format="yyyy-mm-dd" value="<?php echo $cInfo->customers_dob; ?>">
							</div>
						</div>
						<?php } ?>

						<?php if (ACCOUNT_GENDER == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="customers_gender"><?php echo ENTRY_GENDER; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<select class="input-block-level" id="customers_gender" name="customers_gender" data-required="true">
									<option value="" <?php echo (empty($cInfo->customers_gender)) ? 'selected' : ''; ?>></option>
									<option value="m" <?php echo ($cInfo->customers_gender == 'm') ? 'selected' : ''; ?>><?php echo MALE; ?></option>
									<option value="f" <?php echo ($cInfo->customers_gender == 'f') ? 'selected' : ''; ?>><?php echo FEMALE; ?></option>
								</select>
							</div>
						</div>
						<?php } ?>

						<div class="control-group">
							<label class="control-label" for="customers_newsletter"><?php echo ENTRY_NEWSLETTER; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<select class="input-block-level" id="customers_newsletter" name="customers_newsletter" data-required="true">
									<option value="1" <?php echo ($cInfo->customers_newsletter == 1) ? 'selected' : ''; ?>><?php echo ENTRY_NEWSLETTER_YES; ?></option>
									<option value="0" <?php echo ($cInfo->customers_newsletter == 0) ? 'selected' : ''; ?>><?php echo ENTRY_NEWSLETTER_NO; ?></option>
								</select>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="entry_password"><?php echo ENTRY_NEW_PASSWORD; ?></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="entry_password" name="entry_password" value="<?php echo $cInfo->entry_password; ?>">
							</div>
						</div>
					</div>

					<div class="span6">
						<?php if ($_GET['action'] == 'new') { ?>
						<div class="control-group">
							<label class="control-label" for="customers_mail"><?php echo ENTRY_MAIL; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<select class="input-block-level" id="customers_mail" name="customers_mail" data-required="true">
									<option value="yes" selected><?php echo YES; ?></option>
									<option value="no"><?php echo NO; ?></option>
								</select>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="mail_comments"><?php echo ENTRY_MAIL_COMMENTS; ?></label>
							<div class="controls">
								<textarea class="input-block-level" id="mail_comments" name="mail_comments"></textarea>
							</div>
						</div>
						<?php } else { ?>
						<div class="control-group">
							<label class="control-label" for=""></label>
							<div class="controls">
								<a href="#" class="ajax-load-page btn btn-small btn-success pull-right" data-load-page="customers&action=add_memo&c_id=<?php echo $_GET['cID']; ?>" class="data-toggle"><?php echo BUTTON_INSERT; ?></a>
								<h5><?php echo ENTRY_MEMO; ?></h5>
								<table class="table table-condensed table-big-list">
									<thead>
										<tr>
											<th><?php echo TEXT_DATE; ?></th>
											<th><span class="line"></span><?php echo TEXT_TITLE; ?></th>
											<th><span class="line"></span><?php echo TEXT_POSTER; ?></th>
											<th><span class="line"></span>Текст</th>
											<th class="tright"><span class="line"></span>Действие</th>
										</tr>
									</thead>
								<?php
								$memo_query = os_db_query("SELECT * FROM ".TABLE_CUSTOMERS_MEMO." WHERE customers_id = '".(int)$_GET['cID']."' ORDER BY memo_date DESC");

								if (os_db_num_rows($memo_query) > 0)
								{
									while ($memo_values = os_db_fetch_array($memo_query))
									{
										$poster_query = os_db_query("SELECT customers_firstname, customers_lastname FROM ".TABLE_CUSTOMERS." WHERE customers_id = '".(int)$memo_values['poster_id']."'");
										$poster_values = os_db_fetch_array($poster_query);
										?>
										<tr>
											<td><?php echo $memo_values['memo_date']; ?></td>
											<td><?php echo $memo_values['memo_title']; ?></td>
											<td><?php echo $poster_values['customers_lastname']; ?> <?php echo $poster_values['customers_firstname']; ?></td>
											<td><?php echo $memo_values['memo_text']; ?></td>
											<td class="tright"><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, 'cID='.$_GET['cID'].'&action=edit&special=remove_memo&mID='.$memo_values['memo_id']); ?>" onClick="return confirm('<?php echo DELETE_ENTRY; ?>')"><?php echo BUTTON_DELETE; ?></a></td>
										</tr>
										<?php
									}
								}
								else
								{
									echo '<tr><td colspan="4">Заметок нет</td></tr>';
								}
								?>
								</table>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div></div>

			<?php if (ACCOUNT_PROFILE == 'true') { ?>
			<div class="tab-pane" id="profile"><div class="pt10">

				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label class="control-label" for="customers_username">customers_username</label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_username" name="customers_username" value="<?php echo $cInfo->customers_username; ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="customers_signature">customers_signature</label>
							<div class="controls">
								<textarea class="input-block-level" id="customers_signature" name="customers_signature"><?php echo $profile['customers_signature']; ?></textarea>
							</div>
						</div>
						<hr>
						<div class="control-group">
							<label class="control-label" for="customers_avatar">customers_avatar</label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_avatar" name="customers_avatar" value="<?php echo $profile['customers_avatar']; ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="customers_photo">customers_photo</label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_photo" name="customers_photo" value="<?php echo $profile['customers_photo']; ?>">
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="control-group">
							<label class="control-label" for="show_gender">show_gender</label>
							<div class="controls">
								<select class="input-block-level" id="show_gender" name="show_gender">
									<option value="1" <?php echo ($profile['show_gender'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_gender'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="show_firstname">show_firstname</label>
							<div class="controls">
								<select class="input-block-level" id="show_firstname" name="show_firstname">
									<option value="1" <?php echo ($profile['show_firstname'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_firstname'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="show_secondname">show_secondname</label>
							<div class="controls">
								<select class="input-block-level" id="show_secondname" name="show_secondname">
									<option value="1" <?php echo ($profile['show_secondname'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_secondname'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="show_lastname">show_lastname</label>
							<div class="controls">
								<select class="input-block-level" id="show_lastname" name="show_lastname">
									<option value="1" <?php echo ($profile['show_lastname'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_lastname'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="show_dob">show_dob</label>
							<div class="controls">
								<select class="input-block-level" id="show_dob" name="show_dob">
									<option value="1" <?php echo ($profile['show_dob'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_dob'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="show_email">show_email</label>
							<div class="controls">
								<select class="input-block-level" id="show_email" name="show_email">
									<option value="1" <?php echo ($profile['show_email'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_email'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="show_telephone">show_telephone</label>
							<div class="controls">
								<select class="input-block-level" id="show_telephone" name="show_telephone">
									<option value="1" <?php echo ($profile['show_telephone'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_telephone'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="show_fax">show_fax</label>
							<div class="controls">
								<select class="input-block-level" id="show_fax" name="show_fax">
									<option value="1" <?php echo ($profile['show_fax'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
									<option value="0" <?php echo ($profile['show_fax'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>

			</div></div>
			<?php } ?>

			<div class="tab-pane" id="other"><div class="pt10">

				<div class="row-fluid">
					<div class="span6">
						<h5><?php echo CATEGORY_ADDRESS; ?></h5>

						<?php if (ACCOUNT_COUNTRY == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="country"><?php echo ENTRY_COUNTRY; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<?php echo os_get_country_list('country', $cInfo->entry_country_id, 'onChange="changeselect();" id="country" data-required="true"'); ?>
							</div>
						</div>
						<?php } ?>

						<?php if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="state"><?php echo ENTRY_STATE;?> <span class="input-required">*</span></label>
							<div class="controls">
								<script language="javascript">
								<!--
								function changeselect(reg) {
									//clear select
									document.customers.state.length=0;
									var j=0;
									for (var i=0;i<zones.length;i++) {
									if (zones[i][0]==document.customers.country.value) {
									document.customers.state.options[j]=new Option(zones[i][1],zones[i][1]);
									j++;
									}
									}
									if (j==0) {
									document.customers.state.options[0]=new Option('-','-');
									}
									if (reg) { document.customers.state.value = reg; }
								}
								var zones = new Array(
								<?php
								$zones_query = os_db_query("select zone_country_id,zone_name from ".TABLE_ZONES." order by zone_name asc");
								$mas=array();
								while ($zones_values = os_db_fetch_array($zones_query)) {
									$zones[] = 'new Array('.$zones_values['zone_country_id'].',"'.$zones_values['zone_name'].'")';
								}
								if ($_GET['action'] == 'edit')
								{
									$zones_array1[] = 'new Array('.$cInfo->entry_country_id.',"'.os_get_zone_name($cInfo->entry_country_id,$cInfo->entry_zone_id,'').'")';
									$zones = array_merge($zones_array1, $zones);
								}
								echo implode(',',$zones);
								?>
								);
								document.write('<select class="input-block-level" id="state" name="state" data-required="true">');
								document.write('</select>');
								changeselect("<?php echo os_db_prepare_input($_POST['state']); ?>");
								-->
								</script>
							</div>
						</div>
						<?php } ?>

						<?php if (ACCOUNT_SUBURB == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="entry_suburb"><?php echo ENTRY_SUBURB; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="entry_suburb" name="entry_suburb" data-required="true" value="<?php echo $cInfo->entry_suburb; ?>">
							</div>
						</div>
						<?php } ?>

						<?php if (ACCOUNT_CITY == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="entry_city"><?php echo ENTRY_CITY; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="entry_city" name="entry_city" data-required="true" value="<?php echo $cInfo->entry_city; ?>">
							</div>
						</div>
						<?php } ?>

						<?php if (ACCOUNT_POSTCODE == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="entry_postcode"><?php echo ENTRY_POST_CODE; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="entry_postcode" name="entry_postcode" data-required="true" value="<?php echo $cInfo->entry_postcode; ?>">
							</div>
						</div>
						<?php } ?>

						<?php if (ACCOUNT_STREET_ADDRESS == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="entry_street_address"><?php echo ENTRY_STREET_ADDRESS; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="entry_street_address" name="entry_street_address" data-required="true" value="<?php echo $cInfo->entry_street_address; ?>">
							</div>
						</div>
						<?php } ?>

						<hr>

						<h5><?php echo CATEGORY_OPTIONS; ?></a></h5>
						<div class="control-group">
							<label class="control-label" for="payment_unallowed"><?php echo ENTRY_PAYMENT_UNALLOWED; ?></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="payment_unallowed" name="payment_unallowed" value="<?php echo $cInfo->payment_unallowed; ?>">
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="shipping_unallowed"><?php echo ENTRY_SHIPPING_UNALLOWED; ?></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="shipping_unallowed" name="shipping_unallowed" value="<?php echo $cInfo->shipping_unallowed; ?>">
							</div>
						</div>

					</div>
					<div class="span6">

						<h5><?php echo CATEGORY_CONTACT; ?></h5>

						<div class="control-group">
							<label class="control-label" for="customers_email_address"><?php echo ENTRY_EMAIL_ADDRESS; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<?php
								$emailError = '';
								if (isset($error) && $error == true)
								{
									if ($entry_email_address_error == true)
										$emailError = ENTRY_EMAIL_ADDRESS_ERROR;
									elseif ($entry_email_address_check_error == true)
										$emailError = ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
									elseif ($entry_email_address_exists == true)
										$emailError = ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
								}
								?>
								<input class="input-block-level" type="text" id="customers_email_address" name="customers_email_address" data-required="true" data-type="email" value="<?php echo $cInfo->customers_email_address; ?>">
								<?php echo $emailError; ?>
							</div>
						</div>

						<?php if (ACCOUNT_TELE == 'true') { ?>
						<div class="control-group">
							<label class="control-label" for="customers_telephone"><?php echo ENTRY_TELEPHONE_NUMBER; ?> <span class="input-required">*</span></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_telephone" name="customers_telephone" data-required="true" value="<?php echo $cInfo->customers_telephone; ?>">
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="customers_fax"><?php echo ENTRY_FAX_NUMBER; ?></label>
							<div class="controls">
								<input class="input-block-level" type="text" id="customers_fax" name="customers_fax" value="<?php echo $cInfo->customers_fax; ?>">
							</div>
						</div>
						<?php } ?>

						<?php if (ACCOUNT_COMPANY == 'true') { ?>
							<hr>
							<h5><?php echo CATEGORY_COMPANY; ?></h5>
							<div class="control-group">
								<label class="control-label" for="entry_company"><?php echo ENTRY_COMPANY; ?> <span class="input-required">*</span></label>
								<div class="controls">
									<input class="input-block-level" type="text" id="entry_company" name="entry_company" data-required="true" value="<?php echo $cInfo->entry_company; ?>">
								</div>
							</div>

							<?php if(ACCOUNT_COMPANY_VAT_CHECK == 'true') { ?>
							<div class="control-group">
								<label class="control-label" for="customers_vat_id"><?php echo ENTRY_VAT_ID; ?> <span class="input-required">*</span></label>
								<div class="controls">
									<input class="input-block-level" type="text" id="customers_vat_id" name="customers_vat_id" data-required="true" value="<?php echo $cInfo->customers_vat_id; ?>">
								</div>
							</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>

			</div></div>

			<?php if (!empty($extraFields)) { ?>
			<div class="tab-pane" id="extra_fields"><div class="pt10">
				<?php foreach ($extraFields as $ef) { ?>
					<div class="control-group">
						<label class="control-label" for="<?php echo $ef['id']; ?>"><?php echo $ef['name']; ?> <?php echo ($ef['required'] == 1) ? '*' : ''; ?></label>
						<div class="controls">
							<?php echo $ef['value']; ?>
						</div>
					</div>
				<?php } ?>
			</div></div>
			<?php } ?>

			<div class="tab-pane" id="ip_log"><div class="pt10">
				<table class="table table-condensed table-big-list">
					<thead>
						<tr>
							<th>IP</th>
							<th><span class="line"></span><?php echo TEXT_DATE; ?></th>
							<th><span class="line"></span>host</th>
							<th><span class="line"></span>advertiser</th>
							<th><span class="line"></span><?php echo TEXT_INFO_ORIGINAL_REFERER; ?></th>
						</tr>
					</thead>
					<?php
					$customers_log_info_array = os_get_user_info((int)$_GET['cID']);
					if (os_db_num_rows($customers_log_info_array))
					{
						while ($customers_log_info = os_db_fetch_array($customers_log_info_array))
						{ ?>
							<tr>
								<td><?php echo $customers_log_info['customers_ip']; ?></td>
								<td><?php echo $customers_log_info['customers_ip_date']; ?></td>
								<td><?php echo $customers_log_info['customers_host']; ?></td>
								<td><?php echo $customers_log_info['customers_advertiser']; ?></td>
								<td><?php echo $customers_log_info['customers_referer_url']; ?></td>
							</tr>
						<?php }
					}
					?>
				</table>
			</div></div>

		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="customers_saveCustomer" data-reload-page="1" type="submit" value="<?php echo BUTTON_UPDATE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array('action'))); ?>"><?php echo TEXT_BACK; ?></a>
		</div>

	</div>

</form>
<?php

} else { ?>

<div class="second-page-nav">

	<div class="row-fluid">
		<div class="span6">
			<?php echo os_draw_form('search', FILENAME_CUSTOMERS, '', 'get'); ?>
				<fieldset>
					<?php echo os_draw_input_field('search', '', 'placeholder="'.HEADING_TITLE_SEARCH.'…"').os_draw_hidden_field(os_session_name(), os_session_id()); ?>
				</fieldset>
			</form>
		</div>
		<div class="span6">
			<div class="pull-right">
				<?php
				$select_data = array ();
				$select_data = array (array ('id' => '99', 'text' => HEADING_TITLE_STATUS), array ('id' => '100', 'text' => TEXT_ALL_CUSTOMERS));
				?>
				<?php echo os_draw_form('status', FILENAME_CUSTOMERS, '', 'get'); ?>
					<fieldset>
						<?php echo os_draw_pull_down_menu('status', os_array_merge($select_data, $customers_statuses_array), '99', 'onChange="this.form.submit();"').os_draw_hidden_field(os_session_name(), os_session_id()); ?>
					</fieldset>
				</form>
			</div>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span8">

		</div>
		<div class="span4">
			<div class="pull-right">
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'action=new'); ?>"><?php echo BUTTON_CREATE_ACCOUNT; ?></a>
			</div>
		</div>
	</div>
</div>


<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_ACCOUNT_TYPE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_LASTNAME.os_sorting(FILENAME_CUSTOMERS,'customers_lastname'); ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_FIRSTNAME.os_sorting(FILENAME_CUSTOMERS,'customers_firstname'); ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_COUNTRY; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_NUMBER_OF_REVIEWS; ?></th>
			<th><span class="line"></span><?php echo HEADING_TITLE_STATUS; ?></th>
			<?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {?>
			<th><span class="line"></span><?php echo HEADING_TITLE_VAT; ?></th>
			<?php } ?>
			<th><span class="line"></span><?php echo TABLE_HEADING_ACCOUNT_CREATED.os_sorting(FILENAME_CUSTOMERS,'date_account_created'); ?></th>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
<?php

$search = '';
if ((isset($_GET['search'])) && (os_not_null($_GET['search']))) 
{
	$keywords = os_db_input(os_db_prepare_input($_GET['search']));
	$search = "and (c.customers_lastname like '%".$keywords."%' or c.customers_firstname like '%".$keywords."%' or c.customers_email_address like '%".$keywords."%' or c.customers_telephone like '%".$keywords."%')";
}

if (isset($_GET['status']) && ($_GET['status'] != '100' or $_GET['status'] == '0')) 
{
	$search = "and c.customers_status = '".(int)$_GET['status']."'";
}

if (isset($_GET['sorting'])) 
{
	switch ($_GET['sorting'])
	{
		case 'customers_firstname' :
			$sort = 'order by c.customers_firstname';
		break;

		case 'customers_firstname-desc' :
			$sort = 'order by c.customers_firstname DESC';
		break;

		case 'customers_lastname' :
			$sort = 'order by c.customers_lastname';
		break;

		case 'customers_lastname-desc' :
			$sort = 'order by c.customers_lastname DESC';
		break;

		case 'date_account_created' :
			$sort = 'order by ci.customers_info_date_account_created';
		break;

		case 'date_account_created-desc' :
			$sort = 'order by ci.customers_info_date_account_created DESC';
		break;
	}
}
else
{
	if (!isset($sort) or empty($sort))
		$sort = '';
}

$customers_query_raw = "select c.account_type, c.customers_id, c.customers_vat_id, c.customers_vat_id_status, c.customers_lastname, c.customers_firstname, c.customers_secondname, c.customers_email_address, a.entry_country_id, c.customers_status, c.member_flag, ci.customers_info_date_account_created from ".TABLE_CUSTOMERS." c , ".TABLE_ADDRESS_BOOK." a, ".TABLE_CUSTOMERS_INFO." ci WHERE c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id and ci.customers_info_id = c.customers_id ".$search." group by c.customers_id ".$sort;

$customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $customers_query_raw, $customers_query_numrows);
$customers_query = os_db_query($customers_query_raw);

$aCustomers = array();
$aCustomersCountries = array();
$aCustomersIds = array();
while ($customers = os_db_fetch_array($customers_query))
{
	$aCustomers[] = $customers;
	$aCustomersCountries[$customers['customers_id']] = $customers['entry_country_id'];
	$aCustomersIds[$customers['customers_id']] = $customers['customers_id'];
}

// Страны
$aCountries = array();
$country_query = os_db_query("select countries_id, countries_name from ".TABLE_COUNTRIES." where countries_id IN (".implode(',', $aCustomersCountries).")");
while ($country = os_db_fetch_array($country_query))
{
	$aCountries[$country['countries_id']] = $country['countries_name'];
}

// Отзывы
$aReviewsCount = array();
$reviews_query = os_db_query("select customers_id, count(reviews_id) as number_of_reviews from ".TABLE_REVIEWS." where customers_id IN (".implode(',', $aCustomersIds).") GROUP BY customers_id");
while ($reviews = os_db_fetch_array($reviews_query))
{
	$aReviewsCount[$reviews['customers_id']] = $reviews['number_of_reviews'];
}

// Покупатели
foreach ($aCustomers AS $customers)
{
	//if (is_object($cInfo))
	//{
		/*
		$contents[] = array ('text' => '<br /><b>'.TEXT_DATE_ACCOUNT_CREATED.'</b> '.os_date_short($cInfo->date_account_created));
		$contents[] = array ('text' => '<br /><b>'.TEXT_DATE_ACCOUNT_LAST_MODIFIED.'</b> '.os_date_short($cInfo->date_account_last_modified));
		$contents[] = array ('text' => '<br /><b>'.TEXT_INFO_DATE_LAST_LOGON.'</b> '.os_date_short($cInfo->date_last_logon));
		$contents[] = array ('text' => '<br /><b>'.TEXT_INFO_NUMBER_OF_LOGONS.'</b> '.$cInfo->number_of_logons);*/
	//}
	?>
	<tr>
		<td><?php echo (($customers['account_type'] == 1) ? TEXT_GUEST : TEXT_ACCOUNT); ?></td>
		<td><strong><?php echo $customers['customers_lastname']; ?></strong></td>
		<td><?php echo $customers['customers_firstname']; ?></td>
		<td><?php echo $aCountries[$customers['entry_country_id']]; ?></td>
		<td><?php echo $aReviewsCount[$customers['customers_id']]; ?></td>
		<td>
			<?php if ($customers['customers_id'] != 1) { ?>
				<a href="#" class="ae_select" data-type="select" data-value="<?php echo $customers['customers_status']; ?>" data-pk="<?php echo $customers['customers_id']; ?>" data-url="customers_changeStatus_get&customers_status=<?php echo $customers['customers_status']; ?>" data-action="customers_getStatus" data-original-title="<?php echo TEXT_INFO_HEADING_STATUS_CUSTOMER; ?>"><?php echo $customers_statuses_array[$customers['customers_status']]['text']; ?></a>
			<?php } else { ?>
				<?php echo $customers_statuses_array[$customers['customers_status']]['text']; ?>
			<?php } ?>
		</td>
		<?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {?>
		<td>
		<?php
		if ($customers['customers_vat_id'])
		{
			echo $customers['customers_vat_id'].'<br />('.os_validate_vatid_status($customers['customers_id']).')';
		}
		?>
		</td>
		<?php } ?>
		<td><?php echo $customers['date_account_created']; ?></td>
		<td>
			<div class="btn-group pull-right">
				<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<?php if ($customers['customers_id'] == 1 && $_SESSION['customer_id'] == 1) { ?>
					<li><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$customers['customers_id'].'&action=edit'); ?>"><?php echo BUTTON_EDIT; ?></a></li>
					<li class="divider"></li>
					<?php } ?>
					<?php if ($customers['customers_id'] != 1) { ?>
					<li><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$customers['customers_id'].'&action=edit'); ?>"><?php echo BUTTON_EDIT; ?></a></li>
					<li><a href="#" class="ajax-load-page" data-load-page="customers&action=delete&c_id=<?php echo $customers['customers_id']; ?>" class="data-toggle"><?php echo BUTTON_DELETE; ?></a></li>
					<?php if ($customers['customers_status'] == 0) { ?>
					<li class="divider"></li>
					<li><a href="<?php echo os_href_link(FILENAME_ACCOUNTING, os_get_all_get_params(array ('cID', 'action')).'cID='.$customers['customers_id']); ?>"><?php echo BUTTON_ACCOUNTING; ?></a></li>
					<?php } ?>
					<li class="divider"></li>
					<?php } ?>
					<li><a href="<?php echo os_href_link(FILENAME_ORDERS, 'cID='.$customers['customers_id']); ?>"><?php echo BUTTON_ORDERS; ?></a></li>
					<li><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$customers['customers_id'].'&action=new_order'); ?>" onClick="return confirm('<?php echo NEW_ORDER; ?>')"><?php echo BUTTON_NEW_ORDER; ?></a></li>
					<li class="divider"></li>
					<li><a href="<?php echo os_href_link(FILENAME_MAIL, 'selected_box=tools&customer='.$customers['customers_email_address']); ?>"><?php echo BUTTON_EMAIL; ?></a></li>
				</ul>
			</div>
		</td>
	</tr>
	<?php
}
?>
</table>

<?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?>
<?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], os_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?>

<?php } ?>

<?php $main->bottom(); ?>