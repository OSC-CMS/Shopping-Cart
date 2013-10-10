<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCache extends CartET
{
	public function clean()
	{
		set_all_cache();

		if ($d = opendir(_CACHE))
		{
			while (false !== ($file = readdir($d)))
			{
				if ($file != "." && $file != ".." && $file !=".htaccess" && $file != "system")
				{
					os_delete_file(_CACHE . $file);
				}
			}
			closedir($d);
		}

		$data = array('msg' => 'Кэш успешно очищен!', 'type' => 'ok');

		return $data;
	}
}
?>