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

require('includes/top.php');
require(get_path('class_admin') . 'currencies.php');
$currencies = new currencies();
require_once(_LIB.'phpmailer/class.phpmailer.php');
$osTemplate = new osTemplate;


if ($_GET['selected_box']) 
{
    $_GET['action']='';
    $_GET['old_action']='';
}

if (($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x'])) 
{
    switch ($_POST['customers_email_address']) 
	{
    case '***':
      $mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
      $mail_sent_to = TEXT_ALL_CUSTOMERS;
      break;
    case '**D':
      $mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
      $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
      break;
    default:
      $customers_email_address = os_db_prepare_input($_POST['customers_email_address']);
      $mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . os_db_input($customers_email_address) . "'");
      $mail_sent_to = $_POST['customers_email_address'];
      break;
    }
    $coupon_query = os_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = os_db_fetch_array($coupon_query);
    $coupon_name_query = os_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $coupon_name = os_db_fetch_array($coupon_name_query);

    $from = os_db_prepare_input($_POST['from']);
    $subject = os_db_prepare_input($_POST['subject']);
    while ($mail = os_db_fetch_array($mail_query)) 
	{
      $osTemplate->assign('language', $_SESSION['language_admin']);
      $osTemplate->caching = false;

      $osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
      $osTemplate->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

      $osTemplate->assign('MESSAGE', $_POST['message']);
      $osTemplate->assign('COUPON_ID', $coupon_result['coupon_code']);
      $osTemplate->assign('WEBSITE', HTTP_SERVER  . DIR_WS_CATALOG);


      $html_mail=$osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_coupon.html');
      $txt_mail=$osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_coupon.txt');


      os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail , $txt_mail);

    }

    os_redirect(os_href_link(FILENAME_COUPON_ADMIN, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }

  if ( ($_GET['action'] == 'preview_email') && (!$_POST['customers_email_address']) ) {
    $_GET['action'] = 'email';
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }

  switch ($_GET['action']) {
    case 'confirmdelete':
      $delete_query=os_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N' where coupon_id='".$_GET['cid']."'");
      break;
    case 'update':
      $_POST['coupon_code'] = trim($_POST['coupon_code']);
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		
		  if($languages[$i]['status']==1) {
          $language_id = $languages[$i]['id'];
          $_POST['coupon_name'][$language_id] = trim($_POST['coupon_name'][$language_id]);
          $_POST['coupon_desc'][$language_id] = trim($_POST['coupon_desc'][$language_id]);
		  }
        }
      $_POST['coupon_amount'] = trim($_POST['coupon_amount']);
      $update_errors = 0;
      if (!$_POST['coupon_name']) {
        $update_errors = 1;
        $messageStack->add(ERROR_NO_COUPON_NAME, 'error');
      }
      if ((!$_POST['coupon_amount']) && (!$_POST['coupon_free_ship'])) {
        $update_errors = 1;
        $messageStack->add(ERROR_NO_COUPON_AMOUNT, 'error');
      }
      if (!$_POST['coupon_code']) {
        $coupon_code = create_coupon_code();
      }
      if ($_POST['coupon_code']) $coupon_code = $_POST['coupon_code'];
      $query1 = os_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . os_db_prepare_input($coupon_code) . "'");
      if (os_db_num_rows($query1) && $_POST['coupon_code'] && $_GET['oldaction'] != 'voucheredit')  {
        $update_errors = 1;
        $messageStack->add(ERROR_COUPON_EXISTS, 'error');
      }
      if ($update_errors != 0) {
        $_GET['action'] = 'new';
      } else {
        $_GET['action'] = 'update_preview';
      }
      break;
    case 'update_confirm':
      if ( ($_POST['back_x']) || ($_POST['back_y']) ) {
        $_GET['action'] = 'new';
      } else {
        $coupon_type = "F";
        if (substr($_POST['coupon_amount'], -1) == '%') $coupon_type='P';
        if ($_POST['coupon_free_ship']) $coupon_type = 'S';
        $sql_data_array = array('coupon_code' => os_db_prepare_input($_POST['coupon_code']),
                                'coupon_amount' => os_db_prepare_input($_POST['coupon_amount']),
                                'coupon_type' => os_db_prepare_input($coupon_type),
                                'uses_per_coupon' => os_db_prepare_input($_POST['coupon_uses_coupon']),
                                'uses_per_user' => os_db_prepare_input($_POST['coupon_uses_user']),
                                'coupon_minimum_order' => os_db_prepare_input($_POST['coupon_min_order']),
                                'restrict_to_products' => os_db_prepare_input($_POST['coupon_products']),
                                'restrict_to_categories' => os_db_prepare_input($_POST['coupon_categories']),
                                'coupon_start_date' => $_POST['coupon_startdate'],
                                'coupon_expire_date' => $_POST['coupon_finishdate'],
                                'date_created' => 'now()',
                                'date_modified' => 'now()');
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
          $language_id = $languages[$i]['id'];
          $sql_data_marray[$i] = array('coupon_name' => os_db_prepare_input($_POST['coupon_name'][$language_id]),
                                 'coupon_description' => os_db_prepare_input($_POST['coupon_desc'][$language_id])

                                 );}
        }
        if ($_GET['oldaction']=='voucheredit') {
          os_db_perform(TABLE_COUPONS, $sql_data_array, 'update', "coupon_id='" . $_GET['cid']."'");
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		  if($languages[$i]['status']==1) {
          $language_id = $languages[$i]['id'];
            $update = os_db_query("update " . TABLE_COUPONS_DESCRIPTION . " set coupon_name = '" . os_db_prepare_input($_POST['coupon_name'][$language_id]) . "', coupon_description = '" . os_db_prepare_input($_POST['coupon_desc'][$language_id]) . "' where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $language_id . "'");
			}
          }
        } else {
          $query = os_db_perform(TABLE_COUPONS, $sql_data_array);
          $insert_id = os_db_insert_id();

          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		  if($languages[$i]['status']==1) {
            $language_id = $languages[$i]['id'];
            $sql_data_marray[$i]['coupon_id'] = $insert_id;
            $sql_data_marray[$i]['language_id'] = $language_id;
            os_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i]);
			}
          }
