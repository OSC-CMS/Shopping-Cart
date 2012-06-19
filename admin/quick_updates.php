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

  $row_by_page = $_REQUEST['row_by_page'];
  $sort_by = $_REQUEST['sort_by'];
  $page = $_REQUEST['page'];
  $manufacturer = $_REQUEST['manufacturer'];
  $spec_price = $_REQUEST['spec_price'];
  $search = $_GET['search'];
  $search_model_key = $_GET['search_model_key'];
  
 ($row_by_page) ? define('MAX_DISPLAY_ROW_BY_PAGE' , $row_by_page ) : $row_by_page = MAX_DISPLAY_ADMIN_PAGE; define('MAX_DISPLAY_ROW_BY_PAGE' , MAX_DISPLAY_ADMIN_PAGE );
 
//// Tax Row
    $tax_class_array = array(array('id' => '0', 'text' => NO_TAX_TEXT));
    $tax_class_query = os_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = os_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

////Info Row pour le champ fabriquant
	$manufacturers_array = array(array('id' => '0', 'text' => NO_MANUFACTURER));
	$manufacturers_query = os_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
	while ($manufacturers = os_db_fetch_array($manufacturers_query)) {
		$manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
		'text' => $manufacturers['manufacturers_name']);
	}

// Display the list of the manufacturers
function manufacturers_list(){
	global $manufacturer;

	$manufacturers_query = os_db_query("select m.manufacturers_id, m.manufacturers_name from " . TABLE_MANUFACTURERS . " m order by m.manufacturers_name ASC");
	$return_string = '<select name="manufacturer" onChange="this.form.submit();">';
	$return_string .= '<option value="' . 0 . '">' . TEXT_ALL_MANUFACTURERS . '</option>';
	while($manufacturers = os_db_fetch_array($manufacturers_query)){
		$return_string .= '<option value="' . $manufacturers['manufacturers_id'] . '"';
		if($manufacturer && $manufacturers['manufacturers_id'] == $manufacturer) $return_string .= ' SELECTED';
		$return_string .= '>' . $manufacturers['manufacturers_name'] . '</option>';
	}
	$return_string .= '</select>';
	return $return_string;
}

