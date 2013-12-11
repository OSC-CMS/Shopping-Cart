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
$osTemplate = new osTemplate;
require_once(_LIB.'phpmailer/class.phpmailer.php');
require (_CLASS_ADMIN.'currencies.php');
$currencies = new currencies();

if (isset($_GET['action']))
{
	if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order')) && ($_GET['oID']))
	{
		$oID = os_db_prepare_input($_GET['oID']);

		$orders_query = os_db_query("select orders_id from ".TABLE_ORDERS." where orders_id = '".os_db_input($oID)."'");
		$order_exists = true;
		if (!os_db_num_rows($orders_query))
		{
			$order_exists = false;
			$messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
		}
	}
}

require (get_path('class_admin').'order.php');
require (_CLASS.'price.php');

if (isset($_GET['action']))
{
	if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order')) && ($order_exists))
	{
		$order = new order($oID);

		$order_payment = isset($order->info['payment_class'])?$order->info['payment_class']:'';

		if (!empty($order_payment) && is_file(_MODULES.'payment/'.$order_payment.'/'.$order->info['language'].'.php'))
		{
			require(_MODULES.'payment/'.$order_payment.'/'.$order->info['language'].'.php');
			$order_payment_text = constant(MODULE_PAYMENT_.strtoupper($order_payment)._TEXT_TITLE);
		}
		else
		{
			$order_payment_text = TEXT_NO;
		}

		$shipping_method_query = os_db_query("select title from ".TABLE_ORDERS_TOTAL." where orders_id = '".os_db_input($oID)."' and class = 'ot_shipping'");
		$shipping_method = os_db_fetch_array($shipping_method_query);

		$order_shipping_text = ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title']));
	}
}
else
	$order_shipping_text = '';

if (isset($order->info['language']))
{
	$lang_query = os_db_query("select languages_id from ".TABLE_LANGUAGES." where directory = '".$order->info['language']."'");
	$lang = os_db_fetch_array($lang_query);
	$lang = $lang['languages_id'];
}
else
	$lang = $_SESSION['languages_id'];

