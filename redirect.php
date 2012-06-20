<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');
require_once (_FUNC.'include.php');

do_action('redirect');

switch ($_GET['action']) 
{
	case 'product' :
		if (isset ($_GET['id'])) {
			$product_query = os_db_query("SELECT products_url FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id='".(int) $_GET['id']."' and language_id='".(int) $_SESSION['languages_id']."'");

			if (os_db_num_rows($product_query)) {
				$product = os_db_fetch_array($product_query);

				os_redirect('http://'.$product['products_url']);
			} else {
				os_redirect(os_href_link(FILENAME_DEFAULT));
			}
		} else {
			os_redirect(os_href_link(FILENAME_DEFAULT));
		}
		break;

	case 'manufacturer' :
		if (isset ($_GET['manufacturers_id'])) {
			$manufacturer_query = os_db_query("select manufacturers_url from ".TABLE_MANUFACTURERS_INFO." where manufacturers_id = '".(int) $_GET['manufacturers_id']."' and languages_id = '".(int) $_SESSION['languages_id']."'");
			if (!os_db_num_rows($manufacturer_query)) {
				// no url exists for the selected language, lets use the default language then
				$manufacturer_query = os_db_query("select mi.languages_id, mi.manufacturers_url from ".TABLE_MANUFACTURERS_INFO." mi, ".TABLE_LANGUAGES." l where mi.manufacturers_id = '".(int) $_GET['manufacturers_id']."' and mi.languages_id = l.languages_id and l.code = '".DEFAULT_LANGUAGE."'");
				if (!os_db_num_rows($manufacturer_query)) {
					// no url exists, return to the site
					os_redirect(os_href_link(FILENAME_DEFAULT));
				} else {
					$manufacturer = os_db_fetch_array($manufacturer_query);
					os_db_query("update ".TABLE_MANUFACTURERS_INFO." set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '".(int) $_GET['manufacturers_id']."' and languages_id = '".$manufacturer['languages_id']."'");
				}
			} else {
				// url exists in selected language
				$manufacturer = os_db_fetch_array($manufacturer_query);
				os_db_query("update ".TABLE_MANUFACTURERS_INFO." set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '".(int) $_GET['manufacturers_id']."' and languages_id = '".$_SESSION['languages_id']."'");
			}

			os_redirect($manufacturer['manufacturers_url']);
		} else {
			os_redirect(os_href_link(FILENAME_DEFAULT));
		}
		break;

	default :
		os_redirect(os_href_link(FILENAME_DEFAULT));
		break;
}
?>