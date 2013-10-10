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

$breadcrumb->add(HEADING_TITLE, FILENAME_PRODUCTS_EXPECTED);

$main->head();
$main->top_menu();

os_db_query("update ".TABLE_PRODUCTS." set products_date_available = '' where to_days(now()) > to_days(products_date_available)");
?>

<table class="table table-condensed table-big-list border-radius-top">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_DATE_EXPECTED; ?></th>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
	<?php
	$products_query_raw = "select pd.products_id, pd.products_name, p.products_date_available from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS." p where p.products_id = pd.products_id and p.products_date_available != '' and pd.language_id = '".$_SESSION['languages_id']."' order by p.products_date_available DESC";
	$products_split = new splitPageResults($_GET['page'], 5, $products_query_raw, $products_query_numrows);
	$products_query = os_db_query($products_query_raw);
	while ($products = os_db_fetch_array($products_query)) 
	{
		?>
		<tr>
			<td><?php echo $products['products_name']; ?></td>
			<td class="tcenter"><?php echo $products['products_date_available']; ?></td>
			<td width="100"><div class="pull-right"><?php echo $cartet->html->link('<i class="icon-pencil"></i>', os_href_link(FILENAME_CATEGORIES, 'pID='.$products['products_id'].'&action=new_product'), array('class' => 'btn btn-mini', 'title' => BUTTON_EDIT,)); ?></div></td>
		</tr>
		<?php
	}
	?>
</table>
<div class="action-table">
	<div class="pull-right">
		<div class="pagination pagination-mini pagination-right">
			<?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED); ?>
			<?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>

<?php $main->bottom(); ?>