$orders_statuses = array ();
$orders_status_array = array ();
$orders_status_query = os_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".(int)$lang."'");
while ($orders_status = os_db_fetch_array($orders_status_query))
{
	$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

if (isset($_POST['submit']) && isset($_POST['multi_orders']))
{
	if (($_POST['submit'] == BUTTON_SUBMIT) && (isset($_POST['new_status'])) && (!isset($_POST['delete_orders'])))
	{
		$status = os_db_prepare_input($_POST['new_status']);
		$comments = os_db_prepare_input($_POST['comments']);
		if ($status == '')
		{
			os_redirect(os_href_link(FILENAME_ORDERS),os_get_all_get_params());
		}

		foreach ($_POST['multi_orders'] as $this_orderID)
		{
			$order_updated = false;
			$check_status_query = os_db_query("select customers_name, customers_telephone, customers_email_address, orders_status, date_purchased from ".TABLE_ORDERS." where orders_id = '".(int)$this_orderID."'");
			$check_status = os_db_fetch_array($check_status_query);

			if ($check_status['orders_status'] != $status)
			{
				os_db_query("update ".TABLE_ORDERS." set orders_status = '".os_db_input($status)."', last_modified = now() where orders_id = '".(int)$this_orderID."'");
				$customer_notified ='0'; 
				if (isset($_POST['notify']))
				{
					$notify_comments = '';
					$osTemplate->assign('language', $_SESSION['language_admin']);
					$osTemplate->caching = false;
					$osTemplate->assign('tpl_path', DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
					$osTemplate->assign('logo_path', http_path('images') );
					$osTemplate->assign('NAME', $check_status['customers_name']);
					$osTemplate->assign('ORDER_NR', $this_orderID);
					$osTemplate->assign('ORDER_LINK', os_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$_POST['multi_orders'], 'SSL'));
					$osTemplate->assign('ORDER_DATE', os_date_long($check_status['date_purchased']));
					$osTemplate->assign('ORDER_STATUS', $orders_status_array[$status]);

					if (is_file(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail_'.$status.'.html') && is_file(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail_'.$status.'.txt'))
					{
						$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail_'.$status.'.html');
						$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail_'.$status.'.txt');
					}
					else
					{
						$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail.html');
						$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail.txt');
					}

					os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail, $txt_mail);

					// СМС уведомления
					$smsSetting = $cartet->sms->setting();

					if ($smsSetting['sms_status'] == 1)
					{
						$getDefaultSms = $cartet->sms->getDefaultSms();

						// шаблон смс письма
						$osTemplate->caching = 0;
						$smsText = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail_sms.txt');

						// уведомление покупателя
						if ($check_status['customers_telephone'] && $smsSetting['sms_order_change'] == 1)
						{
							$cartet->sms->send($smsText, $check_status['customers_telephone']);
						}
					}

					$billing_subject = str_replace('{$nr}', $this_orderID, EMAIL_BILLING_SUBJECT);

					$customer_notified = '1';
				}

				os_db_query("insert into ".TABLE_ORDERS_STATUS_HISTORY." (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".(int)$this_orderID."', '".os_db_input($status)."', now(), '".os_db_input($customer_notified)."', '".os_db_input($comments) ."')");
				$order_updated = true;

				// изменение статуса заказа
				do_action('change_order_status');

				$changed = false;

				$check_group_query = os_db_query("select customers_status_id from ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." where orders_status_id = ".$status);
				if (os_db_num_rows($check_group_query))
				{
					while ($groups = os_db_fetch_array($check_group_query))
					{
						$customer_query = os_db_query("select c.* from ".TABLE_CUSTOMERS." as c, ".TABLE_ORDERS." as o where o.customers_id = c.customers_id and o.orders_id = ".(int)$this_orderID );
						$customer = os_db_fetch_array($customer_query);
						unset($customer_id1);
						if ($customer['customers_status'] == '0')
							$customer_id1 = 0;
						else
							$customer_id1 = $customer['customers_id'];

						$statuses_groups_query = os_db_query("select orders_status_id from ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." where customers_status_id = ".$groups['customers_status_id']);
						$purchase_query = "select sum(ot.value) as total from ".TABLE_ORDERS_TOTAL." as ot, ".TABLE_ORDERS." as o where ot.orders_id = o.orders_id and o.customers_id = ".$customer_id1." and ot.class = 'ot_total' and (";
						$statuses = os_db_fetch_array($statuses_groups_query);
						$purchase_query .= " o.orders_status = ".$statuses['orders_status_id'];
						while ($statuses = os_db_fetch_array($statuses_groups_query))
						{
							$purchase_query .= " or o.orders_status = ".$statuses['orders_status_id'];
						}
						$purchase_query .=");";

						$total_purchase_query = os_db_query($purchase_query);
						$total_purchase = os_db_fetch_array($total_purchase_query);
						$customers_total = $total_purchase['total'];
						if (empty($customers_total)) $customers_total = 0;

						$acc_query = os_db_query("
						select cg.customers_status_accumulated_limit,
						cg.customers_status_name,
						cg.customers_status_discount
						from ".TABLE_CUSTOMERS_STATUS." as cg,
						".TABLE_CUSTOMERS." as c
						where cg.customers_status_id = c.customers_status
						and c.customers_id = " .$customer_id1);
						$current_limit = @mysql_result($acc_query, 0, "customers_status_accumulated_limit");
						if (empty($current_limit)) $current_limit = 0;
						$current_discount = @mysql_result($acc_query, 0, "customers_status_discount");
						$current_group = @mysql_result($acc_query, 0, "customers_status_name");
						if (empty($current_discount)) $current_discount = 0;                                                                                                                                                                                                

						$groups_query = os_db_query("select customers_status_discount, customers_status_id, customers_status_name, customers_status_accumulated_limit from ".TABLE_CUSTOMERS_STATUS." where customers_status_accumulated_limit < ".$customers_total." and customers_status_discount >= ".$current_discount." and customers_status_accumulated_limit >= ".$current_limit." and customers_status_id = ".$groups['customers_status_id']." order by customers_status_accumulated_limit DESC");

						if (os_db_num_rows($groups_query))
						{
							$customers_groups_id = @mysql_result($groups_query, 0, "customers_status_id");
							$customers_groups_name = @mysql_result($groups_query, 0, "customers_status_name");
							$limit = @mysql_result($groups_query, 0, "customers_status_accumulated_limit");
							if (empty($limit)) $limit = 0;
							$current_discount = @mysql_result($groups_query, 0, "customers_status_discount");
							if (empty($current_discount)) $current_discount = 0;

							os_db_query("update ".TABLE_CUSTOMERS." set customers_status = ".$customers_groups_id." where customers_id = " .$customer_id1);

							$changed = true;
						}
					}

					$groups_query = os_db_query("select cg.* from ".TABLE_CUSTOMERS_STATUS." as cg, ".TABLE_CUSTOMERS." as c where c.customers_status = cg.customers_status_id and c.customers_id = " .$customer_id1);
					$customers_groups_id = @mysql_result($groups_query, 0, "customers_status_id");
					$customers_groups_name = @mysql_result($groups_query, 0, "customers_status_name");
					$limit = @mysql_result($groups_query, 0, "customers_status_accumulated_limit");
					if (empty($limit)) $limit = 0;
					$current_discount = @mysql_result($groups_query, 0, "customers_status_discount");
					if (empty($current_discount)) $current_discount = 0;
					if ($changed)
					{
						$osTemplate->assign('language', $_SESSION['language_admin']);
						$osTemplate->caching = false;
						$osTemplate->assign('tpl_path', DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
						$osTemplate->assign('logo_path', http_path('images') );
						$osTemplate->assign('CUSTOMERNAME', $check_status['customers_name']);
						$osTemplate->assign('EMAIL', $check_status['customers_email_address']);
						$osTemplate->assign('GROUPNAME', $customers_groups_name);
						$osTemplate->assign('GROUPDISCOUNT', $current_discount);
						$osTemplate->assign('ACCUMULATED_LIMIT', $currencies->display_price($limit, 0));

						$html_mail_admin = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_admin.html');
						$txt_mail_admin = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_admin.txt');

						os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_ACC_SUBJECT, $html_mail_admin, $txt_mail_admin);

						$html_mail_customer = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_customer.html');
						$txt_mail_customer = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_customer.txt');

						os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_ACC_SUBJECT, $html_mail_customer, $txt_mail_customer);
					}
				}
			}

			if ($order_updated == true)
				$messageStack->add_session(BUS_ORDER.$this_orderID.' '.BUS_SUCCESS, 'success');
			else
				$messageStack->add_session(BUS_ORDER.$this_orderID.' '.BUS_WARNING, 'warning');
		}
	}

	if (($_POST['submit'] == BUTTON_SUBMIT)&&(isset($_POST['delete_orders'])))
	{ 
		foreach ($_POST['multi_orders'] as $this_orderID)
		{
			$orders_deleted = false;

			os_db_query("delete from ".TABLE_ORDERS." where orders_id = '".(int)$this_orderID."'");
			os_db_query("delete from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".(int)$this_orderID."'");
			os_db_query("delete from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".(int)$this_orderID."'");
			os_db_query("delete from ".TABLE_ORDERS_PRODUCTS_DOWNLOAD." where orders_id = '".(int)$this_orderID."'");
			os_db_query("delete from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".(int)$this_orderID."'");
			os_db_query("delete from ".TABLE_ORDERS_TOTAL." where orders_id = '".(int)$this_orderID."'");

			$orders_deleted = true;

			if ($orders_deleted == true)
				$messageStack->add_session(BUS_ORDER.$this_orderID.' '.BUS_DELETE_SUCCESS, 'success');
			else
				$messageStack->add_session(BUS_ORDER.$this_orderID.' '.BUS_DELETE_WARNING, 'warning');
		}
	}
	os_redirect(os_href_link(FILENAME_ORDERS),os_get_all_get_params());
}

switch (@$_GET['action'])
{
case 'update_order' :
$oID = os_db_prepare_input($_GET['oID']);
$status = os_db_prepare_input($_POST['status']);
$comments = os_db_prepare_input($_POST['comments']);
$order_updated = false;
$check_status_query = os_db_query("select customers_name, customers_email_address, orders_status, date_purchased from ".TABLE_ORDERS." where orders_id = '".os_db_input($oID)."'");
$check_status = os_db_fetch_array($check_status_query);
if ($check_status['orders_status'] != $status || $comments != '') {
os_db_query("update ".TABLE_ORDERS." set orders_status = '".os_db_input($status)."', last_modified = now() where orders_id = '".os_db_input($oID)."'");

$customer_notified = '0';
if (isset($_POST['notify']) && $_POST['notify'] == 'on')
{
	$notify_comments = '';
	if ($_POST['notify_comments'] == 'on') {
	$notify_comments = $comments;
	} else {
	$notify_comments = '';
	}

	$osTemplate->assign('language', $_SESSION['language_admin']);
	$osTemplate->caching = false;

	$osTemplate->assign('tpl_path', DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
	$osTemplate->assign('logo_path', http_path('images') );

	$osTemplate->assign('NAME', $check_status['customers_name']);
	$osTemplate->assign('ORDER_NR', $oID);
	$osTemplate->assign('ORDER_LINK', os_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$oID, 'SSL'));
	$osTemplate->assign('ORDER_DATE', os_date_long($check_status['date_purchased']));
	$osTemplate->assign('NOTIFY_COMMENTS', $notify_comments);
	$osTemplate->assign('ORDER_STATUS', $orders_status_array[$status]);

	if (is_file(_MAIL.'admin/'.$order->info['language'].'/change_order_mail_'.$status.'.html') && is_file(_MAIL.'admin/'.$order->info['language'].'/change_order_mail_'.$status.'.txt'))
	{
	$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/change_order_mail_'.$status.'.html');
	$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/change_order_mail_'.$status.'.txt');
	}
	else
	{
	$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/change_order_mail.html');
	$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/change_order_mail.txt');
	}

	os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail, $txt_mail);

	// СМС уведомления
	$smsSetting = $cartet->sms->setting();

	if ($smsSetting['sms_status'] == 1)
	{
		$getDefaultSms = $cartet->sms->getDefaultSms();

		// шаблон смс письма
		$osTemplate->caching = 0;
		$smsText = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/change_order_mail_sms.txt');

		// уведомление покупателя
		if ($check_status['customers_telephone'] && $smsSetting['sms_order_change'] == 1)
		{
			$cartet->sms->send($smsText, $check_status['customers_telephone']);
		}
	}

	$billing_subject = str_replace('{$nr}', $oID, EMAIL_BILLING_SUBJECT);
	$customer_notified = '1';
}

os_db_query("insert into ".TABLE_ORDERS_STATUS_HISTORY." (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".os_db_input($oID)."', '".os_db_input($status)."', now(), '".$customer_notified."', '".os_db_input($comments)."')");

$this_orderID = $oID;

// изменение статуса заказа
do_action('change_order_status');

$order_updated = true;
}

if ($order_updated) {
$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
} else {
$messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
}


$changed = false;

$check_group_query = os_db_query("select customers_status_id from ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." where orders_status_id = ".$status);
if (os_db_num_rows($check_group_query)) {
while ($groups = os_db_fetch_array($check_group_query)) {

$customer_query = os_db_query("select c.* from ".TABLE_CUSTOMERS." as c, ".TABLE_ORDERS." as o where o.customers_id = c.customers_id and o.orders_id = ".(int)$oID);
$customer = os_db_fetch_array($customer_query);
if ($customer['customers_status'] == '0') {
$customer_id = 0;
} else {
$customer_id = $customer['customers_id'];
}
$statuses_groups_query = os_db_query("select orders_status_id from ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." where customers_status_id = ".$groups['customers_status_id']);
$purchase_query = "select sum(ot.value) as total from ".TABLE_ORDERS_TOTAL." as ot, ".TABLE_ORDERS." as o where ot.orders_id = o.orders_id and o.customers_id = ".$customer_id." and ot.class = 'ot_total' and (";
$statuses = os_db_fetch_array($statuses_groups_query);
$purchase_query .= " o.orders_status = ".$statuses['orders_status_id'];
while ($statuses = os_db_fetch_array($statuses_groups_query)) {
$purchase_query .= " or o.orders_status = ".$statuses['orders_status_id'];
}
$purchase_query .=");";

$total_purchase_query = os_db_query($purchase_query);
$total_purchase = os_db_fetch_array($total_purchase_query);
$customers_total = $total_purchase['total'];

if (empty($customers_total)) $customers_total = 0;
$acc_query = os_db_query("select cg.customers_status_accumulated_limit, cg.customers_status_name, cg.customers_status_discount from ".TABLE_CUSTOMERS_STATUS." as cg, ".TABLE_CUSTOMERS." as c where cg.customers_status_id = c.customers_status and c.customers_id = ".$customer_id);
$current_limit = @mysql_result($acc_query, 0, "customers_status_accumulated_limit");
if (empty($current_limit)) $current_limit = 0;
$current_discount = @mysql_result($acc_query, 0, "customers_status_discount");
if (empty($current_discount)) $current_discount = 0;
$current_group = @mysql_result($acc_query, "customers_status_name");
if ($customer['customers_status'] > '0') {

// ok, looking for available group
$groups_query = os_db_query("select customers_status_discount, customers_status_id, customers_status_name, customers_status_accumulated_limit from ".TABLE_CUSTOMERS_STATUS." where customers_status_accumulated_limit < ".$customers_total." and customers_status_discount > ".$current_discount." and customers_status_accumulated_limit >= ".$current_limit." and customers_status_id = ".$groups['customers_status_id']." order by customers_status_accumulated_limit DESC");

}
if (os_db_num_rows($groups_query)) {
$customers_groups_id = @mysql_result($groups_query, 0, "customers_status_id");
$customers_groups_name = @mysql_result($groups_query, 0, "customers_status_name");
$limit = @mysql_result($groups_query, 0, "customers_status_accumulated_limit");
if (empty($limit)) $limit = 0;
$current_discount = @mysql_result($groups_query, 0, "customers_status_discount");
if (empty($current_discount)) $current_discount = 0;
os_db_query("update ".TABLE_CUSTOMERS." set customers_status = ".$customers_groups_id." where customers_id = ".$customer_id);
$changed = true;
}
}
$groups_query = os_db_query("select cg.* from ".TABLE_CUSTOMERS_STATUS." as cg, ".TABLE_CUSTOMERS." as c where c.customers_status = cg.customers_status_id and c.customers_id = ".$customer_id);
$customers_groups_id = @mysql_result($groups_query, 0, "customers_status_id");
$customers_groups_name = @mysql_result($groups_query, 0, "customers_status_name");
$limit = @mysql_result($groups_query, 0, "customers_status_accumulated_limit");
$current_discount = @mysql_result($groups_query, 0, "customers_status_discount");
if (empty($current_discount)) $current_discount = 0;
if (empty($limit)) $limit = 0;

if ($changed) {


$osTemplate->assign('language', $_SESSION['language_admin']);
$osTemplate->caching = false;


$osTemplate->assign('tpl_path', DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
$osTemplate->assign('logo_path', http_path('images') );

$osTemplate->assign('CUSTOMERNAME', $check_status['customers_name']);
$osTemplate->assign('EMAIL', $check_status['customers_email_address']);
$osTemplate->assign('GROUPNAME', $customers_groups_name);
$osTemplate->assign('GROUPDISCOUNT', $current_discount);
$osTemplate->assign('ACCUMULATED_LIMIT', $currencies->display_price($limit, 0));


$html_mail_admin = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/accumulated_discount_admin.html');
$txt_mail_admin = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/accumulated_discount_admin.txt');

os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_ACC_SUBJECT, $html_mail_admin, $txt_mail_admin);


$html_mail_customer = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/accumulated_discount_customer.html');
$txt_mail_customer = $osTemplate->fetch(_MAIL.'admin/'.$order->info['language'].'/accumulated_discount_customer.txt');

os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_ACC_SUBJECT, $html_mail_customer, $txt_mail_customer);

}
}

os_redirect(os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('action')).'action=edit'));
break;
}

