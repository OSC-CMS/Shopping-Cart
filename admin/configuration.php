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

    require('includes/top.php');

    if (empty($_GET['gID'])) 
    {
        //подключаем таббер
        add_action('head_admin', 'head_tabs_config');
    }

    if (isset($_GET['action'])) 
    {
        switch ($_GET['action']) 
        {
            case 'save':


                $configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int)$_GET['gID'] . "' order by sort_order");


                while ($configuration = os_db_fetch_array($configuration_query))

                    os_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");

                os_redirect(FILENAME_CONFIGURATION. '?gID=' . (int)$_GET['gID']);
                break;


        }
    }

    $main->head();
?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
        <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td class="main">
                        <a style="right:20px;position:absolute;" class="button" onclick="document.configuration.submit()" href="#"><span><?php echo BUTTON_SAVE;  ?></span></a>

                        <?php os_header('configuration.png',BOX_CONFIGURATION." / ".HEAD_T); ?>
                        <?php if (empty($_GET['gID'])) 
                            { 

                                $cfg_group_query = os_db_query("select configuration_group_key, configuration_group_id from " . TABLE_CONFIGURATION_GROUP.'  order by sort_order;');

                                while ( $cfg_group = os_db_fetch_array($cfg_group_query) )
                                {
                                    $_group[ $cfg_group['configuration_group_id'] ] = $cfg_group['configuration_group_key'];
                                    $_group_id_array[] =  $cfg_group['configuration_group_id'];
                                    $group_array = implode (',', $_group_id_array);
                                }

                            ?>
                            <div id="tabs">
                                <ul>
                                    <?php

                                        foreach ($_group as $group_id => $group_key)
                                        {
                                            if ( defined ( strtoupper( $group_key ).'_TITLE' ) )
                                            {
                                                echo ' <li><a href="#с'.$group_id.'">'. constant (  strtoupper( $group_key ).'_TITLE' ) . '</a></li>';
                                            }
                                        } 
                                    ?>
                                </ul>
                                <?php
                                    $_query = os_db_query("select configuration_id, configuration_key, configuration_value, configuration_group_id, set_function from ".DB_PREFIX."configuration where configuration_group_id in (".$group_array.") order by sort_order;");

                                    while ( $_query_value = os_db_fetch_array($_query, false) )
                                    {
                                        $value[ $_query_value['configuration_group_id'] ] []  = $_query_value;
                                    }  


                                    foreach ($value as $group_id => $group_value)
                                    {
                                        echo '<div id="с'.$group_id.'">';
                                        echo '<table width="100%" border="0">'; 
                                        $color = '';     


                                        foreach ($group_value as $value)
                                        {
                                            $__title = '';

                                            if ( defined( strtoupper( $value['configuration_key'].'_TITLE') ) )
                                            {
                                                $__title = constant(strtoupper( $value['configuration_key'].'_TITLE'));
                                            }

                                            $__desc = '';

                                            if ( defined( strtoupper(  $value['configuration_key'].'_DESC') ) )
                                            {
                                                $__desc = constant(strtoupper(  $value['configuration_key'].'_DESC'));
                                            }
                                            if (os_not_null($value['use_function'])) 
                                            {
                                                $use_function = $value['use_function'];
                                                if (preg_match('/->/', $use_function)) {
                                                    $class_method = explode('->', $use_function);
                                                    if (!is_object(${$class_method[0]})) {
                                                        include(get_path('class_admin') . $class_method[0] . '.php');
                                                        ${$class_method[0]} = new $class_method[0]();
                                                    }
                                                    $cfgValue = os_call_function($class_method[1], $value['configuration_value'], ${$class_method[0]});
                                                } else {
                                                    $cfgValue = os_call_function($use_function, $value['configuration_value']);
                                                }
                                            } else {
                                                $cfgValue = $value['configuration_value'];
                                            }

                                            if ($value['set_function']) 
                                            {
                                                eval('$value_field = ' . $value['set_function'] . '"' . htmlspecialchars($value['configuration_value']) . '");');
                                            } 
                                            else 
                                            {
                                                $value_field = os_draw_input_field($value['configuration_key'], $value['configuration_value'],'size="15" class="round"');
                                            }

                                            $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
                                            echo '<tr style="padding-top:2cm; background-color:'.$color.'">';
                                            echo '<td style="padding-bottom: 10px;padding-top: 10px;" valign="middle" width="200" align="center">'.$value_field.'</td>';
                                            echo '<td style="padding-bottom: 10px;padding-top: 10px;" valign="top" align="left"><b>'.$__title.'</b><br>'.$__desc.'</td></tr>';
                                            echo '</tr>'; 

                                        }
                                        echo '</table></div>';
                                        //  print_r($group_value);
                                    }

                                ?>

                            </div>	
                            <?php } ?>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">

                            <tr>
                                <td valign="top" align="right">

                                    <?php echo os_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=save'); ?>
                                    <table width="100%"  border="0" cellspacing="0" cellpadding="4">
                                        <?php
                                            $configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int)$_GET['gID'] . "' order by sort_order");


                                            while ($configuration = os_db_fetch_array($configuration_query)) {
                                                if ($_GET['gID'] == 6) {
                                                    switch ($configuration['configuration_key']) {
                                                        case 'MODULE_PAYMENT_INSTALLED':
                                                            if ($configuration['configuration_value'] != '') {
                                                                $payment_installed = explode(';', $configuration['configuration_value']);
                                                                for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
                                                                    include(_MODULES.'payment/' . substr($payment_installed[$i], 0, strrpos($payment_installed[$i], '.')).'/'.$_SESSION['language'].'.php');
                                                                }
                                                            }
                                                            break;


                                                        case 'MODULE_SHIPPING_INSTALLED':
                                                            if ($configuration['configuration_value'] != '') {
                                                                $shipping_installed = explode(';', $configuration['configuration_value']);
                                                                for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
                                                                    include(_MODULES.'/shipping/'. substr($shipping_installed[$i], 0, strrpos($shipping_installed[$i], '.')).'/'.$_SESSION['language'].'.php');                       
                                                                }
                                                            }
                                                            break;


                                                        case 'MODULE_ORDER_TOTAL_INSTALLED':
                                                            if ($configuration['configuration_value'] != '') {
                                                                $ot_installed = explode(';', $configuration['configuration_value']);
                                                                for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
                                                                    include(_MODULES.'/order_total/' .  substr($ot_installed[$i], 0, strrpos($ot_installed[$i], '.')).'/'.$_SESSION['language'].'.php');                      
                                                                }
                                                            }
                                                            break;
                                                    }
                                                }
                                                if (os_not_null($configuration['use_function'])) {
                                                    $use_function = $configuration['use_function'];
                                                    if (preg_match('/->/', $use_function)) {
                                                        $class_method = explode('->', $use_function);
                                                        if (!is_object(${$class_method[0]})) {
                                                            include(get_path('class_admin') . $class_method[0] . '.php');
                                                            ${$class_method[0]} = new $class_method[0]();
                                                        }
                                                        $cfgValue = os_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
                                                    } else {
                                                        $cfgValue = os_call_function($use_function, $configuration['configuration_value']);
                                                    }
                                                } else {
                                                    $cfgValue = $configuration['configuration_value'];
                                                }

                                                if (isset($_GET['cID']))
                                                {
                                                    if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
                                                        $cfg_extra_query = os_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
                                                        $cfg_extra = os_db_fetch_array($cfg_extra_query);


                                                        $cInfo_array = os_array_merge($configuration, $cfg_extra);
                                                        $cInfo = new objectInfo($cInfo_array);
                                                    }
                                                }

                                                if ($configuration['set_function']) 
                                                {
                                                    eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
                                                } 
                                                else 
                                                {
                                                    $value_field = os_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size="15" class="round"');
                                                }

                                                // add
                                                $chet =1;
                                                $color = '';

                                                if (strstr($value_field,'configuration_value')) $value_field=str_replace('configuration_value',$configuration['configuration_key'],$value_field);
                                                { 

                                                    echo '<tr style="padding-top:2cm; background-color:';
                                                    $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';

                                                    $__title = '';

                                                    if ( defined( strtoupper($configuration['configuration_key'].'_TITLE') ) )
                                                    {
                                                        $__title = constant(strtoupper($configuration['configuration_key'].'_TITLE'));
                                                    }

                                                    $__desc = '';

                                                    if ( defined( strtoupper( $configuration['configuration_key'].'_DESC') ) )
                                                    {
                                                        $__desc = constant(strtoupper( $configuration['configuration_key'].'_DESC'));
                                                    }

                                                    echo '">
                                                    <td style="padding-bottom: 10px;padding-top: 10px;" valign="middle" width="200" align="center">'.$value_field.'</td>
                                                    <td style="padding-bottom: 10px;padding-top: 10px;" valign="top" align="left"><b>'.$__title.'</b><br>'.$__desc.'</td></tr>';

                                                }
                                            }
                                        ?>
                                    </table><br>
                                    <?php
                                        if ( is_file( dir_path('catalog') . 'admin/includes/pages/default/sql/configuration_'.(int)$_GET['gID'].'.php' ) )
                                        {
                                        ?>
                                        <a onClick="return confirm('Установить настройки по умолчанию?')" style="right:130px;position:absolute;" class="button" href="index.php?action=default&name=configuration&param=<?php echo $_GET['gID']; ?>"><span><?php echo BUTTON_DEFAULT;  ?></span></a>
                                        <?php } ?>
                                    <a style="position:relative;" class="button" onclick="document.configuration.submit()" href="#"><span><?php echo BUTTON_SAVE;  ?></span></a>
                                    </form>       
                                </td>


                            </tr>
                        </table></td>
                </tr>
            </table></td>
    </tr>
</table>
<?php $main->bottom(); ?>