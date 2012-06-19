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
  
  require_once(_LIB.'phpmailer/class.phpmailer.php');
  $osTemplate = new osTemplate;

  require(get_path('class_admin') . 'currencies.php');
  $currencies = new currencies();

  if ($_GET['action']=='confirmrelease' && isset($_GET['gid'])) {
    $gv_query=os_db_query("select release_flag from " . TABLE_COUPON_GV_QUEUE . " where unique_id='".$_GET['gid']."'");
    $gv_result=os_db_fetch_array($gv_query);
    if ($gv_result['release_flag']=='N') { 
      $gv_query=os_db_query("select customer_id, amount from " . TABLE_COUPON_GV_QUEUE ." where unique_id='".$_GET['gid']."'");
      if ($gv_resulta=os_db_fetch_array($gv_query)) {
      $gv_amount = $gv_resulta['amount'];
      $mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . $gv_resulta['customer_id'] . "'");
      $mail = os_db_fetch_array($mail_query);

      $osTemplate->assign('language', $_SESSION['language_admin']);
      $osTemplate->caching = false;

      $osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
      $osTemplate->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

      $osTemplate->assign('AMMOUNT',$currencies->format($gv_amount));

      $html_mail=$osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/gift_accepted.html');
      $txt_mail=$osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/gift_accepted.txt');


      os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME,$mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail , $txt_mail);


      $gv_amount=$gv_resulta['amount'];
      $gv_query=os_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='".$gv_resulta['customer_id']."'");
      $customer_gv=false;
      $total_gv_amount=0;
      if ($gv_result=os_db_fetch_array($gv_query)) {
        $total_gv_amount=$gv_result['amount'];
        $customer_gv=true;
      }    
      $total_gv_amount=$total_gv_amount+$gv_amount;
      if ($customer_gv) {
        $gv_update=os_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount='".$total_gv_amount."' where customer_id='".$gv_resulta['customer_id']."'");
      } else {
        $gv_insert=os_db_query("insert into " .TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) values ('".$gv_resulta['customer_id']."','".$total_gv_amount."')");
      }
        $gv_update=os_db_query("update " . TABLE_COUPON_GV_QUEUE . " set release_flag='Y' where unique_id='".$_GET['gid']."'");
      }
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
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDERS_ID; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_VOUCHER_VALUE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $gv_query_raw = "select c.customers_firstname, c.customers_lastname, gv.unique_id, gv.date_created, gv.amount, gv.order_id from " . TABLE_CUSTOMERS . " c, " . TABLE_COUPON_GV_QUEUE . " gv where (gv.customer_id = c.customers_id and gv.release_flag = 'N')";
  $gv_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $gv_query_raw, $gv_query_numrows);
  $gv_query = os_db_query($gv_query_raw);
  while ($gv_list = os_db_fetch_array($gv_query)) {
    if (((!$_GET['gid']) || (@$_GET['gid'] == $gv_list['unique_id'])) && (!$gInfo)) {
      $gInfo = new objectInfo($gv_list);
    }
	
	$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($gInfo)) && ($gv_list['unique_id'] == $gInfo->unique_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link('gv_queue.php', os_get_all_get_params(array('gid', 'action')) . 'gid=' . $gInfo->unique_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link('gv_queue.php', os_get_all_get_params(array('gid', 'action')) . 'gid=' . $gv_list['unique_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $gv_list['customers_firstname'] . ' ' . $gv_list['customers_lastname']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $gv_list['order_id']; ?></td>
                <td class="dataTableContent" align="right"><?php echo $currencies->format($gv_list['amount']); ?></td>
                <td class="dataTableContent" align="right"><?php echo os_datetime_short($gv_list['date_created']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($gInfo)) && ($gv_list['unique_id'] == $gInfo->unique_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_GV_QUEUE, 'page=' . $_GET['page'] . '&gid=' . $gv_list['unique_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $gv_split->display_count($gv_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS); ?></td>
                    <td class="smallText" align="right"><?php echo $gv_split->display_links($gv_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'release':
      $heading[] = array('text' => '[' . $gInfo->unique_id . '] ' . os_datetime_short($gInfo->date_created) . ' ' . $currencies->format($gInfo->amount));

      $contents[] = array('align' => 'center', 'text' => '<a class="button" style="font-color: red;" onClick="this.blur();" href="' . os_href_link('gv_queue.php','action=confirmrelease&gid='.$gInfo->unique_id,'NONSSL').'"><span>'. BUTTON_CONFIRM . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link('gv_queue.php','action=cancel&gid=' . $gInfo->unique_id,'NONSSL') . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    default:
      $heading[] = array('text' => '[' . $gInfo->unique_id . '] ' . os_datetime_short($gInfo->date_created) . ' ' . $currencies->format($gInfo->amount));

      $contents[] = array('align' => 'center','text' => '<a class="button" onClick="this.blur();" href="' . os_href_link('gv_queue.php','action=release&gid=' . $gInfo->unique_id,'NONSSL'). '"><span>' . BUTTON_RELEASE . '</span></a>');
      break;
   }

  if ( (os_not_null($heading)) && (os_not_null($contents)) ) {
    echo '            <td class="right_box" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>