//        }
      }
    }
  }
  
  add_action('head_admin', 'head_coupon');
  
  function head_coupon ()
  {
     _e('<script type="text/javascript" src="includes/general.js"></script>');
     _e('<link href="includes/javascript/date-picker/css/datepicker.css" rel="stylesheet" type="text/css" />');
     _e('<script type="text/javascript" src="includes/javascript/date-picker/js/datepicker.js"></script>');
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('basket_go.png',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  switch ($_GET['action']) {
  case 'voucherreport':
?>
      <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo CUSTOMER_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo IP_ADDRESS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo REDEEM_DATE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $cc_query_raw = "select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $_GET['cid'] . "'";
    $cc_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $cc_query_raw, $cc_query_numrows);
    $cc_query = os_db_query($cc_query_raw);
    while ($cc_list = os_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['uid']) || (@$_GET['uid'] == $cc_list['unique_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
	  
	  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
      if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link('coupon_admin.php', os_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cInfo->coupon_id . '&action=voucherreport&uid=' . $cinfo->unique_id) . '\'">' . "\n";
      } else {
        echo '          <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link('coupon_admin.php', os_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cc_list['coupon_id'] . '&action=voucherreport&uid=' . $cc_list['unique_id']) . '\'">' . "\n";
      }
$customer_query = os_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $cc_list['customer_id'] . "'");
$customer = os_db_fetch_array($customer_query);

?>
                <td class="dataTableContent"><?php echo $cc_list['customer_id']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $customer['customers_firstname'] . ' ' . $customer['customers_lastname']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $cc_list['redeem_ip']; ?></td>
                <td class="dataTableContent" align="center"><?php echo os_date_short($cc_list['redeem_date']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>



             </table></td>
<?php
    $heading = array();
    $contents = array();
      $coupon_description_query = os_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $coupon_desc = os_db_fetch_array($coupon_description_query);
      $count_customers = os_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $_GET['cid'] . "' and customer_id = '" . $cInfo->customer_id . "'");

      $heading[] = array('text' => '<b>[' . $_GET['cid'] . ']' . COUPON_NAME . ' ' . $coupon_desc['coupon_name'] . '</b>');
      $contents[] = array('text' => '<b>' . TEXT_REDEMPTIONS . '</b>');
      $contents[] = array('text' => TEXT_REDEMPTIONS_TOTAL . '=' . os_db_num_rows($cc_query));
      $contents[] = array('text' => TEXT_REDEMPTIONS_CUSTOMER . '=' . os_db_num_rows($count_customers));
      $contents[] = array('text' => '');
?>
    <td class="right_box" valign="top">
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);
      echo '            </td>' . "\n";

    break;
  case 'preview_email':
    $coupon_query = os_db_query("select coupon_code from " .TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = os_db_fetch_array($coupon_query);
    $coupon_name_query = os_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $coupon_name = os_db_fetch_array($coupon_name_query);
    switch ($_POST['customers_email_address']) {
    case '***':
      $mail_sent_to = TEXT_ALL_CUSTOMERS;
      break;
    case '**D':
      $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
      break;
    default:
      $mail_sent_to = $_POST['customers_email_address'];
      break;
    }
?>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
          <tr><?php echo os_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=send_email_to_user&cid=' . $_GET['cid']); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_CUSTOMER; ?></b><br /><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_COUPON; ?></b><br /><?php echo $coupon_name['coupon_name']; ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br /><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br /><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br /><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td>
<?php
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        echo os_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }
?>
                <table border="0" width="100%" cellpadding="0" cellspacing="2">
                  <tr>
                    <td><?php ?>&nbsp;</td>
                    <td align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_COUPON_ADMIN) . '"><span>' . BUTTON_CANCEL . '</span></a> <span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>' . BUTTON_SEND_EMAIL . '</button></span>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
    break;
  case 'email':
    $coupon_query = os_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = os_db_fetch_array($coupon_query);
    $coupon_name_query = os_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $coupon_name = os_db_fetch_array($coupon_name_query);
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>

          <tr><?php echo os_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=preview_email&cid='. $_GET['cid']); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    $mail_query = os_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
    while($customers_values = os_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_COUPON; ?>&nbsp;&nbsp;</td>
                <td><?php echo $coupon_name['coupon_name']; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_CUSTOMER; ?>&nbsp;&nbsp;</td>
                <td><?php echo os_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?>&nbsp;&nbsp;</td>
                <td><?php echo os_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?>&nbsp;&nbsp;</td>
                <td><?php echo os_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?>&nbsp;&nbsp;</td>
                <td><?php echo os_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>' . BUTTON_SEND_EMAIL . '</button></span>'; ?></td>
              </tr>
            </table></td>
          </form></tr>

      </tr>
      </td>
<?php
    break;
  case 'update_preview':
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
      <td>
<?php echo os_draw_form('coupon', 'coupon_admin.php', 'action=update_confirm&oldaction=' . $_GET['oldaction'] . '&cid=' . $_GET['cid']); ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="6">
<?php
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left"><?php echo COUPON_NAME; ?></td>
        <td align="left"><?php echo $_POST['coupon_name'][$language_id]; ?></td>
      </tr>
<?php
}
}

        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left"><?php echo COUPON_DESC; ?></td>
        <td align="left"><?php echo $_POST['coupon_desc'][$language_id]; ?></td>
      </tr>
