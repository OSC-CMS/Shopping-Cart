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
  
  if (AFFILIATE_DELETE_CLICKTHROUGHS != 'false' && is_numeric(AFFILIATE_DELETE_CLICKTHROUGHS)) {
    $time = mktime (1,1,1,date("m"),date("d") - AFFILIATE_DELETE_CLICKTHROUGHS, date("Y"));
    $time = date("Y-m-d", $time);
    os_db_query("delete from " . TABLE_AFFILIATE_CLICKTHROUGHS . " where affiliate_clientdate < '". $time . "'");
  }
  if (AFFILIATE_DELETE_AFFILIATE_BANNER_HISTORY != 'false' && is_numeric(AFFILIATE_DELETE_AFFILIATE_BANNER_HISTORY)) {
    $time = mktime (1,1,1,date("m"),date("d") - AFFILIATE_DELETE_AFFILIATE_BANNER_HISTORY, date("Y"));
    $time = date("Y-m-d", $time);
    os_db_query("delete from " . TABLE_AFFILIATE_BANNERS_HISTORY . " where affiliate_banners_history_date < '". $time . "'");
  }


  $affiliate_banner_history_raw = "select sum(affiliate_banners_shown) as count from " . TABLE_AFFILIATE_BANNERS_HISTORY . "";
  $affiliate_banner_history_query = os_db_query($affiliate_banner_history_raw);
  $affiliate_banner_history = os_db_fetch_array($affiliate_banner_history_query);
  $affiliate_impressions = $affiliate_banner_history['count'];
  if ($affiliate_impressions == 0) $affiliate_impressions = "n/a";

  $affiliate_clickthroughs_raw = "select count(*) as count from " . TABLE_AFFILIATE_CLICKTHROUGHS . "";
  $affiliate_clickthroughs_query = os_db_query($affiliate_clickthroughs_raw);
  $affiliate_clickthroughs = os_db_fetch_array($affiliate_clickthroughs_query);
  $affiliate_clickthroughs = $affiliate_clickthroughs['count'];

  $affiliate_sales_raw = "
            select count(*) as count, sum(affiliate_value) as total, sum(affiliate_payment) as payment from " . TABLE_AFFILIATE_SALES . " a 
            left join " . TABLE_ORDERS . " o on (a.affiliate_orders_id = o.orders_id) 
            where o.orders_status >= " . AFFILIATE_PAYMENT_ORDER_MIN_STATUS . " 
            ";

  $affiliate_sales_query= os_db_query($affiliate_sales_raw);
  $affiliate_sales= os_db_fetch_array($affiliate_sales_query);

  $affiliate_transactions = $affiliate_sales['count'];
  if ($affiliate_clickthroughs > 0) {
	$affiliate_conversions = os_round(($affiliate_transactions / $affiliate_clickthroughs)*100,2) . "%";
  } else {
    $affiliate_conversions = "n/a";
  }

  $affiliate_amount = $affiliate_sales['total'];
  if ($affiliate_transactions > 0) {
	$affiliate_average = os_round($affiliate_amount / $affiliate_transactions, 2);
  } else {
    $affiliate_average = "n/a";
  }

  $affiliate_commission = $affiliate_sales['payment'];

  $affiliates_raw = "select count(*) as count from " . TABLE_AFFILIATE . "";
  $affiliates_raw_query = os_db_query($affiliates_raw);
  $affiliates_raw = os_db_fetch_array($affiliates_raw_query);
  $affiliate_number = $affiliates_raw['count'];
  
  add_action('head_admin', 'head_affiliate_summary' );
  
  function head_affiliate_summary()
  {
    _e('<script language="javascript"><!--
          function popupWindow(url) 
		  {
              window.open(url,\'popupWindow\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=450,height=120,screenX=150,screenY=150,top=150,left=150\')
          }
          //-->
		</script>');
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
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_AFFILIATES; ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_number; ?></td>
                  <td width="35%" align="right" class="dataTableContent"></td>
                  <td width="15%" class="dataTableContent"></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_IMPRESSIONS; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_1) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_impressions; ?></td>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_VISITS; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_2) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_clickthroughs; ?></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_TRANSACTIONS; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_3) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_transactions; ?></td>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_CONVERSION; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_4) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $affiliate_conversions;?></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_AMOUNT; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_5) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $currencies->display_price($affiliate_amount, ''); ?></td>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_AVERAGE; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_6) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo $currencies->display_price($affiliate_average, ''); ?></td>
                </tr>
                <tr>
                  <td width="35%" align="right" class="dataTableContent"><?php echo TEXT_COMMISSION_RATE; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_7) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></td>
                  <td width="15%" class="dataTableContent"><?php echo os_round(AFFILIATE_PERCENT, 2) . ' %'; ?></td>
                  <td width="35%" align="right" class="dataTableContent"><b><?php echo TEXT_COMMISSION; ?><?php echo '<a href="javascript:popupWindow(\'' . (HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_HELP_8) . '\')">' . TEXT_SUMMARY_HELP . '</a>'; ?></b></td>
                  <td width="15%" class="dataTableContent"><b><?php echo $currencies->display_price($affiliate_commission, ''); ?></b></td>
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
                  <td align="right" class="dataTableContent" colspan="4"><?php echo '<a class="button" href="' . os_href_link(FILENAME_AFFILIATE_BANNERS, '') . '"><span>' . IMAGE_BANNERS . '</span></a> <a class="button" href="' . os_href_link(FILENAME_AFFILIATE_CLICKS, '') . '"><span>' . IMAGE_CLICKTHROUGHS . '</span></a> <a class="button" href="' . os_href_link(FILENAME_AFFILIATE_SALES, '') . '"><span>' . IMAGE_SALES . '</span></a>'; ?></td>
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