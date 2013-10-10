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

$breadcrumb->add(HEADING_TITLE, FILENAME_STATS_PRODUCTS_PURCHASED);

$main->head();
$main->top_menu();
?>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_NUMBER; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_PRODUCTS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_PURCHASED; ?></th>
		</tr>
	</thead>
	<?php
	if ($_GET['page'] > 1)
		$rows = $_GET['page'] * '20' - '20';

	$products_query_raw = "select p.products_id, p.products_ordered, pd.products_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where pd.products_id = p.products_id and pd.language_id = '".$_SESSION['languages_id']."' and p.products_ordered > 0 group by pd.products_id order by p.products_ordered DESC, pd.products_name";
	$products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_query_raw, $products_query_numrows);
	$products_query = os_db_query($products_query_raw);

	while ($products = os_db_fetch_array($products_query))
	{
		$rows++;

		if (strlen($rows) < 2)
		{
			$rows = '0'.$rows;
		}

		?>
		<tr>
			<td><?php echo $rows; ?>.</td>
			<td><a href="<?php echo os_href_link(FILENAME_CATEGORIES, 'pID='.$products['products_id'].'&action=new_product', 'NONSSL'); ?>"><?php echo $products['products_name']; ?></a></td>
			<td><?php echo $products['products_ordered']; ?></td>
		</tr>
	<?php } ?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
		<td><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
	</tr>
</table>
<?php $main->bottom(); ?>