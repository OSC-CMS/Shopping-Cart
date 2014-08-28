<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

include ('includes/top.php');

if (isset ($_SESSION['customer_id']))
{
	os_redirect(os_href_link(FILENAME_START, '', 'SSL'));
}

if ($session_started == false)
{
	os_redirect(os_href_link(FILENAME_COOKIE_USAGE));
}

$captcha = false;
if (isset ($_GET['action']) && ($_GET['action'] == 'process'))
{
	$email_address = os_db_prepare_input($_POST['email_address']);
	$password = os_db_prepare_input($_POST['password']);

	// Check if email exists
	$check_customer_query = os_db_query("select customers_id, customers_vat_id, customers_firstname,customers_lastname, customers_gender, customers_password, customers_email_address, login_tries, login_time, customers_default_address_id, customers_username from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($email_address)."' and account_type = '0'");
	if (!os_db_num_rows($check_customer_query))
	{
		$_GET['login'] = 'fail';
		$info_message = TEXT_NO_EMAIL_ADDRESS_FOUND;
	}
	else
	{
		$check_customer = os_db_fetch_array($check_customer_query);

		// Check the login is blocked while login_tries is more than 5 and blocktime is not over
		$blocktime = LOGIN_TIME; // time to block the login in seconds
		$time = time(); // time now as a timestamp
		$logintime = strtotime($check_customer['login_time']);  // conversion from the ISO date format to a timestamp
		$difference = $time - $logintime; // The difference time in seconds between the last login and now
		if ($check_customer['login_tries'] >= LOGIN_NUM and $difference < $blocktime)
		{
			$captcha = true;
			$captchaImg = '<img src="'.FILENAME_DISPLAY_CAPTCHA.'" alt="captcha" name="captcha" />';
			$captchaInput = os_draw_input_field('captcha', '', 'size="6" maxlength="6"', 'text', false);

			if ($_POST['captcha'] == $_SESSION['captcha_keystring'])
			{
				// code ok
				// Check that password is good
				if (!os_validate_password($password, $check_customer['customers_password']))
				{
					$_GET['login'] = 'fail';
					// Login tries + 1
					os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");
					$info_message = TEXT_LOGIN_ERROR;
				}
				else
				{
					if (SESSION_RECREATE == 'True')
					{
						os_session_recreate();
					}
					// Login tries = 0			$date_now = date('Ymd');
					os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = 0, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");

					$check_country_query = os_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
					$check_country = os_db_fetch_array($check_country_query);

					$_SESSION['customer_gender'] = $check_customer['customers_gender'];
					$_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
					$_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
					$_SESSION['customer_id'] = $check_customer['customers_id'];
					$_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
					$_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
					$_SESSION['customer_country_id'] = $check_country['entry_country_id'];
					$_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];

					os_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
					os_write_user_info((int) $_SESSION['customer_id']);
					// restore cart contents
					$_SESSION['cart']->restore_contents();

					os_redirect(os_href_link(FILENAME_START));
				}
			}
			else
			{
				// code falsch
				$info_message = TEXT_WRONG_CODE;
				// Login tries + 1
				os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");
			}
		}
		else
		{
			// Check that password is good
			if (!os_validate_password($password, $check_customer['customers_password']))
			{
				$_GET['login'] = 'fail';
				// Login tries + 1
				os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");
				$info_message = TEXT_LOGIN_ERROR;
			}
			else
			{
				if (SESSION_RECREATE == 'True')
				{
					os_session_recreate();
				}
				// Login tries = 0			$date_now = date('Ymd');
				os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = 0, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");

				$check_country_query = os_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
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

				os_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
				os_write_user_info((int) $_SESSION['customer_id']);
				// restore cart contents
				$_SESSION['cart']->restore_contents();

				os_redirect(os_href_link(FILENAME_START));
			}
		}
	}
}
?>
<!doctype html>
<html lang="en-Us">
<head>
	<meta charset="utf-8">
	<title>CartET - <?php echo ADMIN_LOGIN_TITLE; ?></title>
	<meta name="robots" content="noindex,nofollow" />
	<style type="text/css">
		html, body, div, span, applet, object, iframe,
		h1, h2, h3, h4, h5, h6, p, blockquote, pre,
		a, abbr, acronym, address, big, cite, code,
		del, dfn, em, img, ins, kbd, q, s, samp,
		small, strike, strong, sub, sup, tt, var,
		b, u, i, center,
		dl, dt, dd, ol, ul, li,
		fieldset, form, label, legend,
		table, caption, tbody, tfoot, thead, tr, th, td,
		article, aside, canvas, details, embed,
		figure, figcaption, footer, header, hgroup,
		menu, nav, output, ruby, section, summary,
		time, mark, audio, video {
			margin: 0;
			padding: 0;
			border: 0;
			font-size: 100%;
			font: inherit;
			vertical-align: baseline;
		}
		/* HTML5 display-role reset for older browsers */
		article, aside, details, figcaption, figure,
		footer, header, hgroup, menu, nav, section {
			display: block;
		}
		body {
			line-height: 1;
		}
		ol, ul {
			list-style: none;
		}
		blockquote, q {
			quotes: none;
		}
		blockquote:before, blockquote:after,
		q:before, q:after {
			content: '';
			content: none;
		}
		table {
			border-collapse: collapse;
			border-spacing: 0;
		}
		body {
			background-color: #f8f9ff;
			color: #5a5656;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 16px;
			line-height: 1.5em;
		}
		a { text-decoration: none; }
		h1 { font-size: 1em; }
		h1, p {
			margin-bottom: 10px;
		}
		strong {
			font-weight: bold;
		}
		/* ---------- LOGIN ---------- */
		#login {margin: 200px auto;width: 300px;}
		form fieldset input[type="text"], input[type="password"] {
			background-color: #ecedf4;
			border: none;
			border-radius: 3px;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			color: #5a5656;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 14px;
			height: 50px;
			outline: none;
			padding: 0px 10px;
			width: 280px;
			-webkit-appearance:none;
		}
		form fieldset input[type="submit"] {
			background-color: #008dde;
			border: none;
			border-radius: 3px;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			color: #f4f4f4;
			cursor: pointer;
			font-family: Arial, Helvetica, sans-serif;
			height: 50px;
			text-transform: uppercase;
			width: 300px;
			-webkit-appearance:none;
		}
		.center {text-align: center;}
		#notifier-box {
			position: fixed;
			top: 15px;
			right: 15px;
			z-index:9999;
		}
		div.message-box {
			margin: 0 0 10px 0;
			padding: 10px;
			width: 250px;

			line-height: 14px;

			color: #fff; font-size: 11px; font-family: 'PT Sans Narrow', Ubuntu, Tahoma, Arial, sans-serif;

			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			-o-border-radius: 3px;
			border-radius: 3px;
		}
		div.message-box.error {
			background: #dc2929;background: rgba(220, 41, 41, .9);
			-webkit-text-shadow: 0 1px 0 #941313;
			-moz-text-shadow: 0 1px 0 #941313;
			-o-text-shadow: 0 1px 0 #941313;
			text-shadow: 0 1px 0 #941313;
		}
		div.message-box div.message-body {
			overflow: hidden;
		}

		.captchaField input[type="text"] {
			background-color: #e5e5e5;
			border: none;
			border-radius: 3px;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			color: #5a5656;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 14px;
			height: 50px;
			outline: none;
			padding: 0px 10px;
			width: 150px;
			-webkit-appearance:none;
			float:right;
		}
	</style>
