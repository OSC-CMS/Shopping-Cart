<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$xx_mins_ago = (time() - 900);

require('includes/top.php');
require(_CLASS.'price.php');

os_db_query("delete from ".TABLE_WHOS_ONLINE." where time_last_click < '".$xx_mins_ago."'");

$breadcrumb->add(HEADING_TITLE, FILENAME_WHOS_ONLINE);

$main->head();
$main->top_menu();
?>

<?php
if (SET_WHOS_ONLINE == "true")
{
	?>

	<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_ONLINE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CUSTOMER_ID; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_FULL_NAME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_IP_ADDRESS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ENTRY_TIME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_LAST_CLICK; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_LAST_PAGE_URL; ?></th>
			<th><span class="line"></span>actions</th>
		</tr>
	</thead>
	<?php
	$whos_online_query = os_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from ".TABLE_WHOS_ONLINE ." order by time_last_click desc");
	while ($whos_online = os_db_fetch_array($whos_online_query))
	{
		$time_online = (time() - $whos_online['time_entry']);
		if (((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info))
		{
			$info = $whos_online['session_id'];
		}
	?>
	<tr>
		<td><?php echo gmdate('H:i:s', $time_online); ?></td>
		<td><?php echo $whos_online['customer_id']; ?></td>
		<td><?php echo $whos_online['full_name']; ?></td>
		<td><?php echo $whos_online['ip_address']; ?></td>
		<td><?php echo date('H:i:s', $whos_online['time_entry']); ?></td>
		<td><?php echo date('H:i:s', $whos_online['time_last_click']); ?></td>
		<td><?php if (preg_match('/^(.*)'.os_session_name().'=[a-f,0-9]+[&]*(.*)/i', $whos_online['last_page_url'], $array)) { echo Htmlspecialchars($array[1].$array[2]); } else { echo Htmlspecialchars($whos_online['last_page_url']); } ?>&nbsp;</td>
		<td><a href="<?php echo os_href_link(FILENAME_WHOS_ONLINE, os_get_all_get_params(array('info', 'action')).'info='.$whos_online['session_id'], 'NONSSL'); ?>">info</a></td>
	</tr>
	<?php
	}
	?>
	</table>

	<h4><?php echo sprintf(TEXT_NUMBER_OF_CUSTOMERS, os_db_num_rows($whos_online_query)); ?></h4>

	<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<?php

	$heading = array();
	$contents = array();
	if (@$info)
	{
		$heading[] = array('text' => '<b>'.TABLE_HEADING_SHOPPING_CART.'</b>');

		//фильтруем $info
		$info  = mysql_real_escape_string($info);

		if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $info))
		{
			die('CartET error: invalide session key.');
		}

		if (STORE_SESSIONS == 'mysql')
		{
			$session_data = os_db_query("select value from ".TABLE_SESSIONS." WHERE sesskey = '".$info."'");
			$session_data = os_db_fetch_array($session_data);
			$session_data = trim($session_data['value']);
		}
		else
		{
			if ((file_exists(os_session_save_path().'/sess_'.$info)) && (filesize(os_session_save_path().'/sess_'.$info) > 0))
			{
				$session_data = file(os_session_save_path().'/sess_'.$info);
				$session_data = trim(implode('', $session_data));
			}
		}

		$user_session = unserialize_session_data($session_data);

		if ($user_session) {
			$products = os_get_products($user_session);
			for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
				$contents[] = array('text' => $products[$i]['quantity'].' x '.$products[$i]['name']);
			}

			if (sizeof($products) > 0) {
				$contents[] = array('align' => 'right', 'text'  => TEXT_SHOPPING_CART_SUBTOTAL.' '.$user_session['cart']->total.' '.$user_session['currency']);
			} else {
				$contents[] = array('text' => '&nbsp;');
			}
		}
	}

	if ( (os_not_null($heading)) && (os_not_null($contents)) ) {
	echo '            <td class="right_box" valign="top">'."\n";

	$box = new box;
	echo $box->infoBox($heading, $contents);

	echo '            </td>'."\n";
	}
	?>
	</tr>
	</table>

	<?php
}
else
{
	echo ("Модуль: ".HEADING_TITLE." - отключен;<br>");
	echo ("<a href='configuration.php?gID=17'>Настройка -> Разное -> Быстрое оформление</a>");
}
?>

<?php $main->bottom(); ?>