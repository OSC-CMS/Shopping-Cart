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
      $cross_sell_id = os_db_prepare_input($_GET['oID']);

      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	      if($languages[$i]['status']==1) {
        $cross_sell_name_array = $_POST['cross_sell_group_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('groupname' => os_db_prepare_input($cross_sell_name_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          if (!os_not_null($cross_sell_id)) {
            $next_id_query = os_db_query("select max(products_xsell_grp_name_id) as products_xsell_grp_name_id from " . TABLE_PRODUCTS_XSELL_GROUPS . "");
            $next_id = os_db_fetch_array($next_id_query);
            $cross_sell_id = $next_id['products_xsell_grp_name_id'] + 1;
          }

          $insert_sql_data = array('products_xsell_grp_name_id' => $cross_sell_id,
                                   'language_id' => $language_id);
          $sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
          os_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          os_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array, 'update', "products_xsell_grp_name_id = '" . os_db_input($cross_sell_id) . "' and language_id = '" . $language_id . "'");
        }
		}
      }


      os_redirect(os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell_id));
      break;

    case 'deleteconfirm':
      $oID = os_db_prepare_input($_GET['oID']);

      os_db_query("delete from " . TABLE_PRODUCTS_XSELL_GROUPS . " where products_xsell_grp_name_id = '" . os_db_input($oID) . "'");

      os_redirect(os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $oID = os_db_prepare_input($_GET['oID']);

      $cross_sell_query = os_db_query("select count(*) as count from " . TABLE_PRODUCTS_XSELL . " where products_xsell_grp_name_id = '" . os_db_input($oID) . "'");
      $status = os_db_fetch_array($cross_sell_query);

      $remove_status = true;
      if ($status['count'] > 0) {
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_CROSS_SELLS, 'error');
      }
      break;
  }
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
	<?php os_header('cross_sell_groups.png',BOX_CONFIGURATION." / ".BOX_ORDERS_XSELL_GROUP); ?> 
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_XSELL_GROUP_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $cross_sell_query_raw = "select products_xsell_grp_name_id, groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_xsell_grp_name_id";
  $cross_sell_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $cross_sell_query_raw, $cross_sell_query_numrows);
  $cross_sell_query = os_db_query($cross_sell_query_raw);
  while ($cross_sell = os_db_fetch_array($cross_sell_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $cross_sell['products_xsell_grp_name_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($cross_sell);
    }
	$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($oInfo)) && ($cross_sell['products_xsell_grp_name_id'] == $oInfo->products_xsell_grp_name_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell['products_xsell_grp_name_id']) . '\'">' . "\n";
    }


      echo '                <td class="dataTableContent">' . $cross_sell['groupname'] . '</td>' . "\n";
    
?>
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($cross_sell['products_xsell_grp_name_id'] == $oInfo->products_xsell_grp_name_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell['products_xsell_grp_name_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $cross_sell_split->display_count($cross_sell_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_XSELL_GROUP); ?></td>
                    <td class="smallText" align="right"><?php echo $cross_sell_split->display_links($cross_sell_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
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
  switch ($_GET['action']) 
  {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_XSELL_GROUP . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

      $cross_sell_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
	  {
	       if($languages[$i]['status']==1) {
        $cross_sell_inputs_string .= '<br />' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif') . '&nbsp;' . os_draw_input_field('cross_sell_group_name[' . $languages[$i]['id'] . ']');
		}
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_XSELL_GROUP_NAME . $cross_sell_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_XSELL_GROUP . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $cross_sell_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	    if($languages[$i]['status']==1) 
		{
        $cross_sell_inputs_string .= '<br />' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif') . '&nbsp;' . os_draw_input_field('cross_sell_group_name[' . $languages[$i]['id'] . ']', os_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']));
		}
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_XSELL_GROUP_NAME . $cross_sell_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_XSELL_GROUP . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $oInfo->orders_status_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($oInfo)) {
        $heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');

        $cross_sell_inputs_string = '';
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if($languages[$i]['status']==1) {
          $cross_sell_inputs_string .= '<br />' . os_image(http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif') . '&nbsp;' . os_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']);
		  }
        }

        $contents[] = array('text' => $cross_sell_inputs_string);
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