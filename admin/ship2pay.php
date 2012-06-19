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

@$errorr = "";

function os_p2s_get_moduleinfo($module_type) 
{
   if($module_type == "shipping") 
   {
      $module_directory = _MODULES . 'shipping/';
	  $files = explode(';',MODULE_SHIPPING_INSTALLED);
   } 
   elseif($module_type == "payment")  
   {
	  $module_directory = _MODULES . 'payment/';
	  $files = explode(';',MODULE_PAYMENT_INSTALLED);
   }

   $installed_modules = array();

   foreach($files as $file) 
   {
	  if (!empty($file)) 
	  {
	     $fr = substr($file, 0, strrpos($file, '.'));
		 
		 if (is_file(_MODULES.$module_type.'/'.$fr.'/'.$_SESSION['language_admin'].'.php'))
		 {
		     include (_MODULES.$module_type.'/'.$fr.'/'.$_SESSION['language_admin'].'.php');
		 }
		 
		 if (is_file(_MODULES.$module_type.'/'.$fr.'/'.$fr.'.php'))
		 {
		     include (_MODULES.$module_type.'/'.$fr.'/'.$fr.'.php');
		 }
		 
		 $class = substr($file, 0, strrpos($file, '.'));
		 if (os_class_exists($class)) 
		 {
			$module = new $class;
			$installed_modules[$file] = $module->title;
		 }
	  } 
	  else 
	  {
	     $GLOBALS["errorr"] = TEXT_EMPTY_ERROR; 
      }		
   }
	
   return ($installed_modules);
}

function os_p2s_module_name($modules) 
{
   global $shipping_modules, $payment_modules;
   $files = explode(';',$modules);
   $names = '';
   foreach($files as $file) 
   {
	  $names .= $payment_modules[$file] . ', ';
   }
   return(rtrim($names, ', '));
}
	
$shipping_modules = os_p2s_get_moduleinfo('shipping');
$payment_modules = os_p2s_get_moduleinfo('payment');

