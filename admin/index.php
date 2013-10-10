<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require ('includes/top.php');

if (isset($_GET['action']) && !empty($_GET['action']))
{
	if (is_file(_PAGES_ADMIN.os_check_file_name($_GET['action']).'/'.os_check_file_name($_GET['action']).'.php'))
		include(_PAGES_ADMIN.os_check_file_name($_GET['action']).'/'.os_check_file_name($_GET['action']).'.php');
	else
		echo 'no file';
}
else
	os_redirect(os_href_link(FILENAME_DEFAULT));

$main->bottom();
?>