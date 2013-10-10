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

$breadcrumb->add(HEADING_TITLE, FILENAME_SERVER_INFO);

$system = os_get_system_information();

$main->head();
$main->top_menu();
?>

<table class="table table-condensed table-big-list">
	<tr>
		<td><?php echo TITLE_SERVER_HOST; ?></td>
		<td><?php echo $system['host'] . ' (' . $system['ip'] . ')'; ?></td>
		<td><?php echo TITLE_DATABASE_HOST; ?></td>
		<td><?php echo $system['db_server'] . ' (' . $system['db_ip'] . ')'; ?></td>
	</tr>
	<tr>
		<td><?php echo TITLE_SERVER_OS; ?></td>
		<td><?php echo $system['system'] . ' ' . $system['kernel']; ?></td>
		<td><?php echo TITLE_DATABASE; ?></td>
		<td><?php echo $system['db_version']; ?></td>
	</tr>
	<tr>
		<td><?php echo TITLE_SERVER_DATE; ?></td>
		<td><?php echo $system['date']; ?></td>
		<td><?php echo TITLE_DATABASE_DATE; ?></td>
		<td><?php echo $system['db_date']; ?></td>
	</tr>
	<tr>
		<td><?php echo TITLE_SERVER_UP_TIME; ?></td>
		<td colspan="3"><?php echo $system['uptime']; ?></td>
	</tr>
	<tr>
		<td><?php echo TITLE_HTTP_SERVER; ?></td>
		<td colspan="3"><?php echo $system['http_server']; ?></td>
	</tr>
	<tr>
		<td><?php echo TITLE_PHP_VERSION; ?></td>
		<td colspan="3"><?php echo $system['php'] . ' (' . TITLE_ZEND_VERSION . ' ' . $system['zend'] . ')'; ?></td>
	</tr>
</table>

<?php $main->bottom(); ?>