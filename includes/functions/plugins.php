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

    function get_option ($_key)
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
            $_version = str_replace('OSC-CMS/', '', $_version);
        }
        else
        {
            $_version = '3.0.0';
        }

        return $_version;
    }

    function plugins_switch()
    {
    ?>
    <tr>
        <td colspan="13" valign="bottom">
            <img src="images/arrow_ltr.png" border="0" width="38" height="22"/>
            <a href="#" onclick="javascript:SwitchCheck();"><?php echo PLUGINS_SWITCH_ALL; ?></a>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;
            <select name="action" dir="ltr" onchange="this.form.submit();">

                <option value="<?php echo PLUGINS_SELECTED; ?>" selected="selected"><?php echo PLUGINS_SELECTED; ?></option>
                <option value="install" ><?php echo PLUGINS_INSTALL;?></option>
                <option value="remove" ><?php echo PLUGINS_REMOVE;?></option>

            </select></form>
        </td>
    </tr>
    <?php


    }

?>