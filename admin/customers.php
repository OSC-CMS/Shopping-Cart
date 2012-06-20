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

require ('includes/top.php');
$customers_statuses_array = os_get_customers_statuses();

if (isset($_GET['special']) && $_GET['special'] == 'remove_memo') 
{
	$mID = os_db_prepare_input($_GET['mID']);
	os_db_query("DELETE FROM ".TABLE_CUSTOMERS_MEMO." WHERE memo_id = '".$mID."'");
	os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID='.(int) $_GET['cID'].'&action=edit'));
}

if (isset($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'update')) 
{
	if ($_GET['cID'] == 1 && $_SESSION['customer_id'] == 1) {
	} else {
		if ($_GET['cID'] != 1) {
		} else {
			os_redirect(os_href_link(FILENAME_CUSTOMERS, ''));
		}
	}
}

if (isset($_GET['action']) && $_GET['action']) {
	switch ($_GET['action']) {
		case 'new_order' :

			$customers1_query = os_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$_GET['cID']."'");
			$customers1 = os_db_fetch_array($customers1_query);

			$customers_query = os_db_query("select * from ".TABLE_ADDRESS_BOOK." where customers_id = '".$_GET['cID']."'");
			$customers = os_db_fetch_array($customers_query);

			$country_query = os_db_query("select countries_name from ".TABLE_COUNTRIES." where status='1' and countries_id = '".$customers['entry_country_id']."'");
			$country = os_db_fetch_array($country_query);

			$stat_query = os_db_query("select * from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$customers1[customers_status]."' ");
			$stat = os_db_fetch_array($stat_query);

			$sql_data_array = array ('customers_id' => os_db_prepare_input($customers['customers_id']), 'customers_cid' => os_db_prepare_input($customers1['customers_cid']), 'customers_vat_id' => os_db_prepare_input($customers1['customers_vat_id']), 'customers_status' => os_db_prepare_input($customers1['customers_status']), 'customers_status_name' => os_db_prepare_input($stat['customers_status_name']), 'customers_status_image' => os_db_prepare_input($stat['customers_status_image']), 'customers_status_discount' => os_db_prepare_input($stat['customers_status_discount']), 'customers_name' => os_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_secondname'].' '.$customers['entry_lastname']), 'customers_company' => os_db_prepare_input($customers['entry_company']), 'customers_street_address' => os_db_prepare_input($customers['entry_street_address']), 'customers_suburb' => os_db_prepare_input($customers['entry_suburb']), 'customers_city' => os_db_prepare_input($customers['entry_city']), 'customers_postcode' => os_db_prepare_input($customers['entry_postcode']), 'customers_state' => os_db_prepare_input($customers['entry_state']), 'customers_country' => os_db_prepare_input($country['countries_name']), 'customers_telephone' => os_db_prepare_input($customers1['customers_telephone']), 'customers_email_address' => os_db_prepare_input($customers1['customers_email_address']), 'customers_address_format_id' => '5', 'customers_ip' => '0', 'delivery_name' => os_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_secondname'].' '.$customers['entry_lastname']), 'delivery_company' => os_db_prepare_input($customers['entry_company']), 'delivery_street_address' => os_db_prepare_input($customers['entry_street_address']), 'delivery_suburb' => os_db_prepare_input($customers['entry_suburb']), 'delivery_city' => os_db_prepare_input($customers['entry_city']), 'delivery_postcode' => os_db_prepare_input($customers['entry_postcode']), 'delivery_state' => os_db_prepare_input($customers['entry_state']), 'delivery_country' => os_db_prepare_input($country['countries_name']), 'delivery_address_format_id' => '5', 'billing_name' => os_db_prepare_input($customers['entry_firstname'].' '.$customers['entry_secondname'].' '.$customers['entry_lastname']), 'billing_company' => os_db_prepare_input($customers['entry_company']), 'billing_street_address' => os_db_prepare_input($customers['entry_street_address']), 'billing_suburb' => os_db_prepare_input($customers['entry_suburb']), 'billing_city' => os_db_prepare_input($customers['entry_city']), 'billing_postcode' => os_db_prepare_input($customers['entry_postcode']), 'billing_state' => os_db_prepare_input($customers['entry_state']), 'billing_country' => os_db_prepare_input($country['countries_name']), 'billing_address_format_id' => '5', 'payment_method' => 'cod', 'cc_type' => '', 'cc_owner' => '', 'cc_number' => '', 'cc_expires' => '', 'cc_start' => '', 'cc_issue' => '', 'cc_cvv' => '', 'comments' => '', 'last_modified' => 'now()', 'date_purchased' => 'now()', 'orders_status' => '1', 'orders_date_finished' => '', 'currency' => DEFAULT_CURRENCY, 'currency_value' => '1.0000', 'account_type' => '0', 'payment_class' => 'cod', 'shipping_method' => SHIPPING_FLAT, 'shipping_class' => 'flat_flat', 'customers_ip' => '', 'language' => $_SESSION['language']);

			$insert_sql_data = array ('currency_value' => '1.0000');
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_ORDERS, $sql_data_array);
			$orders_id = os_db_insert_id();

			$sql_data_array = array ('orders_id' => $orders_id, 'title' => ORDER_TOTAL, 'text' => '0', 'value' => '0', 'class' => 'ot_total');

			$insert_sql_data = array ('sort_order' => MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER);
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

			$sql_data_array = array ('orders_id' => $orders_id, 'title' => ORDER_SUBTOTAL, 'text' => '0', 'value' => '0', 'class' => 'ot_subtotal');

			$insert_sql_data = array ('sort_order' => MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER);
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

			os_redirect(os_href_link(FILENAME_ORDERS, 'oID='.$orders_id.'&action=edit'));

			break;
		case 'statusconfirm' :
			$customers_id = os_db_prepare_input($_GET['cID']);
			$customer_updated = false;
			$check_status_query = os_db_query("select customers_firstname, customers_secondname, customers_lastname, customers_email_address , customers_status, member_flag from ".TABLE_CUSTOMERS." where customers_id = '".os_db_input($_GET['cID'])."'");
			$check_status = os_db_fetch_array($check_status_query);
			if ($check_status['customers_status'] != $status) {
				os_db_query("update ".TABLE_CUSTOMERS." set customers_status = '".os_db_input($_POST['status'])."' where customers_id = '".os_db_input($_GET['cID'])."'");

				if ($_POST['status'] == 0) {
               $q = os_db_query("select * from ".TABLE_ADMIN_ACCESS." where customers_id='".os_db_input($_GET['cID'])."'");
               if (!os_db_num_rows($q))		   
					os_db_query("INSERT into ".TABLE_ADMIN_ACCESS." (customers_id,index2) VALUES ('".os_db_input($_GET['cID'])."','1')");
				} else {
					os_db_query("DELETE FROM ".TABLE_ADMIN_ACCESS." WHERE customers_id = '".os_db_input($_GET['cID'])."'");

				}

				$customer_notified = '0';
				os_db_query("insert into ".TABLE_CUSTOMERS_STATUS_HISTORY." (customers_id, new_value, old_value, date_added, customer_notified) values ('".os_db_input($_GET['cID'])."', '".os_db_input($_POST['status'])."', '".$check_status['customers_status']."', now(), '".$customer_notified."')");
				$customer_updated = true;
			}
			os_redirect(os_href_link(FILENAME_CUSTOMERS, 'page='.$_GET['page'].'&cID='.$_GET['cID']));
			break;

		case 'update' :
			$customers_id = os_db_prepare_input($_GET['cID']);
			$customers_cid = os_db_prepare_input($_POST['csID']);
			$customers_vat_id = os_db_prepare_input($_POST['customers_vat_id']);
			$customers_vat_id_status = os_db_prepare_input($_POST['customers_vat_id_status']);
			$customers_firstname = os_db_prepare_input($_POST['customers_firstname']);
			$customers_secondname = os_db_prepare_input($_POST['customers_secondname']);
			$customers_lastname = os_db_prepare_input($_POST['customers_lastname']);
			$customers_email_address = os_db_prepare_input($_POST['customers_email_address']);
			$customers_telephone = os_db_prepare_input($_POST['customers_telephone']);
			$customers_fax = os_db_prepare_input($_POST['customers_fax']);
			$customers_newsletter = os_db_prepare_input($_POST['customers_newsletter']);

			$customers_gender = os_db_prepare_input($_POST['customers_gender']);
			$customers_dob = os_db_prepare_input($_POST['customers_dob']);

			$default_address_id = os_db_prepare_input($_POST['default_address_id']);
			$entry_street_address = os_db_prepare_input($_POST['entry_street_address']);
			$entry_suburb = os_db_prepare_input($_POST['entry_suburb']);
			$entry_postcode = os_db_prepare_input($_POST['entry_postcode']);
			$entry_city = os_db_prepare_input($_POST['entry_city']);
			$entry_country_id = os_db_prepare_input($_POST['entry_country_id']);

			$entry_company = os_db_prepare_input($_POST['entry_company']);
			$entry_state = os_db_prepare_input($_POST['entry_state']);
			$entry_zone_id = os_db_prepare_input($_POST['entry_zone_id']);

			$memo_title = os_db_prepare_input($_POST['memo_title']);
			$memo_text = os_db_prepare_input($_POST['memo_text']);

			$payment_unallowed = os_db_prepare_input($_POST['payment_unallowed']);
			$shipping_unallowed = os_db_prepare_input($_POST['shipping_unallowed']);
			$password = os_db_prepare_input($_POST['entry_password']);
			

			if ($memo_text != '' && $memo_title != '') {
				$sql_data_array = array ('customers_id' => $_GET['cID'], 'memo_date' => date("Y-m-d"), 'memo_title' => $memo_title, 'memo_text' => $memo_text, 'poster_id' => $_SESSION['customer_id']);
				os_db_perform(TABLE_CUSTOMERS_MEMO, $sql_data_array);
			}
			$error = false;

			if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
				$error = true;
				$entry_firstname_error = true;
			} else {
				$entry_firstname_error = false;
			}

			if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
				$error = true;
				$entry_lastname_error = true;
			} else {
				$entry_lastname_error = false;
			}

			if (ACCOUNT_DOB == 'true') {
				if (checkdate(substr(os_date_raw($customers_dob), 4, 2), substr(os_date_raw($customers_dob), 6, 2), substr(os_date_raw($customers_dob), 0, 4))) {
					$entry_date_of_birth_error = false;
				} else {
					$error = true;
					$entry_date_of_birth_error = true;
				}
			}

	if (os_get_geo_zone_code($entry_country_id) != '6') {
	require_once( get_path('class').'vat_validation.php');
	$vatID = new vat_validation($customers_vat_id, $customers_id, '', $entry_country_id);

	$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
	$error = $vatID->vat_info['error'];

	if($error==1){
	$entry_vat_error = true;
	$error = true;
  }

  }

			if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
				$error = true;
				$entry_email_address_error = true;
			} else {
				$entry_email_address_error = false;
			}

			if (!os_validate_email($customers_email_address)) {
				$error = true;
				$entry_email_address_check_error = true;
			} else {
				$entry_email_address_check_error = false;
			}

        if (ACCOUNT_STREET_ADDRESS == 'true') {
			if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
				$error = true;
				$entry_street_address_error = true;
			} else {
				$entry_street_address_error = false;
			}
        }

        if (ACCOUNT_POSTCODE == 'true') {
			if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
				$error = true;
				$entry_post_code_error = true;
			} else {
				$entry_post_code_error = false;
			}
		  }

        if (ACCOUNT_CITY == 'true') {
			if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH) {
				$error = true;
				$entry_city_error = true;
			} else {
				$entry_city_error = false;
			}
        }

		$entry_country_error = false;

