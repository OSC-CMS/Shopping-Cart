<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiNews extends CartET
{
	/**
	 * Получить все новости
	 */
	public function getAll($status = 1, $lang = '', $query = false, $limit = '')
	{
		$lang = (!empty($lang)) ? $lang : $_SESSION['languages_id'];

		$result = "SELECT * FROM ".TABLE_LATEST_NEWS." WHERE status = '".(int)$status."' AND language = '".(int)$lang."' ORDER BY date_added DESC ".((!empty($limit)) ? 'LIMIT '.$limit : '')."";
	
		return ($query == false) ? $result : osDBquery($result);
	}

	/**
	 * Получить новость по id
	 */
	public function getById($news_id, $status = 1, $lang = '')
	{
		if (empty($news_id)) return false;

		$lang = (!empty($lang)) ? $lang : $_SESSION['languages_id'];

		$sql = osDBquery("SELECT * FROM ".TABLE_LATEST_NEWS." WHERE status = '".(int)$status."' AND language = '".(int)$lang."' AND news_id = ".(int)$news_id." LIMIT 1");

		if (os_db_num_rows($sql, true) > 0)
		{
			$result = os_db_fetch_array($sql, true);

			$this->newsData = $result;

			return $result;
		}
		else
			return false;
	}

	public function getData($arr = array())
	{
		$SEF_parameter = ((SEARCH_ENGINE_FRIENDLY_URLS == 'true')) ? '&headline='.os_cleanName($arr['headline']) : '';

		$arr['date_added'] = os_date_short($arr['date_added']);
		$arr['link'] = os_href_link(FILENAME_NEWS, 'news_id='.$arr['news_id'].$SEF_parameter, 'NONSSL');

		return apply_filter('build_news', $arr);
	}

	/**
	 * Сохранение новостей
	 */
	public function save($params)
	{
		if (empty($params)) return false;

		$action = $params['action'];

		$sql_data_array = array(
			'headline'   => os_db_prepare_input($params['headline']),
			'news_page_url'    => os_db_prepare_input($params['news_page_url']),
			'content'    => os_db_prepare_input($params['content']),
			'date_added' => ($action == 'edit') ? os_db_prepare_input($params['date_added']) : 'now()',
			'language'   => os_db_prepare_input($params['item_language']),
			'status'     => os_db_prepare_input($params['status'])
		);

		if (empty($params['images']))
		{
			if ($news_image = &os_try_upload('news_image', get_path('images').'news'))
			{
				// Удаляем старую картинку
				//@unlink(get_path('images').'news/'.$params['news_image_current']);

				$sql_data_array['news_image'] = os_db_prepare_input($news_image->filename);
			}
		}
		elseif (!empty($params['images']))
		{
			$sql_data_array['news_image'] = os_db_prepare_input($params['images']);
		}

		if ($action == 'edit')
			os_db_perform(TABLE_LATEST_NEWS, $sql_data_array, 'update', "news_id = '".(int)$params['id']."'");
		else
			os_db_perform(TABLE_LATEST_NEWS, $sql_data_array);

		set_news_url_cache();

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Статус новостей
	 */
	public function status($params)
	{
		if (is_array($params))
		{
			os_db_query("UPDATE ".TABLE_LATEST_NEWS." SET ".os_db_prepare_input($params['column'])." = '".(int)$params['status']."' WHERE news_id = '".(int)$params['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Удаление новости
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$news_id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("DELETE FROM ".TABLE_LATEST_NEWS." WHERE news_id = '".(int)$news_id."'");

		set_news_url_cache();

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}
?>