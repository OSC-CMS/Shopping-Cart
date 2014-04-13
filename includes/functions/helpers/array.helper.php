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
 * Поиск ключа в многомерном массиве
 */
function multi_array_key_exists($key, $array)
{
	if (is_array($array) && array_key_exists($key, $array))
	{
		return $array[$key];
	}

	foreach ($array as $id => $val)
	{
		if (is_array($val))
		{
			if ($result = multi_array_key_exists($key, $array[$id]))
			{
				return $result;
			}
		}
	}
	return false;
}