if (isset($_POST['country'])) { $entry_country_id = $_POST['country']; } else { $entry_country_id = STORE_COUNTRY; }
$entry_state = $_POST['state'];

	if (ACCOUNT_STATE == 'true') {
		if ($entry_country_error == true) {
			$entry_state_error = true;
		} else {
			$zone_id = 0;
			$entry_state_error = false;
			$check_query = os_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".os_db_input($entry_country_id)."'");
			$check_value = os_db_fetch_array($check_query);
			$entry_state_has_zones = ($check_value['total'] > 0);
			if ($entry_state_has_zones == true) {
				$zone_query = os_db_query("select zone_id from ".TABLE_ZONES." where zone_country_id = '".os_db_input($entry_country_id)."' and zone_name = '".os_db_input($entry_state)."'");
				if (os_db_num_rows($zone_query) == 1) {
					$zone_values = os_db_fetch_array($zone_query);
					$entry_zone_id = $zone_values['zone_id'];
				} else {
					$zone_query = os_db_query("select zone_id from ".TABLE_ZONES." where zone_country_id = '".os_db_input($entry_country)."' and zone_code = '".os_db_input($entry_state)."'");
					if (os_db_num_rows($zone_query) >= 1) {
						$zone_values = os_db_fetch_array($zone_query);
						$zone_id = $zone_values['zone_id'];
					} else {
						$error = true;
						$entry_state_error = true;
					}
				}
			} else {
				if ($entry_state == false) {
					$error = true;
					$entry_state_error = true;
				}
			}
		}
	}


        if (ACCOUNT_TELE == 'true') {
			if (strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
				$error = true;
				$entry_telephone_error = true;
			} else {
				$entry_telephone_error = false;
			}
        }

