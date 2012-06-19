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


if ( isset($_GET['action']))
{
   switch ($_GET['action'])
   {
      case 'themes_remove':
	     //установка новых плагинов
		 $themes_name = $_GET['themes_name'];
		 
		 $themes_plug_array = $p->get_plugins_theme ();
		 
		 if (!empty($themes_plug_array))
		 { 
		   $col = 0;
		   $_plug = array();
		   
		   foreach($themes_plug_array as $_value)
		   {
		      //устанавливаем все плагины шаблона
			  @ $p->install($_value[0], 'themes');
			  $_plug[] = $_value[0];
			  $col ++;
		   }
		 }
		// $p->themes_install($themes_name);
		 //установка плагинов текущего шаблона
		 
		 if ($col > 0)
		 {
		    if ($col == 1)
			{
			    $messageStack->add_session('Плагин шаблона успешно установлен ('.$_plug[0].').', 'success');
			}
			else
			{
			    $messageStack->add_session('Плагины шаблона успешно установлены.', 'success');
			    $messageStack->add_session( '<font color="red">('.$col.') '. implode(', ', $_plug).'</font>', 'success');
			}
		 }
		  os_redirect(FILENAME_THEMES);
	  break;
   }
}

if(!empty($_SERVER['QUERY_STRING']))
{
        if (!empty($_GET['c_templates'])) 
        {     
		        $_c_templates = os_check_file_name($_GET['c_templates']);
				
				//$p->themes_remove(CURRENT_TEMPLATE);
				//$p->themes_install($_c_templates);
				
               os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".$_c_templates."' where configuration_key='CURRENT_TEMPLATE'");
                
				
				os_redirect(FILENAME_THEMES.'?action=themes_remove&themes_name='.CURRENT_TEMPLATE);
        }
} 
$counter = 0;

add_action('head_admin', 'head_themes');

function head_themes ()
{
   global $main;

  		$_fancy_js = '<!--fancybox-->'."\n";
		$_fancy_js .= "<style> @import url('../jscript/jquery/plugins/fancybox/jquery.fancybox-1.2.5.css'); </style>"."\n";
		$_fancy_js .= '<script type="text/javascript" src="../jscript/jquery/plugins/fancybox/jquery.fancybox-1.2.5.pack.js"></script>'."\n";
		$_fancy_js .= '<script type="text/javascript"><!--
$(document).ready(function() {
		$("a.zoom").fancybox({
		"zoomOpacity"			: true,
		"overlayShow"			: false,
		"zoomSpeedIn"			: 500,
		"zoomSpeedOut"			: 500
	});
	});
//--></script>'."\n";

   _e($_fancy_js);
  $main->style('themes');
  
  return true;
}

$main->head();
?>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php $main->top_menu(); ?>
<?php
        
   echo "<div id='themes_other'><a target=\"_blank\" href=\"http://osc-cms.com/extend/themes\">".THEMES_OTHER."</a></div>"; 


?>
<table width="100%" border=0><tr><td valign="top">


<?php
    
//I?iniio? oaaeiiia iaaaceia
if ($dir = opendir(DIR_FS_CATALOG.'themes/')) 
{
        while (($templates = readdir($dir)) !== false) 
        {
                if (is_dir(DIR_FS_CATALOG.'themes/'."//".$templates)  & ($templates != ".") && ($templates != "..") && ($templates != ".svn") ) 
                {
                        $templates_array[] = array ('id' => $templates, 'text' => $templates);
                }
        }
        closedir($dir);
        sort($templates_array);
}

$os_shop_style = array();

foreach($templates_array as $key => $type)
{
    foreach($type as $ship)
    {
            if (!in_array($ship,$os_shop_style)) $os_shop_style[] = $ship; 
    }
}

?>
 <?php os_header('themes.png',BOX_CONFIGURATION." / ".HEADING_TITLE); ?> 
