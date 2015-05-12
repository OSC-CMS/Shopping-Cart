<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCron extends CartET
{
	// получаем все задания крона
	public function getTasks($is_admin = true)
	{
		$where = (!$is_admin) ? "WHERE status = 1" : "";

		$getTasks = os_db_query("SELECT * FROM ".DB_PREFIX."cron_tasks ".$where." ORDER BY id DESC");
		if (!os_db_num_rows($getTasks)) return false;

		$tasks = array();
		while($t = os_db_fetch_array($getTasks))
			$tasks[$t['id']] = $t;

		return $tasks;
	}

	public function saveTask($params)
	{
		if (!$params) return false;

		$sql = array(
			'title' => os_db_prepare_input(os_db_input($params['task']['title'])),
			'class' => os_db_prepare_input(os_db_input($params['task']['class'])),
			'function' => os_db_prepare_input(os_db_input($params['task']['function'])),
			'period' => (int)$params['task']['period'],
			'status' => (int)$params['task']['status'],
		);

		if ($params['action'] == 'add')
			$sql['new'] = 1;

		if ($params['action'] == 'edit' && !empty($params['task_id']))
			os_db_perform(DB_PREFIX.'cron_tasks', $sql, 'update', "id = '".(int)$params['task_id']."'");
		else
			os_db_perform(DB_PREFIX.'cron_tasks', $sql);

		return true;
	}

	// удаление задания
	public function deleteTask($task_id)
	{
		if (!$task_id) return false;

		os_db_query("DELETE FROM ".DB_PREFIX."cron_tasks WHERE id = '".(int)$task_id."'");
		return true;
	}

	// получаем задания которые нужно выполнять
	public function getPendingTasks()
	{
		$getTasks = os_db_query("SELECT * FROM ".DB_PREFIX."cron_tasks WHERE status = 1 ORDER BY id DESC");
		if (!os_db_num_rows($getTasks)) return false;

		$tasks = array();
		while($t = os_db_fetch_array($getTasks))
			$tasks[] = $t;

		$pending = array();
		foreach($tasks as $task)
		{
			if ($task['new'])
			{
				$pending[] = $task;
				continue;
			}

			$time_last_run = strtotime($task['date_last_run']);
			$time_now = time();

			$minutes_ago = floor(abs($time_last_run - $time_now) / 60 % 60);

			if ($minutes_ago >= $task['period'])
			{
				$pending[] = $task;
				continue;
			}
		}

		return $pending;
	}

	// обновляем время выполнения задания
	public function updateTask($id)
	{
		return os_db_perform(DB_PREFIX."cron_tasks", array(
			'new' => 0,
			'date_last_run' => 'now()'
		), 'update', "id = '".$id."'");
	}
}