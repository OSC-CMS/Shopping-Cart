<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiFaq extends CartET
{
	/**
	 * Статус вопросов
	 */
	public function status($params)
	{
		if (is_array($params))
		{
			os_db_query("UPDATE ".TABLE_FAQ." SET ".os_db_prepare_input($params['column'])." = '".(int)$params['status']."' WHERE faq_id = '".(int)$params['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Удаление вопроса
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$faq_id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("DELETE FROM ".TABLE_FAQ." WHERE faq_id = '".(int)$faq_id."'");

		set_news_url_cache();

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение вопроса
	 */
	public function save($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$sql_data_array = array(
			'question'   => os_db_prepare_input($params['question']),
			'faq_page_url'    => os_db_prepare_input($params['faq_page_url']),
			'answer'    => os_db_prepare_input($params['answer']),
			'date_added' => ($action == 'edit') ? os_db_prepare_input($params['date_added']) : 'now()',
			'language'   => os_db_prepare_input($params['item_language']),
			'status'     => os_db_prepare_input($params['status'])
		);

		if ($action == 'edit')
			os_db_perform(TABLE_FAQ, $sql_data_array, 'update', "faq_id = '".(int)$params['id']."'");
		else
			os_db_perform(TABLE_FAQ, $sql_data_array);

		set_faq_url_cache();

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}
}
?>