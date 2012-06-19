<?php
defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

	case 'box_save':
       if (isset($_GET['module']))
	   {
			$plugin_name = $db->prepare_input($_GET['module']);
		    
			if (is_file(_PLUG.$plugin_name.'/'.$plugin_name.'.php'))
			{
			   require_once(_PLUG.$plugin_name.'/'.$plugin_name.'.php');
			   $_plugins = new $plugin_name;
			}		
           $fp = @fopen(_CACHE_PLUG.$_plugins->box.'.cache', 'w+');
           fwrite($fp, $_POST['file']);
           @fclose($fp);				
		}
    break; 	
	
//---- редактирование шаблона -----------------------------------------------------------------------------		
if (!empty($_plugins->box) && isset($_GET['page']) && $_GET['page']=='box_edit')		
{	 
     if (is_writeable(_PLUG.$_GET['module'].'/'.$_plugins->box.'.html'))
     {
           
		   if (!is_file(_PLUG.$_GET['module'].'/'.$_plugins->box.'.html'))
		   {
		       $fp = @fopen(_PLUG.$_GET['module'].'/'.$_plugins->box.'.html', 'rb');
               while (!feof($fp)) 
		       {
                      $st .= fread($fp, 4096);
               }
		   }
           echo '<tr><td align="right" colspan="5" style="padding-left: 10px;padding-right:15px;padding-top:10px;">';
		   echo '<form action="'.FILENAME_PLUGINS.'?action=box_save&module='.$plugin_name.'" method="post">';
		   echo '<textarea name="file" rows="10" style="width:100%">'.$st.'</textarea>';
		   echo '<br />';
           echo '<span class="button"><button type="submit" value="'.PLUGINS_SAVE.'">'.PLUGINS_SAVE.'</span>';
		   echo '<form>';
           echo '</td></tr>';
           @fclose($fp);
     }
     else
     {
         echo 'Установите права доступа 777 на файл '._PLUG.$_GET['module'].'/'.$_plugins->box.'.html';
     } 
}			  
////---редактирование шаблона --------------------------------------------------------------------------------

?>