##// Uptade database
  switch ($_GET['action']) {
    case 'update' :
      $count_update=0;
      $item_updated = array();
	  	if($_POST['product_new_model']){
		   foreach($_POST['product_new_model'] as $id => $new_model) {
			 if (trim($_POST['product_new_model'][$id]) != trim($_POST['product_old_model'][$id])) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_model='" . $new_model . "' WHERE products_id=$id");
			 }
		   }
		}
	  	if($_POST['product_new_name']){
		   foreach($_POST['product_new_name'] as $id => $new_name) {
			 if (trim($_POST['product_new_name'][$id]) != trim($_POST['product_old_name'][$id])) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION . " SET products_name='" . $new_name . "' WHERE products_id=$id and language_id=" . $_SESSION['languages_id']);
			 }
		   }
		}
		// prices
	  	if($_POST['product_new_price']){
		   foreach($_POST['product_new_price'] as $id => $new_price) {
			 if ($_POST['product_new_price'][$id] != $_POST['product_old_price'][$id] && $_POST['update_price'][$id] == 'yes') {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_price=$new_price WHERE products_id=$id");
			 }
		   }
		}
	  	
		if($_POST['product_new_price1']){
		   foreach($_POST['product_new_price1'] as $id => $new_price1) {
//			 if ($_POST['product_new_price1'][$id] != $_POST['product_old_price2'][$id] && $_POST['update_price'][$id] == 'yes') {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE `".DB_PREFIX."personal_offers_by_customers_status_1` SET `personal_offer` = '$new_price1' WHERE `products_id` = '$id'");
//			 
               //если нет записи для обновления - создаем 
			   if (mysql_affected_rows()==0)
			   {
			        os_db_query("INSERT INTO ".DB_PREFIX."personal_offers_by_customers_status_1 (products_id, quantity, personal_offer) VALUES ('$id','1','$new_price1');"); 
			   }
  //}
		   }
		}
	  	
		if($_POST['product_new_price2'])
		{
		   foreach($_POST['product_new_price2'] as $id => $new_price2) {
//			 if ($_POST['product_new_price2'][$id] != $_POST['product_old_price1'][$id] && $_POST['update_price'][$id] == 'yes') {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   //echo "UPDATE `".DB_PREFIX."personal_offers_by_customers_status_2` SET `personal_offer` = '$new_price2' WHERE `products_id` = '$id';".'<br>';
			   os_db_query("UPDATE `".DB_PREFIX."personal_offers_by_customers_status_2` SET `personal_offer` = '$new_price2' WHERE `products_id` = '$id'");
	
	           //если нет записи для обновления - создаем 
			   if (mysql_affected_rows()==0)
			   {
			        os_db_query("INSERT INTO ".DB_PREFIX."personal_offers_by_customers_status_2 (products_id, quantity, personal_offer) VALUES ('$id','1','$new_price2');"); 
			   }
//}
		   }
		}
		
		if($_POST['product_new_price3']){
		   foreach($_POST['product_new_price3'] as $id => $new_price3) {
//			 if ($_POST['product_new_price3'][$id] != $_POST['product_old_price2'][$id] && $_POST['update_price'][$id] == 'yes') {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE `".DB_PREFIX."personal_offers_by_customers_status_3` SET `personal_offer` = '$new_price3' WHERE `products_id` = '$id'");
//			 }
               //если нет записи для обновления - создаем 
			   if (mysql_affected_rows()==0)
			   {
			        os_db_query("INSERT INTO ".DB_PREFIX."personal_offers_by_customers_status_3 (products_id, quantity, personal_offer) VALUES ('$id','1','$new_price3');"); 
			   }
		   }
		}
		// prices
		
		
		if($_POST['product_new_weight']){
		   foreach($_POST['product_new_weight'] as $id => $new_weight) {
			 if ($_POST['product_new_weight'][$id] != $_POST['product_old_weight'][$id]) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_weight=$new_weight WHERE products_id=$id");
			 }
		   }
		}
		if($_POST['product_new_quantity']){
		   foreach($_POST['product_new_quantity'] as $id => $new_quantity) {
			 if ($_POST['product_new_quantity'][$id] != $_POST['product_old_quantity'][$id]) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_quantity=$new_quantity WHERE products_id=$id");
			 }
		   }
		}
		if($_POST['product_new_to_xml']){
		   foreach($_POST['product_new_to_xml'] as $id => $new_to_xml) {
			 if ($_POST['product_new_to_xml'][$id] != $_POST['product_old_to_xml'][$id]) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_to_xml=$new_to_xml WHERE products_id=$id");
			 }
		   }
		}
		if($_POST['product_new_sort']){
		   foreach($_POST['product_new_sort'] as $id => $new_sort) {
			 if ($_POST['product_new_sort'][$id] != $_POST['product_old_sort'][$id]) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_sort=$new_sort WHERE products_id=$id");
			 }
		   }
		}
	  	if($_POST['product_new_image']){
		   foreach($_POST['product_new_image'] as $id => $new_image) {
			 if (trim($_POST['product_new_image'][$id]) != trim($_POST['product_old_image'][$id])) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION . " SET products_image='" . $new_image . "' WHERE products_id=$id");
			 }
		   }
		}
		if($_POST['product_new_manufacturer']){
		   foreach($_POST['product_new_manufacturer'] as $id => $new_manufacturer) {
			 if ($_POST['product_new_manufacturer'][$id] != $_POST['product_old_manufacturer'][$id]) {
			   $count_update++;
			   $item_updated[$id] = 'updated';
			   os_db_query("UPDATE " . TABLE_PRODUCTS . " SET manufacturers_id=$new_manufacturer WHERE products_id=$id");
			 }
		   }
		}
	   	if($_POST['product_new_status']){
		   	foreach($_POST['product_new_status'] as $id => $new_status) {
			 	if ($_POST['product_new_status'][$id] != $_POST['product_old_status'][$id]) {
			   	$count_update++;
			   	$item_updated[$id] = 'updated';
			   	os_set_product_status($id, $new_status);

			 	}
		   	}
		}
	   	if($_POST['product_new_tax']){
		   	foreach($_POST['product_new_tax'] as $id => $new_tax_id) {
			 	if ($_POST['product_new_tax'][$id] != $_POST['product_old_tax'][$id]) {
			   	$count_update++;
			   	$item_updated[$id] = 'updated';
			   	os_db_query("UPDATE " . TABLE_PRODUCTS . " SET products_tax_class_id=$new_tax_id WHERE products_id=$id");
			 	}
		   	}
		}
     $count_item = array_count_values($item_updated);
     if ($count_item['updated'] > 0) $messageStack->add($count_item['updated'].' '.TEXT_PRODUCTS_UPDATED . " $count_update " . TEXT_QTY_UPDATED, 'success');
     break;

     case 'calcul' :
      if ($_POST['spec_price']) $preview_global_price = 'true';
     break;
 }

//// explode string parameters from preview product
     if($info_back && $info_back!="-") {
       $infoback = explode('-',$info_back);
       $sort_by = $infoback[0];
       $page =  $infoback[1];
       $current_category_id = $infoback[2];
       $row_by_page = $infoback[3];
	   $manufacturer = $infoback[4];
     }

//// define the sxtc for rollover lines per page
   $row_bypage_array = array(array());
   for ($i = 50; $i <= 500 ; $i=$i+50) {
      $row_bypage_array[] = array('id' => $i,
                                  'text' => $i);
   }

##// Let's start displaying page with forms
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<script language="javascript">
<!--
var browser_family;
var up = 1;

if (document.all && !document.getElementById)
  browser_family = "dom2";
else if (document.layers)
  browser_family = "ns4";
else if (document.getElementById)
  browser_family = "dom2";
else
  browser_family = "other";

