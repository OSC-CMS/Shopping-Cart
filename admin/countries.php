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

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $countries_name = os_db_prepare_input($_POST['countries_name']);
        $countries_iso_code_2 = os_db_prepare_input($_POST['countries_iso_code_2']);
        $countries_iso_code_3 = os_db_prepare_input($_POST['countries_iso_code_3']);
        $address_format_id = os_db_prepare_input($_POST['address_format_id']);

        os_db_query("insert into " . TABLE_COUNTRIES . " (countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id) values ('" . os_db_input($countries_name) . "', '" . os_db_input($countries_iso_code_2) . "', '" . os_db_input($countries_iso_code_3) . "', '" . os_db_input($address_format_id) . "')");
        os_redirect(os_href_link(FILENAME_COUNTRIES));
        break;
      case 'save':
        $countries_id = os_db_prepare_input($_GET['cID']);
        $countries_name = os_db_prepare_input($_POST['countries_name']);
        $countries_iso_code_2 = os_db_prepare_input($_POST['countries_iso_code_2']);
        $countries_iso_code_3 = os_db_prepare_input($_POST['countries_iso_code_3']);
        $address_format_id = os_db_prepare_input($_POST['address_format_id']);

        os_db_query("update " . TABLE_COUNTRIES . " set countries_name = '" . os_db_input($countries_name) . "', countries_iso_code_2 = '" . os_db_input($countries_iso_code_2) . "', countries_iso_code_3 = '" . os_db_input($countries_iso_code_3) . "', address_format_id = '" . os_db_input($address_format_id) . "' where countries_id = '" . os_db_input($countries_id) . "'");
        os_redirect(os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
        break;
      case 'deleteconfirm':
        $countries_id = os_db_prepare_input($_GET['cID']);

        os_db_query("delete from " . TABLE_COUNTRIES . " where countries_id = '" . os_db_input($countries_id) . "'");
        os_redirect(os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page']));
        break;
      case 'setlflag':
        $countries_id = os_db_prepare_input($_GET['cID']);
        $status = os_db_prepare_input($_GET['flag']);      
        os_db_query("update " . TABLE_COUNTRIES . " set status = '" . os_db_input($status) . "' where countries_id = '" . os_db_input($countries_id) . "'");
        os_redirect(os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));      
      break;  
    }
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center" colspan="2"><?php echo TABLE_HEADING_COUNTRY_CODES; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>                
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $countries_query_raw = "select countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, status, address_format_id from " . TABLE_COUNTRIES . " order by countries_name";
  $countries_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $countries_query_raw, $countries_query_numrows);
  $countries_query = os_db_query($countries_query_raw);
  
  while ($countries = os_db_fetch_array($countries_query)) {
    if (((!$_GET['cID']) || (@$_GET['cID'] == $countries['countries_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($countries);
    }
   $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff'; 
    if ( (is_object($cInfo)) && ($countries['countries_id'] == $cInfo->countries_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';"  style="background-color:'.$color.'" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $countries['countries_name']; ?></td>
                <td class="dataTableContent" align="center" width="40"><?php echo $countries['countries_iso_code_2']; ?></td>
                <td class="dataTableContent" align="center" width="40"><?php echo $countries['countries_iso_code_3']; ?></td>
                <td class="dataTableContent" align="center">
<?php               
      if ($countries['status'] == '1') {
        echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_COUNTRIES, os_get_all_get_params(array('page', 'action', 'cID')) . 'action=setlflag&flag=0&cID=' . $countries['countries_id'] . '&page='.$_GET['page']) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . os_href_link(FILENAME_COUNTRIES, os_get_all_get_params(array('page', 'action', 'cID')) . 'action=setlflag&flag=1&cID=' . $countries['countries_id'].'&page='.$_GET['page']) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }                
?>
                </td>                
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($countries['countries_id'] == $cInfo->countries_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $countries_split->display_count($countries_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
                    <td class="smallText" align="right"><?php echo $countries_split->display_links($countries_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_NEW_COUNTRY . '</span></a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_COUNTRY . '</b>');

      $contents = array('form' => os_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . os_draw_input_field('countries_name'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_2 . '<br />' . os_draw_input_field('countries_iso_code_2'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_3 . '<br />' . os_draw_input_field('countries_iso_code_3'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ADDRESS_FORMAT . '<br />' . os_draw_pull_down_menu('address_format_id', os_get_address_formats()));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_COUNTRY . '</b>');

      $contents = array('form' => os_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . os_draw_input_field('countries_name', $cInfo->countries_name));
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_2 . '<br />' . os_draw_input_field('countries_iso_code_2', $cInfo->countries_iso_code_2));
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_3 . '<br />' . os_draw_input_field('countries_iso_code_3', $cInfo->countries_iso_code_3));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ADDRESS_FORMAT . '<br />' . os_draw_pull_down_menu('address_format_id', os_get_address_formats(), $cInfo->address_format_id));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_COUNTRY . '</b>');

      $contents = array('form' => os_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $cInfo->countries_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->countries_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . $cInfo->countries_name);
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_2 . ' ' . $cInfo->countries_iso_code_2);
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_3 . ' ' . $cInfo->countries_iso_code_3);
        $contents[] = array('text' => '<br />' . TEXT_INFO_ADDRESS_FORMAT . ' ' . $cInfo->address_format_id);
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