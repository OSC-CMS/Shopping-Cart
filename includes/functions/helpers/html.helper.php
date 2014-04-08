<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

/**
 * Возвращает строку безопасную для html
 */
function html($str)
{
	return htmlspecialchars($str);
}