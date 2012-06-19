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
  include(_CLASS.'product.php');
  
  $product = new product();
  
function os_get_manufacturers_meta_title($manufacturer_id, $language_id) 
{
	    $manufacturer_query = os_db_query("select manufacturers_meta_title from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	    $manufacturer = os_db_fetch_array($manufacturer_query);
	    return $manufacturer['manufacturers_meta_title'];
}
	  
	  function os_get_manufacturers_meta_keywords($manufacturer_id, $language_id) {
	    $manufacturer_query = os_db_query("select manufacturers_meta_keywords from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	    $manufacturer = os_db_fetch_array($manufacturer_query);
	    return $manufacturer['manufacturers_meta_keywords'];
	  }

	  function os_get_manufacturers_meta_description($manufacturer_id, $language_id) {
	    $manufacturer_query = os_db_query("select manufacturers_meta_description from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	    $manufacturer = os_db_fetch_array($manufacturer_query);
	    return $manufacturer['manufacturers_meta_description'];
	  }
	  function os_get_manufacturers_description($manufacturer_id, $language_id) {
	    $manufacturer_query = os_db_query("select manufacturers_description from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	    $manufacturer = os_db_fetch_array($manufacturer_query);
	    return $manufacturer['manufacturers_description'];
	  }
	  
// EOF manufacturers meta tags
  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $manufacturers_id = os_db_prepare_input($_GET['mID']);
      $manufacturers_name = os_db_prepare_input($_POST['manufacturers_name']);

      $sql_data_array = array('manufacturers_name' => $manufacturers_name);

      if ($_GET['action'] == 'insert') {
        $insert_sql_data = array('date_added' => 'now()');
        $sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
        os_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
        $manufacturers_id = os_db_insert_id();
      } elseif ($_GET['action'] == 'save') {
        $update_sql_data = array('last_modified' => 'now()');
        $sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
        os_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . os_db_input($manufacturers_id) . "'");
      }

	$dir_manufacturers=dir_path('images')."/manufacturers";
    if ($manufacturers_image = &os_try_upload('manufacturers_image', $dir_manufacturers)) {
        os_db_query("update " . TABLE_MANUFACTURERS . " set
                                 manufacturers_image ='manufacturers/".$manufacturers_image->filename . "'
                                 where manufacturers_id = '" . os_db_input($manufacturers_id) . "'");
    }

      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $manufacturers_url_array = $_POST['manufacturers_url'];

// BOF manufacturers descriptions + meta tags
					$manufacturers_meta_title_array = $_POST['manufacturers_meta_title'];
					$manufacturers_meta_keywords_array = $_POST['manufacturers_meta_keywords'];
					$manufacturers_meta_description_array = $_POST['manufacturers_meta_description'];
					$manufacturers_description_array = $_POST['manufacturers_description'];					

// EOF manufacturers descriptions + meta tags
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('manufacturers_url' => os_db_prepare_input($manufacturers_url_array[$language_id]));

// BOF manufacturers descriptions + meta tags

					$sql_data_array = array_merge($sql_data_array, array('manufacturers_meta_title' => os_db_prepare_input($manufacturers_meta_title_array[$language_id]),'manufacturers_meta_keywords' => os_db_prepare_input($manufacturers_meta_keywords_array[$language_id]),'manufacturers_meta_description' => os_db_prepare_input($manufacturers_meta_description_array[$language_id]),'manufacturers_description' => os_db_prepare_input($manufacturers_description_array[$language_id]),));

// EOF manufacturers descriptions + meta tags
        if ($_GET['action'] == 'insert') {
          $insert_sql_data = array('manufacturers_id' => $manufacturers_id,
                                   'languages_id' => $language_id);
          $sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
          os_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          os_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . os_db_input($manufacturers_id) . "' and languages_id = '" . $language_id . "'");
        }
      }

      if (USE_CACHE == 'true') {
        os_reset_cache_block('manufacturers');
      }

      os_redirect(os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers_id));
      break;

    case 'deleteconfirm':
      $manufacturers_id = os_db_prepare_input($_GET['mID']);

      if ($_POST['delete_image'] == 'on') {
        $manufacturer_query = os_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . os_db_input($manufacturers_id) . "'");
        $manufacturer = os_db_fetch_array($manufacturer_query);
        $image_location = get_path('images').'manufacturers/' . $manufacturer['manufacturers_image'];
        if (is_file($image_location)) @unlink($image_location);
      }

      os_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . os_db_input($manufacturers_id) . "'");
      os_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . os_db_input($manufacturers_id) . "'");

      if ($_POST['delete_products'] == 'on') 
	  {
	     //удаление товаров по производителю
        $products_query = os_db_query("select products_id from " . TABLE_PRODUCTS . " where manufacturers_id = '" . os_db_input($manufacturers_id) . "'");
		
		$_remove_products_array = array();
		
        while ($products = os_db_fetch_array($products_query)) 
		{
		  $_remove_products_array[] = $products['products_id'];
        }
		
		$product->remove ($_remove_products_array);

      } 
	  else 
	  {
          os_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . os_db_input($manufacturers_id) . "'");
      }

      if (USE_CACHE == 'true') {
        os_reset_cache_block('manufacturers');
      }

      os_redirect(os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']));
      break;
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
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MANUFACTURERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $manufacturers_query_raw = "select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from " . TABLE_MANUFACTURERS . " order by manufacturers_name";
  $manufacturers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $manufacturers_query_raw, $manufacturers_query_numrows);
  $manufacturers_query = os_db_query($manufacturers_query_raw);
  while ($manufacturers = os_db_fetch_array($manufacturers_query)) {
    if (((!$_GET['mID']) || (@$_GET['mID'] == $manufacturers['manufacturers_id'])) && (!$mInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $manufacturer_products_query = os_db_query("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . $manufacturers['manufacturers_id'] . "'");
      $manufacturer_products = os_db_fetch_array($manufacturer_products_query);

      $mInfo_array = os_array_merge($manufacturers, $manufacturer_products);
      $mInfo = new objectInfo($mInfo_array);
    }
  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($mInfo)) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id) ) {
      echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '<tr  onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $manufacturers['manufacturers_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($mInfo)) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $manufacturers_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?></td>
                    <td class="smallText" align="right"><?php echo $manufacturers_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if ($_GET['action'] != 'new') {
?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo os_button_link(BUTTON_INSERT, os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=new')); ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_MANUFACTURER . '</b>');

      $contents = array('form' => os_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_NAME . '<br />' . os_draw_input_field('manufacturers_name'));


      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) 
	  {
	    if ($languages[$i]['status'] == 1) 
		{
           $manufacturer_inputs_string .= '<br />' . $languages[$i]['name'] . ':&nbsp;<br />' . os_draw_input_field('manufacturers_meta_title[' . $languages[$i]['id'] . ']');
		}
      }
      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_META_TITLE . $manufacturer_inputs_string);

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br />' . $languages[$i]['name'] . ':&nbsp;<br />' . os_draw_input_field('manufacturers_meta_keywords[' . $languages[$i]['id'] . ']');
      }
      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_META_KEYWORDS . $manufacturer_inputs_string);

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br />' . $languages[$i]['name'] . ':&nbsp;<br />' . os_draw_input_field('manufacturers_meta_description[' . $languages[$i]['id'] . ']');
      }
      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_META_DESCRIPTION . $manufacturer_inputs_string);

