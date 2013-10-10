<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

?>
<table class="table table-striped table-condensed table-content well-table">
	<tbody>
	<?php
	$orders_query_raw = os_db_query("select o.orders_id, o.orders_status, o.customers_name, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by o.date_purchased desc limit 20");
	while ($orders = os_db_fetch_array($orders_query_raw))
	{
	?>
		<tr>
			<td width="30"><?php echo $orders['orders_id']; ?></td>
			<td><a class="tt" rel="tooltip" data-placement="right" title="<?php echo os_datetime_short($orders['date_purchased']); ?>" href="<?php echo os_href_link(FILENAME_ORDERS, os_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit'); ?>"><?php echo $orders['customers_name']; ?></a></td>
			<td width="110"><span class="pull-right"><?php echo strip_tags($orders['order_total']); ?></span></td>
			<td width="140"><span class="pull-right label label-info"><?php echo $orders['orders_status_name']; ?></span></td>
		</tr>
	<?php } ?>
	</tbody>
</table>