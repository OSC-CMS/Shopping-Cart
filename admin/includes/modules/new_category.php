<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

if ( ($_GET['cID']) && (!$_POST) )
{
	$category_query = os_db_query("select * from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.categories_id = '".(int)$_GET['cID']."'");
	$category = os_db_fetch_array($category_query);

	$cInfo = new objectInfo($category);
}
elseif ($_POST)
{
	$cInfo = new objectInfo($_POST);
	$categories_name = $_POST['categories_name'];
	$categories_heading_title = $_POST['categories_heading_title'];
	$categories_description = $_POST['categories_description'];
	$categories_meta_title = $_POST['categories_meta_title'];
	$categories_meta_description = $_POST['categories_meta_description'];
	$categories_meta_keywords = $_POST['categories_meta_keywords'];
}
else
	$cInfo = new objectInfo(array());

$languages = os_get_languages();

$text_new_or_edit = ($_GET['action']=='new_category_ACD') ? TEXT_INFO_HEADING_NEW_CATEGORY : TEXT_INFO_HEADING_EDIT_CATEGORY;

//$catTitle = ($_GET['new_category']) ? TEXT_NEW_CATEGORY : 
$breadcrumb->add(sprintf($text_new_or_edit, os_output_generated_category_path($current_category_id)));

$main->head();
$main->top_menu();
?>



