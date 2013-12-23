<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiMenu extends CartET
{
	/**
	 * Получить все группы
	 */
	public function getGroups($arr = '')
	{
		$lang = (is_array($arr) && isset($arr['lang'])) ? $arr['lang'] : $_SESSION['languages_id'];

		$data = array();
		$sql = os_db_query("SELECT group_id, lang_title FROM ".DB_PREFIX."menu_group LEFT JOIN ".DB_PREFIX."menu_lang ON (lang_type = 1 AND lang_lang = '".(int)$lang."') WHERE lang_type_id = group_id");
		while ($row = os_db_fetch_array($sql))
		{
			$data[$row['group_id']] = $row['lang_title'];
		}
		return $data;
	}

	/**
	 * Название группы по ID
	 */
	public function getGroupTitleById($group_id, $lang = '')
	{
		$lang = (!empty($lang)) ? $lang : $_SESSION['languages_id'];

		$sql = os_db_query("SELECT lang_title FROM ".DB_PREFIX."menu_lang WHERE lang_lang = '".(int)$lang."' AND lang_type = '1' AND lang_type_id = '".(int)$group_id."'");
		$result = os_db_fetch_array($sql);
		return $result['lang_title'];
	}

	/**
	 * Статус меню
	 */
	public function status($post)
	{
		if (is_array($post))
		{
			os_db_query("UPDATE ".DB_PREFIX."menu SET menu_status = '".(int)$post['status']."' WHERE menu_id = '".(int)$post['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Получить пункты меню по id группы
	 */
	public function getByGroupId($array = array())
	{
		if (empty($array['group_id'])) return false;

		$lang = (!empty($array['lang'])) ? $array['lang'] : $_SESSION['languages_id'];
		$group_id = $array['group_id'];
		$status = ($array['status'] == true) ? " AND menu_status = 1 " : '';

		$data = array();
		$sql = os_db_query("SELECT * FROM ".DB_PREFIX."menu LEFT JOIN ".DB_PREFIX."menu_lang ON (lang_type = '0' AND lang_type_id = menu_id AND lang_lang = '".(int)$lang."') WHERE menu_group_id = '".(int)$group_id."' ".$status." ORDER BY menu_position ASC");
		if (os_db_num_rows($sql) > 0)
		{
			while ($row = os_db_fetch_array($sql))
				$data[] = $row;
		}
		return $data;
	}

	/**
	 * Получить группу по id для админки
	 */
	public function groupById($group_id)
	{
		// получаем сам пункт меню
		$groupSql = os_db_query("SELECT * FROM ".DB_PREFIX."menu_group WHERE group_id = '".(int)$group_id."'");
		$group = os_db_fetch_array($groupSql);

		// получаем переводы
		$menuLangsSql = os_db_query("SELECT * FROM ".DB_PREFIX."menu_lang WHERE lang_type = '1' AND lang_type_id = '".(int)$group_id."'");
		while($lang = os_db_fetch_array($menuLangsSql))
		{
			$langs[$lang['lang_lang']] = $lang;
		}

		$group['group_langs'] = $langs;
		return $group;
	}

	/**
	 * Получить меню по id для админки
	 */
	public function byId($post)
	{
		$menu_id = (is_array($post)) ? $post['menu_id'] : $post;

		// получаем сам пункт меню
		$menuSql = os_db_query("SELECT * FROM ".DB_PREFIX."menu WHERE menu_id = '".(int)$menu_id."'");
		$menu = os_db_fetch_array($menuSql);

		// получаем переводы
		$menuLangsSql = os_db_query("SELECT * FROM ".DB_PREFIX."menu_lang WHERE lang_type = '0' AND lang_type_id = '".(int)$menu_id."'");
		while($lang = os_db_fetch_array($menuLangsSql))
		{
			$langs[$lang['lang_lang']] = $lang;
		}

		$menu['menu_langs'] = $langs;
		return $menu;
	}

	/**
	 * Сохраняет меню
	 */
	public function save($post)
	{
		// Обновляем меню
		$menuArray = array(
			'menu_url' => os_db_prepare_input($post['menu_url']),
			'menu_class' => os_db_prepare_input($post['menu_class']),
			'menu_class_icon' => os_db_prepare_input($post['menu_class_icon']),
			'menu_status' => os_db_prepare_input($post['menu_status']),
		);
		os_db_perform(DB_PREFIX.'menu', $menuArray, 'update', "menu_id = '".(int)$post['menu_id']."'");

		// Обновляем переводы
		if (is_array($post['lang']))
		{
			foreach ($post['lang'] as $id => $value)
			{
				$menuLangArray = array('lang_title' => os_db_prepare_input($value));
				os_db_perform(DB_PREFIX.'menu_lang', $menuLangArray, 'update', "lang_lang = '".(int)$id."' AND lang_type = '0' AND lang_type_id = '".(int)$post['menu_id']."'");
			}
		}

		return true;
	}

	/**
	 * Добавление группы
	 */
	public function addGroup($post)
	{
		$menuArray = array(
			'group_status' => $post['group_status'],
		);
		os_db_perform(DB_PREFIX.'menu_group', $menuArray);
		$newId = os_db_insert_id();

		if (is_array($post['lang']))
		{
			foreach ($post['lang'] as $id => $value)
			{
				$menuLangArray = array(
					'lang_title' => os_db_prepare_input($value),
					'lang_type' => '1',
					'lang_type_id' => (int)$newId,
					'lang_lang' => (int)$id

				);
				os_db_perform(DB_PREFIX.'menu_lang', $menuLangArray);
			}
		}

		return true;
	}

	/**
	 * Сохранение группы
	 */
	public function saveGroup($post)
	{
		// Обновляем группу
		$menuArray = array(
			'group_status' => os_db_prepare_input($post['group_status']),
		);
		os_db_perform(DB_PREFIX.'menu_group', $menuArray, 'update', "group_id = '".(int)$post['group_id']."'");

		// Обновляем переводы
		if (is_array($post['lang']))
		{
			foreach ($post['lang'] as $id => $value)
			{
				$menuLangArray = array('lang_title' => os_db_prepare_input($value));
				os_db_perform(DB_PREFIX.'menu_lang', $menuLangArray, 'update', "lang_lang = '".(int)$id."' AND lang_type = '1' AND lang_type_id = '".(int)$post['group_id']."'");
			}
		}

		return true;
	}

	/**
	 * Добавление меню
	 */
	public function add($post, $type = 0)
	{
		// массовое добавление
		if ($type == 1)
		{
			$items = explode("\n", $post['items']);
			if (is_array($items))
			{
				foreach ($items as $item)
				{
					$newId = '';
					$menu = explode("|", $item);

					if (is_array($menu))
					{
						// Обновляем меню
						$groupId = (!empty($post['menu_group_id'])) ? $post['menu_group_id'] : 1;
						$menuArray = array(
							'menu_url' => os_db_prepare_input($menu[1]),
							'menu_group_id' => (int)$groupId,
							'menu_status' => 1,
						);
						os_db_perform(DB_PREFIX.'menu', $menuArray);
						$newId = os_db_insert_id();

						foreach ($this->language->get() as $lang)
						{
							$menuLangArray = array(
								'lang_title' => os_db_prepare_input($menu[0]),
								'lang_type' => '0',
								'lang_type_id' => (int)$newId,
								'lang_lang' => (int)$lang['languages_id']

							);
							os_db_perform(DB_PREFIX.'menu_lang', $menuLangArray);
						}
					}
				}
			}
		}
		else
		{
			// Добавляем меню
			$menuArray = array(
				'menu_url' => $post['menu_url'],
				'menu_group_id' => $post['menu_group_id'],
				'menu_class' => $post['menu_class'],
				'menu_class_icon' => $post['menu_class_icon'],
				'menu_status' => $post['menu_status'],
			);
			os_db_perform(DB_PREFIX.'menu', $menuArray);
			$newId = os_db_insert_id();

			if (is_array($post['lang']))
			{
				foreach ($post['lang'] as $id => $value)
				{
					$menuLangArray = array(
						'lang_title' => os_db_prepare_input($value),
						'lang_type' => '0',
						'lang_type_id' => (int)$newId,
						'lang_lang' => (int)$id
					);
					os_db_perform(DB_PREFIX.'menu_lang', $menuLangArray);
				}
			}
		}

		return true;
	}

	/**
	 * Сохранение позиций меню
	 */
	public function savePosition($post)
	{
		$data = array();
		if (isset($post['positions']))
		{
			$positions = $post['positions'];
			$this->updatePosition(0, $positions);

			$data = array('msg' => 'Успешно обновлено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	private function updatePosition($parent, $children)
	{
		$i = 1;
		foreach ($children as $k => $v)
		{
			$id = (int)$children[$k]['id'];

			// Обновляем позиции
			$menuArray = array(
				'menu_parent_id' => $parent,
				'menu_position' => $i,
			);
			os_db_perform(DB_PREFIX.'menu', $menuArray, 'update', "menu_id = '".(int)$id."'");

			if (isset($children[$k]['children'][0]))
			{
				$this->updatePosition($id, $children[$k]['children']);
			}
			$i++;
		}
	}

	/**
	 * Удаление пункта меню
	 */
	public function delete($params)
	{
		if (empty($params)) return false;

		$id = $params['id'];

		os_db_query("DELETE FROM ".DB_PREFIX."menu WHERE menu_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".DB_PREFIX."menu_lang WHERE lang_type = 0 AND lang_type_id = '".(int)$id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление группы меню, включая мне пункты
	 */
	public function deleteGroup($params)
	{
		if (empty($params)) return false;

		$id = $params['id'];

		os_db_query("DELETE FROM ".DB_PREFIX."menu_group WHERE group_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".DB_PREFIX."menu_lang WHERE lang_type = '1' AND lang_type_id = '".(int)$id."'");

		$sql = os_db_query("SELECT * FROM ".DB_PREFIX."menu LEFT JOIN ".DB_PREFIX."menu_lang ON (lang_type = '0' AND lang_type_id = menu_id) WHERE menu_group_id = '".(int)$id."'");
		if (os_db_num_rows($sql) > 0)
		{
			while ($row = os_db_fetch_array($sql))
			{
				os_db_query("DELETE FROM ".DB_PREFIX."menu WHERE menu_id = '".(int)$row['menu_id']."'");
				os_db_query("DELETE FROM ".DB_PREFIX."menu_lang WHERE lang_id = '".(int)$row['lang_id']."'");
			}
		}

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}
?>