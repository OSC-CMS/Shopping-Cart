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
require(_CLASS.'price.php');

add_action('head_admin', 'head_modules');

function head_modules()
{
  _e('
  <style>
  .icon_module
  {
     width:16px;
     height:16px;
	 text-align:center;
	 background-repeat: no-repeat;
	 background-image: url('.http_path('icons_admin').'plugins/plugins_icons.png);
  }
  </style>
  ');
}

$osPrice = new osPrice($_SESSION['currency'],''); 
  switch ( @$_GET['set'] ) 
  {
    case 'shipping':
      $module_type = 'shipping';
      $module_directory = _MODULES . 'shipping/';
      $module_key = 'MODULE_SHIPPING_INSTALLED';
      break;

    case 'ordertotal':
      $module_type = 'order_total';
      $module_directory = _MODULES. 'order_total/';
      $module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
      break;

    case 'payment':
    default:
      $module_type = 'payment';
      $module_directory = _MODULES. 'payment/';
      $module_key = 'MODULE_PAYMENT_INSTALLED';
      if (isset($_GET['error'])) 
	  {
          $messageStack->add($_GET['error'], 'error');
      }
      break;
  }

  switch (@$_GET['action']) 
  {
    case 'save':
      while (list($key, $value) = each($_POST['configuration'])) {
	  
	  		if ($key == "MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER") 
		  {
			 $value = str_replace(" ","",$value);
           if ($value == "") $value ="0";
		  }
		 
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
		
      }
	  ///set_configuration_cache(); 
     os_redirect(os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']));
      break;

    case 'install':
    case 'remove':
      //$file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
      $class = basename($_GET['module']);
	  if (file_exists($module_directory.$_GET['module'].'/'.$_GET['module'].'.php')) 
	  {
        include($module_directory.$_GET['module'].'/'.$_GET['module'].'.php');
        $module = new $class(0);
        if ($_GET['action'] == 'install') {
          $module->install();
        } elseif ($_GET['action'] == 'remove') {
          $module->remove();
        }
      }
	  ///set_configuration_cache(); 
      os_redirect(os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class));
      break;
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('plugin.gif', HEADING_TITLE); ?>  
	<?php
	
	echo "<div style=\"position:absolute;top:58px; right:30px;\"><a target=\"_blank\" style=\"color:#4378a1\" href=\"http://osc-cms.com/extend/modules\">".MODULES_OTHER."</a></div>"; 

	
	?>
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>

	   

	  <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0" >
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
			    <td class="dataTableHeadingContent" width="20px;">&nbsp;</td>
                <td class="dataTableHeadingContent" ><?php echo TABLE_HEADING_ADDONS; ?></td>
				<td class="dataTableHeadingContent" width="130"><?php echo TABLE_HEADING_FILENAME; ?></td>
				<td class="dataTableHeadingContent" width="50"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right" width="50"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
  $directory_array = array();
  if ($dir = @dir($module_directory)) 
  {
     while ($file = $dir->read())
	 { 
        if (is_dir($module_directory . $file) && $file != '.' && $file != '..' && $file != '.svn') 
		{
		   $directory_array[] = $file;
        }
     }
     sort($directory_array);
     $dir->close();
  }

  $installed_modules = array();
  for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) 
  {
    $file = $directory_array[$i];

   // include(DIR_FS_LANGUAGES . $_SESSION['language_admin'] . '/modules/' . $module_type . '/' . $file . '.php');
   if (is_file($module_directory . $file . '/' . $file . '.php'))
   {
      include($module_directory . $file . '/' . $file . '.php');
   }
	
	if (is_file($module_directory . $file . '/' . $_SESSION['language_admin'] . '.php'))
	{
       include($module_directory . $file . '/' . $_SESSION['language_admin'] . '.php');
	}   
	else
	{
	   if (is_file($module_directory . $file . '/ru.php'))
	   {
	       include($module_directory . $file . '/ru.php');
	   }
	}
    //$class = substr($file, 0, strrpos($file, '.'));
    $class = $file;
    if (os_class_exists($class)) 
	{
       $module = new $class();
       if ($module->check() > 0) 
	   {
          if ($module->sort_order > 0) 
		  {
             if ($installed_modules[$module->sort_order] != '') 
			 {
                $zc_valid = false;
             }        
             $installed_modules[$module->sort_order] = $file;
          } 
		  else 
		  {
             $installed_modules[] = $file;
          }
      }

      if (((@!$_GET['module']) || (@$_GET['module'] == $class)) && (@!$mInfo)) 
	  {
        $module_info = array('code' => $module->code,
                             'title' => $module->title,
                             'description' => $module->description,
                             'status' => $module->check());

        $module_keys = $module->keys();

        $keys_extra = array();
        for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
          $key_value_query = os_db_query("select configuration_key,configuration_value, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_keys[$j] . "'");
          $key_value = os_db_fetch_array($key_value_query);
          if ($key_value['configuration_key'] !='')  $keys_extra[$module_keys[$j]]['title'] = constant(strtoupper($key_value['configuration_key'] .'_TITLE'));
          $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
          if ($key_value['configuration_key'] !='')  $keys_extra[$module_keys[$j]]['description'] = constant(strtoupper($key_value['configuration_key'] .'_DESC'));
          $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
          $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
        }

        $module_info['keys'] = $keys_extra;

        $mInfo = new objectInfo($module_info);
      }
$color = '';
      if ( (@is_object($mInfo)) && ($class == $mInfo->code) ) {
        if ($module->check() > 0) {
          echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class . '&action=edit') . '\'">' . "\n";
        } else {
          echo '<tr class="dataTableRowSelected">' . "\n";
        }
      } else {
	  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
        echo '<tr onmouseover="this.style.background=\'#e9fff1\';" onmouseout="this.style.background=\''.$color.'\';"  style="background-color:'.$color;
		echo ';" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class) . '\'">' . "\n";
      }
	  	     
?><td class="dataTableContent" style="padding:5px;text-align:center;">
<?php 
if ($module_type=='payment'  )
{
    if ( isset($module->icon_small) && is_file($module_directory.$module->code.'/'.$module->icon_small) )
	{
        echo '<img width="16px" height="16px" src="'.http_path('modules').$module_type.'/'.$module->code.'/'.$module->icon_small.'" border="0" />'; 
	}
	else
	{
	      echo '<div class="icon_module">&nbsp;</div>';
	}
}
else 
{
    echo '<div class="icon_module">&nbsp;</div>';
}

?></td>

                <td class="dataTableContent"><?php echo $module->title; ?></td>
				<td class="dataTableContent"><?php echo str_replace('.php','',$file); ?></td>
			   <td class="dataTableContent" align="center">
             <?php
			 
			 
			 
			// print($mInfo->status); 
            if (is_numeric($module->sort_order)) {
                 echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="modules.php?action=remove&module='.str_replace('.php','',$file).'&set='.$_GET['set'] . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
             } 
			 else 
			 {
                 echo '<a href="modules.php?'.'action=install&module='.str_replace('.php','',$file).'&set='.$_GET['set'].'">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
             }
             ?>
             </td>
				

                <td class="dataTableContent" align="right"><?php if ( (@is_object($mInfo)) && ($class == $mInfo->code) ) 
				{ 
				     echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); 
				} 
				else 
				{ 
				   echo '<a href="' . os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
				} ?>&nbsp;</td>
              </tr>
<?php
    }
  }

  ksort($installed_modules);
  $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_key . "'");
  if (os_db_num_rows($check_query)) {
    $check = os_db_fetch_array($check_query);
    if ($check['configuration_value'] != implode(';', $installed_modules)) {
      os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . implode('.php;', $installed_modules) . ".php', last_modified = now() where configuration_key = '" . $module_key . "'");
    }
  } else {
    os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ( '" . $module_key . "', '" . implode(';', $installed_modules) . "','6', '0', now())");
  }
  if (isset($zc_valid) && $zc_valid == false) {
    $messageStack->add_session(WARNING_MODULES_SORT_ORDER, 'error');
  }  
