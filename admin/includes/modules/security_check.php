<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$file_warning = '';

/*
if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'config.php')), '444')) 
{
   $file_warning .= '<br>config.php';
}
*/

if (!strpos(decoct(fileperms(_CACHE)), '777') and !strpos(decoct(fileperms(_CACHE)), '755')) 
{
   $folder_warning .= '<br>'.'cache/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'media/')), '777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'media/')), '755')) 
{
   $folder_warning .= '<br>media/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'media/content/')), '777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'media/content/')), '755')) 
{
   $folder_warning .= '<br>media/content/';
}

	$payment_query = os_db_query("SELECT *
				FROM ".TABLE_CONFIGURATION."
				WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
while ($payment_data = os_db_fetch_array($payment_query)) 
{
   $installed_payment = $payment_data['configuration_value'];
}

	$shipping_query = os_db_query("SELECT *
				FROM ".TABLE_CONFIGURATION."
				WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
while ($shipping_data = os_db_fetch_array($shipping_query)) 
{
   $installed_shipping = $shipping_data['configuration_value'];
}

$system_error = 0;
	
if ($file_warning != '' or $folder_warning != '' or $installed_shipping == '' or $installed_payment == '') 
{
   $system_error = 1;
}

if (is_dir(_CATALOG.'install'))
{
   $system_error = 1;
}
?>