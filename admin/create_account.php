<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require ('includes/top.php');
require_once(_LIB.'phpmailer/class.phpmailer.php');


$osTemplate = new osTemplate;

$customers_statuses_array = os_get_customers_statuses();
if ($customers_password == '') {
	$customers_password_encrypted =  os_RandomString(8);
	$customers_password = os_encrypt_password($customers_password_encrypted);
}
if ($_GET['action'] == 'edit') {
	$customers_firstname = os_db_prepare_input($_POST['customers_firstname']);
	$customers_secondname = os_db_prepare_input($_POST['customers_secondname']);
	$customers_cid = os_db_prepare_input($_POST['csID']);
	$customers_vat_id = os_db_prepare_input($_POST['customers_vat_id']);
	$customers_vat_id_status = os_db_prepare_input($_POST['customers_vat_id_status']);
	$customers_lastname = os_db_prepare_input($_POST['customers_lastname']);
	$customers_email_address = os_db_prepare_input($_POST['customers_email_address']);
	$customers_telephone = os_db_prepare_input($_POST['customers_telephone']);
	$customers_fax = os_db_prepare_input($_POST['customers_fax']);
	$customers_status_c = os_db_prepare_input($_POST['status']);

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

	$customers_send_mail = os_db_prepare_input($_POST['customers_mail']);
	$customers_password_encrypted = os_db_prepare_input($_POST['entry_password']);
	$customers_password = os_encrypt_password($customers_password_encrypted);
	
	$customers_mail_comments = os_db_prepare_input($_POST['mail_comments']);

	$payment_unallowed = os_db_prepare_input($_POST['payment_unallowed']);
	$shipping_unallowed = os_db_prepare_input($_POST['shipping_unallowed']);

	if ($customers_password == '') {
		$customers_password_encrypted =  os_RandomString(8);
		$customers_password = os_encrypt_password($customers_password_encrypted);
	}
	$error = false; 

	if (ACCOUNT_GENDER == 'true') {
		if (($customers_gender != 'm') && ($customers_gender != 'f')) {
			$error = true;
			$entry_gender_error = true;
		} else {
			$entry_gender_error = false;
		}
	}

	if (strlen($customers_password) < ENTRY_PASSWORD_MIN_LENGTH) {
		$error = true;
		$entry_password_error = true;
	} else {
		$entry_password_error = false;
	}

	if (($customers_send_mail != 'yes') && ($customers_send_mail != 'no')) {
		$error = true;
		$entry_mail_error = true;
	} else {
		$entry_mail_error = false;
	}

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

		if ($customers_vat_id != '') {

			if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {

				$validate_vatid = validate_vatid($customers_vat_id);

				if ($validate_vatid == '0') {
					if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
						$entry_vat_error = true;
						$error = true;
					}
					$customers_vat_id_status = '0';
				}

				if ($validate_vatid == '1') {
					$customers_vat_id_status = '1';
				}

				if ($validate_vatid == '8') {
					if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
						$entry_vat_error = true;
						$error = true;
					}
					$customers_vat_id_status = '8';
				}

				if ($validate_vatid == '9') {
					if (ACCOUNT_VAT_BLOCK_ERROR == 'true') {
						$entry_vat_error = true;
						$error = true;
					}
					$customers_vat_id_status = '9';
				}

			}

		}
	}
	
	if (os_get_geo_zone_code($entry_country_id) != '6') {
	require_once(get_path('class').'vat_validation.php');
	$vatID = new vat_validation($customers_vat_id, '', '', $entry_country_id);

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

	if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
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
		$sql_data_array = array ('customers_status' => $customers_status_c, 'customers_cid' => $customers_cid, 'customers_vat_id' => $customers_vat_id, 'customers_vat_id_status' => $customers_vat_id_status, 'customers_firstname' => $customers_firstname, 'customers_secondname' => $customers_secondname, 'customers_lastname' => $customers_lastname, 'customers_email_address' => $customers_email_address, 'customers_telephone' => $customers_telephone, 'customers_fax' => $customers_fax, 'payment_unallowed' => $payment_unallowed, 'shipping_unallowed' => $shipping_unallowed, 'customers_password' => $customers_password,'customers_date_added' => 'now()','customers_last_modified' => 'now()');

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['customers_gender'] = $customers_gender;
		if (ACCOUNT_DOB == 'true')
			$sql_data_array['customers_dob'] = os_date_raw($customers_dob);

		os_db_perform(TABLE_CUSTOMERS, $sql_data_array);

		$cc_id = os_db_insert_id();

		$sql_data_array = array ('customers_id' => $cc_id, 'entry_firstname' => $customers_firstname, 'entry_secondname' => $customers_secondname, 'entry_lastname' => $customers_lastname, 'entry_street_address' => $entry_street_address, 'entry_postcode' => $entry_postcode, 'entry_city' => $entry_city, 'entry_country_id' => $entry_country_id,'address_date_added' => 'now()','address_last_modified' => 'now()');

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['entry_gender'] = $customers_gender;
		if (ACCOUNT_COMPANY == 'true')
			$sql_data_array['entry_company'] = $entry_company;
		if (ACCOUNT_SUBURB == 'true')
			$sql_data_array['entry_suburb'] = $entry_suburb;
		if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
			if ($zone_id > 0) {
				$sql_data_array['entry_zone_id'] = $entry_zone_id;
				$sql_data_array['entry_state'] = '';
			} else {
				$sql_data_array['entry_zone_id'] = '0';
				$sql_data_array['entry_state'] = $entry_state;
			}
		}

		os_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

		$address_id = os_db_insert_id();

		os_db_query("update ".TABLE_CUSTOMERS." set customers_default_address_id = '".$address_id."' where customers_id = '".$cc_id."'");

		os_db_query("insert into ".TABLE_CUSTOMERS_INFO." (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('".$cc_id."', '0', now())");

		// Create insert into admin access table if admin is created.
		if ($customers_status_c == '0') {
			//os_db_query("INSERT into ".TABLE_ADMIN_ACCESS." (customers_id,index2) VALUES ('".$cc_id."','1')");
		}

		// Create eMail
		if (($customers_send_mail == 'yes')) {

			// assign language to template for caching
			$osTemplate->assign('language', $_SESSION['language']);
			$osTemplate->caching = false;

			$osTemplate->assign('tpl_path', http_path('themes_c'));
			$osTemplate->assign('logo_path', http_path('themes_c').'img/');

			$osTemplate->assign('NAME', $customers_lastname.' '.$customers_firstname);
			$osTemplate->assign('EMAIL', $customers_email_address);
			$osTemplate->assign('COMMENTS', $customers_mail_comments);
			$osTemplate->assign('PASSWORD', $customers_password_encrypted);

			$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language'].'/create_account_mail.html');
			$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language'].'/create_account_mail.txt');

			os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $customers_email_address, $customers_lastname.' '.$customers_firstname, EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);
		}
		
       os_db_query("delete from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where customers_id=" . (int)$cc_id);
        $extra_fields_query =os_db_query("select ce.fields_id from " . TABLE_EXTRA_FIELDS . " ce where ce.fields_status=1 ");
        while($extra_fields = os_db_fetch_array($extra_fields_query)){
            $sql_extra_data_array = array('customers_id' => (int)$cc_id,
                              'fields_id' => $extra_fields['fields_id'],
                              'value' => $_POST['fields_' . $extra_fields['fields_id'] ]);
            os_db_perform(TABLE_CUSTOMERS_TO_EXTRA_FIELDS, $sql_extra_data_array);
        }

		// Customer profile
		$customerProfileArray = array(
			'customers_id' => $cc_id,
		);
		customerProfile($customerProfileArray, 'new');

		os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID='.$cc_id, 'SSL'));
	}
}
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr><?php echo os_draw_form('customers', FILENAME_CREATE_ACCOUNT, os_get_all_get_params(array('action')) . 'action=edit', 'post', 'onSubmit="return check_form();"') . os_draw_hidden_field('default_address_id', $customers_default_address_id); ?>
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
			echo os_draw_radio_field('customers_gender', 'm', false, $customers_gender).'&nbsp;&nbsp;'.MALE.'&nbsp;&nbsp;'.os_draw_radio_field('customers_gender', 'f', false, $customers_gender).'&nbsp;&nbsp;'.FEMALE.'&nbsp;'.ENTRY_GENDER_ERROR;
		} else {
			echo ($customers_gender == 'm') ? MALE : FEMALE;
			echo os_draw_radio_field('customers_gender', 'm', false, $customers_gender).'&nbsp;&nbsp;'.MALE.'&nbsp;&nbsp;'.os_draw_radio_field('customers_gender', 'f', false, $customers_gender).'&nbsp;&nbsp;'.FEMALE;
		}
	} else {
		echo os_draw_radio_field('customers_gender', 'm', false, $customers_gender).'&nbsp;&nbsp;'.MALE.'&nbsp;&nbsp;'.os_draw_radio_field('customers_gender', 'f', false, $customers_gender).'&nbsp;&nbsp;'.FEMALE;
	}