//			if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
//				$error = true;
//				$entry_password_error = true;
//			} else {
//				$entry_password_error = false;
//			}

			$check_email = os_db_query("select customers_email_address from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($customers_email_address)."' and customers_id <> '".os_db_input($customers_id)."'");
			if (os_db_num_rows($check_email)) {
				$error = true;
				$entry_email_address_exists = true;
			} else {
				$entry_email_address_exists = false;
			}
      $extra_fields_query = os_db_query("select ce.fields_id, ce.fields_input_type, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_size from " . TABLE_EXTRA_FIELDS . " ce, " . TABLE_EXTRA_FIELDS_INFO . " cei where ce.fields_status=1 and ce.fields_required_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =" . (int)$_SESSION['languages_id']);
      while($extra_fields = os_db_fetch_array($extra_fields_query)){
        if(strlen($_POST['fields_' . $extra_fields['fields_id'] ])<$extra_fields['fields_size']){
          $error = true;
          $string_error=sprintf(ENTRY_EXTRA_FIELDS_ERROR,$extra_fields['fields_name'],$extra_fields['fields_size']);
          $messageStack->add($string_error);
        }
      }

			if ($error == false) {
				$sql_data_array = array ('customers_firstname' => $customers_firstname, 'customers_secondname' => $customers_secondname, 'customers_cid' => $customers_cid, 'customers_vat_id' => $customers_vat_id, 'customers_vat_id_status' => (int)$customers_vat_id_status, 'customers_lastname' => $customers_lastname, 'customers_email_address' => $customers_email_address, 'customers_telephone' => $customers_telephone, 'customers_fax' => $customers_fax, 'payment_unallowed' => $payment_unallowed, 'shipping_unallowed' => $shipping_unallowed, 'customers_newsletter' => $customers_newsletter,'customers_last_modified' => 'now()');

				// if new password is set
				if ($password != "") {			
					$sql_data_array=array_merge($sql_data_array,array('customers_password' => os_encrypt_password($password)));						
				}

				if (ACCOUNT_GENDER == 'true')
					$sql_data_array['customers_gender'] = $customers_gender;
				if (ACCOUNT_DOB == 'true')
					$sql_data_array['customers_dob'] = os_date_raw($customers_dob);

				os_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '".os_db_input($customers_id)."'");

				os_db_query("update ".TABLE_CUSTOMERS_INFO." set customers_info_date_account_last_modified = now() where customers_info_id = '".os_db_input($customers_id)."'");

				if ($entry_zone_id > 0)
					$entry_state = '';

				$sql_data_array = array ('entry_firstname' => $customers_firstname, 'entry_secondname' => $customers_secondname, 'entry_lastname' => $customers_lastname, 'entry_street_address' => $entry_street_address, 'entry_postcode' => $entry_postcode, 'entry_city' => $entry_city, 'entry_country_id' => (int)$entry_country_id,'address_last_modified' => 'now()');
				
				
				if (ACCOUNT_COMPANY == 'true')
					$sql_data_array['entry_company'] = $entry_company;
				if (ACCOUNT_SUBURB == 'true')
					$sql_data_array['entry_suburb'] = $entry_suburb;

				if (ACCOUNT_STATE == 'true') {
					if ($entry_zone_id > 0) {
						$sql_data_array['entry_zone_id'] = (int)$entry_zone_id;
						$sql_data_array['entry_state'] = '';
					} else {
						$sql_data_array['entry_zone_id'] = 0;
						$sql_data_array['entry_state'] = $entry_state;
					}
				}

				os_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '".os_db_input($customers_id)."' and address_book_id = '".os_db_input($default_address_id)."'");

        os_db_query("delete from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where customers_id=" . (int)$customers_id);
        $extra_fields_query =os_db_query("select ce.fields_id from " . TABLE_EXTRA_FIELDS . " ce where ce.fields_status=1 ");
        while($extra_fields = os_db_fetch_array($extra_fields_query)){
            $sql_data_array = array('customers_id' => (int)$customers_id,
                              'fields_id' => $extra_fields['fields_id'],
                              'value' => $_POST['fields_' . $extra_fields['fields_id'] ]);
            os_db_perform(TABLE_CUSTOMERS_TO_EXTRA_FIELDS, $sql_data_array);
        }
				os_redirect(os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$customers_id));
			}
			elseif ($error == true) {
				$cInfo = new objectInfo($_POST);
				$processed = true;
			}

			break;
		case 'deleteconfirm' :
			$customers_id = os_db_prepare_input($_GET['cID']);

			if ($_POST['delete_reviews'] == 'on') {
				$reviews_query = os_db_query("select reviews_id from ".TABLE_REVIEWS." where customers_id = '".os_db_input($customers_id)."'");
				while ($reviews = os_db_fetch_array($reviews_query)) {
					os_db_query("delete from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."'");
				}
				os_db_query("delete from ".TABLE_REVIEWS." where customers_id = '".os_db_input($customers_id)."'");
			} else {
				os_db_query("update ".TABLE_REVIEWS." set customers_id = null where customers_id = '".os_db_input($customers_id)."'");
			}

			os_db_query("delete from ".TABLE_ADDRESS_BOOK." where customers_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_CUSTOMERS." where customers_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." where customers_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_PRODUCTS_NOTIFICATIONS." where customers_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_WHOS_ONLINE." where customer_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_CUSTOMERS_STATUS_HISTORY." where customers_id = '".os_db_input($customers_id)."'");
			os_db_query("delete from ".TABLE_CUSTOMERS_IP." where customers_id = '".os_db_input($customers_id)."'");
			os_db_query("DELETE FROM ".TABLE_ADMIN_ACCESS." WHERE customers_id = '".os_db_input($customers_id)."'");
			os_db_query("DELETE FROM ".DB_PREFIX."customers_profile WHERE customers_id = '".os_db_input($customers_id)."'");

			os_redirect(os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action'))));
			break;

		default :
			$customers_query = os_db_query("select c.customers_id,c.customers_cid, c.customers_gender, c.customers_firstname, c.customers_secondname, c.customers_lastname, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_state, a.entry_zone_id, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter, c.customers_default_address_id from ".TABLE_CUSTOMERS." c left join ".TABLE_ADDRESS_BOOK." a on c.customers_default_address_id = a.address_book_id where a.customers_id = c.customers_id and c.customers_id = '".$_GET['cID']."'");
			$customers = os_db_fetch_array($customers_query);
			$cInfo = new objectInfo($customers);
	}
}

function head_customers ()
{
   

if (isset($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'update')) {
?>
<script type="text/javascript"><!--
function check_form() {
  var error = 0;
  var error_message = "<?php echo os_js_lang(JS_ERROR); ?>";

  var customers_firstname = document.customers.customers_firstname.value;
  var customers_lastname = document.customers.customers_lastname.value;
<?php if (ACCOUNT_COMPANY == 'true') echo 'var entry_company = document.customers.entry_company.value;' . "\n"; ?>
<?php if (ACCOUNT_DOB == 'true') echo 'var customers_dob = document.customers.customers_dob.value;' . "\n"; ?>
  var customers_email_address = document.customers.customers_email_address.value;  
  var entry_street_address = document.customers.entry_street_address.value;
  var entry_postcode = document.customers.entry_postcode.value;
  var entry_city = document.customers.entry_city.value;
  var customers_telephone = document.customers.customers_telephone.value;

<?php if (ACCOUNT_GENDER == 'true') { ?>
  if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked) {
  } else {
    error_message = error_message + "<?php echo os_js_lang(JS_GENDER); ?>";
    error = 1;
  }
<?php } ?>

  if (customers_firstname == "" || customers_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_FIRST_NAME); ?>";
    error = 1;
  }

  if (customers_lastname == "" || customers_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_LAST_NAME); ?>";
    error = 1;
  }

<?php if (ACCOUNT_DOB == 'true') { ?>
  if (customers_dob == "" || customers_dob.length < <?php echo ENTRY_DOB_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_DOB); ?>";
    error = 1;
  }
<?php } ?>

  if (customers_email_address == "" || customers_email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_EMAIL_ADDRESS); ?>";
    error = 1;
  }