// EOF manufacturers meta tags

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_desc_string .= '<br />' . $languages[$i]['name'] . ':&nbsp;<br />' . os_draw_textarea_field('manufacturers_description[' . $languages[$i]['id'] . ']', 'soft', '30', '5');
      }
      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_DESCRIPTION . $manufacturer_desc_string);

      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_IMAGE . '<br />' . os_draw_file_field('manufacturers_image'));

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $manufacturer_inputs_string .= '<br />' . $languages[$i]['name'] . '&nbsp;<br/>' . os_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br />' . os_button(BUTTON_SAVE) . '&nbsp;' . os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'])));
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_MANUFACTURER . '</b>');

      $contents = array('form' => os_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_NAME . '<br />' . os_draw_input_field('manufacturers_name', $mInfo->manufacturers_name));

// BOF manufacturers meta tags

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br>' . $languages[$i]['name'] . ':&nbsp;<br/>' . os_draw_input_field('manufacturers_meta_title[' . $languages[$i]['id'] . ']', os_get_manufacturers_meta_title($mInfo->manufacturers_id, $languages[$i]['id']));
      }
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_TITLE . $manufacturer_inputs_string);

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br>' . $languages[$i]['name'] . ':&nbsp;<br/>' . os_draw_input_field('manufacturers_meta_keywords[' . $languages[$i]['id'] . ']', os_get_manufacturers_meta_keywords($mInfo->manufacturers_id, $languages[$i]['id']));
      }
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_KEYWORDS . $manufacturer_inputs_string);

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br>' . $languages[$i]['name'] . ':&nbsp;<br/>' . os_draw_input_field('manufacturers_meta_description[' . $languages[$i]['id'] . ']', os_get_manufacturers_meta_description($mInfo->manufacturers_id, $languages[$i]['id']));
      }
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_META_DESCRIPTION . $manufacturer_inputs_string);

