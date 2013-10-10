<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

global $PHP_SELF;

if (strstr($PHP_SELF, FILENAME_CREATE_ACCOUNT))
{
	$form_id = 'create_account';
}
if (strstr($PHP_SELF, FILENAME_CHECKOUT_ALTERNATIVE))
{
	$form_id = 'checkout_alternative';
}
if (strstr($PHP_SELF, FILENAME_CHECKOUT_PAYMENT))
{
	$form_id = 'checkout_payment';
}
if (strstr($PHP_SELF, FILENAME_CREATE_GUEST_ACCOUNT ))
{
	$form_id = 'create_account';
}
if (strstr($PHP_SELF, FILENAME_ACCOUNT_PASSWORD ))
{
	$form_id = 'account_password';
}
if (strstr($PHP_SELF, FILENAME_ACCOUNT_EDIT ))
{
	$form_id = 'account_edit';
}
if (strstr($PHP_SELF, FILENAME_ADDRESS_BOOK_PROCESS ))
{
	$form_id = 'addressbook';
}
if (strstr($PHP_SELF, FILENAME_CHECKOUT_SHIPPING_ADDRESS ) OR strstr($PHP_SELF,FILENAME_CHECKOUT_PAYMENT_ADDRESS))
{
	$form_id = 'checkout_address';
}
?>
<script type="text/javascript" src="<?php echo _HTTP; ?>jscript/validate/jquery.validate.pack.js"></script>
<script type="text/javascript" src="<?php echo _HTTP; ?>jscript/modified.js"></script>
<script type="text/javascript"><!--
$(document).ready(function()
{
	$("#<?php echo $form_id; ?>").validate({
		rules: {
			gender: "required",
			customers_username: {
				required: true,
				minlength: <?php echo ENTRY_USERNAME_MIN_LENGTH; ?>
			},
			firstname: {
				required: true,
				minlength: <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>
			},
			lastname: {
				required: true,
				minlength: <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>
			},
			dob: {
				required: true,
				minlength: <?php echo ENTRY_DOB_MIN_LENGTH; ?>
			},
			email_address: {
				required: true,
				minlength: <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>,
				email: true
			},
			street_address: {
				required: true,
				minlength: <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>
			},
			postcode: {
				required: true,
				minlength: <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>
			},
			city: {
				required: true,
				minlength: <?php echo ENTRY_CITY_MIN_LENGTH; ?>
			},
			//state: {
				//required: true,
				//minlength: <?php echo ENTRY_STATE_MIN_LENGTH; ?>
			//},
			//country: {
				//required: true,
				//minlength: <?php echo ENTRY_STATE_MIN_LENGTH; ?>
			//},
			telephone: {
				required: true,
				minlength: <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>
			},
			password: {
				required: true,
				minlength: <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>
			},
			confirmation: {
				required: true,
				minlength: <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>,
				equalTo: "#pass"
			},
		},
		messages: {
			gender: "<?php echo ENTRY_GENDER_ERROR; ?>",
			customers_username: {
				required: "<?php echo ENTRY_USERNAME_ERROR; ?>",
				minlength: "<?php echo ENTRY_USERNAME_ERROR; ?>"
			},
			firstname: {
				required: "<?php echo ENTRY_FIRST_NAME_ERROR; ?>",
				minlength: "<?php echo ENTRY_FIRST_NAME_ERROR; ?>"
			},
			lastname: {
				required: "<?php echo ENTRY_LAST_NAME_ERROR; ?>",
				minlength: "<?php echo ENTRY_LAST_NAME_ERROR; ?>"
			},
			dob: {
				required: "<?php echo ENTRY_DATE_OF_BIRTH_ERROR; ?>",
				minlength: "<?php echo ENTRY_DATE_OF_BIRTH_ERROR; ?>"
			},
			email_address: "<?php echo ENTRY_EMAIL_ADDRESS_ERROR; ?>",
			street_address: {
				required: "<?php echo ENTRY_STREET_ADDRESS_ERROR; ?>",
				minlength: "<?php echo ENTRY_STREET_ADDRESS_ERROR; ?>"
			},
			postcode: {
				required: "<?php echo ENTRY_POST_CODE_ERROR; ?>",
				minlength: "<?php echo ENTRY_POST_CODE_ERROR; ?>"
			},
			city: {
				required: "<?php echo ENTRY_CITY_ERROR; ?>",
				minlength: "<?php echo ENTRY_CITY_ERROR; ?>"
			},
			//state: {
				//required: "<?php echo ENTRY_STATE_ERROR_SELECT; ?>",
				//minlength: "<?php echo ENTRY_STATE_ERROR_SELECT; ?>"
			//},
			//country: {
				//required: "<?php echo ENTRY_COUNTRY_ERROR; ?>",
				//minlength: "<?php echo ENTRY_COUNTRY_ERROR; ?>"
			//},
			telephone: {
				required: "<?php echo ENTRY_TELEPHONE_NUMBER_ERROR; ?>",
				minlength: "<?php echo ENTRY_TELEPHONE_NUMBER_ERROR; ?>"
			},
			password: {
				required: "<?php echo ENTRY_PASSWORD_ERROR; ?>",
				minlength: "<?php echo ENTRY_PASSWORD_CURRENT_ERROR; ?>"
			},
			confirmation: {
				required: "<?php echo ENTRY_PASSWORD_ERROR_NOT_MATCHING; ?>",
				minlength: "<?php echo ENTRY_PASSWORD_CURRENT_ERROR; ?>",
				equalTo: "<?php echo ENTRY_PASSWORD_ERROR_NOT_MATCHING; ?>"
			}
		}
	});
});
//--></script>