<?php if (ACCOUNT_STREET_ADDRESS == 'true') { ?>
  if (entry_street_address == "" || entry_street_address.length < <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_ADDRESS); ?>";
    error = 1;
  }
<?php } ?>

<?php if (ACCOUNT_POSTCODE == 'true') { ?>
  if (entry_postcode == "" || entry_postcode.length < <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_POST_CODE); ?>";
    error = 1;
  }
<?php } ?>

<?php if (ACCOUNT_CITY == 'true') { ?>
  if (entry_city == "" || entry_city.length < <?php echo ENTRY_CITY_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_CITY); ?>";
    error = 1;
  }
<?php } ?>

<?php

	if (ACCOUNT_STATE == 'true') {
?>
  if (document.customers.elements['entry_state'].type != "hidden") {
    if (document.customers.entry_state.value == '' || document.customers.entry_state.value.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?> ) {
       error_message = error_message + "<?php echo os_js_lang(JS_STATE); ?>";
       error = 1;
    }
  }
<?php

	}
?>

<?php if (ACCOUNT_COUNTRY == 'true') { ?>
  if (document.customers.elements['entry_country_id'].type != "hidden") {
    if (document.customers.entry_country_id.value == 0) {
      error_message = error_message + "<?php echo os_js_lang(JS_COUNTRY); ?>";
      error = 1;
    }
  }
<?php } ?>

<?php if (ACCOUNT_TELE == 'true') { ?>
  if (customers_telephone == "" || customers_telephone.length < <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo os_js_lang(JS_TELEPHONE); ?>";
    error = 1;
  }
<?php } ?>

  if (error == 1) {
    alert(unescape(error_message));
    return false;
  } else {
    return true;
  }
}
//--></script>
<?php

}
}

add_action('head_admin', 'head_customers');

$main->head(); 
?>

<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php

if (isset($_GET['action']) && ( $_GET['action'] == 'edit' || $_GET['action'] == 'update')) {
	$customers_query = os_db_query("select c.payment_unallowed, c.shipping_unallowed, c.customers_gender, c.customers_vat_id, c.customers_status, c.member_flag, c.customers_firstname, c.customers_secondname,c.customers_cid, c.customers_lastname, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_state, a.entry_zone_id, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter, c.customers_default_address_id from ".TABLE_CUSTOMERS." c left join ".TABLE_ADDRESS_BOOK." a on c.customers_default_address_id = a.address_book_id where a.customers_id = c.customers_id and c.customers_id = '".$_GET['cID']."'");

	$customers = os_db_fetch_array($customers_query);
	$cInfo = new objectInfo($customers);
	$newsletter_array = array (array ('id' => '1', 'text' => ENTRY_NEWSLETTER_YES), array ('id' => '0', 'text' => ENTRY_NEWSLETTER_NO));
?>
    <td class="boxCenter" width="100%" valign="top">
  	<?php os_header('uses.png',$cInfo->customers_lastname.' '.$cInfo->customers_firstname); ?> 
  </td>
  </tr>

  <tr>
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="middle" class="pageHeading"><?php if ($customers_statuses_array[$customers['customers_status']]['csa_image'] != '') { echo os_image(http_path('icons_admin') . $customers_statuses_array[$customers['customers_status']]['csa_image'], ''); } ?></td>
            <td class="main"></td>
          </tr>
          <tr>
            <td colspan="3" class="main"><?php echo HEADING_TITLE_STATUS  .': ' . $customers_statuses_array[$customers['customers_status']]['text'] ; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr><?php echo os_draw_form('customers', FILENAME_CUSTOMERS, os_get_all_get_params(array('action')) . 'action=update', 'post', 'onSubmit="return check_form();"') . os_draw_hidden_field('default_address_id', $cInfo->customers_default_address_id); ?>
        <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
<?php

	if (ACCOUNT_GENDER == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_GENDER; ?></td>
            <td class="main"><?php

		if ($error == true) {
			if ($entry_gender_error == true) {
				echo os_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender).'&nbsp;&nbsp;'.MALE.'&nbsp;&nbsp;'.os_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender).'&nbsp;&nbsp;'.FEMALE.'&nbsp;'.ENTRY_GENDER_ERROR;
			} else {
				echo ($cInfo->customers_gender == 'm') ? MALE : FEMALE;
				echo os_draw_hidden_field('customers_gender');
			}
		} else {
			echo os_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender).'&nbsp;&nbsp;'.MALE.'&nbsp;&nbsp;'.os_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender).'&nbsp;&nbsp;'.FEMALE;
		}
?></td>
          </tr>
<?php


	}
?>
          <tr>
            <td class="main" bgcolor="#FFCC33"><?php echo ENTRY_CID; ?></td>
            <td class="main" width="100%" bgcolor="#FFCC33"><?php

	echo os_draw_input_field('csID', $cInfo->customers_cid, 'maxlength="32"', false);
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
            <td class="main"><?php

	if (isset($entry_firstname_error) && $entry_firstname_error == true) 
	{
		echo os_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"').'&nbsp;'.ENTRY_FIRST_NAME_ERROR;
	} else {
		echo os_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"', true);
	}
?></td>
          </tr>
<?php

	if (ACCOUNT_SECOND_NAME == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_SECOND_NAME; ?></td>
            <td class="main"><?php

		echo os_draw_input_field('customers_secondname', $cInfo->customers_secondname, 'maxlength="32"', false);

?></td>
          </tr>
<?php

	}
