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
<style>
	.demo-container {
		width: 100%;
		height: 300px;
	}
	.flot-stat-order-placeholder {
		width: 100%;
		height: 100%;
		font-size: 14px;
		line-height: 1.2em;
	}
	#flotTip{font-size:11px;padding:3px 5px;background-color:#000;z-index:100;color:#fff;opacity:.7;filter:alpha(opacity=70);-webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px;}
</style>
<?php
$statGetOrdersByDay = os_db_query("SELECT
					orders_id, date_purchased,
					day(date_purchased) purchased_day,
					month(date_purchased) purchased_month,
					year(date_purchased) purchased_year
				FROM ".TABLE_ORDERS." WHERE date_purchased > (CURDATE()- INTERVAL 1 month) ORDER BY orders_id DESC");
$aStatOrders = array();
if (os_db_num_rows($statGetOrdersByDay) > 0)
{
	while($o = os_db_fetch_array($statGetOrdersByDay))
		$aStatOrders[mktime(0, 0, 0, $o['purchased_month'], $o['purchased_day'], $o['purchased_year'])][] = $o;
}
$aStatOrders2 = array();
if (is_array($aStatOrders))
{
	foreach($aStatOrders AS $day => $orders)
		$aStatOrders2[] = array(($day*1000), count($orders));
}

//_print_r($aStatOrders2);
//echo json_encode(array_reverse($aStatOrders2));
?>
<script type="text/javascript">
	$(function() {
		var orders = [<?php echo json_encode($aStatOrders2); ?>];
		var options = {
			series: {
				lines: {
					show: true,
					lineWidth: 1,
					fill: true,
					fillColor: {
						colors: [{
							opacity: 0.3
						}, {
							opacity: 0.3
						}]
					}
				},
				points: {
					radius: 4,
					show: true
				},
				shadowSize: 2
			},
			grid: {
				hoverable: true,
				clickable: true,
				tickColor: "#f0f0f0",
				borderWidth: 1,
				color: '#f0f0f0'
			},
			colors: ["#23b7e5"],
			xaxis:{
				mode: "time",
				timeformat: "%m-%d"
			},
			tooltip: true,
			tooltipOpts: {
				content: "%y",
				defaultTheme: false
			}
		};

		$.plot("#flot_stat_order", orders, options);
	});
</script>
<div class="demo-container">
	<div id="flot_stat_order" class="flot-stat-order-placeholder"></div>
</div>