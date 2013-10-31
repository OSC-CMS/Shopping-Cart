<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require (_CLASS.'price.php');
$osPrice = new osPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

$i = 0;
$group_query = os_db_query("SELECT customers_status_image, customers_status_id, customers_status_name FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id = '".$_SESSION['languages_id']."' AND customers_status_id != '0'");
while ($group_values = os_db_fetch_array($group_query))
{
	// load data into array
	$i ++;
	$group_data[$i] = array
	(
		'STATUS_NAME' => $group_values['customers_status_name'],
		'STATUS_IMAGE' => $group_values['customers_status_image'],
		'STATUS_ID' => $group_values['customers_status_id']
	);
}
?>

<div class="control-group">
	<label class="control-label" for=""><?php echo TEXT_PRODUCTS_PRICE; ?></label>
	<div class="controls">
		<?php
		// calculate brutto price for display
		if (PRICE_IS_BRUTTO == 'true')
			$products_price = os_round($pInfo->products_price * ((100 + os_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);
		else
			$products_price = os_round($pInfo->products_price, PRICE_PRECISION);

		echo $cartet->html->input_text(
			'products_price',
			$products_price,
			array('class' => 'input-block-level')
		);

		if (PRICE_IS_BRUTTO == 'true')
		{
			echo TEXT_NETTO.'<b>'.$osPrice->Format($pInfo->products_price, false).'</b>  ';
		}
		?>
	</div>
</div>

<?php
for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++)
{
	if ($group_data[$col]['STATUS_NAME'] != '')
	{
		?>
		<div class="control-group">
			<label class="control-label" for=""><?php echo $group_data[$col]['STATUS_NAME']; ?></label>
			<div class="controls">
				<?php
				if (PRICE_IS_BRUTTO == 'true')
					$products_price = os_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id) * ((100 + os_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);
				else
					$products_price = os_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id), PRICE_PRECISION);

				//echo os_draw_input_field('products_price_'.$group_data[$col]['STATUS_ID'], $products_price);

				echo $cartet->html->input_text(
					'products_price_'.$group_data[$col]['STATUS_ID'],
					$products_price,
					array('class' => 'input-block-level')
				);

				if (PRICE_IS_BRUTTO == 'true' && get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id) != '0')
				{
					echo TEXT_NETTO.'<b>'.$osPrice->Format(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id), false).'</b>  ';
				}
				if ($_GET['pID'] != '')
				{
					echo '<span class="help-block"><a class="btn btn-mini btn-info" href="javascript:;" onClick="javascript:toggleBox(\'staffel_'.$group_data[$col]['STATUS_ID'].'\');">'.TXT_STAFFELPREIS.'</a></span>';
				}
				?>
				<div id="staffel_<?php echo $group_data[$col]['STATUS_ID']; ?>" class="longDescription" style="display:none;">
					<div class="row-fluid">
						<div class="span6">
							<div class="well well-small">
								<div class="control-group">
									<label class="control-label" for=""><?php echo TXT_STK; ?></label>
									<div class="controls">
										<?php echo $cartet->html->input_text(
											'products_quantity_staffel_'.$group_data[$col]['STATUS_ID'],
											0,
											array('class' => 'input-block-level')
										); ?>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for=""><?php echo TXT_PRICE; ?></label>
									<div class="controls">
										<?php echo $cartet->html->input_text(
											'products_price_staffel_'.$group_data[$col]['STATUS_ID'],
											0,
											array('class' => 'input-block-level')
										); ?>
									</div>
								</div>

								<?php echo '<input class="btn btn-mini btn-success" type="submit" onClick="return confirm(\''.SAVE_ENTRY.'\')" value="'.BUTTON_INSERT.'" />'; ?>
							</div>
						</div>
					</div>
				</div>

				<?php
				// ok, lets check if there is already a staffelpreis
				$staffel_query = os_db_query("SELECT products_id, quantity, personal_offer FROM ".DB_PREFIX."personal_offers_by_customers_status_".$group_data[$col]['STATUS_ID']." WHERE products_id = '".$pInfo->products_id."' AND quantity != 1 ORDER BY quantity ASC");
				if (os_db_num_rows($staffel_query) > 0)
				{
					echo '<div class="row-fluid"><div class="span6"><table class="table table-condensed table-bordered table-striped">';
					while ($staffel_values = os_db_fetch_array($staffel_query))
					{
						// load data into array
						?>
						<tr>
							<td><?php echo $staffel_values['quantity']; ?></td>
							<td>
								<?php
								if (PRICE_IS_BRUTTO == 'true')
								{
									$tax_query = os_db_query("select tax_rate from ".TABLE_TAX_RATES." where tax_class_id = '".$pInfo->products_tax_class_id."' ");
									$tax = os_db_fetch_array($tax_query);
									$products_price = os_round($staffel_values['personal_offer'] * ((100 + $tax['tax_rate']) / 100), PRICE_PRECISION);
								}
								else
								{
									$products_price = os_round($staffel_values['personal_offer'], PRICE_PRECISION);
								}
								echo $products_price;
								if (PRICE_IS_BRUTTO == 'true')
								{
									echo ' ('.TEXT_NETTO.'<b>'.$osPrice->Format($staffel_values['personal_offer'], false).'</b>)';
								}
								?>
							</td>
							<td width="40" class="tcenter"><a class="btn btn-mini btn-danger" href="<?php echo os_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.'&function=delete&quantity='.$staffel_values['quantity'].'&statusID='.$group_data[$col]['STATUS_ID'].'&action=new_product&pID='.$_GET['pID']); ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-remove icon-white"></i></a></td>
						</tr>
						<?php
					}
					echo '</table></div></div>';
				}
				?>

			</div>
		</div>
		<?php
	}
} ?>