// добавление товара к заказу
if (isset($_POST['new_product']) && !empty($_POST['new_product']))
{
	$cartet->order->addProductsToOrder($_POST['new_product']);
	os_redirect(os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('action')).'action=edit'));
}

$breadcrumb->add(HEADING_TITLE, FILENAME_ORDERS);

if (isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists)) {

$breadcrumb->add(TABLE_HEADING_EDIT.' #'.$oID, FILENAME_ORDERS);

}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists)) { ?>
	<?php $osPrice = new osPrice($order->info['currency'], isset($order->info['status']) ? $order->info['status'] : ''); ?>

	<div class="p10">

		<div class="btn-group pull-right">
			<?php
			if (ACTIVATE_GIFT_SYSTEM == 'true')
			{
				echo '<a class="btn" href="'.os_href_link(FILENAME_GV_MAIL, os_get_all_get_params(array ('cID', 'action')).'cID='.$order->customer['ID']).'">'.BUTTON_SEND_COUPON.'</a>';
			}
			?>
			<a class="btn ajax-load-page" href="#" data-container="1" data-load-page="orders&o_id=<?php echo $_GET['oID']; ?>&action=edit_address" data-toggle="modal"><?php echo TEXT_EDIT_ADDRESS; ?></a>
			<a class="btn ajax-load-page" href="#" data-container="1" data-load-page="orders&o_id=<?php echo $_GET['oID']; ?>&action=edit_other" data-toggle="modal"><?php echo TEXT_EDIT_OTHER; ?></a>
			<a class="btn" href="<?php echo os_href_link(FILENAME_ORDERS, 'page='.$_GET['page'].'&oID='.$_GET['oID']); ?>"><?php echo BUTTON_BACK; ?></a>
		</div>

		<div class="btn-group pull-right" style="margin-right:5px;">
			<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-print"></i> <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<?php
				$array = array();
				$array['params'] = array('order_id' => $_GET['oID'], 'payment_method' => $order->info['payment_method']);
				$array = apply_filter('admin_print_menu', $array);

				if (is_array($array['link']) && !empty($array['link']))
				{
					foreach($array['link'] AS $link)
					{
						echo '<li><a href="Javascript:void()" onclick="window.open(\''.$link['href'].'\', \'popup\', \'toolbar=0, width=640, height=600\')">'.$link['name'].'</a></li>';
					}
				}
				?>
				<!--<li><a href="Javascript:void()" onclick="window.open('<?php echo os_href_link(FILENAME_PRINT_ORDER,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><?php echo BUTTON_INVOICE; ?></a></li>-->
				<li><a href="Javascript:void()" onclick="window.open('<?php echo os_href_link(FILENAME_PRINT_PACKINGSLIP,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><?php echo BUTTON_PACKINGSLIP; ?></a></li>
			</ul>
		</div>

		<div class="page-header nomargin-top">
			<h1><?php echo ENTRY_ORDER_NUMBER; ?> #<?php echo $oID; ?></h1>
		</div>

		<div class="row-fluid">
			<div class="span3">
				<ul class="default-ul font-small">
					<li><strong><?php echo TABLE_HEADING_DATE_PURCHASED; ?>:</strong> <?php echo $order->info['date_purchased']; ?></li>
					<?php if (isset($order->customer['telephone']) && !empty($order->customer['telephone'])) { ?>
					<li><strong><?php echo ENTRY_TELEPHONE; ?></strong> <?php echo $order->customer['telephone']; ?></li>
					<?php } ?>
					<li><strong><?php echo ENTRY_EMAIL_ADDRESS; ?></strong> <?php echo '<a href="mailto:'.$order->customer['email_address'].'">'.$order->customer['email_address'].'</a>'; ?></li>
					<?php if (isset($order->customer['vat_id']) && !empty($order->customer['vat_id'])) { ?>
					<li><strong><?php echo ENTRY_CUSTOMERS_VAT_ID; ?></strong> <?php echo $order->customer['vat_id']; ?></li>
					<?php } ?>
					<li><strong><?php echo IP; ?></strong> <?php echo isset($order->customer['cIP'])?$order->customer['cIP']:''; ?></li>
					<?php if (isset($order->customer['orig_reference']) && !empty($order->customer['orig_reference'])) { ?>
					<li><strong><?php echo ENTRY_ORIGINAL_REFERER; ?></strong> <?php echo $order->customer['orig_reference']; ?></li>
					<?php } ?>
					<li>
						<strong><?php echo CUSTOMERS_MEMO; ?></strong> 
						<?php
						if (isset($order->customer['ID']))
						{
							$memo_query = os_db_query("SELECT count(*) as count FROM ".TABLE_CUSTOMERS_MEMO." where customers_id='".$order->customer['ID']."'");
							$memo_count = os_db_fetch_array($memo_query);
						}
						else
							$memo_count = 0;
						?>
						<?php echo $memo_count['count'].'</strong>'; ?> <a style="cursor:pointer" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_POPUP_MEMO,'ID='.@$order->customer['ID']); ?>', 'popup', 'scrollbars=yes, width=500, height=500')">(<?php echo DISPLAY_MEMOS; ?>)</a>
					</li>
					<?php if ($order->customer['csID']!='') { ?>
					<li><strong><?php echo ENTRY_CID; ?></strong> <?php echo $order->customer['csID']; ?></li>
					<?php } ?>
					<?php echo os_get_extra_fields_order(isset($order->customer['ID'])?$order->customer['ID']:'', $_SESSION['languages_id']); ?>
				</ul>
			</div>

			<div class="span9 font-small">
				<div class="row-fluid">
					<div class="span4"><address><strong><?php echo ENTRY_SHIPPING_ADDRESS; ?></strong><br /><?php echo os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'); ?></address></div>
					<div class="span4"><address><strong><?php echo TEXT_EDIT_BILLING_ADDRESS; ?></strong><br /><?php echo os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'); ?></address></div>
					<div class="span4"><address><strong><?php echo ENTRY_BILLING_ADDRESS; ?></strong><br /><?php echo os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'); ?></address></div>
				</div>
			</div>
		</div>

		<div class="page-header">
			<h1><?php echo TEXT_ORDER_STATUS; ?></h1>
		</div>

		<table class="table table-condensed table-big-list nomargin">
			<thead>
				<tr>
					<th><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_COMMENTS; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
				</tr>
			</thead>
		<?php

		$orders_history_query = os_db_query("select orders_status_id, date_added, customer_notified, comments from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".os_db_input($oID)."' order by date_added DESC");
		if (os_db_num_rows($orders_history_query))
		{
			$i = 0;
			while ($orders_history = os_db_fetch_array($orders_history_query))
			{
				$i++;
				if ($orders_history['customer_notified'] == '1')
					$customer_notified = '<i class="icon-envelope" title="'.ICON_TICK.'"></i>';
				else
					$customer_notified = '<i class="icon-remove" title="'.ICON_CROSS.'"></i>';

				if($orders_history['orders_status_id']!='0')
					$orders_status = $orders_status_array[$orders_history['orders_status_id']];
				else
					$orders_status = '<font color="#FF0000">'.TEXT_VALIDATING.'</font>';

				$first = ($i == 1) ? ' class="warning"' : '';

				?>
				<tr<?php echo $first; ?>>
					<td><?php echo $customer_notified; ?></td>
					<td><?php echo $orders_status; ?></td>
					<td><?php echo nl2br(os_db_output($orders_history['comments'])); ?></td>
					<td><?php echo $orders_history['date_added']; ?></td>
				</tr>
				<?php
			}
		} else {
			echo '<tr><td colspan="4">'.TEXT_NO_ORDER_HISTORY.'</td></tr>';
		}
		?>
		</table>

		<div class="pt10"><button type="button" class="btn btn-mini btn-info" data-toggle="collapse" data-target="#change_status">Изменить статус</button></div>

		<div id="change_status" class="collapse">
			<div class="form-horizontal pt10">
				<?php echo os_draw_form('status', FILENAME_ORDERS, os_get_all_get_params(array('action')).'action=update_order'); ?>
					<div class="control-group">
						<label class="control-label" for="comments"><?php echo TABLE_HEADING_COMMENTS; ?></label>
						<div class="controls">
							<?php echo $cartet->html->textarea('comments', '', array('id' => 'comments', 'class' => 'span6', 'rows' => '3')); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo ENTRY_STATUS; ?></label>
						<div class="controls">
							<?php echo os_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo os_draw_checkbox_field('notify', '', true); ?>
								<?php echo ENTRY_NOTIFY_CUSTOMER; ?>
							</label>
							<label class="checkbox">
								<?php echo os_draw_checkbox_field('notify_comments', '', true); ?>
								<?php echo ENTRY_NOTIFY_COMMENTS; ?>
							</label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""></label>
						<div class="controls">
							<input class="btn" type="submit" value="<?php echo BUTTON_UPDATE; ?>" />
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="page-header">
			<h1><?php echo TEXT_ORDER_PRODUCTS; ?></h1>
		</div>
		<table class="table table-condensed table-big-list nomargin">
			<thead>
				<tr>
					<th class="tcenter"><?php echo TABLE_HEADING_QUANTITY_SHORT; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_PRODUCTS; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_PRODUCTS_SHIPPING_TIME; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></th>
					<?php if (isset($order->products[0]['allow_tax']) && $order->products[0]['allow_tax'] == 1) { ?>
						<th><span class="line"></span><?php echo TABLE_HEADING_TAX; ?> (%)</th>
						<th><span class="line"></span><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></th>
					<?php } ?>
					<th class="tright"><span class="line"></span>
					<?php
						echo TABLE_HEADING_TOTAL_INCLUDING_TAX;
						if (isset($i) && isset($order->products[$i]) && $order->products[$i]['allow_tax'] == 1)
						{
							echo ' (excl.)';
						}
					?>
					</th>
					<th><span class="line"></span></th>
				</tr>
			</thead>

			<?php foreach($cartet->orders->getProducts($_GET['oID']) AS $product) { ?>
			<tr>
				<td class="tcenter"><a href="#" class="ajax_editable" data-action="order_quickChangeProduct" data-name="products_quantity" data-pk="<?php echo $product['orders_products_id']; ?>" data-type="text"><?php echo $product['products_quantity']; ?></a></td>
				<td>
					<a href="<?php echo os_href_link(FILENAME_CATEGORIES, 'pID='.$product['products_id'].'&action=new_product'); ?>" target="_blank"><?php echo $product['products_name']; ?></a>
					<?php
					//Bundle
					$products_bundle = '';
					if ($product['bundle'] == 1)
					{
						$bundle_query = getBundleProducts($product['products_id']);

						if (os_db_num_rows($bundle_query) > 0)
						{
							while($bundle_data = os_db_fetch_array($bundle_query))
							{
								$products_bundle_data .= '- <a href="'.os_href_link(FILENAME_CATEGORIES, 'pID='.$bundle_data['products_id'].'&action=new_product').'">'.$bundle_data['products_name'].' ('.TEXT_QTY.$bundle_data['products_quantity'].TEXT_UNITS.')</a><br />';
							}
						}
						$products_bundle = (!empty($products_bundle_data)) ? '<div class="bundles-products-block">'.$products_bundle_data.'</div>' : '';
					}
					echo $products_bundle;
					?>
				</td>
				<td><a href="#" class="ajax_editable" data-action="order_quickChangeProduct" data-name="products_model" data-pk="<?php echo $product['orders_products_id']; ?>" data-type="text"><?php echo $product['products_model']; ?></a></td>
				<td><a href="#" class="ajax_editable" data-action="order_quickChangeProduct" data-name="products_shipping_time" data-pk="<?php echo $product['orders_products_id']; ?>" data-type="text"><?php echo $product['products_shipping_time']; ?></a></td>
				<td><?php echo format_price($product['final_price'] / $product['products_quantity'], 1, $order->info['currency'], isset($product['allow_tax']) ? $product['allow_tax'] : '', $product['products_tax']); ?></td>
				<?php if (isset($product['allow_tax']) && $product['allow_tax'] == 1) { ?>
					<td><a href="#" class="ajax_editable" data-action="order_quickChangeProduct" data-name="products_tax" data-pk="<?php echo $product['orders_products_id']; ?>" data-type="text"><?php echo $product['products_tax']; ?></a></td>
					<td><a href="#" class="ajax_editable" data-action="order_quickChangeProduct" data-name="products_price" data-pk="<?php echo $product['orders_products_id']; ?>" data-type="text"><?php echo $product['products_price']; ?></a></td>
				<?php } ?>
				<td class="tright"><a href="#" class="ajax_editable" data-action="order_quickChangeProduct" data-name="final_price" data-pk="<?php echo $product['orders_products_id']; ?>" data-type="text"><?php echo $product['final_price'] ; ?></a></td>
				<td class="width80px">
					<div class="btn-group pull-right">	
						<a class="btn btn-mini ajax-load-page" href="#" data-container="1" data-load-page="orders&o_id=<?php echo $_GET['oID']; ?>&p_id=<?php echo $product['products_id']; ?>&op_id=<?php echo $product['orders_products_id']; ?>&action=edit_attributes" data-toggle="modal"><i class="icon-tasks"></i></a>
						<a class="btn btn-mini" href="#" data-action="order_deleteProduct" data-remove-parent="tr" data-id="<?php echo $product['orders_products_id']; ?>" data-confirm="Вы уверены, что хотите удалить этот товар?"><i class="icon-trash"></i></a>
					</div>
				</td>
			</tr>
			<?php if (is_array($product['attributes']) && !empty($product['attributes'])) { ?>
			<tr>
				<td colspan="9">
					<div class="table-big-text">
					<?php foreach($product['attributes'] AS $attributes) { ?>
					<span class="label label-gray">
						<a href="#" data-action="order_deleteAttributes" data-remove-parent="span" data-id="<?php echo $attributes['orders_products_attributes_id']; ?>" data-confirm="Вы уверены, что хотите удалить этот атрибут?"><i class="icon-remove"></i></a> 
						<?php echo $attributes['products_options']; ?>: <?php echo $attributes['products_options_values']; ?> | 
						<?php echo $osPrice->Format($attributes['options_values_price'], true); ?>
						<?php echo ($attributes['attributes_model']) ? '('.$attributes['attributes_model'].')' : ''; ?>
					</span>
					<?php } ?>
					</div>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</table>

		<div class="pt10"><button type="button" class="btn btn-mini btn-info" data-toggle="collapse" data-target="#add_products">Добавить товар</button></div>

		<div id="add_products" class="collapse">
			<?php
			//if ($_POST['new_product'])
			//{
				//$newProducts = array();
				//foreach($_POST['new_product'] as $k => $v)
				//	foreach($v as $item => $value)
				//		$newProducts[$item][$k] = $value;

					//_print_r($newProducts);
			//}
			?>
			<form method="post" action="" class="pt10">

			<div class="row-fluid">
				<div class="span6"><input type="text" name="related" id="add_product" class="input_autocomplete" /></div>
				<div class="span6 tright"><input class="btn" type="submit" value="Добавить товары" /></div>
			</div>

			<div id="products_app" class="mb20px"></div>

			</form>

			<table class="table table-condensed table-big-list nomargin" id="new_product" style='display:none; text-align:left;'>
				<tr>
					<td><a class="product_name" href="" target="_blank"></a></td>
					<td width="150"><span class="products_model"></span></td>
					<td width="60">
						<input type="hidden" name="new_product[products_id][]" value="" />
						<input type="hidden" name="new_product[orders_id][]" value="<?php echo $oID; ?>" />
						<input type="hidden" name="new_product[allow_tax][]" value="<?php echo $order->products[0]['allow_tax']; ?>" />
						<input type="hidden" name="new_product[products_model][]" value="" />
						<input type="hidden" name="new_product[products_name][]" value="" />
						<input class="width40px tcenter" type="number" name="new_product[product_qty][]" value="1" />
					</td>
					<td width="120"><input class="width100px" type="text" name="new_product[products_price][]" value="" /></td>
					<td class="width40px tright"><a class="del_new_product" href="javascript:;"><i class="icon-trash"></i></a></td>
				</tr>
			</table>
		</div>

		<div class="page-header">
			<h1><?php echo EMAIL_TEXT_INVOICE_URL; ?></h1>
		</div>

		<div class="row-fluid">
			<div class="span6">
				<h5><?php echo TEXT_ORDER_PAYMENT; ?></h5>
				<table class="table table-condensed table-big-list nomargin">
					<tr>
						<td class="width130px"><strong><?php echo ENTRY_LANGUAGE; ?></strong></td>
						<td><?php echo isset($order->info['language'])?$order->info['language']:''; ?></td>
					</tr>
					<tr>
						<td><strong><?php echo ENTRY_PAYMENT_METHOD; ?></strong></td>
						<td><?php echo $order_payment_text; ?></td>
					</tr>
					<?php if (isset($order->info['shipping_class']) && $order->info['shipping_class'] != '') { ?>          
					<tr>
						<td><strong><?php echo ENTRY_SHIPPING_METHOD; ?></strong></td>
						<td><?php echo $order_shipping_text; ?></td>
					</tr>
					<?php } ?>
				</table>
			</div>
			<div class="span6">
				<h5>Итого</h5>
				<table class="table table-condensed table-big-list nomargin">
				<?php foreach ($order->totals as $total) { ?>
					<tr>
						<td class="tright"><?php echo $total['title']; ?></td>
						<td class="tright width130px"><?php echo $total['text']; ?></td>
					</tr>
				<?php } ?>
				</table>
			</div>
		</div>
	</div>     

<?php } else { ?>

	<div class="second-page-nav">

		<div class="row-fluid">
			<div class="span6">
				<?php echo os_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
					<fieldset>
						<?php echo os_draw_input_field('oID', '', 'placeholder="'.HEADING_TITLE_SEARCH.'…"').os_draw_hidden_field('action', 'edit').os_draw_hidden_field(os_session_name(), os_session_id()); ?>
					</fieldset>
				</form>
			</div>
			<div class="span6">
				<div class="pull-right">
					<?php echo os_draw_form('goto', FILENAME_ORDERS, '', 'get'); ?>
						<fieldset>
							<?php echo os_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)),array(array('id' => '0', 'text' => TEXT_VALIDATING)), $orders_statuses), '', 'onChange="this.form.submit();"').os_draw_hidden_field(os_session_name(), os_session_id()); ?>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php echo os_draw_form('multi_action_form', FILENAME_ORDERS,os_get_all_get_params()); ?>
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th class="tcenter"><input type="checkbox" class="selectAllCheckbox" onClick="javascript:SwitchCheck();"></th>
					<th class="tcenter"><span class="line"></span>#</th>
					<th><span class="line"></span><?php echo TABLE_HEADING_CUSTOMER; ?></th>
					<th class="tcenter"><span class="line"></span><?php echo TEXT_ORDER_PRODUCTS; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_ORDER_TOTAL; ?></th>
					<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_DATE_PURCHASED; ?></th>
					<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
					<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
				</tr>
			</thead>
		<?php

		if (isset($_GET['cID'])) 
		{
			$cID = os_db_prepare_input($_GET['cID']);
			$orders_query_raw = "select *, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.customers_id = '".os_db_input($cID)."' and (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by o.orders_id DESC";
		}
		elseif (isset($_GET['status']) && $_GET['status']=='0')
		{
			$orders_query_raw = "select *, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id) where o.orders_status = '0' and ot.class = 'ot_total' order by o.orders_id DESC";
		}
		elseif (isset($_GET['status']))
		{
			$status = os_db_prepare_input($_GET['status']);
			$orders_query_raw = "select *, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and s.orders_status_id = '".os_db_input($status)."' and ot.class = 'ot_total' order by o.orders_id DESC";
		}
		else
		{
			//$orders_query_raw = "select o.orders_id, o.orders_status, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by o.orders_id DESC";
			$orders_query_raw = "select *, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by o.orders_id DESC";
		}

		$orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $orders_query_raw, $orders_query_numrows);
		$orders_query = os_db_query($orders_query_raw);

		// Формируем два массива. 1 - данные заказов, 2 - id заказов
		$ordersData = array();
		$ordersIds = array();
		while ($orders = os_db_fetch_array($orders_query))
		{
			$ordersData[$orders['orders_id']] = $orders;
			$ordersIds[] = $orders['orders_id'];
		}

		// Получаем товары заказов
		if (!empty($ordersIds))
		{
			$ordersProducts = $cartet->orders->getProducts(array('orders_id' => $ordersIds));
		}

		foreach($ordersData AS $orderId => $orders)
		{
			$products = $ordersProducts[$orderId];
			$productsCount = count($products);

			if (((!isset($_GET['oID'])) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo)))
			{
				$oInfo = new objectInfo($orders);
			}
		?>
		<tr class="item_selected_<?php echo $orders['orders_id']; ?>">
			<td class="tcenter"><input type="checkbox" name="multi_orders[]" value="<?php echo $orders['orders_id'];?>"></td>
			<td class="tcenter"><?php echo $orders['orders_id']; ?></td>
			<td><?php echo $orders['customers_name']; ?></td>
			<td class="tcenter"><?php echo $productsCount;?></a></td>
			<td><?php echo strip_tags($orders['order_total']); ?></td>
			<td class="tcenter"><?php echo $orders['date_purchased']; ?></td>
			<td class="tcenter"><?php if($orders['orders_status']!='0') { echo $orders['orders_status_name']; }else{ echo '<font color="#FF0000">'.TEXT_VALIDATING.'</font>';}?></td>
			<td>
				<div class="pull-right">
					<div class="btn-group">
						<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><i class="icon-print"></i> <span class="caret"></span></button>
						<ul class="dropdown-menu">
							<?php
							$array = array();
							$array['params'] = array('order_id' => $orders['orders_id'], 'payment_method' => $orders['payment_method']);
							$array = apply_filter('admin_print_menu', $array);

							if (is_array($array['link']) && !empty($array['link']))
							{
								foreach($array['link'] AS $link)
								{
									echo '<li><a href="Javascript:void()" onclick="window.open(\''.$link['href'].'\', \'popup\', \'toolbar=0, width=640, height=600\')">'.$link['name'].'</a></li>';
								}
							}
							?>
							<!--<li><a href="<?php echo os_href_link(FILENAME_PRINT_ORDER,'oID='.$orders['orders_id']); ?>" target="_blank"><?php echo BUTTON_INVOICE; ?></a></li>-->
							<li><a href="<?php echo os_href_link(FILENAME_PRINT_PACKINGSLIP,'oID='.$orders['orders_id']); ?>" target="_blank"><?php echo BUTTON_PACKINGSLIP; ?></a></li>
						</ul>
					</div>
					<div class="btn-group">
						<a class="btn btn-mini show_or_hide" data-toggle="button" data-item="<?php echo $orders['orders_id']; ?>" href="#"><i class="icon-info-sign"></i></a>
						<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('oID', 'action')).'oID='.$orders['orders_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-pencil"></i></a>
						<a class="btn btn-mini ajax-load-page" href="<?php echo os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('oID', 'action')).'oID='.$orders['orders_id'].'&action=delete'); ?>" data-load-page="orders&o_id=<?php echo $orders['orders_id']; ?>&action=delete" data-toggle="modal" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
					</div>
				</div>
			</td>
		</tr>
		<tr class="display-none item_<?php echo $orders['orders_id']; ?>">
			<td colspan="8">
				<div class="table-big-text">
					<div class="row-fluid order-products-list-item">
						<div class="span6">
						<?php
							if (is_array($products) && !empty($products))
							{
								echo '<div class="order-products-list">';
									foreach ($products as $pId => $product) {//icon-th
									?>
									<div class="row-fluid order-products-list-item">
										<div class="span8 border-right"><div class="text-nowrap"><a href="<?php echo FILENAME_CATEGORIES; ?>?pID=<?php echo $product['products_id']; ?>&action=new_product" target="_blank"><?php echo $product['products_name']; ?></a></div></div>
										<div class="span1 border-right"><?php echo $product['products_quantity']; ?> x</div>
										<div class="span3 tright"><?php echo number_format($product['final_price'], 0); ?></div>
									</div>
									<?php
									}
								echo '</div>';
							}
						?>
						</div>
						<div class="span3">
							<span class="bold"><?php echo TEXT_INFO_SHIPPING_METHOD; ?></span><br /><?php echo $orders['shipping_method']; ?><br />
						</div>
						<div class="span3">
							<?php
							if (!empty($orders['payment_method']) && is_file(_MODULES.'payment/'.$orders['payment_method'].'/'.$_SESSION['language_admin'].'.php'))  
							{
								require(_MODULES.'payment/'.$orders['payment_method'].'/'.$_SESSION['language_admin'].'.php');
								$order_payment_text = @constant(MODULE_PAYMENT_.strtoupper($orders['payment_method'])._TEXT_TITLE);
							}
							else
								$order_payment_text = TEXT_NO;
							?>
							<span class="bold"><?php echo TEXT_INFO_PAYMENT_METHOD; ?></span><br /><?php echo $order_payment_text; ?>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<?php } ?>
		</table>

		<div class="action-table">
			<div class="pull-left">
				<?php echo os_draw_pull_down_menu('new_status', array_merge(array(array('id' => '', 'text' => BUS_TEXT_NEW_STATUS)), $orders_statuses), '', ''); ?>
				<input class="btn" type="submit" name="submit" value="<?php echo BUTTON_SUBMIT; ?>" />
				<div class="pt10">
					<label class="checkbox inline"><?php echo os_draw_checkbox_field('delete_orders','1'); ?> <?php echo BUS_DELETE_ORDERS; ?></label> 
					<label class="checkbox inline"><?php echo os_draw_checkbox_field('notify','1',true); ?> <?php echo BUS_NOTIFY_CUSTOMERS; ?></label>
				</div>
			</div>

			<div class="pull-right">
				<div class="pagination pagination-mini pagination-right">
					<ul>
						<?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?>
						<?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], os_get_all_get_params(array('page', 'oID', 'action'))); ?>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
		</div>

	</form>

<?php } ?>

<?php $main->bottom(); ?>