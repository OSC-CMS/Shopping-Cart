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

$gID = 26;

if ($_GET['action'] && $_GET['action'] == 'save')
{
	///set_configuration_cache();
	$configuration_query = os_db_query("SELECT configuration_key,configuration_id, configuration_value, use_function,set_function FROM ".TABLE_CONFIGURATION." WHERE configuration_group_id = '".(int)$gID."' ORDER BY sort_order");

	while ($configuration = os_db_fetch_array($configuration_query))
		os_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' WHERE configuration_key = '".$configuration['configuration_key']."'");

	/// set_configuration_cache(); 
	os_redirect(FILENAME_ARTICLES_CONFIG);
}

$breadcrumb->add(HEADING_TITLE, FILENAME_ARTICLES_CONFIG);

$main->head();
$main->top_menu();
?>

<?php echo os_draw_form('configuration', FILENAME_ARTICLES_CONFIG, 'gID='.(int)$gID.'&action=save'); ?>

<table class="table table-condensed">

<?php
$configuration_query = os_db_query("SELECT configuration_key, configuration_id, configuration_value, use_function,set_function FROM ".TABLE_CONFIGURATION." WHERE configuration_group_id = '".(int)$gID."' ORDER BY sort_order");

while ($configuration = os_db_fetch_array($configuration_query))
{
	if ($configuration['set_function'])
	{
		eval('$value_field = '.$configuration['set_function'].'"'.htmlspecialchars($configuration['configuration_value']).'");');
	}
	else
	{
		$value_field = os_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size=15');
	}

	if (strstr($value_field,'configuration_value'))
		$value_field = str_replace('configuration_value', $configuration['configuration_key'], $value_field);

	echo '<tr>
	<td>'.$value_field.'</td>
	<td><b>'.constant(strtoupper($configuration['configuration_key'].'_TITLE')).'</b><br>'.constant(strtoupper( $configuration['configuration_key'].'_DESC')).'</td>
	</tr>
	';
}
?>
</table>

	<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
	</div>

</form>

<?php $main->bottom(); ?>