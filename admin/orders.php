<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

require ('includes/top.php');
$osTemplate = new osTemplate;
require_once(_LIB.'phpmailer/class.phpmailer.php');
require (_CLASS_ADMIN.'currencies.php');
$currencies = new currencies();

if (isset($_GET['action']))
{
if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order')) && ($_GET['oID'])) {
	$oID = os_db_prepare_input($_GET['oID']);

	$orders_query = os_db_query("select orders_id from ".TABLE_ORDERS." where orders_id = '".os_db_input($oID)."'");
	$order_exists = true;
	if (!os_db_num_rows($orders_query)) {
		$order_exists = false;
		$messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
	}
}
}

require (get_path('class_admin').'order.php');

if (isset($_GET['action']))
{
if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order')) && ($order_exists)) {
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

      $shipping_method_query = os_db_query("select title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . os_db_input($oID) . "' and class = 'ot_shipping'");
      $shipping_method = os_db_fetch_array($shipping_method_query);

  $order_shipping_text = ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title']));
  

}
}
else
{
   $order_shipping_text = '';
}

  if (isset($order->info['language']))
  {
     $lang_query = os_db_query("select languages_id from " . TABLE_LANGUAGES . " where directory = '" . $order->info['language'] . "'");
     $lang = os_db_fetch_array($lang_query);
     $lang=$lang['languages_id'];
  }
  else
  {
     $lang=$_SESSION['languages_id'];
  }
$orders_statuses = array ();
$orders_status_array = array ();
$orders_status_query = os_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$lang."'");
while ($orders_status = os_db_fetch_array($orders_status_query)) {
	$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

if (isset($_POST['submit']) && isset($_POST['multi_orders'])){
 if (($_POST['submit'] == BUTTON_SUBMIT)&&(isset($_POST['new_status']))&&(!isset($_POST['delete_orders']))){
  $status = os_db_prepare_input($_POST['new_status']);
  $comments = os_db_prepare_input($_POST['comments']);
  if ($status == '') {     os_redirect(os_href_link(FILENAME_ORDERS),os_get_all_get_params());
  }
  foreach ($_POST['multi_orders'] as $this_orderID){
    $order_updated = false;
    $check_status_query = os_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . (int)$this_orderID . "'");
    $check_status = os_db_fetch_array($check_status_query);

    if ($check_status['orders_status'] != $status) {
       os_db_query("update " . TABLE_ORDERS . " set orders_status = '" . os_db_input($status) . "', last_modified = now() where orders_id = '" . (int)$this_orderID . "'");
       $customer_notified ='0'; 
          if (isset($_POST['notify'])) {
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
           $billing_subject = str_replace('{$nr}', $this_orderID, EMAIL_BILLING_SUBJECT);

            $customer_notified = '1';
          }
          os_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$this_orderID . "', '" . os_db_input($status) . "', now(), '" . os_db_input($customer_notified) . "', '" . os_db_input($comments)  . "')");
          $order_updated = true;

		  // изменение статуса заказа
		  do_action('change_order_status');

        $changed = false;
       
        $check_group_query = os_db_query("select customers_status_id from " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " where orders_status_id = " . $status);
        if (os_db_num_rows($check_group_query)) {
           while ($groups = os_db_fetch_array($check_group_query)) {
              $customer_query = os_db_query("select c.* from " . TABLE_CUSTOMERS . " as c, " . TABLE_ORDERS . " as o where o.customers_id = c.customers_id and o.orders_id = " . (int)$this_orderID );
              $customer = os_db_fetch_array($customer_query);
			  unset($customer_id1);
			     if ($customer['customers_status'] == '0') {
              $customer_id1 = 0;
              } else {
              $customer_id1 = $customer['customers_id'];
              }
              $statuses_groups_query = os_db_query("select orders_status_id from " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " where customers_status_id = " . $groups['customers_status_id']);
              $purchase_query = "select sum(ot.value) as total from " . TABLE_ORDERS_TOTAL . " as ot, " . TABLE_ORDERS . " as o where ot.orders_id = o.orders_id and o.customers_id = " . $customer_id1 . " and ot.class = 'ot_total' and (";
              $statuses = os_db_fetch_array($statuses_groups_query);
              $purchase_query .= " o.orders_status = " . $statuses['orders_status_id'];
              while ($statuses = os_db_fetch_array($statuses_groups_query)) {
                  $purchase_query .= " or o.orders_status = " . $statuses['orders_status_id'];
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
			  from " . TABLE_CUSTOMERS_STATUS . " as cg,
			  " . TABLE_CUSTOMERS . " as c
			  where cg.customers_status_id = c.customers_status
			  and c.customers_id = " .$customer_id1);
              $current_limit = @mysql_result($acc_query, 0, "customers_status_accumulated_limit");
			  if (empty($current_limit)) $current_limit = 0;
              $current_discount = @mysql_result($acc_query, 0, "customers_status_discount");
              $current_group = @mysql_result($acc_query, 0, "customers_status_name");
               if (empty($current_discount)) $current_discount = 0;                                                                                                                                                                                                

              $groups_query = os_db_query("select customers_status_discount, customers_status_id, customers_status_name, customers_status_accumulated_limit from " . TABLE_CUSTOMERS_STATUS . " where customers_status_accumulated_limit < " . $customers_total . " and customers_status_discount >= " . $current_discount . " and customers_status_accumulated_limit >= " . $current_limit . " and customers_status_id = " . $groups['customers_status_id'] . " order by customers_status_accumulated_limit DESC");

              if (os_db_num_rows($groups_query)) {
                 $customers_groups_id = @mysql_result($groups_query, 0, "customers_status_id");
                 $customers_groups_name = @mysql_result($groups_query, 0, "customers_status_name");
                 $limit = @mysql_result($groups_query, 0, "customers_status_accumulated_limit");
				 if (empty($limit)) $limit = 0;
                 $current_discount = @mysql_result($groups_query, 0, "customers_status_discount");
                 if (empty($current_discount)) $current_discount = 0;
				 
                 os_db_query("update " . TABLE_CUSTOMERS . " set customers_status = " . $customers_groups_id . " where customers_id = " .$customer_id1);

                 $changed = true;
             }
           }

           $groups_query = os_db_query("select cg.* from " . TABLE_CUSTOMERS_STATUS . " as cg, " . TABLE_CUSTOMERS . " as c where c.customers_status = cg.customers_status_id and c.customers_id = " .$customer_id1);
           $customers_groups_id = @mysql_result($groups_query, 0, "customers_status_id");
           $customers_groups_name = @mysql_result($groups_query, 0, "customers_status_name");
           $limit = @mysql_result($groups_query, 0, "customers_status_accumulated_limit");
		   if (empty($limit)) $limit = 0;
           $current_discount = @mysql_result($groups_query, 0, "customers_status_discount");
		   if (empty($current_discount)) $current_discount = 0;
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

				$html_mail_admin = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_admin.html');
				$txt_mail_admin = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_admin.txt');

				os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_ACC_SUBJECT, $html_mail_admin, $txt_mail_admin);

				$html_mail_customer = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_customer.html');
				$txt_mail_customer = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/accumulated_discount_customer.txt');

				os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_ACC_SUBJECT, $html_mail_customer, $txt_mail_customer);

           }
        }

    }
        if ($order_updated == true) {
         $messageStack->add_session(BUS_ORDER . $this_orderID . ' ' . BUS_SUCCESS, 'success');
        } else {
          $messageStack->add_session(BUS_ORDER . $this_orderID . ' ' . BUS_WARNING, 'warning');
        }
  } 
 }
 
 
 if (($_POST['submit'] == BUTTON_SUBMIT)&&(isset($_POST['delete_orders']))){ 

  foreach ($_POST['multi_orders'] as $this_orderID){

    $orders_deleted = false;

		  os_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . (int)$this_orderID . "'");
		  os_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$this_orderID . "'");
		  os_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$this_orderID . "'");
		  os_db_query("delete from " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " where orders_id = '" . (int)$this_orderID . "'");
		  os_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . (int)$this_orderID . "'");
		  os_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$this_orderID . "'");

          $orders_deleted = true;

        if ($orders_deleted == true) {
         $messageStack->add_session(BUS_ORDER . $this_orderID . ' ' . BUS_DELETE_SUCCESS, 'success');
        } else {
          $messageStack->add_session(BUS_ORDER . $this_orderID . ' ' . BUS_DELETE_WARNING, 'warning');
        }
  } 
 }


   os_redirect(os_href_link(FILENAME_ORDERS),os_get_all_get_params());
}