?></td>
          </tr>
<?php

}
?>
          <tr>
            <td class="main"><?php echo ENTRY_CID; ?></td>
            <td class="main"><?php


echo os_draw_input_field('csID', $customers_cid, 'maxlength="32"');
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
            <td class="main"><?php

if ($error == true) {
	if ($entry_firstname_error == true) {
		echo os_draw_input_field('customers_firstname', $customers_firstname, 'maxlength="32"').'&nbsp;'.ENTRY_FIRST_NAME_ERROR;
	} else {
		echo os_draw_input_field('customers_firstname', $customers_firstname, 'maxlength="32"');
	}
} else {
	echo os_draw_input_field('customers_firstname', $customers_firstname, 'maxlength="32"');
}
?></td>
          </tr>
<?php

if (ACCOUNT_SECOND_NAME == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_SECOND_NAME; ?></td>
            <td class="main"><?php

	echo os_draw_input_field('customers_secondname', $customers_secondname, 'maxlength="32"');

?></td>
          </tr>
<?php

	}
?>
          <tr>
            <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
            <td class="main"><?php

if ($error == true) {
	if ($entry_lastname_error == true) {
		echo os_draw_input_field('customers_lastname', $customers_lastname, 'maxlength="32"').'&nbsp;'.ENTRY_LAST_NAME_ERROR;
	} else {
		echo os_draw_input_field('customers_lastname', $customers_lastname, 'maxlength="32"');
	}
} else {
	echo os_draw_input_field('customers_lastname', $customers_lastname, 'maxlength="32"');
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
			echo os_draw_input_field('customers_dob', os_date_short($customers_dob), 'maxlength="10"').'&nbsp;'.ENTRY_DATE_OF_BIRTH_ERROR;
		} else {
			echo os_draw_input_field('customers_dob', os_date_short($customers_dob), 'maxlength="10"');
		}
	} else {
		echo os_draw_input_field('customers_dob', os_date_short($customers_dob), 'maxlength="10"');
	}
