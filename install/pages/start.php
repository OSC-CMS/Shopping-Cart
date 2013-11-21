<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function step($is_submit)
{
	if ($is_submit)
	{
		return setSession();
	}

    $result = array('html' => display('start', array()));

    return $result;
}

function setSession()
{
	$error = !isset($_POST['type']);

	unset($_SESSION['install']);

	$_SESSION['install']['type'] = $_POST['type'];

	return array(
		'error' => $error,
		'message' => t('start_8')
	);
}