switch (@$_GET['action']) {
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
			if (isset($_POST['notify']) && $_POST['notify'] == 'on') {
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
        
        $check_group_query = os_db_query("select customers_status_id from " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " where orders_status_id = " . $status);
        if (os_db_num_rows($check_group_query)) {
           while ($groups = os_db_fetch_array($check_group_query)) {

              $customer_query = os_db_query("select c.* from " . TABLE_CUSTOMERS . " as c, " . TABLE_ORDERS . " as o where o.customers_id = c.customers_id and o.orders_id = " . (int)$oID);
              $customer = os_db_fetch_array($customer_query);
			     if ($customer['customers_status'] == '0') {
              $customer_id = 0;
              } else {
              $customer_id = $customer['customers_id'];
              }
              $statuses_groups_query = os_db_query("select orders_status_id from " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " where customers_status_id = " . $groups['customers_status_id']);
              $purchase_query = "select sum(ot.value) as total from " . TABLE_ORDERS_TOTAL . " as ot, " . TABLE_ORDERS . " as o where ot.orders_id = o.orders_id and o.customers_id = " . $customer_id . " and ot.class = 'ot_total' and (";
              $statuses = os_db_fetch_array($statuses_groups_query);
              $purchase_query .= " o.orders_status = " . $statuses['orders_status_id'];
              while ($statuses = os_db_fetch_array($statuses_groups_query)) {
                  $purchase_query .= " or o.orders_status = " . $statuses['orders_status_id'];
              }
              $purchase_query .=");";
                   
              $total_purchase_query = os_db_query($purchase_query);
              $total_purchase = os_db_fetch_array($total_purchase_query);
              $customers_total = $total_purchase['total'];

              if (empty($customers_total)) $customers_total = 0;
              $acc_query = os_db_query("select cg.customers_status_accumulated_limit, cg.customers_status_name, cg.customers_status_discount from " . TABLE_CUSTOMERS_STATUS . " as cg, " . TABLE_CUSTOMERS . " as c where cg.customers_status_id = c.customers_status and c.customers_id = " . $customer_id);
              $current_limit = @mysql_result($acc_query, 0, "customers_status_accumulated_limit");
			  if (empty($current_limit)) $current_limit = 0;
              $current_discount = @mysql_result($acc_query, 0, "customers_status_discount");
			   if (empty($current_discount)) $current_discount = 0;
              $current_group = @mysql_result($acc_query, "customers_status_name");
			     if ($customer['customers_status'] > '0') {
                                                                                                                                                                                                 
              // ok, looking for available group
              $groups_query = os_db_query("select customers_status_discount, customers_status_id, customers_status_name, customers_status_accumulated_limit from " . TABLE_CUSTOMERS_STATUS . " where customers_status_accumulated_limit < " . $customers_total . " and customers_status_discount > " . $current_discount . " and customers_status_accumulated_limit >= " . $current_limit . " and customers_status_id = " . $groups['customers_status_id'] . " order by customers_status_accumulated_limit DESC");

              }
              if (os_db_num_rows($groups_query)) {
                 $customers_groups_id = @mysql_result($groups_query, 0, "customers_status_id");
                 $customers_groups_name = @mysql_result($groups_query, 0, "customers_status_name");
                 $limit = @mysql_result($groups_query, 0, "customers_status_accumulated_limit");
				 if (empty($limit)) $limit = 0;
                 $current_discount = @mysql_result($groups_query, 0, "customers_status_discount");
                 if (empty($current_discount)) $current_discount = 0;
                 os_db_query("update " . TABLE_CUSTOMERS . " set customers_status = " . $customers_groups_id . " where customers_id = " . $customer_id);
                 $changed = true;
             }
           }
           $groups_query = os_db_query("select cg.* from " . TABLE_CUSTOMERS_STATUS . " as cg, " . TABLE_CUSTOMERS . " as c where c.customers_status = cg.customers_status_id and c.customers_id = " . $customer_id);
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
	case 'deleteconfirm' :
		$oID = os_db_prepare_input($_GET['oID']);

		os_remove_order($oID, $_POST['restock']);

		os_redirect(os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('oID', 'action'))));
		break;
	case 'deleteccinfo' :
		$oID = os_db_prepare_input($_GET['oID']);

		os_db_query("update ".TABLE_ORDERS." set cc_cvv = null where orders_id = '".os_db_input($oID)."'");
		os_db_query("update ".TABLE_ORDERS." set cc_number = '0000000000000000' where orders_id = '".os_db_input($oID)."'");
		os_db_query("update ".TABLE_ORDERS." set cc_expires = null where orders_id = '".os_db_input($oID)."'");
		os_db_query("update ".TABLE_ORDERS." set cc_start = null where orders_id = '".os_db_input($oID)."'");
		os_db_query("update ".TABLE_ORDERS." set cc_issue = null where orders_id = '".os_db_input($oID)."'");

		os_redirect(os_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));
		break;
}


