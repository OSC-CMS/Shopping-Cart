<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('../config.php');

require('includes/top.php');

require_once(_CATALOG.'includes/database.php');

if (!isset($_SESSION['language']) )
{
   $_SESSION['language'] = 'ru';
}   

include('lang/'.$_SESSION['language'].'/lang.php');
  
os_db_connect() or die('Unable to connect to database server!'); 
    
$configuration_query = os_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);

while ($configuration = os_db_fetch_array($configuration_query)) 
{
  @   define($configuration['cfgKey'], $configuration['cfgValue']);
}

$messageStack = new messageStack();
$process = false;
  
if (isset($_POST['action']) && (($_POST['action'] == 'process') || ($_POST['action'] == 'refresh'))) 
{
   if ($_POST['action'] == 'process')  
   {
      $process = true;
   }  

   $firstname = os_db_prepare_input($_POST['FIRST_NAME']);
   $lastname = os_db_prepare_input($_POST['LAST_NAME']);
   $email_address = os_db_prepare_input($_POST['EMAIL_ADRESS']);
   $street_address = os_db_prepare_input($_POST['STREET_ADRESS']);
   $postcode = os_db_prepare_input($_POST['POST_CODE']);
   $city = os_db_prepare_input($_POST['CITY']);
   $zone_id = 98;
   $state = AUTO_INPUT_CITY;
   $country = 176;
   $telephone = os_db_prepare_input($_POST['TELEPHONE']);
   $password = os_db_prepare_input($_POST['PASSWORD']);
   $confirmation = os_db_prepare_input($_POST['PASSWORD']);
   $store_name = os_db_prepare_input($_POST['STORE_NAME']);
   $email_from = os_db_prepare_input($_POST['EMAIL_ADRESS_FROM']);
   $company = os_db_prepare_input($_POST['COMPANY']);
	
   if ($process) 
   {
	  $error = false;
      if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_FIRST_NAME_ERROR);
      }

      if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_LAST_NAME_ERROR);
      }
	
      if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_EMAIL_ADDRESS_ERROR);
      } 
	  elseif (os_validate_email($email_address) == false) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
      }  

      if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_STREET_ADDRESS_ERROR);
      }

      if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_POST_CODE_ERROR);
      }

      if (strlen($city) < ENTRY_CITY_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_CITY_ERROR);
      }

      if (ACCOUNT_COUNTRY == 'true') 
	  {
	     if (is_numeric($country) == false) 
		 {
            $error = true;
            $messageStack->add('step6', ENTRY_COUNTRY_ERROR);
         }
      }

      if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') 
	  {
         $zone_id = 0;
         $check_query = os_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "'");
         $check = os_db_fetch_array($check_query);
         $entry_state_has_zones = ($check['total'] > 0);
         if ($entry_state_has_zones == true) 
		 {
            $zone_query = os_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and zone_name = '" . os_db_input($state) . "'");
            if (os_db_num_rows($zone_query) == 1) 
			{
               $zone = os_db_fetch_array($zone_query);
               $zone_id = $zone['zone_id'];
            } 
			else 
			{
               $error = true;
               $messageStack->add('step6', ENTRY_STATE_ERROR_SELECT);
            }
         } 
		 else 
		 {
            if (strlen($state) < ENTRY_STATE_MIN_LENGTH) 
			{
               $error = true;
               $messageStack->add('step6', ENTRY_STATE_ERROR);
            }
         }
      }

      if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_TELEPHONE_NUMBER_ERROR);
      }


      if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_PASSWORD_ERROR);
      } 
	  elseif ($password != $confirmation) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_PASSWORD_ERROR_NOT_MATCHING);
      }
	
	  if (strlen($store_name) < '3') 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_STORE_NAME_ERROR);
      }
	  
	  if (strlen($company) < '2') 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_COMPANY_NAME_ERROR);
      }
	
      if (strlen($email_from) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_EMAIL_ADDRESS_FROM_ERROR);
      } 
	  elseif (os_validate_email($email_from) == false) 
	  {
         $error = true;
         $messageStack->add('step6', ENTRY_EMAIL_ADDRESS_FROM_CHECK_ERROR);
      } 

	  if ($error == false) 
	  {
         $customer_query = os_db_query("select c.customers_id, ci.customers_info_id, ab.customers_id from " . TABLE_CUSTOMERS . " c, " . TABLE_CUSTOMERS_INFO . " ci, " . TABLE_ADDRESS_BOOK . " ab ");
         if (os_db_num_rows($customer_query) >= 1) 
		 {
            $db_action = "update";
         } 
		 else 
		 {
            $db_action = "insert";
         }

         os_db_perform(TABLE_CUSTOMERS, array(
              'customers_id' => '1',
              'customers_status' => '0',
              'customers_firstname' => $firstname,
              'customers_lastname' => $lastname,
              'customers_email_address' => $email_address,
              'customers_default_address_id' => '1',
              'customers_telephone' => $telephone,
              'customers_password' => os_encrypt_password($password),
              'delete_user' => '0',
              'customers_date_added' => 'now()',
              'customers_last_modified' => 'now()',),
              $db_action, 'customers_id = 1'
              );

         os_db_perform(TABLE_CUSTOMERS_INFO, array(
              'customers_info_id' => '1',
              'customers_info_number_of_logons' => '0',
              'customers_info_date_account_created' => 'now()',
              'customers_info_date_account_last_modified' => 'now()'),
              $db_action, 'customers_info_id = 1'
              );

         os_db_perform(TABLE_ADDRESS_BOOK, array(
              'customers_id' => '1',
              'entry_company' => ($company),
              'entry_firstname' => ($firstname),
              'entry_lastname' => ($lastname),
              'entry_street_address' => ($street_address),
              'entry_postcode' => ($postcode),
              'entry_city' => ($city),
              'entry_state' => ($state),
              'entry_country_id' => ($country),
              'entry_zone_id' => ($zone_id),
              'address_date_added' => 'now()',
              'address_last_modified' => 'now()'),
              $db_action, 'customers_id = 1'
              );

         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($email_address). "' WHERE configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($store_name). "' WHERE configuration_key = 'STORE_NAME'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($email_from). "' WHERE configuration_key = 'EMAIL_FROM'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($country). "' WHERE configuration_key = 'SHIPPING_ORIGIN_COUNTRY'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($postcode). "' WHERE configuration_key = 'SHIPPING_ORIGIN_ZIP'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($company). "' WHERE configuration_key = 'STORE_OWNER'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($email_from). "' WHERE configuration_key = 'EMAIL_BILLING_FORWARDING_STRING'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($email_from). "' WHERE configuration_key = 'EMAIL_BILLING_ADDRESS'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($email_from). "' WHERE configuration_key = 'CONTACT_US_EMAIL_ADDRESS'");
         os_db_query("UPDATE " .TABLE_CONFIGURATION . " SET configuration_value='". ($email_from). "' WHERE configuration_key = 'EMAIL_SUPPORT_ADDRESS'");
         os_redirect(os_href_link('install/finished.php', '', 'NONSSL'));
	  }
   }
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
<head>
<meta http-equiv=Content-Type content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE_STEP6; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<style type='text/css' media='all'>@import url('includes/install.css');</style>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<script src="bootstrap/jquery.js"></script>
<script src="bootstrap/js/bootstrap-transition.js"></script>
<script src="bootstrap/js/bootstrap-alert.js"></script>
<script src="bootstrap/js/bootstrap-modal.js"></script>
<script src="bootstrap/js/bootstrap-dropdown.js"></script>
<script src="bootstrap/js/bootstrap-scrollspy.js"></script>
<script src="bootstrap/js/bootstrap-tab.js"></script>
<script src="bootstrap/js/bootstrap-tooltip.js"></script>
<script src="bootstrap/js/bootstrap-popover.js"></script>
<script src="bootstrap/js/bootstrap-button.js"></script>
<script src="bootstrap/js/bootstrap-collapse.js"></script>
<script src="bootstrap/js/bootstrap-carousel.js"></script>
<script src="bootstrap/js/bootstrap-typeahead.js"></script>
</head>
<body onLoad="generation();">

