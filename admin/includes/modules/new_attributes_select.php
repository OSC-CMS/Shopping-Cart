<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ не допускается.');
$adminImages = DIR_WS_CATALOG."langs/".$_SESSION['language_admin']."/admin/images/buttons/";

$breadcrumb->add($pageTitle);

$main->head();
$main->top_menu();
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="SELECT_PRODUCT" method="post">
<input type="hidden" name="action" value="edit">
<?php echo os_draw_hidden_field(os_session_name(), os_session_id()); ?>

	<div class="control-group">
		<label class="control-label" for=""><?php echo SELECT_PRODUCT; ?></label>
		<div class="controls">
			<select name="current_product_id">
			<?php
			$result = os_db_query("SELECT * FROM  ".TABLE_PRODUCTS_DESCRIPTION."  where products_id LIKE '%' AND language_id = '".(int)$_SESSION['languages_id']."' ORDER BY products_name ASC");

			$matches = os_db_num_rows($result);
			if ($matches)
			{
				while ($line = os_db_fetch_array($result))
				{
					$title = $line['products_name'];
					$current_product_id = $line['products_id'];
					echo '<option value="'.$current_product_id.'">'.$title.'</option>';
				}
			}
			else
				echo '<option>You have no products at this time</option>';

			echo "</select>";
			?>
			</select>
			<span class="help-block">
				<input type="submit" class="btn btn-mini btn-info" value="<?php echo BUTTON_EDIT; ?>" />
			</span>
		</div>
	</div>


	<div class="control-group">
		<label class="control-label" for=""><?php echo SELECT_COPY; ?></label>
		<div class="controls">
			<select name="copy_product_id">
			<?php
			$copy_query = os_db_query("SELECT pd.products_name, pd.products_id FROM  ".TABLE_PRODUCTS_DESCRIPTION."  pd, ".TABLE_PRODUCTS_ATTRIBUTES." pa where pa.products_id = pd.products_id AND pd.products_id LIKE '%' AND pd.language_id = '".(int)$_SESSION['languages_id']."' GROUP BY pd.products_id ORDER BY pd.products_name ASC");
			$copy_count = os_db_num_rows($copy_query);

			if ($copy_count)
			{
				echo '<option value="0">no copy</option>';
				while ($copy_res = os_db_fetch_array($copy_query))
				{
					echo '<option value="'.$copy_res['products_id'].'">'.$copy_res['products_name'].'</option>';
				}
			}
			else
				echo 'No products to copy attributes from';

			echo "</select>";
			?>
			</select>
			<span class="help-block">
				<input type="submit" class="btn btn-mini btn-info" value="<?php echo BUTTON_EDIT; ?>" />
			</span>
		</div>
	</div>

</form>