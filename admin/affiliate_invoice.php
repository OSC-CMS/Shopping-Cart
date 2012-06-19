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

  $payments_query = os_db_query("select * from " . TABLE_AFFILIATE_PAYMENT . " where affiliate_payment_id = '" . $_GET['pID'] . "'");
  $payments = os_db_fetch_array($payments_query);

  $affiliate_address['firstname'] = $payments['affiliate_firstname'];
  $affiliate_address['lastname'] = $payments['affiliate_lastname'];
  $affiliate_address['street_address'] = $payments['affiliate_street_address'];
  $affiliate_address['suburb'] = $payments['affiliate_suburb'];
  $affiliate_address['city'] = $payments['affiliate_city'];
  $affiliate_address['state'] = $payments['affiliate_state'];
  $affiliate_address['country'] = $payments['affiliate_country'];
  $affiliate_address['postcode'] = $payments['affiliate_postcode']
?>
<?php $main->head();?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php echo nl2br(STORE_NAME_ADDRESS); ?></td>
        <td class="pageHeading" align="center"><?php echo HEADING_TITLE; ?></td>
        <td class="pageHeading" align="right"><?php echo os_image(_HTTP_IMG . 'logo.gif'); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo os_draw_separator(); ?></td>
      </tr>
      <tr>
        <td valign="top"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" valign="top"><b><?php echo TEXT_AFFILIATE; ?></b></td>
            <td class="main"><?php echo os_address_format($payments['affiliate_address_format_id'], $affiliate_address, 1, '&nbsp;', '<br>'); ?></td>
          </tr>
          <tr>
            <td><?php echo os_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
          </tr>
	      <tr>
             <td class="main"><b><?php echo TEXT_AFFILIATE_PAYMENT; ?></b></td>
             <td class="main">&nbsp;<?php echo $currencies->format($payments['affiliate_payment_total']); ?></td>
          </tr>
          <tr>
             <td><?php echo os_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
          </tr>
          <tr>
             <td class="main"><b><?php echo TEXT_AFFILIATE_BILLED; ?></b></td>
             <td class="main">&nbsp;<?php echo os_date_short($payments['affiliate_payment_date']); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><?php echo os_draw_separator('pixel_trans.gif', '1', '20'); ?></td>
  </tr>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_ID; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDER_DATE; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_VALUE; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_COMMISSION_RATE; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_COMMISSION_VALUE; ?></td>
      </tr>
<?php
  $affiliate_payment_query = os_db_query("select * from " . TABLE_AFFILIATE_PAYMENT . " where affiliate_payment_id = '" . $_GET['pID'] . "'");
  $affiliate_payment = os_db_fetch_array($affiliate_payment_query);
  $affiliate_sales_query = os_db_query("select * from " . TABLE_AFFILIATE_SALES . " where affiliate_payment_id = '" . $payments['affiliate_payment_id'] . "' order by affiliate_payment_date desc");
  while ($affiliate_sales = os_db_fetch_array($affiliate_sales_query)) {
?>

      <tr class="dataTableRow">
        <td class="dataTableContent" align="right" valign="top"><?php echo $affiliate_sales['affiliate_orders_id']; ?></td>
        <td class="dataTableContent" align="center" valign="top"><?php echo os_date_short($affiliate_sales['affiliate_date']); ?></td>
        <td class="dataTableContent" align="right" valign="top"><b><?php echo $currencies->display_price($affiliate_sales['affiliate_value'], ''); ?></b></td>
        <td class="dataTableContent" align="right" valign="top"><?php echo $affiliate_sales['affiliate_percent']; ?><?php echo ENTRY_PERCENT; ?></td>
        <td class="dataTableContent" align="right" valign="top"><b><?php echo $currencies->display_price($affiliate_sales['affiliate_payment'], ''); ?></b></td>
      </tr>
<?php
  }
?>
    </table></td>
  </tr>
  <tr>
    <td align="right" colspan="5"><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td align="right" class="smallText"><?php echo TEXT_SUB_TOTAL; ?></td>
        <td align="right" class="smallText"><?php echo $currencies->display_price($affiliate_payment['affiliate_payment'], ''); ?></td>
      </tr>
      <tr>
        <td align="right" class="smallText"><?php echo TEXT_TAX; ?></td>
        <td align="right" class="smallText"><?php echo $currencies->display_price($affiliate_payment['affiliate_payment_tax'], ''); ?></td>
      </tr>
      <tr>
        <td align="right" class="smallText"><b><?php echo TEXT_TOTAL; ?></b></td>
        <td align="right" class="smallText"><b><?php echo $currencies->display_price($affiliate_payment['affiliate_payment_total'], ''); ?></b></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom();?>