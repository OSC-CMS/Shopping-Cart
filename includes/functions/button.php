<?php
    /*
    #####################################
    #  OSC-CMS: Shopping Cart Software.
    #  Copyright (c) 2011-2012
    #  http://osc-cms.com
    #  http://osc-cms.com/forum
    #  Ver. 1.0.0
    #####################################
    */

    function button_continue($_href = '')
    {
        if (empty($_href)) $_href = os_href_link(FILENAME_DEFAULT);

        if ($value != 'submit')
        {
            $_array = array('img' => 'button_continue.gif', 'href' => $_href, 'alt' => IMAGE_BUTTON_CONTINUE, 'code' => '');

            $_array = apply_filter('button_continue', $_array);	

            if (empty($_array['code']))
            {
                $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
            }								

        }
        else
        {
            $_array = array('img' => 'button_continue.gif', 'href' => '', 'alt' => IMAGE_BUTTON_CONTINUE, 'code' => '');


            $_array = apply_filter('button_continue', $_array);	

            if (empty($_array['code']))
            {
                $_array['code'] =  os_image_submit($_array['img'], $_array['alt']);
            }	
        }

        return $_array['code'];
    }

    function button_continue_submit()
    {
        $_array = array('img' => 'button_continue.gif', 'href' => '', 'alt' => IMAGE_BUTTON_CONTINUE, 'code' => '');

        $_array = apply_filter('button_continue', $_array);	

        if (empty($_array['code']))
        {
            $_array['code'] =  os_image_submit($_array['img'], $_array['alt']);
        }

        return $_array['code'];	   
    }

?>