</head>
<body>
	<?php if ($info_message) { ?>
		<div id="notifier-box">
			<div class="message-box error" style="">
				<div class="message-body"><span><?php echo $info_message; ?></span></div>
			</div>
		</div>
	<?php } ?>

	<div id="login">
		<div class="center">
			<a target="_blank" href="http://osc-cms.com"><img src="<?php echo _HTTP; ?>images/logo_login.gif" alt="CartET"/></a>
			<h1><?php echo ADMIN_LOGIN_TITLE; ?></h1>
		</div>
		<form name="login" action="<?php echo os_href_link('login_admin.php', 'action=process', 'SSL'); ?>" method="post">
			<fieldset>
				<p><input type="text" name="email_address" required value="Email" onBlur="if(this.value=='')this.value='Email'" onFocus="if(this.value=='Email')this.value='' "></p>
				<p><input type="password" name="password" required value="Password" onBlur="if(this.value=='')this.value='Password'" onFocus="if(this.value=='Password')this.value='' "></p>
				<?php if ($captcha == true) { ?>
					<p class="captchaField">
						<?php echo $captchaImg; ?>
						<?php echo $captchaInput; ?>
					</p>
				<?php } ?>
				<p><input type="submit" value="<?php echo TEXT_BUTTON_LOGIN; ?>"></p>
			</fieldset>
		</form>
	</div>
</body>
</html>
<?php include ('includes/bottom.php'); ?>