<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require_once ('includes/top.php');

// Файл для модального окна
if (isset($_GET['ajax_page']) && !empty($_GET['ajax_page']))
{
	$loadPage = dirname(__FILE__).'/includes/ajax/'.$_GET['ajax_page'].'.ajax.php';
	if (is_file($loadPage))
	{
		include $loadPage;
	}
}

if (isset($_GET['ajax_action']) && !empty($_GET['ajax_action']))
{
	// Получаем параметр ajax_action
	$getAction = ($_GET['ajax_action']) ? $_GET['ajax_action'] : $_POST['ajax_action'];
	// Разбиваем параметр
	$params = explode('_', $getAction);

	if (is_array($params) && !empty($params))
	{
		// Название класса
		$className = $params[0];
		// Название метода
		$methodName = $params[1];
		// Тип запроса
		$paramType = $params[2];
		// Смотрим какой тип запроса
		$setData = ($paramType == 'get') ? $_GET : $_POST;

		// Есть ли файл класса
		if (is_file(CLS_NEW.$className.'.class.php'))
		{
			require_once(CLS_NEW.$className.'.class.php');
			$classObg = new $className();

			// Проверяем существование класса
			if (class_exists($className))
			{
				// Проверяем существование запрашиваемого метода
				if (method_exists($classObg, $methodName))
				{
					$data = $classObg->$methodName($setData);
					if ($data)
					{
						echo json_encode($data);
						exit();
					}
				}
			}
		}
		elseif (is_object($cartet->$className))
		{
			if (method_exists($cartet->$className, $methodName))
			{
				$data = $cartet->$className->$methodName($setData);
				if ($data)
				{
					echo json_encode($data);
					exit();
				}
			}
		}
	}
}

/********************************************************
	В зависимости от категории выводим товары
********************************************************/
if (isset($_GET['ajax_action']) && $_GET['ajax_action'] == 'load_products')
{
	$cat_id = $_GET['this_id'];
	$result = $cartet->products->getProductsByCategoryId($cat_id);
	echo json_encode($result);
	exit();
}
?>