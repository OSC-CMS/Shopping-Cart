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

defined('_VALID_OS') or die('Прямой доступ не допускается.');
$article_query = "SELECT products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id='".(int) $_GET['current_product_id']."' and language_id = '".$_SESSION['languages_id']."'";
$article_data = os_db_fetch_array(os_db_query($article_query));

$cross_sell_groups = os_get_cross_sell_groups();

function buildCAT($catID) {

	$cat = array ();
	$tmpID = $catID;

	while (getParent($catID) != 0 || $catID != 0) {
		$cat_select = os_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".$catID."' and language_id='".$_SESSION['languages_id']."'");
		$cat_data = os_db_fetch_array($cat_select);
		$catID = getParent($catID);
		$cat[] = $cat_data['categories_name'];

	}
	$catStr = '';
	for ($i = count($cat); $i > 0; $i --) {
		$catStr .= $cat[$i -1].' > ';
	}

	return $catStr;
}

function getParent($catID) {
	$parent_query = os_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".$catID."'");
	$parent_data = os_db_fetch_array($parent_query);
	return $parent_data['parent_id'];
}
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo CROSS_SELLING.' : '.$article_data['products_name']; ?></td>
          </tr>
          <tr>
            <td colspan="2"><a class="button" href="<?php echo os_href_link(FILENAME_CATEGORIES,'cPath='.$_GET['cpath'].'&pID='.$_GET['current_product_id']); ?>"><span><?php echo BUTTON_BACK; ?></span></a></td>
          </tr>
        </table></td>
      </tr>
	  <tr>
        <td>
        
        <?php

echo os_draw_form('cross_selling', FILENAME_CATEGORIES, '', 'GET', '');
echo os_draw_hidden_field(os_session_name(), os_session_id());
echo os_draw_hidden_field('action', 'edit_crossselling');
echo os_draw_hidden_field('special', 'edit');
echo os_draw_hidden_field('current_product_id', $_GET['current_product_id']);
echo os_draw_hidden_field('cpath', $_GET['cpath']);
?>
 
 
 <table width="100%" border="0">
  <tr>
    <td class="dataTableHeadingContent" width="1%"><?php echo HEADING_DEL; ?></td>
    <td class="dataTableHeadingContent" width="4%"><?php echo HEADING_SORTING; ?></td>
    <td class="dataTableHeadingContent" width="5%"><?php echo HEADING_GROUP; ?></td>
    <td class="dataTableHeadingContent" width="15%"><?php echo HEADING_MODEL; ?></td>
    <td class="dataTableHeadingContent" width="34%"><?php echo HEADING_NAME; ?></td>
    <td class="dataTableHeadingContent" width="42%"><?php echo HEADING_CATEGORY; ?></td>
  </tr>
<?php


