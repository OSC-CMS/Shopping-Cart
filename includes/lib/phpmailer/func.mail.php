<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

//Отправка e-mail
function os_php_mail($from_email_address, $from_email_name, $to_email_address, $to_name, $forwarding_to, $reply_address, $reply_address_name, $path_to_attachement, $path_to_more_attachements, $email_subject, $message_body_html, $message_body_plain) 
{
	global $mail_error;
	$mail = new PHPMailer();
	$mail->PluginDir = _LIB.'phpmailer/';

	if (isset ($_SESSION['language_charset'])) 
	{
        $mail->CharSet = $_SESSION['language_charset'];
	} 
	else 
	{
		$lang_query = "SELECT * FROM ".TABLE_LANGUAGES." WHERE code = '".DEFAULT_LANGUAGE."'";
		$lang_query = os_db_query($lang_query);
		$lang_data = os_db_fetch_array($lang_query);
		$mail->CharSet = $lang_data['language_charset'];
	}

	if ($_SESSION['language'] == 'ru') 
	{
		$mail->SetLanguage("ru", _LIB.'phpmailer/language/');
	} 
	else 
	{
		$mail->SetLanguage("en", _LIB.'phpmailer/language/');
	}

	if (EMAIL_TRANSPORT == 'smtp') 
	{
	
		$mail->IsSMTP();
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->Username = SMTP_USERNAME;
		$mail->Password = SMTP_PASSWORD; // SMTP password
		$mail->Host = SMTP_MAIN_SERVER.';'.SMTP_Backup_Server;;
		$mail->Port = SMTP_PORT;
		if (SMTP_SECURE == 'true')
		{
		   $mail->SMTPSecure = 'ssl';
		}
		$mail->SMTPAuth = SMTP_AUTH;
	}


	if (EMAIL_TRANSPORT == 'sendmail') 
	{ // set mailer to use SMTP
		$mail->IsSendmail();
		$mail->Sendmail = SENDMAIL_PATH;
	}

	if (EMAIL_TRANSPORT == 'mail') 
	{
		$mail->IsMail();
	}


	if (EMAIL_USE_HTML == 'true') // set email format to HTML
    {
		$mail->IsHTML(true);
		$mail->Body = $message_body_html;
		// remove html tags
		$message_body_plain = str_replace('<br />', " \n", $message_body_plain);
		$message_body_plain = strip_tags($message_body_plain);
		$mail->AltBody = $message_body_plain;
	} 
	else 
	{
		$mail->IsHTML(false);
		//remove html tags
		$message_body_plain = str_replace('<br />', " \n", $message_body_plain);
		$message_body_plain = strip_tags($message_body_plain);
		$mail->Body = $message_body_plain;
	}

	$mail->From = $from_email_address;
	$mail->Sender = $from_email_address;
	$mail->FromName = $from_email_name;
	$mail->AddAddress($to_email_address, $to_name);
	if ($forwarding_to != '') $mail->AddBCC($forwarding_to);
	$mail->AddReplyTo($reply_address, $reply_address_name);
	$mail->WordWrap = 100; // set word wrap to 50 characters

	//$mail->AddAttachment($path_to_attachement);                     // add attachments
	//$mail->AddAttachment($path_to_more_attachements);               // optional name                                          

	$mail->Subject = $email_subject;

	if (!$mail->Send()) 
	{
		echo TEXT_PHP_MAILER_ERROR;
		echo TEXT_PHP_MAILER_ERROR1 . $mail->ErrorInfo;
		exit;
	}

}

?>