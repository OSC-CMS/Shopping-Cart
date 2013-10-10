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

$breadcrumb->add(HEADING_TITLE, FILENAME_GV_MAIL);

$main->head();
$main->top_menu();
?>

<form id="mail" name="mail" action="<?php echo os_href_link(FILENAME_GV_MAIL); ?>" method="post">

<?php
if ($_GET['cID'])
{
	$select = 'where customers_id='.$_GET['cID'];
}
else
{
	$customers = array();
	$customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
	$customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
	$customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
}
$mail_query = os_db_query("select customers_id, customers_email_address, customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." ".$select." order by customers_lastname");
while($customers_values = os_db_fetch_array($mail_query))
{
	$customers[] = array(
		'id' => $customers_values['customers_email_address'],
		'text' => $customers_values['customers_lastname'].', '.$customers_values['customers_firstname'].' ('.$customers_values['customers_email_address'].')'
	);
}
?>
	<div class="control-group">
		<label class="control-label" for="customers_email_address"><?php echo TEXT_CUSTOMER; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<select class="input-block-level" id="customers_email_address" name="customers_email_address" data-required="true">
				<?php
				foreach ($customers AS $item)
				{
					echo '<option value="'.$item['id'].'">'.$item['text'].'</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="email_to"><?php echo TEXT_TO; ?></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="email_to" name="email_to" value="">
			<span class="help-block"><?php echo TEXT_SINGLE_EMAIL; ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="from"><?php echo TEXT_FROM; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="from" name="from" data-required="true" value="<?php echo EMAIL_FROM; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="subject"><?php echo TEXT_SUBJECT; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="subject" name="subject" data-required="true" value="">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="amount"><?php echo TEXT_AMOUNT; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="amount" name="amount" data-required="true" value="">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="message"><?php echo TEXT_MESSAGE; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<textarea class="input-block-level" id="message" name="message" data-required="true"></textarea>
		</div>
	</div>

	<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success ajax-save-form" data-form-action="coupon_sendGift" data-reload-page="1" type="submit" value="<?php echo BUTTON_SEND_EMAIL; ?>" />
	</div>

</form>

<?php $main->bottom(); ?>