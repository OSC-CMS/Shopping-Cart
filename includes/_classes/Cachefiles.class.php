<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCacheFiles extends CartET
{
	private $cache_path = 'cache/';

	public function set($key, $value, $cache_expire)
	{
		list($path, $file) = $this->getFile($key);

		@mkdir(DIR.$path, 0777, true);

		$data = array(
			'cache_expire' => $cache_expire,
			'time' => time(),
			'value' => serialize($value)
		);

		return file_put_contents(DIR.$file, serialize($data));
	}

	public function has($key)
	{
		list($path, $file) = $this->getFile($key);

		return file_exists($file);
	}

	public function get($key)
	{
		list($path, $file) = $this->getFile($key);

		$data = file_get_contents($file);

		if (!$data)
		{
			return false;
		}

		$data = unserialize($data);

		if (time() > $data['time'] + $data['cache_expire'])
		{
			$this->clean($key);
			return false;
		}

		return unserialize($data['value']);
	}

	public function clean($key = false)
	{
		if ($key)
		{
			$path = $this->cache_path.str_replace('.', '/', $key);
			return files_remove_directory($path);
		}
		else
		{
			return files_remove_directory($this->cache_path, true);
		}
	}

	public function getFile($key)
	{
		$path = $this->cache_path.str_replace('.', '/', $key);
		$file = explode('/', $path);

		$path = dirname($path);
		$file = $path.'/'.$file[sizeof($file)-1].'.dat';

		return array($path, $file);
	}
}
