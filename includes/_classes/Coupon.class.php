<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCoupon extends CartET
{
	/**
	 * Удаление (выключение) купона
	 */
	public function deleteCoupon($params)
	{
		if (!isset($params)) return false;
		$coupon_id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("UPDATE ".TABLE_COUPONS." SET coupon_active = 'N' WHERE coupon_id = '".(int)$coupon_id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Отправка сообщений пользователям
	 */
	public function sendMail($params)
	{
		if (!isset($params)) return false;

		include 'lang/'.$_SESSION['language_admin'].'/coupon_admin.php';
		require _LIB . 'phpmailer/PHPMailerAutoload.php';
		include_once (_LIB.'phpmailer/func.mail.php');
		$osTemplate = new osTemplate;

		switch ($params['customers_email_address'])
		{
			case '***':
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS);
				$mail_sent_to = TEXT_ALL_CUSTOMERS;
				break;
			case '**D':
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS." where customers_newsletter = '1'");
				$mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
				break;
			default:
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($params['customers_email_address'])."'");
				$mail_sent_to = $params['customers_email_address'];
				break;
		}

		$coupon_query = os_db_query("select coupon_code from ".TABLE_COUPONS." where coupon_id = '".(int)$params['cid']."'");
		$coupon_result = os_db_fetch_array($coupon_query);

		while ($mail = os_db_fetch_array($mail_query))
		{
			$osTemplate->assign('language', $_SESSION['language_admin']);
			$osTemplate->caching = false;

			$osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
			$osTemplate->assign('logo_path',HTTP_SERVER .DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

			$osTemplate->assign('MESSAGE', $params['message']);
			$osTemplate->assign('COUPON_ID', $coupon_result['coupon_code']);
			$osTemplate->assign('WEBSITE', HTTP_SERVER .DIR_WS_CATALOG);

			$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_coupon.html');
			$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_coupon.txt');

			os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $mail['customers_email_address'] , $mail['customers_firstname'].' '.$mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', os_db_prepare_input($params['subject']), $html_mail , $txt_mail);
		}

		$data = array('msg' => 'Успешно отправлено: '.$mail_sent_to, 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение\добавление купона
	 */
	public function saveCoupon($params)
	{
		if (!isset($params)) return false;

		$coupon_type = "F";
		if (substr($params['coupon_amount'], -1) == '%')
			$coupon_type='P';

		if ($params['coupon_free_ship'])
			$coupon_type = 'S';

		$sql_data_array = array(
			'coupon_code' => os_db_prepare_input($params['coupon_code']),
			'coupon_amount' => os_db_prepare_input($params['coupon_amount']),
			'coupon_type' => os_db_prepare_input($coupon_type),
			'uses_per_coupon' => os_db_prepare_input($params['coupon_uses_coupon']),
			'uses_per_user' => os_db_prepare_input($params['coupon_uses_user']),
			'coupon_minimum_order' => os_db_prepare_input($params['coupon_min_order']),
			'restrict_to_products' => os_db_prepare_input($params['coupon_products']),
			'restrict_to_categories' => os_db_prepare_input($params['coupon_categories']),
			'coupon_start_date' => $params['coupon_startdate'],
			'coupon_expire_date' => $params['coupon_finishdate'],
			'date_created' => 'now()',
			'date_modified' => 'now()'
		);

		$languages = os_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			if ($languages[$i]['status'] == 1)
			{
				$language_id = $languages[$i]['id'];
				$sql_data_marray[$i] = array(
					'coupon_name' => os_db_prepare_input($params['coupon_name'][$language_id]),
					'coupon_description' => os_db_prepare_input($params['coupon_desc'][$language_id])
				);
			}
		}
		if ($params['action'] == 'edit')
		{
			os_db_perform(TABLE_COUPONS, $sql_data_array, 'update', "coupon_id='".$params['cid']."'");
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
			{
				if($languages[$i]['status']==1) {
					$language_id = $languages[$i]['id'];
					os_db_query("update ".TABLE_COUPONS_DESCRIPTION." set coupon_name = '".os_db_prepare_input($params['coupon_name'][$language_id])."', coupon_description = '".os_db_prepare_input($params['coupon_desc'][$language_id])."' where coupon_id = '".$params['cid']."' and language_id = '".$language_id."'");
				}
			}
		}
		else
		{
			os_db_perform(TABLE_COUPONS, $sql_data_array);
			$insert_id = os_db_insert_id();

			for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
			{
				if ($languages[$i]['status']==1)
				{
					$language_id = $languages[$i]['id'];
					$sql_data_marray[$i]['coupon_id'] = $insert_id;
					$sql_data_marray[$i]['language_id'] = $language_id;
					os_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i]);
				}
			}
		}

		if ($params['action'] == 'edit')
			$data = array('msg' => 'Успешно обновлено!', 'type' => 'ok');
		else
			$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Отправка сертификата
	 */
	public function sendGift($params)
	{
		if (!isset($params)) return false;

		include 'lang/'.$_SESSION['language_admin'].'/coupon_admin.php';
		require _LIB . 'phpmailer/PHPMailerAutoload.php';
		include_once (_LIB.'phpmailer/func.mail.php');

		$osTemplate = new osTemplate;

		require(get_path('class_admin').'currencies.php');
		$currencies = new currencies();

		switch ($_POST['customers_email_address'])
		{
			case '***':
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS);
				$mail_sent_to = TEXT_ALL_CUSTOMERS;
				break;
			case '**D':
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS." where customers_newsletter = '1'");
				$mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
				break;
			default:
				$customers_email_address = os_db_prepare_input($_POST['customers_email_address']);

				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($customers_email_address)."'");
				$mail_sent_to = $_POST['customers_email_address'];
				if ($_POST['email_to'])
				{
					$mail_sent_to = $_POST['email_to'];
				}
				break;
		}

		$subject = os_db_prepare_input($_POST['subject']);
		while ($mail = os_db_fetch_array($mail_query))
		{
			$id1 = create_coupon_code($mail['customers_email_address']);
			$osTemplate->assign('language', $_SESSION['language_admin']);
			$osTemplate->caching = false;

			$osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
			$osTemplate->assign('logo_path',HTTP_SERVER .DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

			$osTemplate->assign('AMMOUNT', $currencies->format($_POST['amount']));
			$osTemplate->assign('MESSAGE', $_POST['message']);
			$osTemplate->assign('GIFT_ID', $id1);
			$osTemplate->assign('WEBSITE', HTTP_SERVER .DIR_WS_CATALOG);

			$link = HTTP_SERVER .DIR_WS_CATALOG.'gv_redeem.php'.'?gv_no='.$id1;

			$osTemplate->assign('GIFT_LINK',$link);

			$html_mail=$osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/send_gift.html');
			$txt_mail=$osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/send_gift.txt');

			if ($subject == '')
				$subject=EMAIL_BILLING_SUBJECT;

			os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $mail['customers_email_address'] , $mail['customers_firstname'].' '.$mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail , $txt_mail);

			$insert_query = os_db_query("insert into ".TABLE_COUPONS." (coupon_code, coupon_type, coupon_amount, date_created) values ('".$id1."', 'G', '".$_POST['amount']."', now())");
			$insert_id = os_db_insert_id($insert_query);
			os_db_query("insert into ".TABLE_COUPON_EMAIL_TRACK." (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('".$insert_id ."', '0', 'Admin', '".$mail['customers_email_address']."', now() )");
		}
		if ($_POST['email_to'])
		{
			$id1 = create_coupon_code($_POST['email_to']);
			$osTemplate->assign('language', $_SESSION['language_admin']);
			$osTemplate->caching = false;

			$osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
			$osTemplate->assign('logo_path',HTTP_SERVER .DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

			$osTemplate->assign('AMMOUNT', $currencies->format($_POST['amount']));
			$osTemplate->assign('MESSAGE', $_POST['message']);
			$osTemplate->assign('GIFT_ID', $id1);
			$osTemplate->assign('WEBSITE', HTTP_SERVER .DIR_WS_CATALOG);

			if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
				$link = HTTP_SERVER.DIR_WS_CATALOG.'gv_redeem.php'.'/gv_no,'.$id1;
			else
				$link = HTTP_SERVER.DIR_WS_CATALOG.'gv_redeem.php'.'?gv_no='.$id1;

			$osTemplate->assign('GIFT_LINK', $link);

			$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_gift.html');
			$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/send_gift.txt');

			os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $_POST['email_to'] , '' , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail , $txt_mail);

			$insert_query = os_db_query("insert into ".TABLE_COUPONS." (coupon_code, coupon_type, coupon_amount, date_created) values ('".$id1."', 'G', '".$_POST['amount']."', now())");
			$insert_id = os_db_insert_id($insert_query);
			 os_db_query("insert into ".TABLE_COUPON_EMAIL_TRACK." (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('".$insert_id ."', '0', 'Admin', '".$_POST['email_to']."', now() )");
		}

		$data = array('msg' => 'Успешно отправлено: '.$mail_sent_to, 'type' => 'ok');

		return $data;
	}

	/**
	 * Отправка сертификата
	 */
	public function couponActivate($params)
	{
		if (!isset($params)) return false;

		$gid = (is_array($params)) ? $params['id'] : $params;

		require _LIB . 'phpmailer/PHPMailerAutoload.php';
		include_once (_LIB.'phpmailer/func.mail.php');
		$osTemplate = new osTemplate;
		require(get_path('class_admin').'currencies.php');
		$currencies = new currencies();

		$gv_query = os_db_query("select release_flag from ".TABLE_COUPON_GV_QUEUE." where unique_id='".(int)$gid."'");
		$gv_result = os_db_fetch_array($gv_query);
		if ($gv_result['release_flag']=='N')
		{
			$gv_query = os_db_query("select customer_id, amount from ".TABLE_COUPON_GV_QUEUE ." where unique_id='".(int)$gid."'");
			if ($gv_resulta = os_db_fetch_array($gv_query))
			{
				$gv_amount = $gv_resulta['amount'];
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS." where customers_id = '".$gv_resulta['customer_id']."'");
				$mail = os_db_fetch_array($mail_query);

				$osTemplate->assign('language', $_SESSION['language_admin']);
				$osTemplate->caching = false;
				$osTemplate->assign('tpl_path',DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
				$osTemplate->assign('logo_path',HTTP_SERVER .DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');
				$osTemplate->assign('AMMOUNT',$currencies->format($gv_amount));

				$html_mail = $osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/gift_accepted.html');
				$txt_mail= $osTemplate->fetch(_MAIL.'/admin/'.$_SESSION['language_admin'].'/gift_accepted.txt');

				os_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME,$mail['customers_email_address'] , $mail['customers_firstname'].' '.$mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail , $txt_mail);

				$gv_amount = $gv_resulta['amount'];
				$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id='".$gv_resulta['customer_id']."'");
				$customer_gv = false;
				$total_gv_amount = 0;
				if ($gv_result=os_db_fetch_array($gv_query))
				{
					$total_gv_amount=$gv_result['amount'];
					$customer_gv=true;
				}
				$total_gv_amount=$total_gv_amount+$gv_amount;
				if ($customer_gv)
					os_db_query("update ".TABLE_COUPON_GV_CUSTOMER." set amount='".$total_gv_amount."' where customer_id='".$gv_resulta['customer_id']."'");
				else
					os_db_query("insert into " .TABLE_COUPON_GV_CUSTOMER." (customer_id, amount) values ('".$gv_resulta['customer_id']."','".$total_gv_amount."')");

				os_db_query("update ".TABLE_COUPON_GV_QUEUE." set release_flag='Y' where unique_id='".(int)$gid."'");
			}
		}

		$data = array('msg' => 'Сертификат успешно активирован', 'type' => 'ok');

		return $data;
	}
}
?>