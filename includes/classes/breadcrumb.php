<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

    class breadcrumb 
    {
        var $_trail;

        function breadcrumb() 
        {
            $this->reset();
            do_action('breadcrumb_init');
        }

        function reset() 
        {
            do_action('breadcrumb_reset');
            $this->_trail = array();
        }

        function add($title, $link = '') 
        {
            do_action('breadcrumb_add');

            $this->_trail[] = apply_filter('breadcrumb_info', array('title' => $title, 'link' => $link));
        }

        function trail() 
        {
			global $osTemplate;
            do_action('breadcrumb_trail');

            $array = array();
            $array['trail'] = $this->_trail;

            $array = apply_filter('breadcrumb_trail', $array);
            $this->_trail = $array['trail'];

			$aBreadCrumbs = array();
            for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++)
			{
				// Массив с "хлебными крошками"
				$aBreadCrumbs[] = $this->_trail[$i];
            }

			// Передаем массив в файл шаблона breadcrumb.html
			$osTemplate->assign('aBreadCrumbs', $aBreadCrumbs);

			// Файл шаблона Не кэшируем
			$osTemplate->caching = 0;
			$getTemplate = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/breadcrumb.html');

			return $getTemplate;
        }
    }
?>