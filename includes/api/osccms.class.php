<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

class OscCms
{
	private $aClasses = array
	(
		'orders' => 'apiOrders',
	);

	private static $obj = array();

	public function __construct()
	{}

	public function __get($name)
	{
		if(isset(self::$obj[$name]))
		{
			return(self::$obj[$name]);
		}

		if(!array_key_exists($name, $this->aClasses))
		{
			return null;
		}

		$class = $this->aClasses[$name];

		include_once(_INCLUDES.'api/'.$class.'.class.php');

		self::$obj[$name] = new $class();

		return self::$obj[$name];
	}
}
?>