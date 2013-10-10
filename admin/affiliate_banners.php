<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

  require('includes/top.php');

  $affiliate_banner_extension = os_banner_image_extension();

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'setaffiliate_flag':
        if ( ($_GET['affiliate_flag'] == '0') || ($_GET['affiliate_flag'] == '1') ) {
          os_set_banner_status($_GET['abID'], $_GET['affiliate_flag']);
          $messageStack->add_session(SUCCESS_BANNER_STATUS_UPDATED, 'success');
        } else {
          $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
        }

        os_redirect(os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $_GET['abID']));
        break;
      case 'insert':
      case 'update':
        $affiliate_banners_id = os_db_prepare_input($_POST['affiliate_banners_id']);
        $affiliate_banners_title = os_db_prepare_input($_POST['affiliate_banners_title']);
        $affiliate_products_id  = os_db_prepare_input($_POST['affiliate_products_id']);
        $new_affiliate_banners_group = os_db_prepare_input($_POST['new_affiliate_banners_group']);
        $affiliate_banners_group = (empty($new_affiliate_banners_group)) ? os_db_prepare_input($_POST['affiliate_banners_group']) : $new_affiliate_banners_group;
        $affiliate_banners_image_target = os_db_prepare_input($_POST['affiliate_banners_image_target']);
        $affiliate_banners_image_local = os_db_prepare_input($_POST['affiliate_banners_image_local']);
        $db_image_location = '';

        $affiliate_banner_error = false;
        if (empty($affiliate_banners_title)) {
          $messageStack->add(ERROR_BANNER_TITLE_REQUIRED, 'error');
          $affiliate_banner_error = true;
          $_GET['action'] = 'new';
        }
        if (($_FILES['affiliate_banners_image']['name'] != '')) {
          if (!is_writeable( dir_path('images') )) {
          	$messageStack->add(ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
            $affiliate_banner_error = true;
            $_GET['action'] = 'new';
          }
          else {
          	$image_location = dir_path('images') . $_FILES['affiliate_banners_image']['name'];
          	move_uploaded_file($_FILES['affiliate_banners_image']['tmp_name'], $image_location);
          	@chmod($image_location, 0644);

            $db_image_location = $_FILES['affiliate_banners_image']['name'];
            
            if (!$affiliate_products_id) $affiliate_products_id="0";
            $sql_data_array = array('affiliate_banners_title' => $affiliate_banners_title,
                                    'affiliate_products_id' => $affiliate_products_id,
                                    'affiliate_banners_image' => $db_image_location,
                                    'affiliate_banners_group' => $affiliate_banners_group);
                                    
            if ($_GET['action'] == 'insert') {
            	$insert_sql_data = array('affiliate_date_added' => 'now()',
                                         'affiliate_status' => '1');
                $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                os_db_perform(TABLE_AFFILIATE_BANNERS, $sql_data_array);
                $affiliate_banners_id = os_db_insert_id();
                
                if ($affiliate_banners_id==1) os_db_query("update " . TABLE_AFFILIATE_BANNERS . " set affiliate_banners_id = affiliate_banners_id + 1");
                $messageStack->add_session(SUCCESS_BANNER_INSERTED, 'success');
            } elseif ($_GET['action'] == 'update') {
            	$insert_sql_data = array('affiliate_date_status_change' => 'now()');
            	$sql_data_array = array_merge($sql_data_array, $insert_sql_data);
            	os_db_perform(TABLE_AFFILIATE_BANNERS, $sql_data_array, 'update', 'affiliate_banners_id = \'' . $affiliate_banners_id . '\'');
            	$messageStack->add_session(SUCCESS_BANNER_UPDATED, 'success');
            }
            os_redirect(os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $affiliate_banners_id));
		  }
		}
        break;
      case 'deleteconfirm':
        $affiliate_banners_id = os_db_prepare_input($_GET['abID']);
        $delete_image = os_db_prepare_input($_POST['delete_image']);

        if ($delete_image == 'on') {
          $affiliate_banner_query = os_db_query("select affiliate_banners_image from " . TABLE_AFFILIATE_BANNERS . " where affiliate_banners_id = '" . os_db_input($affiliate_banners_id) . "'");
          $affiliate_banner = os_db_fetch_array($affiliate_banner_query);
          if (file_exists( dir_path('images') . $affiliate_banner['affiliate_banners_image'])) {
            if (is_writeable( dir_path('images') . $affiliate_banner['affiliate_banners_image'])) {
              unlink( dir_path('images') . $affiliate_banner['affiliate_banners_image']);
            } else {
              $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
            }
          } else {
            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
          }
        }

        os_db_query("delete from " . TABLE_AFFILIATE_BANNERS . " where affiliate_banners_id = '" . os_db_input($affiliate_banners_id) . "'");
        os_db_query("delete from " . TABLE_AFFILIATE_BANNERS_HISTORY . " where affiliate_banners_id = '" . os_db_input($affiliate_banners_id) . "'");

        $messageStack->add_session(SUCCESS_BANNER_REMOVED, 'success');

        os_redirect(os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page']));
        break;
    }
  }
  
  add_action('head_admin','affiliate_banner');
  
  function affiliate_banner()
  {
     _e('<script language="javascript"><!--
function popupImageWindow(url) {
  window.open(url,\'popupImageWindow\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150\')
}
//--></script>');
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main">

    <?php echo HEADING_TITLE; ?> 
        
        </td>
      </tr>

  <tr>
<?php
  if ($_GET['action'] == 'new') {
    $form_action = 'insert';
    if ($_GET['abID']) {
      $abID = os_db_prepare_input($_GET['abID']);
      $form_action = 'update';

      $affiliate_banner_query = os_db_query("select * from " . TABLE_AFFILIATE_BANNERS . " where affiliate_banners_id = '" . os_db_input($abID) . "'");
      $affiliate_banner = os_db_fetch_array($affiliate_banner_query);

      $abInfo = new objectInfo($affiliate_banner);
    } elseif ($_POST) {
      $abInfo = new objectInfo($_POST);
    } else {
      $abInfo = new objectInfo(array());
    }

    $groups_array = array();
    $groups_query = os_db_query("select distinct affiliate_banners_group from " . TABLE_AFFILIATE_BANNERS . " order by affiliate_banners_group");
    while ($groups = os_db_fetch_array($groups_query)) {
      $groups_array[] = array('id' => $groups['affiliate_banners_group'], 'text' => $groups['affiliate_banners_group']);
    }
?>
      <tr>
        <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo os_draw_form('new_banner', FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"'); if ($form_action == 'update') echo os_draw_hidden_field('affiliate_banners_id', $abID); ?>
        <td><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_TITLE; ?></td>
            <td class="main"><?php echo os_draw_input_field('affiliate_banners_title', $abInfo->affiliate_banners_title, '', true); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_LINKED_PRODUCT; ?></td>
            <td class="main"><?php echo os_draw_input_field('affiliate_products_id', $abInfo->affiliate_products_id, '', false); ?></td>
          </tr>
          <tr>
            <td class="main" colspan=2><?php echo TEXT_BANNERS_LINKED_PRODUCT_NOTE ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_BANNERS_IMAGE; ?></td>
            <td class="main"><?php echo os_draw_file_field('affiliate_banners_image') . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br>' . dir_path('images') . os_draw_input_field('affiliate_banners_image_local', $abInfo->affiliate_banners_image); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
<td class="main" align="right" valign="top" nowrap><?php echo (($form_action == 'insert') ? '<span class="button"><button type="submit" value="' . BUTTON_INSERT . '">' . BUTTON_INSERT . '</button></span>' : '<span class="button"><button type="submit" value="' . BUTTON_UPDATE . '">' . BUTTON_UPDATE . '</button></span>'). '&nbsp;&nbsp;<a class="button" href="' . os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $_GET['abID']) . '"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_BANNERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRODUCT_ID; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATISTICS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $affiliate_banners_query_raw = "select * from " . TABLE_AFFILIATE_BANNERS . " order by affiliate_banners_title, affiliate_banners_group";
    $affiliate_banners_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $affiliate_banners_query_raw, $affiliate_banners_query_numrows);
    $affiliate_banners_query = os_db_query($affiliate_banners_query_raw);
    while ($affiliate_banners = os_db_fetch_array($affiliate_banners_query)) {
      $info_query = os_db_query("select sum(affiliate_banners_shown) as affiliate_banners_shown, sum(affiliate_banners_clicks) as affiliate_banners_clicks from " . TABLE_AFFILIATE_BANNERS_HISTORY . " where affiliate_banners_id = '" . $affiliate_banners['affiliate_banners_id'] . "'");
      $info = os_db_fetch_array($info_query);

      if (((!$_GET['abID']) || ($_GET['abID'] == $affiliate_banners['affiliate_banners_id'])) && (!$abInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
        $abInfo_array = array_merge($affiliate_banners, $info);
        $abInfo = new objectInfo($abInfo_array);
      }

      $affiliate_banners_shown = ($info['affiliate_banners_shown'] != '') ? $info['affiliate_banners_shown'] : '0';
      $affiliate_banners_clicked = ($info['affiliate_banners_clicks'] != '') ? $info['affiliate_banners_clicks'] : '0';

      if ( (is_object($abInfo)) && ($affiliate_banners['affiliate_banners_id'] == $abInfo->affiliate_banners_id) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_AFFILIATE_BANNERS,'abID=' . $abInfo->affiliate_banners_id . '&action=new')  . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . os_href_link(FILENAME_AFFILIATE_BANNERS, 'abID=' . $affiliate_banners['affiliate_banners_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="javascript:popupImageWindow(\'' . FILENAME_AFFILIATE_POPUP_IMAGE . '?banner=' . $affiliate_banners['affiliate_banners_id'] . '\')">' . os_image(http_path('icons_admin')  . 'icon_popup.gif', ICON_PREVIEW) . '</a>&nbsp;' . $affiliate_banners['affiliate_banners_title']; ?></td>
                <td class="dataTableContent" align="right"><?php if ($affiliate_banners['affiliate_products_id']>0) echo $affiliate_banners['affiliate_products_id']; else echo '&nbsp;'; ?></td>
                <td class="dataTableContent" align="right"><?php echo $affiliate_banners_shown . ' / ' . $affiliate_banners_clicked; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($abInfo)) && ($affiliate_banners['affiliate_banners_id'] == $abInfo->affiliate_banners_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $affiliate_banners['affiliate_banners_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $affiliate_banners_split->display_count($affiliate_banners_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNERS); ?></td>
                    <td class="smallText" align="right"><?php echo $affiliate_banners_split->display_links($affiliate_banners_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a class="button" href="' . os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'delete':
      $heading[] = array('text' => '<b>' . $abInfo->affiliate_banners_title . '</b>');

      $contents = array('form' => os_draw_form('affiliate_banners', FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $abInfo->affiliate_banners_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $abInfo->affiliate_banners_title . '</b>');
      if ($abInfo->affiliate_banners_image) $contents[] = array('text' => '<br>' . os_draw_checkbox_field('delete_image', 'on', true) . ' ' . TEXT_INFO_DELETE_IMAGE);
      $contents[] = array('align' => 'center', 'text' => '<br><span class="button"><button type="submit" value="' . BUTTON_DELETE . '">' . BUTTON_DELETE . '</button></span>&nbsp;<a class="button" href="' . os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $_GET['abID']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    default:
      if (is_object($abInfo)) {
        $sql = "select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $abInfo->affiliate_products_id . "' and language_id = '" . $_SESSION['languages_id'] . "'";
        $product_description_query = os_db_query($sql);
        $product_description = os_db_fetch_array($product_description_query);
        $heading[] = array('text' => '<b>' . $abInfo->affiliate_banners_title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $abInfo->affiliate_banners_id . '&action=new') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" href="' . os_href_link(FILENAME_AFFILIATE_BANNER_MANAGER, 'page=' . $_GET['page'] . '&abID=' . $abInfo->affiliate_banners_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => $product_description['products_name']);
        $contents[] = array('text' => '<br>' . TEXT_BANNERS_DATE_ADDED . ' ' . os_date_short($abInfo->affiliate_date_added));
        $contents[] = array('text' => '' . sprintf(TEXT_BANNERS_STATUS_CHANGE, os_date_short($abInfo->affiliate_date_status_change)));
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
<?php
  }
?>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>