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

  require(get_path('class_admin') . 'currencies.php');
  $currencies = new currencies();

  $affiliate_banner_history_raw = "select sum(affiliate_banners_shown) as count from " . TABLE_AFFILIATE_BANNERS_HISTORY .  " where affiliate_banners_affiliate_id  = '" .  $_GET['acID'] . "'";
  $affiliate_banner_history_query = os_db_query($affiliate_banner_history_raw);
  $affiliate_banner_history = os_db_fetch_array($affiliate_banner_history_query);
  $affiliate_impressions = $affiliate_banner_history['count'];
  if ($affiliate_impressions == 0) $affiliate_impressions = "n/a"; 
  
  $affiliate_query = os_db_query("select * from " . TABLE_AFFILIATE . " where affiliate_id ='" . $_GET['acID'] . "'");
 
  $affiliate = os_db_fetch_array($affiliate_query);
  $affiliate_percent = 0;
  $affiliate_percent = $affiliate['affiliate_commission_percent'];
  if ($affiliate_percent < AFFILIATE_PERCENT) $affiliate_percent = AFFILIATE_PERCENT;
  
  $affiliate_clickthroughs_raw = "select count(*) as count from " . TABLE_AFFILIATE_CLICKTHROUGHS . " where affiliate_id = '" . $_GET['acID'] . "'";
  $affiliate_clickthroughs_query = os_db_query($affiliate_clickthroughs_raw);
  $affiliate_clickthroughs = os_db_fetch_array($affiliate_clickthroughs_query);
  $affiliate_clickthroughs = $affiliate_clickthroughs['count'];

  $affiliate_sales_raw = "
    select count(*) as count, sum(affiliate_value) as total, sum(affiliate_payment) as payment from " . TABLE_AFFILIATE_SALES . " a 
    left join " . TABLE_ORDERS . " o on (a.affiliate_orders_id=o.orders_id) 
    where a.affiliate_id = '" . $_GET['acID'] . "' and o.orders_status >= " . AFFILIATE_PAYMENT_ORDER_MIN_STATUS . "
    ";
  $affiliate_sales_query = os_db_query($affiliate_sales_raw);
  $affiliate_sales = os_db_fetch_array($affiliate_sales_query);

  $affiliate_transactions=$affiliate_sales['count'];
  if ($affiliate_clickthroughs > 0) {
	  $affiliate_conversions = os_round(($affiliate_transactions / $affiliate_clickthroughs)*100,2) . "%";
  } else {
    $affiliate_conversions = "n/a";
  }

  if ($affiliate_sales['total'] > 0) {
    $affiliate_average = $affiliate_sales['total'] / $affiliate_sales['count'];
  } else {
    $affiliate_average = 0;
  }
  
  add_action('head_admin', 'affiliate_statistics');
  
  function affiliate_statistics()
  {
     _e('<script language="javascript"><!--
function popupWindow(url) {
  window.open(url,\'popupWindow\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=450,height=120,screenX=150,screenY=150,top=150,left=150\')
}
//--></script>');
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
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TEXT_SUMMARY_TITLE; ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellpadding="4" cellspacing="2" class="dataTableContent">
              <center>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><b><?php echo TEXT_AFFILIATE_NAME; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate['affiliate_firstname'] . ' ' . $affiliate['affiliate_lastname']; ?></td>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_AFFILIATE_JOINDATE; ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                  <td width="15%" class="dataTableContent"><?php echo os_date_short($affiliate['affiliate_date_account_created']); ?></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_IMPRESSIONS; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_1) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_impressions; ?></td>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_VISITS; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_2) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_clickthroughs; ?></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_TRANSACTIONS; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_3) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_sales['count']; ?></td>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_CONVERSION; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_4) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_conversions.' %';?></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_AMOUNT; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_5) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $currencies->display_price($affiliate_sales['total'], ''); ?></td>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_AVERAGE; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_6) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $currencies->display_price($affiliate_average, ''); ?></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_COMMISSION_RATE; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_7) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_percent, ' %'; ?></td>
                  <td width="35%" align="right" class="dataTableContent"><b><?php echo TEXT_COMMISSION; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_8) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></b></td>
                  <td width="15%" class="dataTableContent"><b><?php echo $currencies->display_price($affiliate_sales['payment'], ''); ?></b></td>
                </tr>
                <tr>
                  <td colspan="4"><?php echo os_draw_separator(); ?></td>
                </tr>
                <tr>
                  <td align="center" class="dataTableContent" colspan="4"><b><?php echo TEXT_SUMMARY; ?></b></td>
                </tr>
                <tr>
                  <td colspan="4"><?php echo os_draw_separator(); ?></td>
                </tr>
                <tr>
                  <td align="right" class="dataTableContent" colspan="4"><?php echo '<a class="button" href="' . os_href_link(FILENAME_AFFILIATE_CLICKS, 'acID=' . $_GET['acID']) . '"><span>' . IMAGE_CLICKTHROUGHS . '</span></a> <a class="button" href="' . os_href_link(FILENAME_AFFILIATE_SALES, 'acID=' . $_GET['acID']) . '"><span>' . IMAGE_SALES . '</span></a>'; ?></td>
                </tr>
              </center>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom();?>