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

      if (ACCOUNT_STATE == 'true') 
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
<style type='text/css' media='all'>@import url('includes/style.css');</style>
<script type="text/javascript" src="includes/include.js"></script>
</head>
<body onLoad="generation();">
<div id="header1">
   <div id="header2">
      <?php echo install_menu(); ?>
      <div id="header3"></div>
   </div>
</div>
		<div id="content-box">
			<div id="content-pad">
				

	<div id="stepbar">
		<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
			<h1><?php echo STEPS ;?></h1>
<div class="step-off"><a href="index.php"><?php echo START; ?></a></div>
<div class="step-off"><?php echo STEP1; ?></div>
<div class="step-off"><?php echo STEP2; ?></div>
<div class="step-off"><?php echo STEP3; ?></div>
<div class="step-off"><?php echo STEP4; ?></div>
<div class="step-off"><?php echo STEP5; ?></div>
<div class="step-on"><?php echo STEP6; ?></div>
<div class="step-off"><?php echo END; ?></div>

		<div class="box"></div>
  	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
  </div>



	<div id="warning">
		<noscript>
			<div id="javascript-warning">
				<?php echo  OS_BROWS_ERROR;?>
			</div>
		</noscript>
	</div>




<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
		</div>
		<div class="m">

				<div class="far-right">
					
				

	<div class="button1-right"><div class="prev"><a href="index.php" alt="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a></div></div>
<div class="button1-left"><div class="next"><a onclick="document.install.submit();"
 alt="<?php echo IMAGE_CONTINUE;?>"><?php echo IMAGE_CONTINUE;?></a></div></div>
			<?php echo lang_menu(); ?>			
				</div>
				<span class="step"><?php echo  TITLE_STEP6; ?></span>
			</div>
		<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
		</div>
	</div>

	<div id="installer">
			<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
		</div>
		<div class="m">



<?php echo TEXT_WELCOME_STEP6; ?>


<?php             
if ($messageStack->size('step6') > 0) {
?>
<div class="formerror">
<?php echo $messageStack->output('step6'); ?>
</div>
<?php } ?>
<form name="install" action="6.php" method="post" onsubmit="return check_form(step6);">
<input name="action" type="hidden" value="process" />

<fieldset class="form">
<legend><?php echo TITLE_ADMIN_CONFIG; ?></legend>
<p><strong><?php echo TEXT_FIRSTNAME; ?></strong>&nbsp;<input type="text" name="FIRST_NAME" value="<?php echo AUTO_INPUT_FIRSTNAME; ?>"></p>
<p><strong><?php echo TEXT_LASTNAME; ?></strong>&nbsp;<input type="text" name="LAST_NAME" value="<?php echo AUTO_INPUT_LASTNAME; ?>"></p>
<p><strong><?php echo TEXT_EMAIL; ?></strong>&nbsp;<input type="text" name="EMAIL_ADRESS" value="<?php echo AUTO_INPUT_EMAIL_FROM; ?>"></p>
<p><strong><?php echo TEXT_STREET; ?></strong>&nbsp;<input type="text" name="STREET_ADRESS" value="<?php echo ENTRY_STREET_ADDRESS_TEXT; ?>" />&nbsp;</p>
<p><strong><?php echo TEXT_POSTCODE; ?></strong>&nbsp;<input type="text" name="POST_CODE" value="<?php echo AUTO_INPUT_POST_CODE; ?>" /></p>
<p><strong><?php echo TEXT_CITY; ?></strong>&nbsp;<input type="text" name="CITY" value="<?php echo AUTO_INPUT_CITY; ?>" /></p>
<p><strong><?php echo TEXT_COUNTRY; ?></strong>&nbsp;<?php echo os_get_country_list('country',STORE_COUNTRY, 'onChange="changeselect();"') . '&nbsp;'; ?></p>
<p><strong><?php echo TEXT_STATE; ?></strong>&nbsp;
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
document.write('<select name="state">');
document.write('</select>');
changeselect("<?php echo os_db_prepare_input(@$_POST['state']); ?>");
-->
</script>
</p>
<p><strong><?php echo TEXT_TEL; ?></strong>&nbsp;<input type="text" name="TELEPHONE" value="(123) 123-45-67" /></p>
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
<p><strong><?php echo TEXT_PASSWORD; ?></strong>&nbsp;<input style="color:red;" id="password" type="text" name="PASSWORD" /></p>
</fieldset>

<fieldset class="form">
<legend><?php echo TITLE_SHOP_CONFIG; ?></legend>
<p><strong><?php echo TEXT_STORE; ?></strong>&nbsp;<input type="text" name="STORE_NAME" value="<?php echo AUTO_INPUT_STORE_NAME; ?>" /></p>
<p><strong><?php echo TEXT_COMPANY; ?></strong>&nbsp;<input type="text" name="COMPANY" value="<?php echo AUTO_INPUT_COMPANY; ?>" /></p>
<p><strong><?php echo TEXT_EMAIL_FROM; ?></strong>&nbsp;<input type="text" name="EMAIL_ADRESS_FROM" value="admin@admin.com" /></p>
</fieldset>

</form>

		<div class="newsection"></div>
		</div>
		<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
		</div>
		</div>
	</div>
</div>

<div class="clr"></div>


			</div>
		</div>
		<div id="footer1">
			<div id="footer2">
				<div id="footer3"></div>
			</div>
		</div>
		
<?php _copy(); ?>
</body>
</html>