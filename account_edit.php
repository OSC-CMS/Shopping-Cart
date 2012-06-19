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

include ('includes/top.php');

if (!isset ($_SESSION['customer_id']))
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));
	
if ($_SESSION['customers_status']['customers_status_id']==0)
	os_redirect(os_href_link_admin(FILENAME_CUSTOMERS, 'cID='.$_SESSION['customer_id'].'&action=edit', 'SSL'));

if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {
	if (ACCOUNT_GENDER == 'true')
		$gender = os_db_prepare_input($_POST['gender']);
	$firstname = os_db_prepare_input($_POST['firstname']);
	if (ACCOUNT_SECOND_NAME == 'true')
	$secondname = os_db_prepare_input($_POST['secondname']);
	$lastname = os_db_prepare_input($_POST['lastname']);
	if (ACCOUNT_DOB == 'true')
		$dob = os_db_prepare_input($_POST['dob']);
	if (ACCOUNT_COMPANY_VAT_CHECK == 'true')
		$vat = os_db_prepare_input($_POST['vat']);
	$email_address = os_db_prepare_input($_POST['email_address']);
	$telephone = os_db_prepare_input($_POST['telephone']);
	$fax = os_db_prepare_input($_POST['fax']);
    os_get_extra_fields($_SESSION['customer_id'], $_SESSION['languages_id']);

	$error = false;

	if (ACCOUNT_GENDER == 'true') {
		if (($gender != 'm') && ($gender != 'f')) {
			$error = true;
			$messageStack->add('account_edit', ENTRY_GENDER_ERROR);
		}
	}

	if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;
		$messageStack->add('account_edit', ENTRY_FIRST_NAME_ERROR);
	}

	if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
		$error = true;
		$messageStack->add('account_edit', ENTRY_LAST_NAME_ERROR);
	}

	if (ACCOUNT_DOB == 'true') {
		if (checkdate(substr(os_date_raw($dob), 4, 2), substr(os_date_raw($dob), 6, 2), substr(os_date_raw($dob), 0, 4)) == false) {
			$error = true;
			$messageStack->add('account_edit', ENTRY_DATE_OF_BIRTH_ERROR);
		}
	}

	// New VAT Check
	$country = os_get_customers_country($_SESSION['customer_id']);
	require_once(_CLASS.'vat_validation.php');
	$vatID = new vat_validation($vat, $_SESSION['customer_id'], '', $country);

	$customers_status = $vatID->vat_info['status'];
	$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
	$error = $vatID->vat_info['error'];

	if($error==1){
	$messageStack->add('account_edit', ENTRY_VAT_ERROR);
	$error = true;
  }