// EOF manufacturers meta tags

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_desc_string .= '<br>' . $languages[$i]['name'] . ':&nbsp;<br/>' . os_draw_textarea_field('manufacturers_description[' . $languages[$i]['id'] . ']', 'soft', '30', '5', os_get_manufacturers_description($mInfo->manufacturers_id, $languages[$i]['id']));
      }
      $contents[] = array('text' => '<br>' . TEXT_MANUFACTURERS_DESCRIPTION . $manufacturer_desc_string);

      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_IMAGE . '<br />' . os_draw_file_field('manufacturers_image') . '<br />' . $mInfo->manufacturers_image);

      $manufacturer_inputs_string = '';
      $languages = os_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $manufacturer_inputs_string .= '<br />' . $languages[$i]['name'] . '&nbsp;<br/>' . os_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']', os_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br />' . TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br />' . os_button(BUTTON_SAVE) . '&nbsp;' . os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id)));
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_MANUFACTURER . '</b>');

      $contents = array('form' => os_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $mInfo->manufacturers_name . '</b>');
      $contents[] = array('text' => '<br />' . os_draw_checkbox_field('delete_image', '', true) . ' ' . TEXT_DELETE_IMAGE);

      if ($mInfo->products_count > 0) {
        $contents[] = array('text' => '<br />' . os_draw_checkbox_field('delete_products') . ' ' . TEXT_DELETE_PRODUCTS);
        $contents[] = array('text' => '<br />' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $mInfo->products_count));
      }

      $contents[] = array('align' => 'center', 'text' => '<br />' . os_button(BUTTON_DELETE) . '&nbsp;' . os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id)));
      break;

    default:
      if (is_object($mInfo)) {
        $heading[] = array('text' => '<b>' . $mInfo->manufacturers_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => os_button_link(BUTTON_EDIT, os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=edit')) . '&nbsp;' . os_button_link(BUTTON_DELETE, os_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . TEXT_DATE_ADDED . ' ' . os_date_short($mInfo->date_added));
        if (os_not_null($mInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . os_date_short($mInfo->last_modified));
        $contents[] = array('text' => '<br />' . os_info_image($mInfo->manufacturers_image, $mInfo->manufacturers_name));
        $contents[] = array('text' => '<br />' . TEXT_PRODUCTS . ' ' . $mInfo->products_count);
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