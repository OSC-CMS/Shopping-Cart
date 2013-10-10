<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
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
			$tpl = new osTemplate;
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
			$tpl->assign('aBreadCrumbs', $aBreadCrumbs);

			// Файл шаблона Не кэшируем
			$tpl->caching = 0;
			$getTemplate = $tpl->fetch(CURRENT_TEMPLATE.'/module/breadcrumb.html');

			return $getTemplate;
        }
    }
?>