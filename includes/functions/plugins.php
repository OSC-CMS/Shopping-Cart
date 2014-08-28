<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

    function add_option( $name, $value, $func = 'input', $_array = '')
    {
        global $p; 
        global $_plugin_sort;

        if (!empty($p->name))
        {
            if (empty($_array))
            {
                if ( isset($_plugin_sort[$p->name]) ) $_plugin_sort[$p->name]++;
                else $_plugin_sort[$p->name] = 1;

                os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$p->name."', '".$name."','".$value."','".$_plugin_sort[$p->name]."','0', '".$func."(');");
            }
            else
            {
                @ $_plugin_sort[$p->name]++;
                if ($func == 'radio' or $func == 'checkbox')
                {
                    $_array = mysql_real_escape_string(trim($_array));
                    os_db_query("insert ".DB_PREFIX."plugins (plugins_key, plugins_name, plugins_value, sort_order, sort_plugins, use_function) VALUES ('".$p->name."', '".$name."','".$value."','".$_plugin_sort[$p->name]."',0, '".$func."($_array,"."');");
                }
            }
        }	   

        return true;   
    }

    function get_option($_key)
    {
        global $p;

        if (!empty($p->options) && isset($p->options[$_key]))
        {
            return $p->options[$_key];
        }
        else return false;
    }

    function update_option ($_key, $_value)
    {
        $_key = os_db_prepare_input($_key);
        $_value = os_db_prepare_input($_value);

        if ($_key != 'show')
        {
            os_db_query("update `".DB_PREFIX."plugins` set plugins_value='".$_value."' where plugins_name='".$_key."';");
        }
        return true;
    }

    //возвращает версию магазина
    function db_version()
    {
        if ( is_file( dir_path('catalog').'VERSION' ) )
        {
            $_version = @ file_get_contents ( dir_path('catalog').'VERSION' );
            $_version = str_replace('CartET/', '', $_version);
        }
        else
        {
            $_version = '3.0.0';
        }

        return $_version;
    }
?>