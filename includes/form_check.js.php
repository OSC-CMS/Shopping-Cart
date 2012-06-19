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

?>
<script type="text/javascript"><!--
var form = "";
var submitted = false;
var error = false;
var error_message = "";

function check_input(field_name, field_size, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == '' || field_value.length < field_size) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_radio(field_name, message) {
  var isChecked = false;

  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var radio = form.elements[field_name];

    for (var i=0; i<radio.length; i++) {
      if (radio[i].checked == true) {
        isChecked = true;
        break;
      }
    }

    if (isChecked == false) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_select(field_name, field_default, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == field_default) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_password(field_name_1, field_name_2, field_size, message_1, message_2) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password = form.elements[field_name_1].value;
    var confirmation = form.elements[field_name_2].value;

    if (password == '' || password.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password != confirmation) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    }
  }
}

function check_password_new(field_name_1, field_name_2, field_name_3, field_size, message_1, message_2, message_3) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password_current = form.elements[field_name_1].value;
    var password_new = form.elements[field_name_2].value;
    var password_confirmation = form.elements[field_name_3].value;

    if (password_current == '' || password_current.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password_new == '' || password_new.length < field_size) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    } else if (password_new != password_confirmation) {
      error_message = error_message + "* " + message_3 + "\n";
      error = true;
    }
  }
}

function check_form(form_name) {
  if (submitted == true) {
    alert(unescape("<?php echo os_js_lang(JS_ERROR_SUBMITTED); ?>"));
    return false;
  }
  
  

  error = false;
  form = form_name;
  error_message = unescape("<?php echo os_js_lang(JS_ERROR); ?>");

<?php if (ACCOUNT_GENDER == 'true') echo '  check_radio("gender", "' . ENTRY_GENDER_ERROR . '");' . "\n"; ?>

  check_input("firstname", <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>, "<?php echo os_js_lang(ENTRY_FIRST_NAME_ERROR); ?>");
  
<?php if (ACCOUNT_LAST_NAME == 'true') echo '  check_input("lastname", '.ENTRY_LAST_NAME_MIN_LENGTH.', "'.os_js_lang(ENTRY_LAST_NAME_ERROR).'");' . "\n"; ?>

<?php if (ACCOUNT_DOB == 'true') echo '  check_input("dob", ' . ENTRY_DOB_MIN_LENGTH . ', "' . os_js_lang(ENTRY_DATE_OF_BIRTH_ERROR) . '");' . "\n"; ?>

  check_input("email_address", <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>, "<?php echo os_js_lang(ENTRY_EMAIL_ADDRESS_ERROR); ?>");

<?php if (ACCOUNT_STREET_ADDRESS == 'true') echo '  check_input("street_address", ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ', "' . os_js_lang(ENTRY_STREET_ADDRESS_ERROR) . '");' . "\n"; ?>

<?php if (ACCOUNT_POSTCODE == 'true') echo '  check_input("postcode", ' . ENTRY_POSTCODE_MIN_LENGTH . ', "' . os_js_lang(ENTRY_POST_CODE_ERROR) . '");' . "\n"; ?>

<?php if (ACCOUNT_CITY == 'true') echo '  check_input("city", ' . ENTRY_CITY_MIN_LENGTH . ', "' . os_js_lang(ENTRY_CITY_ERROR) . '");' . "\n"; ?>

<?php if (ACCOUNT_STATE == 'true') echo '  check_input("state", ' . ENTRY_STATE_MIN_LENGTH . ', "' . os_js_lang(ENTRY_STATE_ERROR) . '");' . "\n"; ?>

<?php if (ACCOUNT_COUNTRY == 'true') echo ' check_select("country", "", "' . os_js_lang(ENTRY_COUNTRY_ERROR) . '");' . "\n"; ?>

