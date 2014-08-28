<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

if (isset($_GET['payment']) && !empty($_GET['payment']))
{
	include_once(dirname(__FILE__).'/includes/functions/os_check.php');

	$_payment = os_check_file_name($_GET['payment']);
	$_payment_file = dirname(__FILE__).'/modules/payment/'.$_payment.'/'.$_payment.'_process.php';

	if (is_file($_payment_file))
		include($_payment_file);
	else
		die ('CartET error: no payment_process file!');
}
else
	die ('CartET error: no payment_process file!');