?>
          <tr>
            <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
            <td class="main"><?php

	if (isset($error) && $error == true) {
		if ($entry_lastname_error == true) {
			echo os_draw_input_field('customers_lastname', $cInfo->customers_lastname, 'maxlength="32"').'&nbsp;'.ENTRY_LAST_NAME_ERROR;
		} else {
			echo $cInfo->customers_lastname.os_draw_hidden_field('customers_lastname');
		}
	} else {
		echo os_draw_input_field('customers_lastname', $cInfo->customers_lastname, 'maxlength="32"', true);
	}
?></td>
          </tr>
<?php

	if (ACCOUNT_DOB == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
            <td class="main"><?php

		if ($error == true) {
			if ($entry_date_of_birth_error == true) {
				echo os_draw_input_field('customers_dob', os_date_short($cInfo->customers_dob), 'maxlength="10"').'&nbsp;'.ENTRY_DATE_OF_BIRTH_ERROR;
			} else {
				echo $cInfo->customers_dob.os_draw_hidden_field('customers_dob');
			}
		} else {
			echo os_draw_input_field('customers_dob', os_date_short($cInfo->customers_dob), 'maxlength="10"', true);
		}
?></td>
          </tr>
<?php

	}
?>
          <tr>
            <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
            <td class="main"><?php

	if (isset($error) && $error == true) {
		if ($entry_email_address_error == true) {
			echo os_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"').'&nbsp;'.ENTRY_EMAIL_ADDRESS_ERROR;
		}
		elseif ($entry_email_address_check_error == true) {
			echo os_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"').'&nbsp;'.ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
		}
		elseif ($entry_email_address_exists == true) {
			echo os_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"').'&nbsp;'.ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
		} else {
			echo $customers_email_address.os_draw_hidden_field('customers_email_address');
		}
	} else {
		echo os_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"', true);
	}
?></td>
          </tr>
        </table></td>
      </tr>
<?php

	if (ACCOUNT_COMPANY == 'true') {
?>
      <tr>
        <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_COMPANY; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_COMPANY; ?></td>
            <td class="main"><?php

		if ($error == true) {
			if ($entry_company_error == true) {
				echo os_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="32"').'&nbsp;'.ENTRY_COMPANY_ERROR;
			} else {
				echo $cInfo->entry_company.os_draw_hidden_field('entry_company');
			}
		} else {
			echo os_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="32"');
		}
?></td>
          </tr>

<?php if(ACCOUNT_COMPANY_VAT_CHECK == 'true'){ ?>
          <tr>
            <td class="main"><?php echo ENTRY_VAT_ID; ?></td>
            <td class="main"><?php

		if ($error == true) {
			if ($entry_vat_error == true) {
				echo os_draw_input_field('customers_vat_id', $cInfo->customers_vat_id, 'maxlength="32"').'&nbsp;'.ENTRY_VAT_ID_ERROR;
			} else {
				echo $cInfo->customers_vat_id.os_draw_hidden_field('customers_vat_id');
			}
		} else {
			echo os_draw_input_field('customers_vat_id', $cInfo->customers_vat_id, 'maxlength="32"');
		}
?></td>
          </tr>
<?php } ?>

        </table></td>
      </tr>
<?php

	}
?>
      <tr>
        <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php

	if (ACCOUNT_STREET_ADDRESS == 'true') {
?>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_ADDRESS; ?></td>
      </tr>
<?php

	}
?>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
<?php

	if (ACCOUNT_STREET_ADDRESS == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_STREET_ADDRESS; ?></td>
            <td class="main"><?php

	if (isset($error) && $error == true) {
		if ($entry_street_address_error == true) {
			echo os_draw_input_field('entry_street_address', $cInfo->entry_street_address, 'maxlength="64"').'&nbsp;'.ENTRY_STREET_ADDRESS_ERROR;
		} else {
			echo $cInfo->entry_street_address.os_draw_hidden_field('entry_street_address');
		}
	} else {
		echo os_draw_input_field('entry_street_address', $cInfo->entry_street_address, 'maxlength="64"', true);
	}
?></td>
          </tr>
<?php

	}
?>

<?php

	if (ACCOUNT_SUBURB == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_SUBURB; ?></td>
            <td class="main"><?php

		if ($error == true) {
			if ($entry_suburb_error == true) {
				echo os_draw_input_field('suburb', $cInfo->entry_suburb, 'maxlength="32"').'&nbsp;'.ENTRY_SUBURB_ERROR;
			} else {
				echo $cInfo->entry_suburb.os_draw_hidden_field('entry_suburb');
			}
		} else {
			echo os_draw_input_field('entry_suburb', $cInfo->entry_suburb, 'maxlength="32"');
		}
?></td>
          </tr>
<?php

	}
?>
<?php

	if (ACCOUNT_POSTCODE == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_POST_CODE; ?></td>
            <td class="main"><?php

	if (isset($error) && $error == true) {
		if ($entry_post_code_error == true) {
			echo os_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"').'&nbsp;'.ENTRY_POST_CODE_ERROR;
		} else {
			echo $cInfo->entry_postcode.os_draw_hidden_field('entry_postcode');
		}
	} else {
		echo os_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"', true);
	}
?></td>
          </tr>
<?php

	}
?>
<?php

	if (ACCOUNT_CITY == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_CITY; ?></td>
            <td class="main"><?php

	if (isset($error) && $error == true) {
		if ($entry_city_error == true) {
			echo os_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"').'&nbsp;'.ENTRY_CITY_ERROR;
		} else {
			echo $cInfo->entry_city.os_draw_hidden_field('entry_city');
		}
	} else {
		echo os_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"', true);
	}
?></td>
          </tr>
<?php

	}
