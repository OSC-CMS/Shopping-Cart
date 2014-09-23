<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');
require _LIB . 'phpmailer/PHPMailerAutoload.php';
include_once (_LIB.'phpmailer/func.mail.php');

if (($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x']))
{
	switch ($_POST['customers_email_address'])
	{
		case '***':
			$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
			$mail_sent_to = TEXT_ALL_CUSTOMERS;
		break;

		case '**D':
			$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
			$mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
		break;

		default:
			if (is_numeric($_POST['customers_email_address']))
			{
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_status = " . $_POST['customers_email_address']);
				$sent_to_query = os_db_query("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
				$sent_to = os_db_fetch_array($sent_to_query);
				$mail_sent_to = $sent_to['customers_status_name'];
			}
			else
			{
				$customers_email_address = os_db_prepare_input($_POST['customers_email_address']);
				$mail_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . os_db_input($customers_email_address) . "'");
				$mail_sent_to = $_POST['customers_email_address'];
			}
		break;
	}

	$from = os_db_prepare_input($_POST['from']);
	$subject = os_db_prepare_input($_POST['subject']);
	$message = os_db_prepare_input($_POST['message']);

	while ($mail = os_db_fetch_array($mail_query))
	{
		if (os_validate_email($mail['customers_email_address']) != false)
		{
			os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $mail['customers_email_address'], $mail['customers_firstname'] . ' ' . $mail['customers_lastname'], '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', $subject, $message, $message);
		}
	}

	os_redirect(os_href_link(FILENAME_MAIL, 'mail_sent_to=' . urlencode($mail_sent_to)));
}

if (($_GET['action'] == 'preview') && (!$_POST['customers_email_address']))
{
	$messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
}

if ($_GET['mail_sent_to'])
{
	$messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'success');
}
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<h5><?php echo HEADING_TITLE; ?></h5>

<?php
if (($_GET['action'] == 'preview') && ($_POST['customers_email_address']))
{
	switch ($_POST['customers_email_address'])
	{
		case '***':
			$mail_sent_to = TEXT_ALL_CUSTOMERS;
		break;

		case '**D':
			$mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
		break;

		default:
			if (is_numeric($_POST['customers_email_address']))
			{
				echo "hier bin ich";
				$sent_to_query = os_db_query("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
				$sent_to = os_db_fetch_array($sent_to_query);
				$mail_sent_to = $sent_to['customers_status_name'];
			}
			else
			{
				$mail_sent_to = $_POST['customers_email_address'];
			}
		break;
	}
	?>
	<?php echo os_draw_form('mail', FILENAME_MAIL, 'action=send_email_to_user'); ?>
		<strong><?php echo TEXT_CUSTOMER; ?></strong> <?php echo $mail_sent_to; ?><br />
		<strong><?php echo TEXT_FROM; ?></strong> <?php echo htmlspecialchars(stripslashes($_POST['from'])); ?><br />
		<strong><?php echo TEXT_SUBJECT; ?></strong> <?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?>

		<hr>

		<strong><?php echo TEXT_MESSAGE; ?></strong><br />
		<?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?>

		<hr>

		<?php
		reset($_POST);
		while (list($key, $value) = each($_POST))
		{
			if (!is_array($_POST[$key]))
			{
				echo os_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
			}
		}
		?>

		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SEND_EMAIL; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_MAIL); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>


	</form>
	<?php
}
else
{
	?>

	<?php echo os_draw_form('mail', FILENAME_MAIL, 'action=preview'); ?>
		<?php
		$customers = array();
		$customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
		$customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
		$customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
		$customers_statuses_array = os_db_query("select customers_status_id , customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE language_id='" . $_SESSION['languages_id'] . "' order by customers_status_name");
		while ($customers_statuses_value = os_db_fetch_array($customers_statuses_array))
		{
			$customers[] = array(
				'id' => $customers_statuses_value['customers_status_id'],
				'text' => $customers_statuses_value['customers_status_name']
			);
		}
		$mail_query = os_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
		while($customers_values = os_db_fetch_array($mail_query))
		{
			$customers[] = array(
				'id' => $customers_values['customers_email_address'],
				'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')'
			);
		}
		?>

		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_CUSTOMER; ?></label>
			<div class="controls">
				<?php echo os_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="from"><?php echo TEXT_FROM; ?></label>
			<div class="controls">
				<input type="text" name="from" id="from" value="<?php echo EMAIL_FROM; ?>" class="span12">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_SUBJECT; ?></label>
			<div class="controls">
				<input type="text" name="subject" id="subject" class="span12">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="message"><?php echo TEXT_MESSAGE; ?></label>
			<div class="controls">
				<textarea id="message" name="message" wrap="soft" class="span12 textarea_big"></textarea>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SEND_EMAIL; ?>" />
		</div>
	</form>
	<?php
}
?>

<?php $main->bottom(); ?>