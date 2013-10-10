<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class CartET
{
	private static $aClasses = array();

	private static $obj = array();

	public function __construct()
	{
		$this->loadClasses();
	}

	private function loadClasses()
	{
		$files = scandir(dir_path('includes').'_classes');

		$aFiles = array();
		for($i = 0; $i < sizeof($files); $i++) 
		{
			$files[$i] = str_replace('.class.php', '', $files[$i]);

			if ($files[$i] != "." && $files[$i] != ".." && $files[$i] != 'cartet')
			{
				$aFiles[strtolower($files[$i])] = ucfirst($files[$i]);
			}
		}

		self::$aClasses = $aFiles;
	}

	public function __get($name)
	{
		if(isset(self::$obj[$name]))
		{
			return(self::$obj[$name]);
		}

		if(!array_key_exists($name, self::$aClasses))
		{
			return null;
		}

		$class = self::$aClasses[$name];
		$className = 'api'.$class;

		include_once(dir_path('includes').'_classes/'.$class.'.class.php');

		self::$obj[$name] = new $className();

		return self::$obj[$name];
	}
}
?>