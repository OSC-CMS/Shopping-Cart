<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/coupon_admin.php';
?>

<?php if ($_GET['action'] == 'view_products' && !empty($_GET['c_id'])) { ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>Products</h4>
	</div>

	<div class="modal-body">
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th>ID</th>
					<th><span class="line"></span>Name</th>
					<th><span class="line"></span>Model</th>
				</tr>
			</thead>
			<?php
			$coupon_get = os_db_query("select restrict_to_products from ".TABLE_COUPONS."  where coupon_id='".$_GET['c_id']."'");
			$get_result = os_db_fetch_array($coupon_get);
			$pr_ids = preg_split("/[,]/", $get_result['restrict_to_products']);
			for ($i = 0; $i < count($pr_ids); $i++) {
				$result = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'and p.products_id = '" . $pr_ids[$i] . "'");
				if ($row = os_db_fetch_array($result)) { ?>
					<tr>
						<td><?php echo $row["products_id"]; ?></td>
						<td><?php echo $row["products_name"]; ?></td>
						<td><?php echo $row["products_model"]; ?></td>
					</tr>
			<?php }} ?>
		</table>
	</div>

<?php } elseif ($_GET['action'] == 'view_categories' && !empty($_GET['c_id'])) { ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>Categories</h4>
	</div>

	<div class="modal-body">
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th>ID</th>
					<th><span class="line"></span>Name</th>
				</tr>
			</thead>
			<?php
			$coupon_get = os_db_query("select restrict_to_categories from ".TABLE_COUPONS."  where coupon_id='".$_GET['c_id']."'");
			$get_result = os_db_fetch_array($coupon_get);
			$cat_ids = preg_split("/[,]/", $get_result['restrict_to_categories']);
			for ($i = 0; $i < count($cat_ids); $i++) {
				$result = os_db_query("SELECT * FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd WHERE c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' and c.categories_id='" . $cat_ids[$i] . "'");
				if ($row = os_db_fetch_array($result)) { ?>
					<tr>
						<td><?php echo $row["categories_id"]; ?></td>
						<td><?php echo $row["categories_name"]; ?></td>
					</tr>
			<?php }} ?>
		</table>
	</div>

<?php } elseif ($_GET['action'] == 'products') { ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>Products</h4>
	</div>

	<div class="modal-body mh500 oa">
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th>ID</th>
					<th><span class="line"></span>Name</th>
					<th><span class="line"></span>Model</th>
				</tr>
			</thead>
			<?php
			$result = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE p.products_id = pd.products_id and pd.language_id = '".$_SESSION['languages_id']."' ORDER BY pd.products_name");
			while($row = os_db_fetch_array($result)) { ?>
				<tr>
					<td><?php echo $row["products_id"]; ?></td>
					<td><?php echo $row["products_name"]; ?></td>
					<td><?php echo $row["products_model"]; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>

<?php } elseif ($_GET['action'] == 'categories') { ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>Categories</h4>
	</div>

	<div class="modal-body mh500 oa">
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th>ID</th>
					<th><span class="line"></span>Name</th>
				</tr>
			</thead>
			<?php
			$result = os_db_query("SELECT * FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd WHERE c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' ORDER BY c.categories_id");
			while($row = os_db_fetch_array($result)) { ?>
				<tr>
					<td><?php echo $row["categories_id"]; ?></td>
					<td><?php echo $row["categories_name"]; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>

<?php } ?>