<?php
    /*
    #####################################
    #  OSC-CMS: Shopping Cart Software.
    #  Copyright (c) 2011-2012
    #  http://osc-cms.com
    #  http://osc-cms.com/forum
    #####################################
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

        function trail($separator = ' - ') 
        {
            do_action('breadcrumb_trail');

            $array = array();
            $array['trail'] = $this->_trail;
            $array['separator'] = $separator;

            $array = apply_filter('breadcrumb_trail', $array);
            $separator = $array['separator'];
            $this->_trail = $array['trail'];

            $trail_string = '';

            for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++) {
                if (isset($this->_trail[$i]['link']) && os_not_null($this->_trail[$i]['link'])) {
                    $trail_string .= '<a href="' . $this->_trail[$i]['link'] . '">' . $this->_trail[$i]['title'] . '</a>';
                } else {
                    $trail_string .= $this->_trail[$i]['title'];
                }

                if (($i+1) < $n) $trail_string .= $separator;
            }

            return  apply_filter('trail_string', $trail_string);
        }
    }
?>