?></td>
          </tr>
<?php

}
?>
          <tr>
            <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
            <td class="main"><?php

if ($error == true) {
	if ($entry_email_address_error == true) {
		echo os_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"').'&nbsp;'.ENTRY_EMAIL_ADDRESS_ERROR;
	}
	elseif ($entry_email_address_check_error == true) {
		echo os_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"').'&nbsp;'.ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
	}
	elseif ($entry_email_address_exists == true) {
		echo os_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"').'&nbsp;'.ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
	} else {
		echo os_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"');
	}
} else {
	echo os_draw_input_field('customers_email_address', $customers_email_address, 'maxlength="96"');
}
?></td>
          </tr>
        </table></td>
      </tr>
<?php

if (ACCOUNT_COMPANY == 'true') {
?>
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
			echo os_draw_input_field('entry_company', $entry_company, 'maxlength="32"').'&nbsp;'.ENTRY_COMPANY_ERROR;
		} else {
			echo os_draw_input_field('entry_company', $entry_company, 'maxlength="32"');
		}
	} else {
		echo os_draw_input_field('entry_company', $entry_company, 'maxlength="32"');
	}
?></td>
          </tr>
<?php

	if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_VAT_ID; ?></td>
            <td class="main"><?php

		if ($error == true) {
			if ($entry_vat_error == true) {
				echo os_draw_input_field('customers_vat_id', $customers_vat_id, 'maxlength="32"').'&nbsp;'.ENTRY_VAT_ERROR;
			} else {
				echo os_draw_input_field('customers_vat_id', $customers_vat_id, 'maxlength="32"');
			}
		} else {
			echo os_draw_input_field('customers_vat_id', $customers_vat_id, 'maxlength="32"');
		}
?></td>
          </tr>
<?php

	}
?>

        </table></td>
      </tr>
<?php

}
?>
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

