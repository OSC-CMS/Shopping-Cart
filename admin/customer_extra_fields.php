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

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (os_not_null($action)) {
    switch ($action) {
      case 'insert':
      case 'save':
        if (isset($_GET['fID'])) $fields_id = os_db_prepare_input($_GET['fID']);
        $fields_input_type = os_db_prepare_input($_POST['fields_input_type']);
        $fields_input_value = os_db_prepare_input($_POST['fields_input_value']);
        $fields_required_status =  os_db_prepare_input($_POST['fields_required_status']);
        $fields_size = os_db_prepare_input($_POST['fields_size']);
        $fields_required_email = os_db_prepare_input($_POST['fields_required_email']);
        $sql_data_array = array('fields_status' => 1,
                                'fields_input_type' => $fields_input_type,
                                'fields_input_value' => $fields_input_value,
                                'fields_required_status' => $fields_required_status,
                                'fields_size' => $fields_size,
				'fields_required_email' => $fields_required_email);

        if ($action == 'insert') {
          os_db_perform(TABLE_EXTRA_FIELDS, $sql_data_array);
          $fields_id = os_db_insert_id();
        } elseif ($action == 'save') {
          os_db_perform(TABLE_EXTRA_FIELDS, $sql_data_array, 'update', "fields_id = '" . (int)$fields_id . "'");
        }

        $languages = os_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		  if($languages[$i]['status']==1) {
          $fields_name_array = $_POST['fields_name'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = array('fields_name' => os_db_prepare_input($fields_name_array[$language_id]));

          if ($action == 'insert') {
            $insert_sql_data = array('fields_id' => $fields_id,
                                     'languages_id' => $language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            os_db_perform(TABLE_EXTRA_FIELDS_INFO, $sql_data_array);
          } elseif ($action == 'save') {
            os_db_perform(TABLE_EXTRA_FIELDS_INFO, $sql_data_array, 'update', "fields_id = '" . (int)$fields_id . "' and languages_id = '" . (int)$language_id . "'");
          }
		  }
        }
        os_redirect(os_href_link(FILENAME_EXTRA_FIELDS, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'fID=' . $fields_id));
        break;
      case 'deleteconfirm':
        $fields_id = os_db_prepare_input($_GET['fID']);

        os_db_query("delete from " . TABLE_EXTRA_FIELDS . " where fields_id = '" . (int)$fields_id . "'");
        os_db_query("delete from " . TABLE_EXTRA_FIELDS_INFO . " where fields_id = '" . (int)$fields_id . "'");
	os_db_query("delete from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where fields_id = '" . (int)$fields_id . "'");

        os_redirect(os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page']));
        break;
      case 'setflag':
        $fields_id = os_db_prepare_input($_GET['fID']);
        $flag = os_db_prepare_input($_GET['flag']);

        $sql_data_array = array('fields_status' => $flag);
        os_db_perform(TABLE_EXTRA_FIELDS, $sql_data_array, 'update', "fields_id = '" . (int)$fields_id . "'");

        os_redirect(os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page']));
        break;
    }
  }
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
        <td width="100%">

    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
  
  </td>
  </tr>
  <tr>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIELDS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $fields_query_raw = "select ce.fields_id, ce.fields_size, ce.fields_input_type, ce.fields_input_value, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_required_email from " . TABLE_EXTRA_FIELDS . " ce, " . TABLE_EXTRA_FIELDS_INFO . " cei where cei.fields_id=ce.fields_id and cei.languages_id =" . (int)$_SESSION['languages_id'];
  $fields_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $fields_query_raw, $manufacturers_query_numrows);
  $fields_query = os_db_query($fields_query_raw);
  while ($fields = os_db_fetch_array($fields_query)) {
  if ((!isset($_GET['fID']) || (isset($_GET['fID']) && ($_GET['fID'] == $fields['fields_id']))) && !isset($fInfo) && (substr($action, 0, 3) != 'new')) {
      $fInfo = new objectInfo($fields);
  }
   $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if (isset($fInfo) && is_object($fInfo) && ($fields['fields_id'] == $fInfo->fields_id)) 
	{
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fields['fields_id'] . '&action=edit') . '\'">' . "\n";
    } 
	else 
	{
      echo '              <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fields['fields_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $fields['fields_name']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($fields['fields_status'] == '1') {
        echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'action=setflag&flag=0&fID=' . $fields['fields_id'] . '&page=' . $_GET['page']) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'action=setflag&flag=1&fID=' . $fields['fields_id'] . '&page=' . $_GET['page']) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?>              </td>
                <td class="dataTableContent" align="right"><?php if (isset($fInfo) && is_object($fInfo) && ($fields['fields_id'] == $fInfo->fields_id)) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fields['fields_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $fields_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FIELDS); ?></td>
                    <td class="smallText" align="right"><?php echo $fields_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="3" class="smallText"><?php echo '<a class="button" href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->fields_id . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_FIELD . '</b>');

      $contents = array('form' => os_draw_form('fields', FILENAME_EXTRA_FIELDS, 'action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $field_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
	       if($languages[$i]['status']==1) {
        $field_inputs_string .= '<br>' . $languages[$i]['name'] . ':&nbsp;' . os_draw_input_field('fields_name[' . $languages[$i]['id'] . ']');           }
      }
      $contents[] = array('text' => '<br>' . TEXT_FIELD_NAME . $field_inputs_string);      
	  	$contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . '<br>' . os_draw_radio_field('fields_input_type', 0, ($fInfo->fields_input_type==0) ? true : false) . TEXT_INPUT_FIELD . '<br>' .
																																							os_draw_radio_field('fields_input_type', 1, ($fInfo->fields_input_type==1) ? true : false) . TEXT_TEXTAREA_FIELD . '<br>' .
                                                                              os_draw_radio_field('fields_input_type', 2, ($fInfo->fields_input_type==2) ? true : false) . TEXT_RADIO_FIELD . '<br>' .
                                                                              os_draw_radio_field('fields_input_type', 3, ($fInfo->fields_input_type==3) ? true : false) . TEXT_CHECK_FIELD . '<br>' .
                                                                              os_draw_radio_field('fields_input_type', 4, ($fInfo->fields_input_type==4) ? true : false) . TEXT_DOWN_FIELD);
      
	  $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_VALUE . '<br>' . os_draw_textarea_field('fields_input_value', 'nowrap', /*$width*/ 10, /*$height*/ 8, $fInfo->fields_input_value /*, $parameters = '', $reinsert_value = true*/));
			$contents[] = array('text' => '<br>' . TEXT_FIELD_REQUIRED_STATUS . '<br>' . os_draw_radio_field('fields_required_status', 0, ($fInfo->fields_required_status==0) ? true : false) . NO.'<br>' . os_draw_radio_field('fields_required_status', 1, ($fInfo->fields_required_status==1) ? true : false) . YES);
      $contents[] = array('text' =>  TEXT_FIELD_SIZE . '<br>' . os_draw_input_field('fields_size',$fInfo->fields_size));
	  $contents[] = array('text' => '<br>' . TEXT_FIELD_STATUS_EMAIL . '<br>' . os_draw_radio_field('fields_required_email', 0, ($fInfo->fields_required_email==0) ? true : false) . NO.'<br>' . os_draw_radio_field('fields_required_email', 1, ($fInfo->fields_required_email==1) ? true : false) . YES);
      $contents[] = array('align' => 'center', 'text' => '<br><span class="button"><button type="submit" value="' . BUTTON_SAVE .'">' . BUTTON_SAVE .'</button></span> <a class="button" href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $_GET['fID']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_FIELD . '</b>');

      $contents = array('form' => os_draw_form('fields', FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->fields_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_EDIT_INTRO);
      $field_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) 
	  {
	       if($languages[$i]['status']==1) {
        $field_inputs_string .= '<br>' . $languages[$i]['name'] . ':&nbsp;' . os_draw_input_field('fields_name[' . $languages[$i]['id'] . ']',os_get_customers_extra_fields_name($fInfo->fields_id, $languages[$i]['id']));
		   }
      }
      $contents[] = array('text' => '<br>' . TEXT_FIELD_NAME . $field_inputs_string);
      $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . '<br>' . os_draw_radio_field('fields_input_type', 0, ($fInfo->fields_input_type==0) ? true : false) . TEXT_INPUT_FIELD . '<br>' .
																																							os_draw_radio_field('fields_input_type', 1, ($fInfo->fields_input_type==1) ? true : false) . TEXT_TEXTAREA_FIELD . '<br>' .
                                                                              os_draw_radio_field('fields_input_type', 2, ($fInfo->fields_input_type==2) ? true : false) . TEXT_RADIO_FIELD . '<br>' .
                                                                              os_draw_radio_field('fields_input_type', 3, ($fInfo->fields_input_type==3) ? true : false) . TEXT_CHECK_FIELD . '<br>' .
                                                                              os_draw_radio_field('fields_input_type', 4, ($fInfo->fields_input_type==4) ? true : false) . TEXT_DOWN_FIELD);
     
	 $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_VALUE . '<br>' . os_draw_textarea_field('fields_input_value', 'nowrap', /*$width*/ 30, /*$height*/ 8, $fInfo->fields_input_value /*, $parameters = '', $reinsert_value = true*/));
	$contents[] = array('text' => '<br>' . TEXT_FIELD_REQUIRED_STATUS . '<br>' . os_draw_radio_field('fields_required_status', 0, ($fInfo->fields_required_status==0) ? true : false) . NO.'<br>' . os_draw_radio_field('fields_required_status', 1, ($fInfo->fields_required_status==1) ? true : false) . YES);
      $contents[] = array('text' =>  TEXT_FIELD_SIZE . '<br>' . os_draw_input_field('fields_size', $fInfo->fields_size));
	  $contents[] = array('text' => '<br>' . TEXT_FIELD_STATUS_EMAIL . '<br>' . os_draw_radio_field('fields_required_email',0,($fInfo->fields_required_email==0)?true:false) . NO.'<br>' . os_draw_radio_field('fields_required_email',1,($fInfo->fields_required_email==1)?true:false) . YES);
      $contents[] = array('align' => 'center', 'text' => '<br><span class="button"><button type="submit" value="' . BUTTON_SAVE .'">' . BUTTON_SAVE .'</button></span> <a class="button" href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->fields_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_FIELD . '</b>');
      $contents = array('form' => os_draw_form('manufacturers', FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->fields_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $fInfo->fields_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br><span class="button"><button type="submit" value="' . BUTTON_DELETE .'">' . BUTTON_DELETE .'</button></span> <a class="button" href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->fields_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;
    default:
      if (isset($fInfo) && is_object($fInfo)) {
        $heading[] = array('text' => '<b>' . $fInfo->fields_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a class="button" href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->fields_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" href="' . os_href_link(FILENAME_EXTRA_FIELDS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->fields_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br>' . TEXT_FIELD_NAME .  $fInfo->fields_name);
				switch($fInfo->fields_input_type)
				{
				  case  0: $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . TEXT_INPUT_FIELD ); break;
				  case  1: $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . TEXT_TEXTAREA_FIELD ); break;
				  case  2: $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . TEXT_RADIO_FIELD ); break;
				  case  3: $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . TEXT_CHECK_FIELD ); break;
				  case  4: $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . TEXT_DOWN_FIELD ); break;
				  default: $contents[] = array('text' => '<br>' . TEXT_FIELD_INPUT_TYPE . TEXT_INPUT_FIELD );
				}
        $contents[] = array('text' => TEXT_FIELD_REQUIRED_STATUS . (($fInfo->fields_required_status==1) ? 'true' : 'false'));
        $contents[] = array('text' => TEXT_FIELD_SIZE .  $fInfo->fields_size);
		$contents[] = array('text' => TEXT_FIELD_REQUIRED_EMAIL . (($fInfo->fields_required_email==1) ? 'true' : 'false'));
        $contents[] = array('text' => '<br>');
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