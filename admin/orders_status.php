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
      $orders_status_id = os_db_prepare_input($_GET['oID']);

      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_name_array = $_POST['orders_status_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('orders_status_name' => os_db_prepare_input($orders_status_name_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          if (!os_not_null($orders_status_id)) {
            $next_id_query = os_db_query("select max(orders_status_id) as orders_status_id from " . TABLE_ORDERS_STATUS . "");
            $next_id = os_db_fetch_array($next_id_query);
            $orders_status_id = $next_id['orders_status_id'] + 1;
          }

          $insert_sql_data = array('orders_status_id' => $orders_status_id,
                                   'language_id' => $language_id);
          $sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
          os_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          os_db_perform(TABLE_ORDERS_STATUS, $sql_data_array, 'update', "orders_status_id = '" . os_db_input($orders_status_id) . "' and language_id = '" . $language_id . "'");
        }
      }

      if ($_POST['default'] == 'on') {
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . os_db_input($orders_status_id) . "' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
        ///set_configuration_cache(); 
	  }

      os_redirect(os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status_id));
      break;

    case 'deleteconfirm':
      $oID = os_db_prepare_input($_GET['oID']);

      $orders_status_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
      $orders_status = os_db_fetch_array($orders_status_query);
      if ($orders_status['configuration_value'] == $oID) {
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
		///set_configuration_cache(); 
      }

      os_db_query("delete from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . os_db_input($oID) . "'");

      os_redirect(os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $oID = os_db_prepare_input($_GET['oID']);

      $status_query = os_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . os_db_input($oID) . "'");
      $status = os_db_fetch_array($status_query);

      $remove_status = true;
      if ($oID == DEFAULT_ORDERS_STATUS_ID) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_ORDER_STATUS, 'error');
      } elseif ($status['count'] > 0) {
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_ORDERS, 'error');
      } else {
        $history_query = os_db_query("select count(*) as count from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_status_id = '" . os_db_input($oID) . "'");
        $history = os_db_fetch_array($history_query);
        if ($history['count'] > 0) {
          $remove_status = false;
          $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
        }
      }
      break;
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
	<?php os_header('portfolio_package.gif',BOX_CONFIGURATION." / ".BOX_ORDERS_STATUS); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $orders_status_query_raw = "select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by orders_status_id";
  $orders_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $orders_status_query_raw, $orders_status_query_numrows);
  $orders_status_query = os_db_query($orders_status_query_raw);
 
  while ($orders_status = os_db_fetch_array($orders_status_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $orders_status['orders_status_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($orders_status);
    }
   $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) {
      echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '\'">' . "\n";
    }

    if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id']) {
      echo '                <td class="dataTableContent"><b>' . $orders_status['orders_status_name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $orders_status['orders_status_name'] . '</td>' . "\n";
    }
?>
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_status_split->display_count($orders_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?></td>
                    <td class="smallText" align="right"><?php echo $orders_status_split->display_links($orders_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ORDERS_STATUS . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

      $orders_status_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_inputs_string .= '<br />' . os_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . os_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ORDERS_STATUS . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $orders_status_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_inputs_string .= '<br />' . os_image( http_path('lang').$languages[$i]['code'].'/'.$languages[$i]['image']) . '&nbsp;' . os_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', os_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id) $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDERS_STATUS . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $oInfo->orders_status_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($oInfo)) {
        $heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');

        $orders_status_inputs_string = '';
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= '<br />' . os_image(http_path('catalog').'admin/themes/default/images/lang/'.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . os_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $orders_status_inputs_string);
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