add_action ('head_admin', 'head_orders');

function head_orders()
{
   _e('<script type="text/javascript" src="includes/javascript/categories.js"></script>');
   _e('<script type="text/javascript" src="includes/javascript/tabber.js"></script>');
   _e('<link rel="stylesheet" href="includes/javascript/tabber.css" TYPE="text/css" MEDIA="screen">');
   _e('<link rel="stylesheet" href="includes/javascript/tabber-print.css" TYPE="text/css" MEDIA="print">');
}
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
   <?php $main->heading('portfolio_package.gif', HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php

if (isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists)) {

?>
      <tr>
      <td width="100%">
 <?php echo '<a class="button" href="' . os_href_link(FILENAME_ORDERS, os_get_all_get_params(array('action'))) . '"><span>' . BUTTON_BACK . '</span></a>'; ?>

   <a class="button" href="<?php echo os_href_link(FILENAME_ORDERS_EDIT, 'oID='.$_GET['oID'].'&cID=' . @$order->customer['ID']);?>"><span><?php echo BUTTON_EDIT ?></span></a>
 </td>

      </tr>
</table>

<div class="tabber">
        <div class="tabbertab">
        <h3><?php echo TEXT_ORDER_SUMMARY; ?></h3>
          <table border="0">
          <tr>
            <td valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_CUSTOMER; ?></b></td>
                <td class="main"><?php echo os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'); ?></td>
              </tr>
            </table></td>
            <td valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_SHIPPING_ADDRESS; ?></b></td>
                <td class="main"><?php echo os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'); ?></td>
              </tr>
            </table></td>
            <td valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_BILLING_ADDRESS; ?></b></td>
                <td class="main"><?php echo os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'); ?></td>
              </tr>
            </table></td>
          </tr>
          
          <tr>
          <td colspan="3">
          <table width="100%" border="0" cellspacing="2" cellpadding="2">

              <tr>
                <td class="main"><b><?php echo TABLE_HEADING_DATE_PURCHASED; ?>:</b></td>
                <td class="main"><?php echo os_date_long($order->info['date_purchased']); ?></td>
              </tr>
	          <tr>
	            <td class="main"><b><?php echo ENTRY_ORDER_NUMBER; ?></b></td>
	            <td class="main"><?php echo $oID; ?></td>
	          </tr>
                    
          <tr>
            <?php if ($order->customer['csID']!='') { ?>
                <tr>
                <td class="main" valign="top" bgcolor="#FFCC33"><b><?php echo ENTRY_CID; ?></b></td>
                <td class="main" bgcolor="#FFCC33"><?php echo $order->customer['csID']; ?></td>
              </tr>
            <?php } ?>
              <tr>
                <td class="main" valign="top"><b><?php echo CUSTOMERS_MEMO; ?></b></td>
<?php
    if (isset($order->customer['ID']))
	{
	   $memo_query = os_db_query("SELECT count(*) as count FROM ".TABLE_CUSTOMERS_MEMO." where customers_id='".$order->customer['ID']."'");
	   $memo_count = os_db_fetch_array($memo_query);
	}
	else
	{
	   $memo_count = 0;
	}
?>
                <td class="main"><b><?php echo $memo_count['count'].'</b>'; ?>  <a style="cursor:pointer" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_POPUP_MEMO,'ID='.@$order->customer['ID']); ?>', 'popup', 'scrollbars=yes, width=500, height=500')">(<?php echo DISPLAY_MEMOS; ?>)</a></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_TELEPHONE; ?></b></td>
                <td class="main"><?php echo $order->customer['telephone']; ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                <td class="main"><?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_CUSTOMERS_VAT_ID; ?></b></td>
                <td class="main"><?php echo isset($order->customer['vat_id'])?$order->customer['vat_id']:''; ?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo IP; ?></b></td>
                <td class="main"><b><?php echo isset($order->customer['cIP'])?$order->customer['cIP']:''; ?></b></td>
              </tr>
	          <tr>
	            <td class="main"><b><?php echo ENTRY_ORIGINAL_REFERER; ?></b></td>
	            <td class="main"><?php echo isset($order->customer['orig_reference'])?$order->customer['orig_reference']:''; ?></td>
	          </tr>

              <?php echo os_get_extra_fields_order(isset($order->customer['ID'])?$order->customer['ID']:'', $_SESSION['languages_id']); ?>

             </table>   
             </td>       
             </tr>

