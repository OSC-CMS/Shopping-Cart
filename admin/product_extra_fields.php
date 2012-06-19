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
if (isset($_POST['remove'])) $action='remove';

if (os_not_null($action)) {
  switch ($action) {
    case 'setflag':
      $sql_data_array = array('products_extra_fields_status' => os_db_prepare_input($_GET['flag']));
	  os_db_perform(TABLE_PRODUCTS_EXTRA_FIELDS, $sql_data_array, 'update', 'products_extra_fields_id=' . $_GET['id']);
      os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS));	
	  break;
    case 'add':
      $sql_data_array = array('products_extra_fields_name' => os_db_prepare_input($_POST['field']['name']),
	                          'languages_id' => os_db_prepare_input ($_POST['field']['language']),
							  'products_extra_fields_order' => os_db_prepare_input($_POST['field']['order']));
			os_db_perform(TABLE_PRODUCTS_EXTRA_FIELDS, $sql_data_array, 'insert');

      os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS));
      break;
    case 'update':
      foreach ($_POST['field'] as $key=>$val) {
        $sql_data_array = array('products_extra_fields_name' => os_db_prepare_input($val['name']),
		                        'languages_id' =>  os_db_prepare_input($val['language']),
			   					'products_extra_fields_order' => os_db_prepare_input($val['order']));
			  os_db_perform(TABLE_PRODUCTS_EXTRA_FIELDS, $sql_data_array, 'update', 'products_extra_fields_id=' . $key);
      }
      os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS));

      break;
    case 'remove':
      if ($_POST['mark']) {
        foreach ($_POST['mark'] as $key=>$val) {
          os_db_query("DELETE FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " WHERE products_extra_fields_id=" . os_db_input($key));
          os_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " WHERE products_extra_fields_id=" . os_db_input($key));
        }
        os_redirect(os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS));
      }

      break;
  }
}

  $languages=os_get_languages();
  $values[0]=array ('id' =>'0', 'text' => TEXT_ALL_LANGUAGES);
  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
	$values[$i+1]=array ('id' =>$languages[$i]['id'], 'text' =>$languages[$i]['name']);
  }
		 
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
  <td width="100%" valign="top">
   <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
     <td width="100%">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
       <tr>
        <td class="main">
        
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
        
        
        </td>
       </tr>
      </table>
     </td>
    </tr>

    <tr>
     <td width="100%">
       <?php echo "<b>".SUBHEADING_TITLE."</b>"; ?>
      <br />
      <?php echo os_draw_form("add_field", FILENAME_PRODUCTS_EXTRA_FIELDS, 'action=add', 'post'); ?>
      <table border="0" width="400" cellspacing="2" cellpadding="2">
       <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIELDS; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDER; ?></td>
		<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LANGUAGE; ?></td>
       </tr>

       <tr>
        <td class="dataTableContent">
         <?php echo os_draw_input_field('field[name]', $field['name'], 'size=30', false, 'text', true);?>
        </td>
		<td class="dataTableContent" align="center">
         <?php echo os_draw_input_field('field[order]', $field['order'], 'size=5', false, 'text', true);?>
        </td>
		<td class="dataTableContent" align="center">
         <?php
		 echo os_draw_pull_down_menu('field[language]', $values, '0', '');?>
        </td>		
        <td  align="right">
	<?php echo '<span class="button"><button type="submit" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>'; ?>
        </td>
       </tr>
       </form>
      </table><br>
      <div style="width: 90%; border-top: 1px dashed #4378a1;" />
      <br>
      <?php
       echo os_draw_form('extra_fields', FILENAME_PRODUCTS_EXTRA_FIELDS,'action=update','post');
      ?>
      <?php echo $action_message; ?>
      <table border="0" width="100%" cellspacing="2" cellpadding="2">
       <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" width="20">&nbsp;</td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIELDS; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDER; ?></td>
		<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LANGUAGE; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
       </tr>
<?php
$products_extra_fields_query = os_db_query("SELECT * FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " ORDER BY products_extra_fields_order");
while ($extra_fields = os_db_fetch_array($products_extra_fields_query)) {
?>
       <tr>
        <td width="20">
         <?php echo os_draw_checkbox_field('mark['.$extra_fields['products_extra_fields_id'].']', 1) ?>
        </td>
        <td class="dataTableContent">
         <?php echo os_draw_input_field('field['.$extra_fields['products_extra_fields_id'].'][name]', $extra_fields['products_extra_fields_name'], 'size=30', false, 'text', true);?>
        </td>
		<td class="dataTableContent" align="center">
         <?php echo os_draw_input_field('field['.$extra_fields['products_extra_fields_id'].'][order]', $extra_fields['products_extra_fields_order'], 'size=5', false, 'text', true);?>
        </td>
		<td class="dataTableContent" align="center">
		 <?php echo os_draw_pull_down_menu('field['.$extra_fields['products_extra_fields_id'].'][language]', $values, $extra_fields['languages_id'], ''); ?>
        </td>	
				<td  class="dataTableContent" align="center">
         <?php
          if ($extra_fields['products_extra_fields_status'] == '1') {
            echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'action=setflag&flag=0&id=' . $extra_fields['products_extra_fields_id'], 'NONSSL') . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
          }
          else {
            echo '<a href="' . os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, 'action=setflag&flag=1&id=' . $extra_fields['products_extra_fields_id'], 'NONSSL') . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
          }
         ?>
        </td>
       </tr>
<?php } ?>
       <tr>
        <td colspan="4">
         <?php echo '<span class="button"><button type="submit" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>'; ?> 
         &nbsp;&nbsp;
	 <?php echo '<span class="button"><button type="submit" value="' . BUTTON_DELETE . '" name="remove" />' . BUTTON_DELETE . '</button></span>'; ?>
        </td>
       </tr>
       </form>
      </table>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<?php $main->bottom(); ?>