<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

/**
 * Сохраняет удаленное изображение
 */
function files_download_image($image, $path, $name = '')
{
	if (empty($image) OR empty($path)) return false;

	$image = str_replace(' ', '%20', $image);

	$file = pathinfo($image, PATHINFO_BASENAME);
	$ext = pathinfo($file, PATHINFO_EXTENSION);

	$cName = (!empty($name)) ? $name : '';
	$cFile = $cName.'_'.translit(urldecode(pathinfo($file, PATHINFO_FILENAME)));

	$new_file = $cFile.'.'.$ext;

	while (file_exists($path.$new_file))
	{
		$new_base = pathinfo($new_file, PATHINFO_FILENAME);
		if(preg_match('/_([0-9]+)$/', $new_base, $parts))
			$new_file = $cFile.'_'.($parts[1]+1).'.'.$ext;
		else
			$new_file = $cFile.'_1.'.$ext;
	}
	copy($image, $path.$new_file);

	return $new_file;
}

/**
 * Рекурсивно удаляет директорию
 */
function files_remove_directory($directory, $clear = false)
{
	if (substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory))
	{
		return false;
	}

	$handle = opendir($directory);

	while (false !== ($node = readdir($handle)))
	{
		if($node != '.' && $node != '..')
		{
			$path = $directory.'/'.$node;

			if (is_dir($path))
			{
				if (!files_remove_directory($path)) { return false; }
			}
			else
			{
				if (!@unlink($path)) { return false; }
			}
		}
	}

	closedir($handle);

	if ($clear == false)
	{
		if (!@rmdir($directory))
		{
			return false;
		}
	}

	return true;
}

/**
 * Возвращает дерево каталогов и файлов по указанному пути в виде
 */
function files_tree_to_array($path)
{
	$data = array();

	$dir = new DirectoryIterator($path);

	foreach ($dir as $node)
	{
		if ($node->isDir() && !$node->isDot())
		{
			$data[$node->getFilename()] = files_tree_to_array($node->getPathname());
		}
		else if ($node->isFile())
		{
			$data[] = $node->getFilename();
		}
	}

	return $data;
}

/**
 * Формирование удобного массива из $_FILES
 */
function files_make_files_array(&$file_post)
{
	$aFiles = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);

	for ($i = 0; $i < $file_count; $i++)
	{
		foreach ($file_keys as $key)
		{
			$aFiles[$i][$key] = $file_post[$key][$i];
		}
	}

	return $aFiles;
}