</table>

</div>

        <div class="tabbertab">
        <h3><?php echo TEXT_ORDER_PAYMENT; ?></h3>

          <table border="0">

      <tr>
        <td><table border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td class="main"><b><?php echo ENTRY_LANGUAGE; ?></b></td>
            <td class="main"><?php echo isset($order->info['language'])?$order->info['language']:''; ?></td>
          </tr>
          <tr>
            <td class="main"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
            <td class="main"><?php echo $order_payment_text; ?></td>
          </tr>
<?php if (isset($order->info['shipping_class']) && $order->info['shipping_class'] != '') { ?>          
          <tr>
            <td class="main"><b><?php echo ENTRY_SHIPPING_METHOD; ?></b></td>
            <td class="main"><?php echo $order_shipping_text; ?></td>
          </tr>
<?php } ?>          
<?php

	if ((($order->info['cc_type']) || ($order->info['cc_owner']) || ($order->info['cc_number']))) {
?>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
            <td class="main"><?php echo $order->info['cc_type']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
            <td class="main"><?php echo $order->info['cc_owner']; ?></td>
          </tr>
<?php

		if ($order->info['cc_number'] != '0000000000000000') {
			if (strtolower(CC_ENC) == 'true') {
				$cipher_data = $order->info['cc_number'];
				$order->info['cc_number'] = changedataout($cipher_data, CC_KEYCHAIN);
			}
		}
?>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
            <td class="main"><?php echo $order->info['cc_number']; ?></td>
          </tr>
          <tr>
          <td class="main"><?php echo ENTRY_CREDIT_CARD_CVV; ?></td>
          <td class="main"><?php echo $order->info['cc_cvv']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
            <td class="main"><?php echo $order->info['cc_expires']; ?></td>
          </tr>
<?php

	}

	$banktransfer_query = os_db_query("select banktransfer_prz, banktransfer_status, banktransfer_owner, banktransfer_number, banktransfer_bankname, banktransfer_blz, banktransfer_fax from " . TABLE_BANKTRANSFER . " where orders_id = '".os_db_input($_GET['oID'])."'");
	$banktransfer = os_db_fetch_array($banktransfer_query);
	if (($banktransfer['banktransfer_bankname']) || ($banktransfer['banktransfer_blz']) || ($banktransfer['banktransfer_number'])) {
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_NAME; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_bankname']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_BLZ; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_blz']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_NUMBER; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_number']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_OWNER; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_owner']; ?></td>
          </tr>