<table border="0" width="100%"><tr valign="top"><td>
<div class="PortfolioContent">
<h1 class="str"><?php echo THEMES_H1; ?></h1>           
<?php
           $src = "";
       foreach($os_shop_style as $str)
       {
              if (!empty($str)) 
                  {     
                     $src = "themes/".$str."/screenshot.jpg";
                     if (!is_file(DIR_FS_CATALOG.$src)) //Nouanoaoao ee ne?eioio?
                     {
                        $src = 'admin/themes/'.ADMIN_TEMPLATE.'/images/themes.png';
                     }
                     $src = HTTP_SERVER.DIR_WS_CATALOG.$src;
                   
                  if (CURRENT_TEMPLATE == $str)
          {             
                     echo "<tr class=\"available-theme-ok\"><td><a href=\"?c_templates=$str\"><img class=\"Image1\" style=\"margin-right: 11px;\" src=\"".$src."\" width=\"200\" height=\"116\" alt=\"$str\" /></a><br><a class=\"zoom\" href=\""._HTTP_THEMES.$str."/screenshot-1.jpg\"><img border=\"0\" src=\"".HTTP_SERVER.DIR_WS_CATALOG."images/zoom.gif\"></a></td>";
                     echo "<td align=\"left\" valign=\"top\">";
                    
					  echo "<table width='100%' border=0><tr><td valign=\"top\">";  
          echo "</td></tr><tr><td valign=bottom><font size=2>[</font> <a href=\"themes_edit.php?themes_a=".$str."\"><font size=2>".THEMES_EDIT."</font></a><font size=2> ]</font></td></tr></table>";
					
                     echo "</td></tr>";
                  }                               
                }
                
 }
          
          echo "</tr></table>";
          
?>
</td></tr></table>

<?php

if (count($os_shop_style)>1)
{
?>
<h1 class="str"><?php echo THEMES_H2; ?></h1>   




        <table border="0" width="100%">


<?php
$num = 1;
  foreach($os_shop_style as $str)
  {
     if (!empty($str) && CURRENT_TEMPLATE != $str) 
        {  
      if ($num == 1)
          {
             echo "<tr><td width=\"30%\" valign=\"top\">"; 
          }
          
          if ($num == 2)
          {
             echo "<td width=\"50%\" valign='top'>";
                
          }
           echo '<br />';
          echo '<a style="font-size:17px;" href="?c_templates='.$str.'">'.ucwords($str).'</a>';
        echo '<table border=0>';


                   $src = "themes/".$str."/screenshot.jpg";
                   if (!is_file(DIR_FS_CATALOG.$src)) //Nouanoaoao ee ne?eioio?
                   {
                      $src = 'admin/themes/'.ADMIN_TEMPLATE.'/images/themes.png';
                   }
                   $src = HTTP_SERVER.DIR_WS_CATALOG.$src;


                   echo "<tr class=\"available-theme\"><td><a href=\"?c_templates=$str\"><img class=\"Image\" style=\"margin-right: 11px;\" src=\"".$src."\" width=\"200\" height=\"116\" alt=\"$str\" /></a><br><a class=\"zoom\" href=\""._HTTP_THEMES.$str."/screenshot-1.jpg\"><img src=\"".HTTP_SERVER.DIR_WS_CATALOG."images/zoom.gif\"  border=\"0\"></a><br/>";
				    echo "<table width='100%' border=0><tr><td valign=\"top\">";  
          echo "</td></tr><tr><td valign=bottom class='themes_box'><font size=2>[</font> <a href=\"themes_edit.php?themes_a=".$str."\"><font size=2>Редактировать</font></a><font size=2> ]</font></td></tr></table></td>";
                   echo "<td align=\"left\" valign=\"top\">";
                  
					
                   echo "</td></tr>";


                 echo '</table>';
          if ($num == 1)
          {
             echo "</td>";
          }
          if ($num == 2)
          {
             echo "</td></tr>";
          }
          if ($num ==1) 
          {
             $num = 2;
          }
      else
      {
             $num = 1;
          }      
  }       
  }


echo '</table>';
}
  echo '</div></div></td></tr></table>';
?>
<?php $main->bottom(); ?>