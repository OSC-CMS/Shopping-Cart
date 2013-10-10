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

require_once (_CLASS_ADMIN.'currencies.php');

$currencies = new currencies();
?>
<table class="table table-striped table-condensed table-content well-table">
	<tbody>
	<?php
	$products_query_raw = os_db_query("SELECT p.products_tax_class_id, p.products_id, pd.products_name, p.products_price, p.products_last_modified FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE p.products_id = pd.products_id AND pd.language_id = '".(int)$_SESSION['languages_id']."' order by p.products_date_added desc limit 20");
	while ($products = os_db_fetch_array($products_query_raw))
	{ ?>
		<tr>
			<td><div class="text-nowrap"><a href="<?php echo os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('pID', 'action')).'pID='.$products['products_id'].'&action=new_product'); ?>"><?php echo $products['products_name']; ?></a></div></td>
			<td width="120"><span class="pull-right"><?php echo $currencies->format(os_round($products['products_price'], PRICE_PRECISION)); ?></span></td>
		</tr>
	<?php } ?>
	</tbody>
</table>