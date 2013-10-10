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
if (isset($_GET['action']) && $_GET['action']=='default')
{
os_db_query('UPDATE '.DB_PREFIX.'products_description SET products_viewed = \'0\'');
}

$breadcrumb->add(HEADING_TITLE, FILENAME_STATS_PRODUCTS_VIEWED);

$main->head();
$main->top_menu();
?>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_NUMBER; ?></th>
			<th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
			<th><?php echo TABLE_HEADING_VIEWED; ?></th>
		</tr>
	</thead>
<?php
if ($_GET['page'] > 1)
	$rows = $_GET['page'] * '20' - '20';
$products_query_raw = "select p.products_id, pd.products_name, pd.products_viewed, l.name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_LANGUAGES . " l where p.products_id = pd.products_id and l.languages_id = pd.language_id order by pd.products_viewed DESC";
$products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_query_raw, $products_query_numrows);
$products_query = os_db_query($products_query_raw);

while ($products = os_db_fetch_array($products_query))
{
	$rows++;

	if (strlen($rows) < 2)
	{
		$rows = '0' . $rows;
	}
	?>
	<tr>
		<td><?php echo $rows; ?>.</td>
		<td><?php echo $products['products_name'] . ' (' . $products['name'] . ')'; ?></td>
		<td><?php echo $products['products_viewed']; ?></td>
	</tr>
	<?php
}
?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
		<td><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
	</tr>
</table>

<a class="button" href="?action=default"><span><?php echo SET_DEFAULT_VIEWED; ?></span></a>

<?php $main->bottom(); ?>