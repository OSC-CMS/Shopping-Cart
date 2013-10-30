<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiNotes extends CartET
{
	/**
	 * Сохранение заметки
	 */
	public function save($params)
	{
		if (empty($params)) return false;

		os_db_perform(DB_PREFIX."admin_notes", array(
			'note' => os_db_prepare_input($params['note']),
			'customer' => (int)$_SESSION['customer_id'],
			'date_added' => 'now()',
			'status' => 1
		));

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление заметки
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("DELETE FROM ".DB_PREFIX."admin_notes WHERE id = '".(int)$id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}
?>