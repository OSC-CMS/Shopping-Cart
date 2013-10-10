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

$breadcrumb->add(HEADING_TITLE, FILENAME_GV_QUEUE);

$main->head();
$main->top_menu();
?>

<table class="table table-condensed table-big-list">
	<thead>
	<tr>
		<th><?php echo TABLE_HEADING_CUSTOMERS; ?></th>
		<th><span class="line"></span><?php echo TABLE_HEADING_ORDERS_ID; ?></th>
		<th><span class="line"></span><?php echo TABLE_HEADING_VOUCHER_VALUE; ?></th>
		<th><span class="line"></span><?php echo TABLE_HEADING_DATE_PURCHASED; ?></th>
		<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
	</tr>
	</thead>
<?php
$gv_query_raw = "select c.customers_firstname, c.customers_lastname, gv.unique_id, gv.date_created, gv.amount, gv.order_id from ".TABLE_CUSTOMERS." c, ".TABLE_COUPON_GV_QUEUE." gv where (gv.customer_id = c.customers_id and gv.release_flag = 'N')";
$gv_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $gv_query_raw, $gv_query_numrows);
$gv_query = os_db_query($gv_query_raw);
while ($gv_list = os_db_fetch_array($gv_query))
{
	?>
	<tr>
		<td><?php echo $gv_list['customers_firstname'].' '.$gv_list['customers_lastname']; ?></td>
		<td><?php echo $gv_list['order_id']; ?></td>
		<td><?php echo $currencies->format($gv_list['amount']); ?></td>
		<td><?php echo os_datetime_short($gv_list['date_created']); ?></td>
		<td width="100">
			<div class="pull-right">
				<a class="btn btn-mini" href="#" data-action="coupon_couponActivate" data-remove-parent="tr" data-id="<?php echo $gv_list['unique_id']; ?>" data-confirm="Вы уверены, что хотите активировать сертификат?" title="<?php echo BUTTON_RELEASE; ?>"><i class="icon-ok"></i></a>
			</div>
		</td>
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