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
		return array(
			'error' => false,
		);
	}

    $result = array('html' => display('start', array()));

    return $result;
}