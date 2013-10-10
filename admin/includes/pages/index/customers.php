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
	$customers_query_raw = os_db_query("select c.customers_id, c.customers_lastname, c.customers_firstname, c.customers_date_added from ".TABLE_CUSTOMERS." c order by c.customers_date_added desc limit 20");
	while ($customers = os_db_fetch_array($customers_query_raw))
	{
	?>
	<tr>
		<td><div class="text-nowrap"><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID')).'cID='.$customers['customers_id'].'&action=edit'); ?>"><?php echo $customers['customers_lastname']; ?> <?php echo $customers['customers_firstname']; ?></a></div></td>
		<td width="140"><span class="pull-right"><?php echo $customers['customers_date_added']; ?></span></td>
	</tr>
	<?php } ?>
	</tbody>
</table>