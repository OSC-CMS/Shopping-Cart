<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include ('includes/top.php');

// Получение списка задач для выполнения
$tasks = $cartet->cron->getPendingTasks();

// Если задач нет, то выходим
if (!$tasks) { exit; }

// Выполняем задачи по списку
foreach($tasks as $task)
{
	$class = $task['class'];
	$function = $task['function'];

	if (is_object($cartet->$class) && method_exists($cartet->$class, $function))
	{
		$cartet->$class->$function();
	}
	elseif (class_exists($class))
	{
		if (!is_object($class))
			$obj = new $class;
		else
			$obj = $class;

		if (method_exists($obj, $function))
		{
			$obj->$function();
		}
	}
	elseif (function_exists($function))
	{
		$function();
	}

	$cartet->cron->updateTask($task['id']);
}

echo 'OK';