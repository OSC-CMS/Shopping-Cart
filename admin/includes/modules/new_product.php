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


if (($_GET['pID']) && (!$_POST)) 
{
        $product_query = os_db_query("select *, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available
                                       from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
                                  where p.products_id = '".(int) $_GET['pID']."'
                                  and p.products_id = pd.products_id
                                  and pd.language_id = '".$_SESSION['languages_id']."'");

        $product = os_db_fetch_array($product_query);
        $pInfo = new objectInfo($product);

      $products_extra_fields_query = os_db_query("SELECT * FROM " . TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS . " WHERE products_id=" . (int)$_GET['pID']);
      while ($products_extra_fields = os_db_fetch_array($products_extra_fields_query)) {
        $extra_field[$products_extra_fields['products_extra_fields_id']] = $products_extra_fields['products_extra_fields_value'];
      }
	  $extra_field_array=array('extra_field'=>@$extra_field);
	  $pInfo->objectInfo($extra_field_array);

}
elseif ($_POST) {
        $pInfo = new objectInfo($_POST);
        $products_name = $_POST['products_name'];
        $products_description = $_POST['products_description'];
        $products_short_description = $_POST['products_short_description'];
        $products_keywords = $_POST['products_keywords'];
        $products_meta_title = $_POST['products_meta_title'];
        $products_meta_description = $_POST['products_meta_description'];
        $products_meta_keywords = $_POST['products_meta_keywords'];
        $products_url = $_POST['products_url'];
        $products_page_url = $_POST['products_page_url'];
        $pInfo->products_startpage = $_POST['products_startpage'];
   $products_startpage_sort = $_POST['products_startpage_sort'];
} else {
        $pInfo = new objectInfo(array ());
}

$manufacturers_array = array (array ('id' => '', 'text' => TEXT_NONE));
$manufacturers_query = os_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS." order by manufacturers_name");
while ($manufacturers = os_db_fetch_array($manufacturers_query)) {
        $manufacturers_array[] = array ('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
}

$vpe_array = array (array ('id' => '', 'text' => TEXT_NONE));
$vpe_query = os_db_query("select products_vpe_id, products_vpe_name from ".TABLE_PRODUCTS_VPE." WHERE language_id='".$_SESSION['languages_id']."' order by products_vpe_name");
while ($vpe = os_db_fetch_array($vpe_query)) {
        $vpe_array[] = array ('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
}

$tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
$tax_class_query = os_db_query("select tax_class_id, tax_class_title from ".TABLE_TAX_CLASS." order by tax_class_title");
while ($tax_class = os_db_fetch_array($tax_class_query)) {
        $tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
}
$shipping_statuses = array ();
$shipping_statuses = os_get_shipping_status();
$languages = os_get_languages();

switch ($pInfo->products_status) {
        case '0' :
                $status = false;
                $out_status = true;
                break;
        case '1' :
        default :
                $status = true;
                $out_status = false;
}

switch ($pInfo->products_to_xml) {
        case '0' :
                $in_xml = false;
                $out_xml = true;
                break;
        case '1' :
        default :
                $in_xml = true;
                $out_xml = false;
}


if ($pInfo->products_startpage == '1') { $startpage_checked = true; } else { $startpage_checked = false; }

?>
<tr><td>
<?php 
$form_action = ($_GET['pID']) ? 'update_product' : 'insert_product';  
?>
<?php $fsk18_array=array(array('id'=>0,'text'=>NO),array('id'=>1,'text'=>YES)); ?>
<span class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, os_output_generated_category_path(@$current_category_id)); ?> / <?php echo(os_get_products_name(@$pInfo->products_id, @$languages[$i]['id'])); ?></span><br />
<?php
    echo os_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&action='.$form_action, 'post', 'enctype="multipart/form-data" cf="true"');
    echo os_draw_hidden_field('products_date_added', (($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
    echo os_draw_hidden_field('products_id', $pInfo->products_id);
?>
    <span class="button"><button type="submit" value="<?php echo BUTTON_SAVE; ?>" cf="false"><?php echo BUTTON_SAVE; ?></button></span>
    <a class="button" href="<?php echo os_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']); ?>"><span><?php echo BUTTON_CANCEL; ?></span></a>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <a class="button" href="<?php echo os_href_link(FILENAME_NEW_ATTRIBUTES, 'action=edit' . '&current_product_id=' . $_GET['pID'] . '&cpath=' . $cPath); ?>"><span><?php echo BUTTON_EDIT_ATTRIBUTES; ?></span></a>
    <a class="button" href="<?php echo os_href_link(FILENAME_CATEGORIES, 'action=edit_crossselling' . '&current_product_id=' . $_GET['pID'] . '&cpath=' . $cPath); ?>"><span><?php echo BUTTON_EDIT_CROSS_SELLING; ?></span></a>

<div class="tabber">
<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
{ 
  if($languages[$i]['status']==1) {
?>
        <div class="tabbertab">
        <h3><?php echo $languages[$i]['name']; ?></h3>
          <table border="0">
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_NAME; ?></td>
            <td valign="top" class="main">
			<input <?php if (SEO_URL_PRODUCT_GENERATOR == 'true' && empty($pInfo->products_page_url)) echo  'onKeyPress="onchange_products_page_url()"  onChange="onchange_products_page_url()"'; ?> id="products_name" type="text" name="<?php echo 'products_name[' . $languages[$i]['id'] . ']'; ?>" value="<?php echo htmlspecialchars(
os_get_products_name($pInfo->products_id, $languages[$i]['id'])); ?>" size=60>
			</td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_URL; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : os_get_products_url($pInfo->products_id, $languages[$i]['id'])),'size=60') . '&nbsp;<small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
            <td valign="top" class="main"><?php echo os_draw_textarea_field('products_description_' . $languages[$i]['id'], 'soft', '103', '25', (($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : os_get_products_description($pInfo->products_id, $languages[$i]['id']))); ?><br /><a href="javascript:toggleHTMLEditor('<?php echo 'products_description_' . $languages[$i]['id'];?>');" class="code"><?php echo TEXT_EDIT_E;?></a><br><br></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></td>
           <td valign="top" class="main"><?php echo os_draw_textarea_field('products_short_description_' . $languages[$i]['id'], 'soft', '103', '25', (($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : os_get_products_short_description($pInfo->products_id, $languages[$i]['id']))); ?><br /><a href="javascript:toggleHTMLEditor('<?php echo 'products_short_description_' . $languages[$i]['id'];?>');" class="code" ><?php echo TEXT_EDIT_E;?></a><br><br></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_KEYWORDS; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_keywords[' . $languages[$i]['id'] . ']',(($products_keywords[$languages[$i]['id']]) ? stripslashes($products_keywords[$languages[$i]['id']]) : os_get_products_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=80 maxlenght=255'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_META_TITLE; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_meta_title[' . $languages[$i]['id'] . ']',(($products_meta_title[$languages[$i]['id']]) ? stripslashes($products_meta_title[$languages[$i]['id']]) : os_get_products_meta_title($pInfo->products_id, $languages[$i]['id'])), 'size=80 maxlenght=50'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_META_DESCRIPTION; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_meta_description[' . $languages[$i]['id'] . ']',(($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : os_get_products_meta_description($pInfo->products_id, $languages[$i]['id'])), 'size=80 maxlenght=50'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_META_KEYWORDS; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_meta_keywords[' . $languages[$i]['id'] . ']', (($products_meta_keywords[$languages[$i]['id']]) ? stripslashes($products_meta_keywords[$languages[$i]['id']]) : os_get_products_meta_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=80 maxlenght=50'); ?></td>
          </tr>
          </table>
        </div>
<?php }} ?>

<!-- info -->
        <div class="tabbertab">
        <h3><?php echo TEXT_PRODUCTS_DATA; ?></h3>
          <table border="0">
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
            <td valign="top" class="main"><?php echo os_draw_radio_field('products_status', '1', $status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . os_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
            <td>&nbsp;&nbsp;</td>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?> <small>(YYYY-MM-DD)</small></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_date_available', $pInfo->products_date_available, 'size="10" class="format-y-m-d dividor-slash" id="example"'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_STARTPAGE; ?></td>
            <td valign="top" class="main"><?php echo os_draw_radio_field('products_startpage', '1', $startpage_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_YES . os_draw_radio_field('products_startpage', '0', !$startpage_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_NO; ?></td>
            <td></td>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_SORT; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_startpage_sort', $pInfo->products_startpage_sort ,'size=3'); ?></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_SORT; ?></td>
            <td valign="top" class="main"><?php echo  os_draw_input_field('products_sort', $pInfo->products_sort,'size=3'); ?></td>
          </tr>
<!--// Products URL begin //-->
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_PAGE_URL; ?></td>
            <td valign="top" class="main"><input type="text" name="products_page_url" id="products_page_url" value="<?php echo $pInfo->products_page_url; ?>" size=40></td>
          </tr>
<!--// Products URL end //-->
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_QUANTITY; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_quantity', $pInfo->products_quantity,'size=5'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_WEIGHT; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_weight', $pInfo->products_weight,'size=4') . '&nbsp;' . TEXT_PRODUCTS_WEIGHT_INFO; ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
            <td valign="top" class="main"><?php echo  os_draw_input_field('products_model', $pInfo->products_model); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_EAN; ?></td>
            <td valign="top" class="main"><?php echo  os_draw_input_field('products_ean', $pInfo->products_ean); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
            <td valign="top" class="main"><?php echo os_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?>&nbsp;<a href="<?php echo os_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL', false); ?>"><?php echo TEXT_EDIT; ?></a></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_FSK18; ?></td>
            <td valign="top" class="main"><?php echo os_draw_pull_down_menu('fsk18', $fsk18_array, $pInfo->products_fsk18); ?></td>
          </tr>
          <tr>
          <?php if (ACTIVATE_SHIPPING_STATUS=='true') { ?>
          <tr>
            <td valign="top" class="main"><?php echo BOX_SHIPPING_STATUS.':'; ?></td>
            <td valign="top" class="main"><?php echo os_draw_pull_down_menu('shipping_status', $shipping_statuses, $pInfo->products_shippingtime); ?>&nbsp;<a href="<?php echo os_href_link(FILENAME_SHIPPING_STATUS, '', 'NONSSL', false); ?>"><?php echo TEXT_EDIT; ?></a></td>
          </tr>
          <?php } ?>
      <tr>
<?php
$files = array();
foreach (array('product_info', 'product_options') as $key) {
   
 if ($dir = opendir(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/'.$key.'/')) {
        while (($file = readdir($dir)) !== false) {
            if (is_file(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/'.$key.'/'.$file) and ($file != "index.html")) {
                $files[$key][] = array ('id' => $file, 'text' => $file);
            } //if
        } // while
        closedir($dir);
    }
    // set default value in dropdown!
 if ($content['content_file'] == '') {
        $files[$key] = array_merge(array(array('id' => 'default', 'text' => TEXT_SELECT)), $files[$key]);
    } else {
        $files[$key] = array_merge(array(array('id' => 'default', 'text' => TEXT_NO_FILE)), $files[$key]);
    } 
}
?>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_CHOOSE_INFO_TEMPLATE; ?></td>
            <td valign="top" class="main"><?php echo os_draw_pull_down_menu('info_template', $files['product_info'], $pInfo->product_template); ?></td>
          <tr>
          </tr>
            <td valign="top" class="main"><?php echo TEXT_CHOOSE_OPTIONS_TEMPLATE.':'; ?></td>
            <td valign="top" class="main"><?php echo os_draw_pull_down_menu('options_template', $files['product_options'], $pInfo->options_template); ?></td>
          </tr>
          <tr>
            <td valign="top" colspan="2" class="main"><?php echo TEXT_YANDEX_MARKET; ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_TO_XML; ?></td>
            <td valign="top" class="main"><?php echo os_draw_radio_field('products_to_xml', '1', $in_xml) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE_TO_XML . '&nbsp;' . os_draw_radio_field('products_to_xml', '0', $out_xml) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE_TO_XML; ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_YANDEX_MARKET_BID; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('yml_bid', $pInfo->yml_bid, 'size="2"'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_YANDEX_MARKET_CBID; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('yml_cbid', $pInfo->yml_cbid, 'size="2"'); ?></td>
          </tr>
          </table>
        </div>
        <div class="tabbertab">
        <h3><?php echo strip_tags(HEADING_PRODUCT_IMAGES); ?></h3>
        <table border="0" class="main" width="100%">
        <?php include (_MODULES_ADMIN.'products_images.php'); ?>
        </table>
        </div>
        <div class="tabbertab">
        <h3><?php echo strip_tags(HEADING_PRICES_OPTIONS); ?></h3>
        <table border="0" class="main">
          <?php include(_MODULES_ADMIN.'group_prices.php'); ?>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_VPE_VISIBLE; ?></td>
            <td valign="top" class="main"><?php echo os_draw_selection_field('products_vpe_status', 'checkbox', '1',$pInfo->products_vpe_status==1 ? true : false); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_VPE_VALUE; ?></td>
            <td valign="top" class="main"><?php echo os_draw_input_field('products_vpe_value', $pInfo->products_vpe_value,'size=4'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_PRODUCTS_VPE; ?></td>
            <td valign="top" class="main"><?php echo os_draw_pull_down_menu('products_vpe', $vpe_array, $pInfo->products_vpe='' ?  DEFAULT_PRODUCTS_VPE_ID : $pInfo->products_vpe); ?>&nbsp;<a href="<?php echo os_href_link(FILENAME_PRODUCTS_VPE, '', 'NONSSL', false); ?>"><?php echo TEXT_EDIT; ?></a></td>
          </tr>
        </table>
        </div>
<!-- price -->
<!-- group check-->
<?php
    if (GROUP_CHECK == 'true') {
        $customers_statuses_array = os_get_customers_statuses();
        $customers_statuses_array = array_merge(array (array ('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
?>
        <div class="tabbertab">
        <h3><?php echo ENTRY_CUSTOMERS_ACCESS; ?></h3>
<?php
    for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
        $code = '$id=$pInfo->group_permission_'.$customers_statuses_array[$i]['id'].';';
        eval ($code);
        $checked = ($id==1) ? 'checked ' : '';
        echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
    }
?>
        </div>
<?php } ?>

        <div class="tabbertab">
        <h3><?php echo strip_tags(BOX_PRODUCT_EXTRA_FIELDS);?></h3>
        <table border="0" class="main">

<?php
// START: Extra Fields Contribution (chapter 1.4)
      // Sort language by ID  
	  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
	    $languages_array[$languages[$i]['id']]=$languages[$i];
	  }
      $extra_fields_query = os_db_query("SELECT * FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " ORDER BY products_extra_fields_order");
      while ($extra_fields = os_db_fetch_array($extra_fields_query)) {
	  // Display language icon or blank space
        if ($extra_fields['languages_id']==0) {
	    } else $m= $languages_array[$extra_fields['languages_id']]['name'];
?>
          <tr>
            <td class="main"><?php echo $extra_fields['products_extra_fields_name']; ?>:</td>
            <td class="main"><?php echo os_draw_input_field("extra_field[".$extra_fields['products_extra_fields_id']."]", $pInfo->extra_field[$extra_fields['products_extra_fields_id']]); ?></td>
          </tr>
<?php
}
?>

          <tr>
            <td colspan="2" class="main"><a href="<?php echo os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, '', 'NONSSL', false); ?>"><?php echo TEXT_EDIT_FIELDS; ?></a></td>
          </tr>

<?php
	if (os_db_num_rows($extra_fields_query) <= 0) {
?>

          <tr>
            <td colspan="2" class="main"><a href="<?php echo os_href_link(FILENAME_PRODUCTS_EXTRA_FIELDS, '', 'NONSSL', false); ?>"><?php echo TEXT_ADD_FIELDS; ?></a></td>
          </tr>

<?php
}
?>     
        
        </table>
        </div>


        <div class="tabbertab">
        <h3><?php echo TABLE_HEADING_BANDLE; ?></h3>
			<?php echo TABLE_USE_BANDLE; ?> 
			<select name="products_bundle">
				<option value="0" <?php echo ($pInfo->products_bundle == '0') ? 'selected' : ''; ?>><?php echo TABLE_USE_BANDLE_NO; ?></option>
				<option value="1" <?php echo ($pInfo->products_bundle == '1') ? 'selected' : ''; ?>><?php echo TABLE_USE_BANDLE_YES; ?></option>
			</select>
			<br />
			<br />
			<div id="bundles-block">
				<table class="bundles-block-table" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td class="br bbt-head" width="40%"><?php echo TABLE_USE_BANDLE_PRODUCT_NAME; ?></td>
						<td class="br bbt-head" width="25%"><?php echo TABLE_USE_BANDLE_PRODUCT_ID; ?></td>
						<td class="br bbt-head" width="25%"><?php echo TABLE_USE_BANDLE_PRODUCT_QTY; ?></td>
						<td class="bbt-head" width="10%"></td>
					</tr>
				</table>

				<div id="bundles">
				<?php
				// Bundle
				if (isset($pInfo->products_bundle) && $pInfo->products_bundle == '1')
				{
					// this product is a bundle so get contents data
					$bundle_query = os_db_query("
					SELECT 
						pb.subproduct_id, pb.subproduct_qty, pd.products_name 
					FROM 
						".TABLE_PRODUCTS_DESCRIPTION." pd 
							INNER JOIN ".DB_PREFIX."products_bundles pb ON pb.subproduct_id=pd.products_id 
					WHERE 
						pb.bundle_id = " . $pInfo->products_id . " and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
					while ($bundle_contents = os_db_fetch_array($bundle_query))
					{
					?>
					<table class="bundles-block-table" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="br" width="40%"><?php echo $bundle_contents['products_name']; ?></td>
							<td class="br" width="25%"><input name="bundles[id][]" type="text" value="<?php echo $bundle_contents['subproduct_id']; ?>" /></td>
							<td class="br" width="25%"><input name="bundles[qty][]" type="text" value="<?php echo $bundle_contents['subproduct_qty']; ?>" /></td>
							<td width="10%"><a class="del_bundles button" href="javascript:;"><span><?php echo TABLE_USE_BANDLE_DEL; ?></span></a></td>
						</tr>
					</table>
					<?php
					}
				}
				// End of Bundle
				?>
				</div>
				<br />
				<div><?php echo TABLE_USE_BANDLE_QTY_DESC; ?></div>
				<br />
				<table class="bundles-block-table" border="0" cellspacing="0" cellpadding="0" id="new_bundles" style='display:none;'>
					<tr>
						<td class="br" width="40%"></td>
						<td class="br" width="25%"><input name="bundles[id][]" type="text" value="" /></td>
						<td class="br" width="25%"><input name="bundles[qty][]" type="text" value="" /></td>
						<td width="10%"><a class="del_bundles button" href="javascript:;"><span><?php echo TABLE_USE_BANDLE_DEL; ?></span></a></td>
					</tr>
				</table>
				<a class="button" href="javascript:;" id="add-new-bundles"><span><?php echo TABLE_USE_BANDLE_ADD; ?></span></a>
			</div>
        </div>



		<?php 
		 /*
		       $array = array(1 => array(
			   'tab_name' => '?????', 
			   'tab_content' => '')
			   );
		 */
		   $array = array();
		   
		   $array['param'] = array('products_id' => @$_GET['pID'], 'category_id'=>@$_GET['cPath'] );
		   
		   $array = apply_filter('news_product_add_tabs', $array);
		   
		   if (isset($array['values']) && is_array($array['values']) )
		   {
		      foreach ($array['values'] as $num => $value)
			  { 
			     echo '<div class="tabbertab"><h3>'.$value['tab_name'].'</h3>';
				 echo $value['tab_content'];
			     echo '</div>';
			  }
		   }
		?>
		</form>
		
</div>
</td></tr>
