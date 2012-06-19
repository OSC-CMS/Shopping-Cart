<?php
/*
Plugin Name: Jquery
Plugin URI: 
Version: 1.1
Author: Матецкий Евгений
Author URI: 
Plugin Group: System	
*/

    if ( get_option('check_jquery') == 'true' )
    {
        add_filter('head_array_detail', 'head_array_detail_jquery', 1);
    }

    if ( get_option('check_jquery_admin') == 'true' )
    {
        add_action('head_admin', 'head_admin_jquery', 1);
    }

    function head_array_detail_jquery ($head)
    {
        $head_new = array();

        foreach ($head as $value)
        {
            if ( isset($value['title']) )
            {
                $head_new[] = $value;
                add_js('jscript/jquery/jquery.js', $head_new, 'jquery');
            }
            else
            {
                $head_new[] = $value;
            }
        }

        return $head_new;
    }

    //jquery в админке
    function head_admin_jquery ()
    {
        $head = array();
        add_js('includes/javascript/jquery_1.3.2.js', $head, 'jquery');
        $head = osc_head_array ($head);
        _e( $head[0] );
    }

    //установка плагина
    function jquery_install()
    {
        add_option('check_jquery', 'true', 'radio', "array('true','false')");
        add_option('check_jquery_admin', 'true', 'radio', "array('true','false')");
    }
?>