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
	// Установка
	if (empty($_POST['db']['base']) OR empty($_POST['db']['host']) OR empty($_POST['db']['user']))
	{
		return array(
			'error' => true,
			'message' => t('db_10')
		);
	}

	$db = $_SESSION['install']['db'];

	os_db_connect_installer($db['host'], $db['user'], $db['pass']);
	os_db_select_db($db['base']);

	// удаляем таблицы, если они есть
	include(PATH.'sql'.DS.'db_delete.php');
	// заливаем новые таблицы
	include(PATH.'sql'.DS.'db_struct.php');
	// заливаем необходимые данные
	include(PATH.'sql'.DS.'db_default.php');

	// если выбрали установку демо-данных
	if (isset($_POST['demo']) && $_POST['demo'] == '1')
	{
		include(PATH.'sql'.DS.'db_demo.php');
		@copy_folder(PATH.'sql'.DS.'product_images', ROOT_PATH.'images'.DS.'product_images');
	}

	return array(
		'error' => false,
		'message' => t('db_10')
	);
}