function display_ttc(action, prix, taxe, up){
  if(action == 'display'){
  	if(up != 1)
  	valeur = Math.round((prix + (taxe / 100) * prix) * 100) / 100;
  }else{
  	if(action == 'keyup'){
		valeur = Math.round((parseFloat(prix) + (taxe / 100) * parseFloat(prix)) * 100) / 100;
	}else{
	 valeur = '0';
	}
  }
  switch (browser_family){
    case 'dom2':
	  document.getElementById('descDiv').innerHTML = '<?php echo TOTAL_COST; ?> : '+valeur;
      break;
    case 'ie4':
      document.all.descDiv.innerHTML = '<?php echo TOTAL_COST; ?> : '+valeur;
      break;
    case 'ns4':
      document.descDiv.document.descDiv_sub.document.write(valeur);
      document.descDiv.document.descDiv_sub.document.close();
      break;
    case 'other':
      break;
  }
}
-->
</script>

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>

    <td class="boxCenter" width="100%" valign="top">
	<?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
        <td width="100%" align="right">

				<?php
					echo os_draw_form('search', FILENAME_QUICK_UPDATES, '', 'get');
					echo HEADING_TITLE_SEARCH . ' ' . os_draw_input_field('search') . os_draw_hidden_field('search_model_key','no');
					echo '</form><br>';

					echo os_draw_form('search', FILENAME_QUICK_UPDATES, '', 'get');
					echo HEADING_TITLE_SEARCH_MODEL . ' ' . os_draw_input_field('search') . os_draw_hidden_field('search_model_key','yes');
					echo '</form>';
				?>
  
  </td>
  </tr>

  <tr>
<!-- body_text //-->

