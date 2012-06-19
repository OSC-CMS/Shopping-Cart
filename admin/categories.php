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

require_once ('includes/top.php');

require_once (_FUNC_ADMIN.'wysiwyg_tiny.php');
require_once (_CLASS_ADMIN.FILENAME_IMAGEMANIPULATOR);
require_once (_CLASS_ADMIN.'categories.php');
require_once (_CLASS_ADMIN.'currencies.php');

$currencies = new currencies();
$catfunc = new categories();

if (@$_GET['function']) 
   {
	   switch ($_GET['function']) {
		   case 'delete' :
			os_db_query("DELETE FROM ".TABLE_PERSONAL_OFFERS.(int) $_GET['statusID']."
						                     WHERE products_id = '".(int) $_GET['pID']."'
						                     AND quantity    = '".(int) $_GET['quantity']."'");
			break;
	}
	
	set_categories_url_cache();
	set_category_cache();
	os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&action=new_product&pID='.(int) $_GET['pID']));
}

if (isset ($_POST['multi_status_on'])) {
	if (is_array($_POST['multi_categories'])) {
		foreach ($_POST['multi_categories'] AS $category_id) {
			$catfunc->set_category_recursive($category_id, '1');
		}
	}
	if (is_array($_POST['multi_products'])) {
		foreach ($_POST['multi_products'] AS $product_id) {
			$catfunc->set_product_status($product_id, '1');
		}
	}
	
	set_categories_url_cache();
	set_category_cache();
	os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.os_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
}

if (isset ($_POST['multi_status_off'])) {
	if (is_array($_POST['multi_categories'])) {
		foreach ($_POST['multi_categories'] AS $category_id) {
			$catfunc->set_category_recursive($category_id, "0");
		}
	}
	if (is_array($_POST['multi_products'])) {
		foreach ($_POST['multi_products'] AS $product_id) {
			$catfunc->set_product_status($product_id, "0");
		}
	}
	
	set_categories_url_cache();
	set_category_cache();
	os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.os_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
}

if (@$_GET['action']) 
{
	switch ($_GET['action']) {

		case 'setcflag' :
			if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
				if ($_GET['cID']) {
					$catfunc->set_category_recursive($_GET['cID'], $_GET['flag']);
				}
			}
			
			set_categories_url_cache();
	        set_category_cache();
			
			os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
			break;

		case 'setpflag' :
			if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
				if ($_GET['pID']) {
					$catfunc->set_product_status($_GET['pID'], $_GET['flag']);
				}
			}
			if (!isset($_GET['page'])) $_GET['page'] = 0;
			
			set_categories_url_cache();
	        set_category_cache();
			
			if ($_GET['pID']) {
				os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&pID='.$_GET['pID']));
			} else {
				os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&cID='.$_GET['cID']));
			}
			break;
			//EOB setpflag

      case 'setxml' :
        if (($_GET['flagxml'] == '0') || ($_GET['flagxml'] == '1')) {
          if ($_GET['pID']) {
            os_set_product_xml($_GET['pID'], $_GET['flagxml']);
          }
         }
			if (!isset($_GET['page'])) $_GET['page'] = 0;
         
		set_categories_url_cache();
	    set_category_cache();
		
        os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&page=' . $_GET['page'] . '&pID=' . $_GET['pID']));
        break;
		case 'setcxml' :
			if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
				if ($_GET['cID']) {
					$catfunc->set_category_xml_recursive($_GET['cID'], $_GET['flag']);
				}
			}
			
			set_categories_url_cache();
			set_category_cache();
			
			os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
			break;
			
		case 'setsflag' :
			if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
				if ($_GET['pID']) {
					$catfunc->set_product_startpage($_GET['pID'], $_GET['flag']);
//					if ($_GET['flag'] == '1') $catfunc->link_product($_GET['pID'], 0);
				}
			}
			
			set_categories_url_cache();
	        set_category_cache();
			if (!isset($_GET['page'])) $_GET['page'] = 0;
			if ($_GET['pID']) {
				os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&pID='.$_GET['pID']));
			} else {
				os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&page='.$_GET['page'].'&cID='.$_GET['pID']));
			}
			break;

		case 'update_category' :
		     
			$catfunc->insert_category($_POST, '', 'update');
			  set_categories_url_cache();
			  
			break;

		case 'insert_category' :

			$catfunc->insert_category($_POST, $current_category_id);
			set_categories_url_cache();
			set_category_cache();
			
			break;

		case 'update_product' :
		  
			$catfunc->insert_product($_POST, '', 'update');
			 set_products_url_cache();
			break;

		case 'insert_product' :
		     
			$catfunc->insert_product($_POST, $current_category_id);
			set_products_url_cache();
		break;

		case 'edit_crossselling' :
		
			$catfunc->edit_cross_sell($_GET);
			set_products_url_cache();
		    set_categories_url_cache();
		break;

		case 'multi_action_confirm' :
		  

			if (isset ($_POST['multi_delete_confirm'])) {
				if (is_array($_POST['multi_categories'])) {
					foreach ($_POST['multi_categories'] AS $category_id) {
						$catfunc->remove_categories($category_id);
					}
				}
				if (is_array($_POST['multi_products']) && is_array($_POST['multi_products_categories'])) {
					foreach ($_POST['multi_products'] AS $product_id) {
						$catfunc->delete_product($product_id, $_POST['multi_products_categories'][$product_id]);
					}
					   
				}
			}
			if (isset ($_POST['multi_move_confirm'])) {
				if (is_array($_POST['multi_categories']) && os_not_null($_POST['move_to_category_id'])) {
					foreach ($_POST['multi_categories'] AS $category_id) {
						$dest_category_id = os_db_prepare_input($_POST['move_to_category_id']);
						if ($category_id != $dest_category_id) {
							$catfunc->move_category($category_id, $dest_category_id);
						}
					}
				}
				if (is_array($_POST['multi_products']) && os_not_null($_POST['move_to_category_id']) && os_not_null($_POST['src_category_id'])) {
					foreach ($_POST['multi_products'] AS $product_id) {
						$product_id = os_db_prepare_input($product_id);
						$src_category_id = os_db_prepare_input($_POST['src_category_id']);
						$dest_category_id = os_db_prepare_input($_POST['move_to_category_id']);
						$catfunc->move_product($product_id, $src_category_id, $dest_category_id);
					}
				}
				set_products_url_cache();
				set_categories_url_cache();
	            set_category_cache();
				os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$dest_category_id.'&'.os_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
			}
			if (isset ($_POST['multi_copy_confirm'])) {
				if (is_array($_POST['multi_categories']) && (is_array(@$_POST['dest_cat_ids']) || os_not_null($_POST['dest_category_id']))) {
					$_SESSION['copied'] = array ();
					foreach ($_POST['multi_categories'] AS $category_id) {
						if (is_array(@$_POST['dest_cat_ids'])) {
							foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
								if ($_POST['copy_as'] == 'link') {
									$catfunc->copy_category($category_id, $dest_category_id, 'link');
								}
								elseif ($_POST['copy_as'] == 'duplicate') {
									$catfunc->copy_category($category_id, $dest_category_id, 'duplicate');
								} else {
									$messageStack->add_session('Copy type not specified.', 'error');
								}
							}
						}
						elseif (os_not_null($_POST['dest_category_id'])) {
							if ($_POST['copy_as'] == 'link') {
								$catfunc->copy_category($category_id, $dest_category_id, 'link');
							}
							elseif (@$_POST['copy_as'] == 'duplicate') {
								$catfunc->copy_category($category_id, @$dest_category_id, 'duplicate');
							} else {
								$messageStack->add_session('Copy type not specified.', 'error');
							}
						}
					}
					unset ($_SESSION['copied']);
				}
				if (is_array(@$_POST['multi_products']) && (is_array($_POST['dest_cat_ids']) || os_not_null($_POST['dest_category_id']))) {
					foreach ($_POST['multi_products'] AS $product_id) {
						$product_id = os_db_prepare_input($product_id);
						if (is_array($_POST['dest_cat_ids'])) {
							foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
								$dest_category_id = os_db_prepare_input($dest_category_id);
								if ($_POST['copy_as'] == 'link') {
									$catfunc->link_product($product_id, $dest_category_id);
								}
								elseif ($_POST['copy_as'] == 'duplicate') {
									$catfunc->duplicate_product($product_id, $dest_category_id);
								} else {
									$messageStack->add_session('Copy type not specified.', 'error');
								}
							}
						}
						elseif (os_not_null($_POST['dest_category_id'])) {
							$dest_category_id = os_db_prepare_input($_POST['dest_category_id']);
							if ($_POST['copy_as'] == 'link') {
								$catfunc->link_product($product_id, $dest_category_id);
							}
							elseif ($_POST['copy_as'] == 'duplicate') {
								$catfunc->duplicate_product($product_id, $dest_category_id);
							} else {
								$messageStack->add_session('Copy type not specified.', 'error');
							}
						}
					}
				}
				
				set_products_url_cache();
				set_categories_url_cache();
	            set_category_cache();
				os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.@$dest_category_id.'&'.os_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
			}			

			os_redirect(os_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.os_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
		    set_products_url_cache();
		    set_categories_url_cache();
			break;
	}
}
if (is_dir(dir_path('images'))) 
{
	if (!is_writeable(dir_path('images')))
	{
		$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE.' '. dir_path('images'), 'error');
	}	
} 
else 
{
	$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST.' '.dir_path('images'), 'error');
}

	$query = os_db_query("SELECT code FROM ".TABLE_LANGUAGES." WHERE languages_id='".$_SESSION['languages_id']."'");
	$data = os_db_fetch_array($query);
	$languages = os_get_languages();
	if (@$_GET['action'] == 'new_category' || @$_GET['action'] == 'edit_category') {
		for ($i = 0; $i < sizeof($languages); $i ++) {
			add_action('head_admin', os_wysiwyg_tiny('categories_description', $data['code'], $languages[$i]['id']));
		}
	}
	if (@$_GET['action'] == 'new_product') {
		for ($i = 0; $i < sizeof($languages); $i ++) {
			add_action('head_admin', os_wysiwyg_tiny('products_description', $data['code'], $languages[$i]['id']));
			add_action('head_admin', os_wysiwyg_tiny('products_short_description', $data['code'], $languages[$i]['id']));
		}
	} 

    add_action('head_admin', 'head_categories');

    if(isset($_GET['action']) && ($_GET['action']=='new_product' or $_GET['action']=='new_category' or $_GET['action']=='edit_category'))
	{
	    add_action('head_admin', 'head_new_product');
	}
	
	function head_categories ()
	{
		_e('<script type="text/javascript" src="includes/javascript/categories.js"></script>');
		
	    $query = os_db_query("SELECT code FROM ".TABLE_LANGUAGES." WHERE languages_id='".$_SESSION['languages_id']."'");
	    $data = os_db_fetch_array($query);
	    $languages = os_get_languages();
		
	    if (@$_GET['action'] == 'new_category' || @$_GET['action'] == 'edit_category') 
		{
		    for ($i = 0; $i < sizeof($languages); $i ++) 
			{
			    echo os_wysiwyg_tiny('categories_description', $data['code'], $languages[$i]['id']);
		    }
	    }
		
	    if (@$_GET['action'] == 'new_product') 
		{
		    for ($i = 0; $i < sizeof($languages); $i ++) 
			{
			    echo os_wysiwyg_tiny('products_description', $data['code'], $languages[$i]['id']);
			   // echo os_wysiwyg_tiny('products_short_description', $data['code'], $languages[$i]['id']);
		    }
	    } 
	}
	
	function head_new_product()
	{
	   _e('<link href="includes/javascript/date-picker/css/datepicker.css" rel="stylesheet" type="text/css" />');
       _e('<script type="text/javascript" src="includes/javascript/date-picker/js/datepicker.js"></script>');
       _e('<script type="text/javascript" src="includes/javascript/modified.js"></script>');
	   
       if (ENABLE_TABS == 'true') 
	   {
            _e('<script type="text/javascript" src="includes/javascript/tabber.js"></script>');
            _e('<link rel="stylesheet" href="includes/javascript/tabber.css" TYPE="text/css" MEDIA="screen">');
            _e('<link rel="stylesheet" href="includes/javascript/tabber-print.css" TYPE="text/css" MEDIA="print">');
       } 
	}
	
	$main->head();
    $main->top_menu(); 
?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

                    <?php
if (isset($_GET['action']) && ($_GET['action'] == 'new_category' || $_GET['action'] == 'edit_category')) 
{
	include (_MODULES_ADMIN.'new_category.php');
}
elseif (isset($_GET['action']) && $_GET['action'] == 'new_product') {
	include (_MODULES_ADMIN.'new_product.php');
}
elseif (isset($_GET['action']) && $_GET['action'] == 'edit_crossselling') {
	include (_MODULES_ADMIN.'cross_selling.php');
} else {
	if (!$cPath) { $cPath = '0'; }
	include (_MODULES_ADMIN.'categories_view.php');
}
?>
				</table></td>
			</tr>
		</table>

<?php $main->bottom(); ?>