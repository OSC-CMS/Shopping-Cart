<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require_once (_LIB.'smarty/smarty.class.php');

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