<?php if (ACCOUNT_TELE == 'true') echo '  check_input("telephone", ' . ENTRY_TELEPHONE_MIN_LENGTH . ', "' . os_js_lang(ENTRY_TELEPHONE_NUMBER_ERROR) . '");' . "\n"; ?>

  check_password("password", "confirmation", <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>, "<?php echo os_js_lang(ENTRY_PASSWORD_ERROR); ?>", "<?php echo os_js_lang(ENTRY_PASSWORD_ERROR_NOT_MATCHING); ?>");
  check_password_new("password_current", "password_new", "password_confirmation", <?php echo os_js_lang(ENTRY_PASSWORD_MIN_LENGTH); ?>, "<?php echo os_js_lang(ENTRY_PASSWORD_ERROR); ?>", "<?php echo os_js_lang(ENTRY_PASSWORD_NEW_ERROR); ?>", "<?php echo os_js_lang(ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING); ?>");

  if (error == true) {
    alert(unescape(error_message));
    return false;
  } else {
    submitted = true;
    return true;
  }
}

		function checkform(of)
		{
			if(!document.getElementById || !document.createTextNode){return;}
			if(!document.getElementById('required')){return;}
			var errorID='errormsg';
			var errorClass='error'
			var errorMsg='<?php echo JS_ERROR; ?>';
			var errorImg='media/icons/warning.gif';
			var errorAlt='<?php echo ICON_ERROR; ?>';
			var errorTitle='<?php echo ICON_ERROR; ?>';
			var reqfields=document.getElementById('required').value.split(',');
			if(document.getElementById(errorID))
			{
				var em=document.getElementById(errorID);
				em.parentNode.removeChild(em);
			}
			for(var i=0;i<reqfields.length;i++)
			{
				var f=document.getElementById(reqfields[i]);
				if(!f){continue;}
				if(f.previousSibling && /img/i.test(f.previousSibling.nodeName))
				{
					f.parentNode.removeChild(f.previousSibling);
				}
				f.className='';
			}
			for(var i=0;i<reqfields.length;i++)
			{
				var f=document.getElementById(reqfields[i]);
				if(!f){continue;}

				switch(f.type.toLowerCase())
				{
					case 'text':
						if(f.name=='firstname' && f.value.length<<?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='lastname' && f.value.length<<?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='dob' && f.value=='' && f.id!='email'){cf_adderr(f)}				
						if(f.name=='street_address' && f.value.length<<?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='postcode' && f.value.length<<?php echo ENTRY_POSTCODE_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='city' && f.value.length<<?php echo ENTRY_CITY_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='telephone' && f.value.length<<?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='state' && f.value.length<<?php echo ENTRY_STATE_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.id=='email' && !cf_isEmailAddr(f.value)){cf_adderr(f)}							
					break;
					case 'password':
						if(f.name=='password' && f.value.length<<?php echo ENTRY_PASSWORD_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='confirmation' && f.value.length<<?php echo ENTRY_PASSWORD_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='password_current' && f.value.length<<?php echo ENTRY_PASSWORD_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='password_new' && f.value.length<<?php echo ENTRY_PASSWORD_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
						if(f.name=='password_confirmation' && f.value.length<<?php echo ENTRY_PASSWORD_MIN_LENGTH; ?> && f.id!='email'){cf_adderr(f)}				
					break;
					case 'textarea':
						if(f.value==''){cf_adderr(f)}							
					break;
					case 'checkbox':
						if(!f.checked){cf_adderr(f)}							
					break;
					case 'radio':
						if(f.value=='f' && !f.checked){cf_adderr(f)}							
					break;
					case 'select-one':
						if(!f.selectedIndex && f.selectedIndex==1){cf_adderr(f)}							
					break;
				}
			}
			return !document.getElementById(errorID);

			function cf_adderr(o)
			{
				var errorIndicator=document.createElement('img');
				errorIndicator.alt=errorAlt;
				errorIndicator.src=errorImg;
				errorIndicator.title=errorTitle;
				o.className=errorClass;
				o.parentNode.insertBefore(errorIndicator,o);

				if(!document.getElementById(errorID))
				{
					var em=document.createElement('div');
					em.id=errorID;
					var newp=document.createElement('p');
					newp.appendChild(document.createTextNode(errorMsg))
					em.appendChild(newp);
					var newul=document.createElement('ul');		
					em.appendChild(newul);

					for(var i=0;i<of.getElementsByTagName('input').length;i++)
					{
						if(/image/i.test(of.getElementsByTagName('input')[i].type))
						{
							var sb=of.getElementsByTagName('input')[i];
							break;
						}
					}
					if(sb)
					{
						sb.parentNode.insertBefore(em,sb);
					}	
				} 
				var em=document.getElementById(errorID).getElementsByTagName('ul')[0];
				var newli=document.createElement('li');
				var newa=document.createElement('a');
				for(var i=0;i<of.getElementsByTagName('label').length;i++)
				{
					if(of.getElementsByTagName('label')[i].htmlFor==o.id)
					{
						var txt=of.getElementsByTagName('label')[i].firstChild.nodeValue + ' ' + of.getElementsByTagName('label')[i].title;
						break;
					}
				}
	
				newa.appendChild(document.createTextNode(txt));
				newa.href='#'+f.id;
				newa.onclick=function()
				{
					var loc=this.href.match(/#(\w.+)/)[1];
					document.getElementById(loc).focus();
					return false;
				}
				newli.appendChild(newa);
				em.appendChild(newli);
			}
			function cf_isEmailAddr(str) 
			{
			    return str.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/);
			}
		}
//--></script>