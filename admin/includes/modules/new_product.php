<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

if (($_GET['pID']) && (!$_POST)) 
{
	$product_query = os_db_query("select *, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = '".(int) $_GET['pID']."' and p.products_id = pd.products_id and pd.language_id = '".$_SESSION['languages_id']."'");

	$product = os_db_fetch_array($product_query);
	$pInfo = new objectInfo($product);

	$products_extra_fields_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." WHERE products_id=".(int)$_GET['pID']);
	while ($products_extra_fields = os_db_fetch_array($products_extra_fields_query))
	{
		$extra_field[$products_extra_fields['products_extra_fields_id']] = $products_extra_fields['products_extra_fields_value'];
	}
	$extra_field_array=array('extra_field'=>@$extra_field);
	$pInfo->objectInfo($extra_field_array);
}
elseif ($_POST)
{
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
	$pInfo->products_reviews = $_POST['products_reviews'];
	$pInfo->products_search = $_POST['products_search'];
	$products_startpage_sort = $_POST['products_startpage_sort'];
} else {
	$pInfo = new objectInfo(array());
}

$manufacturers_array = array (array ('id' => '', 'text' => TEXT_NONE));
$manufacturers_query = os_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS." order by manufacturers_name");
while ($manufacturers = os_db_fetch_array($manufacturers_query))
{
	$manufacturers_array[] = array ('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
}

$vpe_array = array (array ('id' => '', 'text' => TEXT_NONE));
$vpe_query = os_db_query("select products_vpe_id, products_vpe_name from ".TABLE_PRODUCTS_VPE." WHERE language_id='".$_SESSION['languages_id']."' order by products_vpe_name");
while ($vpe = os_db_fetch_array($vpe_query))
{
	$vpe_array[] = array ('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
}

$tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
$tax_class_query = os_db_query("select tax_class_id, tax_class_title from ".TABLE_TAX_CLASS." order by tax_class_title");
while ($tax_class = os_db_fetch_array($tax_class_query))
{
	$tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
}
$shipping_statuses = array ();
$shipping_statuses = os_get_shipping_status();
$languages = os_get_languages();

$form_action = ($_GET['pID']) ? 'update_product' : 'insert_product';

$breadcrumb->add(os_output_generated_category_path(@$current_category_id), FILENAME_CATEGORIES.'?cPath='.$_GET['cPath']);
$breadcrumb->add(os_get_products_name(@$pInfo->products_id, @$languages[$i]['id']));

$main->head();
$main->top_menu();

$fsk18_array = array(array('id' => 0, 'text' => NO), array('id' => 1, 'text' => YES));
$ymlAvailable = array(
	array('id' => 0, 'text' => TEXT_YANDEX_MARKET_AVAILABLE_0),
	array('id' => 1, 'text' => TEXT_YANDEX_MARKET_AVAILABLE_1),
	array('id' => 2, 'text' => TEXT_YANDEX_MARKET_AVAILABLE_2)
);
?>

<?php
echo os_draw_form('new_product', FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].(isset($_GET['page']) ? '&page='.$_GET['page'] : '').'&action='.$form_action, 'post', 'enctype="multipart/form-data" cf="true"');
echo os_draw_hidden_field('products_date_added', (($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
echo os_draw_hidden_field('products_id', $pInfo->products_id);
?>

	<div class="btn-group pull-right">
		<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
		<a class="btn" href="<?php echo os_href_link(FILENAME_NEW_ATTRIBUTES, 'action=edit'.'&current_product_id='.$_GET['pID'].'&cpath='.$cPath); ?>"><?php echo BUTTON_EDIT_ATTRIBUTES; ?></a>
		<a class="btn" href="<?php echo os_href_link(FILENAME_CATEGORIES, 'action=edit_crossselling'.'&current_product_id='.$_GET['pID'].'&cpath='.$cPath); ?>"><?php echo BUTTON_EDIT_CROSS_SELLING; ?></a>
	</div>

	<ul class="nav nav-tabs" id="productTabs">
		<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
		<li <?php echo ($i == 0) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $languages[$i]['id']; ?>"><?php echo $languages[$i]['name']; ?></a></li>
		<?php } ?>
		<li><a href="#tab_info"><?php echo TEXT_PRODUCTS_DATA; ?></a></li>
		<li><a href="#tab_price"><?php echo HEADING_PRICES_OPTIONS; ?></a></li>
		<li><a href="#tab_images"><?php echo HEADING_PRODUCT_IMAGES; ?></a></li>
		<li><a href="#tab_extra_fields"><?php echo HEADING_PRODUCT_EXTRA_FIELDS;?></a></li>
		<li><a href="#tab_bundle"><?php echo TABLE_HEADING_BUNDLE; ?></a></li>
		<li><a href="#tab_files"><?php echo TABLE_HEADING_CONTENT_FILES; ?></a></li>
		<?php if (GROUP_CHECK=='true') { ?>
		<li><a href="#tab_groups"><?php echo ENTRY_CUSTOMERS_ACCESS; ?></a></li>
		<?php } ?>
		<?php
		$array = array();

		$array['param'] = array('products_id' => @$_GET['pID'], 'category_id'=>@$_GET['cPath'] );

		$array = apply_filter('news_product_add_tabs', $array);

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
	</ul>

	<div class="tab-content">

		<!-- ТОВАРЫ -->
		<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
		{
			if($languages[$i]['status'] == 1)
			{
				if (SEO_URL_PRODUCT_GENERATOR == 'true' && empty($pInfo->products_page_url))
					$prodParams = array('id' => 'products_name_'.$languages[$i]['id'], 'class' => 'input-block-level', 'onKeyPress' => 'onchange_products_page_url()', 'onChange' =>'onchange_products_page_url()');
				else
					$prodParams = array('id' => 'products_name_'.$languages[$i]['id'], 'class' => 'input-block-level');
		?>
		<div class="tab-pane <?php echo ($i == 0) ? 'active' : ''; ?>" id="tab_lang_<?php echo $languages[$i]['id']; ?>">
			<div class="pt10">
				<div class="control-group">
					<label class="control-label" for="products_name_<?php echo $languages[$i]['id']; ?>"><?php echo TEXT_PRODUCTS_NAME; ?></label>
					<div class="controls">
						<?php
						echo $cartet->html->input_text(
							'products_name['.$languages[$i]['id'].']',
							htmlspecialchars(os_get_products_name($pInfo->products_id, $languages[$i]['id'])),
							$prodParams
						);
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for=""><?php echo TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></label>
					<div class="controls">
						<?php
						echo $cartet->html->textarea(
							'products_short_description_'.$languages[$i]['id'],
							(($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : os_get_products_short_description($pInfo->products_id, $languages[$i]['id'])),
							array('id' => 'products_short_description_'.$languages[$i]['id'], 'class' => 'input-block-level textarea_small')
						);
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for=""><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></label>
					<div class="controls">
						<?php
						echo $cartet->html->textarea(
							'products_description_'.$languages[$i]['id'],
							(($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : os_get_products_description($pInfo->products_id, $languages[$i]['id'])),
							array('id' => 'products_description_'.$languages[$i]['id'], 'class' => 'input-block-level textarea_big')
						);
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for=""><?php echo TEXT_PRODUCTS_KEYWORDS; ?></label>
					<div class="controls">
						<?php
						echo $cartet->html->input_text(
							'products_keywords['.$languages[$i]['id'].']',
							(($products_keywords[$languages[$i]['id']]) ? stripslashes($products_keywords[$languages[$i]['id']]) : os_get_products_keywords($pInfo->products_id, $languages[$i]['id'])),
							array('class' => 'input-block-level')
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
							'products_meta_title['.$languages[$i]['id'].']',
							(($products_meta_title[$languages[$i]['id']]) ? stripslashes($products_meta_title[$languages[$i]['id']]) : os_get_products_meta_title($pInfo->products_id, $languages[$i]['id'])),
							array('class' => 'input-block-level')
						);
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for=""><?php echo TEXT_META_DESCRIPTION; ?></label>
					<div class="controls">
						<?php
						echo $cartet->html->input_text(
							'products_meta_description['.$languages[$i]['id'].']',
							(($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : os_get_products_meta_description($pInfo->products_id, $languages[$i]['id'])),
							array('class' => 'input-block-level')
						);
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for=""><?php echo TEXT_META_KEYWORDS; ?></label>
					<div class="controls">
						<?php
						echo $cartet->html->input_text(
							'products_meta_keywords['.$languages[$i]['id'].']',
							(($products_meta_keywords[$languages[$i]['id']]) ? stripslashes($products_meta_keywords[$languages[$i]['id']]) : os_get_products_meta_keywords($pInfo->products_id, $languages[$i]['id'])),
							array('class' => 'input-block-level')
						);
						?>
					</div>
				</div>

				<hr>

				<div class="control-group">
					<label class="control-label" for=""><?php echo TEXT_PRODUCTS_URL; ?></label>
					<div class="controls">
						<?php
						echo $cartet->html->input_text(
							'products_url['.$languages[$i]['id'].']',
							(($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : os_get_products_url($pInfo->products_id, $languages[$i]['id'])),
							array('class' => 'input-block-level')
						);
						?>
						<span class="help-block"><?php echo TEXT_PRODUCTS_URL_WITHOUT_HTTP; ?></span>
					</div>
				</div>
			</div>
		</div>
		<?php }} ?>
		<!-- /ТОВАРЫ -->

		<!-- ДОПОЛНИТЕЛЬНО -->
		<div class="tab-pane" id="tab_info">
			<div class="pt10">

				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_PAGE_URL; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_page_url',
									$pInfo->products_page_url,
									array('class' => 'input-block-level', 'id' => 'products_page_url')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_QUANTITY; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_quantity',
									$pInfo->products_quantity,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_WEIGHT; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_weight',
									$pInfo->products_weight,
									array('class' => 'input-block-level')
								);
								?>
								<span class="help-block"><?php echo TEXT_PRODUCTS_WEIGHT_INFO; ?></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_MODEL; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_model',
									$pInfo->products_model,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_EAN; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_ean',
									$pInfo->products_ean,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?>
								<span class="help-block"><a class="btn btn-mini btn-info" href="<?php echo os_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL', false); ?>"><?php echo TEXT_EDIT; ?></a></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_FSK18; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('fsk18', $fsk18_array, $pInfo->products_fsk18); ?>
							</div>
						</div>
						<?php if (ACTIVATE_SHIPPING_STATUS=='true') { ?>
						<div class="control-group">
							<label class="control-label" for=""><?php echo BOX_SHIPPING_STATUS; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('shipping_status', $shipping_statuses, $pInfo->products_shippingtime); ?>
								<span class="help-block"><a class="btn btn-mini btn-info" href="<?php echo os_href_link(FILENAME_SHIPPING_STATUS, '', 'NONSSL', false); ?>"><?php echo TEXT_EDIT; ?></a></span>
							</div>
						</div>
						<?php } ?>

						<?php
						$files = array();
						foreach (array('product_info', 'product_options') as $key)
						{
							if ($dir = opendir(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/'.$key.'/'))
							{
								while (($file = readdir($dir)) !== false)
								{
									if (is_file(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/'.$key.'/'.$file) and ($file != "index.html"))
									{
										$files[$key][] = array ('id' => $file, 'text' => $file);
									}
								}
								closedir($dir);
							}

							// set default value in dropdown!
							if ($content['content_file'] == '')
								$files[$key] = array_merge(array(array('id' => 'default', 'text' => TEXT_SELECT)), $files[$key]);
							else
								$files[$key] = array_merge(array(array('id' => 'default', 'text' => TEXT_NO_FILE)), $files[$key]);
						}
						?>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_CHOOSE_INFO_TEMPLATE; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('info_template', $files['product_info'], $pInfo->product_template); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_CHOOSE_OPTIONS_TEMPLATE; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('options_template', $files['product_options'], $pInfo->options_template); ?>
							</div>
						</div>
					</div>

					<div class="span6">
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_STATUS; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('products_status', $fsk18_array, (($pInfo->products_status != '') ? $pInfo->products_status : 1)); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?> <small>(YYYY-MM-DD)</small></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_date_available',
									$pInfo->products_date_available,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_STARTPAGE; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('products_startpage', $fsk18_array, (($pInfo->products_startpage != '') ? $pInfo->products_startpage : 0)); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_STARTPAGE_SORT; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_startpage_sort',
									$pInfo->products_startpage_sort,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_SORT; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_sort',
									$pInfo->products_sort,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_REVIEWS; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('products_reviews', $fsk18_array, (($pInfo->products_reviews != '') ? $pInfo->products_reviews : 1)); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_SEARCH; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('products_search', $fsk18_array, (($pInfo->products_search != '') ? $pInfo->products_search : 0)); ?>
							</div>
						</div>

						<legend><?php echo TEXT_YANDEX_MARKET; ?></legend>

						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_TO_XML; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('products_to_xml', $fsk18_array, (($pInfo->products_to_xml != '') ? $pInfo->products_to_xml : 1)); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_YANDEX_MARKET_AVAILABLE; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('yml_available', $ymlAvailable, (($pInfo->yml_available != '') ? $pInfo->yml_available : 1)); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_YANDEX_MARKET_BID; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'yml_bid',
									$pInfo->yml_bid,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_YANDEX_MARKET_CBID; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'yml_cbid',
									$pInfo->yml_cbid,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /ДОПОЛНИТЕЛЬНО -->

		<!-- ЦЕНЫ -->
		<div class="tab-pane" id="tab_price">
			<div class="pt10">

				<div class="row-fluid">
					<div class="span6">
						<?php include(_MODULES_ADMIN.'group_prices.php'); ?>
					</div>
					<div class="span6">
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_DISCOUNT_ALLOWED; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_discount_allowed',
									($pInfo->products_discount_allowed == '' ? 100 : $pInfo->products_discount_allowed),
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id); ?>
							</div>
						</div>
						<hr>
						<div class="control-group">
							<label class="control-label" for=""></label>
							<div class="controls">
								<label class="checkbox">
									<?php echo os_draw_selection_field('products_vpe_status', 'checkbox', '1', $pInfo->products_vpe_status == 1 ? true : false); ?> <?php echo TEXT_PRODUCTS_VPE_VISIBLE; ?>
								</label>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_VPE_VALUE; ?></label>
							<div class="controls">
								<?php
								echo $cartet->html->input_text(
									'products_vpe_value',
									$pInfo->products_vpe_value,
									array('class' => 'input-block-level')
								);
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TEXT_PRODUCTS_VPE; ?></label>
							<div class="controls">
								<?php echo os_draw_pull_down_menu('products_vpe', $vpe_array, $pInfo->products_vpe='' ?  DEFAULT_PRODUCTS_VPE_ID : $pInfo->products_vpe); ?>
								<span class="help-block"><a class="btn btn-mini btn-info" href="<?php echo os_href_link(FILENAME_PRODUCTS_VPE, '', 'NONSSL', false); ?>"><?php echo TEXT_EDIT; ?></a></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /ЦЕНЫ -->

		<!-- КАРТИНКИ -->
		<div class="tab-pane" id="tab_images">
			<div class="pt10">

				<a class="btn btn-mini" href="javascript:;" id="image_upload">С компьютера</a>
				<a class="btn btn-mini" href="javascript:;" id="image_url">Из интернета</a>
				<a class="btn btn-mini" href="javascript:;" id="image_auto">Автоматически</a>
				<a class="btn btn-mini" href="javascript:;" id="checkAllImages">Выбрать все</a>

				<hr>

				<?php if (!$pInfo->products_image) { ?><div class="alert alert-error">Главная картинка не установлена!</div><?php } ?>

				<div id="images"></div>

				<input type="hidden" name="main_image" value="<?php echo $pInfo->products_image; ?>">

				<div class="productImages" id="tableImagesList">
					<ul>
						<?php if ($pInfo->products_image) { ?>
							<li>
								<div class="title"></div>
								<span class="delete"><input type="checkbox" name="image_delete[]" value="<?php echo $pInfo->products_image; ?>"></span>
								<div class="product-image-thumb">
									<div class="product-image"><a href="<?php echo http_path('images_popup').$pInfo->products_image; ?>" target="_blank"><?php echo os_image(http_path('images_thumbnail').$pInfo->products_image, TEXT_STANDART_IMAGE); ?></a></div>
								</div>
							</li>
						<?php } ?>
						<?php $mo_images = os_get_products_mo_images($pInfo->products_id);?>
						<?php if ($mo_images) { foreach($mo_images AS $img) { ?>
							<li>
								<a class="btn btn-mini ajax-action ajax-change-main-image" data-action="products_setMainImage_get&products_id=<?php echo $pInfo->products_id; ?>&image_id=<?php echo $img["image_id"]; ?>" href="javascript:;" title="Установить главной картинкой"><i class="icon-pushpin"></i></a>
								<div class="title">
									<a href="#" class="ajax_editable" data-action="products_saveImageValue" data-type="text" data-value="<?php echo $img['text']; ?>" data-pk="<?php echo $img["image_id"]; ?>" data-name="text"><?php echo $img['text']; ?></a>
								</div>
								
								<span class="delete"><input type="checkbox" name="images_delete[]" value="<?php echo $img["image_name"]; ?>"></span>
								<div class="product-image-thumb">
									<div class="product-image"><a href="<?php echo http_path('images_popup').$img["image_name"]; ?>" target="_blank"><?php echo os_image(http_path('images_thumbnail').$img["image_name"], TEXT_STANDART_IMAGE.' '.($i +1)); ?></a></div>
								</div>
							</li>
						<?php }} ?>
					</ul>
					<div class="clear"></div>
				</div>

			</div>
		</div>
		<!-- /КАРТИНКИ -->

		<!-- ДОП. ПОЛЯ -->
		<div class="tab-pane" id="tab_extra_fields">
			<div class="pt10">

				<a class="btn btn-mini" href="#" id="extraFieldsData">Подобрать автоматически</a>

				<hr>

				<?php
				$extra_fields_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_EXTRA_FIELDS." ORDER BY products_extra_fields_order");

				if (os_db_num_rows($extra_fields_query) > 0)
				{
					while ($extra_fields = os_db_fetch_array($extra_fields_query,true))
					{
						$extra_fields_data[$extra_fields['products_extra_fields_group']][] = $extra_fields;
					}

					$groupsDescQuery = os_db_query("
					SELECT
						*
					FROM
						".DB_PREFIX."products_extra_fields_groups g
							LEFT JOIN ".DB_PREFIX."products_extra_fields_groups_desc d ON (g.extra_fields_groups_id = d.extra_fields_groups_id AND d.extra_fields_groups_languages_id = '".(int)$_SESSION['languages_id']."')
					WHERE
						g.extra_fields_groups_status = 1
					ORDER BY
						g.extra_fields_groups_order ASC
					");
					$groupDescEdit = array();
					if (os_db_num_rows($groupsDescQuery) > 0)
					{
						while ($groups = os_db_fetch_array($groupsDescQuery))
						{
							$groupDescEdit[$groups['extra_fields_groups_id']] = $groups;
						}
					}

					$efResult = array();
					foreach($groupDescEdit AS $gId => $gValue)
					{
						foreach ($extra_fields_data as $fGId => $fValue)
						{
							if ($gId == $fGId)
							{
								$efResult[$gId] = $gValue;
								$efResult[$gId]['values'] = $fValue;
							}
						}
					}
				}
				?>

				<?php if ($efResult) { ?>
				<ul class="nav nav-tabs default-tabs">
					<?php $i = 0; foreach ($efResult as $group) { $i++; ?>
						<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_group_<?php echo $group['extra_fields_groups_desc_id']; ?>"><?php echo $group['extra_fields_groups_name']; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>

				<?php if ($efResult) { ?>
				<div class="tab-content">
					<?php $i = 0; foreach ($efResult as $group) { $i++; ?>
						<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_group_<?php echo $group['extra_fields_groups_desc_id']; ?>">

							<table class="table table-condensed table-big-list">
								<?php
								foreach ($group['values'] AS $extra_fields)
								{
									?>
									<tr>
										<td class="span5"><span class="ef_name"><?php echo $extra_fields['products_extra_fields_name']; ?></span></td>
										<td>
											<?php echo $cartet->html->input_text(
												'extra_field['.$extra_fields['products_extra_fields_id'].']',
												$pInfo->extra_field[$extra_fields['products_extra_fields_id']],
												array('class' => 'input-block-level')
											); ?>
										</td>
									</tr>
								<?php
								}
								?>
							</table>

						</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php
				$getGroupsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."products_extra_fields_groups g LEFT JOIN ".DB_PREFIX."products_extra_fields_groups_desc d ON (g.extra_fields_groups_id = d.extra_fields_groups_id AND d.extra_fields_groups_languages_id = '".(int)$_SESSION['languages_id']."') ORDER BY g.extra_fields_groups_order");
				$groupsList = array();
				if (os_db_num_rows($getGroupsQuery) > 0)
				{
					while ($getGroup = os_db_fetch_array($getGroupsQuery))
					{
						$groupsList[] = array(
							'id' => $getGroup['extra_fields_groups_id'],
							'text' => $getGroup['extra_fields_groups_name']
						);
					}
				}
				?>

				<table class="table table-condensed table-big-list add_ef">
						<tr id="addEf">
							<td class="span1"><?php echo os_draw_pull_down_menu('efGroup[]', $groupsList); ?></td>
							<td><span class="ef_name"><input class="input-block-level" type="text" name="efName[]"></span></td>
							<td>
								<input class="input-block-level" type="text" name="efValue[]" />
							</td>
						</tr>
				</table>

				<hr>

				<a class="btn btn-mini" id="add_new_ef" href="javascript:;">Добавить новое поле</a>
			</div>
		</div>
		<!-- /ДОП. ПОЛЯ -->

		<!-- НАБОРЫ -->
		<div class="tab-pane" id="tab_bundle">
			<div class="pt10">
				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<?php echo os_draw_selection_field('products_bundle', 'checkbox', '1',$pInfo->products_bundle == 1 ? true : false); ?> <?php echo TABLE_USE_BUNDLE; ?>
						</label>
					</div>
				</div>

				<div id="bundles-block">
					<div id="bundles">
						<?php
						// Bundle
						if (isset($pInfo->products_bundle) && $pInfo->products_bundle == '1')
						{
							echo '<table class="table table-striped table-bordered table-condensed">';
							echo '<thead>';
							echo '<tr>';
								echo '<th class="br bbt-head" width="40%">'.TABLE_USE_BUNDLE_PRODUCT_NAME.'</th>';
								echo '<th class="br bbt-head" width="25%">'.TABLE_USE_BUNDLE_PRODUCT_ID.'</th>';
								echo '<th class="br bbt-head" width="25%">'.TABLE_USE_BUNDLE_PRODUCT_QTY.'</th>';
								echo '<th class="bbt-head" width="10%"></th>';
							echo '</tr>';
							echo '</thead>';
							// this product is a bundle so get contents data
							$bundle_query = os_db_query("SELECT pb.subproduct_id, pb.subproduct_qty, pd.products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." pd INNER JOIN ".DB_PREFIX."products_bundles pb ON pb.subproduct_id=pd.products_id WHERE pb.bundle_id = ".(int)$pInfo->products_id." and pd.language_id = '".(int)$_SESSION['languages_id']."'");
							while ($bundle_contents = os_db_fetch_array($bundle_query))
							{
								?>
								<tr>
									<td width="40%"><?php echo $bundle_contents['products_name']; ?></td>
									<td width="25%"><?php echo $cartet->html->input_text('bundles[id][]', $bundle_contents['subproduct_id'], array('class' => 'input-block-level')); ?></td>
									<td width="25%"><?php echo $cartet->html->input_text('bundles[qty][]', $bundle_contents['subproduct_qty'], array('class' => 'input-block-level')); ?></td>
									<td width="10%"><a class="btn btn-mini btn-danger del_bundles" href="javascript:;"><i class="icon-remove icon-white"></i> <?php echo TABLE_USE_BUNDLE_DEL; ?></a></td>
								</tr>
								<?php
							}
							echo '</table>';
						}
						// End of Bundle
						?>
					</div>

					<div class="alert alert-info"><?php echo TABLE_USE_BUNDLE_QTY_DESC; ?></div>

					<table class="table table-striped table-bordered table-condensed" border="0" cellspacing="0" cellpadding="0" id="new_bundles" style='display:none;'>
						<tr>
							<td width="40%"></td>
							<td width="25%"><?php echo $cartet->html->input_text('bundles[id][]', '', array('class' => 'input-block-level')); ?></td>
							<td width="25%"><?php echo $cartet->html->input_text('bundles[qty][]', '', array('class' => 'input-block-level')); ?></td>
							<td width="10%"><a class="btn btn-mini btn-danger del_bundles_new" href="javascript:;"><i class="icon-remove icon-white"></i> <?php echo TABLE_USE_BUNDLE_DEL; ?></a></td>
						</tr>
					</table>
					<a class="btn btn-mini btn-success" href="javascript:;" id="add-new-bundles"><?php echo TABLE_USE_BUNDLE_ADD; ?></a>
				</div>
			</div>
		</div>
		<!-- /НАБОРЫ -->

		<!-- ФАЙЛЫ -->
		<div class="tab-pane" id="tab_files">
			<div class="pt10">

<?php
$content_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id = '".(int)$_GET['pID']."' order by content_name");

?>


<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_PRODUCTS_ID; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_LANGUAGE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_NAME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_FILE; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_LINK; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_ACTION; ?></th>
		</tr>
	</thead>
<?php
while ($content_data = os_db_fetch_array($content_query))
{
	?>
	<tr>
		<td><?php echo  $content_data['content_id']; ?></td>
		<?php
		//if ($content_data['file'] != '')
		//echo os_image(http_path('catalog').'admin/images/icons/icon_'.str_replace('.','',strstr($content_data['file'],'.')).'.gif');
		//else
		//echo os_image(http_path('catalog').'admin/images/icons/icon_link.gif');

		for ($xx = 0, $zz = sizeof($languages); $xx<$zz; $xx++)
		{
			if ($languages[$xx]['id'] == $content_data['languages_id'])
			{
				$lang_dir = $languages[$xx]['directory'];
				break;
			}
		}
		?>
		<td><?php echo os_image(http_path_admin('icons').'lang/'.$lang_dir.'.gif'); ?></td>
		<td><span class="bold"><?php echo $content_data['content_name']; ?></span><br /><?php echo $content_data['file_comment']; ?></td>
		<td><span class="bold"><?php echo $content_data['content_file']; ?></span><br />
			<?php echo TABLE_HEADING_CONTENT_FILESIZE; ?> <?php echo os_filesize($content_data['content_file']); ?> | <?php echo TABLE_HEADING_CONTENT_HITS; ?> <?php echo $content_data['content_read']; ?>
		</td>
		<td><?php
			if ($content_data['content_link']!='')
			{
				echo '<a href="'.$content_data['content_link'].'" target="new">'.$content_data['content_link'].'</a>';
			}
			?></td>
		<td width="100">
			<div class="btn-group pull-right">
				<?php if (preg_match('/.gif/i',$content_data['content_file']) or preg_match('/.jpg/i',$content_data['content_file']) or preg_match('/.png/i',$content_data['content_file'])
					or preg_match('/.html/i',$content_data['content_file']) or preg_match('/.htm/i',$content_data['content_file']) or
					preg_match('/.txti/',$content_data['content_file']) or preg_match('/.bmp/i',$content_data['content_file'])
				) { ?>
					<a class="btn btn-mini" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_CONTENT_PREVIEW,'pID=media&coID='.$content_data['content_id']); ?>', 'popup', 'toolbar=0, width=640, height=600')" title="<?php echo TEXT_PREVIEW; ?>"><i class="icon-eye-open"></i></a>
				<?php } ?>
				<a class="btn btn-mini" target="_blank" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER, 'act=products&action=edit_products&coID='.$content_data['content_id']); ?>" title="<?php echo TEXT_EDIT; ?>"><i class="icon-edit"></i></a>
				<a class="btn btn-mini" href="#" data-action="content_deleteProduct" data-remove-parent="tr" data-id="<?php echo $content_data['content_id']; ?>" data-confirm="<?php echo CONFIRM_DELETE_PRODUCT; ?>" title="<?php echo TEXT_DELETE; ?>"><i class="icon-trash"></i></a>
			</div>
		</td>
	</tr>
<?php
}
?>
	</table>

			</div>
		</div>
		<!-- /ФАЙЛЫ -->

		<!-- ГРУППЫ -->
		<?php
		if (GROUP_CHECK=='true')
		{
			$customers_statuses_array = os_get_customers_statuses();
			$customers_statuses_array = array_merge(array (array ('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
			?>
			<div class="tab-pane" id="tab_groups">
				<?php
				for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++)
				{
					$code = '$id=$pInfo->group_permission_'.$customers_statuses_array[$i]['id'].';';
					eval ($code);
					$checked = ($id==1) ? 'checked' : '';
					?>
					<div class="control-group">
						<div class="controls">
							<label class="checkbox"><input type="checkbox" name="groups[]" value="<?php echo $customers_statuses_array[$i]['id']; ?>" <?php echo $checked; ?> > <?php echo $customers_statuses_array[$i]['text']; ?></label>
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
				echo '<div class="tab-pane" id="tab_plugin_'.$ip.'"><div class="pt10">';
				echo $value['tab_content'];
				echo '</div></div>';
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

</form>

</div>