<?php

		if ($banktransfer['banktransfer_status'] == 0) {
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_STATUS; ?></td>
            <td class="main"><?php echo "OK"; ?></td>
          </tr>
<?php

		} else {
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_STATUS; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_status']; ?></td>
          </tr>
<?php

			switch ($banktransfer['banktransfer_status']) {
				case 1 :
					$error_val = TEXT_BANK_ERROR_1;
					break;
				case 2 :
					$error_val = TEXT_BANK_ERROR_2;
					break;
				case 3 :
					$error_val = TEXT_BANK_ERROR_3;
					break;
				case 4 :
					$error_val = TEXT_BANK_ERROR_4;
					break;
				case 5 :
					$error_val = TEXT_BANK_ERROR_5;
					break;
				case 8 :
					$error_val = TEXT_BANK_ERROR_8;
					break;
				case 9 :
					$error_val = TEXT_BANK_ERROR_9;
					break;
			}
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_ERRORCODE; ?></td>
            <td class="main"><?php echo $error_val; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_PRZ; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_prz']; ?></td>
          </tr>
<?php

		}
	}
	if ($banktransfer['banktransfer_fax']) {
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_FAX; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_fax']; ?></td>
          </tr>
<?php

	}
	// end modification for banktransfer
?>
        </table></td>
      </tr>
</table>

</div>

        <div class="tabbertab">
        <h3><?php echo TEXT_ORDER_PRODUCTS; ?></h3>

          <table border="0" width="100%">

      <tr>
        <td><table border="0" width="100%" cellspacing="2" cellpadding="2">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
<?php

	if (isset($order->products[0]['allow_tax']) && $order->products[0]['allow_tax'] == 1) {
?>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
<?php

	}
?>
            <td class="dataTableHeadingContent" align="right"><?php

	echo TABLE_HEADING_TOTAL_INCLUDING_TAX;
	if (isset($i) && isset($order->products[$i]) && $order->products[$i]['allow_tax'] == 1) {
		echo ' (excl.)';
	}
?></td>
          </tr>
<?php
    $color = '';
	for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {

				$products_id_order=$order->products[$i]['id'];
				
				$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
				 echo '<tr onmouseover="this.style.background=\'#e9fff1\';" onmouseout="this.style.background=\''.$color.'\';"  style="background-color:'.$color;
		      echo ';" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'">' . "\n";
			  
				echo '<td class="dataTableContent" valign="top" align="right">'.$order->products[$i]['qty'].'&nbsp;x&nbsp;</td>'."\n".'            <td class="dataTableContent" valign="top"><a href="'.os_href_link(FILENAME_CATEGORIES, 'pID='.$products_id_order.'&action=new_product').'">'.$order->products[$i]['name'].'</a>';

				//Bundle
				$products_bundle = '';
				if ($order->products[$i]['bundle'] == 1)
				{
					
					$bundle_query = getBundleProducts($order->products[$i]['id']);
					
					if (os_db_num_rows($bundle_query) > 0)
					{
						while($bundle_data = os_db_fetch_array($bundle_query))
						{
							$products_bundle_data .= ' - <a href="'.os_href_link(FILENAME_CATEGORIES, 'pID='.$bundle_data['products_id'].'&action=new_product').'">'.$bundle_data['products_name'].' ('.TEXT_QTY.$bundle_data['products_quantity'].TEXT_UNITS.')</a><br />';
						}
					}
					$products_bundle = (!empty($products_bundle_data)) ? '<br /><div class="bundles-products-block">'.$products_bundle_data.'</div>' : '';
				}
				echo $products_bundle;
				//End of Bundle

		if (isset($order->products[$i]['attributes']) && sizeof($order->products[$i]['attributes']) > 0) {
			for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j ++) {

				echo '<br /><nobr><small>&nbsp;<i> - '.$order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value'].': ';

			}

			echo '</i></small></nobr>';
		}

		echo '            </td>'."\n".'            <td class="dataTableContent" valign="top">';

		if ($order->products[$i]['model'] != '') {
			echo $order->products[$i]['model'];
		} else {
			echo '<br />';
		}

		if (isset($order->products[$i]['attributes']) && sizeof($order->products[$i]['attributes']) > 0) {
			for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j ++) {

				$model = os_get_attributes_model($order->products[$i]['id'], $order->products[$i]['attributes'][$j]['value'],$order->products[$i]['attributes'][$j]['option']);
				if ($model != '') {
					echo $model.'<br />';
				} else {
					echo '<br />';
				}
			}
		}

		echo '&nbsp;</td>'."\n".'            <td class="dataTableContent" align="center" valign="top">'.format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], isset($order->products[$i]['allow_tax'])?$order->products[$i]['allow_tax']:'', $order->products[$i]['tax']).'</td>'."\n";

		if (isset($order->products[$i]['allow_tax']) && $order->products[$i]['allow_tax'] == 1) {
			echo '<td class="dataTableContent" align="right" valign="top">';
			echo os_display_tax_value($order->products[$i]['tax']).'%';
			echo '</td>'."\n";
			echo '<td class="dataTableContent" align="right" valign="top"><b>';

			echo format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], 0, 0);

			echo '</b></td>'."\n";
		}
		echo '            <td class="dataTableContent" align="right" valign="top"><b>'.format_price(($order->products[$i]['final_price']), 1, $order->info['currency'], 0, 0).'</b></td>'."\n";
		echo '          </tr>'."\n";
	}
