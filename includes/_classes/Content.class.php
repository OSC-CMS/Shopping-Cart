<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiContent extends CartET
{
	/**
	 * Статус контента
	 */
	public function status($params)
	{
		if (is_array($params))
		{
			os_db_query("UPDATE ".TABLE_CONTENT_MANAGER." SET ".os_db_prepare_input($params['column'])." = '".(int)$params['status']."' WHERE content_id = '".(int)$params['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Удаление контента
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$content_id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("DELETE FROM ".TABLE_CONTENT_MANAGER." WHERE content_id = '".(int)$content_id."'");

		set_content_url_cache();

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление контента-товара
	 */
	public function deleteProduct($params)
	{
		if (!isset($params)) return false;
		$content_id = (is_array($params)) ? $params['id'] : $params;

		$contentFileQuery = os_db_query("SELECT content_file FROM ".TABLE_PRODUCTS_CONTENT." WHERE content_id = '".(int)$content_id."'");
		if (os_db_num_rows($contentFileQuery) > 0)
		{
			$contentFile = os_db_fetch_array($contentFileQuery);
			$file = DIR_FS_CATALOG.'media/products/'.$contentFile['content_file'];
			if (is_file($file))
				@unlink($file);
		}

		os_db_query("DELETE FROM ".TABLE_PRODUCTS_CONTENT." WHERE content_id = '".(int)$content_id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Боксы для быстрого изменения
	 */
	public function getFlags()
	{
		$flagsQuery = os_db_query("SELECT * FROM ".TABLE_CM_FILE_FLAGS."");

		$result = array();
		while ($f = os_db_fetch_array($flagsQuery)) {
			$result[] = array('value' => $f['file_flag'], 'text' => $f['file_flag_name']);
		}
		return $result;
	}

	/**
	 * Изменение бокса
	 */
	public function changeFlag($params)
	{
		if (empty($params)) return false;

		$content_id = (isset($params['content_id'])) ? $params['content_id'] : $params['pk'];
		$flag = (isset($params['file_flag'])) ? $params['file_flag'] : $params['value'];

		$sql_data_array = array('file_flag' => (int)$flag);
		os_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '".(int)$content_id."'");

		$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Файлы для быстрого изменения
	 */
	public function getFiles()
	{
		require_once(dir_path('func_admin').'file_system.php');
		$files = os_get_filelist(DIR_FS_CATALOG.'media/content/', '', array('index.html'));
		$result = array();
		foreach($files AS $f)
		{
			$result[] = array('value' => $f['id'], 'text' => $f['text']);
		}

		$default_array[] = array('id' => '','text' => '');
		$result = (count($result) == 0) ? $default_array : os_array_merge($default_array, $result);

		return $result;
	}

	/**
	 * Изменение файла
	 */
	public function changeFile($params)
	{
		if (empty($params)) return false;

		$content_id = (isset($params['content_id'])) ? $params['content_id'] : $params['pk'];
		$file = (isset($params['content_file'])) ? $params['content_file'] : $params['value'];

		$sql_data_array = array('content_file' => os_db_prepare_input($file));
		os_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '".(int)$content_id."'");

		$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение контента
	 */
	public function save($params)
	{
		if (empty($params)) return false;

		$languages = os_get_languages();

		$action = $params['action'];

		$group_ids = '';
		if(isset($params['groups'])) foreach($params['groups'] as $b){
			$group_ids .= 'c_'.$b."_group ,";
		}

		$customers_statuses_array = os_get_customers_statuses();
		if (strpos($group_ids, 'c_all_group'))
		{
			$group_ids = 'c_all_group,';
			for ($i=0; $n=sizeof($customers_statuses_array), $i<$n;$i++)
			{
				$group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
			}
		}

		$content_title = os_db_prepare_input($params['content_title']);
		$content_header = os_db_prepare_input($params['content_heading']);
		$content_url = os_db_prepare_input($params['content_url']);
		$content_page_url = os_db_prepare_input($params['content_page_url']);
		$content_text = os_db_prepare_input($params['content_text']);
		$coID = os_db_prepare_input($params['coID']);
		$content_status = os_db_prepare_input($params['status']);
		$content_language = os_db_prepare_input($params['languages_id']);
		$select_file = os_db_prepare_input($params['select_file']);
		$file_flag = os_db_prepare_input($params['file_flag']);
		$parent_check = os_db_prepare_input($params['parent_check']);
		$parent_id = os_db_prepare_input($params['parent_id']);
		$group_id = os_db_prepare_input($params['content_group']);
		$group_ids = $group_ids;
		$sort_order = os_db_prepare_input($params['sort_order']);
		$content_meta_title = os_db_prepare_input($params['content_meta_title']);
		$content_meta_description = os_db_prepare_input($params['content_meta_description']);
		$content_meta_keywords = os_db_prepare_input($params['content_meta_keywords']);

		for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
		{
			if ($languages[$i]['status'] == 1)
			{
				if ($languages[$i]['code'] == $content_language)
					$content_language = $languages[$i]['id'];
			}
		}

		$content_status = ($content_status == 'yes') ? 1 : 0;
		$parent_id = ($parent_check == 'yes') ? $parent_id : '0';

		if ($select_file != 'default')
			$content_file_name = $select_file;

		if ($content_file = &os_try_upload('file_upload', DIR_FS_CATALOG.'media/content/'))
		{
			$content_file_name = $content_file->filename;
		}

		$sql_data_array = array(
			'languages_id' => $content_language,
			'content_title' => $content_title,
			'content_heading' => $content_header,
			'content_page_url' => $content_page_url,
			'content_url' => $content_url,
			'content_text' => $content_text,
			'content_file' => $content_file_name,
			'content_status' => $content_status,
			'parent_id' => $parent_id,
			'group_ids' => $group_ids,
			'content_group' => $group_id,
			'sort_order' => $sort_order,
			'file_flag' => $file_flag,
			'content_meta_title' => $content_meta_title,
			'content_meta_description' => $content_meta_description,
			'content_meta_keywords' => $content_meta_keywords
		);
		if ($action == 'edit')
			os_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '".$coID."'");
		else
			os_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array);

		set_content_url_cache();

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение контента товара
	 */
	public function saveProduct($params)
	{
		if (empty($params)) return false;

		$languages = os_get_languages();

		$action = $params['action'];

		$group_ids = '';
		if (isset($params['groups']))
		{
			foreach($params['groups'] as $b)
			{
				$group_ids .= 'c_'.$b."_group ,";
			}
		}

		$customers_statuses_array = os_get_customers_statuses();
		if (strpos($group_ids, 'c_all_group'))
		{
			$group_ids = 'c_all_group,';
			for ($i=0; $n=sizeof($customers_statuses_array),$i<$n;$i++)
			{
				$group_ids .= 'c_'.$customers_statuses_array[$i]['id'].'_group,';
			}
		}

		$content_title = os_db_prepare_input($params['content_name']);
		$content_link = os_db_prepare_input($params['content_link']);
		$content_language = os_db_prepare_input($params['language']);
		$product = os_db_prepare_input($params['products_id']);
		$filename = os_db_prepare_input($params['file_name']);
		$coID = os_db_prepare_input($params['coID']);
		$file_comment = os_db_prepare_input($params['file_comment']);
		$select_file = os_db_prepare_input($params['select_file']);
		$group_ids = $group_ids;

		for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
		{
			if ($languages[$i]['status']==1)
			{
				if ($languages[$i]['code']==$content_language)
					$content_language = $languages[$i]['id'];
			}
		}

		// mkdir() wont work with php in safe_mode
		if  (!is_dir(DIR_FS_CATALOG.'media/products/'.$product.'/')) {

			$old_umask = umask(0);
			os_mkdirs(DIR_FS_CATALOG.'media/products/'.$product.'/', 0777);
			umask($old_umask);
		}

		if ($select_file == 'default')
		{
			if ($content_file = &os_try_upload('file_upload', DIR_FS_CATALOG.'media/products/'))
			{
				$content_file_name = $content_file->filename;
				$old_filename = $content_file->filename;
				$timestamp = str_replace('.','',microtime());
				$timestamp = str_replace(' ','',$timestamp);
				$content_file_name = $timestamp.strstr($content_file_name,'.');
				$rename_string = DIR_FS_CATALOG.'media/products/'.$content_file_name;
				rename(DIR_FS_CATALOG.'media/products/'.$old_filename,$rename_string);
				copy($rename_string, DIR_FS_CATALOG.'media/products/backup/'.$content_file_name);
			}

			if ($content_file_name == '')
				$content_file_name = $filename;
		}
		else
			$content_file_name = $select_file;

		$sql_data_array = array(
			'products_id' => $product,
			'group_ids' => $group_ids,
			'content_name' => $content_title,
			'content_file' => $content_file_name,
			'content_link' => $content_link,
			'file_comment' => $file_comment,
			'languages_id' => $content_language
		);

		if ($action == 'edit_products')
			os_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array, 'update', "content_id = '".(int)$coID."'");
		else
			os_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array);

		set_content_url_cache();

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}
}
?>