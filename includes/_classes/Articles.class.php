<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiArticles extends CartET
{
	public function __construct()
	{
	}

	/**
	 * Получить статью по id
	 */
	public function getById($articles_id, $status = 1, $lang = '')
	{
		if (empty($articles_id)) return false;

		$lang = (!empty($lang)) ? $lang : $_SESSION['languages_id'];

		$sql = osDBquery("
			SELECT 
				* 
			FROM 
				".TABLE_ARTICLES." a, 
				".TABLE_ARTICLES_DESCRIPTION." ad 
			WHERE 
				a.articles_status = '".(int)$status."' AND 
				a.articles_id = '".(int)$articles_id."' AND 
				ad.articles_id = a.articles_id AND 
				ad.language_id = '".(int)$lang."'
		");

		if (os_db_num_rows($sql, true) > 0)
		{
			// Обновляем количество просмотров
			os_db_query("UPDATE ".TABLE_ARTICLES_DESCRIPTION." SET articles_viewed = articles_viewed+1 WHERE articles_id = '".(int)$articles_id."' AND language_id = '".(int)$lang."'");

			$result = os_db_fetch_array($sql);

			$this->articlesData = $result;

			return $result;
		}
		else
			return false;
	}

	public function getData($arr = array())
	{
		$arr['articles_date_added'] = os_date_long($arr['articles_date_added']);
		$arr['link_article'] = os_href_link(FILENAME_ARTICLE_INFO, 'articles_id='.$arr['articles_id']);

		return $arr;
	}
}
?>