?>
          <tr>
            <td align="right" colspan="10"><table border="0" cellspacing="0" cellpadding="2">
<?php

	for ($i = 0, $n = sizeof($order->totals); $i < $n; $i ++) {
		echo '              <tr>'."\n".'                <td align="right" class="smallText">'.$order->totals[$i]['title'].'</td>'."\n".'                <td align="right" class="smallText">'.$order->totals[$i]['text'].'</td>'."\n".'              </tr>'."\n";
	}
?>
            </table></td>
          </tr>
        </table></td>
      </tr>   
</table>
      
</div>
        <div class="tabbertab">
        <h3><?php echo TEXT_ORDER_STATUS; ?></h3>
      
          <table border="0">

      <tr>
        <td class="main"><table border="0" cellspacing="2" cellpadding="5">
          <tr>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_DATE_ADDED; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_STATUS; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
          </tr>
<?php

	$orders_history_query = os_db_query("select orders_status_id, date_added, customer_notified, comments from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".os_db_input($oID)."' order by date_added");
	if (os_db_num_rows($orders_history_query)) {
		while ($orders_history = os_db_fetch_array($orders_history_query)) {
			echo '          <tr>'."\n".'            <td class="smallText" align="center">'.os_datetime_short($orders_history['date_added']).'</td>'."\n".'            <td class="smallText" align="center">';
			if ($orders_history['customer_notified'] == '1') {
				echo os_image(http_path('icons_admin').'tick.gif', ICON_TICK)."</td>\n";
			} else {
				echo os_image(http_path('icons_admin').'cross.gif', ICON_CROSS)."</td>\n";
			}
			echo '            <td class="smallText">';
			if($orders_history['orders_status_id']!='0') {
				echo $orders_status_array[$orders_history['orders_status_id']];
			}else{
				echo '<font color="#FF0000">'.TEXT_VALIDATING.'</font>';
			}
			echo '</td>'."\n".'            <td class="smallText">'.nl2br(os_db_output($orders_history['comments'])).'&nbsp;</td>'."\n".'          </tr>'."\n";
		}
	} else {
		echo '          <tr>'."\n".'            <td class="smallText" colspan="5">'.TEXT_NO_ORDER_HISTORY.'</td>'."\n".'          </tr>'."\n";
	}
?>
        </table></td>
      </tr>
      <tr>
        <td class="main"><br /><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
      </tr>
      <tr><?php echo os_draw_form('status', FILENAME_ORDERS, os_get_all_get_params(array('action')) . 'action=update_order'); ?>
        <td class="main"><?php echo os_draw_textarea_field('comments', 'soft', '60', '5'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo ENTRY_STATUS; ?></b> <?php echo os_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b> <?php echo os_draw_checkbox_field('notify', '', true); ?></td>
                <td class="main"><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b> <?php echo os_draw_checkbox_field('notify_comments', '', true); ?></td>
              </tr>
            </table></td>
            <td valign="top"><span class="button"><button type="submit" value="<?php echo BUTTON_UPDATE; ?>"><?php echo BUTTON_UPDATE; ?></button></span></td>
          </tr>
        </table></td>
      </form></tr>
      
</table>
      
</div>      
</div>

<table width="100%" border="0" cellspacing="2" cellpadding="2">
      
      <tr>
        <td align="right">
        <br />
<?php
	if (ACTIVATE_GIFT_SYSTEM == 'true') {
		echo '<a class="button" href="'.os_href_link(FILENAME_GV_MAIL, os_get_all_get_params(array ('cID', 'action')).'cID='.$order->customer['ID']).'"><span>'.BUTTON_SEND_COUPON.'</span></a>';
	}
?>
   <a class="button" href="Javascript:void()" onclick="window.open('<?php echo os_href_link(FILENAME_PRINT_ORDER,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><span><?php echo BUTTON_INVOICE; ?></span></a>
   <a class="button" href="Javascript:void()" onclick="window.open('<?php echo os_href_link(FILENAME_PRINT_PACKINGSLIP,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><span><?php echo BUTTON_PACKINGSLIP; ?></span></a>
	<a class="button" href="<?php echo os_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=deleteccinfo').'"><span>'.BUTTON_REMOVE_CC_INFO;?></span></a>&nbsp;
   <a class="button" href="<?php echo os_href_link(FILENAME_ORDERS, 'page='.$_GET['page'].'&oID='.$_GET['oID']).'"><span>'.BUTTON_BACK;?></span></a>
       </td>
      </tr>

</table>      
      
<?php

}
elseif (isset($_GET['action']) && $_GET['action'] == 'custom_action') {

	include ('orders_actions.php');

} else {
?>
      <tr>
        <td width="100%">
        

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" class="main" align="right">
              <?php echo os_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
                <?php echo HEADING_TITLE_SEARCH . ' ' . os_draw_input_field('oID', '', 'size="12"') . os_draw_hidden_field('action', 'edit').os_draw_hidden_field(os_session_name(), os_session_id()); ?>
              </form>
</td>
  </tr>
  <tr>
    <td colspan="2" class="main" valign="top" align="right"><?php echo os_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
                <?php echo HEADING_TITLE_STATUS . ' ' . os_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)),array(array('id' => '0', 'text' => TEXT_VALIDATING)), $orders_statuses), '', 'onChange="this.form.submit();"').os_draw_hidden_field(os_session_name(), os_session_id()); ?>
              </form></td>
  </tr>
