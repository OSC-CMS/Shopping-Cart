<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');
//echo MENU_SYSTEM_ERRORS;
?>
<?php
	if ($file_warning != '')
	{
		echo '<div class="alert alert-block">><h4>'.TEXT_FILE_WARNING.'</h4>'.$file_warning.'</div>';
	}

	if ($folder_warning != '')
	{
		echo '<div class="alert alert-block"><h4>'.TEXT_FOLDER_WARNING.'</h4>'.$folder_warning.'</div>';
	}

	if ( ($installed_payment == '') or ($installed_shipping == ''))
	{
		if ($installed_payment == '') {

		echo '<div class="alert alert-error"><a href="modules.php?set=payment" target="_blank">'.TEXT_PAYMENT_ERROR.'</a></div>';
		}

		if ($installed_shipping == '') {

		echo '<div class="alert alert-error"><a href="modules.php?set=shipping" target="_blank">'.TEXT_SHIPPING_ERROR.'</a></div>';
		}	
	}
 
	if (is_dir(_CATALOG.'install'))
	{
		echo '<div class="alert alert-error">'.TEXT_INSTALL_ERROR.'</div>';
	}
?>