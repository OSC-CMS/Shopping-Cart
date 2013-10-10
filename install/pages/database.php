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
        return check_db();
    }

    $result = array('html' => display('database', array()));

    return $result;
}

function check_db()
{
	$type = $_SESSION['install']['type'];

	// Установка
	if (isset($type) && $type == '1')
	{
		$db = $_POST['db'];
		define('DB_PREFIX', $db['prefix']);

		os_db_connect_installer($db['host'], $db['user'], $db['pass']);
		os_db_select_db($db['base']);

		// удаляем таблицы, если они есть
		include(dirname(dirname(__FILE__)).'/sql/install/db_delete.php');
		// заливаем новые таблицы
		include(dirname(dirname(__FILE__)).'/sql/install/db_struct.php');
		// заливаем необходимые данные
		include(dirname(dirname(__FILE__)).'/sql/install/db_default.php');

		// если выбрали установку демо-данных
		if (isset($_POST['demo']) && $_POST['demo'] == '1')
		{
			include(dirname(dirname(__FILE__)).'/sql/install/db_demo.php');
			@copy_folder(dirname(dirname(__FILE__)).'/sql/install/product_images', dirname(dirname(dirname(__FILE__))).'/images/product_images');
		}

	}
	// Обновление
	elseif (isset($type) && $type == '2')
	{
		include(dirname(dirname(dirname(__FILE__))).'/config.php');

		os_db_connect_installer(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
		os_db_select_db(DB_DATABASE);

		if ($_POST['update'] == '101_110')
			include(dirname(dirname(__FILE__)).'/sql/update/update_101_to_110.php');
		elseif ($_POST['update'] == '100_101')
			include(dirname(dirname(__FILE__)).'/sql/update/update_100_to_101.php');

		$_SESSION['install']['update'] = $_POST['update'];
	}
	$_SESSION['install']['db'] = $db;

	return array(
		'error' => false,
		'message' => t('db_11')
	);
}