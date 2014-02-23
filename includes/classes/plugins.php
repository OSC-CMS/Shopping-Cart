<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
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
    var $allOptions = array();

    var $name; //имя текущего плагина
    var $group; // main || themes || update
    var $dir = 'modules/'; // текущее располодение плагина

    var $module; //$_GET['modules']
    var $type = 'admin';

    //Получение информации из языковых файлов
    function lang()
    {
        if ( !empty($this->module) )
        {
            $this->name = $this->module;
            $this->group = $this->info[$this->name]['group'];
        }

        $plugin_dir = get_path('catalog').'modules/plugins/'.$this->name.'/';

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

    function desc($plugin_name)
    {
        $len = 50;

        _e($plugin_name,'');
    }

    //сохранение полей
	function save_options()
	{
		if (!empty($_POST))
		{
			foreach ($_POST as $_name => $_value)
			{
				if (is_array($_value))
				{
					$_value = implode("|", $_value);
				}

				$plugins_value = $_value;
				$plugins_name = $_name;
				$plugins_key = $this->module;

				if (!empty($plugins_name))
				{
					os_db_query("UPDATE ".DB_PREFIX."plugins SET plugins_value = '".$plugins_value."' where plugins_key = '".$plugins_key."' and plugins_name='".$plugins_name."'");
				}
			}
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
	function option($plugin = '')
	{
		$module = ($plugin) ? $plugin : $this->module;
		$this->name = $module;
		$this->lang($module);
		$_lang_array = $this->lang;

		if ($this->info[$module]['status'] == 1)
		{
			//$option = $this->get_option($module);
			$getAllOption = $this->getAllOption();
			$option = $getAllOption[$module];//$this->get_option($module);

			if (!empty($option))
			{
				$optionsArray = array();
				foreach ($option as $option_name => $option_values)
				{
					if (!empty($option_values['use_function']) && $option_values['use_function'] != 'NULL' && $option_values['use_function'] != 'readonly(')
					{
						$_func = '$returnEval = $this->'.$option_values['use_function'];

						if (isset($_lang_array[$module][$option_name]))
						{
							$_option_name = htmlspecialchars($_lang_array[$module][$option_name]);
						}
						else
							$_option_name = $option_name;

						$_value_name = trim($option_name.'_desc');
						if (isset($_lang_array[$module][ $_value_name ]))
						{
							$_option_name_desc = addslashes(htmlspecialchars($_lang_array[$module][ $_value_name ]));
						}

						eval($_func.'\''.htmlspecialchars($option_values['value']).'\''.', \''.addslashes($option_name)."');");

						$optionsArray[] = array(
							'name' => $_option_name,
							'option' => $option_name,
							'desc' => $_option_name_desc,
							'value' => $returnEval,
						);
					}
				}
			}

			if (count($option) == 1)
			{
				foreach ($option as $_value_tmp)
				{
					if (isset($_value_tmp['use_function']) && $_value_tmp['use_function'] == 'readonly(')
					{
						$option = '';
					}
				}
			}
		}
		else
		{
			if ($this->info[$module]['group'] == 'update' && !$this->check_update())
			{
				//echo '<center style="color:red;text-align:center; font-weight: bold; ">РћР±РЅРѕРІР»РµРЅРёРµ СѓР¶Рµ СѓСЃС‚Р°РЅРѕРІР»РµРЅРѕ!</center>';
			}
		}

		return array(
			'options' => $optionsArray,
			'options_info' => $this->option_desc($_lang_array),
		);
	}

    function option_desc($_lang_array)
    {
        //$_content_start = '<table class="contentTable" border="0" width="237px" cellspacing="0" cellpadding="2" style="margin-top:10px;"><tr ><td class="infoBoxHeading">'.PLUGINS_DESCRIPTION.'</td></tr></table>';
       // $_content_start .= '<table class="contentTable2" border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContent2">';
      //  $_content_start = '<table width="100%" border="0">';
        //описание модуля

      //  $_content = '';

        //if (isset($this->info[$this->module]['title']) && !empty($this->info[$this->module]['title']))
       // {
       //     $_content .= '<tr><td align="left"><b>Название</b>: '.$this->info[$this->module]['title'].'</td></tr>';
       // }

       // if (isset($this->info[$this->module]['version']) && !empty($this->info[$this->module]['version']))
       // {
       //     $_content .= '<tr><td align="left"><b>'.TABLE_HEADING_VERSION.'</b>: '.$this->info[$this->module]['version'].'</td></tr>';
       // }

       // if (!empty($this->info[$this->module]['desc']))
       // {
       //     $_content .= '<tr><td align="left"><b>'.PLUGINS_SHORT_DESCRIPTION.':</b></td></tr>';
      //      $_content .= '<tr><td align="center">'.$this->info[$this->module]['desc'] .'</td></tr>';
       // }

       // if (isset($_lang_array[$this->module][$this->module.'_desc']))
      //  {
       //     $_content .= '<tr><td align="left"><b>'.PLUGINS_DESCRIPTION.':</b></td></tr>';
      //      $_content .= '<tr><td align="center">'.$_lang_array[$this->module][$this->module.'_desc'] .'</td></tr>';
       // }

       // if (isset($this->info[$this->module]['author']) && !empty($this->info[$this->module]['author']))
      //  {
       //     if (isset($this->info[$this->module]['author_uri']) && !empty($this->info[$this->module]['author_uri']))
       //     {
       //         $_content .= '<tr><td align="left"><b>'.PLUGINS_AUTHOR.'</b>: <a href="'.$this->info[$this->module]['author_uri'].'" class="author_uri" target="_blank">'.$this->info[$this->module]['author'].'</a></td></tr>';
       //     }
      //      else
      //      {
       //         $_content .= '<tr><td align="left"><b>'.PLUGINS_AUTHOR.'</b>: '.$this->info[$this->module]['author'].'</td></tr>';
      //      }
      //  }

       // $_content_end = '</table>';
      //  $_content_end .= '</td></tr></table>';

       // if (!empty($_content))
       // {
       //     _e($_content_start);
       //     _e($_content);
      //      _e($_content_end);
      //  }
		return false;
    }

	function getReadonlyOption()
	{
        $options = array();

        $plugins_query = os_db_query("select plugins_key, plugins_name, plugins_value, use_function from ".DB_PREFIX."plugins where use_function = 'readonly(' AND plugins_name <> 'show'");

        while ($plugins_result = os_db_fetch_array($plugins_query))
        {
            $options[$plugins_result['plugins_key']] = $plugins_result;
        }

        return $options;
	}

	function getReadonly()//use_function'] != 'readonly(
	{
		$option = $this->getReadonlyOption();

		if (!empty($option))
		{
			$optionsArray = array();
			foreach ($option as $option_name => $option_values)
			{
				//echo $option_values['plugins_name'].'<br />';
				if (!empty($option_values['use_function']) && $option_values['use_function'] != 'NULL')
				{
					$_func = '$returnEval = $this->'.$option_values['use_function'];

					eval($_func.'\''.htmlspecialchars($option_values['value']).'\''.', \''.addslashes($option_values['plugins_name'])."');");

					if (!empty($returnEval))
					{
						$optionsArray[$option_name] = $returnEval;
					}
				}
			}
		}

		return $optionsArray;
	}

    function get_option($key)
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

	/**
	 * Получение всех опций плагинов
	 */
	function getAllOption($gorup = false)
	{
		if (!empty($this->allOptions)) return $this->allOptions;

		$options = array();
		$plugins_query = os_db_query("SELECT * FROM ".DB_PREFIX."plugins WHERE plugins_name <> 'show' AND use_function != 'readonly(' ORDER BY sort_order");

		while ($plugins_result = os_db_fetch_array($plugins_query))
		{
			$options[$plugins_result['plugins_key']][$plugins_result['plugins_name']]['value'] = $plugins_result['plugins_value'];
			$options[$plugins_result['plugins_key']][$plugins_result['plugins_name']]['use_function'] = $plugins_result['use_function'];

			if ($gorup == false) unset($options[$plugins_result['plugins_key']]['group']);
		}

		$this->allOptions = $options;

        return $options;
	}

	function clearAllOptions()
	{
		$this->allOptions = array();
	}

	// TODO: добавить возможность выключать\включать плагины без удаления инфы из БД.
	// Включение\выключение плагина БЕЗ удаления настроек из БД.
	function updatePluginStatus($_plugin_name, $set_status)
	{
		//определяем получаемый статус
		$status = $set_status;

		if (is_numeric($status))
		{
			//определяем имя устанавливаемого плагина
			if (!empty($_plugin_name))
				$this->name = $_plugin_name;
			else
				$this->name = $this->module;

			os_db_query("UPDATE ".DB_PREFIX."plugins SET plugins_value = '".$status."' WHERE plugins_key = '".os_db_prepare_input($this->name)."' AND plugins_name = 'show'");
		}
		else
			return false;
	}

    //удалене модуля
    /*
    если нужно удалить какой то конкретный модуль.
    указываем параметр $_plugin_name, а не берем из $this->module
    */
    function remove($_plugin_name = '')
    {
        //определяем имя устанавливаемого плагина
        if (!empty($_plugin_name))
			$this->name = $_plugin_name;
        else
			$this->name = $this->module;

        if (is_file(dir_path('plug').$this->name.'/'.$this->name.'.php'))
            $_plug_file = dir_path('plug').$this->name.'/'.$this->name.'.php';
        elseif (is_file(dir_path('plug').$this->name.'.php'))
            $_plug_file = dir_path('plug').$this->name.'.php';
        else
            return false;

        require_once($_plug_file);

        if (function_exists($this->name.'_remove'))
        {
            $func = $this->name.'_remove';
            $func();
        }

        os_db_query('delete from '.DB_PREFIX.'plugins where plugins_key=\''.$this->name.'\'');

		$this->clearAllOptions();
    }

    //установка плагина
    /*
    если нужно установить какой то конкретный модуль.
    указываем параметр $_plugin_name, а не берем из $this->module
    */
    function install($_plugin_name = '')
    {
        //определяем имя устанавливаемого плагина
        if (!empty($_plugin_name))
			$this->name = $_plugin_name;
        else
			$this->name = $this->module;

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
        //os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$this->name."', 'group','main',0,0, NULL);");

		$this->clearAllOptions();
    }

    function process($_plugin_name = '')
    {
        //определяем имя устанавливаемого плагина
        if (!empty($_plugin_name))
			$this->name = $_plugin_name;
        else
			$this->name = $this->module;

        if (!isset($this->info[$this->name]['process']) or count($this->info[$this->name]['process'])==0)
			return 0;

        require_once (get_path('catalog').$this->dir.$this->name.'.php');

        $_pocess = $this->info[$this->name]['process'][0];

        if (function_exists($_pocess))
        {
            $func = $_pocess;
            $func();
        }

        os_redirect(FILENAME_PLUGINS.'?module='.$this->name);

		$this->clearAllOptions();
    }

    function get_name() //возвращает список плагинов
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

    function plugins($param = '') // возвращает массив. информацию о всех плагинах
    {
        $plugins_query = os_db_query('select plugins_key, plugins_name, plugins_value from '.DB_PREFIX.'plugins order by sort_order');

        while ($plugins_result = os_db_fetch_array($plugins_query))
        {
            if (!empty($plugins_result['plugins_key']))
            {
                //определяем все статусы для плагинов
                if ($plugins_result['plugins_name'] == 'show')
                {
                    //статус плагина
                    if ((int)$plugins_result['plugins_value'] == 1)
                        $this->info[$plugins_result['plugins_key']]['status'] = 1;
                    else
                        $this->info[$plugins_result['plugins_key']]['status'] = 0;
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

	// Textarea
	function textarea($value, $name)
	{
		return '<textarea class="input-block-level" id="'.$name.'" name="'.$name.'">'.htmlspecialchars($value).'</textarea>';
	}

	// Readonly
	function readonly($value, $name)
	{
		$func = $name.'_readonly';

		return (function_exists($func)) ? $func($value) : $value;
	}

	// Input
	function input($value, $name)
	{
		return '<input class="input-block-level" type="text" id="'.$name.'" name="'.$name.'" value="'.addslashes($value).'" />';
	}

	// Radio
	function radio($_array, $value, $name)
	{
		$_lang_array = $this->lang;

		if (!empty($_array) && is_array($_array))
		{
			$return = '';
			foreach ($_array as $_val)
			{
				$__val = '';

				if (isset($_lang_array[$this->module][$_val]))
					$__val = $_lang_array[$this->module][$_val];
				else
					$__val = $_val;

				if (strtolower(trim($_val)) == 'true') $__val = YES;
				if (strtolower(trim($_val)) == 'false') $__val = NO;

				$checked = ($_val == $value) ? 'checked' : '';
				$return .= '<label class="radio"><input type="radio" name="'.$name.'" value="'.$_val.'" '.$checked.'> '.$__val.'</label>';
			}
		}
		return $return;
	}

	// Checkbox
	function checkbox($_array, $value, $name)
	{
		$_lang_array = $this->lang;

		$return = '';
		$aValues = array();
		if (!empty($_array) && is_array($_array))
		{
			$aValues = explode('|', $value);

			foreach ($_array as $_val)
			{
				$__val = '';

				if (isset($_lang_array[$this->module][$_val]))
					$__val = $_lang_array[$this->module][$_val];
				else
					$__val = $_val;

				$checked = (in_array($_val, $aValues)) ? 'checked' : '';
				$return .= '<label class="checkbox"><input type="checkbox" name="'.$name.'['.$_val.']" value="'.$_val.'" '.$checked.'> '.$__val.'</label>';
			}

			return $return;
		}
		else
			return false;
	}

	// Select
	function select($_array, $value, $name)
	{
		$_lang_array = $this->lang;

		$return = '';
		if (!empty($_array) && is_array($_array))
		{
			$return .= '<select class="input-block-level" id="'.$name.'" name="'.$name.'">';
			foreach ($_array as $_val)
			{
				$__val = '';
				if ($_val == 'true') $__val = YES;
				if ($_val == 'false') $__val = NO;

				if (isset($_lang_array[$this->module][$_val]))
					$__val = $_lang_array[$this->module][$_val];
				else
					$__val = $_val;

				$selected = ($_val == $value) ? 'selected' : '';
				$return .= '<option value="'.$_val.'" '.$selected.'>'.$__val.'</option>';
			}
			$return .= '</select>';
			return $return;
		}
		else
			return false;
	}

    //резервирование настроек плагина
    function set_reserve_setting( $param = array() )
    {

    }

    function plug_array()
    {
        $plugins_file = $this->get_name();

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

            $this->group = $_group;

           // $this->set_dir();

            // echo $this->name.'<br>';
            // echo plugdir().'<br>'	;
            require_once($plugins_file[$i][1]);

            $this->info[$this->name]['title'] = $plugin_data['Title'];

            $this->info[$this->name]['desc'] = $plugin_data['Description'];
            $this->info[$this->name]['author'] = $plugin_data['Author'];
            $this->info[$this->name]['author_uri'] = $plugin_data['AuthorURI'];
            $this->info[$this->name]['plugin_uri'] = $plugin_data['PluginURI'];

            $this->_plug_array[$_group][$i]['title'] = $plugin_data['Title'];
            $this->_plug_array[$_group][$i]['desc'] = $plugin_data['Description'];
            $this->_plug_array[$_group][$i]['version'] = $plugin_data['Version'];
            $this->_plug_array[$_group][$i]['author'] = $plugin_data['Author'];
            $this->_plug_array[$_group][$i]['author_uri'] = $plugin_data['AuthorURI'];
            $this->_plug_array[$_group][$i]['plugin_uri'] = $plugin_data['PluginURI'];
            $this->_plug_array[$_group][$i]['group'] = $_group;
            $this->_plug_array[$_group][$i]['name'] = $this->name;

            if (empty($this->info[$this->name]['group'])) $this->info[$this->name]['group'] = $_group;

            $this->info[$this->name]['version'] = $plugin_data['Version'];

            //если не указан статус плагина, устанавливает статус в 0
            if (!isset($this->info[$this->name]['status']))
				$this->info[$this->name]['status'] = 0;

            if (is_file(get_path('catalog').$this->dir.'icon.png') && !empty($plugins_file[$i][2]))
            {
                $this->_plug_array[$_group][$i]['icon'] =  '<img src="'.$plugins_file[$i][2].'icon.png'.'" border="0" alt="" />';
            }
            elseif (is_file(get_path('catalog').$this->dir.'icon.gif') && !empty($plugins_file[$i][2]))
            {
                $this->_plug_array[$_group][$i]['icon'] =  '<img src="'.$plugins_file[$i][2].'icon.gif'.'" border="0" alt="" />';
            }
            else
            {
                $this->_plug_array[$_group][$i]['icon'] = '<img src="'.http_path('icons_admin').'plugins/plugins_icons.png'.'" border="0" alt="" />';
            }
        }

        return $this->_plug_array;
    }

    function multi_action()
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
        }
		elseif (isset($_POST['action']) && $_POST['action']=='remove')
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
            }

        }
    }
}

?>
