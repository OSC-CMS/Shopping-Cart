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
  require_once(_FUNC_ADMIN.'wysiwyg_tiny.php');
  require(get_path('class_admin') . 'currencies.php');
  $currencies = new currencies();
  
  require_once(_LIB.'phpmailer/class.phpmailer.php');

  $osTemplate = new osTemplate;

  if ( ($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address'] || $_POST['email_to']) && (!$_POST['back_x']) ) {
    switch ($_POST['customers_email_address']) {
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
        if ($_POST['email_to']) {
          $mail_sent_to = $_POST['email_to'];
        }
        break;
    }

    $from = os_db_prepare_input($_POST['from']);
    $subject = os_db_prepare_input($_POST['subject']);
    while ($mail = os_db_fetch_array($mail_query)) {
      $id1 = create_coupon_code($mail['customers_email_address']);
      $osTemplate->assign('language', $_SESSION['language_admin']);
      $osTemplate->caching = false;

      $osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
      $osTemplate->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

      $osTemplate->assign('AMMOUNT', $currencies->format($_POST['amount']));
      $osTemplate->assign('MESSAGE', $_POST['message']);
      $osTemplate->assign('GIFT_ID', $id1);
      $osTemplate->assign('WEBSITE', HTTP_SERVER  . DIR_WS_CATALOG);


      $link = HTTP_SERVER  . DIR_WS_CATALOG . 'gv_redeem.php' . '?gv_no='.$id1;


      $osTemplate->assign('GIFT_LINK',$link);

      $html_mail=$osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/send_gift.html');
      $txt_mail=$osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/send_gift.txt');

      if ($subject=='') $subject=EMAIL_BILLING_SUBJECT;
      os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail , $txt_mail);

      $insert_query = os_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $id1 . "', 'G', '" . $_POST['amount'] . "', now())");
      $insert_id = os_db_insert_id($insert_query);
      $insert_query = os_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $mail['customers_email_address'] . "', now() )");
    }
    if ($_POST['email_to']) {
      $id1 = create_coupon_code($_POST['email_to']);
      $osTemplate->assign('language', $_SESSION['language_admin']);
      $osTemplate->caching = false;

      $osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
      $osTemplate->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

      $osTemplate->assign('AMMOUNT', $currencies->format($_POST['amount']));
      $osTemplate->assign('MESSAGE', $_POST['message']);
      $osTemplate->assign('GIFT_ID', $id1);
      $osTemplate->assign('WEBSITE', HTTP_SERVER  . DIR_WS_CATALOG);

      if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
        $link = HTTP_SERVER  . DIR_WS_CATALOG . 'gv_redeem.php' . '/gv_no,'.$id1;
      } else {
        $link = HTTP_SERVER  . DIR_WS_CATALOG . 'gv_redeem.php' . '?gv_no='.$id1;
      }

      $osTemplate->assign('GIFT_LINK',$link);

      $html_mail=$osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_gift.html');
      $txt_mail=$osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_gift.txt');


      os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $_POST['email_to'] , '' , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail , $txt_mail);

      $insert_query = os_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $id1 . "', 'G', '" . $_POST['amount'] . "', now())");
      $insert_id = os_db_insert_id($insert_query);
      $insert_query = os_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $_POST['email_to'] . "', now() )");
    }
    os_redirect(os_href_link(FILENAME_GV_MAIL, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['customers_email_address']) && (!$_POST['email_to']) ) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['amount']) ) {
    $messageStack->add(ERROR_NO_AMOUNT_SELECTED, 'error');
  }

  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }
  
  add_action('head_admin', 'head_gv_mail');
  
  function head_gv_mail()
  {
     if ($_GET['action'] != 'preview') 
	 {
         $query=os_db_query("SELECT code FROM ". TABLE_LANGUAGES ." WHERE languages_id='".$_SESSION['languages_id']."'");
         $data=os_db_fetch_array($query);
         echo os_wysiwyg_tiny('gv_mail',$data['code']);
     } 
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('basket_go.png',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( ($_GET['action'] == 'preview') && ($_POST['customers_email_address'] || $_POST['email_to']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;
      case '**D':
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;
      default:
        $mail_sent_to = $_POST['customers_email_address'];
        if ($_POST['email_to']) {
          $mail_sent_to = $_POST['email_to'];
        }
        break;
    }
?>
          <tr><?php echo os_draw_form('mail', FILENAME_GV_MAIL, 'action=send_email_to_user'); ?>
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
                <td class="smallText"><b><?php echo TEXT_AMOUNT; ?></b><br /><?php echo nl2br(htmlspecialchars(stripslashes($_POST['amount']))); ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br /><?php echo $_POST['message']; ?></td>
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
                    <td><?php echo '<span class="button"><button type="submit" name="back" onClick="this.blur();" value="' . BUTTON_BACK . '"/>' . BUTTON_BACK . '</button></span>'; ?></td>
                    <td align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GV_MAIL) . '"><span>' . BUTTON_CANCEL . '</span></a> <span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>' . BUTTON_SEND_EMAIL . '</button></span>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo os_draw_form('mail', FILENAME_GV_MAIL, 'action=preview'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    if ($_GET['cID']) {
    $select='where customers_id='.$_GET['cID'];
    } else {
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    }
    $mail_query = os_db_query("select customers_id, customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " ".$select." order by customers_lastname");
    while($customers_values = os_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td class="main"><?php echo TEXT_CUSTOMER; ?></td>
                <td><?php echo os_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
               <tr>
                <td class="main"><?php echo TEXT_TO; ?></td>
                <td><?php echo os_draw_input_field('email_to'); ?><?php echo '&nbsp;&nbsp;' . TEXT_SINGLE_EMAIL; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
             <tr>
                <td class="main"><?php echo TEXT_FROM; ?></td>
                <td><?php echo os_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo os_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_AMOUNT; ?></td>
                <td><?php echo os_draw_input_field('amount'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
<td><?php echo os_draw_textarea_field('message', 'soft', '100%', '55'); ?><br /><a href="javascript:toggleHTMLEditor('message');" class="code"><?php echo TEXT_EDIT_E;?></a></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>' . BUTTON_SEND_EMAIL . '</button></span>'; ?></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  }
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>