<?php
}
}
?>
      <tr>
        <td align="left"><?php echo COUPON_AMOUNT; ?></td>
        <td align="left"><?php echo $_POST['coupon_amount']; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo COUPON_MIN_ORDER; ?></td>
        <td align="left"><?php echo $_POST['coupon_min_order']; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo COUPON_FREE_SHIP; ?></td>
<?php
    if ($_POST['coupon_free_ship']) {
?>
        <td align="left"><?php echo TEXT_FREE_SHIPPING; ?></td>
<?php
    } else {
?>
        <td align="left"><?php echo TEXT_NO_FREE_SHIPPING; ?></td>
<?php
    }
?>
      </tr>
      <tr>
        <td align="left"><?php echo COUPON_CODE; ?></td>
<?php
    if ($_POST['coupon_code']) {
      $c_code = $_POST['coupon_code'];
    } else {
      $c_code = $coupon_code;
    }
?>
        <td align="left"><?php echo $coupon_code; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo COUPON_USES_COUPON; ?></td>
        <td align="left"><?php echo $_POST['coupon_uses_coupon']; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo COUPON_USES_USER; ?></td>
        <td align="left"><?php echo $_POST['coupon_uses_user']; ?></td>
      </tr>

       <tr>
        <td align="left"><?php echo COUPON_PRODUCTS; ?></td>
        <td align="left"><?php echo $_POST['coupon_products']; ?></td>
      </tr>


      <tr>
        <td align="left"><?php echo COUPON_CATEGORIES; ?></td>
        <td align="left"><?php echo $_POST['coupon_categories']; ?></td>
      </tr>
      <tr>
        <td align="left"><?php echo COUPON_STARTDATE; ?></td>