if ($error == true) {
	if ($entry_street_address_error == true) {
		echo os_draw_input_field('entry_street_address', $entry_street_address, 'maxlength="64"').'&nbsp;'.ENTRY_STREET_ADDRESS_ERROR;
	} else {
		echo os_draw_input_field('entry_street_address', $entry_street_address, 'maxlength="64"');
	}
} else {
	echo os_draw_input_field('entry_street_address', $entry_street_address, 'maxlength="64"');
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
			echo os_draw_input_field('suburb', $entry_suburb, 'maxlength="32"').'&nbsp;'.ENTRY_SUBURB_ERROR;
		} else {
			echo os_draw_input_field('entry_suburb', $entry_suburb, 'maxlength="32"');
		}
	} else {
		echo os_draw_input_field('entry_suburb', $entry_suburb, 'maxlength="32"');
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

if ($error == true) {
	if ($entry_post_code_error == true) {
		echo os_draw_input_field('entry_postcode', $entry_postcode, 'maxlength="8"').'&nbsp;'.ENTRY_POST_CODE_ERROR;
	} else {
		echo os_draw_input_field('entry_postcode', $entry_postcode, 'maxlength="8"');
	}
} else {
	echo os_draw_input_field('entry_postcode', $entry_postcode, 'maxlength="8"');
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

if ($error == true) {
	if ($entry_city_error == true) {
		echo os_draw_input_field('entry_city', $entry_city, 'maxlength="32"').'&nbsp;'.ENTRY_CITY_ERROR;
	} else {
		echo os_draw_input_field('entry_city', $entry_city, 'maxlength="32"');
	}
} else {
	echo os_draw_input_field('entry_city', $entry_city, 'maxlength="32"');
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
                <td class="main"><?php echo os_get_country_list('country',STORE_COUNTRY, 'onChange="changeselect();"') . '&nbsp;' . (defined(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>': ''); ?></td>
              </tr>
<?php
  }
?>
<?php
if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
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

if ($error == true) {
	if ($entry_telephone_error == true) {
		echo os_draw_input_field('customers_telephone', $customers_telephone).'&nbsp;'.ENTRY_TELEPHONE_NUMBER_ERROR;
	} else {
		echo os_draw_input_field('customers_telephone', $customers_telephone);
	}
} else {
	echo os_draw_input_field('customers_telephone', $customers_telephone);
}
?></td>
          </tr>
<?php

	}
?>
<?php

	if (ACCOUNT_FAX == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_FAX_NUMBER; ?></td>
            <td class="main"><?php echo os_draw_input_field('customers_fax'); ?></td>
          </tr>
<?php

	}
?>
        </table></td>
      </tr>

      <?php echo os_get_extra_fields($_GET['cID'],$_SESSION['languages_id']); ?>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_OPTIONS; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
            <td class="main"><?php

if ($processed == true) {
	echo os_draw_hidden_field('status');
} else {
	echo os_draw_pull_down_menu('status', $customers_statuses_array, 2);
}
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_MAIL; ?></td>
            <td class="main">
<?php

if ($error == true) {
	if ($entry_mail_error == true) {
		echo os_draw_radio_field('customers_mail', 'yes', true, $customers_send_mail).'&nbsp;&nbsp;'.YES.'&nbsp;&nbsp;'.os_draw_radio_field('customers_mail', 'no', false, $customers_send_mail).'&nbsp;&nbsp;'.NO.'&nbsp;'.ENTRY_MAIL_ERROR;
	} else {
		echo os_draw_radio_field('customers_mail', 'yes', true, $customers_send_mail).'&nbsp;&nbsp;'.YES.'&nbsp;&nbsp;'.os_draw_radio_field('customers_mail', 'no', false, $customers_send_mail).'&nbsp;&nbsp;'.NO;
	}
} else {
	echo os_draw_radio_field('customers_mail', 'yes', true, $customers_send_mail).'&nbsp;&nbsp;'.YES.'&nbsp;&nbsp;'.os_draw_radio_field('customers_mail', 'no', false, $customers_send_mail).'&nbsp;&nbsp;'.NO;
}
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_PAYMENT_UNALLOWED; ?></td>
            <td class="main"><?php echo os_draw_input_field('payment_unallowed'); ?></td>
          </tr>
           <tr>
            <td class="main"><?php echo ENTRY_SHIPPING_UNALLOWED; ?></td>
            <td class="main"><?php echo os_draw_input_field('shipping_unallowed'); ?></td>
          </tr>
            <td class="main" bgcolor="#FFCC33"><?php echo ENTRY_PASSWORD; ?></td>
            <td class="main" bgcolor="#FFCC33"><?php

if ($error == true) {
	if ($entry_password_error == true) {
		echo os_draw_password_field('entry_password', $customers_password_encrypted).'&nbsp;'.ENTRY_PASSWORD_ERROR;
	} else {
		echo os_draw_password_field('entry_password', $customers_password_encrypted);
	}
} else {
	echo os_draw_password_field('entry_password', $customers_password_encrypted);
}
?></td>
          </tr>
            <td class="main" valign="top"><?php echo ENTRY_MAIL_COMMENTS; ?></td>
            <td class="main"><?php echo os_draw_textarea_field('mail_comments', 'soft', '60', '5', $mail_comments); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td align="right" class="main"><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '">'.BUTTON_INSERT.'</button></span><a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array('action'))) .'"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>
      </tr></form>
      </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>