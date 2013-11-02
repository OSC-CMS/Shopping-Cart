<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

if (isset($_POST['products_id']))
{
	// обновление товаров
	for ($i = 0, $n = sizeof($_POST['products_id']); $i < $n; $i++)
	{
		if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array ())))
		{
			$_SESSION['cart']->remove($_POST['products_id'][$i]);
		}
		else
		{
			if ((int)$_POST['cart_quantity'][$i] > MAX_PRODUCTS_QTY) $_POST['cart_quantity'][$i] = MAX_PRODUCTS_QTY;
			$attributes = ($_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
			$_SESSION['cart']->add_cart($_POST['products_id'][$i], os_remove_non_numeric((int)$_POST['cart_quantity'][$i]), $attributes, false);
		}
	}

	$_SESSION['cartID'] = $_SESSION['cart']->cartID;

	require (DIR_WS_MODULES.'order_details_cart.php');

	echo json_encode($main_content);
}