<?php
    $start_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['startdate-mm'],$_POST['startdate-dd'] ,$_POST['startdate'] ));
?>
        <td align="left"><?php echo $start_date; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo COUPON_FINISHDATE; ?></td>
<?php
    $finish_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['finishdate-mm'],$_POST['finishdate-dd'] ,$_POST['finishdate'] ));
    echo date('Y-m-d', mktime(0, 0, 0, $_POST['startdate-mm'],$_POST['startdate-dd'] ,$_POST['startdate'] ));
?>
        <td align="left"><?php echo $finish_date; ?></td>
      </tr>
<?php
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
          $language_id = $languages[$i]['id'];
          echo os_draw_hidden_field('coupon_name[' . $languages[$i]['id'] . ']', $_POST['coupon_name'][$language_id]);
          echo os_draw_hidden_field('coupon_desc[' . $languages[$i]['id'] . ']', $_POST['coupon_desc'][$language_id]);
		  }
       }
    echo os_draw_hidden_field('coupon_amount', $_POST['coupon_amount']);
    echo os_draw_hidden_field('coupon_min_order', $_POST['coupon_min_order']);
    echo os_draw_hidden_field('coupon_free_ship', $_POST['coupon_free_ship']);
    echo os_draw_hidden_field('coupon_code', $c_code);
    echo os_draw_hidden_field('coupon_uses_coupon', $_POST['coupon_uses_coupon']);
    echo os_draw_hidden_field('coupon_uses_user', $_POST['coupon_uses_user']);
    echo os_draw_hidden_field('coupon_products', $_POST['coupon_products']);
    echo os_draw_hidden_field('coupon_categories', $_POST['coupon_categories']);
    echo os_draw_hidden_field('coupon_startdate', date('Y-m-d', mktime(0, 0, 0, $_POST['startdate-mm'],$_POST['startdate-dd'] ,$_POST['startdate'] )));
    echo os_draw_hidden_field('coupon_finishdate', date('Y-m-d', mktime(0, 0, 0, $_POST['finishdate-mm'],$_POST['finishdate-dd'] ,$_POST['finishdate'] )));
?>
     <tr>
        <td align="left"><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_CONFIRM . '"/>' . BUTTON_CONFIRM . '</button></span>'; ?></td>
        <td align="left"><?php echo '<span class="button"><button type="submit" name="back" class="button" onClick="this.blur();" value="' . BUTTON_BACK . '"/>' . BUTTON_BACK . '</button></span>'; ?></td>
      </td>
      </tr>

      </td></table></form>
      </tr>

      </table></td>
<?php

    break;
  case 'voucheredit':
    $languages = os_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	if($languages[$i]['status']==1) {
      $language_id = $languages[$i]['id'];
      $coupon_query = os_db_query("select coupon_name,coupon_description from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" .  $_GET['cid'] . "' and language_id = '" . $language_id . "'");
      $coupon = os_db_fetch_array($coupon_query);
      $coupon_name[$language_id] = $coupon['coupon_name'];
      $coupon_desc[$language_id] = $coupon['coupon_description'];
	  }
    }
    $coupon_query = os_db_query("select coupon_code, coupon_amount, coupon_type, coupon_minimum_order, coupon_start_date, coupon_expire_date, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
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
  case 'new':
// set some defaults
    if (!$coupon_uses_user) $coupon_uses_user=1;
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
      <td>
<?php
    echo os_draw_form('coupon', 'coupon_admin.php', 'action=update&oldaction='.$_GET['action'] . '&cid=' . $_GET['cid'],'post', 'enctype="multipart/form-data"');
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="6">
<?php
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
        $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left" class="main"><?php if ($i==0) echo COUPON_NAME; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_name[' . $languages[$i]['id'] . ']', $coupon_name[$language_id]) . '&nbsp;' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif'); ?></td>
        <td align="left" class="main" width="40%"><?php if ($i==0) echo COUPON_NAME_HELP; ?></td>
      </tr>
<?php
}
}

        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
        $language_id = $languages[$i]['id'];
