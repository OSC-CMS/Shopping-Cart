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
 *	Вычисляет разницу между двух чисел
 *
 *	Вернет 30
 *	{numbers_difference number1=700 number2=1000 return=percent}
 *	Вернет 300
 *	{numbers_difference number1=700 number2=1000}
 */
function smarty_function_numbers_difference($params)
{
	if (empty($params)) return false;

	$result = array(
		'percent' => round((1 - $params['number1'] / $params['number2']) * 100, 1),
		'difference' => $params['number2']-$params['number1']
	);

	$return = ($params['return']) ? $params['return'] : 'difference';

	return $result[$return];
}