<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCustomer extends CartET
{
	/**
	 * Авторизован юзер как админ или нет
	 */
	public function isAdmin()
	{
		return ($_SESSION['customers_status']['customers_status_id'] == 0) ? true : false;
	}

	/**
	 * Авторизуем покупателя
	 */
	public function login($login, $password)
	{
		global $messageStack;

		$login = os_db_prepare_input($login);
		$password = os_db_prepare_input($password);
		$errors = false;

		// Проверяем Email или телефон на существование
		$check_customer_query = os_db_query("select * from ".TABLE_CUSTOMERS." where (customers_email_address = '".os_db_input($login)."' OR customers_telephone = '".os_db_input($login)."') and account_type = '0'");
		if (!os_db_num_rows($check_customer_query))
		{
			$errors = true;
			$messageStack->add_session('login', TEXT_NO_EMAIL_ADDRESS_FOUND);
			$data = array('msg' => TEXT_NO_EMAIL_ADDRESS_FOUND, 'type' => 'error');
		}
		else
		{
			$check_customer = os_db_fetch_array($check_customer_query);

			// Check the login is blocked while login_tries is more than 5 and blocktime is not over
			$blocktime = LOGIN_TIME; // time to block the login in seconds
			$time = time(); // time now as a timestamp
			$logintime = strtotime($check_customer['login_time']); // conversion from the ISO date format to a timestamp
			$difference = $time - $logintime; // The difference time in seconds between the last login and now

			if ($check_customer['login_tries'] >= LOGIN_NUM and $difference < $blocktime)
			{
				$_SESSION['captcha'] = 1;

				if ($_POST['captcha'] != $_SESSION['captcha_keystring'])
				{
					// Добавляем попытку входа
					os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE (customers_email_address = '".os_db_input($login)."' OR customers_telephone = '".os_db_input($login)."')");		
					$messageStack->add_session('login', TEXT_WRONG_CODE);
					$data = array('msg' => TEXT_WRONG_CODE, 'type' => 'error');
					$errors = true;
				}
			}

			// Проверяем пароль
			if (!os_validate_password($password, $check_customer['customers_password']))
			{
				// Добавляем попытку входа
				os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE (customers_email_address = '".os_db_input($login)."' OR customers_telephone = '".os_db_input($login)."')");		
				
				$messageStack->add_session('login', TEXT_LOGIN_ERROR);
				$data = array('msg' => TEXT_LOGIN_ERROR, 'type' => 'error');
				$errors = true;
			}

			if ($errors == false)
			{
				if (SESSION_RECREATE == 'True')
				{
					os_session_recreate();
				}

				// Обнуляем попытки входа
				os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = 0, login_time = now() WHERE (customers_email_address = '".os_db_input($login)."' OR customers_telephone = '".os_db_input($login)."')");		

				$check_country_query = os_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int)$check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
				$check_country = os_db_fetch_array($check_country_query);

				$_SESSION['customer_gender'] = $check_customer['customers_gender'];
				$_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
				$_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
				$_SESSION['customer_id'] = $check_customer['customers_id'];
				$_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
				$_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
				$_SESSION['customer_country_id'] = $check_country['entry_country_id'];
				$_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];
				$_SESSION['customers_username'] = $check_customer['customers_username'];

				unset($_SESSION['captcha']);

				os_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
				os_write_user_info((int) $_SESSION['customer_id']);

				$_SESSION['cart']->restore_contents();

				$data = array('msg' => 'Вы успешно вошли!', 'type' => 'ok');
			}
		}

		return ($this->request->isAjax()) ? $data : array('login' => $errors);
	}
}