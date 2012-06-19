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

   define('DEFAULT_PRODUCTS_VPE_ID','1');

  require('includes/top.php');

  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $products_vpe_id = os_db_prepare_input($_GET['oID']);

      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $products_vpe_name_array = $_POST['products_vpe_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('products_vpe_name' => os_db_prepare_input($products_vpe_name_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          if (!os_not_null($products_vpe_id)) {
            $next_id_query = os_db_query("select max(products_vpe_id) as products_vpe_id from " . TABLE_PRODUCTS_VPE . "");
            $next_id = os_db_fetch_array($next_id_query);
            $products_vpe_id = $next_id['products_vpe_id'] + 1;
          }

          $insert_sql_data = array('products_vpe_id' => $products_vpe_id,
                                   'language_id' => $language_id);
          $sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
          os_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          os_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array, 'update', "products_vpe_id = '" . os_db_input($products_vpe_id) . "' and language_id = '" . $language_id . "'");
        }
      }

      if ($_POST['default'] == 'on') {
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . os_db_input($products_vpe_id) . "' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
        //set_configuration_cache(); 
	  }

      os_redirect(os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe_id));
      break;

    case 'deleteconfirm':
      $oID = os_db_prepare_input($_GET['oID']);

      $products_vpe_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
      $products_vpe = os_db_fetch_array($products_vpe_query);
      if ($products_vpe['configuration_value'] == $oID) {
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
		//set_configuration_cache(); 
      }

      os_db_query("delete from " . TABLE_PRODUCTS_VPE . " where products_vpe_id = '" . os_db_input($oID) . "'");

      os_redirect(os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $oID = os_db_prepare_input($_GET['oID']);


      $remove_status = true;
      if ($oID == DEFAULT_PRODUCTS_VPE_ID) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_PRODUCTS_VPE, 'error');
      } 
      break;
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
   
	<?php os_header('portfolio_package.gif',BOX_CONFIGURATION." / ".BOX_PRODUCTS_VPE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_VPE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $products_vpe_query_raw = "select products_vpe_id, products_vpe_name from " . TABLE_PRODUCTS_VPE . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_vpe_id";
  $products_vpe_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_vpe_query_raw, $products_vpe_query_numrows);
  $products_vpe_query = os_db_query($products_vpe_query_raw);
  while ($products_vpe = os_db_fetch_array($products_vpe_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $products_vpe['products_vpe_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($products_vpe);
    }
	 $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($oInfo)) && ($products_vpe['products_vpe_id'] == $oInfo->products_vpe_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe['products_vpe_id']) . '\'">' . "\n";
    }

    if (DEFAULT_PRODUCTS_VPE_ID == $products_vpe['products_vpe_id']) {
      echo '                <td class="dataTableContent"><b>' . $products_vpe['products_vpe_name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $products_vpe['products_vpe_name'] . '</td>' . "\n";
    }
?>
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($products_vpe['products_vpe_id'] == $oInfo->products_vpe_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe['products_vpe_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $products_vpe_split->display_count($products_vpe_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_VPE); ?></td>
                    <td class="smallText" align="right"><?php echo $products_vpe_split->display_links($products_vpe_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_PRODUCTS_VPE . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

      $products_vpe_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $products_vpe_inputs_string .= '<br />' . os_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . os_draw_input_field('products_vpe_name[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_PRODUCTS_VPE_NAME . $products_vpe_inputs_string);
      $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_PRODUCTS_VPE . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $products_vpe_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $products_vpe_inputs_string .= '<br />' . os_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . os_draw_input_field('products_vpe_name[' . $languages[$i]['id'] . ']', os_get_products_vpe_name($oInfo->products_vpe_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_PRODUCTS_VPE_NAME . $products_vpe_inputs_string);
      if (DEFAULT_PRODUCTS_VPE_ID != $oInfo->products_vpe_id) $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCTS_VPE . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $oInfo->products_vpe_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($oInfo)) {

        $heading[] = array('text' => '<b>' . $oInfo->products_vpe_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');

        $products_vpe_inputs_string = '';
        $languages = os_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $products_vpe_inputs_string .= '<br />' . os_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . os_get_products_vpe_name($oInfo->products_vpe_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $products_vpe_inputs_string);
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