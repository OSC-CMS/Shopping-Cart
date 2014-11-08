<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

if (!$cartet->request->isAjax()) die();

if (isset($_POST['products_id']) && !empty($_POST['products_id']))
{
	$oProduct = new product($_POST['products_id']);

	$pPrice = $osPrice->GetPrice($oProduct->data['products_id'], false, 1, $oProduct->data['products_tax_class_id'], $oProduct->data['products_price'], 1, 0, $oProduct->data['products_discount_allowed']);

	$products_qty = str_replace('-', '', (int)$_POST['products_qty']);
	$products_qty = ($products_qty > 0) ? $products_qty : 1;

	$attrPrice = 0;
	if (isset($_POST['id']))
	{
		foreach ($_POST['id'] AS $oId => $vId)
		{
			$values = $osPrice->GetOptionPrice($oProduct->data['products_id'], $oId, $vId);
			$attrPrice += $values['price'];
		}
	}

	$total = $pPrice['price']*$products_qty+$attrPrice*$products_qty;
	$result = $osPrice->Format($total, true);

	echo json_encode($result);
}