</table>
        

        
        
        </td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
<?php 
echo os_draw_form('multi_action_form', FILENAME_ORDERS,os_get_all_get_params()); 
?>
            <table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><input type="checkbox" onClick="javascript:SwitchCheck();"></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php

	if (isset($_GET['cID'])) 
	{
		$cID = os_db_prepare_input($_GET['cID']);
		$orders_query_raw = "select o.orders_id,  o.customers_name, o.customers_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.customers_id = '".os_db_input($cID)."' and (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by orders_id DESC";
	}
	elseif (isset($_GET['status']) && $_GET['status']=='0') {
			$orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id) where o.orders_status = '0' and ot.class = 'ot_total' order by o.orders_id DESC";
	}
	elseif (isset($_GET['status'])) {
			$status = os_db_prepare_input($_GET['status']);
			$orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and s.orders_status_id = '".os_db_input($status)."' and ot.class = 'ot_total' order by o.orders_id DESC";
	} else {
		$orders_query_raw = "select o.orders_id, o.orders_status, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by o.orders_id DESC";
	}
	$orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $orders_query_raw, $orders_query_numrows);
	$orders_query = os_db_query($orders_query_raw);
	
	$color = '';
	while ($orders = os_db_fetch_array($orders_query)) {
		if (((!isset($_GET['oID'])) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo))) {
			$oInfo = new objectInfo($orders);
		}
    $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
        if ( isset($oInfo) && (is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) {
            echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n";
        } else {
            echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'"  onmouseout="this.className=\'dataTableRow\'">' . "\n";
        }

?>
                <td class="dataTableContent"><input type="checkbox" name="multi_orders[]" value="<?php echo $orders['orders_id'];?>"></td>
                <td class="dataTableContent"><?php echo '<a href="' . os_href_link(FILENAME_ORDERS, os_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . os_image(http_path('icons_admin') . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;<a href="' . os_href_link(FILENAME_ORDERS, os_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id']) . '">' . $orders['customers_name'] . '</a>'; ?></td>
                <td class="dataTableContent" align="right"><?php echo $orders['orders_id']; ?></td>
                <td class="dataTableContent" align="right"><?php echo strip_tags($orders['order_total']); ?></td>
                <td class="dataTableContent" align="center"><?php echo os_datetime_short($orders['date_purchased']); ?></td>
                <td class="dataTableContent" align="right"><?php if($orders['orders_status']!='0') { echo $orders['orders_status_name']; }else{ echo '<font color="#FF0000">'.TEXT_VALIDATING.'</font>';}?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($oInfo) && (is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) { echo os_image(get_path('icons_admin', 'http') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_ORDERS, os_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . os_image(get_path('icons_admin', 'http') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php

	}
?>
<?php
echo '<tr class="dataTableContent"><td colspan="7">' . BUS_HEADING_TITLE . ': ' . os_draw_pull_down_menu('new_status', array_merge(array(array('id' => '', 'text' => BUS_TEXT_NEW_STATUS)), $orders_statuses), '', '') . os_draw_checkbox_field('notify','1',true) . ' ' . BUS_NOTIFY_CUSTOMERS . '</td></tr>';
echo '<tr class="dataTableContent" align="left"><td colspan="7" nobr="nobr" align="left">' .
BUS_DELETE_ORDERS . ': ' . os_draw_checkbox_field('delete_orders','1') . '</td></tr>';
echo '<tr class="dataTableContent" align="center"><td colspan="7" nobr="nobr" align="left">' .
     '<a class="button" href="javascript:SwitchCheck()" onClick="this.blur()"><span>' . BUTTON_REVERSE_SELECTION . '</span></a>&nbsp;
<span class="button"><button type="submit" name="submit" onClick="this.blur();" value="' . BUTTON_SUBMIT . '"/>'.BUTTON_SUBMIT.'</button></span></td></tr>';
?>
</form>
              <tr>
                <td colspan="7"><table border="0" width="100%" cellspacing="2" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                    <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], os_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php

	$heading = array ();
	$contents = array ();
	switch (@$_GET['action']) {
		case 'delete' :
			$heading[] = array ('text' => '<b>'.TEXT_INFO_HEADING_DELETE_ORDER.'</b>');

			$contents = array ('form' => os_draw_form('orders', FILENAME_ORDERS, os_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=deleteconfirm'));
			$contents[] = array ('text' => TEXT_INFO_DELETE_INTRO.'<br /><br /><b>'.$cInfo->customers_firstname.' '.$cInfo->customers_lastname.'</b>');
			$contents[] = array ('text' => '<br />'.os_draw_checkbox_field('restock').' '.TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);
			$contents[] = array ('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" value="'. BUTTON_DELETE .'">'. BUTTON_DELETE .'</button></span><a class="button" href="'.os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id).'"><span>' . BUTTON_CANCEL . '</span></a>');
			break;
		default :
			if (is_object($oInfo)) {
				$heading[] = array ('text' => '<b>['.$oInfo->orders_id.']&nbsp;&nbsp;'.os_datetime_short($oInfo->date_purchased).'</b>');

				$contents[] = array ('align' => 'center', 'text' => '<a class="button" href="'.os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'"><span>'.BUTTON_EDIT.'</span></a><br><a class="button" href="'.os_href_link(FILENAME_ORDERS, os_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=delete').'"><span>'.BUTTON_DELETE.'</span></a><br>&nbsp;<a class="button" href="'.os_href_link(FILENAME_PRINT_ORDER,'oID='.$oInfo->orders_id).'" target="_blank"><span>'.BUTTON_INVOICE.'</span></a><br>&nbsp;<a class="button" href="'.os_href_link(FILENAME_PRINT_PACKINGSLIP,'oID='.$oInfo->orders_id).'" target="_blank"><span>'.BUTTON_PACKINGSLIP.'</span></a>');
				
  $order_payment = $oInfo->payment_method;
  
  
if (!empty($order_payment) && is_file(_MODULES.'payment/'.$order_payment.'/'.$_SESSION['language_admin'].'.php'))  
{
   require(_MODULES.'payment/'.$order_payment.'/'.$_SESSION['language_admin'].'.php');
   $order_payment_text = @constant(MODULE_PAYMENT_.strtoupper($order_payment)._TEXT_TITLE);
}
else
{
  $order_payment_text = TEXT_NO;
}

$shipping_method_query = os_db_query("select title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" .$oInfo->orders_id. "' and class = 'ot_shipping'");
if (os_db_num_rows($shipping_method_query) > 0)
{
	$shipping_method = os_db_fetch_array($shipping_method_query);
	$order_shipping_text = ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title']));
}
else
	$order_shipping_text = '';

				$contents[] = array ('text' => '<br />'.TEXT_DATE_ORDER_CREATED.' '.os_date_short($oInfo->date_purchased));
				if (os_not_null($oInfo->last_modified))
					$contents[] = array ('text' => TEXT_DATE_ORDER_LAST_MODIFIED.' '.os_date_short($oInfo->last_modified));
				$contents[] = array ('text' => '<br />'.TEXT_INFO_PAYMENT_METHOD.' '.$order_payment_text);
				$contents[] = array ('text' => '<br />'.TEXT_INFO_SHIPPING_METHOD.' '.$order_shipping_text);
				
				$order = new order($oInfo->orders_id);
				$contents[] = array ('text' => '<br /><br />'.sizeof($order->products).TEXT_PRODUCTS);

				for ($i = 0; $i < sizeof($order->products); $i ++) {

					$products_id_order=$order->products[$i]['id'];
					
					$rest_order_query = os_db_query("SELECT products_id, products_quantity, products_bundle FROM ".DB_PREFIX."products WHERE products_id = '".$products_id_order."'");
					$rest_order = os_db_fetch_array($rest_order_query);

					//Bundle
					$products_bundle = '';
					if ($order->products[$i]['bundle'] == 1)
					{
						
						$bundle_query = getBundleProducts($order->products[$i]['id']);
						
						if (os_db_num_rows($bundle_query) > 0)
						{
							while($bundle_data = os_db_fetch_array($bundle_query))
							{
								$products_bundle_data .= ' - <a href="'.os_href_link(FILENAME_CATEGORIES, 'pID='.$bundle_data['products_id'].'&action=new_product').'">'.$bundle_data['products_name'].' ('.TEXT_QTY.$bundle_data['products_quantity'].TEXT_UNITS.')</a><br />';
							}
						}
						$products_bundle = (!empty($products_bundle_data)) ? '<br /><div class="bundles-products-block">'.$products_bundle_data.'</div>' : '';
					}
					//End of Bundle

					$model = ($order->products[$i]['model']) ? ' ('.$order->products[$i]['model'].') ' : '';
					
					$contents[] = array ('text' => $order->products[$i]['qty'].'&nbsp;x&nbsp;<a href="'.os_href_link(FILENAME_CATEGORIES, 'pID='.$products_id_order.'&action=new_product').'">'.$order->products[$i]['name'].$model.' ('.TEXT_QTY.$rest_order['products_quantity'].TEXT_UNITS.')</a>'.$products_bundle.'');

					if (isset($order->products[$i]['attributes']) && sizeof($order->products[$i]['attributes']) > 0) {
						for ($j = 0; $j < sizeof($order->products[$i]['attributes']); $j ++) {
							$contents[] = array ('text' => '<small>&nbsp;<i> - '.$order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value'].'</i></small></nobr>');
						}
					}
				}
			}
			break;
	}

	if ((os_not_null($heading)) && (os_not_null($contents))) {
		echo '            <td class="right_box" valign="top">'."\n";

		$box = new box;
		echo $box->infoBox($heading, $contents);

		echo '            </td>'."\n";
	}
?>
          </tr>
        </table></td>
      </tr>
<?php

}
?>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>