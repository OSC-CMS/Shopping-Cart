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

$breadcrumb->add(HEADING_TITLE, FILENAME_STATS_CUSTOMERS);

$main->head();
$main->top_menu();
?>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_NUMBER; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CUSTOMERS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_TOTAL_PURCHASED; ?></th>
		</tr>
	</thead>
<?php
if ($_GET['page'] > 1) $rows = $_GET['page'] * '20' - '20';
$customers_query_raw = "select c.customers_firstname, c.customers_lastname, sum(op.final_price) as ordersum from ".TABLE_CUSTOMERS." c, ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_ORDERS." o where c.customers_id = o.customers_id and o.orders_id = op.orders_id group by c.customers_firstname, c.customers_lastname order by ordersum DESC";
$customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $customers_query_raw, $customers_query_numrows);
$customers_query_numrows = os_db_query("select customers_id from ".TABLE_ORDERS." group by customers_id");
$customers_query_numrows = os_db_num_rows($customers_query_numrows);

$customers_query = os_db_query($customers_query_raw);
while ($customers = os_db_fetch_array($customers_query))
{
	$rows++;

	if (strlen($rows) < 2)
	{
		$rows = '0'.$rows;
	}
	?>
	<tr>
		<td><?php echo $rows; ?>.</td>
		<td><?php echo '<a href="'.os_href_link(FILENAME_CUSTOMERS, 'search='.$customers['customers_lastname'], 'NONSSL').'">'.$customers['customers_firstname'].' '.$customers['customers_lastname'].'</a>'; ?></td>
		<td><?php echo $customers['ordersum']; ?>&nbsp;</td>
	</tr>
	<?php
}
?>
</table>
	
<table>
<tr>
<td><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
<td><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>&nbsp;</td>
</tr>
</table>

<?php $main->bottom(); ?>