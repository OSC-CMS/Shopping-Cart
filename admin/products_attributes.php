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
  
  	$order = '';
if (isset($_GET['sort']))
{
    //id
    if ($_GET['sort-type'] == 'id' && strtolower($_GET['sort']) == 'desc')
	{
       $order = ' ORDER BY pov.products_options_values_id DESC';
	}
	elseif ($_GET['sort-type'] == 'id' && strtolower($_GET['sort']) == 'asc')
	{
	   $order = ' ORDER BY pov.products_options_values_id ASC';
	}    
	
		
	//value
	if ($_GET['sort-type'] == 'value' && strtolower($_GET['sort']) == 'desc')
	{
       $order = ' ORDER BY pov.products_options_values_name DESC';
	}
	elseif ($_GET['sort-type'] == 'value' && strtolower($_GET['sort']) == 'asc')
	{
	   $order = ' ORDER BY pov.products_options_values_name ASC';
	}
	
	//$order
}

  $languages = os_get_languages();
  $max_byte_size = MAX_BYTE_SIZE;
  $max_thumb_width = MAX_THUMB_WIDTH;
  $max_thumb_height = MAX_THUMB_HEIGHT;
  $max_admin_width = MAX_ADMIN_WIDTH;
  $max_admin_height = MAX_ADMIN_HEIGHT;
  
  if($_GET['status'] == '0') $messageStack->add(TEXT_ATTRIBUTE_FILE_1);
  if($_GET['status'] == '1') $messageStack->add(TEXT_ATTRIBUTE_FILE_2);
  if($_GET['status'] == '2') $messageStack->add(TEXT_ATTRIBUTE_FILE_3);
  if($_GET['status'] == '3') $messageStack->add(TEXT_ATTRIBUTE_FILE_4);
  if($_GET['status'] == '4') $messageStack->add(TEXT_ATTRIBUTE_FILE_5);
  if($_GET['status'] == '5') $messageStack->add(TEXT_ATTRIBUTE_FILE_6);
  if($_GET['status'] == '6') $messageStack->add(TEXT_ATTRIBUTE_FILE_7);
  if($_GET['status'] == '7') $messageStack->add(TEXT_ATTRIBUTE_FILE_8);
  if($_GET['status'] == 'image_processing') {
	  $files_to_rebuild = os_db_query('SELECT products_options_values_image FROM '.TABLE_PRODUCTS_OPTIONS_VALUES.' WHERE products_options_values_image != ""');
	  while($file_to_rebuild = os_db_fetch_array($files_to_rebuild)) {
		  $filename = $file_to_rebuild['products_options_values_image'];
		  $filetyp = explode('.',$filename);
		  $filetyp = ($filetyp[((count($filetyp))-1)]);
		  if(!os_attribute_image_processing($filename,$filetyp,_IMG.'attribute_images/',$max_thumb_width,$max_thumb_height,$max_admin_width,$max_admin_height)) $messageStack->add('failed while image_processing filename: '.$filename);
	  }
  }
  
  if ($_GET['action']) {
    $page_info = 'option_page=' . $_GET['option_page'] . '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $_GET['attribute_page'];
    switch($_GET['action']) {
      case 'add_product_options':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $option_name = $_POST['option_name'];
          os_db_query("insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, products_options_name, language_id) values ('" . $_POST['products_options_id'] . "', '" . $option_name[$languages[$i]['id']] . "', '" . $languages[$i]['id'] . "')");
        }
        os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info)); 
        break;

      case 'add_product_option_values':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $value_name = $_POST['value_name'];
          $value_description = $_POST['value_description'];
          $value_text = $_POST['value_text'];
          $value_link = $_POST['value_link'];
          
          $status = os_upload_attribute_image($_FILES['value_image'],$languages[$i]['id'],$max_byte_size,_IMG.'attribute_images/',$max_thumb_width,$max_thumb_height,$max_admin_width,$max_admin_height);
          
          if($status[0] == 'success') {
	          os_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name, products_options_values_description, products_options_values_text, products_options_values_image, products_options_values_link) values ('" . $_POST['value_id'] . "', '" . $languages[$i]['id'] . "', '" . $value_name[$languages[$i]['id']] . "', '" . $value_description[$languages[$i]['id']] . "', '" . $value_text[$languages[$i]['id']] . "', '" . $status[1] . "', '" . $value_link[$languages[$i]['id']] . "')");
          }
        }
        if($status[0] == 'success') {
	        os_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " (products_options_id, products_options_values_id) values ('" . $_POST['option_id'] . "', '" . $_POST['value_id'] . "')");
        }
        os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info.'&status='.$status[1]));
        break;

		case 'add_product_attributes' :
			os_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES . " values ('', '" . $_POST['products_id'] . "', '" . $_POST['options_id'] . "', '" . $_POST['values_id'] . "', '" . $_POST['value_price'] . "', '" . $_POST['price_prefix'] . "')");
			$products_attributes_id = os_db_insert_id();
			if ((DOWNLOAD_ENABLED == 'true') && $_POST['products_attributes_filename'] != '') {
				os_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " values (" . $products_attributes_id . ", '" . $_POST['products_attributes_filename'] . "', '" . $_POST['products_attributes_maxdays'] . "', '" . $_POST['products_attributes_maxcount'] . "')");
			}
			os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
			break;
		case 'update_option_name' :
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
				$option_name = $_POST['option_name'];
				os_db_query("update " . TABLE_PRODUCTS_OPTIONS . " set products_options_name = '" . $option_name[$languages[$i]['id']] . "' where products_options_id = '" . $_POST['option_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
			}
			os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
			break;

		case 'update_value' :
       $value_name = $_POST['value_name'];
       $value_description = $_POST['value_description'];
       for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
	       $value_text = $_POST['value_text'];
           $value_link = $_POST['value_link'];
	       $new_image = $_POST['orig_image_'.$languages[$i]['code']];
	       $status = array('success','');
	     
		     if((isset($_POST['delete_flag'])) and (in_array($languages[$i]['code'],$_POST['delete_flag']))) {
			     unlink(_IMG.'attribute_images/original/'.$new_image);
			     unlink(_IMG.'attribute_images/thumbs/'.$new_image);
			     unlink(_IMG.'attribute_images/mini/'.$new_image);
			     $new_image = '';
		     }
		     
		     if((isset($_POST['edit_flag'])) and (in_array($languages[$i]['code'],$_POST['edit_flag']))) {
			     $status = os_upload_attribute_image($_FILES['value_image'],$languages[$i]['id'],$max_byte_size,_IMG.'attribute_images/',$max_thumb_width,$max_thumb_height,$max_admin_width,$max_admin_height);
			     if($status[0] == 'success') {
				     unlink(_IMG.'attribute_images/original/'.$new_image);
				     unlink(_IMG.'attribute_images/thumbs/'.$new_image);
				     unlink(_IMG.'attribute_images/mini/'.$new_image);
				     $new_image = $status[1];
			     }
	     	}
         os_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES . " set products_options_values_name = '" . $value_name[$languages[$i]['id']] . "', products_options_values_description = '" . $value_description[$languages[$i]['id']] . "', products_options_values_text = '" . $value_text[$languages[$i]['id']] . "', products_options_values_image = '" . $new_image . "', products_options_values_link = '" . $value_link[$languages[$i]['id']] . "' where products_options_values_id = '" . $_POST['value_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
       }
       os_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " set products_options_id = '" . $_POST['option_id'] . "' where products_options_values_id = '" . $_POST['value_id'] . "'");
       os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info.'&status='.$status[1]));
       break;

      case 'update_product_attribute':
        os_db_query("update " . TABLE_PRODUCTS_ATTRIBUTES . " set products_id = '" . $_POST['products_id'] . "', options_id = '" . $_POST['options_id'] . "', options_values_id = '" . $_POST['values_id'] . "', options_values_price = '" . $_POST['value_price'] . "', price_prefix = '" . $_POST['price_prefix'] . "' where products_attributes_id = '" . $_POST['attribute_id'] . "'");
        if ((DOWNLOAD_ENABLED == 'true') && $_POST['products_attributes_filename'] != '') {
          os_db_query("update " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " 
                        set products_attributes_filename='" . $_POST['products_attributes_filename'] . "',
                            products_attributes_maxdays='" . $_POST['products_attributes_maxdays'] . "',
                            products_attributes_maxcount='" . $_POST['products_attributes_maxcount'] . "'
                        where products_attributes_id = '" . $_POST['attribute_id'] . "'");
        }
        os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'delete_option':
    
    $del_options = os_db_query("select products_options_values_id from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");
    while($del_options_values = os_db_fetch_array($del_options)){  
    	  os_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['option_id'] . "'");
       	 }
        os_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");
        os_db_query("delete from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");
 
        os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'delete_value':

      	$filenames_to_delete = os_db_query("SELECT products_options_values_image from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "'");
      	while($filename_to_delete = os_db_fetch_array($filenames_to_delete)){
	      	if($filename_to_delete['products_options_values_image'] != '') {
		      	unlink(_IMG.'attribute_images/original/'.$filename_to_delete['products_options_values_image']);
		      	unlink(_IMG.'attribute_images/thumbs/'.$filename_to_delete['products_options_values_image']);
		      	unlink(_IMG.'attribute_images/mini/'.$filename_to_delete['products_options_values_image']);
	      	}
      	}

        os_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "'");
        os_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "'");
        os_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_values_id = '" . $_GET['value_id'] . "'");
        os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
      case 'delete_attribute':
        os_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_attributes_id = '" . $_GET['attribute_id'] . "'");
        os_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " where products_attributes_id = '" . $_GET['attribute_id'] . "'");
        break;
    }
  }
  
  add_action('head_admin', 'head_atributes');
  
  function head_atributes ()
  {
     echo '<script type="text/javascript"><!--
function go_option() {
  if (document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value != "none") {
    location = "'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . ($_GET['option_page'] ? $_GET['option_page'] : 1)).'&option_order_by="+document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value;
  }
}
//--></script>';
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    <?php os_header('portfolio_package.gif',HEADING_TITLE_OPT . ' - ' . HEADING_TITLE_VAL); 
	

	?> 
	
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <td valign="top" width="100%"><table width="100%" border="0" cellspacing="2" cellpadding="2">
<?php

if ($_GET['action'] == 'delete_option_value') { // delete product option value
	$values = os_db_query("select products_options_values_id, products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
	$values_values = os_db_fetch_array($values);
?>
              <tr>
                <td colspan="3" class="pageHeading">&nbsp;<?php echo $values_values['products_options_values_name']; ?>&nbsp;</td>
              </tr>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php

	$products = os_db_query("select p.products_id, pd.products_name, po.products_options_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and po.language_id = '" . $_SESSION['languages_id'] . "' and pa.products_id = p.products_id and pa.options_values_id='" . $_GET['value_id'] . "' and po.products_options_id = pa.options_id order by pd.products_name");
	if (os_db_num_rows($products)) {
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_PRODUCT; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
                  </tr>
<?php

		while ($products_values = os_db_fetch_array($products)) {
			$rows++;
?>
                  <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
                    <td align="center" class="smallText">&nbsp;<?php echo $products_values['products_id']; ?>&nbsp;</td>
                    <td class="smallText">&nbsp;<?php echo $products_values['products_name']; ?>&nbsp;</td>
                    <td class="smallText">&nbsp;<?php echo $products_values['products_options_name']; ?>&nbsp;</td>
                  </tr>
<?php

		}
?>

                  <tr>
                    <td class="main" colspan="3"><br /><?php echo TEXT_WARNING_OF_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br /><?php echo os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL'));?>&nbsp;</td>
                  </tr>
<?php

	} else {
?>
                  <tr>
                    <td class="main" colspan="3"><br /><?php echo TEXT_OK_TO_DELETE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" align="right" colspan="3"><br /><?php echo os_button_link(BUTTON_DELETE, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_value&value_id=' . $_GET['value_id'], 'NONSSL')); ?>&nbsp;&nbsp;&nbsp;<?php echo os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&option_page=' . $option_page . '&value_page=' . $_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL'));?>&nbsp;</td>
                  </tr>
<?php

	}
?>
              	</table></td>
              </tr>
<?php

} else {
?>
              <tr>
                             <td colspan="4" align="right"><br>
<table border="0">
	<tr>
	<td class="main">
<form name="search" action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>" method="GET">
<?php echo TEXT_SEARCH; ?><input type="text" name="search_optionsname" size="20" value="<?php echo $_GET['search_optionsname'];?>">
</form>
		</td>
	</tr>
</table>
								</td</tr>
              <tr>
                <td colspan="4" class="smallText">
<?php

	$per_page = MAX_DISPLAY_ADMIN_PAGE;
	if (isset ($_GET['search_optionsname'])) {
		$values = "select distinct 
								pov.products_options_values_id, 
								pov.products_options_values_name, 
								pov.products_options_values_description, 
						pov.products_options_values_text,
						pov.products_options_values_image,
						pov.products_options_values_link,
								pov2po.products_options_id 
							from " . TABLE_PRODUCTS_OPTIONS . " po,
								" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
								left join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po 
								on pov.products_options_values_id = pov2po.products_options_values_id 
							where pov.language_id = '" . $_SESSION['languages_id'] . "' 
							and pov2po.products_options_id = po.products_options_id
							and (po.products_options_name LIKE '%" . $_GET['search_optionsname'] . "%' or pov.products_options_values_name LIKE '%" . $_GET['search_optionsname'] . "%')
							order by pov.products_options_values_id";
	} else {
	///////--------------------------------------opt.products_options_name
		$values = "select 
								pov.products_options_values_id, 
								pov.products_options_values_name, 
								pov.products_options_values_description, 
						pov.products_options_values_text,
						pov.products_options_values_image,
						pov.products_options_values_link,
								pov2po.products_options_id
							from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
								left join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po 
								on pov.products_options_values_id = pov2po.products_options_values_id 
							where pov.language_id = '" . $_SESSION['languages_id'] . "'".$order;
	}
	if (!$_GET['value_page']) {
		$_GET['value_page'] = 1;
	}
	$prev_value_page = $_GET['value_page'] - 1;
	$next_value_page = $_GET['value_page'] + 1;

	$value_query = os_db_query($values);

	$value_page_start = ($per_page * $_GET['value_page']) - $per_page;
	$num_rows = os_db_num_rows($value_query);

	if ($num_rows <= $per_page) {
		$num_pages = 1;
	} else
		if (($num_rows % $per_page) == 0) {
			$num_pages = ($num_rows / $per_page);
		} else {
			$num_pages = ($num_rows / $per_page) + 1;
		}
	$num_pages = (int) $num_pages;

	$values = $values . " LIMIT $value_page_start, $per_page";

	if ($prev_value_page) {
		echo '<a href="' . os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $prev_value_page . '&search_optionsname=' . $_GET['search_optionsname']) . '"> &lt;&lt; </a> | ';
	}

	for ($i = 1; $i <= $num_pages; $i++) {
		if ($i != $_GET['value_page']) {
			echo '<a href="' . os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $i . '&search_optionsname=' . $_GET['search_optionsname']) . '">' . $i . '</a> | ';
		} else {
			echo '<b><font color="#ff0000">' . $i . '</font></b> | ';
		}
	}

	if ($_GET['value_page'] != $num_pages) {
		echo '<a href="' . os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' . $next_value_page . '&search_optionsname=' . $_GET['search_optionsname']) . '"> &gt;&gt;</a> ';
	}
?>
                </td>
              </tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;
							<?php    echo '<a href="' . os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=asc&sort-type=id">&uarr;</a>'); ?>
				<?php    echo '<a href="' . os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=desc&sort-type=id">&darr;</a>'); ?>
				</td>
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;			
				</td>
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_VALUE; ?>&nbsp;
						<?php    echo '<a href="' . os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=asc&sort-type=value">&uarr;</a>'); ?>
				<?php    echo '<a href="' . os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=desc&sort-type=value">&darr;</a>'); ?>
				</td>
                <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php

	$next_id = 1;
	$values = os_db_query($values);
	while ($values_values = os_db_fetch_array($values)) {
		$options_name = os_options_name($values_values['products_options_id']);
		$values_name = $values_values['products_options_values_name'];
		$rows++;
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php

		if (($_GET['action'] == 'update_option_value') && ($_GET['value_id'] == $values_values['products_options_values_id'])) {
			echo os_draw_form('values', FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_value&value_page=' . $_GET['value_page'], 'post', 'enctype="multipart/form-data"');
			$inputs = '';
?>
                <td align="center" class="smallText" colspan="4">
                
<table width="100%" cellpadding="1" cellspacing="0" border="0">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" width="100"><?php echo $values_values['products_options_values_id']; ?><input type="hidden" name="value_id" value="<?php echo $values_values['products_options_values_id']; ?>"></td>
    <td class="dataTableHeadingContent" width="150"><b><?php echo TABLE_HEADING_OPT_NAME; ?></b></td>
    <td class="dataTableHeadingContent" width="1"><select name="option_id"> 

<?php

			$options = os_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_options_name");
			while ($options_values = os_db_fetch_array($options)) {
				echo "\n" . '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '"';
				if ($values_values['products_options_id'] == $options_values['products_options_id']) {
					echo ' selected';
				}
				echo '>' . $options_values['products_options_name'] . '</option>';
			}
?>
	</select>
	</td>
   </tr>
<?php

        $inputs = '';
        $inputs_desc = '';
        $inputs_text = '';
        $inputs_image = '';
        $inputs_image_edit = '';
        $inputs_image_delete = '';
        $inputs_link = '';
  sort($languages);
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {

          $value_name = os_db_query("select products_options_values_name, products_options_values_description, products_options_values_text, products_options_values_image, products_options_values_link from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $values_values['products_options_values_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
				$value_name = os_db_fetch_array($value_name);
				$flag = $languages[$i]['name'];
          $inputs .= $languages[$i]['name'] . ':&nbsp;<input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15" value="' . $value_name['products_options_values_name'] . '">&nbsp;<input type="hidden" name="orig_image_'.$languages[$i]['code'].'" value="'.$value_name['products_options_values_image'].'"></input><br />';
          $inputs_text .= $languages[$i]['name'] . ':&nbsp;<input type="text" name="value_text[' . $languages[$i]['id'] . ']" size="15" value="' . $value_name['products_options_values_text'] . '">&nbsp;<br />';

				$inputs_desc = $flag . ':&nbsp;<textarea name="value_description[' . $languages[$i]['id'] . ']" cols="50" rows="4">' . $value_name['products_options_values_description'] . '</textarea>&nbsp;<br />';

          if($value_name['products_options_values_image'] != '') {
	          $inputs_image .= $languages[$i]['name'] . ':&nbsp;<img src="'.(($request_type == 'SSL') ? _HTTPS_IMG : _HTTP_IMG).'attribute_images/mini/'.$value_name['products_options_values_image'].'">&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&value_page=' . $_GET['value_page'] . '&image=edit', 'NONSSL').'">'.os_image(http_path('icons_admin').'icon_edit.gif', IMAGE_EDIT).'</a>&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&value_page=' . $_GET['value_page'] . '&image=delete', 'NONSSL').'">'.os_image(http_path('icons_admin').'delete.gif', IMAGE_DELETE).'</a>';
	          $inputs_image_delete .= $languages[$i]['name'] . ':&nbsp;<img src="'.http_path('images').'attribute_images/mini/'.$value_name['products_options_values_image'].'"></img>&nbsp;'.os_draw_checkbox_field('delete_flag[]',$languages[$i]['code']).'&nbsp;'.DELETE_TEXT;
          } else {
	          $inputs_image .= $languages[$i]['name'] . ':&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&value_page=' . $_GET['value_page'] . '&image=edit', 'NONSSL').'">'.os_image(http_path('icons_admin').'icon_edit.gif', IMAGE_EDIT).'</a>&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&value_page=' . $_GET['value_page'] . '&image=delete', 'NONSSL').'">'.os_image(http_path('icons_admin').'delete.gif', IMAGE_DELETE).'</a>';
	          $inputs_image_delete .= $languages[$i]['name'] . ':&nbsp;'.os_draw_checkbox_field('delete_flag[]',$languages[$i]['code']).'&nbsp;'.DELETE_TEXT;
          }

          $inputs_image_edit .= $languages[$i]['code'] . ':&nbsp;<input type="file" name="value_image[' . $languages[$i]['id'] . ']" size="15" value="' . $value_name['products_options_values_image'] . '">&nbsp;'.os_draw_checkbox_field('edit_flag[]',$languages[$i]['code']).'&nbsp;'.EDIT_TEXT.'&nbsp;<br />';
          $inputs_link .= $languages[$i]['name'] . ':&nbsp;http://<input type="text" name="value_link[' . $languages[$i]['id'] . ']" size="15" value="' . $value_name['products_options_values_link'] . '">&nbsp;<br />';

?>
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_VALUE; ?></b></td>
    <td class="dataTableContent" align="left"><?php echo $inputs; ?></td>
  </tr>
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_TEXT; ?></b></td>
    <td class="dataTableContent"  align="left"><?php echo $inputs_text; ?></td>
  </tr>
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_DESC; ?></b></td>
    <td class="dataTableContent"  align="left"><?php echo $inputs_desc; ?></td>
  </tr>

<?php if(($_GET['image'] == 'nothing') || (!isset($_GET['image']))) { ?>
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_IMAGE; ?></b></td>
    <td class="dataTableContent" align="left"><?php echo $inputs_image; ?></td>
  </tr>
<?php } elseif($_GET['image'] == 'edit') { ?>      
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_IMAGE; ?></b></td>
    <td class="dataTableContent" align="left"><?php echo $inputs_image_edit; ?></td>
  </tr>
<?php } elseif($_GET['image'] == 'delete') { ?>
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_IMAGE; ?></b></td>
    <td class="dataTableContent" align="left"><?php echo $inputs_image_delete; ?></td>
  </tr>
<?php } ?> 
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_LINK; ?></b></td>
    <td class="dataTableContent" align="left"><?php echo $inputs_link; ?></td>
  </tr>

<?php

			}
?>
<tr class="dataTableRowSelected">
	<td align="center" colspan="3" class="dataTableContent">&nbsp;<?php echo os_button(BUTTON_UPDATE); ?>&nbsp;<?php echo os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'value_page='.$_GET['value_page'], 'NONSSL')); ?>&nbsp;</td>