// New VAT CHECK END


	if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;
		$messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR);
	}

	if (os_validate_email($email_address) == false) {
		$error = true;
		$messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
	}

	if (ACCOUNT_TELE == 'true') {
	if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
		$error = true;
		$messageStack->add('account_edit', ENTRY_TELEPHONE_NUMBER_ERROR);
	}
  }

        $extra_fields_query = osDBquery("select ce.fields_id, ce.fields_input_type, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_size from " . TABLE_EXTRA_FIELDS . " ce, " . TABLE_EXTRA_FIELDS_INFO . " cei where ce.fields_status=1 and ce.fields_required_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =" . $_SESSION['languages_id']);

   while($extra_fields = os_db_fetch_array($extra_fields_query,true)){
   
    if(strlen($_POST['fields_' . $extra_fields['fields_id'] ])<$extra_fields['fields_size']){
      $error = true;
      $string_error=sprintf(ENTRY_EXTRA_FIELDS_ERROR,$extra_fields['fields_name'],$extra_fields['fields_size']);
      $messageStack->add('account_edit', $string_error);
    }
  }

	if ($error == false) {
		$sql_data_array = array ('customers_vat_id' => $vat, 'customers_vat_id_status' => (int) $customers_vat_id_status, 'customers_firstname' => $firstname, 'customers_secondname' => $secondname, 'customers_lastname' => $lastname, 'customers_email_address' => $email_address, 'customers_telephone' => $telephone, 'customers_fax' => $fax,'customers_last_modified' => 'now()');

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['customers_gender'] = $gender;
		if (ACCOUNT_DOB == 'true')
			$sql_data_array['customers_dob'] = os_date_raw($dob);

		os_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '".(int) $_SESSION['customer_id']."'");

		os_db_query("update ".TABLE_CUSTOMERS_INFO." set customers_info_date_account_last_modified = now() where customers_info_id = '".(int) $_SESSION['customer_id']."'");

     $customers_id = (int)$_SESSION['customer_id'];
      os_db_query("delete from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where customers_id=" . (int)$customers_id);
   	  	$extra_fields_query = os_db_query("select ce.fields_id from " . TABLE_EXTRA_FIELDS . " ce where ce.fields_status=1 ");
    	  while($extra_fields = os_db_fetch_array($extra_fields_query))
				{
				  if(isset($_POST['fields_' . $extra_fields['fields_id']])){
            $sql_data_array = array('customers_id' => (int)$customers_id,
                              'fields_id' => $extra_fields['fields_id'],
                              'value' => $_POST['fields_' . $extra_fields['fields_id']]);
       		}
       		else
					{
					  $sql_data_array = array('customers_id' => (int)$customers_id,
                              'fields_id' => $extra_fields['fields_id'],
                              'value' => '');
						$is_add = false;
						for($i = 1; $i <= $_POST['fields_' . $extra_fields['fields_id'] . '_total']; $i++)
						{
							if(isset($_POST['fields_' . $extra_fields['fields_id'] . '_' . $i]))
							{
							  if($is_add)
							  {
                  $sql_data_array['value'] .= "\n";
								}
								else
								{
                  $is_add = true;
								}
              	$sql_data_array['value'] .= $_POST['fields_' . $extra_fields['fields_id'] . '_' . $i];
							}
						}
					}

					os_db_perform(TABLE_CUSTOMERS_TO_EXTRA_FIELDS, $sql_data_array);
      	}

		// reset the session variables
		$customer_first_name = $firstname;
		$messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');
		os_redirect(os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
	}
} else {
	$account_query = os_db_query("select customers_gender, customers_cid, customers_vat_id, customers_vat_id_status, customers_firstname, customers_secondname, customers_lastname, customers_dob, customers_email_address, customers_telephone, customers_fax from ".TABLE_CUSTOMERS." where customers_id = '".(int) $_SESSION['customer_id']."'");
	$account = os_db_fetch_array($account_query);
}

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_EDIT, os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_EDIT, os_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));

