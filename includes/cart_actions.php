<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

if (!is_object($_SESSION['cart'])) {
	$_SESSION['cart'] = new shoppingCart();
}

if (isset ($_GET['action'])) {
	if ($session_started == false) {
		os_redirect(os_href_link(FILENAME_COOKIE_USAGE));
	}

	if (DISPLAY_CART == 'true') {
		$goto = FILENAME_SHOPPING_CART;
		$parameters = array (
			'action',
			'cPath',
			'products_id',
			'pid'
		);
	} else {
		$goto = basename($PHP_SELF);
		if ($_GET['action'] == 'buy_now') {
			$parameters = array (
				'action',
				'pid',
				'products_id',
				'BUYproducts_id'
			);
		} else {
			$parameters = array (
				'action',
				'pid',
				'BUYproducts_id',
				'info'
			);
		}
	}
	switch ($_GET['action']) {
		case 'update_product' :

			for ($i = 0, $n = sizeof($_POST['products_id']); $i < $n; $i++) {
					if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array ()))) {
					$_SESSION['cart']->remove($_POST['products_id'][$i]);
				} else {
					if ((int)$_POST['cart_quantity'][$i] > MAX_PRODUCTS_QTY)
						$_POST['cart_quantity'][$i] = MAX_PRODUCTS_QTY;
					$attributes = ($_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
					$_SESSION['cart']->add_cart($_POST['products_id'][$i], os_remove_non_numeric((int)$_POST['cart_quantity'][$i]), $attributes, false);
				}
			}
			os_redirect(os_href_link($goto, os_get_all_get_params($parameters)));
			break;
			//добавление продуктов
		case 'add_product' :
		
       foreach( $_REQUEST as $key => $value) $_POST[$key]=$value;
			if (isset ($_POST['products_id']) && is_numeric($_POST['products_id'])) {
				if ((int)$_POST['products_qty'] > MAX_PRODUCTS_QTY)
					$_POST['products_qty'] = MAX_PRODUCTS_QTY;
				$_SESSION['cart']->add_cart((int) $_POST['products_id'], $_SESSION['cart']->get_quantity(os_get_uprid($_POST['products_id'], $_POST['id'])) + os_remove_non_numeric((int)$_POST['products_qty']), $_POST['id']);
			}
			os_redirect(os_href_link($goto, 'products_id=' . (int) $_POST['products_id'] . '&' . os_get_all_get_params($parameters)));
			break;

		case 'check_gift' :
			os_collect_posts();
			break;

			// customer wants to add a quickie to the cart (called from a box)
		case 'add_a_quickie' :
			$quicky = addslashes($_POST['quickie']);
			if (GROUP_CHECK == 'true') {
				$group_check = "and group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
			}

			$quickie_query = os_db_query("select
						                                        products_fsk18,
						                                        products_id from " . TABLE_PRODUCTS . "
						                                        where products_model = '" . $quicky . "' " . "AND products_status = '1' " . $group_check);

			if (!os_db_num_rows($quickie_query)) {
				if (GROUP_CHECK == 'true') {
					$group_check = "and group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
				}
				$quickie_query = os_db_query("select
								                                                 products_fsk18,
								                                                 products_id from " . TABLE_PRODUCTS . "
								                                                 where products_model LIKE '%" . $quicky . "%' " . "AND products_status = '1' " . $group_check);
			}
			if (os_db_num_rows($quickie_query) != 1) {
				os_redirect(os_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $quicky, 'NONSSL'));
			}
			$quickie = os_db_fetch_array($quickie_query);
			if (os_has_product_attributes($quickie['products_id'])) {
				os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL'));
			} else {
				if ($quickie['products_fsk18'] == '1' && $_SESSION['customers_status']['customers_fsk18'] == '1') {
					os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL'));
				}
				if ($_SESSION['customers_status']['customers_fsk18_display'] == '0' && $quickie['products_fsk18'] == '1') {
					os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL'));
				}
				if ($_POST['quickie'] != '') {
					$act_qty = $_SESSION['cart']->get_quantity(os_get_uprid($quickie['products_id'], 1));
					if ($act_qty > MAX_PRODUCTS_QTY)
						$act_qty = MAX_PRODUCTS_QTY - 1;
					$_SESSION['cart']->add_cart($quickie['products_id'], $act_qty +1, 1);
					os_redirect(os_href_link($goto, os_get_all_get_params(array (
						'action'
					)), 'NONSSL'));
				} else {
					os_redirect(os_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $quicky, 'NONSSL'));
				}
			}
			break;

			// performed by the 'buy now' button in product listings and review page
		case 'buy_now' :
			if (isset ($_GET['BUYproducts_id'])) {
				// check permission to view product

				$permission_query = os_db_query("SELECT group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " as customer_group, products_fsk18 from " . TABLE_PRODUCTS . " where products_id='" . (int) $_GET['BUYproducts_id'] . "'");
				$permission = os_db_fetch_array($permission_query);

				// check for FSK18
				if ($permission['products_fsk18'] == '1' && $_SESSION['customers_status']['customers_fsk18'] == '1') {
					os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id'], 'NONSSL'));
				}
				if ($_SESSION['customers_status']['customers_fsk18_display'] == '0' && $permission['products_fsk18'] == '1') {
					os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id'], 'NONSSL'));
				}

				if (GROUP_CHECK == 'true') {

					if ($permission['customer_group'] != '1') {
						os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id']));
					}
				}
				if (os_has_product_attributes($_GET['BUYproducts_id'])) {
					os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id']));
				} else {
					if (isset ($_SESSION['cart'])) {
						$_SESSION['cart']->add_cart((int) $_GET['BUYproducts_id'], $_SESSION['cart']->get_quantity((int) $_GET['BUYproducts_id']) + 1);
					} else {
						os_redirect(os_href_link(FILENAME_DEFAULT));
					}
				}
			}
				if (os_has_product_attributes($_GET['BUYproducts_id'])) {
					os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['BUYproducts_id']));
				} else {
			os_redirect(os_href_link($goto, os_get_all_get_params(array (
				'action',
				'BUYproducts_id'
			))));
         }
			break;
		case 'cust_order' :
			if (isset ($_SESSION['customer_id']) && isset ($_GET['pid'])) {
				if (os_has_product_attributes((int) $_GET['pid'])) {
					os_redirect(os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $_GET['pid']));
				} else {
					$_SESSION['cart']->add_cart((int) $_GET['pid'], $_SESSION['cart']->get_quantity((int) $_GET['pid']) + 1);
				}
			}
			os_redirect(os_href_link($goto, os_get_all_get_params($parameters)));
			break;
	}
}
?>