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
  
  require_once(_LIB.'phpmailer/class.phpmailer.php');

  if ( ($_GET['action'] == 'send_email_to_user') && ($_POST['affiliate_email_address']) && (!$_POST['back_x']) ) {
    switch ($_POST['affiliate_email_address']) {
      case '***':
        $mail_query = os_db_query("select affiliate_firstname, affiliate_lastname, affiliate_email_address from " . TABLE_AFFILIATE . " ");
        $mail_sent_to = TEXT_ALL_AFFILIATES;
        break;
      default:
        $affiliate_email_address = os_db_prepare_input($_POST['affiliate_email_address']);

        $mail_query = os_db_query("select affiliate_firstname, affiliate_lastname, affiliate_email_address from " . TABLE_AFFILIATE . " where affiliate_email_address = '" . os_db_input($affiliate_email_address) . "'");
        $mail_sent_to = $_POST['affiliate_email_address'];
        break;
    }

    $from = os_db_prepare_input($_POST['from']);
    $subject = os_db_prepare_input($_POST['subject']);
    $message = os_db_prepare_input($_POST['message']);

    while ($mail = os_db_fetch_array($mail_query)) {
      os_php_mail(EMAIL_SUPPORT_ADDRESS,
               	   EMAIL_SUPPORT_NAME,
               	   $mail['affiliate_email_address'] ,
               	   $mail['affiliate_firstname'] . ' ' . $mail['affiliate_lastname'] ,
               	   '',
               	   EMAIL_SUPPORT_REPLY_ADDRESS,
               	   EMAIL_SUPPORT_REPLY_ADDRESS_NAME,
               	   '',
               	   '',
               	   $subject,
               	   $message,
               	   $message);
    }

    os_redirect(os_href_link(FILENAME_AFFILIATE_CONTACT, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['affiliate_email_address']) ) {
    $messageStack->add(ERROR_NO_AFFILIATE_SELECTED, 'error');
  }

  if (os_not_null($_GET['mail_sent_to'])) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main">

    <?php echo HEADING_TITLE; ?> 
        
        </td>
      </tr>

  <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( ($_GET['action'] == 'preview') && ($_POST['affiliate_email_address']) ) {
    switch ($_POST['affiliate_email_address']) {
      case '***':
        $mail_sent_to = TEXT_ALL_AFFILIATES;
        break;
      default:
        $mail_sent_to = $_POST['affiliate_email_address'];
        break;
    }
?>
          <tr><?php echo os_draw_form('mail', FILENAME_AFFILIATE_CONTACT, 'action=send_email_to_user'); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_AFFILIATE; ?></b><br><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
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
                    <td><?php echo '<span class="button"><button type="submit" name="back" value="' . BUTTON_BACK . '">' . BUTTON_BACK . '</button></span>'; ?></td>
                    <td align="right"><?php echo '<a class="button" href="' . os_href_link(FILENAME_AFFILIATE_CONTACT) . '"><span>' . BUTTON_CANCEL . '</span></a> ' . '<span class="button"><button type="submit" value="' . BUTTON_SEND . '">' . BUTTON_SEND . '</button></span>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo os_draw_form('mail', FILENAME_AFFILIATE_CONTACT, 'action=preview'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    $affiliate = array();
    $affiliate[] = array('id' => '', 'text' => TEXT_SELECT_AFFILIATE);
    $affiliate[] = array('id' => '***', 'text' => TEXT_ALL_AFFILIATES);
    $mail_query = os_db_query("select affiliate_email_address, affiliate_firstname, affiliate_lastname from " . TABLE_AFFILIATE . " order by affiliate_lastname");
    while($affiliate_values = os_db_fetch_array($mail_query)) {
      $affiliate[] = array('id' => $affiliate_values['affiliate_email_address'],
                           'text' => $affiliate_values['affiliate_lastname'] . ', ' . $affiliate_values['affiliate_firstname'] . ' (' . $affiliate_values['affiliate_email_address'] . ')');
    }
?>
              <tr>
                <td class="main"><?php echo TEXT_AFFILIATE; ?></td>
                <td><?php echo os_draw_pull_down_menu('affiliate_email_address', $affiliate, $_GET['affiliate']);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?></td>
                <td><?php echo os_draw_input_field('from', AFFILIATE_EMAIL_ADDRESS, 'size="60"'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo os_draw_input_field('subject', '', 'size="60"'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
                <td><?php echo os_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo '<span class="button"><button type="submit" value="' . BUTTON_SEND . '">' . BUTTON_SEND . '</button></span>'; ?></td>
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