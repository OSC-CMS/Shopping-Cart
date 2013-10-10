<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ  не допускается.');
$article_query = "SELECT products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id='".(int)$_GET['current_product_id']."' and language_id = '".(int)$_SESSION['languages_id']."'";
$article_data = os_db_fetch_array(os_db_query($article_query));

$cross_sell_groups = os_get_cross_sell_groups();

function buildCAT($catID)
{
	$cat = array ();
	$tmpID = $catID;

	while (getParent($catID) != 0 || $catID != 0)
	{
		$cat_select = os_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".(int)$catID."' and language_id='".(int)$_SESSION['languages_id']."'");
		$cat_data = os_db_fetch_array($cat_select);
		$catID = getParent($catID);
		$cat[] = $cat_data['categories_name'];
	}

	$catStr = '';
	for ($i = count($cat); $i > 0; $i --)
	{
		$catStr .= $cat[$i -1].' > ';
	}

	return $catStr;
}

function getParent($catID)
{
	$parent_query = os_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".(int)$catID."'");
	$parent_data = os_db_fetch_array($parent_query);
	return $parent_data['parent_id'];
}

$breadcrumb->add($article_data['products_name'], FILENAME_CATEGORIES.'?cPath='.$_GET['cpath'].'&pID='.$_GET['current_product_id'].'&action=new_product');
$breadcrumb->add(CROSS_SELLING);

$main->head();
$main->top_menu();
?>

<?php
echo os_draw_form('cross_selling', FILENAME_CATEGORIES, '', 'GET', '');
echo os_draw_hidden_field(os_session_name(), os_session_id());
echo os_draw_hidden_field('action', 'edit_crossselling');
echo os_draw_hidden_field('special', 'edit');
echo os_draw_hidden_field('current_product_id', $_GET['current_product_id']);
echo os_draw_hidden_field('cpath', $_GET['cpath']);
?>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th width="80"><?php echo HEADING_DEL; ?></th>
				<th><span class="line"></span><?php echo HEADING_SORTING; ?></th>
				<th><span class="line"></span><?php echo HEADING_GROUP; ?></th>
				<th><span class="line"></span><?php echo HEADING_MODEL; ?></th>
				<th><span class="line"></span><?php echo HEADING_NAME; ?></th>
				<th><span class="line"></span><?php echo HEADING_CATEGORY; ?></th>
			</tr>
		</thead>

	<?php
	$cross_query = os_db_query("SELECT cs.ID,cs.products_id,pd.products_name,cs.sort_order,p.products_model,p.products_id,cs.products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." cs, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS." p WHERE cs.products_id = '".(int)$_GET['current_product_id']."' and cs.xsell_id=p.products_id and p.products_id=pd.products_id  and pd.language_id = '".(int)$_SESSION['languages_id']."' ORDER BY cs.sort_order");

	if (!os_db_num_rows($cross_query))
	{
		echo '<tr><td colspan="6">- NO ENRTY -</td></tr>';
	}

	while ($cross_data = os_db_fetch_array($cross_query))
	{
		$categorie_query = os_db_query("SELECT categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id='".(int)$cross_data['products_id']."' LIMIT 0,1");
		$categorie_data = os_db_fetch_array($categorie_query);
		?>
		<tr>
			<td class="tcenter"><input type="checkbox" name="ids[]" value="<?php echo $cross_data['ID']; ?>"></td>
			<td><?php echo $cartet->html->input_text('sort['.$cross_data['ID'].']', $cross_data['sort_order']); ?></td>
			<td><?php echo os_draw_pull_down_menu('group_name['.$cross_data['ID'].']',$cross_sell_groups,$cross_data['products_xsell_grp_name_id']); ?></td>
			<td><?php echo $cross_data['products_model']; ?></td>
			<td><?php echo $cross_data['products_name']; ?></td>
			<td><?php echo buildCAT($categorie_data['categories_id']); ?></td>
		</tr>
		<?php
		}
	?>
	</table>

	<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')" />
		<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CATEGORIES,'cPath='.$_GET['cpath'].'&pID='.$_GET['current_product_id']); ?>"><?php echo BUTTON_BACK; ?></a>
	</div>
</form>

<hr>

<?php
echo os_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
echo os_draw_hidden_field('action', 'edit_crossselling');
echo os_draw_hidden_field(os_session_name(), os_session_id());
echo os_draw_hidden_field('current_product_id', $_GET['current_product_id']);
echo os_draw_hidden_field('cpath', $_GET['cpath']);
?>
	<h5><?php echo CROSS_SELLING_SEARCH; ?></h5>
	<div class="input-append">
		<?php echo $cartet->html->input_text('search', '', array('class' => 'span12')); ?>
		<input class="btn" type="submit" onClick="this.blur();" value="<?php echo BUTTON_SEARCH; ?>" />
	</div>
</form>

<?php
// search results
if ($_GET['search'])
{
	echo os_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
	echo os_draw_hidden_field('action', 'edit_crossselling');
	echo os_draw_hidden_field('special', 'add_entries');
	echo os_draw_hidden_field('current_product_id', $_GET['current_product_id']);
	echo os_draw_hidden_field('cpath', $_GET['cpath']);
	?>
	<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th width="9%"><?php echo HEADING_ADD; ?></th>
			<th width="10%"><span class="line"></span><?php echo HEADING_GROUP; ?></th>
			<th width="10%"><span class="line"></span><?php echo HEADING_MODEL; ?></th>
			<th width="34%"><span class="line"></span><?php echo HEADING_NAME; ?></th>
			<th width="42%"><span class="line"></span><?php echo HEADING_CATEGORY; ?></th>
		</tr>
	</thead>
	<?php
	$search_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS." p WHERE p.products_id=pd.products_id and pd.language_id='".(int)$_SESSION['languages_id']."' and p.products_id!='".(int)$_GET['current_product_id']."' and (pd.products_name LIKE '%".$_GET['search']."%' or p.products_model LIKE '%".$_GET['search']."%')");

	while ($search_data = os_db_fetch_array($search_query))
	{
		$categorie_query = os_db_query("SELECT categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id='".(int)$search_data['products_id']."' LIMIT 0,1");
		$categorie_data = os_db_fetch_array($categorie_query);
		?>
		<tr>
			<td><input type="checkbox" name="ids[]" value="<?php echo $search_data['products_id']; ?>"></td>
			<td><?php echo os_draw_pull_down_menu('group_name['.$search_data['products_id'].']',$cross_sell_groups); ?></td>
			<td><?php echo $search_data['products_model']; ?></td>
			<td><?php echo $search_data['products_name']; ?></td>
			<td><?php echo buildCAT($categorie_data['categories_id']); ?></td>
		</tr>
		<?php
	}
	?>
	</table>

<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')" />
		<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CATEGORIES,'cPath='.$_GET['cpath'].'&pID='.$_GET['current_product_id']); ?>"><?php echo BUTTON_BACK; ?></a>
	</div>

	</form>
<?php } ?>