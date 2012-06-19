<?php
    /*
    #####################################
    #  OSC-CMS: Shopping Cart Software.
    #  Copyright (c) 2011-2012
    #  http://osc-cms.com
    #  http://osc-cms.com/forum
    #####################################
    */

    require_once('includes/top.php');

    if (!isset($_SESSION['language']) && !isset($_POST['LANGUAGE']))
    {
        $_SESSION['language'] = 'ru';
    }  

    if ( isset($_SESSION['language']) && ($_SESSION['language'] == 'ru' or $_SESSION['language']=='en'))
    {
        ///
    } 
    else
    {
        $_SESSION['language'] = 'ru';
    }

    if (isset($_POST['LANGUAGE']))
    {
        $_SESSION['language'] = $_POST['LANGUAGE'];
    }
    include('lang/'.$_SESSION['language'].'/lang.php');

    define('HTTP_SERVER','');
    define('HTTPS_SERVER','');
    define('DIR_WS_CATALOG','');
    $text = "";

    function  text_ok ($str, $type)
    {
        if ($type)
        {
            $GLOBALS["text"] = $GLOBALS["text"]."<tr><td align=\"center\" width=\"30\"><img src=\"images/os_folder.gif\"></td><td  align=\"left\"  valign=\"top\">$str</td><td valign=\"top\"><span class=\"Yes\">".TEXT_YES."</span><span class=\"small\">&nbsp;</span></td></tr>";
        }
        else
        {
            $GLOBALS["text"] = $GLOBALS["text"]."<tr><td align=\"center\" width=\"30\"><img src=\"images/os_file.gif\"></td><td  align=\"left\"  valign=\"top\">$str</td><td valign=\"top\"><span class=\"Yes\">".TEXT_YES."</span><span class=\"small\">&nbsp;</span></td></tr>";
        }
    }

    function text_no ($str, $type)
    {
        if ($type)
        {
            $GLOBALS["text"] = $GLOBALS["text"]."<tr><td  align=\"center\" width=\"30\"><img src=\"images/os_folder.gif\"></td><td align=\"left\" valign=\"top\">$str</td><td valign=\"top\"><span class=\"No\">".TEXT_NO."</span><span class=\"small\">&nbsp; Установите права доступа 777</span></td></tr>";
        }
        else
        {
            $GLOBALS["text"] = $GLOBALS["text"]."<tr><td  align=\"center\" width=\"30\"><img src=\"images/os_file.gif\"></td><td align=\"left\" valign=\"top\">$str</td><td valign=\"top\"><span class=\"No\">".TEXT_NO."</span><span class=\"small\">&nbsp; Установите права доступа 777</span></td></tr>";
        }   
    }

    $messageStack = new messageStack();
    $process = false;

    if (isset($_POST['action']) && ($_POST['action'] == 'process')) 
    {
        $process = true;
        $_SESSION['language'] = os_db_prepare_input($_POST['LANGUAGE']);
        $error = false;


        if ($error == false) 
        {
            os_redirect(os_href_link('1.php', '', 'NONSSL'));
        }
    }


    $error_flag=false;
    $message='';
    $ok_message='';
    $text = ""; 
    //Проверка прав доступа к файлам  
    if (!is_writeable(_CATALOG.'config.php'))
    {
        $error_flag=true; 
        text_no("config.php",false); 
    } 
    else 
    {
        text_ok("config.php", false);
    }


    //if (is_file(_CATALOG.'htaccess.txt')) //проверка самого наличия и прав доступа к файлу htaccess.txt
    //{
    if (!is_writeable(_CATALOG.'htaccess.txt'))
    {
        $error_flag=true;
        text_no("htaccess.txt",false);
    }
    else 
    {
        text_ok("htaccess.txt",false);
    }   
    //} 

    $status = TEXT_OK;

    if ($error_flag==true) 
    { 
        $color='red'; 
    } 
    else 
    { 
        $color = 'green'; 
    }

    if ($error_flag == true) 
    {
        $status='<span class="errorText">' . TEXT_ERROR . '</span>';
    }

    $ok_message.= "<font color=\"$color\"><b>".TEXT_FILE_PERMISSIONS.'</b></font> '.$status;
    $folder_flag=false;

    //Проверка прав доступа к папкам

    if (!is_writeable(_CATALOG.'admin/backups/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("admin/backups/",true);
    }
    else 
    {
        text_ok("admin/backups/", true);
    }

    if (!is_writeable(_CATALOG.'tmp/')) 
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("tmp/",true);
    } 
    else 
    {
        text_ok("tmp/",true);
    }

    if (!is_writeable(_CATALOG.'cache/')) 
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("cache/", true);
    } 
    else 
    {
        text_ok("cache/", true);
    }

    if (!is_writeable(_CATALOG.'cache/system/')) 
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("cache/system/", true);
    } 
    else 
    {

        text_ok("cache/system/", true);
    }

    if (!is_writeable(_CATALOG.'images/attribute_images/')) 
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/attribute_images/", true);
    } 
    else 
    {
        text_ok("images/attribute_images/", true);
    }


    if (!is_writeable(_CATALOG.'images/attribute_images/mini/')) 
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/attribute_images/mini/", true);
    } 
    else 
    {
        text_ok("images/attribute_images/mini/", true);
    }


    if (!is_writeable(_CATALOG.'images/attribute_images/original/')) 
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/attribute_images/original/", true);
    } 
    else 
    {
        text_ok("images/attribute_images/original/", true);
    }

    if (!is_writeable(_CATALOG.'images/attribute_images/thumbs/')) 
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/attribute_images/thumbs/", true);
    } 
    else 
    {
        text_ok("images/attribute_images/thumbs/", true);
    }


    if (!is_writeable(_CATALOG.'media/export/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("media/export/", true);
    }
    else 
    {
        text_ok("media/export/", true);
    }   


    if (!is_writeable(_CATALOG.'media/products/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("media/products/", true);
    }
    else 
    {
        text_ok("media/products/", true);
    }   

    if (!is_writeable(_CATALOG.'media/import/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("media/import/", true);
    }
    else 
    {
        text_ok("media/import/", true);
    }   

    if (!is_writeable(_CATALOG.'images/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/", true);
    }
    else 
    {
        text_ok("images/", true);
    }   

    if (!is_writeable(_CATALOG.'images/categories/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/categories/", true);
    } 
    else 
    {
        text_ok("images/categories/", true); 
    }

    if (!is_writeable(_CATALOG.'images/banner/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/banner/", true);
    }
    else 
    {
        text_ok("images/banner/",true);
    }   

    if (!is_writeable(_CATALOG.'images/product_images/info_images/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/product_images/info_images/", true);
    }
    else 
    {
        text_ok("images/product_images/info_images/", true);
    }   

    if (!is_writeable(_CATALOG.'images/product_images/original_images/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no('images/product_images/original_images/', true);
    } 
    else 
    {
        text_ok('images/product_images/original_images/', true);
    }

    if (!is_writeable(_CATALOG.'images/product_images/popup_images/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/product_images/popup_images/", true);
    }
    else 
    {
        text_ok("images/product_images/popup_images/", true);
    }	

    if (!is_writeable(_CATALOG.'images/product_images/thumbnail_images/'))
    {
        $error_flag=true;
        $folder_flag=true;
        text_no("images/product_images/thumbnail_images/", true);
    } 
    else 
    {
        text_ok("images/product_images/thumbnail_images/", true);
    }

    $status = TEXT_OK;
    if ($folder_flag==true)
    {
        $color='red'; 
    } 
    else 
    { 
        $color = 'green'; 
    }

    if ($error_flag==true & $folder_flag==true) 
    {
        $status='<span class="errorText">' . TEXT_ERROR . '</span>';
    }   

    $ok_message.= "<br><font color=\"$color\"><b>" . TEXT_FOLDER_PERMISSIONS . '</b></font> ' . $status;

    $php_flag=false;
    if (os_check_version()!=1)
    {
        $error_flag=true;
        $php_flag=true;
        $message .= PHP_VERSION_ERROR;
    }

    $status = TEXT_OK;

    if ($php_flag==true) 
    {
        @$color='red'; 
    } 
    else 
    { 
        @$color = 'green'; 
    }

    if ($php_flag==true) 
    {
        $status='<span class="errorText">' . TEXT_ERROR . '</span>';
    }

    $ok_message.= "<br><font color=\"$color\"><b>" . TEXT_PHP_VERSION . ': </b>'.TEXT_OK.' ('.PHP_VERSION.')</font> ';

    if (function_exists('gd_info'))
    {
        $gd = gd_info();
        if (empty($gd['GD Version'])) 
        {
            $gd['GD Version']='<span class="errorText">пусто' . TEXT_GD_LIB_NOT_FOUND . '</span>';
        }

        $ok_message.= '<br><font color="green"><b>' . TEXT_GD_LIB_VERSION1 . ': </b> '.TEXT_OK.' ('.$gd['GD Version'].')</font>';
        if ($gd['GIF Read Support']==1 or $gd['GIF Support']==1) 
        {
            $status = TEXT_OK;
            $color = 'green';
        } 
        else 
        {
            $status = TEXT_GD_LIB_GIF_SUPPORT_ERROR;
            $color = 'red';
        }
        $ok_message.= "<br><font color=\"$color\"><b>" . TEXT_GD_LIB_GIF_SUPPORT . ':</b></font> ' . $status;
    }
    else
    {
        $error_flag= true;
        $status='<span class="errorText">' . TEXT_ERROR . '</span>';
        $ok_message.= '<br><font color="red"><b>' . TEXT_GD_LIB_VERSION1 . ': </b></font>' .TEXT_ERROR;
        $ok_message.= "<br><font color=\"red\"><b>" . TEXT_GD_LIB_GIF_SUPPORT . ':</b></font> ' . $status;
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=<?php echo CHARSET; ?>">
        <title><?php echo TITLE_INDEX; ?></title>
        <link rel="shortcut icon" href="favicon.ico" />
        <style type='text/css' media='all'>@import url('includes/style.css');</style>
        <style type='text/css' media='all'>@import url('includes/menu.css');</style>
        <script type="text/javascript" src="includes/include.js"></script>	
    </head>
    <body>
        <script type="text/javascript"></script>
        <div id="header1">
            <div id="header2">
                <?php echo install_menu(); ?>
                <div id="header3"></div>
            </div>
        </div>	
        <div id="content-box">
            <div id="content-pad">
                <div id="stepbar">
                    <div class="t">
                        <div class="t">
                            <div class="t"></div>
                        </div>
                    </div>
                    <div class="m">
                        <h1><?php echo STEPS ;?></h1>
                        <div class="step-on"><?php echo START; ?></div>
                        <div class="step-off"><?php echo STEP1; ?></div>
                        <div class="step-off"><?php echo STEP2; ?></div>
                        <div class="step-off"><?php echo STEP3; ?></div>
                        <div class="step-off"><?php echo STEP4; ?></div>
                        <div class="step-off"><?php echo STEP5; ?></div>
                        <div class="step-off"><?php echo STEP6; ?></div>
                        <div class="step-off"><?php echo END; ?></div>
                        <div class="box"></div>
                    </div>
                    <div class="b"><div class="b"><div class="b"></div></div></div>
                </div>
                <form name="language" id="languag" method="post">
                    <input type="hidden" name="LANGUAGE" id="lang_a" value="<?php echo $_SESSION['language']; ?>" />
                    <input type="hidden" name="action" id="action" value="process" />


                    <div id="right">
                        <div id="rightpad">
                            <div id="step">
                                <div class="t">
                                    <div class="t">
                                        <div class="t"></div>
                                    </div>
                                </div>
                                <div class="m">

                                    <div class="far-right">

                                        <div class="button1-left"><div class="refresh"><a href="index.php" alt="<?php echo IMAGE_RETRY; ?>"><?php echo IMAGE_RETRY ; ?></a></div></div>

                                        <div class="button1-left"><div class="next"><a <?php if ($error_flag==false) { ?>onclick="document.language.submit();"<?php }?>
                                                    alt="Далее"><?php echo IMAGE_CONTINUE;?></a></div></div>

                                    </div>
                                    <?php echo lang_menu_index(); ?>
                                    <span class="step"><?php echo TEXT_BEGIN_CHECKING;?></span>



                                </div>
                                <div class="b">
                                    <div class="b">
                                        <div class="b"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="installer">
                                <div class="t">
                                    <div class="t">
                                        <div class="t"></div>
                                    </div>
                                </div>
                                <div class="m">

                                    <h2><?php echo TEXT_SETUP_INDEX.' ('.$_rev.')'; ?></h2>
                                    <div class="install-text">
                                        <?php echo TEXT_INSTALL_INDEX; ?>
                                    </div>

                                    <div class="install-body">
                                        <div class="t">
                                            <div class="t">
                                                <div class="t"></div>
                                            </div>
                                        </div>
                                        <div class="m">
                                            <fieldset>
                                                <table border="0" width="100%">




                                                    <?PHP
                                                        echo "$text";
                                                    ?>

                                                    <input type="hidden" name="task" value="" />					

                                                    <tr>
                                                        <td valign="top" class="item">
                                                        </td>
                                                    </tr>
                                                </table>

                                            </fieldset>

                                        </div>

                                        <div class="b">
                                            <div class="b">
                                                <div class="b"></div>
                                            </div>
                                        </div>

                                        <div class="clr"></div>

                                    </div>

                                    <?php				
                                        if ($error_flag==true) { echo "$message"; 
                                        ?>
                                        <?php } ?>
                                    <?php
                                        if ($ok_message!='') {
                                        ?>
                                        <br /><br /><span class="errorText"><?php echo TEXT_CHECKING; ?></span><br />
                                        <div class="os-ok-blok">
                                            <?php echo $ok_message; ?>
                                        </div>
                                        <?php } ?>
                                    <br />
                                    <?php
                                        if ($messageStack->size('index') > 0) {
                                        ?><br />
                                        <?php echo $messageStack->output('index'); ?></td>

                                        <?php
                                        }

                                    ?>
                                    <div class="newsection"></div>
                                </div>
                                <div class="b">
                                    <div class="b">
                                        <div class="b"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="clr"></div>
            </div>
        </div>
        <div id="footer1">
            <div id="footer2">
                <div id="footer3"></div>
            </div>
        </div>
        <?php echo _copy(); ?>
    </body>
</html>