require(_INCLUDES.'header.php');
$osTemplate->assign('FORM_ACTION', os_draw_form('account_edit', os_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post', 'onsubmit="return checkform(this);"').os_draw_hidden_field('action', 'process') . os_draw_hidden_field('required', 'gender,firstname,lastname,dob,email,telephone', 'id="required"'));

if ($messageStack->size('account_edit') > 0)
	$osTemplate->assign('error', $messageStack->output('account_edit'));

if (ACCOUNT_GENDER == 'true') {
	$osTemplate->assign('gender', '1');
	$male = ($account['customers_gender'] == 'm') ? true : false;
	$female = !$male;
	$osTemplate->assign('INPUT_MALE', os_draw_radio_field(array ('name' => 'gender', 'suffix' => MALE.'&nbsp;'), 'm', $male, 'id="gender" checked="checked"'));
	$osTemplate->assign('INPUT_FEMALE', os_draw_radio_field(array ('name' => 'gender', 'suffix' => FEMALE.'&nbsp;', 'text' => (os_not_null(ENTRY_GENDER_TEXT) ? '<span class="Requirement">'.ENTRY_GENDER_TEXT.'</span>' : '')), 'f', $female, 'id="gender"'));
	$osTemplate->assign('ENTRY_GENDER_ERROR', ENTRY_GENDER_ERROR);
}

if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
	$osTemplate->assign('vat', '1');
	$osTemplate->assign('INPUT_VAT', os_draw_input_fieldNote(array ('name' => 'vat', 'text' => '&nbsp;'. (os_not_null(ENTRY_VAT_TEXT) ? '<span class="Requirement">'.ENTRY_VAT_TEXT.'</span>' : '')), $account['customers_vat_id']));
} else {
	$osTemplate->assign('vat', '0');
}

$osTemplate->assign('INPUT_FIRSTNAME', os_draw_input_fieldNote(array ('name' => 'firstname', 'text' => '&nbsp;'. (os_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_FIRST_NAME_TEXT.'</span>' : '')), $account['customers_firstname'], 'id="firstname"'));
$osTemplate->assign('ENTRY_FIRST_NAME_ERROR', ENTRY_FIRST_NAME_ERROR);
if (ACCOUNT_SECOND_NAME == 'true') {
	$osTemplate->assign('secondname', '1');
$osTemplate->assign('INPUT_SECONDNAME', os_draw_input_fieldNote(array ('name' => 'secondname', 'text' => '&nbsp;'. (os_not_null(ENTRY_SECOND_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_SECOND_NAME_TEXT.'</span>' : '')), $account['customers_secondname'], 'id="secondname"'));
}
$osTemplate->assign('INPUT_LASTNAME', os_draw_input_fieldNote(array ('name' => 'lastname', 'text' => '&nbsp;'. (os_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_LAST_NAME_TEXT.'</span>' : '')), $account['customers_lastname'], 'id="lastname"'));
$osTemplate->assign('ENTRY_LAST_NAME_ERROR', ENTRY_LAST_NAME_ERROR);
$osTemplate->assign('csID', $account['customers_cid']);

if (ACCOUNT_DOB == 'true') {
	$osTemplate->assign('birthdate', '1');
	$osTemplate->assign('INPUT_DOB', os_draw_input_fieldNote(array ('name' => 'dob', 'text' => '&nbsp;'. (os_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="Requirement">'.ENTRY_DATE_OF_BIRTH_TEXT.'</span>' : '')), os_date_short($account['customers_dob']), 'id="dob"'));
   $osTemplate->assign('ENTRY_DATE_OF_BIRTH_ERROR', ENTRY_DATE_OF_BIRTH_ERROR);
}

$osTemplate->assign('INPUT_EMAIL', os_draw_input_fieldNote(array ('name' => 'email_address', 'text' => '&nbsp;'. (os_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="Requirement">'.ENTRY_EMAIL_ADDRESS_TEXT.'</span>' : '')), $account['customers_email_address'], 'id="email"'));
$osTemplate->assign('ENTRY_EMAIL_ADDRESS_ERROR', ENTRY_EMAIL_ADDRESS_ERROR);

if (ACCOUNT_TELE == 'true') {
	$osTemplate->assign('telephone', '1');
   $osTemplate->assign('INPUT_TEL', os_draw_input_fieldNote(array ('name' => 'telephone', 'text' => '&nbsp;'. (os_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="Requirement">'.ENTRY_TELEPHONE_NUMBER_TEXT.'</span>' : '')), $account['customers_telephone'], 'id="telephone"'));
   $osTemplate->assign('ENTRY_TELEPHONE_NUMBER_ERROR', ENTRY_TELEPHONE_NUMBER_ERROR);
}

if (ACCOUNT_FAX == 'true') {
	$osTemplate->assign('fax', '1');
   $osTemplate->assign('INPUT_FAX', os_draw_input_fieldNote(array ('name' => 'fax', 'text' => '&nbsp;'. (os_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="Requirement">'.ENTRY_FAX_NUMBER_TEXT.'</span>' : '')), $account['customers_fax']));
}

	$osTemplate->assign('customers_extra_fileds', '1');
   $osTemplate->assign('INPUT_CUSTOMERS_EXTRA_FIELDS', os_get_extra_fields($_SESSION['customer_id'],$_SESSION['languages_id']));

   
   	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_ACCOUNT, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
$osTemplate->assign('BUTTON_BACK', $_array['code']);


$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());
$osTemplate->assign('FORM_END', '</form>');
$osTemplate->assign('language', $_SESSION['language']);

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/account_edit.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ACCOUNT_EDIT.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ACCOUNT_EDIT.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>