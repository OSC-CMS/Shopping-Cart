<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCustomers extends CartET
{
	/**
	 * Изменение статуса покупателя
	 */
	public function changeStatus($params = array())
	{
		if (empty($params)) return false;

		$customers_id = (isset($params['customers_id'])) ? $params['customers_id'] : $params['pk'];
		$status = (isset($params['status'])) ? $params['status'] : $params['value'];

		$check_status_query = os_db_query("SELECT * FROM ".TABLE_CUSTOMERS." WHERE customers_id = '".(int)$customers_id."'");
		$check_status = os_db_fetch_array($check_status_query);

		if ($check_status['customers_status'] != $status)
		{
			if ($check_status['customers_email_address']) {
				require _LIB . 'phpmailer/PHPMailerAutoload.php';
				include_once(_LIB . 'phpmailer/func.mail.php');

				$osTemplate = new osTemplate;
				$osTemplate->assign('language', $_SESSION['language']);
				$osTemplate->assign('status', os_get_customers_status_name($status));

				$osTemplate->caching = false;
				$html_mail = $osTemplate->fetch(_MAIL . $_SESSION['language'] . '/change_customer_status.html');
				$txt_mail = $osTemplate->fetch(_MAIL . $_SESSION['language'] . '/change_customer_status.txt');

				os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $check_status['customers_email_address'], $check_status['customers_firstname'] . ' ' . $check_status['customers_lastname'], EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);
			}

			os_db_query("update ".TABLE_CUSTOMERS." set customers_status = '".(int)$status."' where customers_id = '".(int)$customers_id."'");

			if ($status == 0)
			{
				$q = os_db_query("select * from ".TABLE_ADMIN_ACCESS." where customers_id='".(int)$customers_id."'");
				if (!os_db_num_rows($q))		   
					os_db_query("INSERT into ".TABLE_ADMIN_ACCESS." (customers_id, index2) VALUES ('".(int)$customers_id."', '1')");
			}
			else
			{
				os_db_query("DELETE FROM ".TABLE_ADMIN_ACCESS." WHERE customers_id = '".(int)$customers_id."'");
			}

			os_db_query("insert into ".TABLE_CUSTOMERS_STATUS_HISTORY." (customers_id, new_value, old_value, date_added, customer_notified) values ('".(int)$customers_id."', '".(int)$status."', '".$check_status['customers_status']."', now(), '0')");

			$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Группы покупателя для быстрого изменения
	 */
	public function getStatus()
	{
		$status = os_get_customers_statuses();
		$result = array();
		foreach ($status as $s) {
			$result[] = array('value' => $s['id'], 'text' => $s['text']);
		}
		return $result;
	}

	/**
	 * Удаление покупателя
	 */
	public function delete($params = array())
	{
		if (empty($params)) return false;

		$id = $params['id'];
		$reviews = $params['reviews'];

		if ($reviews == 'on')
		{
			$reviews_query = os_db_query("select reviews_id from ".TABLE_REVIEWS." WHERE customers_id = '".(int)$id."'");
			while ($r = os_db_fetch_array($reviews_query))
			{
				os_db_query("DELETE FROM ".TABLE_REVIEWS_DESCRIPTION." WHERE reviews_id = '".(int)$r['reviews_id']."'");
			}
			os_db_query("DELETE FROM ".TABLE_REVIEWS." WHERE customers_id = '".(int)$id."'");
		}
		else
			os_db_query("update ".TABLE_REVIEWS." set customers_id = null WHERE customers_id = '".(int)$id."'");

		os_db_query("DELETE FROM ".TABLE_ADDRESS_BOOK." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_INFO." WHERE customers_info_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_PRODUCTS_NOTIFICATIONS." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_WHOS_ONLINE." WHERE customer_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_STATUS_HISTORY." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_IP." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_ADMIN_ACCESS." WHERE customers_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".DB_PREFIX."customers_profile WHERE customers_id = '".(int)$id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Возвращает массив дополнительный полей покупателя
	 */
	public function getExtraFields($customer_id, $languages_id)
	{
		$extra_fields_query = os_db_query("select ce.fields_id, ce.fields_input_type, ce.fields_input_value, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type from ".TABLE_EXTRA_FIELDS." ce, ".TABLE_EXTRA_FIELDS_INFO." cei where ce.fields_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =".$languages_id);

		$result = array();
		if (os_db_num_rows($extra_fields_query) > 0)
		{
			while($extra_fields = os_db_fetch_array($extra_fields_query))
			{
				$value = '';
				$value_list = array();
				if (isset($customer_id))
				{
					$value_query = os_db_query("select value from ".TABLE_CUSTOMERS_TO_EXTRA_FIELDS." where customers_id=".(int)$customer_id." and fields_id=".(int)$extra_fields['fields_id']);
					$value_info = os_db_fetch_array($value_query);
					$value_list = explode("\n", $value_info['value']);
					for($i = 0, $n = sizeof($value_list); $i < $n; $i++)
					{
						$value_list[$i] = trim($value_list[$i]);
					}
					$value = $value_list[0];
				}

				$select_values_list = explode("\n", $extra_fields['fields_input_value']);
				$select_values = array();
				foreach($select_values_list as $item)
				{
					$item = trim($item);
					$select_values[] = array('id' => $item, 'text' => $item);
				}

				$required_status = $extra_fields['fields_required_status'];
				$required = ($required_status == 1) ? 'data-required="true"' : '';
				$fieldId = 'fields_'.$extra_fields['fields_id'];

				switch($extra_fields['fields_input_type'])
				{
					case 0:
						$return = '<input class="span12" type="text" id="'.$fieldId.'" name="'.$fieldId.'" value="'.$value.'" '.$required.'>';
					break;

					case 1:
						$return = '<textarea class="span12" id="'.$fieldId.'" name="'.$fieldId.'" '.$required.'>'.$value.'</textarea>';
					break;

					case 2:
						$return = '';
						$selected = '';
						foreach($select_values_list as $item)
						{
							$item = trim($item);
							$selected = ($value == $item) ? 'checked="checked"' : '';
							$return .= '<label class="radio"><input type="radio" name="'.$fieldId.'" value="'.$item.'" '.$selected.'> '.$item.'</label>';
							$extra_fields['fields_required_status'] = 0;
						}
					break;

					case 3:
						$cnt = 1;
						$return = '';
						foreach($select_values_list as $item)
						{
							$item = trim($item);
							$selected = (in_array($item, $value_list)) ? 'checked="checked"' : '';
							$return .= '<label class="checkbox"><input type="checkbox" name="'.$fieldId.'_'.($cnt++).'" value="'.$item.'" '.$selected.'> '.$item.'</label>';
							//$return .= os_draw_selection_field('fields_'.$extra_fields['fields_id'].'_'.($cnt++), 'checkbox', $item, ((in_array($item, $value_list))?(true):(false))).$item. (($extra_fields['fields_required_status']==1) ? '<span class="inputRequirement">*</span>': '').'<br>';
							$extra_fields['fields_required_status']  = 0;
						}
						$return .= os_draw_hidden_field('fields_'.$extra_fields['fields_id'].'_total', $cnt);
					break;

					case 4: $return = os_draw_pull_down_menu('fields_'.$extra_fields['fields_id'], $select_values, $value).(($extra_fields['fields_required_status']==1) ? '<span class="inputRequirement">*</span>': ''); break;

					default: $return = os_draw_input_field('fields_'.$extra_fields['fields_id'],$value).(($extra_fields['fields_required_status']==1) ? '<span class="inputRequirement">*</span>': ''); break;
				}

				$result[] = array(
					'id' => $fieldId,
					'name' => $extra_fields['fields_name'],
					'value' => $return,
					'required' => $required_status
				);
			}
		}
		return $result;
	}

	/**
	 * Добавление заметки о покупателе
	 */
	public function addMemo($params)
	{
		if (empty($params)) return false;

		$memo_title = os_db_prepare_input($params['memo_title']);
		$memo_text = os_db_prepare_input($params['memo_text']);

		if ($memo_text != '')
		{
			$sql_data_array = array(
				'customers_id' => (int)$params['customers_id'],
				'memo_date' => date("Y-m-d"),
				'memo_title' => $memo_title,
				'memo_text' => $memo_text,
				'poster_id' => (int)$_SESSION['customer_id']
			);
			os_db_perform(TABLE_CUSTOMERS_MEMO, $sql_data_array);

			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Добавление или обновление покупателя
	 */
	public function saveCustomer($params)
	{
		$customers_id = os_db_prepare_input($params['cID']);
		$customers_cid = os_db_prepare_input($params['csID']);
		$customers_vat_id = os_db_prepare_input($params['customers_vat_id']);
		$customers_vat_id_status = os_db_prepare_input($params['customers_vat_id_status']);
		$customers_status = os_db_prepare_input($params['status']);
		$customers_status_old = os_db_prepare_input($params['status_old']);
		$customers_firstname = os_db_prepare_input($params['customers_firstname']);
		$customers_username = os_db_prepare_input($params['customers_username']);
		$customers_secondname = os_db_prepare_input($params['customers_secondname']);
		$customers_lastname = os_db_prepare_input($params['customers_lastname']);
		$customers_email_address = os_db_prepare_input($params['customers_email_address']);
		$customers_telephone = os_db_prepare_input($params['customers_telephone']);
		$customers_fax = os_db_prepare_input($params['customers_fax']);
		$customers_newsletter = os_db_prepare_input($params['customers_newsletter']);
		$customers_gender = os_db_prepare_input($params['customers_gender']);
		$customers_dob = os_db_prepare_input($params['customers_dob']);
		$default_address_id = os_db_prepare_input($params['default_address_id']);
		$entry_street_address = os_db_prepare_input($params['entry_street_address']);
		$entry_suburb = os_db_prepare_input($params['entry_suburb']);
		$entry_postcode = os_db_prepare_input($params['entry_postcode']);
		$entry_city = os_db_prepare_input($params['entry_city']);
		$entry_country_id = os_db_prepare_input($params['entry_country_id']);
		$entry_company = os_db_prepare_input($params['entry_company']);
		$entry_state = os_db_prepare_input($params['entry_state']);
		$entry_zone_id = os_db_prepare_input($params['entry_zone_id']);
		$payment_unallowed = os_db_prepare_input($params['payment_unallowed']);
		$shipping_unallowed = os_db_prepare_input($params['shipping_unallowed']);
		$customers_send_mail = os_db_prepare_input($_POST['customers_mail']);
		$mail_comments = os_db_prepare_input($params['mail_comments']);

		if ($params['entry_password'] == '' && $params['action'] == 'new')
		{
			$password = os_RandomString(8);
		}
		else
			$password = $params['entry_password'];

		$error = false;

		if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH)
		{
			$error = true;
		}

		if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH)
		{
			$error = true;
		}

		if (ACCOUNT_DOB == 'true')
		{
			if (empty($customers_dob))
			{
				$error = true;
			}
		}

		if (os_get_geo_zone_code($entry_country_id) != '6')
		{
			require_once( get_path('class').'vat_validation.php');
			$vatID = new vat_validation($customers_vat_id, $customers_id, '', $entry_country_id);

			$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
			$error = $vatID->vat_info['error'];

			if ($error == 1)
			{
				$error = true;
			}
		}

		if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH)
		{
			$error = true;
		}

		if (!os_validate_email($customers_email_address))
		{
			$error = true;
		}

		if (ACCOUNT_STREET_ADDRESS == 'true')
		{
			if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH)
			{
				$error = true;
			}
		}

		if (ACCOUNT_POSTCODE == 'true')
		{
			if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH)
			{
				$error = true;
			}
		}

		if (ACCOUNT_CITY == 'true')
		{
			if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH)
			{
				$error = true;
			}
		}

		if (isset($params['country']))
			$entry_country_id = $params['country'];
		else
			$entry_country_id = STORE_COUNTRY;

		$entry_state = $params['state'];

		if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true')
		{
			$zone_id = 0;
			$check_query = os_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".os_db_input($entry_country_id)."'");
			$check_value = os_db_fetch_array($check_query);

			if ($check_value['total'] > 0)
			{
				$zone_query = os_db_query("select zone_id from ".TABLE_ZONES." where zone_country_id = '".os_db_input($entry_country_id)."' and zone_name = '".os_db_input($entry_state)."'");
				if (os_db_num_rows($zone_query) == 1)
				{
					$zone_values = os_db_fetch_array($zone_query);
					$entry_zone_id = $zone_values['zone_id'];
				}
				else
				{
					$zone_query = os_db_query("select zone_id from ".TABLE_ZONES." where zone_country_id = '".os_db_input($entry_country)."' and zone_code = '".os_db_input($entry_state)."'");
					if (os_db_num_rows($zone_query) >= 1)
					{
						$zone_values = os_db_fetch_array($zone_query);
						$zone_id = $zone_values['zone_id'];
					}
					else
					{
						$error = true;
					}
				}
			}
			else
			{
				if (empty($entry_state))
				{
					$error = true;
				}
			}
		}

		if (ACCOUNT_TELE == 'true')
		{
			if (strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH)
			{
				$error = true;
			}
		}

		$check_email = os_db_query("select customers_email_address from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($customers_email_address)."' and customers_id <> '".os_db_input($customers_id)."'");
		if (os_db_num_rows($check_email))
		{
			$error = true;
		}

		$extra_fields_query = os_db_query("select ce.fields_id, ce.fields_input_type, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_size from ".TABLE_EXTRA_FIELDS." ce, ".TABLE_EXTRA_FIELDS_INFO." cei where ce.fields_status=1 and ce.fields_required_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =".(int)$_SESSION['languages_id']);
		$string_error = '';
		while($extra_fields = os_db_fetch_array($extra_fields_query))
		{
			if (strlen($params['fields_'.$extra_fields['fields_id'] ]) < $extra_fields['fields_size'])
			{
				$error = true;
				$string_error .= sprintf(ENTRY_EXTRA_FIELDS_ERROR,$extra_fields['fields_name'],$extra_fields['fields_size']).'<br>';
			}
		}

		if ($error == false)
		{
			$sql_data_array = array(
				'customers_firstname' => $customers_firstname,
				'customers_secondname' => $customers_secondname,
				'customers_cid' => $customers_cid,
				'customers_vat_id' => $customers_vat_id,
				'customers_vat_id_status' => (int)$customers_vat_id_status,
				'customers_status' => (int)$customers_status,
				'customers_lastname' => $customers_lastname,
				'customers_email_address' => $customers_email_address,
				'customers_telephone' => $customers_telephone,
				'customers_fax' => $customers_fax,
				'payment_unallowed' => $payment_unallowed,
				'shipping_unallowed' => $shipping_unallowed,
				'customers_newsletter' => $customers_newsletter,
				'customers_last_modified' => 'now()',
				'customers_username' => $customers_username,
			);

			// if new password is set
			if ($password != "")
			{
				$sql_data_array['customers_password'] = os_encrypt_password($password);
			}

			if (ACCOUNT_GENDER == 'true')
			{
				$sql_data_array['customers_gender'] = $customers_gender;
			}

			if (ACCOUNT_DOB == 'true')
			{
				$sql_data_array['customers_dob'] = $customers_dob;
			}

			if ($params['action'] == 'new')
			{
				$sql_data_array['customers_date_added'] = 'now()';
				os_db_perform(TABLE_CUSTOMERS, $sql_data_array);
				$customers_id = os_db_insert_id();
			}
			else
			{
				os_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '".os_db_input($customers_id)."'");
			}

			if ($params['action'] == 'new')
				os_db_query("insert into ".TABLE_CUSTOMERS_INFO." (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('".os_db_input($customers_id)."', '0', now())");
			else
				os_db_query("update ".TABLE_CUSTOMERS_INFO." set customers_info_date_account_last_modified = now() where customers_info_id = '".os_db_input($customers_id)."'");

			// Profile
			$sqlDataArray = array(
				'customers_id' => os_db_prepare_input($customers_id),
				'customers_signature' => os_db_prepare_input($params['customers_signature']),
				'show_gender' => os_db_prepare_input($params['show_gender']),
				'show_firstname' => os_db_prepare_input($params['show_firstname']),
				'show_secondname' => os_db_prepare_input($params['show_secondname']),
				'show_lastname' => os_db_prepare_input($params['show_lastname']),
				'show_dob' => os_db_prepare_input($params['show_dob']),
				'show_email' => os_db_prepare_input($params['show_email']),
				'show_telephone' => os_db_prepare_input($params['show_telephone']),
				'show_fax' => os_db_prepare_input($params['show_fax']),
				'customers_avatar' => os_db_prepare_input($params['customers_avatar']),
				'customers_photo' => '',
			);

			if ($params['action'] == 'new')
				os_db_perform(DB_PREFIX."customers_profile", $sqlDataArray);
			else
				os_db_perform(DB_PREFIX."customers_profile", $sqlDataArray, 'update', "customers_id = '".(int)$customers_id."'");
			// Profile

			if ($entry_zone_id > 0)
				$entry_state = '';

			$sql_data_array = array(
				'customers_id' => $customers_id,
				'entry_firstname' => $customers_firstname,
				'entry_secondname' => $customers_secondname,
				'entry_lastname' => $customers_lastname,
				'entry_street_address' => $entry_street_address,
				'entry_postcode' => $entry_postcode,
				'entry_city' => $entry_city,
				'entry_country_id' => (int)$entry_country_id,
				'address_last_modified' => 'now()'
			);

			if (ACCOUNT_COMPANY == 'true')
				$sql_data_array['entry_company'] = $entry_company;

			if ($params['action'] == 'new')
				$sql_data_array['address_date_added'] = 'now()';

			if (ACCOUNT_SUBURB == 'true')
				$sql_data_array['entry_suburb'] = $entry_suburb;

			if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true')
			{
				if ($entry_zone_id > 0)
				{
					$sql_data_array['entry_zone_id'] = (int)$entry_zone_id;
					$sql_data_array['entry_state'] = '';
				}
				else
				{
					$sql_data_array['entry_zone_id'] = 0;
					$sql_data_array['entry_state'] = $entry_state;
				}
			}

			if ($params['action'] == 'new')
			{
				os_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
				$address_id = os_db_insert_id();
				os_db_query("update ".TABLE_CUSTOMERS." set customers_default_address_id = '".$address_id."' where customers_id = '".$customers_id."'");
			}
			else
				os_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '".os_db_input($customers_id)."' and address_book_id = '".os_db_input($default_address_id)."'");

			if (empty($params['action']))
				os_db_query("delete from ".TABLE_CUSTOMERS_TO_EXTRA_FIELDS." where customers_id=".(int)$customers_id);

			$extra_fields_query = os_db_query("select ce.fields_id from ".TABLE_EXTRA_FIELDS." ce where ce.fields_status=1 ");
			while($extra_fields = os_db_fetch_array($extra_fields_query))
			{
				$sql_data_array = array(
					'customers_id' => (int)$customers_id,
					'fields_id' => $extra_fields['fields_id'],
					'value' => $params['fields_'.$extra_fields['fields_id'] ]
				);
				os_db_perform(TABLE_CUSTOMERS_TO_EXTRA_FIELDS, $sql_data_array);
			}

			if (($customers_status != $customers_status_old && !empty($customers_status_old)) OR $customers_send_mail == 'yes')
			{
				require _LIB . 'phpmailer/PHPMailerAutoload.php';
				include_once(_LIB . 'phpmailer/func.mail.php');

				$osTemplate = new osTemplate;
				$osTemplate->assign('language', $_SESSION['language']);
			}

			if ($customers_status != $customers_status_old && !empty($customers_status_old))
			{
				$osTemplate->assign('status', os_get_customers_status_name($customers_status));

				$osTemplate->caching = false;
				$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/change_customer_status.html');
				$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/change_customer_status.txt');

				os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $customers_email_address, $customers_lastname.' '.$customers_firstname, EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);
			}

			// Отправляем email пользователю
			if ($customers_send_mail == 'yes')
			{
				$osTemplate->assign('tpl_path', http_path('themes_c'));
				$osTemplate->assign('logo_path', http_path('themes_c').'img/');
				$osTemplate->assign('NAME', $customers_lastname.' '.$customers_firstname);
				$osTemplate->assign('EMAIL', $customers_email_address);
				$osTemplate->assign('COMMENTS', $mail_comments);
				$osTemplate->assign('PASSWORD', $password);

				$osTemplate->caching = false;
				$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language'].'/create_account_mail.html');
				$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language'].'/create_account_mail.txt');

				os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $customers_email_address, $customers_lastname.' '.$customers_firstname, EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);
			}

			if ($params['action'] == 'new')
				$data = array('msg' => 'Успешно изменено!', 'type' => 'ok', 'urlBack' => FILENAME_CUSTOMERS);
			else
				$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		elseif ($error == true)
			$data = array('msg' => (!empty($string_error)) ? $string_error : 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Изменение статуса дополнительных полей
	 */
	public function fieldStatus($params)
	{
		if (is_array($params))
		{
			os_db_query("UPDATE ".TABLE_EXTRA_FIELDS." SET ".os_db_prepare_input($params['column'])." = '".(int)$params['status']."' WHERE fields_id = '".(int)$params['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Удаление дополнительных полей
	 */
	public function deleteField($params)
	{
		if (empty($params)) return false;

		$id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("DELETE FROM ".TABLE_EXTRA_FIELDS." WHERE fields_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_EXTRA_FIELDS_INFO." WHERE fields_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_TO_EXTRA_FIELDS." WHERE fields_id = '".(int)$id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Добавление и изменение поля
	 */
	public function saveField($params)
	{
		if (empty($params)) return false;

		$action = $params['action'];

		if ($action != 'new')
			$fields_id = os_db_prepare_input($params['field_id']);

		$sql_data_array = array(
			'fields_status' => (int)$params['fields_input_type'],
			'fields_input_type' => (int)$params['fields_input_type'],
			'fields_input_value' => os_db_prepare_input($params['fields_input_value']),
			'fields_required_status' => (int)$params['fields_required_status'],
			'fields_size' => os_db_prepare_input($params['fields_size']),
			'fields_required_email' => (int)$params['fields_required_email']
		);

		if ($action == 'new')
		{
			os_db_perform(TABLE_EXTRA_FIELDS, $sql_data_array);
			$fields_id = os_db_insert_id();
		}
		else
			os_db_perform(TABLE_EXTRA_FIELDS, $sql_data_array, 'update', "fields_id = '".(int)$fields_id."'");

		if (is_array($params['lang']))
		{
			foreach ($params['lang'] as $id => $value)
			{
				$sql_data_array = array(
					'fields_id' => (int)$fields_id,
					'fields_name' => os_db_prepare_input($value),
					'languages_id' => (int)$id
				);

				if ($action == 'new')
					os_db_perform(TABLE_EXTRA_FIELDS_INFO, $sql_data_array);
				else
				{
					os_db_perform(TABLE_EXTRA_FIELDS_INFO, $sql_data_array, 'update', "fields_id = '".(int)$fields_id."' AND languages_id = '".(int)$id."'");
				}
			}
		}

		if ($action == 'new')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok', 'urlBack' => FILENAME_EXTRA_FIELDS);
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Получение поля по ID
	 */
	public function getFieldById($params)
	{
		if (empty($params)) return false;

		$field_id = (is_array($params)) ? $params['field_id'] : $params;

		// получаем сам пункт меню
		$menuSql = os_db_query("SELECT * FROM ".TABLE_EXTRA_FIELDS." WHERE fields_id = '".(int)$field_id."'");
		$aField = os_db_fetch_array($menuSql);

		// получаем переводы
		$fieldLangSql = os_db_query("SELECT * FROM ".TABLE_EXTRA_FIELDS_INFO." WHERE fields_id = '".(int)$field_id."'");
		while($l = os_db_fetch_array($fieldLangSql))
		{
			$lang[$l['languages_id']] = $l;
		}

		$aField['fields_lang'] = $langs;
		return $aField;
	}

	/**
	 * Получение группы покупателей по ID
	 */
	public function getCustomerStatusById($params)
	{
		$status_id = (is_array($params)) ? $params['status_id'] : $params;

		$statusQuery = os_db_query("SELECT * FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id = '".(int)$_SESSION['languages_id']."' AND customers_status_id = '".(int)$status_id."'");
		$aStatus = os_db_fetch_array($statusQuery);

		$statusLangSql = os_db_query("SELECT * FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id = '".(int)$status_id."'");
		while($l = os_db_fetch_array($statusLangSql))
		{
			$lang[$l['language_id']] = $l['customers_status_name'];
		}

		$aStatus['status_lang'] = $lang;
		return $aStatus;
	}

	/**
	 * Удаление статуса покупателя по ID
	 */
	public function deleteStatus($params)
	{
		if (!isset($params)) return false;
		$status_id = (is_array($params)) ? $params['id'] : $params;

		// Удаляем историю
		$history_query = os_db_query("SELECT count(*) as count FROM ".TABLE_CUSTOMERS_STATUS_HISTORY." WHERE '".os_db_input($status_id)."' IN (new_value, old_value)");
		$history = os_db_fetch_array($history_query);
		if ($history['count'] > 0)
		{
			os_db_query("DELETE FROM ".TABLE_CUSTOMERS_STATUS_HISTORY." WHERE '".os_db_input($status_id)."' in (new_value, old_value)");
		}

		// Обновляем стандартный ID покупателя, если этот ID уже есть в настройках
		$customers_status_query = os_db_query("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
		$customers_status = os_db_fetch_array($customers_status_query);
		if ($customers_status['configuration_value'] == $status_id)
		{
			os_db_query("UPDATE ".TABLE_CONFIGURATION." set configuration_value = '' WHERE configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
		}

		// Удаляем иконку группы
		$groupIcon = os_db_query("SELECT customers_status_image FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id = ". os_db_input($status_id));
		$icon = os_db_fetch_array($groupIcon);

		if (!empty($icon['customers_status_image']) && is_file(GROUP_ICONS_PATH.$icon['customers_status_image']))
		{
			unlink(GROUP_ICONS_PATH.$icon['customers_status_image']);
		}

		// Удаляем статус
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id = '".os_db_input($status_id)."'");
		os_db_query("DELETE FROM ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." WHERE customers_status_id = ". os_db_input($status_id));
		// Удаляем скидки групп
		os_db_query("DROP TABLE IF EXISTS ".TABLE_PERSONAL_OFFERS.os_db_input($status_id)."");
		// Удаляем права доступа
		os_db_query("ALTER TABLE `".DB_PREFIX."products` DROP `group_permission_".os_db_input($status_id)."`");
		os_db_query("ALTER TABLE `".DB_PREFIX."categories` DROP `group_permission_".os_db_input($status_id)."`");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение групп покупателей
	 */
	public function saveCustomersStatus($params)
	{
		if (empty($params)) return false;

		$customers_status_id = (int)$params['cID'];

		$languages = os_get_languages();
		for ($i=0; $i<sizeof($languages); $i++)
		{
			$language_id = $languages[$i]['id'];

			$customers_status_name_array = $params['lang'][$language_id];
			$customers_status_public = $params['customers_status_public'];
			$customers_status_show_price = $params['customers_status_show_price'];
			$customers_status_show_price_tax = $params['customers_status_show_price_tax'];
			$customers_status_min_order = $params['customers_status_min_order'];
			$customers_status_max_order = $params['customers_status_max_order'];
			$customers_status_discount = $params['customers_status_discount'];
			$customers_status_ot_discount_flag = $params['customers_status_ot_discount_flag'];
			$customers_status_ot_discount = $params['customers_status_ot_discount'];
			$customers_status_graduated_prices = $params['customers_status_graduated_prices'];
			$customers_status_discount_attributes = $params['customers_status_discount_attributes'];
			$customers_status_add_tax_ot = $params['customers_status_add_tax_ot'];
			$customers_status_payment_unallowed = $params['customers_status_payment_unallowed'];
			$customers_status_shipping_unallowed = $params['customers_status_shipping_unallowed'];
			$customers_fsk18 = $params['customers_fsk18'];
			$customers_fsk18_display = $params['customers_fsk18_display'];
			$customers_status_write_reviews = $params['customers_status_write_reviews'];
			$customers_status_read_reviews = $params['customers_status_read_reviews'];
			$customers_status_accumulated_limit = $params['customers_status_accumulated_limit'];
			$customers_base_status = $params['customers_base_status'];

			$sql_data_array = array(
				'customers_status_name' => os_db_prepare_input($customers_status_name_array),
				'customers_status_public' => os_db_prepare_input($customers_status_public),
				'customers_status_show_price' => os_db_prepare_input($customers_status_show_price),
				'customers_status_show_price_tax' => os_db_prepare_input($customers_status_show_price_tax),
				'customers_status_min_order' => os_db_prepare_input($customers_status_min_order),
				'customers_status_max_order' => os_db_prepare_input($customers_status_max_order),
				'customers_status_discount' => os_db_prepare_input($customers_status_discount),
				'customers_status_ot_discount_flag' => os_db_prepare_input($customers_status_ot_discount_flag),
				'customers_status_ot_discount' => os_db_prepare_input($customers_status_ot_discount),
				'customers_status_graduated_prices' => os_db_prepare_input($customers_status_graduated_prices),
				'customers_status_add_tax_ot' => os_db_prepare_input($customers_status_add_tax_ot),
				'customers_status_payment_unallowed' => os_db_prepare_input($customers_status_payment_unallowed),
				'customers_status_shipping_unallowed' => os_db_prepare_input($customers_status_shipping_unallowed),
				'customers_fsk18' => os_db_prepare_input($customers_fsk18),
				'customers_fsk18_display' => os_db_prepare_input($customers_fsk18_display),
				'customers_status_write_reviews' => os_db_prepare_input($customers_status_write_reviews),
				'customers_status_read_reviews' => os_db_prepare_input($customers_status_read_reviews),
				'customers_status_accumulated_limit' => os_db_prepare_input($customers_status_accumulated_limit),
				'customers_status_discount_attributes' => os_db_prepare_input($customers_status_discount_attributes)
			);

			if ($params['action'] == 'insert')
			{
				if (!os_not_null($customers_status_id))
				{
					$next_id_query = os_db_query("select max(customers_status_id) as customers_status_id from ".TABLE_CUSTOMERS_STATUS."");
					$next_id = os_db_fetch_array($next_id_query);
					$customers_status_id = $next_id['customers_status_id'] + 1;
					os_db_query("create table IF NOT EXISTS ".TABLE_PERSONAL_OFFERS.$customers_status_id." (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, products_id int NOT NULL, quantity int, personal_offer decimal(15,4)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");
					os_db_query("ALTER TABLE `".DB_PREFIX."products` ADD `group_permission_".$customers_status_id."` TINYINT( 1 ) NOT NULL");
					os_db_query("ALTER TABLE `".DB_PREFIX."categories` ADD `group_permission_".$customers_status_id."` TINYINT( 1 ) NOT NULL");

					$products_query = os_db_query("select price_id, products_id, quantity, personal_offer from ".TABLE_PERSONAL_OFFERS.$customers_base_status ."");
					while($products = os_db_fetch_array($products_query)){
						$product_data_array = array(
							'price_id' => os_db_prepare_input($products['price_id']),
							'products_id' => os_db_prepare_input($products['products_id']),
							'quantity' => os_db_prepare_input($products['quantity']),
							'personal_offer' => os_db_prepare_input($products['personal_offer'])
						);
						os_db_perform(TABLE_PERSONAL_OFFERS.$customers_status_id, $product_data_array);
					}
				}

				$insert_sql_data = array('customers_status_id' => os_db_prepare_input($customers_status_id), 'language_id' => os_db_prepare_input($language_id));
				$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
				os_db_perform(TABLE_CUSTOMERS_STATUS, $sql_data_array);

			} elseif ($params['action'] == 'save') {
				os_db_perform(TABLE_CUSTOMERS_STATUS, $sql_data_array, 'update', "customers_status_id = '".os_db_input($customers_status_id)."' and language_id = '".$language_id."'");
			}
		}

		if ($customers_status_image = &os_try_upload('customers_status_image', GROUP_ICONS_PATH)) {
			os_db_query("update ".TABLE_CUSTOMERS_STATUS." set customers_status_image = '".$customers_status_image->filename."' where customers_status_id = '".os_db_input($customers_status_id)."'");
		}

		if ($params['default'] == 'on') {
			os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".os_db_input($customers_status_id)."' where configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
			// set_configuration_cache();
		}

		os_db_query("delete from ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." where customers_status_id = ". os_db_input($customers_status_id));
		$orders_status_query = os_db_query("select orders_status_id from ".TABLE_ORDERS_STATUS." where language_id = ".$_SESSION['languages_id']." order by orders_status_id");
		while ($orders_status = os_db_fetch_array($orders_status_query)) {
			if ($params['orders_status_'.$orders_status['orders_status_id']]) {
				os_db_query("insert into ".TABLE_CUSTOMERS_STATUS_ORDERS_STATUS." values (". os_db_input($customers_status_id).", ".$orders_status['orders_status_id'].")");
			}
		}

		if ($params['action'] == 'insert')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok', 'urlBack' => FILENAME_CUSTOMERS_STATUS);
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение стран
	 */
	public function saveCountries($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$dataArray = array(
			'countries_name' => os_db_prepare_input($params['countries_name']),
			'countries_iso_code_2' => os_db_prepare_input($params['countries_iso_code_2']),
			'countries_iso_code_3' => os_db_prepare_input($params['countries_iso_code_3']),
			'address_format_id' => os_db_prepare_input($params['address_format_id'])
		);

		if ($action == 'edit')
			os_db_perform(TABLE_COUNTRIES, $dataArray, 'update', "countries_id = '".(int)$params['cID']."'");
		else
			os_db_perform(TABLE_COUNTRIES, $dataArray);

		if ($params['action'] == 'new')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление страны
	 */
	public function deleteCountry($params)
	{
		if (!isset($params)) return false;
		$cID = (is_array($params)) ? $params['id'] : $params;

		os_db_query("delete from ".TABLE_COUNTRIES." where countries_id = '".(int)$cID."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Статус страны
	 */
	public function statusCountry($params)
	{
		if (is_array($params))
		{
			os_db_query("UPDATE ".TABLE_COUNTRIES." SET ".os_db_prepare_input($params['column'])." = '".(int)$params['status']."' WHERE countries_id = '".(int)$params['id']."'");
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');

		return $data;
	}

	/**
	 * Сохранение зон
	 */
	public function saveZones($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$dataArray = array(
			'zone_country_id' => os_db_prepare_input($params['zone_country_id']),
			'zone_code' => os_db_prepare_input($params['zone_code']),
			'zone_name' => os_db_prepare_input($params['zone_name'])
		);

		if ($action == 'edit')
			os_db_perform(TABLE_ZONES, $dataArray, 'update', "zone_id = '".(int)$params['cID']."'");
		else
			os_db_perform(TABLE_ZONES, $dataArray);

		if ($params['action'] == 'new')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление зоны
	 */
	public function deleteZone($params)
	{
		if (!isset($params)) return false;
		$cID = (is_array($params)) ? $params['id'] : $params;

		os_db_query("delete from ".TABLE_ZONES." where zone_id = '".(int)$cID."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Связь страны и зоны
	 */
	public function saveSubGeoZone($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$dataArray = array(
			'geo_zone_id' => (int)$params['zID'],
			'zone_country_id' => (int)$params['zone_country_id'],
			'zone_id' => (int)$params['zone_id']
		);

		if ($action == 'edit')
		{
			$dataArray['last_modified'] = 'now()';
			os_db_perform(TABLE_ZONES_TO_GEO_ZONES, $dataArray, 'update', "association_id = '".(int)$params['sID']."'");
		}
		else
		{
			$dataArray['date_added'] = 'now()';
			os_db_perform(TABLE_ZONES_TO_GEO_ZONES, $dataArray);
		}

		if ($params['action'] == 'new')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление связи страны и зоны
	 */
	public function deleteSubGeoZone($params)
	{
		if (!isset($params)) return false;
		$sID = (is_array($params)) ? $params['id'] : $params;

		os_db_query("delete from ".TABLE_ZONES_TO_GEO_ZONES." where association_id = '".(int)$sID."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение геозоны
	 */
	public function saveGeoZone($params)
	{
		if (!isset($params)) return false;

		$action = $params['action'];

		$dataArray = array(
			'geo_zone_name' => os_db_prepare_input($params['geo_zone_name']),
			'geo_zone_description' => os_db_prepare_input($params['geo_zone_description'])
		);

		if ($action == 'edit_zone')
		{
			$dataArray['last_modified'] = 'now()';
			os_db_perform(TABLE_GEO_ZONES, $dataArray, 'update', "geo_zone_id = '".(int)$params['zID']."'");
		}
		else
		{
			$dataArray['date_added'] = 'now()';
			os_db_perform(TABLE_GEO_ZONES, $dataArray);
		}

		if ($params['action'] == 'new_zone')
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Успешно изменено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление геозоны
	 */
	public function deleteGeoZone($params)
	{
		if (!isset($params)) return false;
		$zID = (is_array($params)) ? $params['id'] : $params;

		os_db_query("delete from ".TABLE_GEO_ZONES." where geo_zone_id = '".(int)$zID."'");
		os_db_query("delete from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".(int)$zID."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}
?>