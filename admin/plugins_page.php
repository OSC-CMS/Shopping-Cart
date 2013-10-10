<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

    require('includes/top.php');
    include( dir_path_admin('func') . 'plugin.php'); 

    $plugins = $p->info;

    $plugins_file = $p->get_name (); // получение списка плагинов /

    $p->lang ();

    if (isset($_GET['main_page']))
    {
    ?>

    <?php $main->head(); ?>
    <?php $main->top_menu(); ?>

    <table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr>
            <td class="boxCenter" width="100%" valign="top">
                <?php  

                    if (isset($_GET['main_page']) && isset($os_action['main_page_admin'][$_GET['main_page']]))
                    {		
                        $p->name = $os_action_plug[ $_GET['main_page'] ];
                        $p->group = $p->info[ $p->name ]['group'];
                        $p->set_dir();

                        foreach ($plugins_file as $_val)
                        {
                            if ($_val[0] == $os_action_plug[ $_GET['main_page'] ])
                            {
                                $plugin_data = get_plugin_data($_val[1]);

                                if ( isset( $p->lang[ $p->name ][ $_GET['main_page'] ] ) )
                                {
                                    $_p_name = $p->lang[ $p->name ][ $_GET['main_page'] ];
                                }
                                elseif ( !empty($plugin_data['Name'] ) )
                                {
                                    $_p_name = $plugin_data['Name'];
                                }
                                else
                                {     
                                    $_p_name = $_GET['main_page'];
                                }




                            }
                        }

                        $_p = $_p_name; 
                    }
                    else
                    {
                        $_p = ''; 
                    }
                ?>
                <?php echo $_p; ?>  
                <?php $main->fly_menu(PLUGINS_URL, MODULES_OTHER);

                    if (isset($os_action['main_page_admin'][$_GET['main_page']]) && function_exists($_GET['main_page']))
                    {
                        $p->name = $os_action_plug[ $_GET['main_page'] ];
                        $p->group = $p->info[$p->name]['group'];
                        $p->set_dir();

                        $_GET['main_page']();
                    }
                    else
                    {
                        echo 'no page!';
                    }




                ?>
            </td>
        </tr>
    </table>

    </div>
    <?php $main->bottom(); 
    }
    else
    {
        if (isset($os_action['page_admin'][$_GET['page']]) && function_exists($_GET['page']))
        {
            $p->name = $os_action_plug[ $_GET['page'] ];
            $p->group = $p->info[$p->name]['group'];
            $p->set_dir();

            $_GET['page']();
        }
        else
        {
            echo 'no page!';
        }
    }
?>