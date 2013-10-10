<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class articles extends CartET
{
	/**
	 * Получить статью по ID
	 */
	public function getArticleById($t_id)
	{
		$article_query = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_ARTICLES." a, ".TABLE_ARTICLES_DESCRIPTION." ad 
		WHERE 
			a.articles_id = '".(int)$t_id."' AND 
			a.articles_id = ad.articles_id AND 
			ad.language_id = '".(int)$_SESSION['languages_id']."'
		");

		$article = os_db_fetch_array($article_query);
		return $article;
	}

	/**
	 * Получить категорию статей по ID
	 */
	public function getTopicById($t_id)
	{
		$topics_query = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_TOPICS." t, ".TABLE_TOPICS_DESCRIPTION." td 
		WHERE 
			t.topics_id = '".(int)$t_id."' AND 
			t.topics_id = td.topics_id AND 
			td.language_id = '".(int)$_SESSION['languages_id']."'
		");

		$topics = os_db_fetch_array($topics_query);
		return $topics;
	}

	/**
	 * Сохранение статей
	 */
	public function saveArticle($post)
	{
		// Обновление или добавление
		$action = ($post['articles_id']) ? 'update' : 'new';
		if ($action == 'update')
			$articles_id = (int)$post['articles_id'];
		else
			$articles_id = '';
	
		$articles_date_available = os_db_prepare_input($post['articles_date_available']);

		$articles_date_available = (date('Y-m-d') < $articles_date_available) ? $articles_date_available : 'null';

		$sql_data_array = array(
			'articles_date_available' => $articles_date_available,
			'articles_status' => os_db_prepare_input($post['articles_status']),
			'articles_page_url' => os_db_prepare_input($post['articles_page_url']),
			'sort_order' => os_db_prepare_input($post['sort_order'])
		);

		if ($action == 'new')
		{
			// Если публикуем в будущем, то ставим эту дату и как дату добавления
			if (isset($post['articles_date_available']) && os_not_null($post['articles_date_available']))
				$insert_sql_data = array('articles_date_added' => os_db_prepare_input($post['articles_date_available']));
			// Либо ставим текущую дату
			else
				$insert_sql_data = array('articles_date_added' => 'now()');

			$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

			os_db_perform(TABLE_ARTICLES, $sql_data_array);
			$articles_id = os_db_insert_id();

			os_db_query("insert into ".TABLE_ARTICLES_TO_TOPICS." (articles_id, topics_id) values ('".(int)$articles_id."', '".(int)$post['current_topic_id']."')");
		}
		elseif ($action == 'update')
		{
			$update_sql_data = array('articles_last_modified' => 'now()');
			// Если публикуем в будущем, то ставим эту дату и как дату добавления
			if (isset($post['articles_date_available']) && os_not_null($post['articles_date_available']))
			{
				$update_sql_data = array('articles_date_added' => os_db_prepare_input($post['articles_date_available']));
			}

			$sql_data_array = array_merge($sql_data_array, $update_sql_data);

			os_db_perform(TABLE_ARTICLES, $sql_data_array, 'update', "articles_id = '".(int)$articles_id."'");
		}

		$languages = os_get_languages();
		for ($i=0, $n=sizeof($languages); $i<$n; $i++)
		{
			if($languages[$i]['status'] == 1)
			{
				$language_id = $languages[$i]['id'];

				$sql_data_array = array(
					'articles_name' => os_db_prepare_input($post['articles_name'][$language_id]),
					'articles_description' => os_db_prepare_input($post['articles_description'][$language_id]),
					'articles_description_short' => os_db_prepare_input($post['articles_description_short'][$language_id]),
					'articles_url' => os_db_prepare_input($post['articles_url'][$language_id]),
					'articles_head_title_tag' => os_db_prepare_input($post['articles_head_title_tag'][$language_id]),
					'articles_head_desc_tag' => os_db_prepare_input($post['articles_head_desc_tag'][$language_id]),
					'articles_head_keywords_tag' => os_db_prepare_input($post['articles_head_keywords_tag'][$language_id])
				);

				if ($action == 'new')
				{
					$insert_sql_data = array(
						'articles_id' => (int)$articles_id,
						'language_id' => (int)$language_id
					);

					$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

					os_db_perform(TABLE_ARTICLES_DESCRIPTION, $sql_data_array);
				}
				elseif ($action == 'update')
				{
					os_db_perform(TABLE_ARTICLES_DESCRIPTION, $sql_data_array, 'update', "articles_id = '".(int)$articles_id."' and language_id = '".(int)$language_id."'");
				}
			}
		}

		if (USE_CACHE == 'true')
		{
			os_reset_cache_block('topics');
		}
	}

	/**
	 * Сохранение категории статей
	 */
	public function saveCategory($post)
	{
		$action = ($post['topics_id']) ? 'update' : 'new';
		if ($action == 'update')
			$topics_id = (int)$post['topics_id'];
		else
			$topics_id = '';

		$sql_data_array = array(
			'sort_order' => (int)$post['sort_order'],
			'topics_page_url' => os_db_prepare_input($post['topics_page_url'])
		);

		// Добавляем
		if ($action == 'new')
		{
			$insert_sql_data = array(
				'parent_id' => $post['parent_id'],
				'topics_page_url' => os_db_prepare_input($post['topics_page_url']),
				'date_added' => 'now()'
			);

			$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

			os_db_perform(TABLE_TOPICS, $sql_data_array);
			$topics_id = os_db_insert_id();
		}
		// Обновляем
		else
		{
			$topics_image = $post['topics_current_image'];
			// Удаляем изображение
			if ($post['del_cat_pic'] == 'yes')
			{
				$topics_image = '';

				$image_location = get_path('images').'articles/'.$post['topics_current_image'];
				if (is_file($image_location))
					@unlink($image_location);
			}

			$update_sql_data = array('last_modified' => 'now()', 'topics_image' => os_db_prepare_input($topics_image));

			$sql_data_array = array_merge($sql_data_array, $update_sql_data);

			os_db_perform(TABLE_TOPICS, $sql_data_array, 'update', "topics_id = '".(int)$topics_id."'");
		}

		$dir_images = get_path('images')."articles";
		if ($topics_image = &os_try_upload('topics_image', $dir_images))
		{
			os_db_query("UPDATE ".TABLE_TOPICS." SET topics_image ='".$topics_image->filename."' WHERE topics_id = '".(int)$topics_id."'");
		}

		$languages = os_get_languages();
		for ($i=0, $n=sizeof($languages); $i<$n; $i++)
		{
			if($languages[$i]['status']==1)
			{
				$language_id = $languages[$i]['id'];

				$sql_data_array = array(
					'topics_name' => os_db_prepare_input($post['topics_name'][$language_id]),
					'topics_heading_title' => os_db_prepare_input($post['topics_heading_title'][$language_id]),
					'topics_description' => os_db_prepare_input($post['topics_description'][$language_id])
				);

				if ($action == 'new')
				{
					$insert_sql_data = array(
						'topics_id' => $topics_id,
						'language_id' => $languages[$i]['id']
					);

					$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

					os_db_perform(TABLE_TOPICS_DESCRIPTION, $sql_data_array);
				}
				else
				{
					os_db_perform(TABLE_TOPICS_DESCRIPTION, $sql_data_array, 'update', "topics_id = '".(int)$topics_id."' and language_id = '".(int)$languages[$i]['id']."'");
				}
			}
		}

		// Обновляем кэш
		if (USE_CACHE == 'true')
		{
			os_reset_cache_block('topics');
		}

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		return $data;
	}

	/**
	 * Копирование статей
	 */
	public function articleCopy($post)
	{
		if (is_array($post) && isset($post['articles_id']) && isset($post['topics_id']))
		{
			$articles_id = os_db_prepare_input($post['articles_id']);
			$topics_id = os_db_prepare_input($post['topics_id']);
			$current_topic_id = os_db_prepare_input($post['current_topic_id']);

			if ($post['copy_as'] == 'link')
			{
				if ($topics_id != $current_topic_id)
				{
					$check_query = os_db_query("SELECT count(*) as total FROM ".TABLE_ARTICLES_TO_TOPICS." WHERE articles_id = '".(int)$articles_id."' AND topics_id = '".(int)$topics_id."'");
					$check = os_db_fetch_array($check_query);
					if ($check['total'] < '1')
					{
						os_db_query("insert into ".TABLE_ARTICLES_TO_TOPICS." (articles_id, topics_id) values ('".(int)$articles_id."', '".(int)$topics_id."')");
					}
				}
				else
				{
					return array('msg' => 'Нельзя создавать ссылку на статью в том же разделе, где находится статья.', 'type' => 'error');
				}
			}
			elseif ($post['copy_as'] == 'duplicate')
			{
				$article_query = os_db_query("SELECT articles_date_available, articles_page_url, sort_order FROM ".TABLE_ARTICLES." WHERE articles_id = '".(int)$articles_id."'");
				$article = os_db_fetch_array($article_query);

				os_db_query("insert into ".TABLE_ARTICLES." (articles_date_added, articles_date_available, articles_status, articles_page_url, sort_order) values (now(), '".os_db_input($article['articles_date_available'])."', '0', '', '".(int)$article['sort_order']."')");
				$dup_articles_id = os_db_insert_id();

				$description_query = os_db_query("SELECT language_id, articles_name, articles_description_short, articles_description, articles_url, articles_head_title_tag, articles_head_desc_tag, articles_head_keywords_tag FROM ".TABLE_ARTICLES_DESCRIPTION." WHERE articles_id = '".(int)$articles_id."'");
				while ($description = os_db_fetch_array($description_query))
				{
					os_db_query("insert into ".TABLE_ARTICLES_DESCRIPTION." (articles_id, language_id, articles_name, articles_description_short, articles_description, articles_url, articles_head_title_tag, articles_head_desc_tag, articles_head_keywords_tag, articles_viewed) values ('".(int)$dup_articles_id."', '".(int)$description['language_id']."', '".os_db_input($description['articles_name'])."', '".os_db_input($description['articles_description_short'])."', '".os_db_input($description['articles_description'])."', '".os_db_input($description['articles_url'])."', '".os_db_input($description['articles_head_title_tag'])."', '".os_db_input($description['articles_head_desc_tag'])."', '".os_db_input($description['articles_head_keywords_tag'])."', '0')");
				}

				os_db_query("insert into ".TABLE_ARTICLES_TO_TOPICS." (articles_id, topics_id) values ('".(int)$dup_articles_id."', '".(int)$topics_id."')");
			}

			if (USE_CACHE == 'true')
			{
				os_reset_cache_block('topics');
			}

			$data = array('msg' => 'Успешно скопировано!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Перенос статей
	 */
	public function articleMove($post)
	{
		if (is_array($post))
		{
			$articles_id = os_db_prepare_input($post['articles_id']);
			$new_parent_id = os_db_prepare_input($post['move_to_topic_id']);
			$current_topic_id = os_db_prepare_input($post['current_topic_id']);

			$duplicate_check_query = os_db_query("SELECT count(*) as total from ".TABLE_ARTICLES_TO_TOPICS." WHERE articles_id = '".(int)$articles_id."' AND topics_id = '".(int)$new_parent_id."'");
			$duplicate_check = os_db_fetch_array($duplicate_check_query);
			if ($duplicate_check['total'] < 1)
			{
				os_db_query("UPDATE ".TABLE_ARTICLES_TO_TOPICS." SET topics_id = '".(int)$new_parent_id."' WHERE articles_id = '".(int)$articles_id."' AND topics_id = '".(int)$current_topic_id."'");
			}

			$data = array('msg' => 'Успешно перенесено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Перенос категории статей
	 */
	public function categoryMove($post)
	{
		if (is_array($post) && isset($post['topics_id']) && ($post['topics_id'] != $post['move_to_topic_id']))
		{
			$topics_id = os_db_prepare_input($post['topics_id']);
			$new_parent_id = os_db_prepare_input($post['move_to_topic_id']);

			$path = explode('_', os_get_generated_topic_path_ids($new_parent_id));

			if (in_array($topics_id, $path))
			{
				return array('msg' => ERROR_CANNOT_MOVE_TOPIC_TO_PARENT, 'type' => 'error');
			}
			else
			{
				os_db_query("UPDATE ".TABLE_TOPICS." SET parent_id = '".(int)$new_parent_id."', last_modified = now() WHERE topics_id = '".(int)$topics_id."'");

				// Обновляем кэш
				if (USE_CACHE == 'true')
				{
					os_reset_cache_block('topics');
				}

				$data = array('msg' => 'Успешно перенесено!', 'type' => 'ok');
			}
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Удаление категории статей
	 */
	public function categoryDelete($post)
	{
		$topic_id = (is_array($post)) ? $post['topics_id'] : $post;

		$topic_image_query = os_db_query("SELECT topics_image FROM ".TABLE_TOPICS." WHERE topics_id = '".(int)$topic_id."'");
		$topic_image = os_db_fetch_array($topic_image_query);

		if (is_file(get_path('images')."articles/".$topic_image['topics_image']))
		{
			@unlink(get_path('images')."articles/".$topic_image['topics_image']);
		}

		// Удаляем статьи
		$getArticles = os_db_query("SELECT articles_id FROM ".TABLE_ARTICLES_TO_TOPICS." WHERE topics_id = '".(int)$topic_id."'");
		if (os_db_num_rows($getArticles) > 0)
		{
			while ($aId = os_db_fetch_array($getArticles))
			{
				$this->articleDelete($aId['articles_id']);
			}
		}

		os_db_query("DELETE FROM ".TABLE_TOPICS." WHERE topics_id = '".(int)$topic_id."'");
		os_db_query("DELETE FROM ".TABLE_TOPICS." WHERE parent_id = '".(int)$topic_id."'");
		os_db_query("DELETE FROM ".TABLE_TOPICS_DESCRIPTION." WHERE topics_id = '".(int)$topic_id."'");
		os_db_query("DELETE FROM ".TABLE_ARTICLES_TO_TOPICS." WHERE topics_id = '".(int)$topic_id."'");

		// Обновляем кэш
		if (USE_CACHE == 'true')
		{
			os_reset_cache_block('topics');
			os_reset_cache_block('also_purchased');
		}

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		return $data;
	}

	/**
	 * Удаление статей
	 */
	public function articleDelete($post)
	{
		$articles_id = (is_array($post)) ? $post['articles_id'] : $post;

		os_db_query("DELETE FROM ".TABLE_ARTICLES." WHERE articles_id = '".(int)$articles_id."'");
		os_db_query("DELETE FROM ".TABLE_ARTICLES_TO_TOPICS." WHERE articles_id = '".(int)$articles_id."'");
		os_db_query("DELETE FROM ".TABLE_ARTICLES_DESCRIPTION." WHERE articles_id = '".(int)$articles_id."'");

		// Обновляем кэш
		if (USE_CACHE == 'true')
		{
			os_reset_cache_block('topics');
			os_reset_cache_block('also_purchased');
		}

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');
		return $data;
	}

	/**
	 * Статус статей
	 */
	public function status($post)
	{
		if (is_array($post))
		{
			os_db_query("UPDATE ".TABLE_ARTICLES." SET articles_status = '".(int)$post['status']."', articles_last_modified = now() WHERE articles_id = '".(int)$post['id']."'");

			if (USE_CACHE == 'true')
			{
				os_reset_cache_block('topics');
			}

			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}
}