?>

      <tr>
        <td align="left" valign="top" class="main"><?php if ($i==0) echo COUPON_DESC; ?></td>
        <td align="left" valign="top"><?php echo os_draw_textarea_field('coupon_desc[' . $languages[$i]['id'] . ']','physical','24','3', $coupon_desc[$language_id]) . '&nbsp;' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif'); ?></td>
        <td align="left" valign="top" class="main"><?php if ($i==0) echo COUPON_DESC_HELP; ?></td>
      </tr>
<?php
}}
?>
      <tr>
        <td align="left" class="main"><?php echo COUPON_AMOUNT; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_amount', $coupon_amount); ?></td>
        <td align="left" class="main"><?php echo COUPON_AMOUNT_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_MIN_ORDER; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_min_order', $coupon_min_order); ?></td>
        <td align="left" class="main"><?php echo COUPON_MIN_ORDER_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_FREE_SHIP; ?></td>
        <td align="left"><?php echo os_draw_checkbox_field('coupon_free_ship', $coupon_free_ship); ?></td>
        <td align="left" class="main"><?php echo COUPON_FREE_SHIP_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_CODE; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_code', $coupon_code); ?></td>
        <td align="left" class="main"><?php echo COUPON_CODE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_USES_COUPON; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_uses_coupon', $coupon_uses_coupon); ?></td>
        <td align="left" class="main"><?php echo COUPON_USES_COUPON_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_USES_USER; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_uses_user', $coupon_uses_user); ?></td>
        <td align="left" class="main"><?php echo COUPON_USES_USER_HELP; ?></td>
      </tr>
       <tr>
        <td align="left" class="main"><?php echo COUPON_PRODUCTS; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_products', $coupon_products); ?> <A HREF="validproducts.php" TARGET="_blank" ONCLICK="window.open('validproducts.php', 'Valid_Products', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">View</A></td>
        <td align="left" class="main"><?php echo COUPON_PRODUCTS_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_CATEGORIES; ?></td>
        <td align="left"><?php echo os_draw_input_field('coupon_categories', $coupon_categories); ?> <A HREF="validcategories.php" TARGET="_blank" ONCLICK="window.open('validcategories.php', 'Valid_Categories', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">View</A></td>
        <td align="left" class="main"><?php echo COUPON_CATEGORIES_HELP; ?></td>
      </tr>
      <tr>
<?php
    if (!$_POST['coupon_startdate']) {
      $coupon_startdate = preg_split("/[-]/", date('Y-m-d'));
    } else {
      $coupon_startdate = preg_split("/[-]/", $_POST['coupon_startdate']);
    }
    if (!$_POST['coupon_finishdate']) {
      $coupon_finishdate = preg_split("/[-]/", date('Y-m-d'));
      $coupon_finishdate[0] = $coupon_finishdate[0] + 1;
    } else {
      $coupon_finishdate = preg_split("/[-]/", $_POST['coupon_finishdate']);
    }
?>
        <td align="left" class="main"><?php echo COUPON_STARTDATE; ?></td>
        <td align="left"><?php echo os_draw_input_field('startdate-dd', $coupon_startdate[2], "size=\"2\" id=\"startdate-dd\""); ?> / <?php echo os_draw_input_field('startdate-mm', $coupon_startdate[1], "size=\"2\" id=\"startdate-mm\""); ?> / <?php echo os_draw_input_field('startdate', $coupon_startdate[0], "size=\"4\" id=\"startdate\" class=\"format-d-m-y split-date\""); ?></td>
        <td align="left" class="main"><?php echo COUPON_STARTDATE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_FINISHDATE; ?></td>
        <td align="left"><?php echo os_draw_input_field('finishdate-dd', $coupon_finishdate[2], "size=\"2\" id=\"finishdate-dd\""); ?> / <?php echo os_draw_input_field('finishdate-mm', $coupon_finishdate[1], "size=\"2\" id=\"finishdate-mm\""); ?> / <?php echo os_draw_input_field('finishdate', $coupon_finishdate[0], "size=\"4\" id=\"finishdate\" class=\"format-d-m-y split-date\""); ?></td></td>
        <td align="left" class="main"><?php echo COUPON_FINISHDATE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left"><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_PREVIEW . '"/>' . BUTTON_PREVIEW . '</button></span>'; ?></td>
        <td align="left"><?php echo '&nbsp;&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link('coupon_admin.php', ''); ?>"><span><?php echo BUTTON_CANCEL; ?></span></a>
      </td>
      </tr>
      </td></table></form>
      </tr>

      </table></td>
