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
		if (empty($_POST['db']['base']) OR empty($_POST['db']['host']) OR empty($_POST['db']['user']))
		{
			return array(
				'error' => true,
				'message' => t('db_11')
			);
		}

		$db = $_SESSION['install']['db'];

		os_db_connect_installer($db['host'], $db['user'], $db['pass']);
		os_db_select_db($db['base']);

		// удаляем таблицы, если они есть
		include(PATH.'sql'.DS.'install'.DS.'db_delete.php');
		// заливаем новые таблицы
		include(PATH.'sql'.DS.'install'.DS.'db_struct.php');
		// заливаем необходимые данные
		include(PATH.'sql'.DS.'install'.DS.'db_default.php');

		// если выбрали установку демо-данных
		if (isset($_POST['demo']) && $_POST['demo'] == '1')
		{
			include(PATH.'sql'.DS.'install'.DS.'db_demo.php');
			@copy_folder(PATH.'sql'.DS.'install'.DS.'product_images', ROOT_PATH.'images'.DS.'product_images');
		}
	}

	// Обновление
	elseif (isset($type) && $type == '2')
	{
		include(ROOT_PATH.'config.php');

		os_db_connect_installer(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
		os_db_select_db(DB_DATABASE);

		if ($_POST['update'] == '101_110')
			include(PATH.'sql'.DS.'update'.DS.'update_101_to_110.php');
		elseif ($_POST['update'] == '100_101')
			include(PATH.'sql'.DS.'update'.DS.'update_100_to_101.php');

		$_SESSION['install']['update'] = $_POST['update'];
	}

	return array(
		'error' => false,
		'message' => t('db_11')
	);
}