if (@$_GET['action']) 
{
    switch ($_GET['action']) 
	{
      case 'insert':
        $shp_id = os_db_prepare_input($_POST['shp_id']);
        if (isset($_POST['pay_ids']))
		{
           $pay_ids = os_db_prepare_input(implode(";", $_POST['pay_ids']));
        }
        os_db_query("insert into " . TABLE_SHIP2PAY . " (shipment, payments_allowed, zones_id, status) values ('" . os_db_input($shp_id) . "', '" . os_db_input($pay_ids) . "', '" . (int)$_POST['configuration']['zone_id'] . "', 1)");
        os_redirect(os_href_link(FILENAME_SHIP2PAY));
        break;
      case 'save':
        $s2p_id = os_db_prepare_input($_GET['s2p_id']);
        $shp_id = os_db_prepare_input($_POST['shp_id']);
        if (isset($_POST['pay_ids']))
		{
           $pay_ids = os_db_prepare_input(implode(";", $_POST['pay_ids']));
        }
        os_db_query("update " . TABLE_SHIP2PAY . " set payments_allowed = '" . os_db_input($pay_ids) . "', shipment = '" . os_db_input($shp_id) . "', zones_id = '" . (int)$_POST['configuration']['zone_id'] . "' where s2p_id = ". os_db_input($s2p_id));
        os_redirect(os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
        break;
      case 'deleteconfirm':
        $s2p_id = os_db_prepare_input($_GET['s2p_id']);
        os_db_query("delete from " . TABLE_SHIP2PAY . " where s2p_id = " . os_db_input($s2p_id));
        os_redirect(os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page']));
        break;
      case 'set_flag':
        $shp_id = os_db_prepare_input($_GET['s2p_id']);
        os_db_query("update " . TABLE_SHIP2PAY . " set status = '" . (int)os_db_prepare_input($_GET['flag']) . "' where s2p_id = " . os_db_input($shp_id));
        os_redirect(os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
        break;
    }
}

   $main->head();
?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
<?php os_header('plugin.gif',HEADING_TITLE); ?> 
<?php
	echo "<div style=\"position:absolute;top:100px; right:3%;\"><a target=\"_blank\" style=\"color:#4378a1\" href=\"http://osc-cms.com/module/\">".MODULES_OTHER."</a></div>"; 
?>
<?php if (!empty($errorr)) { ?>
<table style="border: 1px groove ; border-color: #ff0000;padding-left:5px;margin-bottom:5px;-moz-border-radius:4px; border-radius:4px;-webkit-border-radius:4px;-khtml-border-radius:4px;" bgcolor="#fff3f3" border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td><font color="red"><?php echo $errorr;?></font></td></tr></table>
<?php
}
?>
  <?php if (empty($errorr)) { ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SHIPMENT; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ZONE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $s2p_query_raw = "select s2p_id, shipment, payments_allowed, zones_id, status from " . TABLE_SHIP2PAY;
  $s2p_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $s2p_query_raw, $s2p_query_numrows);
  $s2p_query = os_db_query($s2p_query_raw);
  while ($s2p = os_db_fetch_array($s2p_query)) 
  {
     if (((!$_GET['s2p_id']) || (@$_GET['s2p_id'] == $s2p['s2p_id'])) && (!$trInfo) && (substr($_GET['action'], 0, 3) != 'new')) 
	 {
        $trInfo = new objectInfo($s2p);
     }

	 $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
     if ( (is_object($trInfo)) && ($s2p['s2p_id'] == $trInfo->s2p_id) ) 
	 {
        echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=edit') . '\'">' . "\n";
     } 
	 else 
	 {
        echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" class="dataTableRow" this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p['s2p_id']) . '\'">' . "\n";
     }
?>
<td class="dataTableContent">&nbsp;<?php echo $shipping_modules[$s2p['shipment']]; ?></td>
<td class="dataTableContent">&nbsp;<?php echo os_get_zone_class_title($s2p['zones_id']); ?></td>
<td class="dataTableContent"><?php echo os_p2s_module_name($s2p['payments_allowed']); ?></td>
<td class="dataTableContent" align="center">
<?php
   if ($s2p['status'] == '1') 
   {
      echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p['s2p_id'] . '&action=set_flag&flag=0') . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
   } 
   else 
   {
      echo '<a href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p['s2p_id'] . '&action=set_flag&flag=1') . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
   }
?>
</td>
 <td class="dataTableContent" align="right">
<?php 
   if ( (is_object($trInfo)) && ($s2p['s2p_id'] == $trInfo->s2p_id) ) 
   { 
      echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); 
   } 
   else 
   { 
      echo '<a href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p['s2p_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
   } 
?>
&nbsp;</td></tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $s2p_split->display_count($s2p_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAYMENTS); ?></td>
                    <td class="smallText" align="right"><?php echo $s2p_split->display_links($s2p_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (@!$_GET['action']) {
?>
                  <tr>
                    <td colspan="5" align="right"><?php echo '<a class="button" href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (@$_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SHP2PAY . '</b>');
      $contents = array('form' => os_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
			$ship_menu = array();
			foreach($shipping_modules as $file => $title) {
				$ship_menu[] = array('id' => $file, 'text' => $title);
			}
			$contents[] = array('text' => '<br>' . TEXT_INFO_SHIPMENT . '<br>' . os_draw_pull_down_menu("shp_id", $ship_menu) );
			$contents[] = array('text' => '<br>' . TEXT_INFO_ZONE . '<br>' . os_cfg_pull_down_zone_classes(0, 'zone_id') );
			$pay_menu = '';
			foreach($payment_modules as $file => $title) {
				$pay_menu .= '<br />' . os_draw_checkbox_field('pay_ids[]', $file, false) . $title;
			}
      $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS . $pay_menu);
      $contents[] = array('align' => 'center', 'text' => '<br>' . '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>' . '&nbsp;<a class="button" href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SHP2PAY . '</b>');
      $contents = array('form' => os_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
			$ship_menu = array();
			foreach($shipping_modules as $file => $title) {
				$ship_menu[] = array('id' => $file, 'text' => $title);
			}
			$contents[] = array('text' => '<br>' . TEXT_INFO_SHIPMENT . '<br>' . os_draw_pull_down_menu("shp_id", $ship_menu, $trInfo->shipment) );
			$contents[] = array('text' => '<br>' . TEXT_INFO_ZONE . '<br>' . os_cfg_pull_down_zone_classes($trInfo->zones_id, 'zone_id') );
			$pay_menu = '';
			$allowed = explode(';',$trInfo->payments_allowed);
			foreach($payment_modules as $file => $title) {
				$pay_menu .= '<br />' . os_draw_checkbox_field('pay_ids[]', $file, (in_array($file, $allowed) ? true : false)) . $title;
			}
      $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS . $pay_menu);
      $contents[] = array('align' => 'center', 'text' => '<br>' . '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>' . '&nbsp;<a class="button" href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SHP2PAY . '</b>');
      $contents = array('form' => os_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS_ALLOWED . '<br><b>' . $shipping_modules[$trInfo->shipment] . ' >> <br></b>');
      $contents[] = array('text' => '<br><b>' . os_get_zone_class_title($trInfo->zones_id) . '<br>' . os_p2s_module_name($trInfo->payments_allowed) . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>' . '&nbsp;<a class="button" href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    default:
      if ( @is_object($trInfo) ) {
        $heading[] = array('text' => '<b>' . $shipping_modules[$trInfo->shipment] . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a>' .
                                                           '<a class="button" href="' . os_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>'
                                                           );
        $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS_ALLOWED . '<br><b>' . os_get_zone_class_title($trInfo->zones_id) .'</b>');
        $contents[] = array('text' => '<b>' . os_p2s_module_name($trInfo->payments_allowed) .'</b>');
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
<?php
	}
?>
  </tr>
</table>
<?php $main->bottom(); ?>