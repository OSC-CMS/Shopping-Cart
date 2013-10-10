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

if ($_GET['cpath'])
{
	$product_query = os_db_query("select *, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available
	from 
		".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
	where 
		p.products_id = '".(int)$_GET['current_product_id']."' and p.products_id = pd.products_id and pd.language_id = '".(int)$_SESSION['languages_id']."'");

	$product = os_db_fetch_array($product_query);
	$breadcrumb->add(BOX_HEADING_PRODUCTS, './'.FILENAME_CATEGORIES.'?cPath='.$_GET['cpath']);
	$breadcrumb->add($product['products_name'], './'.FILENAME_CATEGORIES.'?cPath='.$_GET['cpath'].'&pID='.$_GET['current_product_id'].'&action=new_product');
}

$breadcrumb->add(TITLE_EDIT);

$main->head();
$main->top_menu();

require(_CLASS.'price.php');
$osPrice = new osPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="SUBMIT_ATTRIBUTES" enctype="multipart/form-data">
<input type="hidden" name="current_product_id" value="<?php echo $_POST['current_product_id']; ?>">
<input type="hidden" name="action" value="change">

<?php

echo os_draw_hidden_field(os_session_name(), os_session_id());
if ($cPath)
	echo '<input type="hidden" name="cPathID" value="'.$cPath.'">';

require(_MODULES_ADMIN . 'new_attributes_functions.php');

$tempTextID = '1999043';
$result = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_OPTIONS." where products_options_id LIKE '%' AND language_id = '".(int)$_SESSION['languages_id']."'");
$matches = os_db_num_rows($result);

?>

	<table class="table table-condensed table-big-list">
<?php
if ($matches)
{
	while ($line = os_db_fetch_array($result))
	{
		$current_product_option_name = $line['products_options_name'];
		$current_product_option_id = $line['products_options_id'];

		// Find all of the Current Option's Available Values
		$result2 = os_db_query("
			SELECT
				*
			FROM
				".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povto
					LEFT JOIN ".TABLE_PRODUCTS_OPTIONS_VALUES." pov ON povto.products_options_values_id = pov.products_options_values_id
			WHERE
				povto.products_options_id = '".(int)$current_product_option_id."' AND
				language_id = '".(int)$_SESSION['languages_id']."'
			ORDER BY
				pov.products_options_values_name ASC"
		);
		$matches2 = os_db_num_rows($result2);

		if ($matches2)
		{

		echo '<tr><thead>';
			echo '<th></th>';
			echo '<th><span class="line"></span>'.$current_product_option_name.'</th>';
			echo '<th><span class="line"></span>'.SORT_ORDER.'</th>';
			echo '<th><span class="line"></span>'.ATTR_MODEL.'</th>';
			echo '<th><span class="line"></span>'.ATTR_STOCK.'</th>';
			echo '<th><span class="line"></span>'.ATTR_WEIGHT.'</th>';
			echo '<th><span class="line"></span>'.ATTR_PREFIXWEIGHT.'</th>';
			echo '<th><span class="line"></span>'.ATTR_PRICE.'</th>';
			echo '<th><span class="line"></span>'.ATTR_PREFIXPRICE.'</th>';
		echo '</thead></tr>';


			$i = '0';
			while ($line = os_db_fetch_array($result2))
			{
				$i++;
				$current_value_id = $line['products_options_values_id'];

				$isSelected = checkAttribute($current_value_id, $_POST['current_product_id'], $current_product_option_id);

				if ($isSelected)
					$checked = ' checked';
				else
					$checked = '';

				$result3 = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '".(int)$current_value_id."' AND language_id = '".(int)$_SESSION['languages_id']."'");

				while($line = os_db_fetch_array($result3))
				{
					$current_value_name = $line['products_options_values_name'];
					echo '<tr>';

					echo '<td><input type="checkbox" name="optionValues['.$current_value_id.']" value="1" '.$checked.'></td>';
					echo '<td>'.$current_value_name.'</td>';
					echo '<td>'.$cartet->html->input_text($current_value_id.'_sortorder', $sortorder, array('class' => 'width30')).'</td>';
					echo '<td>'.$cartet->html->input_text($current_value_id.'_model', $attribute_value_model, array('class' => 'width90')).'</td>';
					echo '<td>'.$cartet->html->input_text($current_value_id.'_stock', $attribute_value_stock, array('class' => 'width30')).'</td>';
					echo '<td>'.$cartet->html->input_text($current_value_id.'_weight', $attribute_value_weight, array('class' => 'width30')).'</td>';
					echo '<td><select name="'.$current_value_id.'_weight_prefix" class="width30"><option value="+"'.$posCheck_weight.'>+<option value="-"'.$negCheck_weight.'>-</select></td>';

					if (PRICE_IS_BRUTTO=='true')
						$attribute_value_price_calculate = $osPrice->Format(os_round($attribute_value_price*((100+(os_get_tax_rate(os_get_tax_class_id($_POST['current_product_id']))))/100),PRICE_PRECISION),false);
					else
						$attribute_value_price_calculate = os_round($attribute_value_price,PRICE_PRECISION);

					echo '<td>'.$cartet->html->input_text($current_value_id.'_price', $attribute_value_price_calculate, array('class' => 'width90'));
					if (PRICE_IS_BRUTTO == 'true')
					{
						echo TEXT_NETTO .' ('.$osPrice->Format(os_round($attribute_value_price,PRICE_PRECISION),true).')';
					}
					echo '</td>';

					echo '<td><select name="'.$current_value_id.'_prefix" class="width30"> <option value="+" '.$posCheck.' >+<option value="-" '.$negCheck.' >-</select></td>';

					echo '</tr>';

					if (DOWNLOAD_ENABLED == 'true')
					{
						$file_list = os_array_merge(array('0' => array('id' => '', 'text' => SELECT_FILE)),os_getFiles(_DOWNLOAD));

						echo '<tr>';
						echo '<td colspan="2" class="main">'.DL_FILE.'<br />'.os_draw_pull_down_menu($current_value_id.'_download_file', $file_list,$attribute_value_download_filename).'</td>';
						echo '<td colspan="2">'.DL_COUNT.'<br />'.$cartet->html->input_text($current_value_id.'_download_count', $attribute_value_download_count, array('class' => 'width90')).'</td>';
						echo '<td colspan="2">'.DL_EXPIRE.'<br />'.$cartet->html->input_text($current_value_id.'_download_expire', $attribute_value_download_expire, array('class' => 'width90')).'</td>';
						echo '<td colspan="2"></td>';
						echo '</tr>';
					}
				}
				if ($i == $matches2 ) $i = '0';
			}
		}
		/*else
		{
			echo '<tr>';
			echo '<td colspan="8"><small>'.OS_NO_VALUES.'</small></td>';
			echo '</tr>';
		}*/
	}
}
?>

</table>

<hr>
	<div class="tcenter footer-btn">
		<input class="btn btn-success ajax_save_attr" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
		<a class="btn btn-link" href="javascript:history.back()"><?php echo BUTTON_CANCEL; ?></a>
	</div>

</form>