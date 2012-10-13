<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

    include(_FUNC.'str.php');

    class plugins
    {
        var $status = array();
        /*
        array('plugins_name'=>array('plugins options'))
        */
        var $info = array(); 
        var $icons = '';
        var $lang = array();
        var $options = array();
        var $_plug_array = array();

        var $name; //имя текущего плагина
        var $group; // main || themes || update
        var $dir; // текущее располодение плагина

        var $module; //$_GET['modules']
        var $type = 'admin';

        //Получение информации из языковых файлов
        function lang ($name = '')
        { 
            if ( !empty($this->module) )
            {
                $this->name = $this->module;
                $this->group = $this->info[$this->name]['group'];
                $this->set_dir();
            }
            
            $plugin_dir = get_path('catalog').$this->dir;

            if (!empty($_SESSION['language']))
            {
                if (is_file($plugin_dir.$_SESSION['language'].'.php'))
                {
                    require_once($plugin_dir.$_SESSION['language'].'.php');

                    if (isset($lang))
                    {
                        $this->lang[$this->name] = $lang;
                    }
                }
            }

        }

        function desc ($plugin_name)
        {
            $len = 50;

            _e($plugin_name,'');
        }

        function action ()
        {
            $info = '';

            if ($this->name == $this->module)
            {   
                _e( os_image( http_path('icons_admin') . 'icon_arrow_right.gif') );
            }
            else
            {		
                _e( '<a href="' . os_href_link(FILENAME_PLUGINS, 'module=' . $this->name) . '">'.os_image( http_path('icons_admin') . 'icon_info.gif').'</a>' );	
            }

            _e($info);
        }

        //сохранение полей 
        function save_options ()
        {
            if (!empty($_POST))
            {
                foreach ($_POST as $_name => $_value)
                {
                    $plugins_value = $_value;
                    $plugins_name = $_name;
                    $plugins_key = $this->module;

                    if (!empty($plugins_name))
                    {
                        os_db_query("UPDATE ".DB_PREFIX."plugins SET plugins_value='".$plugins_value."' where plugins_key = '".$plugins_key."' and plugins_name='".$plugins_name."'");
                    }
                }
            }

            return true;
        }

        //вывод статуса в cписки плагина вкл./выкл.
        function status ()
        {
            if(isset($this->info[$this->name]['status']) && ($this->info[$this->name]['status'] == 1))
            {
                _e(os_image(http_path('icons_admin') . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a onclick="return confirm(\'Действительно хотите удалить плагин?\')" href="'.FILENAME_PLUGINS.'?action=remove&module='.$this->name.'&group='.$this->info[$this->name]['group'].'">'. os_image(http_path('icons_admin'). 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>');
            }
            else
            {
                _e('<a href="'.FILENAME_PLUGINS.'?action=install&module='.$this->name.'&group='.$this->info[$this->name]['group'].'">' . os_image(http_path('icons_admin').'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin'). 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10));

            }

            return true;
        }

		// TODO: добавить возможность выключать\включать плагины без удаления инфы из БД.
		//вывод статуса в cписки плагина вкл./выкл.
        function viewUpdatePluginStatus ()
        {
            if(isset($this->info[$this->name]['status']) && ($this->info[$this->name]['status'] == 0))
            {
                _e(os_image(http_path('icons_admin') . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a onclick="return confirm(\'Действительно хотите удалить плагин?\')" href="'.FILENAME_PLUGINS.'?action=update_status&module='.$this->name.'&group='.$this->info[$this->name]['group'].'&set_status=0">'. os_image(http_path('icons_admin'). 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>');
            }
            else
            {
                _e('<a href="'.FILENAME_PLUGINS.'?action=update_status&module='.$this->name.'&group='.$this->info[$this->name]['group'].'&set_status=0">' . os_image(http_path('icons_admin').'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin'). 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10));

            }

            return true;
        }

        function check_update()
        {

            $database_version = db_version(); /* определение версии базы данных */

            if (isset($this->_plug_array['update']))
            {
                foreach ($this->_plug_array['update'] as $_value)
                {

                    if ($_value['name'] == $this->module && $_value['group']=='update' &&  $database_version != $_value['version'] && version_compare($_value['version'], $database_version) >= 0) 
                    {
                        return true;
                    }
                }
            }

            return false;
        }
        //вывод опций текущего плагина
        function option ()
        {
            _e('<table class="contentTable2" border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td class="infoBoxContent2">');

            $this->lang( $this->module );

            $_lang_array = $this->lang;
            _e('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:5px;margin-bottom:5px;">');

            if( $this->info[$this->module]['status'] == 1 )
            {
                _e('<form action="'.FILENAME_PLUGINS.'?action=save&module='.$this->module.'&group='.$this->info[$this->module]['group'].'" method="post">');

                $option = $this->get_option($this->module);

                if (!empty($option))
                {
                    //вывод опций

                    foreach ($option as $option_name => $option_values)
                    {
                        if (!empty($option_values['use_function']))
                        {
                            $_func = '$this->'.$option_values['use_function'];

                            if (isset($_lang_array[$this->module][$option_name]))
                            {
                                $_option_name = Htmlspecialchars($_lang_array[$this->module][$option_name]);

                                _e('<tr><td><b>'.$_option_name.'</b></td></tr>');
                            }

                            $_value_name = trim($option_name.'_desc');
                            if (isset($_lang_array[$this->module][ $_value_name ]))
                            { 
                                $_option_name_desc = addslashes(Htmlspecialchars($_lang_array[$this->module][ $_value_name ]));
                                _e('<tr><td><i>'.$_option_name_desc.'</i></td></tr>');
                            }

                            eval($_func.'\''.Htmlspecialchars($option_values['value']).'\''.', \''.addslashes($option_name)."');");
                            _e('<tr><td></td></tr>');
                            $_option_name = '';
                        }
                    }
                }

                if ($option['group']) unset($option['group']);				

                if (isset($this->info[$this->module]['process']) && count($this->info[$this->module]['process'])>0)
                {
                    _e('<tr><td align="center"><a class="button" href="'.FILENAME_PLUGINS.'?module='.$this->module.'&action=process"><span>'.PLUGINS_PROCESS.'</span></a></td></tr>');

                }


                if (count($option) == 1)
                {
                    foreach ($option as $_value_tmp)
                    {
                        if ( isset($_value_tmp['use_function']) && $_value_tmp['use_function'] == 'readonly(')
                        {
                            $option = '';
                        }
                    }
                }

                //если нет опций - не выводится кнопка сохранить опции

                if (!empty($option))
                {
                    _e('<tr><td align="center"><span class="button"><button type="submit" value="'.PLUGINS_SAVE.'">'.PLUGINS_SAVE.'</button></span></td></tr>');
                }

                _e('</form>');
                //кнопка удалить плагин
                _e('<tr><td align="center"><form onSubmit="return confirm(\'Действительно хотите удалить плагин?\')" action="'.FILENAME_PLUGINS.'?action=remove&group='.$this->group.'&module='.$this->module.'" method="post" name="plugin_delete"><span class="button"><button type="submit" value="'.PLUGINS_REMOVE.'">'.PLUGINS_REMOVE.'</button></span></form></td></tr>');		        
            }
            else
            { 
                if ($this->info[$this->module]['group'] == 'update' && !$this->check_update())
                {
                    echo '<center style="color:red;text-align:center; font-weight: bold; ">Обновление уже установлено!</center>';
                }
                else
                {
                    _e('<table border="0" width="100%">');
                    _e('<tr><td align="center"><a class="button" href="'.FILENAME_PLUGINS.'?action=install&group='.$this->group.'&module='.$this->module.'"><span>'.PLUGINS_INSTALL.'</span></a></td></tr>'); 
                }

            }
            _e('</table>');

            _e('</td></tr></table>');

            $this->option_desc($_lang_array);

            return true;
        }

        function option_desc($_lang_array)
        {
            $_content_start = '<table class="contentTable" border="0" width="237px" cellspacing="0" cellpadding="2" style="margin-top:10px;"><tr ><td class="infoBoxHeading">'.PLUGINS_DESCRIPTION.'</td></tr></table>';		
            $_content_start .= '<table class="contentTable2" border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContent2">';
            $_content_start .= '<table width="100%" border="0">';
            //описание модуля

            $_content = '';

            if (isset($this->info[$this->module]['title']) && !empty($this->info[$this->module]['title']))
            {
                $_content .= '<tr><td align="left"><b>Название</b>: '.$this->info[$this->module]['title'].'</td></tr>';		
            }

            if (isset($this->info[$this->module]['version']) && !empty($this->info[$this->module]['version']))
            {
                $_content .= '<tr><td align="left"><b>'.TABLE_HEADING_VERSION.'</b>: '.$this->info[$this->module]['version'].'</td></tr>';		
            }

            if (!empty($this->info[$this->module]['desc']))
            {
                $_content .= '<tr><td align="left"><b>'.PLUGINS_SHORT_DESCRIPTION.':</b></td></tr>';	
                $_content .= '<tr><td align="center">'.$this->info[$this->module]['desc'] .'</td></tr>';		
            }

            if (isset($_lang_array[$this->module][$this->module.'_desc']))
            {
                $_content .= '<tr><td align="left"><b>'.PLUGINS_DESCRIPTION.':</b></td></tr>';	
                $_content .= '<tr><td align="center">'.$_lang_array[$this->module][$this->module.'_desc'] .'</td></tr>';		
            }

            if (isset($this->info[$this->module]['author']) && !empty($this->info[$this->module]['author']))
            {
                if (isset($this->info[$this->module]['author_uri']) && !empty($this->info[$this->module]['author_uri']))
                {
                    $_content .= '<tr><td align="left"><b>'.PLUGINS_AUTHOR.'</b>: <a href="'.$this->info[$this->module]['author_uri'].'" class="author_uri" target="_blank">'.$this->info[$this->module]['author'].'</a></td></tr>';	
                }	
                else
                {
                    $_content .= '<tr><td align="left"><b>'.PLUGINS_AUTHOR.'</b>: '.$this->info[$this->module]['author'].'</td></tr>';	
                }				
            }

            $_content_end = '</table>';	
            $_content_end .= '</td></tr></table>';	

            if (!empty($_content))
            {
                _e($_content_start);
                _e($_content);
                _e($_content_end);
            }

        }

        function get_option ($key)
        {
            $options = array();

            $plugins_query = os_db_query('select plugins_key, plugins_name, plugins_value, sort_order, use_function from '.DB_PREFIX.'plugins where plugins_key=\''.$key.'\' and plugins_name <> \'show\' order by sort_order');

            while ($plugins_result = os_db_fetch_array($plugins_query))  
            {
                $options[$plugins_result['plugins_name']]['value'] = $plugins_result['plugins_value'];
                $options[$plugins_result['plugins_name']]['use_function'] = $plugins_result['use_function'];
            } 

            return $options;
        }

		// TODO: добавить возможность выключать\включать плагины без удаления инфы из БД.
		// Включение\выключение плагина БЕЗ удаления настроек из БД.
		function updatePluginStatus($_plugin_name = '', $_group = 'main')
		{
			//определяем получаемый статус
			$status = $_GET['set_status'];

			if (is_numeric($status))
			{
				//определяем имя устанавливаемого плагина
				if (!empty($_plugin_name))
					$this->name = $_plugin_name;
				else
					$this->name = $this->module;

				//определяем группу устанавливаемого плагина
				if (isset($_GET['group']) && !empty($_GET['group']))
					$this->group = $_GET['group'];
				else
					$this->group = $_group;

				os_db_query("UPDATE ".DB_PREFIX."plugins SET plugins_value = '".$status."' WHERE plugins_key = '".$this->name."' AND plugins_name = 'show'");
			}
			else
				return false;
		}

        //удалене модуля
        /*
        если нужно удалить какой то конкретный модуль. 
        указываем параметр $_plugin_name, а не берем из $this->module
        */
        function remove($_plugin_name = '', $_group = 'main')
        {
            //определяем имя устанавливаемого плагина
            if (!empty($_plugin_name))
				$this->name = $_plugin_name;
            else
				$this->name = $this->module;

            //определяем группу устанавливаемого плагина
            if (isset($_GET['group']) && !empty($_GET['group']))
				$this->group = $_GET['group'];
			else
				$this->group = $_group;

            switch ($this->group)
            {
                case 'main':
                    if (is_file(dir_path('plug').$this->name.'/'.$this->name.'.php')) 
                    {
                        $_plug_file = dir_path('plug').$this->name.'/'.$this->name.'.php';
                    }
                    elseif (is_file(dir_path('plug').$this->name.'.php'))
                    {
                        $_plug_file = dir_path('plug').$this->name.'.php';
                    }
                    else break;

                    require_once ($_plug_file);

                    if (function_exists($this->name.'_remove'))
                    {
                        $func = $this->name.'_remove';
                        $func();
                    }

                    os_db_query('delete from '.DB_PREFIX.'plugins where plugins_key=\''.$this->name.'\'');

                    break;


                case 'themes':
                    if ( is_file( dir_path('themes_c').'plugins/'.$this->name.'.php' ) ) 
                    {
                        $_plug_file = _CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'.php';
                    }
                    elseif (is_file(_CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'/'.$this->name.'.php'))
                    {
                        $_plug_file = _CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'/'.$this->name.'.php';
                    }
                    else break;

                    require_once($_plug_file);

                    if (function_exists($this->name.'_remove'))
                    {
                        $func = $this->name.'_remove';
                        $func();
                    }

                    os_db_query('delete from '.DB_PREFIX.'plugins where plugins_key=\''.$this->name.'\'');

                    break;

            }
        }

        //установка плагина
        /*
        если нужно установить какой то конкретный модуль. 
        указываем параметр $_plugin_name, а не берем из $this->module
        */
        function install ($_plugin_name = '', $_group = 'main' )
        { 
            //определяем имя устанавливаемого плагина
            if (!empty($_plugin_name)) $this->name = $_plugin_name;
            else $this->name = $this->module;

            
            //определяем группу устанавливаемого плагина
            if (isset($_GET['group']) && !empty($_GET['group'])) $this->group = $_GET['group']; else $this->group = $_group;

            switch ($this->group)
            {
                 case 'themes':
                    if (is_file(_CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'.php')) 
                    {
                        $_plug_file = _CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'.php';
                    }
                    elseif (is_file(_CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'/'.$this->name.'.php'))
                    {
                        $_plug_file = _CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'/'.$this->name.'.php';
                    }
                    else break;

                    require_once($_plug_file);

                    if (function_exists($this->name.'_install'))
                    {
                        $func = $this->name.'_install';
                        $func();
                    }

                    os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$this->name."', 'show','1',0,0, NULL);");
                    os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$this->name."', 'group','themes',0,0, NULL);");

                    break;

                case 'update':
                    if (is_file(dir_path('plug').$this->name.'/'.$this->name.'.php')) 
                    {
                        $_plug_file = dir_path('plug').$this->name.'/'.$this->name.'.php';
                    }
                    elseif (is_file(dir_path('plug').$this->name.'.php'))
                    {
                        $_plug_file = dir_path('plug').$this->name.'.php';
                    }

                    require_once($_plug_file);

                    break;
                    
                default:
                    if (is_file(dir_path('plug').$this->name.'/'.$this->name.'.php')) 
                    {
                        $_plug_file = dir_path('plug').$this->name.'/'.$this->name.'.php';
                    }
                    elseif (is_file(dir_path('plug').$this->name.'.php'))
                    {
                        $_plug_file = dir_path('plug').$this->name.'.php';
                    }
                    else break;

                    require_once ($_plug_file);

                    if (function_exists($this->name.'_install'))
                    {
                        $func = $this->name.'_install';
                        $func();
                    }

                    os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$this->name."', 'show','1',0,0, NULL);");
                    os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$this->name."', 'group','main',0,0, NULL);");

                    break;


               
            }
        }


        function process ($_plugin_name = '', $_group = 'main' )
        {
            //определяем имя устанавливаемого плагина
            if (!empty($_plugin_name)) $this->name = $_plugin_name;
            else $this->name = $this->module;

            //определяем группу устанавливаемого плагина
            if (isset($_GET['group']) && !empty($_GET['group'])) $this->group = $_GET['group']; else $this->group = $_group;

            if (!isset($this->info[$this->name]['process']) or count($this->info[$this->name]['process'])==0)  return 0;

            switch ($this->group)
            {
                case 'main':
                    $this->set_dir();

                    require_once (get_path('catalog').$this->dir.$this->name.'.php');

                    $_pocess = $this->info[$this->name]['process'][0];

                    if (function_exists($_pocess))
                    {
                        $func = $_pocess;
                        $func();
                    }

                    os_redirect(FILENAME_PLUGINS.'?module='.$this->name.'&group='.$this->group);

                    break;


                case 'themes':
                    $this->set_dir();

                    require_once(get_path('catalog').$this->dir.$this->name.'.php');

                    $_pocess = $this->info[$this->name]['process'][0];

                    if (function_exists($_pocess))
                    {
                        $func = $_pocess;
                        $func();
                    }

                    os_redirect(FILENAME_PLUGINS.'?module='.$this->name.'&group='.$this->group);
                    break;
            }   		
        }

        function set_dir ()
        {
            switch ($this->group)
            {
                case 'main':
                    if (is_file(get_path('plug').$this->name.'/'.$this->name.'.php')) 
                    {
                        $this->dir = 'modules/plugins/'.$this->name.'/';
                    }
                    elseif (is_file(get_path('plug').$this->name.'.php'))
                    {
                        $this->dir = 'modules/plugins/';
                    }
                    else break;

                    break;


                case 'themes':
                    if (is_file(get_path('catalog').'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'.php')) 
                    {
                        $this->dir = 'themes/'.CURRENT_TEMPLATE.'/plugins/';
                    }
                    elseif (is_file(get_path('catalog').'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'/'.$this->name.'.php'))
                    {
                        $this->dir = 'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'/';
                    }
                    else break;
                    break;
            }   	
        }

        function get_name () //возвращает список плагинов
        {
            $directory_array = array();

            if ($dir = @dir( dir_path('plug') )) 
            {
                while ($file = $dir->read())
                {  
                    if ($file != '.' && $file != '..' && $file != '.svn') 
                    {
                        if (is_dir( dir_path('plug').$file )) 
                        {
                            if (is_file( dir_path('plug') .$file.'/'.$file.'.php'))
                            {
                                $directory_array[] = array(
                                0 => $file,
                                1 =>  dir_path('plug') .$file.'/'.$file.'.php',
                                2 =>  http_path('plug').$file.'/',
                                'group' => 'main'
                                );
                            }
                        }
                        else
                        {
                            if(is_file(dir_path('plug').$file) && substr($file,count($file)-5,4) == '.php') 
                            {
                                $directory_array[] = array(
                                0 => substr($file, 0, count($file)-5),
                                1 => dir_path('plug') .$file,
                                2 => '',
                                'group' => 'main'
                                );
                            }
                        }

                    }
                }

                $dir->close();
            }

            sort( $directory_array );

            return $directory_array;
        }

        function get_plugins_theme ($themes = '') //возвращает список плагинов
        {
            if (empty($themes)) $themes = CURRENT_TEMPLATE;

            $directory_array = array();

            $_dir  = get_path('themes') . $themes.'/plugins/';

            if (is_dir($_dir))
            {
                if ($dir = dir($_dir)) 
                {
                    while ($file = $dir->read())
                    {  
                        if ($file != '.' && $file != '..' && $file != '.svn') 
                        {
                            if (is_dir($_dir.$file )) 
                            {
                                if (is_file($_dir.$file.'/'.$file.'.php'))
                                {
                                    $directory_array[] = array(
                                    0 => $file,
                                    1 => $_dir.$file.'/'.$file.'.php',
                                    2 => _HTTP.'themes/'.$themes.'/plugins/'.$file.'/',
                                    'group' => 'themes'
                                    );
                                }
                            }
                            else
                            {
                                if(is_file($_dir.$file) && substr($file,count($file)-5,4) == '.php') 
                                {
                                    $directory_array[] = array(
                                    0 => substr($file, 0, count($file)-5),
                                    1 => $_dir.$file,
                                    2=> '',
                                    'group' => 'themes'
                                    );
                                }
                            }

                        }
                    }

                    $dir->close();
                }


                sort( $directory_array );
            }

            return $directory_array;
        }

        function plugins ($param = '') // возвращает массив. информацию о всех плагинах
        { 
            $plugins_query = os_db_query('select plugins_key, plugins_name, plugins_value from '.DB_PREFIX.'plugins order by sort_order');

            while ($plugins_result = os_db_fetch_array($plugins_query,true))  
            {
                if (!empty($plugins_result['plugins_key']))
                {   
                    //определяем все статусы для плагинов
                    if ($plugins_result['plugins_name'] == 'show')
                    {
                        //статус плагина
                        if ((int)$plugins_result['plugins_value'] == 1)
                        {
                            $this->info[$plugins_result['plugins_key']]['status'] = 1;  
                        }
                        else
                        {
                            $this->info[$plugins_result['plugins_key']]['status'] = 0;
                        }
                    }
                    elseif ($plugins_result['plugins_name'] == 'group')
                    {
                        ///группа плагина
                        if (!empty($plugins_result['plugins_value']))
                        {
                            $this->info[$plugins_result['plugins_key']]['group'] = $plugins_result['plugins_value'];  
                        }
                    }
                    else
                    {
                        $this->options[$plugins_result['plugins_name']] = $plugins_result['plugins_value'];
                    }
                }
            }

            return true;
        }

        function textarea ($value, $name)
        { 
            $value = Htmlspecialchars($value);
            _e ('<tr><td><textarea class="round plugin" name="'.$name.'" cols="26" rows="10">'.$value.'</textarea></td></tr>');
        }   

        function readonly ($value, $name)
        { 
            $func = $name.'_readonly';

            if (function_exists($func))
            {
                _e ('<tr><td>');
                $func($value);
                _e ('</td></tr>');
            }
            else
            {
                _e ('<tr><td>'.$value.'</td></tr>');
            }  
        }

        function input ($value, $name)
        {
            $value = addslashes($value);
            $size = 15;
            if (mb_strlen($value) > 15) $size = 25;

            _e ('<tr><td><input size="'.$size.'" class="round plugin" type="text" name="'.$name.'" value="'.$value.'"></td></tr>');
        }	

        function radio ($_array, $value, $name)
        {
            $_lang_array = $this->lang;

            _e('<tr><td>');

            if (!empty($_array) && is_array($_array))
            {
                foreach ($_array as $_val)
                {
                    $__val = '';
                    //локализация поля
                    if (isset($_lang_array[$this->module][$_val])) $__val = $_lang_array[$this->module][$_val]; else $__val  = $_val;
                    if (strtolower(trim($_val)) == 'true') $__val = YES;
                    if (strtolower(trim($_val)) == 'false') $__val = NO;

                    if ($_val == $value)
                    {
                        _e('<input type="radio" name="'.$name.'" checked value="'.$_val.'"> <b>'.$__val.'</b>');
                    }
                    else
                    {
                        _e('<input type="radio" name="'.$name.'" value="'.$_val.'"> <b>'.$__val.'</b>');	
                    }
                }
            }

            _e('</td></tr>');

            return true;
        }	

        //резервирование настроек плагина
        function set_reserve_setting( $param = array() )
        {

        }

        function checkbox ($_array, $value, $name)
        {
            $_lang_array = $this->lang;
            _e('<tr><td>');

            if (!empty($_array) && is_array($_array))
            {
                _e('<select class="round plugin" name="'.$name.'">');
                foreach ($_array as $_val)
                {
                    $__val = '';
                    if ($_val == 'true') $__val = YES;
                    if ($_val == 'false') $__val = NO;

                    //локализация поля
                    if (isset($_lang_array[$this->module][$_val])) 
                    {  
                        $__val = $_lang_array[$this->module][$_val]; 
                    }
                    else
                    {
                        $__val  = $_val;
                    }

                    if ($_val == $value)
                    {
                        _e('<option selected value="'.$_val.'">'.$__val.'</option>');
                    }
                    else
                    {
                        _e('<option value="'.$_val.'">'.$__val.'</option>');	
                    }
                }
                _e('</select>');
            }

            _e('</td></tr>');

        }	

        function plug_array ( $themes = '' )
        {
            $plugins_file = array_merge($this->get_name (), $this->get_plugins_theme ($themes)); /* получения массива всех плагинов */

            $color = '';

            $this->_plug_array = array();

            for ($i = 0, $n = sizeof($plugins_file); $i < $n; $i++) 
            {
                $this->name = $plugins_file[$i][0];

                $plugin_data = get_plugin_data($plugins_file[$i][1]);
				
                $_group = trim($plugin_data['PluginGroup']);

                if ($plugins_file[$i]['group'] == 'main')
                {
                    if (!empty($_group)) /*print_r($this->_plug_array[$_group])*/; else $_group = 'main';
                }
                elseif($plugins_file[$i]['group']=='themes')
                {
                    $_group = 'themes';
                }
                $this->group = $_group;

                $this->set_dir();

                // echo $this->name.'<br>';
                // echo plugdir().'<br>'	;		  
                require_once($plugins_file[$i][1]);


                $this->info[$this->name]['title'] = $plugin_data['Title'];

                $this->info[$this->name]['desc'] = $plugin_data['Description'];
                $this->info[$this->name]['author'] = $plugin_data['Author'];
                $this->info[$this->name]['author_uri'] = $plugin_data['AuthorURI'];

                $this->_plug_array[$_group][$i]['title'] = $plugin_data['Title'];
                $this->_plug_array[$_group][$i]['desc'] = $plugin_data['Description'];
                $this->_plug_array[$_group][$i]['version'] = $plugin_data['Version'];
                $this->_plug_array[$_group][$i]['author'] = $plugin_data['Author'];
                $this->_plug_array[$_group][$i]['author_uri'] = $plugin_data['AuthorURI'];
                $this->_plug_array[$_group][$i]['group'] = $_group;
                $this->_plug_array[$_group][$i]['name'] = $this->name;

                if (empty($this->info[$this->name]['group'])) $this->info[$this->name]['group'] = $_group;

                $this->info[$this->name]['version'] = $plugin_data['Version'];

                //если не указан статус плагина, устанавливает статус в 0
                if (!isset($this->info[$this->name]['status']))  $this->info[$this->name]['status'] = 0;

                if ($_group == 'update') $color = '#bbffb0'; $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';

                $this->_plug_array[$_group][$i]['color'] = $color;

                if (is_file(get_path('catalog').$this->dir.'icon.png') && !empty($plugins_file[$i][2]))
                {
                    $this->_plug_array[$_group][$i]['icon'] =  '<img width="16px" alt="" height="16px" src="'.$plugins_file[$i][2].'icon.png'.'" border="0" />';
                }
                elseif (is_file(get_path('catalog').$this->dir.'icon.gif') && !empty($plugins_file[$i][2]))
                {
                    $this->_plug_array[$_group][$i]['icon'] =  '<img width="16px" alt="" height="16px" src="'.$plugins_file[$i][2].'icon.gif'.'" border="0" />';
                }
                else
                {
                    $this->_plug_array[$_group][$i]['icon'] = '<img width="16px" alt="" height="16px" src="'.http_path('icons_admin').'plugins/plugins_icons.png'.'" border="0" />';
                }

            }

            return $this->_plug_array;
        }  

        function multi_action ()
        {
            if (isset($_POST['action']) && $_POST['action']=='install')
            {
                if (!empty($_POST['plugins']))
                { 
                    for ($i=0; $i<=count($_POST['plugins'])-1; $i++)
                    {
                        $this->install($_POST['plugins'][$i], $this->info[$_POST['plugins'][$i]]['group']);
                    }

                    os_redirect(FILENAME_PLUGINS);
                }
                //print_r($_POST);
            }elseif (isset($_POST['action']) && $_POST['action']=='remove')
            {		     
                if (!empty($_POST['plugins']))
                { 
                    for ($i=0; $i<=count($_POST['plugins'])-1; $i++)
                    {   
                        $this->remove($_POST['plugins'][$i], $this->info[$_POST['plugins'][$i]]['group']);
                    }

                    os_redirect(FILENAME_PLUGINS);
                }

            }

        } 

        /* подключение активных плагинов */
        function require_plugins()
        {
            if (!empty($this->info) && count($this->info) > 0)
            {
                foreach ($this->info as $_name => $_value) //подключение активных модулей
                {
                    $this->name = $_name;
                    $this->group = @ $this->info[$_name]['group'];
                    if (empty($this->group)) $this->group = 'main';

                    switch ($this->group)
                    {
                        case 'main':
                            if (is_file( dir_path('plug') . $this->name.'/'.$this->name.'.php'))
                            {
                                $_plug_file = dir_path('plug') . $this->name.'/'.$this->name.'.php';
                                //указываем какая папка корневая для плагина
                                $this->dir  = 'modules/plugins/'.$this->name.'/';
                            }		 
                            elseif (is_file(dir_path('plug').$this->name.'.php'))
                            {
                                $_plug_file = dir_path('plug').$this->name.'.php';
                                //указываем какая папка корневая для плагина
                                $this->dir  = 'modules/plugins/';
                            }else break;

                            require_once($_plug_file);

                            $this->info[$this->name]['path'] = dir_path('plug').$this->name.'/';
                            $this->info[$this->name]['url'] =  http_path('catalog').'modules/plugins/'.$this->name.'/';

                            break;

                        case 'themes':

                            if (is_file( dir_path('themes') . CURRENT_TEMPLATE.'/plugins/'.$this->name.'.php')) 
                            {
                                $_plug_file = dir_path('themes').CURRENT_TEMPLATE.'/plugins/'.$this->name.'.php';
                                //указываем какая папка корневая для плагина
                                $this->dir = 'themes/'.CURRENT_TEMPLATE.'/plugins/'; 
                            }
                            elseif (is_file(dir_path('themes').CURRENT_TEMPLATE.'/plugins/'.$this->name.'/'.$this->name.'.php'))
                            {
                                $_plug_file = dir_path('themes').CURRENT_TEMPLATE.'/plugins/'.$this->name.'/'.$this->name.'.php';
                                //указываем какая папка корневая для плагина
                                $this->dir = 'themes/'.CURRENT_TEMPLATE.'/plugins/'.$this->name.'/';
                            }
                            else break;

                            require_once($_plug_file);

                            $this->info[$this->name]['path'] = dir_path('plug').$this->name.'/';
                            $this->info[$this->name]['url'] =  http_path('catalog').'modules/plugins/'.$this->name.'/';

                            break;
                    }

                }

            }
        }

        /* удаление всех модулей текущего шаблона */ 
        function themes_remove ( $themes = '' )
        {
            $_array = $this->plug_array();

            if (isset($_array['themes']))
            {
                foreach ($_array['themes'] as $_num => $_values)
                {
                    $this->name =  $_values['name'];

                    if (is_file(get_path('themes').$themes.'/plugins/'.$this->name.'.php')) 
                    {
                        $_plug_file = get_path('themes').$themes.'/plugins/'.$this->name.'.php';
                    }
                    elseif (is_file(get_path('themes').$themes.'/plugins/'.$this->name.'/'.$this->name.'.php'))
                    {
                        $_plug_file = get_path('themes').$themes.'/plugins/'.$this->name.'/'.$this->name.'.php';
                    }
                    else break;

                    require_once($_plug_file);

                    if (function_exists($this->name.'_remove'))
                    {
                        $func = $this->name.'_remove';
                        $func();
                    }

                    os_db_query('delete from '.DB_PREFIX.'plugins where plugins_key=\''.$this->name.'\'');

                }

            }
        }

        /* установка всех модулей текущего шаблона */ 
        function themes_install ( $themes = '')
        {
            $_array = $this->plug_array($themes);

            if (isset($_array['themes']))
            {
                foreach ($_array['themes'] as $_num => $_values)
                {
                    $this->name =  $_values['name'];

                    if (is_file( get_path('themes').$themes.'/plugins/'.$this->name.'.php')) 
                    {
                        $_plug_file = get_path('themes').$themes.'/plugins/'.$this->name.'.php';
                    }
                    elseif (is_file( get_path('themes').$themes.'/plugins/'.$this->name.'/'.$this->name.'.php'))
                    {
                        $_plug_file = get_path('themes').$themes.'/plugins/'.$this->name.'/'.$this->name.'.php';
                    }
                    else break;

                    // require_once($_plug_file);

                    if (function_exists($this->name.'_install'))
                    {
                        $func = $this->name.'_install';
                        $func();
                    }

                    os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$this->name."', 'show','1',0,0, NULL);");
                    os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$this->name."', 'group','themes',0,0, NULL);");
                }

            }
        }

    }

?>
