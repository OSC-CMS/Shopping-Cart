<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiReviews extends CartET
{
	/**
	 * Получить отзыв по ID
	 */
	public function getById($r_id)
	{
		$reviewsQuery = os_db_query("
			SELECT 
				* 
			FROM 
				".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd 
			WHERE 
				r.reviews_id = '".(int)$r_id."' AND r.reviews_id = rd.reviews_id
		");
		$review = os_db_fetch_array($reviewsQuery);

		$productsQuery = os_db_query("
			SELECT 
				products_name 
			FROM 
				".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
			WHERE 
				p.products_id = '".(int)$review['products_id']."' AND 
				p.products_id = pd.products_id AND 
				pd.language_id = '".(int)$_SESSION['languages_id']."'
			");
		$product = os_db_fetch_array($productsQuery);

		$result = os_array_merge($review, $product);
		return $result;
	}

	/**
	 * Сохранение отзыва
	 */
	public function save($post)
	{
		$data = array();

		$updateArray = array(
			'reviews_id' => os_db_prepare_input($_POST['reviews_id']),
			'products_id' => os_db_prepare_input($_POST['products_id']),
			'customers_name' => os_db_prepare_input($_POST['customers_name']),
			'status' => os_db_prepare_input($_POST['status']),
			'reviews_rating' => os_db_prepare_input($_POST['reviews_rating']),
			'reviews_read' => os_db_prepare_input($_POST['reviews_read']),
			'date_added' => os_db_prepare_input($_POST['date_added']),
			'last_modified' => 'now()',
		);

		os_db_perform(TABLE_REVIEWS, $updateArray, 'update', "reviews_id = '".(int)$_POST['reviews_id']."'");

		$updateInfoArray = array(
			'reviews_text' =>  os_db_prepare_input($_POST['reviews_text']),
		);

		os_db_perform(TABLE_REVIEWS_DESCRIPTION, $updateInfoArray, 'update', "reviews_id = '".(int)$_POST['reviews_id']."'");

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
		return $data;
	}

	/**
	 * Удаление отзыва
	 */
	public function delete($post)
	{
		if (is_array($post))
		{
			os_db_query("DELETE FROM ".TABLE_REVIEWS." WHERE reviews_id = '".(int)$post['reviews_id']."'");
			os_db_query("DELETE FROM ".TABLE_REVIEWS_DESCRIPTION." WHERE reviews_id = '".(int)$post['reviews_id']."'");

			$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Статус отзыва
	 */
	public function status($post)
	{
		if (is_array($post))
		{
			os_db_query("UPDATE ".TABLE_REVIEWS." SET status = '".(int)$post['status']."' WHERE reviews_id = '".(int)$post['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}
}