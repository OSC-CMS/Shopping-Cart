<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class featured extends CartET
{

	/**
	 * Получить рекомендуемый по ID
	 */
	public function getById($f_id)
	{
		$product_query = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_FEATURED." f 
		WHERE 
			p.products_id = pd.products_id and 
			pd.language_id = '".(int)$_SESSION['languages_id']."' and 
			p.products_id = f.products_id and 
			f.featured_id = '".(int)$f_id."'
		");
		$product = os_db_fetch_array($product_query);
		return $product;
	}

	/**
	 * Сохранение рекомендуемого
	 */
	public function save($post)
	{
		$data = array();

		if (is_array($post))
		{
			// Обновляем или добавляем
			$action = (isset($post['featured_id']) && !empty($post['featured_id'])) ? 'save' : 'new';

			if ($action == 'save')
			{
				$updateArray = array(
					'featured_quantity' => (int)$post['featured_quantity'],
					'expires_date' => os_db_prepare_input($post['expires_date']),
					'featured_last_modified' => 'now()',
					'status' => (int)$post['status'],
				);

				os_db_perform(TABLE_FEATURED, $updateArray, 'update', "featured_id = '".(int)$post['featured_id']."'");
			}
			else
			{
				$updateArray = array(
					'products_id' => (int)$post['products_id'],
					'featured_quantity' => (int)$post['featured_quantity'],
					'expires_date' => os_db_prepare_input($post['expires_date']),
					'featured_date_added' => 'now()',
					'status' => (int)$post['status'],
				);

				os_db_perform(TABLE_FEATURED, $updateArray);
			}

			$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Удаление рекомендуемого
	 */
	public function delete($post)
	{
		if (is_array($post))
		{
			os_db_query("DELETE FROM ".TABLE_FEATURED." WHERE featured_id = '".os_db_input($post['featured_id'])."'");

			$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Статус рекомендуемого
	 */
	public function status($post)
	{
		if (is_array($post))
		{
			os_db_query("UPDATE ".TABLE_FEATURED." SET status = '".(int)$post['status']."', date_status_change = now() WHERE featured_id = '".(int)$post['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}
}