<?php
$form_action = ($_GET['cID']) ? 'update_category' : 'insert_category'; 
echo os_draw_form('new_category', FILENAME_CATEGORIES, 'cPath='.$cPath.'&cID='.$_GET['cID'].'&action='.$form_action, 'post', 'enctype="multipart/form-data" cf="true"');
?>
	<ul class="nav nav-tabs" id="productTabs">
		<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
		<li <?php echo ($i == 0) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $languages[$i]['id']; ?>"><?php echo $languages[$i]['name']; ?></a></li>
		<?php } ?>
		<li><a href="#tab_info"><?php echo TEXT_PRODUCTS_DATA; ?></a></li>
		<li><a href="#tab_image"><?php echo TEXT_TAB_CATEGORIES_IMAGE; ?></a></li>
		<?php 
		$array = array();
		$array['param'] = array('category_id' => @$_GET['cID'] );
		$array = apply_filter('news_category_add_tabs', $array);

		if (isset($array['values']) && is_array($array['values']) )
		{
			$ip = 0;
			foreach ($array['values'] as $num => $value)
			{
				$ip++;
				echo '<li><a href="#tab_plugin_'.$ip.'">'.$value['tab_name'].'</a></li>';
			}
		}
		?>
		<?php if (GROUP_CHECK=='true') { ?>
		<li><a href="#tab_groups"><?php echo ENTRY_CUSTOMERS_ACCESS; ?></a></li>
		<?php } ?>
	</ul>

	<div class="tab-content">
		<!-- КАТЕГОРИИ -->
		<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
		{ 
			if($languages[$i]['status']==1)
			{

			if (SEO_URL_CATEGORIES_GENERATOR == 'true' && empty($cInfo->categories_url))
				$catParams = array('id' => 'categories_name', 'class' => 'span12', 'onKeyPress' => 'onchange_categories_url()', 'onChange' =>'onchange_categories_url()');
			else
				$catParams = array('id' => 'categories_name', 'class' => 'span12');
			?>
			<div class="tab-pane <?php echo ($i == 0) ? 'active' : ''; ?>" id="tab_lang_<?php echo $languages[$i]['id']; ?>">
				<div class="pt10">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_EDIT_CATEGORIES_NAME; ?></label>
						<div class="controls">
							<?php
							echo $cartet->html->input_text(
								'categories_name['.$languages[$i]['id'].']',
								(($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : os_get_categories_name($cInfo->categories_id, $languages[$i]['id'])),
								$catParams
							);
							?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?></label>
						<div class="controls">
							<?php
							echo $cartet->html->input_text(
								'categories_heading_title['.$languages[$i]['id'].']',
								(($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : os_get_categories_heading_title($cInfo->categories_id, $languages[$i]['id'])),
								array('id' => 'categories_heading_title_'.$languages[$i]['id'], 'class' => 'span12')
							);
							?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_EDIT_CATEGORIES_DESCRIPTION; ?></label>
						<div class="controls">
							<?php
							echo $cartet->html->textarea(
								'categories_description['.$languages[$i]['id'].']',
								(($categories_description[$languages[$i]['id']]) ? stripslashes($categories_description[$languages[$i]['id']]) : os_get_categories_description($cInfo->categories_id, $languages[$i]['id'])),
								array(
									'id' => 'categories_description['.$languages[$i]['id'].']', 'class' => 'span12 textarea_big'
								)
							);
							?>
						</div>
					</div>
					<hr>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_META_TITLE; ?></label>
						<div class="controls">
							<?php
							echo $cartet->html->input_text(
								'categories_meta_title['.$languages[$i]['id'].']',
								(($categories_meta_title[$languages[$i]['id']]) ? stripslashes($categories_meta_title[$languages[$i]['id']]) : os_get_categories_meta_title($cInfo->categories_id, $languages[$i]['id'])),
								array('class' => 'span12')
							);
							?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_META_DESCRIPTION; ?></label>
						<div class="controls">
							<?php
							echo $cartet->html->input_text(
								'categories_meta_description['.$languages[$i]['id'].']',
								(($categories_meta_description[$languages[$i]['id']]) ? stripslashes($categories_meta_description[$languages[$i]['id']]) : os_get_categories_meta_description($cInfo->categories_id, $languages[$i]['id'])),
								array('class' => 'span12')
							);
							?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_META_KEYWORDS; ?></label>
						<div class="controls">
							<?php
							echo $cartet->html->input_text(
								'categories_meta_keywords['.$languages[$i]['id'].']',
								(($categories_meta_keywords[$languages[$i]['id']]) ? stripslashes($categories_meta_keywords[$languages[$i]['id']]) : os_get_categories_meta_keywords($cInfo->categories_id, $languages[$i]['id'])),
								array('class' => 'span12')
							);
							?>
						</div>
					</div>
				</div>

			</div>
			<?php
			}
		}
		?>
		<!-- /КАТЕГОРИИ -->

		<!-- ДОПОЛНИТЕЛЬНО -->
		<div class="tab-pane" id="tab_info">
			<div class="pt10">
				<div class="row-fluid">

					<div class="span6">
						<div class="control-group">
							<label class="control-label" for="categories_url"><?php echo TEXT_EDIT_CATEGORY_URL; ?></label>
							<div class="controls">
								<?php echo $cartet->html->input_text('categories_url', $cInfo->categories_url, array('id' => 'categories_url', 'class' => 'span12')); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<label class="checkbox">
									<?php echo os_draw_selection_field('menu', 'checkbox', '1',$cInfo->menu==1 ? true : false); ?> <?php echo TABLE_HEADING_MENU; ?>
								</label>
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<label class="checkbox">
									<?php echo os_draw_selection_field('status', 'checkbox', '1',$cInfo->categories_status==1 ? true : false); ?> <?php echo TEXT_EDIT_STATUS; ?>
								</label>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="sort_order"><?php echo TEXT_EDIT_SORT_ORDER; ?></label>
							<div class="controls">
								<?php echo $cartet->html->input_text('sort_order', $cInfo->sort_order, array('id' => 'sort_order', 'class' => 'span6')); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_CHOOSE_INFO_TEMPLATE_LISTING; ?></label>
							<div class="controls">
								<?php
								$files = array();
								if ($dir = opendir(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/product_listing/'))
								{
									while  (($file = readdir($dir)) !==false)
									{
										if (is_file(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/product_listing/'.$file) and ($file !="index.html"))
										{
											$files[] = array
											(
												'id' => $file,
												'text' => $file
											);
										}
									} 
									closedir($dir);
								}

								$default_array = array();
								// set default value in dropdown!
								if ($content['content_file']=='')
								{
									$default_array[] = array('id' => 'default','text' => TEXT_SELECT);
									$default_value = $cInfo->listing_template;
									$files = array_merge($default_array,$files);
								}
								else
								{
									$default_array[] = array('id' => 'default','text' => TEXT_NO_FILE);
									$default_value = $cInfo->listing_template;
									$files = array_merge($default_array,$files);
								}
								echo os_draw_pull_down_menu('listing_template',$files, $default_value);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE; ?></label>
							<div class="controls">
								<?php
								$files = array();
								if ($dir = opendir(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/categorie_listing/'))
								{
									while  (($file = readdir($dir)) !==false)
									{
										if (is_file( DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and ($file !="index.html"))
										{
											$files[] = array
											(
												'id' => $file,
												'text' => $file
											);
										}
									} 
									closedir($dir);
								}

								$default_array = array();
								if ($content['content_file']=='')
								{
									$default_array[] = array('id' => 'default','text' => TEXT_SELECT);
									$default_value = $cInfo->categories_template;
									$files = array_merge($default_array,$files);
								}
								else
								{
									$default_array[] = array('id' => 'default','text' => TEXT_NO_FILE);
									$default_value = $cInfo->categories_template;
									$files = array_merge($default_array,$files);
								}
								echo os_draw_pull_down_menu('categories_template',$files,$default_value);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?></label>
							<div class="controls">
								<?php
								$order_array = '';
								$order_array = array
								(
									array('id' => 'p.products_price','text'=>TXT_PRICES),
									array('id' => 'pd.products_name','text'=>TXT_NAME),
									array('id' => 'p.products_ordered','text'=>TXT_ORDERED),
									array('id' => 'p.products_sort','text'=>TXT_SORT),
									array('id' => 'p.products_weight','text'=>TXT_WEIGHT),
									array('id' => 'p.products_quantity','text'=>TXT_QTY),
									array('id' => 'p.products_date_added','text'=>TXT_DATE_ADD)
								);
								$default_value = 'pd.products_name';
								?>
								<?php echo os_draw_pull_down_menu('products_sorting',$order_array,$cInfo->products_sorting); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?></label>
							<div class="controls">
								<?php
								$order_array = '';
								$order_array = array(array('id' => 'ASC','text'=>'ASC (1 first)'), array('id' => 'DESC','text'=>'DESC (1 last)'));
								?>
								<?php echo os_draw_pull_down_menu('products_sorting2',$order_array,$cInfo->products_sorting2); ?>
							</div>
						</div>
					</div>
					<div class="span6">
						<legend><?php echo TEXT_YANDEX_MARKET; ?></legend>

						<div class="control-group">
							<label class="control-label" for="yml_bid"><?php echo TEXT_YANDEX_MARKET_BID; ?></label>
							<div class="controls">
								<?php echo $cartet->html->input_text('yml_bid', $cInfo->yml_bid, array('id' => 'yml_bid', 'class' => 'span6')); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="yml_cbid"><?php echo TEXT_YANDEX_MARKET_CBID; ?></label>
							<div class="controls">
								<?php echo $cartet->html->input_text('yml_cbid', $cInfo->yml_cbid, array('id' => 'yml_cbid', 'class' => 'span6')); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /ДОПОЛНИТЕЛЬНО -->

		<!-- КАРТИНКА -->
		<div class="tab-pane" id="tab_image">
			<div class="pt10">
				<div class="row-fluid">
					<div class="span2">
					
						<?php if ($cInfo->categories_image) { ?>
						<div class="tcenter"><img class="img-polaroid" src="<?php echo DIR_WS_CATALOG.'images/categories/'.$cInfo->categories_image; ?>"></div>
						<br />
						<br />
						<label class="checkbox"><?php $cInfo->categories_image; echo os_draw_selection_field('del_cat_pic', 'checkbox', 'yes').TEXT_DELETE; ?></label>
						<?php } ?>
					</div>
					<div class="span10">
						<h4><?php echo TEXT_EDIT_CATEGORIES_IMAGE; ?></h4>
						<?php echo os_draw_file_field('categories_image').os_draw_hidden_field('categories_previous_image', $cInfo->categories_image); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- /КАРТИНКА -->

		<!-- ГРУППЫ -->
		<?php
		if (GROUP_CHECK=='true')
		{
			$customers_statuses_array = os_get_customers_statuses();
			$customers_statuses_array = array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
			?>
			<div class="tab-pane" id="tab_groups">
				<?php
				for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++)
				{
					$checked = ($category['group_permission_'.$customers_statuses_array[$i]['id']] == 1) ? 'checked' : '';
					?>
					<div class="control-group">
						<div class="controls">
							<label class="checkbox"><input type="checkbox" name="groups[]" value="<?php echo $customers_statuses_array[$i]['id']; ?>" <?php echo $checked; ?>> <?php echo $customers_statuses_array[$i]['text']; ?></label>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		<?php } ?>
		<!-- /ГРУППЫ -->

		<!-- ПЛАГИНЫ -->
		<?php 
		if (isset($array['values']) && is_array($array['values']) )
		{
			$ip = 0;
			foreach ($array['values'] as $num => $value)
			{
				$ip++;
				echo '<div class="tab-pane" id="tab_plugin_'.$ip.'">';
				echo $value['tab_content'];
				echo '</div>';
			}
		}
		?>
		<!-- /ПЛАГИНЫ -->
	</div>
<hr>
	<div class="tcenter footer-btn">
		<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
		<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath); ?>"><?php echo BUTTON_CANCEL; ?></a>
	</div>
	<?php
		echo os_draw_hidden_field('categories_date_added', (($cInfo->date_added) ? $cInfo->date_added : date('Y-m-d'))).
		os_draw_hidden_field('parent_id', $cInfo->parent_id).
		os_draw_hidden_field('categories_id', $cInfo->categories_id);
	?> 
</form>