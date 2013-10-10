<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/
?>

<div class="row-fluid">
	<div class="span6">
		<table class="table table-striped table-condensed table-content well-table">
			<tbody>
				<?php
				$orders_status_query = osDBquery("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '".(int)$_SESSION['languages_id']."'");
				$orders_pending_query = osDBquery("select orders_status, count(*) as count from ".TABLE_ORDERS." group by orders_status");
				$_orders_status = '';

				while ($orders_pending = os_db_fetch_array($orders_pending_query,true))
				{
					$_orders_status[$orders_pending['orders_status']] = $orders_pending['count'];
				}

				while ($orders_status = os_db_fetch_array($orders_status_query,true))
				{
					echo '<tr>
						<td><div class="text-nowrap"><a href="'.os_href_link(FILENAME_ORDERS, 'selected_box=customers&amp;status='.$orders_status['orders_status_id'], 'SSL').'">'.$orders_status['orders_status_name'].'</a></div></td>
						<td width="50"><span class="pull-right">'.(isset($_orders_status[$orders_status['orders_status_id']]) ? $_orders_status[$orders_status['orders_status_id']] : '0').'</span></td>
					</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="span6">
		<table class="table table-striped table-condensed table-content well-table">
			<tbody>
				<?php
				$customers_query = osDBquery("select count(*) as count from ".TABLE_CUSTOMERS);
				$customers = os_db_fetch_array($customers_query,true);
				$products_query = osDBquery("select count(*) as count from ".TABLE_PRODUCTS." where products_status = '1'");
				$products = os_db_fetch_array($products_query,true);
				$reviews_query = osDBquery("select count(*) as count from ".TABLE_REVIEWS);
				$reviews = os_db_fetch_array($reviews_query,true);
				?>
				<tr>
					<td><div class="text-nowrap"><a href="<?php echo os_href_link('customers.php'); ?>"><?php echo BOX_HEADING_CUSTOMERS; ?></a></div></td>
					<td width="50"><span class="pull-right"><?php echo $customers['count']; ?></span></td>
				</tr>
				<tr>
					<td><div class="text-nowrap"><a href="<?php echo os_href_link('categories.php'); ?>"><?php echo TEXT_SUMMARY_PRODUCTS; ?></a></div></td>
					<td width="50"><span class="pull-right"><?php echo $products['count']; ?></span></td>
				</tr>
				<tr>
					<td><div class="text-nowrap"><a href="<?php echo os_href_link('reviews.php'); ?>"><?php echo BOX_REVIEWS; ?></a></div></td>
					<td width="50"><span class="pull-right"><?php echo $reviews['count']; ?></span></td>
				</tr>
			</tbody>
		</table>
		<br />
		<table class="table table-striped table-condensed table-content well-table">
			<tbody>
				<?php
				if(!is_object($currencies))
				{
					include_once(_CLASS_ADMIN.'currencies.php');
					$currencies = new currencies();
				}
				$sum_query = os_db_query("select sum(ot.value) as sum from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id) where ot.class = 'ot_total'");
				$sum = os_db_fetch_array($sum_query);
				$today_sum_query = osDBquery("select sum(ot.value) as sum from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id) where to_days(o.date_purchased) = to_days(now()) and ot.class = 'ot_total'");
				$today_sum = os_db_fetch_array($today_sum_query);
				?>
				<tr>
					<td><div class="text-nowrap"><?php echo TEXT_TODAY_SUM; ?></div></td>
					<td width="90"><span class="pull-right"><?php echo $currencies->format($today_sum['sum']); ?></span></td>
				</tr>
				<tr>
					<td><div class="text-nowrap"><?php echo TEXT_TOTAL_SUM; ?></div></td>
					<td width="90"><span class="pull-right"><?php echo $currencies->format($sum['sum']); ?></span></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>