<td width="100%" valign="top">
  <table class="boxCenter" border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr><td align="center">
		   <table width="100%" cellspacing="0" cellpadding="0" border="1" bgcolor="#F3F9FB"><tr><td>
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr><td height="5"></td></tr>
					<tr align="center">
						<?php echo os_draw_form('row_by_page', FILENAME_QUICK_UPDATES, '', 'get'); echo os_draw_hidden_field( 'manufacturer', $manufacturer); echo os_draw_hidden_field( 'cPath', $current_category_id);?>
						<td><?php echo TEXT_MAXI_ROW_BY_PAGE . '&nbsp;&nbsp;' . os_draw_pull_down_menu('row_by_page', $row_bypage_array, $row_by_page, 'onChange="this.form.submit();"'); ?></td></form>
						<?php echo os_draw_form('categorie', FILENAME_QUICK_UPDATES, '', 'get'); echo os_draw_hidden_field( 'row_by_page', $row_by_page); echo os_draw_hidden_field( 'manufacturer', $manufacturer); ?>
						<td align="center" valign="top"><?php echo DISPLAY_CATEGORIES . '&nbsp;&nbsp;' . os_draw_pull_down_menu('cPath', os_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"'); ?></td></form>
						<?php echo os_draw_form('manufacturers', FILENAME_QUICK_UPDATES, '', 'get'); echo os_draw_hidden_field( 'row_by_page', $row_by_page); echo os_draw_hidden_field( 'cPath', $current_category_id);?>
						<td align="center" valign="top"><?php echo DISPLAY_MANUFACTURERS . '&nbsp;&nbsp' . manufacturers_list(); ?></td></form>
					</tr>
				</table>
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr align="center">
						<td align="center">
						  	<table border="0" cellspacing="0">
							   <form name="spec_price" <?php echo 'action="' . os_href_link(FILENAME_QUICK_UPDATES, os_get_all_get_params(array('action', 'info', 'pID')) . "action=calcul&page=$page&sort_by=$sort_by&cPath=$current_category_id&row_by_page=$row_by_page&manufacturer=$manufacturer" , 'NONSSL') . '"'; ?> method="post">
									 <tr>
									   	<td class="main"  align="center" nowrap> <?php echo TEXT_INPUT_SPEC_PRICE; ?></td>
									   	<td align="center"> <?php echo os_draw_input_field('spec_price',0,'size="5"'); ?> </td>
									   	<td align="center"><?php
										 if ($preview_global_price != true) {
												echo '&nbsp;<span class="button"><button type="submit" value="' . BUTTON_PREVIEW .'" page="' . $page . '&sort_by=' . $sort_by . '&cPath=' . $current_category_id . '&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer . '">' . BUTTON_PREVIEW .'</button></span>';
										 } else { echo '&nbsp;<a class="button" href="' . os_href_link(FILENAME_QUICK_UPDATES, "page=$page&sort_by=$sort_by&cPath=$current_category_id&row_by_page=$row_by_page&manufacturer=$manufacturer") . '"><span>' . BUTTON_CANCEL . '</span></a>'; } ?></td>
									 	 <?php if(ACTIVATE_COMMERCIAL_MARGIN == 'true'){ echo '<td align="center">&nbsp;&nbsp;' . os_draw_checkbox_field('marge','yes','','no') . ' ' . TEXT_MARGE_INFO;}?>
									 </tr>
									 <tr>
									   	<td align="center" colspan="3" nowrap>
											<?php if ($preview_global_price != 'true') {
														 echo TEXT_SPEC_PRICE_INFO1 ;
												  } else echo TEXT_SPEC_PRICE_INFO2;?>
									   	</td>
									 </tr>
								</form>
							</table>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
			</td></tr></table>
			<br />
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr align="center">
						<form name="update" method="POST" action="<?php echo "$PHP_SELF?action=update&page=$page&sort_by=$sort_by&cPath=$current_category_id&row_by_page=$row_by_page&manufacturer=$manufacturer&search=$search&search_model_key=$search_model_key"; ?>">
						<td class="smalltext" align="middle"><?php echo WARNING_MESSAGE; ?> </td>
						<?php echo "<td class=\"pageHeading\" align=\"right\">" . '<script language="javascript"><!--
							switch (browser_family)
							{
							case "dom2":
							case "ie4":
							 document.write(\'<div id="descDiv">\');
							 break;
							default:
							 document.write(\'<ilayer id="descDiv"><layer id="descDiv_sub">\');
					   	  	 break;
							}
							-->
							</script>' . "</td>\n";
						?>

						<td><span class="button"><button type="submit" value="<?php echo BUTTON_UPDATE; ?>"><?php echo BUTTON_UPDATE; ?></button></span></td>
					</tr>
			</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_MODEL == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_model DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_MODEL ."<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_model ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=pd.products_name DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_PRODUCTS . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=pd.products_name ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; ?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_STATUT == 'true')echo "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_status DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_STATUS . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_status ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_WEIGHT == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_weight DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer) ."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_WEIGHT . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_weight ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer) ."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_QUANTITY == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_quantity DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_QUANTITY . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_quantity ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_XML == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_to_xml DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_XML . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_to_xml ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_SORT == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_sort DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_SORT . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_sort ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_IMAGE == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_image DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_IMAGE . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_image ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_MANUFACTURER == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.manufacturers_id DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_MANUFACTURERS ."<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.manufacturers_id ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_price DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer) ."\" title=\"Desc\">&darr;</a>" . TABLE_HEADING_PRICE . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_price ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer) ."\" title=\"Asc\">&uarr;</a>"; ?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php echo TABLE_HEADING_PRICE . '2'; ?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php echo TABLE_HEADING_PRICE . '3'; ?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php echo TABLE_HEADING_PRICE . '4'; ?>
                </td>
                <td class="dataTableHeadingContent">
                  <?php if(DISPLAY_TAX == 'true')echo " <a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_tax_class_id DESC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Desc\">&darr;</a>" .
                     TABLE_HEADING_TAX . "<a href=\"" . os_href_link( FILENAME_QUICK_UPDATES, 'cPath='. $current_category_id .'&sort_by=p.products_tax_class_id ASC&page=' . $page.'&row_by_page=' . $row_by_page . '&manufacturer=' . $manufacturer)."\" title=\"Asc\">&uarr;</a>" ; else echo "&nbsp;";?>
                </td>
                <td class="dataTableHeadingContent">&nbsp;</td>
                <td class="dataTableHeadingContent" width="10px">&nbsp;</td>
              </tr>
          <tr class="datatableRow">
<?php
//// get the specials products list
     $specials_array = array();
     $specials_query = os_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s where s.products_id = p.products_id");
     while ($specials = os_db_fetch_array($specials_query)) {
       $specials_array[] = $specials['products_id'];
     }
//// control string sort page
     if ($sort_by && !ereg('order by',$sort_by)) $sort_by = 'order by '.$sort_by ;
//// define the string parameters for good back preview product
     $origin = FILENAME_QUICK_UPDATES . "?info_back=$sort_by-$page-$current_category_id-$row_by_page-$manufacturer";
//// controle lenght (lines per page)
     $split_page = $page ;
	 //
	 if ($split_page > 1) $rows = $split_page * MAX_DISPLAY_ROW_BY_PAGE - MAX_DISPLAY_ROW_BY_PAGE;


////  select categories

//
    if (isset($_GET['search']) && strlen(trim($_GET['search']))>0 ){ 
     if ($_GET['search_model_key'] == 'no'){
		$products_query_raw = "select p.products_id, p.products_image, p.products_model, pd.products_name, p.products_status, p.products_sort, p.products_to_xml, p.products_weight, p.products_quantity, p.manufacturers_id, p.products_price, p.products_tax_class_id from  " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and pd.products_name like '%" . $_GET['search'] . "%' $sort_by ";
		}else{
		$products_query_raw = "select p.products_id, p.products_image, p.products_model, pd.products_name, p.products_status, p.products_sort, p.products_to_xml, p.products_weight, p.products_quantity, p.manufacturers_id, p.products_price, p.products_tax_class_id from  " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_model like '%" . $_GET['search'] . "%' $sort_by ";
		}
    }else{

////
  if ($current_category_id == 0){
  	if($manufacturer){
    	$products_query_raw = "select p.products_id, p.products_image, p.products_model, pd.products_name, p.products_status, p.products_sort, p.products_to_xml, p.products_weight, p.products_quantity, p.manufacturers_id, p.products_price, p.products_tax_class_id from  " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.manufacturers_id = " . $manufacturer . " $sort_by ";
  	}else{
		$products_query_raw = "select p.products_id, p.products_image, p.products_model, pd.products_name, p.products_status, p.products_sort, p.products_to_xml, p.products_weight, p.products_quantity, p.manufacturers_id, p.products_price, p.products_tax_class_id from  " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' $sort_by ";
	}
  } else {
 	if($manufacturer){
	 	$products_query_raw = "select p.products_id, p.products_image, p.products_model, pd.products_name, p.products_status, p.products_sort, p.products_to_xml, p.products_weight, p.products_quantity, p.manufacturers_id, p.products_price, p.products_tax_class_id from  " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " pc where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_id = pc.products_id and pc.categories_id = '" . $current_category_id . "' and p.manufacturers_id = " . $manufacturer . " $sort_by ";
  	}else{
		$products_query_raw = "select p.products_id, p.products_image, p.products_model, pd.products_name, p.products_status, p.products_sort, p.products_to_xml, p.products_weight, p.products_quantity, p.manufacturers_id, p.products_price, p.products_tax_class_id from  " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " pc where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_id = pc.products_id and pc.categories_id = '" . $current_category_id . "' $sort_by ";
	}
  }
}
//// page splitter and display each products info
  $products_split = new splitPageResults($split_page, MAX_DISPLAY_ROW_BY_PAGE, $products_query_raw, $products_query_numrows);
  $products_query = os_db_query($products_query_raw);
  while ($products = os_db_fetch_array($products_query)) 
  {
     $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    $rows++;
    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
//// check for global add value or rates, calcul and round values rates
    if ($_POST['spec_price']){
    // dopisac aby dzialalo
      $spec_price = $_POST['spec_price'];
      $flag_spec = 'true' ;

      if (substr($_POST['spec_price'],-1) == '%') {
	  	if($_POST['marge'] && substr($_POST['spec_price'],0,1) != '-')
		{
			$valeur = (1 - (preg_replace("/%/", "", $_POST['spec_price']) / 100));
			$price = sprintf("%01.2f", round($products['products_price'] / $valeur,2));
		}
		else
		{
        	$price = sprintf("%01.2f", round($products['products_price'] + (($spec_price / 100) * $products['products_price']),2));
      	}
	  } else $price = sprintf("%01.2f", round($products['products_price'] + $spec_price,2));
    } else $price = $products['products_price'] ;

//// Check Tax_rate for displaying TTC
	$tax_query = os_db_query("select r.tax_rate, c.tax_class_title from " . TABLE_TAX_RATES . " r, " . TABLE_TAX_CLASS . " c where r.tax_class_id=" . $products['products_tax_class_id'] . " and c.tax_class_id=" . $products['products_tax_class_id']);
	$tax_rate = os_db_fetch_array($tax_query);
	if($tax_rate['tax_rate'] == '')$tax_rate['tax_rate'] = 0;

	if(MODIFY_MANUFACTURER == 'false'){
		$manufacturer_query = os_db_query("select manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id=" . $products['manufacturers_id']);
		$manufacturer = os_db_fetch_array($manufacturer_query);
	}
//// display infos per row
		if($flag_spec)
		{
		       echo '<tr style="background-color:'.$color.'" class="dataTableRow" onmouseover="'; 
			   if(DISPLAY_TVA_OVER == 'true')
			   {
			         echo 'display_ttc(\'display\', ' . $price . ', ' . $tax_rate['tax_rate'] . ');';
			   } 
			   echo 'this.className=\'dataTableRowOver\';" onmouseout="'; 
			   
			   if(DISPLAY_TVA_OVER == 'true')
			   {
			      echo 'display_ttc(\'delete\');';
			   } 
			   echo 'this.className=\'dataTableRow1\'">'; 
		}
		else
		{ 
		       echo '<tr style="background-color:'.$color.'" class="dataTableRow" onmouseover="'; 
			   if(DISPLAY_TVA_OVER == 'true')
			   {
			         echo 'display_ttc(\'display\', ' . $products['products_price'] . ', ' . $tax_rate['tax_rate'] . ');';
			   } 
			   echo 'this.className=\'dataTableRowOver\';" onmouseout="'; 
			   
			   if(DISPLAY_TVA_OVER == 'true')
			   {
			      echo 'display_ttc(\'delete\', \'\', \'\', 0);';
			   } 
			   echo 'this.className=\'dataTableRow2\'">';
		}
		
		if(DISPLAY_MODEL == 'true'){if(MODIFY_MODEL == 'true')echo "<td align=\"center\"><input type=\"text\" size=\"6\" name=\"product_new_model[".$products['products_id']."]\" value=\"".$products['products_model']."\"></td>\n";else echo "<td>&nbsp;" . $products['products_model'] . "</td>\n";}else{ echo "<td>";}
        if(MODIFY_NAME == 'true')echo "<td class=\"smallText\" align=\"center\">" . os_draw_input_field('product_new_name['.$products['products_id'].'] ',$products['products_name']) ."</td>\n";else echo "<td class=\"smallText\" align=\"left\">".$products['products_name']."</td>\n";
//// Product status radio button
		if(DISPLAY_STATUT == 'true'){
			if ($products['products_status'] == '1') {
			 echo "<td align=\"center\"><input  type=\"radio\" name=\"product_new_status[".$products['products_id']."]\" value=\"0\" ><input type=\"radio\" name=\"product_new_status[".$products['products_id']."]\" value=\"1\" checked ></td>\n";
			} else {
			 echo "<td align=\"center\"><input type=\"radio\" style=\"background-color: #EEEEEE\" name=\"product_new_status[".$products['products_id']."]\" value=\"0\" checked ><input type=\"radio\" style=\"background-color: #EEEEEE\" name=\"product_new_status[".$products['products_id']."]\" value=\"1\"></td>\n";
			}
		}else{
			echo "<td>&nbsp;</td>";
		}
        if(DISPLAY_WEIGHT == 'true')echo "<td align=\"center\"><input type=\"text\" size=\"5\" name=\"product_new_weight[".$products['products_id']."]\" value=\"".$products['products_weight']."\"></td>\n";else echo "<td>&nbsp;</td>";
        if(DISPLAY_QUANTITY == 'true')echo "<td align=\"center\"><input type=\"text\" size=\"3\" name=\"product_new_quantity[".$products['products_id']."]\" value=\"".$products['products_quantity']."\"></td>\n";else echo "<td>&nbsp;</td>";
		if(DISPLAY_XML == 'true')echo "<td align=\"center\"><input type=\"text\" size=\"8\" name=\"product_new_to_xml[".$products['products_id']."]\" value=\"".$products['products_to_xml']."\"></td>\n";else echo "<td>&nbsp;</td>";
		if(DISPLAY_SORT == 'true')echo "<td align=\"center\"><input type=\"text\" size=\"8\" name=\"product_new_sort[".$products['products_id']."]\" value=\"".$products['products_sort']."\"></td>\n";else echo "<td>&nbsp;</td>";
		if(DISPLAY_IMAGE == 'true')echo "<td align=\"center\"><input type=\"text\" size=\"8\" name=\"product_new_image[".$products['products_id']."]\" value=\"".$products['products_image']."\"></td>\n";else echo "<td>&nbsp;</td>";
		if(DISPLAY_MANUFACTURER == 'true'){if(MODIFY_MANUFACTURER == 'true')echo "<td align=\"center\">".os_draw_pull_down_menu("product_new_manufacturer[".$products['products_id']."]\"", $manufacturers_array, $products['manufacturers_id'])."</td>\n";else echo "<td align=\"center\">" . $manufacturer['manufacturers_name'] . "</td>";}else{ echo "<td>&nbsp;</td>";}
//// check specials
        if ( in_array($products['products_id'],$specials_array)) {
            echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price[".$products['products_id']."]\" value=\"".$products['products_price']."\" disabled >&nbsp;<a href=\"".os_href_link (FILENAME_SPECIALS, 'sID='.$products['products_id'])."\">". os_image(http_path('icons_admin') . 'icon_info.gif', TEXT_SPECIALS_PRODUCTS) ."</a></td>\n";
        } else {
            if ($flag_spec == 'true') {
                   echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price[".$products['products_id']."]\" "; if(DISPLAY_TVA_UP == 'true'){ echo "onKeyUp=\"display_ttc('keyup', this.value" . ", " . $tax_rate['tax_rate'] . ", 1);\"";} echo " value=\"".$price ."\">".os_draw_checkbox_field('update_price['.$products['products_id'].']','yes','checked','no')."</td>\n";
            } else { echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price[".$products['products_id']."]\" "; if(DISPLAY_TVA_UP == 'true'){ echo "onKeyUp=\"display_ttc('keyup', this.value" . ", " . $tax_rate['tax_rate'] . ", 1);\"";} echo " value=\"".$price ."\">".os_draw_hidden_field('update_price['.$products['products_id'].']','yes'). "</td>\n";}
        }
       
		//
		$xquery = "SELECT `personal_offer` FROM `".DB_PREFIX."personal_offers_by_customers_status_1` WHERE `products_id` = '" . $products['products_id'] . "'";
		$xres = os_db_query($xquery);
		$xobj = mysql_fetch_object($xres);
		$xprice1 = $xobj->personal_offer;

    if ($_POST['spec_price']){
    // dopisac aby dzialalo
      $spec_price = $_POST['spec_price'];
      $flag_spec = 'true' ;

      if (substr($_POST['spec_price'],-1) == '%') {
	  	if($_POST['marge'] && substr($_POST['spec_price'],0,1) != '-'){
			$valeur = (1 - (preg_replace("/%/", "", $_POST['spec_price']) / 100));
			$xprice1 = sprintf("%01.2f", round($xobj->personal_offer / $valeur,2));
		}else{
        	$xprice1 = sprintf("%01.2f", round($xobj->personal_offer + (($spec_price / 100) * $xobj->personal_offer),2));
      	}
	  } else $xprice1 = sprintf("%01.2f", round($xobj->personal_offer + $spec_price,2));
    } else $xprice1 = $xobj->personal_offer ;


        if ( in_array($products['products_id'],$specials_array)) {
            echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price1[".$products['products_id']."]\" value=\"".$products['products_price']."\" disabled >&nbsp;<a href=\"".os_href_link (FILENAME_SPECIALS, 'sID='.$products['products_id'])."\">". os_image(http_path('icons_admin') . 'icon_info.gif', TEXT_SPECIALS_PRODUCTS) ."</a></td>\n";
        } else {
            if ($flag_spec == 'true') {
                   echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price1[".$products['products_id']."]\" value=\"".$xprice1 ."\"></td>\n";
            } else { echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price1[".$products['products_id']."]\" value=\"".$xprice1 ."\"></td>\n";
			}
       		echo os_draw_hidden_field('product_old_price1['.$products['products_id'].']', $xprice1);
        }

		//
		$xquery2 = "SELECT `personal_offer` FROM `".DB_PREFIX."personal_offers_by_customers_status_2` WHERE `products_id` = '" . $products['products_id'] . "'";
		$xres2 = os_db_query($xquery2);
		$xobj2 = mysql_fetch_object($xres2);
		$xprice2 = $xobj->personal_offer;
		
    if ($_POST['spec_price']){
    // dopisac aby dzialalo
      $spec_price = $_POST['spec_price'];
      $flag_spec = 'true' ;

      if (substr($_POST['spec_price'],-1) == '%') {
	  	if($_POST['marge'] && substr($_POST['spec_price'],0,1) != '-'){
			$valeur = (1 - (preg_replace("/%/", "", $_POST['spec_price']) / 100));
			$xprice2 = sprintf("%01.2f", round($xobj2->personal_offer / $valeur,2));
		}else{
        	$xprice2 = sprintf("%01.2f", round($xobj2->personal_offer + (($spec_price / 100) * $xobj2->personal_offer),2));
      	}
	  } else $xprice2 = sprintf("%01.2f", round($xobj2->personal_offer + $spec_price,2));
    } else $xprice2 = $xobj2->personal_offer ;
		
        if ( in_array($products['products_id'],$specials_array)) {
            echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price2[".$products['products_id']."]\" value=\"".$products['products_price']."\" disabled >&nbsp;<a href=\"".os_href_link (FILENAME_SPECIALS, 'sID='.$products['products_id'])."\">". os_image(http_path('icons_admin') . 'icon_info.gif', TEXT_SPECIALS_PRODUCTS) ."</a></td>\n";
        } else {
            if ($flag_spec == 'true') {
                   echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price2[".$products['products_id']."]\" value=\"".$xprice2 ."\"></td>\n";
            } else { echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price2[".$products['products_id']."]\" value=\"".$xprice2 ."\"></td>\n";
			}
       		echo os_draw_hidden_field('product_old_price2['.$products['products_id'].']', $xprice2);
        }

		//
		$xquery3 = "SELECT `personal_offer` FROM `".DB_PREFIX."personal_offers_by_customers_status_3` WHERE `products_id` = '" . $products['products_id'] . "'";
		$xres3 = os_db_query($xquery3);
		$xobj3 = mysql_fetch_object($xres3);
		$xprice3 = $xobj3->personal_offer;
		
    if ($_POST['spec_price']){
    // dopisac aby dzialalo
      $spec_price = $_POST['spec_price'];
      $flag_spec = 'true' ;

      if (substr($_POST['spec_price'],-1) == '%') {
	  	if($_POST['marge'] && substr($_POST['spec_price'],0,1) != '-'){
			$valeur = (1 - (preg_replace("/%/", "", $_POST['spec_price']) / 100));
			$xprice3 = sprintf("%01.2f", round($xobj3->personal_offer / $valeur,2));
		}else{
        	$xprice3 = sprintf("%01.2f", round($xobj3->personal_offer + (($spec_price / 100) * $xobj3->personal_offer),2));
      	}
	  } else $xprice3 = sprintf("%01.2f", round($xobj3->personal_offer + $spec_price,2));
    } else $xprice3 = $xobj3->personal_offer ;
		
        if ( in_array($products['products_id'],$specials_array)) {
            echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price3[".$products['products_id']."]\" value=\"".$products['products_price']."\" disabled >&nbsp;<a href=\"".os_href_link (FILENAME_SPECIALS, 'sID='.$products['products_id'])."\">". os_image(http_path('icons_admin') . 'icon_info.gif', TEXT_SPECIALS_PRODUCTS) ."</a></td>\n";
        } else {
            if ($flag_spec == 'true') {
                   echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price3[".$products['products_id']."]\" value=\"".$xprice3 ."\"></td>\n";
            } else { echo "<td align=\"center\">&nbsp;<input type=\"text\" size=\"6\" name=\"product_new_price3[".$products['products_id']."]\" value=\"".$xprice3 ."\"></td>\n";
			}
       		echo os_draw_hidden_field('product_old_price3['.$products['products_id'].']', $xprice3);
        }
        
        if(DISPLAY_TAX == 'true'){if(MODIFY_TAX == 'true')echo "<td align=\"center\">".os_draw_pull_down_menu("product_new_tax[".$products['products_id']."]\"", $tax_class_array, $products['products_tax_class_id'])."</td>\n";else echo "<td align=\"center\">" . $tax_rate['tax_class_title'] . "&nbsp;</td>";}else{ echo "<td>&nbsp;</td>";}
        
//// links to preview or full edit
        if(DISPLAY_PREVIEW == 'true')echo "<td align=\"center\"><a href=\"".os_href_link (FILENAME_CATEGORIES, 'pID='.$products['products_id'].'&action=new_product_preview&read=only&sort_by='.$sort_by.'&page='.$split_page.'&origin='.$origin)."\">". os_image(http_path('icons_admin') . 'icon_info.gif', TEXT_IMAGE_PREVIEW) ."</a>&nbsp;</td>";else{ echo "<td>&nbsp;</td>";}"\n";
		if(DISPLAY_EDIT == 'true')echo "<td align=\"center\"><a href=\"".os_href_link (FILENAME_CATEGORIES, 'pID='.$products['products_id'].'&cPath='.$categories_products[0].'&action=new_product')."\">". os_image(http_path('icons_admin') . 'icon_arrow_right.gif', TEXT_IMAGE_SWITCH_EDIT) ."</a></td>";else{ echo "<td>&nbsp;</td>";}"\n";

//// Hidden parameters for cache old values
		if(MODIFY_NAME == 'true') echo os_draw_hidden_field('product_old_name['.$products['products_id'].'] ',$products['products_name']);
        if(MODIFY_MODEL == 'true') echo os_draw_hidden_field('product_old_model['.$products['products_id'].'] ',$products['products_model']);
		echo os_draw_hidden_field('product_old_status['.$products['products_id'].']',$products['products_status']);
        echo os_draw_hidden_field('product_old_quantity['.$products['products_id'].']',$products['products_quantity']);
		echo os_draw_hidden_field('product_old_to_xml['.$products['products_id'].']',$products['products_to_xml']);
		echo os_draw_hidden_field('product_old_sort['.$products['products_id'].']',$products['products_sort']);
		echo os_draw_hidden_field('product_old_image['.$products['products_id'].']',$products['products_image']);
        if(MODIFY_MANUFACTURER == 'true')echo os_draw_hidden_field('product_old_manufacturer['.$products['products_id'].']',$products['manufacturers_id']);
		echo os_draw_hidden_field('product_old_weight['.$products['products_id'].']',$products['products_weight']);
        echo os_draw_hidden_field('product_old_price['.$products['products_id'].']',$products['products_price']);
        if(MODIFY_TAX == 'true')echo os_draw_hidden_field('product_old_tax['.$products['products_id'].']',$products['products_tax_class_id']);
//// hidden display parameters
        echo os_draw_hidden_field( 'row_by_page', $row_by_page);
        echo os_draw_hidden_field( 'sort_by', $sort_by);
        echo os_draw_hidden_field( 'page', $split_page);
     }
?>
<tr>
<td colspan="15">
<?php
		 //// display bottom page buttons
    echo '<a class="button" href="' . os_href_link(FILENAME_QUICK_UPDATES,"row_by_page=$row_by_page") . '" id="box_properties"><span>' . BUTTON_CANCEL . '</span></a> ';
?><span class="button"><button value="<?php echo PRINT_TEXT?>" title="<?php echo PRINT_TEXT?>" onclick="print();" id="box_properties_input"><?php echo PRINT_TEXT?></button></span>
</td>
<td colspan="3">

<span class="button"><button type="submit" value="<?php echo BUTTON_UPDATE; ?>"><?php echo BUTTON_UPDATE; ?></button></span>
</td>
</tr>
    </table>
</form>

		</td>
      </tr>
            <td>
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
                	<td><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_ROW_BY_PAGE, $split_page, TEXT_DISPLAY_NUMBER_OF_PRODUCTS);  ?></td>
                	<td align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_ROW_BY_PAGE, MAX_DISPLAY_PAGE_LINKS, $split_page, '&cPath='. $current_category_id . '&manufacturer='. $manufacturer .'&sort_by='.$sort_by . '&row_by_page=' . $row_by_page . '&search=' . $search . '&search_model_key=' . $search_model_key); ?></td>
            	</table>
			</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>

<?php $main->bottom(); ?>