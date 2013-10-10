<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/categories.php';

require_once (_CLASS_ADMIN.'categories.php');
$catfunc = new categories();

if ($_GET['action'] == 'multi_move') { ?>

	<form name="categories_form" action="<?php echo FILENAME_CATEGORIES; ?>ajax.php?ajax_action=products_multiMove" method="post" id="categories_form" class="form-inline">

		<input type="hidden" name="cPath" value="<?php echo $_GET['cPath']; ?>">

		<?php
		if (is_array($_POST['multi_categories']))
		{
			foreach ($_POST['multi_categories'] AS $category_id)
			{ ?>
					<input type="hidden" name="multi_categories[]" value="<?php echo $category_id; ?>">
			<?php }
		}

		if (is_array($_POST['multi_products']))
		{
			foreach ($_POST['multi_products'] AS $product_id)
			{ ?>
				<input type="hidden" name="multi_products[]" value="<?php echo $product_id; ?>">
			<?php }
		}
		?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_MOVE_ELEMENTS; ?>
			</h4>
		</div>
		<div class="modal-body">

			<p><?php echo TEXT_MOVE_ALL; ?></p>

			<?php echo os_draw_pull_down_menu('move_to_category_id', os_get_category_tree(), $$current_category_id); ?>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('multi_move', BUTTON_MOVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'products_multiMove', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php }elseif ($_GET['action'] == 'multi_copy') { ?>

	<form name="categories_form" action="<?php echo FILENAME_CATEGORIES; ?>ajax.php?ajax_action=products_multiCopy" method="post" id="categories_form">

		<input type="hidden" name="cPath" value="<?php echo $_GET['cPath']; ?>">

		<?php
		if (is_array($_POST['multi_categories']))
		{
			foreach ($_POST['multi_categories'] AS $category_id)
			{ ?>
					<input type="hidden" name="multi_categories[]" value="<?php echo $category_id; ?>">
			<?php }
		}

		if (is_array($_POST['multi_products']))
		{
			foreach ($_POST['multi_products'] AS $product_id)
			{ ?>
				<input type="hidden" name="multi_products[]" value="<?php echo $product_id; ?>">
			<?php }
		}
		?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_MULTICOPY; ?>
			</h4>
		</div>
		<div class="modal-body">

			<div class="oa mh400">
			<?php
			$cat_tree = os_get_category_tree();

			for ($i=0;$n=sizeof($cat_tree),$i<$n;$i++)
			{
				echo '<label class="checkbox">';
				echo '<input type="checkbox" name="dest_cat_ids[]" value="'.$cat_tree[$i]['id'].'">'.$cat_tree[$i]['text'];
				echo '</label>';
			};
			?>
			</div>

			<hr>

			<h5><?php echo TEXT_HOW_TO_COPY; ?></h5>

			<label class="radio">
				<?php echo os_draw_radio_field('copy_as', 'link', true); ?>
				<?php echo TEXT_COPY_AS_LINK; ?>
			</label>
			<label class="radio">
				<?php echo os_draw_radio_field('copy_as', 'duplicate'); ?>
				<?php echo TEXT_COPY_AS_DUPLICATE; ?>
			</label>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('multi_copy', BUTTON_COPY, array('class' => 'btn btn-success save-form', 'data-form-action' => 'products_multiCopy', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'multi_delete') { ?>


	<form name="categories_form" action="<?php echo FILENAME_CATEGORIES; ?>ajax.php?ajax_action=products_multiDelete" method="post" id="categories_form">

		<input type="hidden" name="cPath" value="<?php echo $_GET['cPath']; ?>">

		<?php
		if (is_array($_POST['multi_categories']))
		{
			foreach ($_POST['multi_categories'] AS $category_id)
			{ ?>
				<input type="hidden" name="multi_categories[]" value="<?php echo $category_id; ?>">
			<?php }
		}

		if (is_array($_POST['multi_products']))
		{
			foreach ($_POST['multi_products'] AS $product_id)
			{ ?>
				<input type="hidden" name="multi_products[]" value="<?php echo $product_id; ?>">
			<?php }
		}
		?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_ELEMENTS; ?>
			</h4>
		</div>
		<div class="modal-body">

			<div class="oa mh400">
			<?php
				if (is_array($_POST['multi_categories']))
				{
					foreach ($_POST['multi_categories'] AS $multi_category)
					{
						$category_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$multi_category."' and c.categories_id = cd.categories_id and cd.language_id = '".(int)$_SESSION['languages_id']."'");
						$category = os_db_fetch_array($category_query);
						$category_childs = array('childs_count' => $catfunc->count_category_childs($multi_category));
						$category_products = array('products_count' => $catfunc->count_category_products($multi_category, true));
						$cInfo_array = os_array_merge($category, $category_childs, $category_products);

						$cInfo = new objectInfo($cInfo_array);
						echo '<b>'.$cInfo->categories_name.'</b><br />';
						if ($cInfo->childs_count > 0)
							echo sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count).'<br />';
						if ($cInfo->products_count > 0)
							echo sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count);

						echo '<hr>';
					}
				}

				if (is_array($_POST['multi_products']))
				{
					foreach ($_POST['multi_products'] AS $multi_product)
					{
						echo '<b>'.os_get_products_name($multi_product).'</b><br />';
						$product_categories_string = '';
						$product_categories = os_generate_category_path($multi_product, 'product');
						for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++)
						{
							$category_path = '';
							for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++)
							{
								$category_path .= $product_categories[$i][$j]['text'].'&nbsp;&gt;&nbsp;';
							}
							$category_path = substr($category_path, 0, -16);

							$product_categories_string .= '<label class="checkbox">'.os_draw_checkbox_field('multi_products_categories['.$multi_product.'][]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true).' '.$category_path.'</label>';
						}
						echo $product_categories_string;
						echo '<hr>';
					}
				}
			?>
			</div>


		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('multi_delete', BUTTON_DELETE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'products_multiDelete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>