?>
<?php
  if (ACCOUNT_COUNTRY == 'true') {
?>
              <tr>
                <td class="main"><?php echo ENTRY_COUNTRY; ?></td>
                <td class="main"><?php echo os_get_country_list('country',$cInfo->entry_country_id, 'onChange="changeselect();"') . '&nbsp;' . (defined(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>': ''); ?></td>
              </tr>
<?php
  }
?>
<?php
if (ACCOUNT_STATE == 'true') {
?>
             <tr>
               <td class="main"><?php echo ENTRY_STATE;?></td>
               <td class="main">
<script language="javascript">
<!--
function changeselect(reg) {
//clear select
    document.customers.state.length=0;
    var j=0;
    for (var i=0;i<zones.length;i++) {
      if (zones[i][0]==document.customers.country.value) {
   document.customers.state.options[j]=new Option(zones[i][1],zones[i][1]);
   j++;
   }
      }
    if (j==0) {
      document.customers.state.options[0]=new Option('-','-');
      }
    if (reg) { document.customers.state.value = reg; }
}
   var zones = new Array(
   <?php
       $zones_query = os_db_query("select zone_country_id,zone_name from " . TABLE_ZONES . " order by zone_name asc");
       $mas=array();
       while ($zones_values = os_db_fetch_array($zones_query)) {
         $zones[] = 'new Array('.$zones_values['zone_country_id'].',"'.$zones_values['zone_name'].'")';
       }

        $zones_array1[] = 'new Array('.$cInfo->entry_country_id.',"'.os_get_zone_name($cInfo->entry_country_id,$cInfo->entry_zone_id,'').'")';

        $zones = array_merge($zones_array1, $zones);

       echo implode(',',$zones);
       ?>
       );
document.write('<SELECT NAME="state">');
document.write('</SELECT>');
changeselect("<?php echo os_db_prepare_input($_POST['state']); ?>");
-->
</script>
          </td>
             </tr>
<?php
}
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php

	if (ACCOUNT_TELE == 'true') {
?>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_CONTACT; ?></td>
      </tr>
<?php

	}
?>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
<?php

	if (ACCOUNT_TELE == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
            <td class="main"><?php

	if (isset($error) && $error == true) {
		if ($entry_telephone_error == true) {
			echo os_draw_input_field('customers_telephone', $cInfo->customers_telephone, 'maxlength="32"').'&nbsp;'.ENTRY_TELEPHONE_NUMBER_ERROR;
		} else {
			echo $cInfo->customers_telephone.os_draw_hidden_field('customers_telephone');
		}
	} else {
		echo os_draw_input_field('customers_telephone', $cInfo->customers_telephone, 'maxlength="32"', true);
	}
?></td>
          </tr>
<?php

	}
?>
<?php

	if (ACCOUNT_TELE == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_FAX_NUMBER; ?></td>
            <td class="main"><?php

	if (isset($processed) && $processed == true) {
		echo $cInfo->customers_fax.os_draw_hidden_field('customers_fax');
	} else {
		echo os_draw_input_field('customers_fax', $cInfo->customers_fax, 'maxlength="32"');
	}
?></td>
          </tr>
<?php

	}
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <?php echo os_get_extra_fields($_GET['cID'],$_SESSION['languages_id']); ?>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_OPTIONS; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
        
        
                  <tr>
            <td class="main"><?php echo ENTRY_PAYMENT_UNALLOWED; ?></td>
            <td class="main"><?php

	if (isset($processed) && $processed == true) {
		echo $cInfo->payment_unallowed.os_draw_hidden_field('payment_unallowed');
	} else {
		echo os_draw_input_field('payment_unallowed', $cInfo->payment_unallowed, 'maxlength="255"');
	}
?></td>
          </tr>
                    <tr>
            <td class="main"><?php echo ENTRY_SHIPPING_UNALLOWED; ?></td>
            <td class="main"><?php

	if (isset($processed) && $processed == true) {
		echo $cInfo->shipping_unallowed.os_draw_hidden_field('shipping_unallowed');
	} else {
		echo os_draw_input_field('shipping_unallowed', $cInfo->shipping_unallowed, 'maxlength="255"');
	}
?></td>
         </tr>
            <td class="main" bgcolor="#FFCC33"><?php echo ENTRY_NEW_PASSWORD; ?></td>
            <td class="main" bgcolor="#FFCC33"><?php

if (isset($error) && $error == true) {
	if ($entry_password_error == true) {
		echo os_draw_input_field('entry_password', $customers_password).'&nbsp;'.ENTRY_PASSWORD_ERROR;
	} else {
		echo os_draw_input_field('entry_password');
	}
} else {
	echo os_draw_input_field('entry_password');
}
?></td>      
        
          <tr>
            <td class="main"><?php echo ENTRY_NEWSLETTER; ?></td>
            <td class="main"><?php

	if (isset($processed) && $processed == true) {
		if ($cInfo->customers_newsletter == '1') {
			echo ENTRY_NEWSLETTER_YES;
		} else {
			echo ENTRY_NEWSLETTER_NO;
		}
		echo os_draw_hidden_field('customers_newsletter');
	} else {
		echo os_draw_pull_down_menu('customers_newsletter', $newsletter_array, $cInfo->customers_newsletter);
	}
?></td>
          </tr>
          <tr>
<?php include(_MODULES_ADMIN . FILENAME_CUSTOMER_MEMO); ?>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main"><span class="button"><button type="submit" onClick="this.blur();" value="<?php echo BUTTON_UPDATE; ?>"><?php echo BUTTON_UPDATE; ?></button></span><?php echo ' <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array('action'))) .'"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>
      </tr></form>
<?php

} else {
?>
  <tr>
        <td width="100%">

    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
  
  </td>
  </tr>
      <tr>
        <td>
        
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><?php echo os_draw_form('search', FILENAME_CUSTOMERS, '', 'get'); ?>
            <td class="pageHeading" align="left"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CREATE_ACCOUNT) . '"><span>' . BUTTON_CREATE_ACCOUNT . '</span></a>'; ?></td>
            <td class="smallText" align="right"><?php echo HEADING_TITLE_SEARCH . ' ' . os_draw_input_field('search').os_draw_hidden_field(os_session_name(), os_session_id()); ?></td>
          </form></tr>
          <tr><?php echo os_draw_form('status', FILENAME_CUSTOMERS, '', 'get'); ?>
<?php

	$select_data = array ();
	$select_data = array (array ('id' => '99', 'text' => TEXT_SELECT), array ('id' => '100', 'text' => TEXT_ALL_CUSTOMERS));
?>          
            <td class="smallText" align="right" colspan="3"><?php echo HEADING_TITLE_STATUS . ' ' . os_draw_pull_down_menu('status',os_array_merge($select_data, $customers_statuses_array), '99', 'onChange="this.form.submit();"').os_draw_hidden_field(os_session_name(), os_session_id()); ?></td>




          </form></tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="40"><?php echo TABLE_HEADING_ACCOUNT_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LASTNAME.os_sorting(FILENAME_CUSTOMERS,'customers_lastname'); ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIRSTNAME.os_sorting(FILENAME_CUSTOMERS,'customers_firstname'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo HEADING_TITLE_STATUS; ?></td>
                <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {?>
                <td class="dataTableHeadingContent" align="center"><?php echo HEADING_TITLE_VAT; ?></td>
                <?php } ?>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ACCOUNT_CREATED.os_sorting(FILENAME_CUSTOMERS,'date_account_created'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php

	$search = '';
	if ((isset($_GET['search'])) && (os_not_null($_GET['search']))) 
	{
		$keywords = os_db_input(os_db_prepare_input($_GET['search']));
		$search = "and (c.customers_lastname like '%".$keywords."%' or c.customers_firstname like '%".$keywords."%' or c.customers_email_address like '%".$keywords."%' or c.customers_telephone like '%".$keywords."%')";
	}

	if (isset($_GET['status']) && ($_GET['status'] != '100' or $_GET['status'] == '0')) 
	{
		$status = os_db_prepare_input($_GET['status']);
		$search = "and c.customers_status = '".$status."'";
	}
    
	if (isset($_GET['sorting'])) 
	{
		switch ($_GET['sorting']) {

			case 'customers_firstname' :
				$sort = 'order by c.customers_firstname';
				break;

			case 'customers_firstname-desc' :
				$sort = 'order by c.customers_firstname DESC';
				break;

			case 'customers_lastname' :
				$sort = 'order by c.customers_lastname';
				break;

			case 'customers_lastname-desc' :
				$sort = 'order by c.customers_lastname DESC';
				break;

			case 'date_account_created' :
				$sort = 'order by ci.customers_info_date_account_created';
				break;

			case 'date_account_created-desc' :
				$sort = 'order by ci.customers_info_date_account_created DESC';
				break;
		}

	}
	else
	{
	   if (!isset($sort) or empty($sort)) $sort = '';
	   
	}

	$customers_query_raw = "select
	                                c.account_type,
	                                c.customers_id,
	                                c.customers_vat_id,
	                                c.customers_vat_id_status,
	                                c.customers_lastname,
	                                c.customers_firstname,
	                                c.customers_secondname,
	                                c.customers_email_address,
	                                a.entry_country_id,
	                                c.customers_status,
	                                c.member_flag,
	                                ci.customers_info_date_account_created
	                                from
	                                ".TABLE_CUSTOMERS." c ,
	                                ".TABLE_ADDRESS_BOOK." a,
	                                ".TABLE_CUSTOMERS_INFO." ci
	                                Where
	                                c.customers_id = a.customers_id
	                                and c.customers_default_address_id = a.address_book_id
	                                and ci.customers_info_id = c.customers_id
	                                ".$search."
	                                group by c.customers_id
	                                ".$sort;

	$customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $customers_query_raw, $customers_query_numrows);
	$customers_query = os_db_query($customers_query_raw);
	 $color = '';
	while ($customers = os_db_fetch_array($customers_query)) {
		$info_query = os_db_query("select customers_info_date_account_created as date_account_created, customers_info_date_account_last_modified as date_account_last_modified, customers_info_date_of_last_logon as date_last_logon, customers_info_number_of_logons as number_of_logons from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".$customers['customers_id']."'");
		$info = os_db_fetch_array($info_query);

		if (((!isset($_GET['cID'])) || (@ $_GET['cID'] == $customers['customers_id'])) && (!isset($cInfo))) 
		{
			$country_query = os_db_query("select countries_name from ".TABLE_COUNTRIES." where countries_id = '".$customers['entry_country_id']."'");
			$country = os_db_fetch_array($country_query);

			$reviews_query = os_db_query("select count(*) as number_of_reviews from ".TABLE_REVIEWS." where customers_id = '".$customers['customers_id']."'");
			$reviews = os_db_fetch_array($reviews_query);

        $customer_info = array_merge((array)$country, (array)$info, (array)$reviews);
        
			$cInfo_array = os_array_merge($customers, $customer_info);
			$cInfo = new objectInfo($cInfo_array);
		}
		
        $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
		
		if (isset($cInfo) && (is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id)) {
			echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\''.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=edit').'\'">'."\n";
		} else {
			echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" class="dataTableRow"  onclick="document.location.href=\''.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID')).'cID='.$customers['customers_id']).'\'">'."\n";
		}

		if ($customers['account_type'] == 1) {

			echo '<td class="dataTableContent align=\"center\"">';
			echo TEXT_GUEST;

		} else {
			echo '<td class="dataTableContent" align=\"center\">';
			echo TEXT_ACCOUNT;
		}
?></td>
                <td class="dataTableContent" align="center"><b><?php echo $customers['customers_lastname']; ?></b></td>
                <td class="dataTableContent" align="center"><?php echo $customers['customers_firstname']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $customers_statuses_array[$customers['customers_status']]['text'] . ' (' . $customers['customers_status'] . ')' ; ?></td>
                <?php if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {?>
                <td class="dataTableContent" align="center">&nbsp;
                <?php

		if ($customers['customers_vat_id']) {
			echo $customers['customers_vat_id'].'<br /><span style="font-size:8pt"><nobr>('.os_validate_vatid_status($customers['customers_id']).')</nobr></span>';
		}
?>
                </td>
                <?php } ?>
                <td class="dataTableContent" align="center"><?php echo os_date_short($info['date_account_created']); ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($cInfo) && (is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php

	}
?>
              <tr>
                <td colspan="7"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], os_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                  </tr>
<?php

	if (isset($_GET['search']) && os_not_null($_GET['search'])) {
?>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS) . '"><span>' . BUTTON_RESET . '</span></a>'; ?></td>
                  </tr>
<?php

	}
?>
                </table></td>
              </tr>
            </table></td>
<?php

	$heading = array ();
	$contents = array ();
	switch (@$_GET['action']) {
		case 'confirm' :
			$heading[] = array ('text' => '<b>'.TEXT_INFO_HEADING_DELETE_CUSTOMER.'</b>');

			$contents = array ('form' => os_draw_form('customers', FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=deleteconfirm'));
			$contents[] = array ('text' => TEXT_DELETE_INTRO.'<br /><br /><b>'.$cInfo->customers_firstname.' '.$cInfo->customers_lastname.'</b>');
			if ($cInfo->number_of_reviews > 0)
				$contents[] = array ('text' => '<br />'.os_draw_checkbox_field('delete_reviews', 'on', true).' '.sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews));
			$contents[] = array ('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" value="'.BUTTON_DELETE.'">'.BUTTON_DELETE.'</button></span><br /><a class="button" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id).'"><span>'.BUTTON_CANCEL.'</span></a><br />');
			break;

		case 'editstatus' :
			if ($_GET['cID'] != 1) {
				$customers_history_query = os_db_query("select new_value, old_value, date_added, customer_notified from ".TABLE_CUSTOMERS_STATUS_HISTORY." where customers_id = '".os_db_input($_GET['cID'])."' order by customers_status_history_id desc");
				$heading[] = array ('text' => '<b>'.TEXT_INFO_HEADING_STATUS_CUSTOMER.'</b>');
				$contents = array ('form' => os_draw_form('customers', FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=statusconfirm'));
				$contents[] = array ('text' => '<br />'.os_draw_pull_down_menu('status', $customers_statuses_array, $cInfo->customers_status));
				$contents[] = array ('text' => '<table nowrap border="0" cellspacing="0" cellpadding="0"><tr><td style="border-bottom: 1px solid; border-color: #000000;" nowrap class="smallText" align="center"><b>'.TABLE_HEADING_NEW_VALUE.' </b></td><td style="border-bottom: 1px solid; border-color: #000000;" nowrap class="smallText" align="center"><b>'.TABLE_HEADING_DATE_ADDED.'</b></td></tr>');

				if (os_db_num_rows($customers_history_query)) {
					while ($customers_history = os_db_fetch_array($customers_history_query)) {

						$contents[] = array ('text' => '<tr>'."\n".'<td class="smallText">'.$customers_statuses_array[$customers_history['new_value']]['text'].'</td>'."\n".'<td class="smallText" align="center">'.os_datetime_short($customers_history['date_added']).'</td>'."\n".'<td class="smallText" align="center">');

						$contents[] = array ('text' => '</tr>'."\n");
					}
				} else {
					$contents[] = array ('text' => '<tr>'."\n".' <td class="smallText" colspan="2">'.TEXT_NO_CUSTOMER_HISTORY.'</td>'."\n".' </tr>'."\n");
				}
				$contents[] = array ('text' => '</table>');
				$contents[] = array ('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" value="'.BUTTON_UPDATE.'">'.BUTTON_UPDATE.'</button></span><br /><a class="button" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id).'"><span>'.BUTTON_CANCEL.'</span></a><br />');
				$status = os_db_prepare_input($_POST['status']);
			}
			break;

		default :
			$customer_status = isset($_GET['cID'])?os_get_customer_status($_GET['cID']):'';
			$cs_id = isset($customer_status['customers_status'])?$customer_status['customers_status']:'';
			$cs_member_flag = isset($customer_status['member_flag'])?$customer_status['member_flag']:'';
			$cs_name = isset($customer_status['customers_status_name'])?$customer_status['customers_status_name']:'';
			$cs_image = isset($customer_status['customers_status_image'])?$customer_status['customers_status_image']:'';
			$cs_discount = isset($customer_status['customers_status_discount'])?$customer_status['customers_status_discount']:'';
			$cs_ot_discount_flag = isset($customer_status['customers_status_ot_discount_flag'])?$customer_status['customers_status_ot_discount_flag']:'';
			$cs_ot_discount = isset($customer_status['customers_status_ot_discount'])?$customer_status['customers_status_ot_discount']:'';
			$cs_staffelpreise = isset($customer_status['customers_status_staffelpreise'])?$customer_status['customers_status_staffelpreise']:'';
			$cs_payment_unallowed = isset($customer_status['customers_status_payment_unallowed'])?$customer_status['customers_status_payment_unallowed']:'';


			if (is_object($cInfo)) {
				$heading[] = array ('text' => '<b>'.$cInfo->customers_firstname.' '.$cInfo->customers_lastname.'</b>');
				if ($cInfo->customers_id != 1) {
					$contents[] = array ('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=edit').'"><span>'.BUTTON_EDIT.'</span></a><br />');
				}
				if ($cInfo->customers_id == 1 && $_SESSION['customer_id'] == 1) {
					$contents[] = array ('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=edit').'"><span>'.BUTTON_EDIT.'</span></a><br />');
				}
				if ($cInfo->customers_id != 1) {
					$contents[] = array ('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=confirm').'"><span>'.BUTTON_DELETE.'</span></a><br />');
				}
				if ($cInfo->customers_id != 1
					) {
					$contents[] = array ('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=editstatus').'"><span>'.BUTTON_STATUS.'</span></a><br />');
				}
				if ($cInfo->customers_id != 1) {
					$contents[] = array ('align' => 'center', 'text' => '<a class="button" ' . ($cs_id != 0 ? 'onClick="alert(\'Сначала определите пользователя в группу Админ (кнопка Статус покупателя)!\nДоступ в админку можно настраивать только для пользователей, состоящих в группе Админ.\');"' : 'onClick="this.blur();"') . ' href="'.os_href_link(FILENAME_ACCOUNTING, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id).'"><span>'.BUTTON_ACCOUNTING.'</span></a><br />');
				}
				$contents[] = array ('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_ORDERS, 'cID='.$cInfo->customers_id).'"><span>'.BUTTON_ORDERS.'</span></a><br /><a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_MAIL, 'selected_box=tools&customer='.$cInfo->customers_email_address).'"><span>'.BUTTON_EMAIL.'</span></a><br /><a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=iplog').'"><span>'.BUTTON_IPLOG.'</span></a><br /><a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID', 'action')).'cID='.$cInfo->customers_id.'&action=new_order').'" onClick="return confirm(\''.NEW_ORDER.'\')"><span>'.BUTTON_NEW_ORDER.'</span></a><br />');

				$contents[] = array ('text' => '<br /><b>'.TEXT_DATE_ACCOUNT_CREATED.'</b> '.os_date_short($cInfo->date_account_created));
				$contents[] = array ('text' => '<br /><b>'.TEXT_DATE_ACCOUNT_LAST_MODIFIED.'</b> '.os_date_short($cInfo->date_account_last_modified));
				$contents[] = array ('text' => '<br /><b>'.TEXT_INFO_DATE_LAST_LOGON.'</b> '.os_date_short($cInfo->date_last_logon));
				$contents[] = array ('text' => '<br /><b>'.TEXT_INFO_NUMBER_OF_LOGONS.'</b> '.$cInfo->number_of_logons);
				$contents[] = array ('text' => '<br /><b>'.TEXT_INFO_COUNTRY.'</b> '.$cInfo->countries_name);
				$contents[] = array ('text' => '<br /><b>'.TEXT_INFO_NUMBER_OF_REVIEWS.'</b> '.$cInfo->number_of_reviews);
			}

			if (isset($_GET['action']) && $_GET['action'] == 'iplog') {
				if (isset ($_GET['cID'])) {
					$contents[] = array ('text' => '<br /><b>IPLOG :');
					$customers_id = os_db_prepare_input($_GET['cID']);
					$customers_log_info_array = os_get_user_info($customers_id);
					if (os_db_num_rows($customers_log_info_array)) {
						while ($customers_log_info = os_db_fetch_array($customers_log_info_array)) {
							$contents[] = array ('text' => '<tr>'."\n".'<td class="smallText">'.$customers_log_info['customers_ip_date'].' '.$customers_log_info['customers_ip'].' '.$customers_log_info['customers_advertiser']);
						}
					}
				}
				break;
			}
	}
	if ((os_not_null($heading)) && (os_not_null($contents))) {
		echo '            <td class="right_box" valign="top">'."\n";

		$box = new box;
		echo $box->infoBox($heading, $contents);

		echo '            </td>'."\n";
	}
?>
          </tr>
        </table></td>
      </tr>
<?php

}
?>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>