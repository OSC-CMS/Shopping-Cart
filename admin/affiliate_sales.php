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

  if ($_GET['acID'] > 0) {

    $affiliate_sales_raw = "
      select asale.*, os.orders_status_name as orders_status, a.affiliate_firstname, a.affiliate_lastname from " . TABLE_AFFILIATE_SALES . " asale 
      left join " . TABLE_ORDERS . " o on (asale.affiliate_orders_id = o.orders_id) 
      left join " . TABLE_ORDERS_STATUS . " os on (o.orders_status = os.orders_status_id and language_id = " . $_SESSION['languages_id'] . ")
      left join " . TABLE_AFFILIATE . " a on (a.affiliate_id = asale.affiliate_id) 
      where asale.affiliate_id = '" . $_GET['acID'] . "'
      order by affiliate_date desc 
      ";
    $affiliate_sales_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $affiliate_sales_raw, $affiliate_sales_numrows);

  } else {

    $affiliate_sales_raw = "
      select asale.*, os.orders_status_name as orders_status, a.affiliate_firstname, a.affiliate_lastname from " . TABLE_AFFILIATE_SALES . " asale 
      left join " . TABLE_ORDERS . " o on (asale.affiliate_orders_id = o.orders_id) 
      left join " . TABLE_ORDERS_STATUS . " os on (o.orders_status = os.orders_status_id and language_id = " . $_SESSION['languages_id'] . ")
      left join " . TABLE_AFFILIATE . " a  on (a.affiliate_id = asale.affiliate_id) 
      order by affiliate_date desc 
      ";
    $affiliate_sales_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $affiliate_sales_raw, $affiliate_sales_numrows);
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main">

    <?php os_header('connect.png',HEADING_TITLE); ?> 
        
        </td>
      </tr>

  <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="4">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_AFFILIATE; ?></td>
            <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_ID; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_VALUE; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PERCENTAGE; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SALES; ?></td>
            <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
          </tr>
<?php
  if ($affiliate_sales_numrows > 0) {
    $affiliate_sales_values = os_db_query($affiliate_sales_raw);
    $number_of_sales = '0';
    while ($affiliate_sales = os_db_fetch_array($affiliate_sales_values)) {
      $number_of_sales++;
      if (($number_of_sales / 2) == floor($number_of_sales / 2)) {
        echo '          <tr class="dataTableRowSelected">';
      } else {
        echo '          <tr class="dataTableRow">';
      }

      $link_to = '<a href="orders.php?action=edit&oID=' . $affiliate_sales['affiliate_orders_id'] . '">' . $affiliate_sales['affiliate_orders_id'] . '</a>';
?>
            <td class="dataTableContent"><?php echo $affiliate_sales['affiliate_firstname'] . " ". $affiliate_sales['affiliate_lastname']; ?></td>
            <td class="dataTableContent" align="center"><?php echo os_date_short($affiliate_sales['affiliate_date']); ?></td>
            <td class="dataTableContent" align="right"><?php echo $link_to; ?></td>
            <td class="dataTableContent" align="right">&nbsp;&nbsp;<?php echo $currencies->display_price($affiliate_sales['affiliate_value'], ''); ?></td>
            <td class="dataTableContent" align="right"><?php echo $affiliate_sales['affiliate_percent'] . "%" ; ?></td>
            <td class="dataTableContent" align="right">&nbsp;&nbsp;<?php echo $currencies->display_price($affiliate_sales['affiliate_payment'], ''); ?></td>
            <td class="dataTableContent" align="center"><?php if ($affiliate_sales['orders_status']) echo $affiliate_sales['orders_status']; else echo TEXT_DELETED_ORDER_BY_ADMIN; ?></td>
<?php
    }
  } else {
?>
          <tr class="dataTableRowSelected">
            <td colspan="7" class="smallText"><?php echo TEXT_NO_SALES; ?></td>
          </tr>
<?php
  }
  if ($affiliate_sales_numrows > 0) {
?>
          <tr>
            <td colspan="7"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText" valign="top"><?php echo $affiliate_sales_split->display_count($affiliate_sales_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SALES); ?></td>
                <td class="smallText" align="right"><?php echo $affiliate_sales_split->display_links($affiliate_sales_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], os_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
  }
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom();?>