</tr>
</table>

</td>
<?php

			echo '</form>';
		} else {
?>
                <td align="center" class="smallText">&nbsp;<?php echo $values_values["products_options_values_id"]; ?>&nbsp;</td>
                <td align="center" class="smallText">&nbsp;<?php echo $options_name; ?>&nbsp;</td>
                <td class="smallText">&nbsp;<?php echo $values_name; ?>&nbsp;</td>
                <td align="center" class="smallText">&nbsp;<?php echo os_button_link(BUTTON_EDIT, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&value_page=' . $_GET['value_page'], 'NONSSL')); ?>&nbsp;&nbsp;<?php echo os_button_link(BUTTON_DELETE, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option_value&value_id=' . $values_values['products_options_values_id'], 'NONSSL')); ?>&nbsp;</td>
<?php

		}
		$max_values_id_query = os_db_query("select max(products_options_values_id) + 1 as next_id from " . TABLE_PRODUCTS_OPTIONS_VALUES);
		$max_values_id_values = os_db_fetch_array($max_values_id_query);
		$next_id = $max_values_id_values['next_id'];
	}
?>
              </tr>
<?php

	if ($_GET['action'] != 'update_option_value') {
?>
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
<?php

		echo os_draw_form('values', FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_option_values&value_page=' . $_GET['value_page'], 'post', 'enctype="multipart/form-data"');
?>
<td colspan="4">
<br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
 <tr>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_NAME; ?></b></td>
    <td class="dataTableContent"><select name="option_id">
<?php

		$options = os_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_options_name");
		while ($options_values = os_db_fetch_array($options)) {
			echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
		}
?>
                </select><input type="hidden" name="value_id" value="<?php echo $next_id; ?>"></td>
                </tr>

<?php


		$inputs = '';
		sort($languages);
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$flag = $languages[$i]['name'];
			$inputs = $flag . ':&nbsp;<input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15">&nbsp;<br />';
			$inputs_desc = $flag . ':&nbsp;<textarea name="value_description[' . $languages[$i]['id'] . ']" cols="50" rows="4"></textarea>&nbsp;<br />';

			$inputs_text = $flag . ':&nbsp;<input type="text" name="value_text[' . $languages[$i]['id'] . ']" size="15">&nbsp;<br />';
			$inputs_image = $flag . ':&nbsp;<input type="file" name="value_image[' . $languages[$i]['id'] . ']" size="15">&nbsp;<br />';
			$inputs_link = $flag . ':&nbsp;http://<input type="text" name="value_link[' . $languages[$i]['id'] . ']" size="15">&nbsp;<br /><br />';
?>

  <tr>
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_VALUE; ?></b></td>
    <td class="dataTableContent"><?php echo $inputs; ?></td>
  </tr>
  <tr>
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_TEXT; ?></b></td>
    <td class="dataTableContent"><?php echo $inputs_text; ?></td>
  </tr>
  <tr>
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_DESC; ?></b></td>
    <td class="dataTableContent"><?php echo $inputs_desc; ?></td>
  </tr>
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_IMAGE; ?></b></td>
    <td class="dataTableContent"><?php echo $inputs_image; ?></td>
  </tr>
  <tr class="dataTableRowSelected">
    <td class="dataTableContent" width="100">&nbsp;</td>
    <td class="dataTableContent" width="150"><b><?php echo TABLE_HEADING_OPT_LINK; ?></b></td>
    <td class="dataTableContent"><?php echo $inputs_link; ?></td>
  </tr>  
  
<?php


		}
?>
<tr class="dataTableRowSelected">
<td align="center" class="dataTableContent" colspan="3">&nbsp;<?php echo os_button(BUTTON_INSERT); ?>&nbsp;</td>
</tr>
</table>

</td>                
                             
<?php

		echo '</form>';
?>
              </tr>
              <tr>
                <td align="right" colspan="7"><?php echo os_button_link(BUTTON_IMAGE_PROCESSING, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'status=image_processing', 'NONSSL')); ?></td>
              </tr>
<?php

	}
}
?>
            </table></td>
          </tr>
        </table></td>
      </tr> 

 
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>