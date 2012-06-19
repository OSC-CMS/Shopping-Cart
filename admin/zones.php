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

  if ($_GET['action']) 
  {
    switch ($_GET['action']) 
	{
      case 'insert':
        $zone_country_id = os_db_prepare_input($_POST['zone_country_id']);
        $zone_code = os_db_prepare_input($_POST['zone_code']);
        $zone_name = os_db_prepare_input($_POST['zone_name']);

        os_db_query("insert into " . TABLE_ZONES . " (zone_country_id, zone_code, zone_name) values ('" . os_db_input($zone_country_id) . "', '" . os_db_input($zone_code) . "', '" . os_db_input($zone_name) . "')");
        os_redirect(os_href_link(FILENAME_ZONES));
        break;
		
      case 'save':
        $zone_id = os_db_prepare_input($_GET['cID']);
        $zone_country_id = os_db_prepare_input($_POST['zone_country_id']);
        $zone_code = os_db_prepare_input($_POST['zone_code']);
        $zone_name = os_db_prepare_input($_POST['zone_name']);

        os_db_query("update " . TABLE_ZONES . " set zone_country_id = '" . os_db_input($zone_country_id) . "', zone_code = '" . os_db_input($zone_code) . "', zone_name = '" . os_db_input($zone_name) . "' where zone_id = '" . os_db_input($zone_id) . "'");
        os_redirect(os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $zone_id));
        break;
		
      case 'deleteconfirm':
        $zone_id = os_db_prepare_input($_GET['cID']);

        os_db_query("delete from " . TABLE_ZONES . " where zone_id = '" . os_db_input($zone_id) . "'");
        os_redirect(os_href_link(FILENAME_ZONES, 'page=' . $_GET['page']));
        break;
    }
  }

   $main->head();  
   $main->top_menu(); 
?>

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
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ZONE_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ZONE_CODE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $zones_query_raw = "select z.zone_id, c.countries_id, c.countries_name, z.zone_name, z.zone_code, z.zone_country_id from " . TABLE_ZONES . " z, " . TABLE_COUNTRIES . " c where z.zone_country_id = c.countries_id order by c.countries_name, z.zone_name";
  $zones_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $zones_query_raw, $zones_query_numrows);
  $zones_query = os_db_query($zones_query_raw);
  
  while ($zones = os_db_fetch_array($zones_query)) {
    if (((!$_GET['cID']) || (@$_GET['cID'] == $zones['zone_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($zones);
    }
	
    $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($cInfo)) && ($zones['zone_id'] == $cInfo->zone_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr style="background-color:'.$color.'"  class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $zones['zone_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $zones['countries_name']; ?></td>
                <td class="dataTableContent"><?php echo $zones['zone_name']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $zones['zone_code']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($zones['zone_id'] == $cInfo->zone_id) ) 
				{ 
				   echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); 
				} 
				else 
				{ echo '<a href="' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $zones['zone_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ZONES); ?></td>
                    <td class="smallText" align="right"><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_NEW_ZONE . '</span></a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ZONE . '</b>');

      $contents = array('form' => os_draw_form('zones', FILENAME_ZONES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONES_NAME . '<br />' . os_draw_input_field('zone_name'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONES_CODE . '<br />' . os_draw_input_field('zone_code'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . os_draw_pull_down_menu('zone_country_id', os_get_countries()));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ZONE . '</b>');

      $contents = array('form' => os_draw_form('zones', FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONES_NAME . '<br />' . os_draw_input_field('zone_name', $cInfo->zone_name));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONES_CODE . '<br />' . os_draw_input_field('zone_code', $cInfo->zone_code));
      $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . os_draw_pull_down_menu('zone_country_id', os_get_countries(), $cInfo->countries_id));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ZONE . '</b>');

      $contents = array('form' => os_draw_form('zones', FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $cInfo->zone_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->zone_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONES_NAME . '<br />' . $cInfo->zone_name . ' (' . $cInfo->zone_code . ')');
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . ' ' . $cInfo->countries_name);
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