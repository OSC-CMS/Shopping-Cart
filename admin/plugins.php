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

include( dir_path_admin('func') . 'plugin.php'); 

if (isset($_GET['action']) && $_GET['action']=='process_true')
{
    $messageStack->add('Плагин '.$_GET['modules'].' успешно выполнен', 'success');
}

if (isset($_GET['module']) && !empty($_GET['module'])) 
{
   $p->module = $_GET['module'];
   $p->name = $_GET['module'];
}

$_plug_array =  $p->plug_array();

$plugins = $p->info;

if (isset($_GET['action']))
{
     switch ($_GET['action'])
     {
          case 'install': 
		      $p->install();
               os_redirect(FILENAME_PLUGINS.'?module='.$p->name.'&group='.$p->group);	  
		  break; 

          case 'remove': 
		       $p->remove(); 
			    os_redirect(FILENAME_PLUGINS.'?module='.$p->name.'&group='.$p->group);
		  break; 

		  // TODO: добавить возможность выключать\включать плагины без удаления инфы из БД.
          case 'update_status': 
				$p->updatePluginStatus(); 
				os_redirect(FILENAME_PLUGINS.'?module='.$p->name.'&group='.$p->group);
		  break; 

	      case 'process': $p->process(); break; 	
	      case 'save': $p->save_options(); break; 
	      case 'multi_action': $p-> multi_action(); break;
     }
}

add_action('head_admin', 'head_plugins');

function head_plugins()
{
    global $main;
    $main->style('plugins');
}



?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<form name="multi_action_form" action="<?php echo FILENAME_PLUGINS; ?>?action=multi_action" method="post">
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    <?php $main->heading('plugin.gif', HEADING_TITLE); ?>  

	<?php
	
	echo "<div style=\"position:absolute;top:58px; right:30px;\"><a target=\"_blank\" style=\"color:#4378a1\" href=\"http://osc-cms.com/extend/plugins\">".MODULES_OTHER."</a></div>"; 

	
	?>

    <table border="0" width="100%" cellspacing="4" cellpadding="2" valign="top">
      <tr>
        <td  valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0"  valign="top">
          <tr>
            <td valign="top">
			<table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
			    <td class="dataTableHeadingContent" width="20px"><input type="checkbox" name="plug_all" onClick="javascript:SwitchCheck();" /></td>
			    <td class="dataTableHeadingContent" width="20px">&nbsp;</td>
				<td class="dataTableHeadingContent" width="300px"><?php echo TABLE_HEADING_PLUGINS; ?></td>
				<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILENAME; ?></td>
				<td class="dataTableHeadingContent" width="50px"><?php echo TABLE_HEADING_VERSION; ?></td>
				<td class="dataTableHeadingContent" width="50px"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right" width="50"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>		  
<?php
    


	foreach ($_plug_array as $_group => $_plug_value)
	{ 
	   $p->group = $_group; 
	   $i = 0;
	   
	   if ($_group == 'themes') 
	   {  
	       $_group = HEADING_TITLE_THEMES.' (<i>'.CURRENT_TEMPLATE.'</i>)';
	   }
	   
	   if ($_group == 'update') $_group = PLUGINS_UPDATE;
	   if ($_group != 'main') 
	   {
	       echo '<tr>';
		   echo '<td class="dataTableHeadingContent" colspan="7" style="text-align:left;">&nbsp;&nbsp;'.$_group.'</td>'; 
		   echo '</tr>';
       }
	   
	   if (!empty($_plug_value))
	   {

	       foreach ($_plug_value as $_value)
	       { 
		        
		        $color = $_value['color'];
				$p->name = $_value['name'];

				if (empty($p->module)) $p->module = $p->name;
				$p->group = $p->info[$p->name]['group']; // текущая группа плагина -> main | themes | update
				
				//определяем обновление установлено уже или нет
				if ($p->check_update()) $_through = ''; else $_through = ' text-decoration: line-through; ';
				
				
		   	    if ($p->module == $p->name)
		        {
		              echo '<tr class="dataTableRowSelected"'.$_through .'>' . "\n";
		        }
		        else
		        { 
                      echo '<tr onmouseover="this.style.background=\'#e9fff1\';" onmouseout="this.style.background=\''.$color.'\';"  style="background-color:'.$color;
		              echo ';" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"'.$_through .'>' . "\n";
	            }  
				
?>				
<td align="center" valign="center"><input type="checkbox" name="plugins[]" value="<?php echo $p->name; ?>" /></td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$p->name.'&group='.$p->group).'\'"';  ?> align="center" valign="center"><?php  echo $_value['icon'];?></td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$p->name.'&group='.$p->group).'\'"'; ?> class="dataTableContent"><?php  echo $_value['title'];?></td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$p->name.'&group='.$p->group).'\'"'; ?> class="dataTableContent"><?php echo $_value['name']; ?></td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$p->name.'&group='.$p->group).'\'"'; ?> class="dataTableContent" align="center"><?php echo (!empty($_value['version'])? $_value['version'] : '1.0'); ?></td>
<td class="dataTableContent" align="center"><?php $p->status();?></td>
<td width="80px" class="dataTableContent" align="right" valign="center"><?php $p->action($p->name); ?></td> 
</tr><?php
	       }
	   }
	}

  plugins_switch();
  
//блоки из текущего шаблона
echo '</table><br /><div style="width:100%;text-align:right;font-size:12px;"><i>/modules/plugins/</i>&nbsp;&nbsp;</div>';

?>
</td>
    <td class="right_box" valign="top">
<table class="contentTable" border="0" width="237px" cellspacing="0" cellpadding="0">
  <tr>
    <td class="infoBoxHeading">
	<?php 
		    if (!empty($p->info[$p->module]['title']))
			{
			  $_title = $p->info[$p->module]['title'];
			}
			else
			{
			     if (isset($p->info[$p->module]))
				 {
				      $_title =  ucfirst($p->module);
				 }
				 else
				 {
				      $_title = '-:-:-';
				 }
			 
			}
	
			if (mb_strlen($_title) > 27)  
			{
				 $_title = _mb_substr($_title, 0, 27);
				 $_title = $_title.'..';
			}
			echo $_title;
	?></td>
  </tr>
</table>
<?php $p->option($_plug_array); ?>

	</td>
  </tr>
</table>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>

</div>
<?php $main->bottom(); ?>