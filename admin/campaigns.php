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

require ('includes/top.php');

  require(get_path('class_admin') . 'currencies.php');
  $currencies = new currencies();

switch ($_GET['action']) {
	case 'insert' :
	case 'save' :
		$campaigns_id = os_db_prepare_input($_GET['cID']);
		$campaigns_name = os_db_prepare_input($_POST['campaigns_name']);
		$campaigns_refID = os_db_prepare_input($_POST['campaigns_refID']);
		$sql_data_array = array ('campaigns_name' => $campaigns_name, 'campaigns_refID' => $campaigns_refID);

		if ($_GET['action'] == 'insert') {
			$insert_sql_data = array ('date_added' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_CAMPAIGNS, $sql_data_array);
			$campaigns_id = os_db_insert_id();
		}
		elseif ($_GET['action'] == 'save') {
			$update_sql_data = array ('last_modified' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
			os_db_perform(TABLE_CAMPAIGNS, $sql_data_array, 'update', "campaigns_id = '".os_db_input($campaigns_id)."'");
		}

		os_redirect(os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns_id));
		break;

	case 'deleteconfirm' :

		$campaigns_id = os_db_prepare_input($_GET['cID']);

		os_db_query("delete from ".TABLE_CAMPAIGNS." where campaigns_id = '".os_db_input($campaigns_id)."'");
		os_db_query("delete from ".TABLE_CAMPAIGNS_IP." where campaign = '".os_db_input($campaigns_id)."'");

		if ($_POST['delete_refferers'] == 'on') {

			os_db_query("update ".TABLE_ORDERS." set refferers_id = '' where refferers_id = '".os_db_input($campaigns_id)."'");
			os_db_query("update ".TABLE_CUSTOMERS." set refferers_id = '' where refferers_id = '".os_db_input($campaigns_id)."'");
		}

		os_redirect(os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page']));
		break;
}
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    <?php os_header('campaigns.png',BOX_CONFIGURATION." / ".HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CAMPAIGNS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php

$campaigns_query_raw = "select * from ".TABLE_CAMPAIGNS." order by campaigns_name";
$campaigns_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $campaigns_query_raw, $campaigns_query_numrows);
$campaigns_query = os_db_query($campaigns_query_raw);
while ($campaigns = os_db_fetch_array($campaigns_query)) {
	if (((!$_GET['cID']) || (@ $_GET['cID'] == $campaigns['campaigns_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
		$cInfo = new objectInfo($campaigns);
	}
	$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
	if ((is_object($cInfo)) && ($campaigns['campaigns_id'] == $cInfo->campaigns_id)) {
		echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\''.os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns['campaigns_id'].'&action=edit').'\'">'."\n";
	} else {
		echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\''.os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns['campaigns_id']).'\'">'."\n";
	}
?>
                <td class="dataTableContent"><?php echo $campaigns['campaigns_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($campaigns['campaigns_id'] == $cInfo->campaigns_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_CAMPAIGNS, 'page=' . $_GET['page'] . '&cID=' . $campaigns['campaigns_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php

}
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $campaigns_split->display_count($campaigns_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CAMPAIGNS); ?></td>
                    <td class="smallText" align="right"><?php echo $campaigns_split->display_links($campaigns_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php

if ($_GET['action'] != 'new') {
?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo os_button_link(BUTTON_INSERT, os_href_link(FILENAME_CAMPAIGNS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->campaigns_id . '&action=new')); ?></td>
              </tr>
<?php

}
?>
            </table></td>
<?php

$heading = array ();
$contents = array ();
switch ($_GET['action']) {
	case 'new' :
		$heading[] = array ('text' => '<b>'.TEXT_HEADING_NEW_CAMPAIGN.'</b>');

		$contents = array ('form' => os_draw_form('campaigns', FILENAME_CAMPAIGNS, 'action=insert', 'post', 'enctype="multipart/form-data"'));
		$contents[] = array ('text' => TEXT_NEW_INTRO);
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_NAME.'<br />'.os_draw_input_field('campaigns_name'));
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_REFID.'<br />'.os_draw_input_field('campaigns_refID'));
		$contents[] = array ('align' => 'center', 'text' => '<br />'.os_button(BUTTON_SAVE).'&nbsp;'.os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$_GET['cID'])));
		break;

	case 'edit' :
		$heading[] = array ('text' => '<b>'.TEXT_HEADING_EDIT_CAMPAIGN.'</b>');

		$contents = array ('form' => os_draw_form('campaigns', FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=save', 'post', 'enctype="multipart/form-data"'));
		$contents[] = array ('text' => TEXT_EDIT_INTRO);
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_NAME.'<br />'.os_draw_input_field('campaigns_name', $cInfo->campaigns_name));
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_REFID.'<br />'.os_draw_input_field('campaigns_refID', $cInfo->campaigns_refID));
		$contents[] = array ('align' => 'center', 'text' => '<br />'.os_button(BUTTON_SAVE).'&nbsp;'.os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id)));
		break;

	case 'delete' :
		$heading[] = array ('text' => '<b>'.TEXT_HEADING_DELETE_CAMPAIGN.'</b>');

		$contents = array ('form' => os_draw_form('campaigns', FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=deleteconfirm'));
		$contents[] = array ('text' => TEXT_DELETE_INTRO);
		$contents[] = array ('text' => '<br /><b>'.$cInfo->campaigns_name.'</b>');

		if ($cInfo->refferers_count > 0) {
			$contents[] = array ('text' => '<br />'.os_draw_checkbox_field('delete_refferers').' '.TEXT_DELETE_REFFERERS);
			$contents[] = array ('text' => '<br />'.sprintf(TEXT_DELETE_WARNING_REFFERERS, $cInfo->refferers_count));
		}

		$contents[] = array ('align' => 'center', 'text' => '<br />'.os_button(BUTTON_DELETE).'&nbsp;'.os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id)));
		break;

	default :
		if (is_object($cInfo)) {
			$heading[] = array ('text' => '<b>'.$cInfo->campaigns_name.'</b>');

			$contents[] = array ('align' => 'center', 'text' => os_button_link(BUTTON_EDIT, os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=edit')).'&nbsp;'.os_button_link(BUTTON_DELETE, os_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=delete')));
			$contents[] = array ('text' => '<br />'.TEXT_DATE_ADDED.' '.os_date_short($cInfo->date_added));
			if (os_not_null($cInfo->last_modified))
				$contents[] = array ('text' => TEXT_LAST_MODIFIED.' '.os_date_short($cInfo->last_modified));
			$contents[] = array ('text' => TEXT_REFERER.'?refID='.$cInfo->campaigns_refID);
		}
		break;
}

if ((os_not_null($heading)) && (os_not_null($contents))) {
	echo '            <td class="right_box" valign="top">'."\n";

	$box = new box;
	echo $box->infoBox($heading, $contents);

	echo '            </td>'."\n";
}
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>