$cross_query = "SELECT cs.ID,cs.products_id,pd.products_name,cs.sort_order,p.products_model,p.products_id,cs.products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." cs, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS." p WHERE cs.products_id = '".(int) $_GET['current_product_id']."' and cs.xsell_id=p.products_id and p.products_id=pd.products_id  and pd.language_id = '".$_SESSION['languages_id']."' ORDER BY cs.sort_order";
$cross_query = os_db_query($cross_query);
if (!os_db_num_rows($cross_query)) {
?>
  <tr>
    <td class="categories_view_data" colspan="6">- NO ENRTY -</td>
  </tr>
<?php


}
while ($cross_data = os_db_fetch_array($cross_query)) {
	$categorie_query = os_db_query("SELECT
		                                            categories_id
		                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
		                                            WHERE products_id='".$cross_data['products_id']."' LIMIT 0,1");
	$categorie_data = os_db_fetch_array($categorie_query);
?>

  <tr>
    <td class="categories_view_data"><input type="checkbox" name="ids[]" value="<?php echo $cross_data['ID']; ?>"></td>
    <td class="categories_view_data"><input name="sort[<?php echo $cross_data['ID']; ?>]" type="text" size="3" value="<?php echo $cross_data['sort_order']; ?>"></td>
    
    <td class="categories_view_data" style="text-align: left;"><?php echo os_draw_pull_down_menu('group_name['.$cross_data['ID'].']',$cross_sell_groups,$cross_data['products_xsell_grp_name_id']); ?></td>
    
    <td class="categories_view_data" style="text-align: left;"><?php echo $cross_data['products_model']; ?></td>
    <td class="categories_view_data" style="text-align: left;"><?php echo $cross_data['products_name']; ?></td>
    <td class="categories_view_data" style="text-align: left;"><?php echo buildCAT($categorie_data['categories_id']); ?> </td>
  </tr>

<?php } ?>
</table>
<span class="button"><button type="submit" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')"><?php echo BUTTON_SAVE; ?></button></span>
</form>
</td>
</tr>
<tr>
<td class="pageHeading"><hr noshade><?php echo CROSS_SELLING_SEARCH; ?>

<table>
<br><br>
<tr class="dataTableRow">
<?php


	echo os_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
	echo os_draw_hidden_field('action', 'edit_crossselling');
	echo os_draw_hidden_field(os_session_name(), os_session_id());
	echo os_draw_hidden_field('current_product_id', $_GET['current_product_id']);
	echo os_draw_hidden_field('cpath', $_GET['cpath']);
?>
<td class="dataTableContent" width="40"><?php echo os_draw_input_field('search', '', 'size="30"');?></td>
<td class="dataTableContent">
<?php


	echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SEARCH . '"/>' . BUTTON_SEARCH . '</button></span>';
?>
</td>
</form>
</tr>
</table>
<hr noshade>
</td>
</tr>
<tr>
<td>

<?php


	// search results
	if ($_GET['search']) {
		echo os_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
		echo os_draw_hidden_field('action', 'edit_crossselling');
		echo os_draw_hidden_field('special', 'add_entries');
		echo os_draw_hidden_field('current_product_id', $_GET['current_product_id']);
		echo os_draw_hidden_field('cpath', $_GET['cpath']);
?>
 <table width="100%" border="0">
  <tr>
    <td class="dataTableHeadingContent" width="9%"><?php echo HEADING_ADD; ?></td>
    <td class="dataTableHeadingContent" width="10%"><?php echo HEADING_GROUP; ?></td>
    <td class="dataTableHeadingContent" width="10%"><?php echo HEADING_MODEL; ?></td>
    <td class="dataTableHeadingContent" width="34%"><?php echo HEADING_NAME; ?></td>
    <td class="dataTableHeadingContent" width="42%"><?php echo HEADING_CATEGORY; ?></td>
  </tr>
  <?php


		$search_query = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS." p WHERE p.products_id=pd.products_id and pd.language_id='".$_SESSION['languages_id']."' and p.products_id!='".$_GET['current_product_id']."' and (pd.products_name LIKE '%".$_GET['search']."%' or p.products_model LIKE '%".$_GET['search']."%')";
		$search_query = os_db_query($search_query);

		while ($search_data = os_db_fetch_array($search_query)) {
			$categorie_query = os_db_query("SELECT
						                                            categories_id
						                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
						                                            WHERE products_id='".$search_data['products_id']."' LIMIT 0,1");
			$categorie_data = os_db_fetch_array($categorie_query);
?>
  <tr>
    <td class="categories_view_data"><input type="checkbox" name="ids[]" value="<?php echo $search_data['products_id']; ?>"></td>
    <td class="categories_view_data" style="text-align: left;"><?php echo os_draw_pull_down_menu('group_name['.$search_data['products_id'].']',$cross_sell_groups); ?></td>
    <td class="categories_view_data" style="text-align: left;"><?php echo $search_data['products_model']; ?></td>
    <td class="categories_view_data" style="text-align: left;"><?php echo $search_data['products_name']; ?></td>
    <td class="categories_view_data" style="text-align: left;"><?php echo buildCAT($categorie_data['categories_id']); ?> </td>
  </tr>

<?php


		}
?>

</table>
<span class="button"><button type="submit" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')"><?php echo BUTTON_SAVE; ?></button></span>
</form>
<?php } ?>

</td>
</tr>
</td>