<div class="container">
	<p></p>
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				<a class="brand" href=""><?php echo TEXT_SETUP_INDEX; ?></a>

				<?php echo lang_menu(); ?>
			</div>
		</div>
	</div>





	<div class="page-header">

		<span class="pull-right">
			<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
			<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
		</span>

		<h1><?php echo STEPS ;?> <?php echo STEP6; ?></h1>
	</div>

	<div class="progress progress-striped active">
		<div class="bar" style="width:87.5%;"></div>
	</div>

	<noscript><div class="alert alert-error"><?php echo  OS_BROWS_ERROR;?></div></noscript>
	
	<p><h4><?php echo TITLE_STEP6; ?></h4></p>
	<hr>
	<div class="well well-small"><?php echo TEXT_WELCOME_STEP6; ?></div>


	<?php             
	if ($messageStack->size('step6') > 0) {
	?>
	<div class="alert alert-error"><?php echo $messageStack->output('step6'); ?></div>
	<?php } ?>

	<div class="form-horizontal">
	<form name="install" action="6.php" method="post" onsubmit="return check_form(step6);">
	<input name="action" type="hidden" value="process" />
		<fieldset>
			<legend><?php echo TITLE_ADMIN_CONFIG; ?></legend>
			<div class="control-group">
				<label class="control-label" for="FIRST_NAME"><?php echo TEXT_FIRSTNAME; ?></label>
				<div class="controls">
					<input type="text" name="FIRST_NAME" id="FIRST_NAME" value="<?php echo AUTO_INPUT_FIRSTNAME; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="LAST_NAME"><?php echo TEXT_LASTNAME; ?></label>
				<div class="controls">
					<input type="text" name="LAST_NAME" id="LAST_NAME" value="<?php echo AUTO_INPUT_LASTNAME; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="EMAIL_ADRESS"><?php echo TEXT_EMAIL; ?></label>
				<div class="controls">
					<input type="text" name="EMAIL_ADRESS" id="EMAIL_ADRESS" value="<?php echo AUTO_INPUT_EMAIL_FROM; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="STREET_ADRESS"><?php echo TEXT_STREET; ?></label>
				<div class="controls">
					<input type="text" name="STREET_ADRESS" id="STREET_ADRESS" value="<?php echo ENTRY_STREET_ADDRESS_TEXT; ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="POST_CODE"><?php echo TEXT_POSTCODE; ?></label>
				<div class="controls">
					<input type="text" name="POST_CODE" id="POST_CODE" value="<?php echo AUTO_INPUT_POST_CODE; ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="CITY"><?php echo TEXT_CITY; ?></label>
				<div class="controls">
					<input type="text" name="CITY" id="CITY" value="<?php echo AUTO_INPUT_CITY; ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="country"><?php echo TEXT_COUNTRY; ?></label>
				<div class="controls">
					<?php echo os_get_country_list('country',STORE_COUNTRY, 'onChange="changeselect();" id="country"'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="state"><?php echo TEXT_STATE; ?></label>
				<div class="controls">
	<script language="javascript">
	<!--
	function changeselect(reg) {
	//clear select
		document.install.state.length=0;
		var j=0;
		for (var i=0;i<zones.length;i++) {
		  if (zones[i][0]==document.install.country.value) {
	   document.install.state.options[j]=new Option(zones[i][1],zones[i][1]);
	   j++;
	   }
		  }
		if (j==0) {
		  document.install.state.options[0]=new Option('---','---');
		  }
		if (reg) { document.install.state.value = reg; }
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
	document.write('<select name="state" id="state">');
	document.write('</select>');
	changeselect("<?php echo os_db_prepare_input(@$_POST['state']); ?>");
	-->
	</script>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="TELEPHONE"><?php echo TEXT_TEL; ?></label>
				<div class="controls">
					<input type="text" name="TELEPHONE" id="TELEPHONE" value="(123) 123-45-67" />
				</div>
			</div>
		<script type="text/javascript">
		generation();
			
		// Генерация пароля
		function generatePassword(symbols, length) {
			var result = "";
			for (var i=0; i<length; i++) {
				result += symbols.charAt(Math.floor(Math.random()*symbols.length));
			};
			return result;
		}
	  
		function generation() {
			// Наборы символов для генерации пароля
			var Symbols = "abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789"
			var output = "";
			
			output += generatePassword(Symbols, 8-document.getElementById("password").value.length);
			if (document.getElementById("password").value.length == 8) 
			{
			   clearInterval(sl);
			}
			document.getElementById("password").value = document.getElementById("password").value+output;
			return true;
		}
		</script>
			<div class="control-group">
				<label class="control-label" for="password"><?php echo TEXT_PASSWORD; ?></label>
				<div class="controls">
					<input style="color:red;" id="password" type="text" name="PASSWORD" />
				</div>
			</div>
			<legend><?php echo TITLE_SHOP_CONFIG; ?></legend>
			<div class="control-group">
				<label class="control-label" for="STORE_NAME"><?php echo TEXT_STORE; ?></label>
				<div class="controls">
					<input type="text" name="STORE_NAME" id="STORE_NAME" value="<?php echo AUTO_INPUT_STORE_NAME; ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="COMPANY"><?php echo TEXT_COMPANY; ?></label>
				<div class="controls">
					<input type="text" name="COMPANY" id="COMPANY" value="<?php echo AUTO_INPUT_COMPANY; ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="EMAIL_ADRESS_FROM"><?php echo TEXT_EMAIL_FROM; ?></label>
				<div class="controls">
					<input type="text" name="EMAIL_ADRESS_FROM" id="EMAIL_ADRESS_FROM" value="admin@admin.com" />
				</div>
			</div>

		</fieldset>
	</form>
	</div>

	<span class="pull-right">
		<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
		<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
	</span>
	<div class="clear"></div>



	<hr>

	<footer>
		<p><?php echo _copy(); ?></p>
	</footer>
</div>

</body>
</html>