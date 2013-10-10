<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require_once (_LIB.'smarty/Smarty.class.php');

class osTemplate extends Smarty
{
	function __construct()
	{
		parent::__construct();
		$this->setTemplateDir(_THEMES);
		$this->setCompileDir(_CACHE);
		$this->setConfigDir(_LANG);
		$this->setCacheDir(_CACHE);
		$this->compile_check   =  TEMPLATE_COMPILE_CHECK;
		$this->setPluginsDir(_LIB.'smarty/plugins');
        $this->assign('app_name', 'osTemplate');
   }
}

?>