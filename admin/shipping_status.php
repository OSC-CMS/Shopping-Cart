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

  switch ($_GET['action']) {
    case 'insert':
    case 'save':
	   
      $shipping_status_id = os_db_prepare_input($_GET['oID']);

      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	     if($languages[$i]['status']==1) {
        $shipping_status_name_array = $_POST['shipping_status_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('shipping_status_name' => os_db_prepare_input($shipping_status_name_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          if (!os_not_null($shipping_status_id)) {
            $next_id_query = os_db_query("select max(shipping_status_id) as shipping_status_id from " . TABLE_SHIPPING_STATUS . "");
            $next_id = os_db_fetch_array($next_id_query);
            $shipping_status_id = $next_id['shipping_status_id'] + 1;
          }

          $insert_sql_data = array('shipping_status_id' => $shipping_status_id,
                                   'language_id' => $language_id);
          $sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
          os_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          os_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array, 'update', "shipping_status_id = '" . os_db_input($shipping_status_id) . "' and language_id = '" . $language_id . "'");
        }
		}
      }

      if ($shipping_status_image = &os_try_upload('shipping_status_image',http_path('icons_admin'))) {
        os_db_query("update " . TABLE_SHIPPING_STATUS . " set shipping_status_image = '" . $shipping_status_image->filename . "' where shipping_status_id = '" . os_db_input($shipping_status_id) . "'");
      }

      if ($_POST['default'] == 'on') 
	  {
         os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . os_db_input($shipping_status_id) . "' where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
        // set_configuration_cache(); 
	  }
	  
      set_default_cache();
      os_redirect(os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status_id));
      break;

    case 'deleteconfirm':
	   
      $oID = os_db_prepare_input($_GET['oID']);

      $shipping_status_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
      $shipping_status = os_db_fetch_array($shipping_status_query);
      if ($shipping_status['configuration_value'] == $oID) {
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
		//set_configuration_cache(); 
      }

      os_db_query("delete from " . TABLE_SHIPPING_STATUS . " where shipping_status_id = '" . os_db_input($oID) . "'");
      set_default_cache();
	  
      os_redirect(os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page']));
	  
      break;

    case 'delete':
	   
      $oID = os_db_prepare_input($_GET['oID']);


      $remove_status = true;
      if ($oID == DEFAULT_SHIPPING_STATUS_ID) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_SHIPPING_STATUS, 'error');
      } else {

      }
	  set_default_cache();
      break;
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
  
	<?php $main->heading('portfolio_package.gif', BOX_CONFIGURATION." / ".BOX_SHIPPING_STATUS); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" width="1"><?php echo TABLE_HEADING_SHIPPING_STATUS; ?></td>
                <td class="dataTableHeadingContent" width="100%">&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $shipping_status_query_raw = "select shipping_status_id, shipping_status_name,shipping_status_image from " . TABLE_SHIPPING_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by shipping_status_id";
  $shipping_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $shipping_status_query_raw, $shipping_status_query_numrows);
  $shipping_status_query = os_db_query($shipping_status_query_raw);
  while ($shipping_status = os_db_fetch_array($shipping_status_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $shipping_status['shipping_status_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($shipping_status);
    }
	$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($oInfo)) && ($shipping_status['shipping_status_id'] == $oInfo->shipping_status_id) ) {
      echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status['shipping_status_id']) . '\'">' . "\n";
    }

    if (DEFAULT_SHIPPING_STATUS_ID == $shipping_status['shipping_status_id']) {
        echo '<td class="dataTableContent" align="left">';
     if ($shipping_status['shipping_status_image'] != '') {
       echo os_image(http_path('icons_admin') . $shipping_status['shipping_status_image'] , IMAGE_ICON_INFO);
     }
     echo '</td>';
      echo '                <td class="dataTableContent"><b>' . $shipping_status['shipping_status_name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {

      			echo '<td class="dataTableContent" align="left">';
                       if ($shipping_status['shipping_status_image'] != '') {
                           echo os_image(http_path('icons_admin') . $shipping_status['shipping_status_image'] , IMAGE_ICON_INFO);
                           }
                           echo '</td>';
      echo '                <td class="dataTableContent">' . $shipping_status['shipping_status_name'] . '</td>' . "\n";
    }
?>
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($shipping_status['shipping_status_id'] == $oInfo->shipping_status_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status['shipping_status_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $shipping_status_split->display_count($shipping_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SHIPPING_STATUS); ?></td>
                    <td class="smallText" align="right"><?php echo $shipping_status_split->display_links($shipping_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
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
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SHIPPING_STATUS . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

      $shipping_status_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	  if($languages[$i]['status']==1) {
        $shipping_status_inputs_string .= '<br />' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif') . '&nbsp;' . os_draw_input_field('shipping_status_name[' . $languages[$i]['id'] . ']');
		}
      }
      $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_IMAGE . '<br />' . os_draw_file_field('shipping_status_image'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_NAME . $shipping_status_inputs_string);
      $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SHIPPING_STATUS . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id  . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $shipping_status_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	  if($languages[$i]['status']==1) {
        $shipping_status_inputs_string .= '<br />' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif') . '&nbsp;' . os_draw_input_field('shipping_status_name[' . $languages[$i]['id'] . ']', os_get_shipping_status_name($oInfo->shipping_status_id, $languages[$i]['id']));
		}
      }
      $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_IMAGE . '<br />' . os_draw_file_field('shipping_status_image',$oInfo->shipping_status_image));
      $contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_NAME . $shipping_status_inputs_string);
      if (DEFAULT_SHIPPING_STATUS_ID != $oInfo->shipping_status_id) $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SHIPPING_STATUS . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $oInfo->shipping_status_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($oInfo)) {
        $heading[] = array('text' => '<b>' . $oInfo->shipping_status_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');

        $shipping_status_inputs_string = '';
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
          $shipping_status_inputs_string .= '<br />' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif') . '&nbsp;' . os_get_shipping_status_name($oInfo->shipping_status_id, $languages[$i]['id']);
		  }
        }

        $contents[] = array('text' => $shipping_status_inputs_string);
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