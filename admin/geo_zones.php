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

  switch ($_GET['saction']) {
    case 'insert_sub':
      $zID = os_db_prepare_input($_GET['zID']);
      $zone_country_id = os_db_prepare_input($_POST['zone_country_id']);
      $zone_id = os_db_prepare_input($_POST['zone_id']);

      os_db_query("insert into " . TABLE_ZONES_TO_GEO_ZONES . " (zone_country_id, zone_id, geo_zone_id, date_added) values ('" . os_db_input($zone_country_id) . "', '" . os_db_input($zone_id) . "', '" . os_db_input($zID) . "', now())");
      $new_subzone_id = os_db_insert_id();

      os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $new_subzone_id));
      break;

    case 'save_sub':
      $sID = os_db_prepare_input($_GET['sID']);
      $zID = os_db_prepare_input($_GET['zID']);
      $zone_country_id = os_db_prepare_input($_POST['zone_country_id']);
      $zone_id = os_db_prepare_input($_POST['zone_id']);

      os_db_query("update " . TABLE_ZONES_TO_GEO_ZONES . " set geo_zone_id = '" . os_db_input($zID) . "', zone_country_id = '" . os_db_input($zone_country_id) . "', zone_id = " . ((os_db_input($zone_id)) ? "'" . os_db_input($zone_id) . "'" : 'null') . ", last_modified = now() where association_id = '" . os_db_input($sID) . "'");

      os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']));
      break;

    case 'deleteconfirm_sub':
      $sID = os_db_prepare_input($_GET['sID']);

      os_db_query("delete from " . TABLE_ZONES_TO_GEO_ZONES . " where association_id = '" . os_db_input($sID) . "'");

      os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage']));
      break;
  }

  switch ($_GET['action']) {
    case 'insert_zone':
      $geo_zone_name = os_db_prepare_input($_POST['geo_zone_name']);
      $geo_zone_description = os_db_prepare_input($_POST['geo_zone_description']);

      os_db_query("insert into " . TABLE_GEO_ZONES . " (geo_zone_name, geo_zone_description, date_added) values ('" . os_db_input($geo_zone_name) . "', '" . os_db_input($geo_zone_description) . "', now())");
      $new_zone_id = os_db_insert_id();

      os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $new_zone_id));
      break;

    case 'save_zone':
      $zID = os_db_prepare_input($_GET['zID']);
      $geo_zone_name = os_db_prepare_input($_POST['geo_zone_name']);
      $geo_zone_description = os_db_prepare_input($_POST['geo_zone_description']);

      os_db_query("update " . TABLE_GEO_ZONES . " set geo_zone_name = '" . os_db_input($geo_zone_name) . "', geo_zone_description = '" . os_db_input($geo_zone_description) . "', last_modified = now() where geo_zone_id = '" . os_db_input($zID) . "'");

      os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']));
      break;

    case 'deleteconfirm_zone':
      $zID = os_db_prepare_input($_GET['zID']);

      os_db_query("delete from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . os_db_input($zID) . "'");
      os_db_query("delete from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . os_db_input($zID) . "'");

      os_redirect(os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage']));
      break;
  }
  
  add_action('head_admin', 'head_zones');
  
  function head_zones ()
  {
  
  if ($_GET['zID']  && (($_GET['saction'] == 'edit') || ($_GET['saction'] == 'new'))) {
?>
<script type="text/javascript"><!--
function resetZoneSelected(theForm) {
  if (theForm.state.value != '') {
    theForm.zone_id.selectedIndex = '0';
    if (theForm.zone_id.options.length > 0) {
      theForm.state.value = '<?php echo JS_STATE_SELECT; ?>';
    }
  }
}

function update_zone(theForm) {
  var NumState = theForm.zone_id.options.length;
  var SelectedCountry = "";

  while(NumState > 0) {
    NumState--;
    theForm.zone_id.options[NumState] = null;
  }         

  SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?php echo os_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

}
//--></script>
<?php
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
            <td valign="top">
<?php
  if ($_GET['action'] == 'list') {
?>
            <table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY_ZONE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;
    $zones_query_raw = "select a.association_id, a.zone_country_id, c.countries_name, a.zone_id, a.geo_zone_id, a.last_modified, a.date_added, z.zone_name from " . TABLE_ZONES_TO_GEO_ZONES . " a left join " . TABLE_COUNTRIES . " c on a.zone_country_id = c.countries_id left join " . TABLE_ZONES . " z on a.zone_id = z.zone_id where a.geo_zone_id = " . $_GET['zID'] . " order by association_id";
    $zones_split = new splitPageResults($_GET['spage'], MAX_DISPLAY_ADMIN_PAGE, $zones_query_raw, $zones_query_numrows);
    $zones_query = os_db_query($zones_query_raw);
    while ($zones = os_db_fetch_array($zones_query)) {
      $rows++;
      if (((!$_GET['sID']) || (@$_GET['sID'] == $zones['association_id'])) && (!$sInfo) && (substr($_GET['saction'], 0, 3) != 'new')) {
        $sInfo = new objectInfo($zones);
      }
      if ( (is_object($sInfo)) && ($zones['association_id'] == $sInfo->association_id) ) {
        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit') . '\'">' . "\n";
      } else {
        echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $zones['association_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo (($zones['countries_name']) ? $zones['countries_name'] : TEXT_ALL_COUNTRIES); ?></td>
                <td class="dataTableContent"><?php echo (($zones['zone_id']) ? $zones['zone_name'] : PLEASE_SELECT); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($sInfo)) && ($zones['association_id'] == $sInfo->association_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $zones['association_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="2" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['spage'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
                    <td class="smallText" align="right"><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['spage'], 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list', 'spage'); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="right" colspan="3"><?php if (!$_GET['saction']) echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']) . '"><span>' . BUTTON_BACK . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
              </tr>
            </table>
<?php
  } else {
?>
            <table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_ZONES; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $zones_query_raw = "select geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added from " . TABLE_GEO_ZONES . " order by geo_zone_name";
    $zones_split = new splitPageResults($_GET['zpage'], MAX_DISPLAY_ADMIN_PAGE, $zones_query_raw, $zones_query_numrows);
    $zones_query = os_db_query($zones_query_raw);
    while ($zones = os_db_fetch_array($zones_query)) {
      if (((!$_GET['zID']) || (@$_GET['zID'] == $zones['geo_zone_id'])) && (!$zInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
        $num_zones_query = os_db_query("select count(*) as num_zones from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $zones['geo_zone_id'] . "' group by geo_zone_id");
        if (os_db_num_rows($num_zones_query) > 0) {
          $num_zones = os_db_fetch_array($num_zones_query);
          $zones['num_zones'] = $num_zones['num_zones'];
        } else {
          $zones['num_zones'] = 0;
        }
        $zInfo = new objectInfo($zones);
      }
	  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';  
      if ( (is_object($zInfo)) && ($zones['geo_zone_id'] == $zInfo->geo_zone_id) ) {
        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list') . '\'">' . "\n";
      } else {
        echo '                  <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id'] . '&action=list') . '">' . os_image(http_path('icons_admin') . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;' . $zones['geo_zone_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($zInfo)) && ($zones['geo_zone_id'] == $zInfo->geo_zone_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="2" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['zpage'], TEXT_DISPLAY_NUMBER_OF_TAX_ZONES); ?></td>
                    <td class="smallText" align="right"><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['zpage'], '', 'zpage'); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="right" colspan="2"><?php if (!$_GET['action']) echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=new_zone') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
              </tr>
            </table>
<?php
  }
?>
            </td>
<?php
  $heading = array();
  $contents = array();

  if ($_GET['action'] == 'list') {
    switch ($_GET['saction']) {
      case 'new':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SUB_ZONE . '</b>');

        $contents = array('form' => os_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID'] . '&saction=insert_sub'));
        $contents[] = array('text' => TEXT_INFO_NEW_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY . '<br />' . os_draw_pull_down_menu('zone_country_id', os_get_countries(TEXT_ALL_COUNTRIES), '', 'onChange="update_zone(this.form);"'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_ZONE . '<br />' . os_draw_pull_down_menu('zone_id', os_prepare_country_zones_pull_down()));
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      case 'edit':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SUB_ZONE . '</b>');

        $contents = array('form' => os_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=save_sub'));
        $contents[] = array('text' => TEXT_INFO_EDIT_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY . '<br />' . os_draw_pull_down_menu('zone_country_id', os_get_countries(TEXT_ALL_COUNTRIES), $sInfo->zone_country_id, 'onChange="update_zone(this.form);"'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_ZONE . '<br />' . os_draw_pull_down_menu('zone_id', os_prepare_country_zones_pull_down($sInfo->zone_country_id), $sInfo->zone_id));
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SUB_ZONE . '</b>');

        $contents = array('form' => os_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=deleteconfirm_sub'));
        $contents[] = array('text' => TEXT_INFO_DELETE_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br /><b>' . $sInfo->countries_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      default:
        if (is_object($sInfo)) {
          $heading[] = array('text' => '<b>' . $sInfo->countries_name . '</b>');

          $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
          $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . os_date_short($sInfo->date_added));
          if (os_not_null($sInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . os_date_short($sInfo->last_modified));
        }
        break;
    }
  } else {
    switch ($_GET['action']) {
      case 'new_zone':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ZONE . '</b>');

        $contents = array('form' => os_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=insert_zone'));
        $contents[] = array('text' => TEXT_INFO_NEW_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . os_draw_input_field('geo_zone_name'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_DESCRIPTION . '<br />' . os_draw_input_field('geo_zone_description'));
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      case 'edit_zone':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ZONE . '</b>');

        $contents = array('form' => os_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=save_zone'));
        $contents[] = array('text' => TEXT_INFO_EDIT_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . os_draw_input_field('geo_zone_name', $zInfo->geo_zone_name));
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_DESCRIPTION . '<br />' . os_draw_input_field('geo_zone_description', $zInfo->geo_zone_description));
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      case 'delete_zone':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ZONE . '</b>');

        $contents = array('form' => os_draw_form('zones', FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=deleteconfirm_zone'));
        $contents[] = array('text' => TEXT_INFO_DELETE_ZONE_INTRO);
        $contents[] = array('text' => '<br /><b>' . $zInfo->geo_zone_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      default:
        if (is_object($zInfo)) {
          $heading[] = array('text' => '<b>' . $zInfo->geo_zone_name . '</b>');

          $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=edit_zone') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=delete_zone') . '"><span>' . BUTTON_DELETE . '</span></a>' . '<br> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list') . '"><span>' . BUTTON_DETAILS . '</span></a>');
          $contents[] = array('text' => '<br />' . TEXT_INFO_NUMBER_ZONES . ' ' . $zInfo->num_zones);
          $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . os_date_short($zInfo->date_added));
          if (os_not_null($zInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . os_date_short($zInfo->last_modified));
          $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_DESCRIPTION . '<br />' . $zInfo->geo_zone_description);
        }
        break;
    }
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