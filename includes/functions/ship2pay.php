<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

function ship2pay() {
	global $order;
	$shipping = $_SESSION['shipping'];
	$shipping_module = substr($shipping['id'], 0, strpos($shipping['id'], '_')) . '.php';
	$q_ship2pay = osDBquery("SELECT payments_allowed, zones_id FROM " . TABLE_SHIP2PAY . " where shipment = '" . $shipping_module . "' and status=1");
	$check_flag = false;
	while($mods = os_db_fetch_array($q_ship2pay,true)) {
		if($mods['zones_id'] > 0) {
			$check_query = osDBquery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $mods['zones_id'] . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
			while ($check = os_db_fetch_array($check_query,true)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break 2;
				} elseif ($check['zone_id'] == $order->delivery['zone_id']) {
					$check_flag = true;
					break 2;
				}
			}
		} else {
			$check_flag = true;
			break;
		}
	}
	if($check_flag)
		$modules = $mods['payments_allowed'];
	else
		$modules = MODULE_PAYMENT_INSTALLED;
	$modules = explode(';', $modules);
	return($modules);
}
?>