<?php
    break;
  default:
?>
    <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" class="main"><?php echo os_draw_form('status', FILENAME_COUPON_ADMIN, '', 'get'); ?>
<?php
    $status_array[] = array('id' => 'Y', 'text' => TEXT_COUPON_ACTIVE);
    $status_array[] = array('id' => 'N', 'text' => TEXT_COUPON_INACTIVE);
    $status_array[] = array('id' => '*', 'text' => TEXT_COUPON_ALL);

    if ($_GET['status']) {
      $status = os_db_prepare_input($_GET['status']);
    } else {
      $status = 'Y';
    }
    echo HEADING_TITLE_STATUS . ' ' . os_draw_pull_down_menu('status', $status_array, $status, 'onChange="this.form.submit();"');
?>
              </form>
           </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><a class="button" onClick="this.blur();" href="<?php echo os_href_link('coupon_admin.php', 'action=new'); ?>"><span><?php echo BUTTON_INSERT; ?></span></a><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo COUPON_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_AMOUNT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_CODE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    if ($_GET['page'] > 1) $rows = $_GET['page'] * 20 - 20;
    if ($status != '*') {
      $cc_query_raw = "select coupon_id, coupon_code, coupon_amount, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, date_created,date_modified from " . TABLE_COUPONS ." where coupon_active='" . os_db_input($status) . "' and coupon_type != 'G'";
    } else {
      $cc_query_raw = "select coupon_id, coupon_code, coupon_amount, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, date_created,date_modified from " . TABLE_COUPONS . " where coupon_type != 'G'";
    }
    $cc_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $cc_query_raw, $cc_query_numrows);
    $cc_query = os_db_query($cc_query_raw);
    while ($cc_list = os_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['cid']) || (@$_GET['cid'] == $cc_list['coupon_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
	  
	  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
      if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) {
        echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link('coupon_admin.php', os_get_all_get_params(array('cid', 'action')) . 'cid=' . $cInfo->coupon_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"  onclick="document.location.href=\'' . os_href_link('coupon_admin.php', os_get_all_get_params(array('cid', 'action')) . 'cid=' . $cc_list['coupon_id']) . '\'">' . "\n";
      }
      $coupon_description_query = os_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cc_list['coupon_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $coupon_desc = os_db_fetch_array($coupon_description_query);
?>
                <td class="dataTableContent"><?php echo $coupon_desc['coupon_name']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($cc_list['coupon_type'] == 'P') {
        echo $cc_list['coupon_amount'] . '%';
      } elseif ($cc_list['coupon_type'] == 'S') {
        echo TEXT_FREE_SHIPPING;
      } else {
        echo $currencies->format($cc_list['coupon_amount']);
      }
?>
            &nbsp;</td>
                <td class="dataTableContent" align="center"><?php echo $cc_list['coupon_code']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
          <tr>
            <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
            <?php if (is_object($cc_split)) { ?>
              <tr>
                <td class="smallText">&nbsp;<?php echo $cc_split->display_count($cc_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUPONS); ?>&nbsp;</td>
                <td align="right" class="smallText">&nbsp;<?php echo $cc_split->display_links($cc_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>&nbsp;</td>
              </tr>
            <?php } ?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link('coupon_admin.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->coupon_id . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>

<?php

    $heading = array();
    $contents = array();

    switch ($_GET['action']) {
    case 'release':
      break;
    case 'voucherreport':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_COUPON_REPORT . '</b>');
      $contents[] = array('text' => TEXT_NEW_INTRO);
      break;
    case 'neww':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_COUPON . '</b>');
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $contents[] = array('text' => '<br />' . COUPON_NAME . '<br />' . os_draw_input_field('name'));
      $contents[] = array('text' => '<br />' . COUPON_AMOUNT . '<br />' . os_draw_input_field('voucher_amount'));
      $contents[] = array('text' => '<br />' . COUPON_CODE . '<br />' . os_draw_input_field('voucher_code'));
      $contents[] = array('text' => '<br />' . COUPON_USES_COUPON . '<br />' . os_draw_input_field('voucher_number_of'));
      break;
    default:
      $heading[] = array('text'=>'['.$cInfo->coupon_id.']  '.$cInfo->coupon_code);
      $amount = $cInfo->coupon_amount;
      if ($cInfo->coupon_type == 'P') {
        $amount .= '%';
      } else {
        $amount = $currencies->format($amount);
      }
      if ($_GET['action'] == 'voucherdelete') {
        $contents[] = array('text'=> TEXT_CONFIRM_DELETE . '</br></br>' .
                '<a class="button" onClick="this.blur();" href="'.os_href_link('coupon_admin.php','action=confirmdelete&cid='.$_GET['cid'],'NONSSL').'"><span>'.BUTTON_CONFIRM.'</span></a>' .
                '<a class="button" onClick="this.blur();" href="'.os_href_link('coupon_admin.php','cid='.$cInfo->coupon_id,'NONSSL').'"><span>'.BUTTON_CANCEL.'</span></a>'
                );
      } else {
        $prod_details = NONE;
        if ($cInfo->restrict_to_products) {
          $prod_details = '<A HREF="listproducts.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listproducts.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
        }
        $cat_details = NONE;
        if ($cInfo->restrict_to_categories) {
          $cat_details = '<A HREF="listcategories.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listcategories.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
        }
        $coupon_name_query = os_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cInfo->coupon_id . "' and language_id = '" . $_SESSION['languages_id'] . "'");
        $coupon_name = os_db_fetch_array($coupon_name_query);
        $contents[] = array('text'=>COUPON_NAME . '&nbsp;::&nbsp; ' . $coupon_name['coupon_name'] . '<br />' .
                     COUPON_AMOUNT . '&nbsp;::&nbsp; ' . $amount . '<br />' .
                     COUPON_STARTDATE . '&nbsp;::&nbsp; ' . os_date_short($cInfo->coupon_start_date) . '<br />' .
                     COUPON_FINISHDATE . '&nbsp;::&nbsp; ' . os_date_short($cInfo->coupon_expire_date) . '<br />' .
                     COUPON_USES_COUPON . '&nbsp;::&nbsp; ' . $cInfo->uses_per_coupon . '<br />' .
                     COUPON_USES_USER . '&nbsp;::&nbsp; ' . $cInfo->uses_per_user . '<br />' .
                     COUPON_PRODUCTS . '&nbsp;::&nbsp; ' . $prod_details . '<br />' .
                     COUPON_CATEGORIES . '&nbsp;::&nbsp; ' . $cat_details . '<br />' .
                     DATE_CREATED . '&nbsp;::&nbsp; ' . os_date_short($cInfo->date_created) . '<br />' .
                     DATE_MODIFIED . '&nbsp;::&nbsp; ' . os_date_short($cInfo->date_modified) . '<br /><br />' .
                     '<table border=0 align=center><tr><td align="center"><a class="button" onClick="this.blur();" href="'.os_href_link('coupon_admin.php','action=email&cid='.$cInfo->coupon_id,'NONSSL').'"><span>'.BUTTON_EMAIL.'</span></a></td></tr><tr><td align="center">' .
                     '<a class="button" onClick="this.blur();" href="'.os_href_link('coupon_admin.php','action=voucheredit&cid='.$cInfo->coupon_id,'NONSSL').'"><span>'.BUTTON_EDIT.'</span></a></td></tr><tr><td align="center">' .
                     '<a class="button" onClick="this.blur();" href="'.os_href_link('coupon_admin.php','action=voucherdelete&cid='.$cInfo->coupon_id,'NONSSL').'"><span>'.BUTTON_DELETE.'</span></a></td></tr><tr><td align="center">' .
                     '<a class="button" onClick="this.blur();" href="'.os_href_link('coupon_admin.php','action=voucherreport&cid='.$cInfo->coupon_id,'NONSSL').'"><span>'.BUTTON_REPORT.'</span></a></center></td></tr>'
                     );
        }
        break;
      }
?>
    <td class="right_box" valign="top">
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
    }
?>
      </tr>
    </table></td>
  </tr>
</table>

</tr></table></tr></table></tr></table>
<?php $main->bottom(); ?>