?>

            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (@$_GET['action']) {
    case 'edit':
      $keys = '';
      reset($mInfo->keys);
      while (list($key, $value) = each($mInfo->keys)) {
        $keys .= '<b>' . $value['title'] . '</b><br />' .  $value['description'].'<br />';
        if ($value['set_function']) {
          eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
        } else {
          $keys .= os_draw_input_field('configuration[' . $key . ']', $value['value']);
        }
        $keys .= '<br /><br />';
      }
      $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

      $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');

      $contents = array('form' => os_draw_form('modules', FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=save'));
      $contents[] = array('text' => $keys);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');

      if ($mInfo->status == '1') {
        $keys = '';
        reset($mInfo->keys);
        while (list(, $value) = each($mInfo->keys)) {
          $keys .= '<b>' . $value['title'] . '</b><br />';
          if ($value['use_function']) {
            $use_function = $value['use_function'];
            if (preg_match('/->/', $use_function)) {
              $class_method = explode('->', $use_function);
              if (!is_object(${$class_method[0]})) {
                include(get_path('class_admin') . $class_method[0] . '.php');
                ${$class_method[0]} = new $class_method[0]();
              }
              $keys .= os_call_function($class_method[1], $value['value'], ${$class_method[0]});
            } else {
              $keys .= os_call_function($use_function, $value['value']);
            }
          } else {
		  if(strlen($value['value']) > 30) {
		  $keys .=  substr($value['value'],0,30) . ' ...';
		  } else {
            $keys .=  $value['value'];
			}
          }
          $keys .= '<br /><br />';
        }
        $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=remove') . '"><span>' . BUTTON_MODULE_REMOVE . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a>');
        $contents[] = array('text' => '<br />' . $mInfo->description);
        $contents[] = array('text' => '<br />' . $keys);
      } else {
        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=install') . '"><span>' . BUTTON_MODULE_INSTALL . '</span></a>');
        $contents[] = array('text' => '<br />' . $mInfo->description);
      }
      break;
  }

  if ( (os_not_null($heading)) && (os_not_null($contents)) ) {
    echo '            <td class="right_box" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>