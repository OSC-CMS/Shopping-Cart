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
require(get_path('class_admin').'currencies.php');
$currencies = new currencies();

$breadcrumb->add(HEADING_TITLE, FILENAME_GV_SENT);

$main->head();
$main->top_menu();
?>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_SENDERS_NAME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_VOUCHER_VALUE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_VOUCHER_CODE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_DATE_SENT; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_EMAIL_ADDRESS; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_SENDERS_ID; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_DATE_REDEEMED; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_IP_ADDRESS; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_CUSTOMERS_ID; ?></th>
		</tr>
	</thead>
<?php

$gv_query_raw = "select c.coupon_amount, c.coupon_code, c.coupon_id, et.sent_firstname, et.sent_lastname, et.customer_id_sent, et.emailed_to, et.date_sent, c.coupon_id, rt.* from ".TABLE_COUPONS." c LEFT JOIN ".TABLE_COUPON_REDEEM_TRACK." rt ON (rt.coupon_id = c.coupon_id), ".TABLE_COUPON_EMAIL_TRACK." et where c.coupon_id = et.coupon_id";
$gv_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $gv_query_raw, $gv_query_numrows);
$gv_query = os_db_query($gv_query_raw);

while ($gv_list = os_db_fetch_array($gv_query))
{
	?>
	<tr>
		<td><?php echo $gv_list['sent_firstname'].' '.$gv_list['sent_lastname']; ?></td>
		<td><?php echo $currencies->format($gv_list['coupon_amount']); ?></td>
		<td><?php echo $gv_list['coupon_code']; ?></td>
		<td><?php echo os_date_short($gv_list['date_sent']); ?></td>
		<td><?php echo $gv_list['emailed_to']; ?></td>
		<td><?php echo $gv_list['customer_id_sent']; ?></td>
		<td><?php echo os_date_short($gv_list['redeem_date']); ?></td>
		<td><?php echo $gv_list['redeem_ip']; ?></td>
		<td><?php echo $gv_list['customer_id']; ?></td>
	</tr>
	<?php
}
?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td><?php echo $gv_split->display_count($gv_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS); ?></td>
<td><?php echo $gv_split->display_links($gv_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
</tr>
</table>

<?php $main->bottom(); ?>