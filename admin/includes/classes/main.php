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

    defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

    class main {
        var $error = array();
        var $style = array();

        //добавляет в даминку главное меню
        function top_menu ()
        {
            global $messageStack;

            if ($messageStack->size > 0) 
            {
                echo $messageStack->output();
            }

            require( dir_path('catalog') . 'admin/includes/top_menu.php');

            return true;
        }

        function bottom()
        {
            _e ('</div>');

            if (DISPLAY_PAGE_PARSE_TIME == 'true') 
            { 
                global $query_counts;
                $time_start = explode(' ', PAGE_PARSE_START_TIME);
                $time_end = explode(' ', microtime());
                $parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
                echo '<center>'.PARSE_TIME.' '. $parse_time . ', '.QUERIES.': ' . $query_counts . '</center>';
            }

            //показывает кол. потребляемой памяти в админке
            if (DISPLAY_MEMORY_USAGE == 'true')
            {
                if ( function_exists('memory_get_usage') )
                {
                    echo  '<center>'.TEXT_MEMORY_USAGE.round(memory_get_usage()/1024/1024, 2) . 'MB</center>';
                }
            }

            if (DISPLAY_DB_QUERY == 'true')
            {

                global $db_query;

                echo "<CENTER><div style='overflow: scroll; width: 60%; height: 200px; text-align: left;border: 1px dotted blue;'>";

                arsort ($db_query);

                $_db_query = array();
                if ( count($db_query) > 0 )
                {
                    foreach ($db_query as $v1 => $v2)
                    {
                        $v1 = str_replace('select', '<font color="#ff1493">select</font>', $v1 );
                        $v1 = str_replace('from', '<font color="#ff1493">from</font>', $v1 );
                        $v1 = str_replace('where', '<font color="#ff1493">where</font>', $v1 );
                        $_db_query[ $v1 ] = $v2; 
                    }
                }

                foreach ($_db_query as $v1 => $v2)
                {
                    echo '<font color="green">('.$v2['num'].')</font> <font color="red">('.$v2['time'].')</font> <font color="blue">'.$v1."</font><br />";
                }

                echo "</div></center><br /><br />";
            }

            _e ('<center><span style="font-size:12px;color:#3366CC;font-family: Verdana,Arial,sans-serif;font-size: 12px;font-weight: normal;">&copy; '.date('Y').' <a href="http://osc-cms.com" target="_blank" title="OSC-CMS" style="font-size:12px;color:#3366CC;">OSC-CMS</a></span></center>');

            return true;
        }

        //добавление стилей с именем $styleю.css
        function style ($style, $param = false)
        {
            if ($param)
            {
                $this->style[] = 'themes/'.ADMIN_TEMPLATE.'/styles/'.$style.'.css';
            }
            else
            {
                _e ('<link rel="stylesheet" type="text/css" href="themes/'.ADMIN_TEMPLATE.'/styles/'.$style.'.css">');
            }
            return true;
        }

        //добавляет favicon
        function favicon ()
        {
            _e ('<link rel="shortcut icon" href="favicon.ico" />');
        }

        // формируется тег <head></head>
        function head ()
        {
            global $os_action;
            global $PHP_SELF;

            _e ('<!DOCTYPE html>');
            _e ('<html>');
            _e ('<head>');
            $this->favicon();
            _e ('<meta http-equiv="Content-Type" content="text/html; charset='.$_SESSION['language_charset'].'">');

            if (TITLES != TITLE." : HEADING_TITLE")  
            {
                _e ('<title>'.TITLES.'</title>');
            }
            else
            {
                _e ('<title>'.TITLE.' : '.HEADING_TITLE.'</title>');
            }

            $this->style('style');
            $this->style('menu');

            _e ('<script type="text/javascript" src="'._HTTP.'jscript/jquery/jquery.js"></script>');
            _e ('<script type="text/javascript" src="includes/general.js"></script>');


            // if ( count( $os_action[ $tag ] ) >1 )     ksort($os_action[ $tag ]);
            // if ( count( $os_action[ $tag ][ $priority ] ) >1 ) sort( $os_action[ $tag ][ $priority ] );

            if ( count($os_action['head_admin']) > 0 )
            {
                $os_action1 = array();
                foreach ( $os_action['head_admin'] as $val2 => $val1)
                {
                    $os_action1[ $val1 ][]  = $val2;
                }


                ksort( $os_action1 );

                foreach ($os_action1 as $val2 => $va9)
                { 
                    if ( count($va9) > 0 )
                    {
                        foreach ($va9 as $val)
                        {
                            if (function_exists($val))
                            {
                                global $os_action_plug;
                                global $p;

                                $p->name = $os_action_plug[$val];
                                $p->group = $p->info[$p->name]['group'];
                                $p->set_dir();
                                $val();
                            }
                            else
                            {
                                $this->error[] = 'no function '.$val;
                            }
                        } 
                    }


                }
            }

            _e ('</head>');
            _e ('<body bgcolor="#FFFFFF">');

        }

        function breadcrumb()
        {
            global $top_menu;

            return true;
        }

        function lang_menu() //Панель с выбором языка
        {

            _e('<form action="" id="lang_form" method="post"><input type="hidden" id="lang_a" name="lang_a" value="ru">');

            if($_SESSION['language_admin'] == 'ru')
            {
                _e('<span onclick="document.getElementById(\'lang_a\').value = \'ru\';"><img width="14px" height="11px" alt="ru" border="0"  class="img_lang" src="'. http_path('icons_admin').'lang/ru.gif'.'" /></span>');  	 
            }
            else
            { 
                _e('<span onclick="document.getElementById(\'lang_a\').value = \'ru\';document.getElementById(\'lang_form\').submit();"><img  class="img_lang_a" width="14px" height="11px" alt="ru" border="0"    src="'. http_path('icons_admin').'lang/ru.gif'.'" /></span>');  
            }


            if($_SESSION['language_admin'] == 'en')
            {
                _e('<span onclick="document.getElementById(\'lang_a\').value = \'en\';"><img width="14px" alt="en" border="0" height="11px" class="img_lang" src="'. http_path('icons_admin').'lang/en.gif'.'" /></span>');;  		 
            }
            else
            { 
                _e('<span onclick="document.getElementById(\'lang_a\').value = \'en\';document.getElementById(\'lang_form\').submit();"><img  class="img_lang_a" width="14px" height="11px" alt="en" border="0" src="'. http_path('icons_admin').'lang/en.gif'.'" /></span>');  
            }	
            _e ('</form>');
            return true;
        }

        function fly_menu($url, $name, $target="_blank")
        {
            if (!empty($target))
            {
                _e('<a target="'.$target.'" style="color:#4378a1;position:absolute;top:100px;right:3%;" id="fly_menu" href="'.$url.'">'.$name.'</a>'); 
            }
            else
            {
                _e('<a style="color:#4378a1;position:absolute;top:100px;right:3%;" id="fly_menu" href="'.$url.'">'.$name.'</a>'); 
            }
        }

        function crutch ()
        {
            //нету костылей пока что
        }

        function heading($img, $text)
        {
            echo '<h1 class="contentBoxHeading"><img border="0" height="16px" width="16px" alt="" src="'.get_path('icons_admin', 'http').$img.'" />'.$text.'</h1>';
        }

    }
?>