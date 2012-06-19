<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

function os_check_file_name ($name)
{
   $name = strtolower(trim($name));
   $name = str_replace('/','',$name);
   $name = str_replace('\\','',$name);
   $name = str_replace('.','',$name);
   $name = str_replace('..','',$name);
   $name = str_replace('*','',$name);
   return $name;
}

function os_check_path ($name)
{
   $name = strtolower(trim($name));
   $name = str_replace('\\','/',$name);
   $name = str_replace('..','',$name);
   $name = str_replace('*','',$name);
   return $name;
}

function js_checkForm()
{
$value = '
function checkForm() {
  var error = 0;
  var error_message = unescape("'.os_js_lang(JS_ERROR).'");

  var review = document.getElementById("product_reviews_write").review.value;

  if (review.length < '.REVIEW_TEXT_MIN_LENGTH.') {
    error_message = error_message + unescape("'.os_js_lang(JS_REVIEW_TEXT).'");
    error = 1;
  }

  if (!((document.getElementById("product_reviews_write").rating[0].checked) || (document.getElementById("product_reviews_write").rating[1].checked) || (document.getElementById("product_reviews_write").rating[2].checked) || (document.getElementById("product_reviews_write").rating[3].checked) || (document.getElementById("product_reviews_write").rating[4].checked))) {
    error_message = error_message + unescape("'.os_js_lang(JS_REVIEW_RATING).'");
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}';

 return $value;
}

function js_check_form_advanced_search()
{
 $value = "
function check_form() {
  var error_message = unescape(\"".os_js_lang(JS_ERROR)."\");
  var error_found = false;
  var error_field;
  var keywords = document.getElementById(\"advanced_search\").keywords.value;
  var pfrom = document.getElementById(\"advanced_search\").pfrom.value;
  var pto = document.getElementById(\"advanced_search\").pto.value;
  var pfrom_float;
  var pto_float;

  if ( (keywords == '' || keywords.length < 1) && (pfrom == '' || pfrom.length < 1) && (pto == '' || pto.length < 1) ) {
    error_message = error_message + unescape(\"".os_js_lang(JS_AT_LEAST_ONE_INPUT)."\");
    error_field = document.getElementById(\"advanced_search\").keywords;
    error_found = true;
  }

  if (pfrom.length > 0) {
    pfrom_float = parseFloat(pfrom);
    if (isNaN(pfrom_float)) {
      error_message = error_message + unescape(\"".os_js_lang(JS_PRICE_FROM_MUST_BE_NUM)."\");
      error_field = document.getElementById(\"advanced_search\").pfrom;
      error_found = true;
    }
  } else {
    pfrom_float = 0;
  }

  if (pto.length > 0) {
    pto_float = parseFloat(pto);
    if (isNaN(pto_float)) {
      error_message = error_message + unescape(\"".os_js_lang(JS_PRICE_TO_MUST_BE_NUM)."\");
      error_field = document.getElementById(\"advanced_search\").pto;
      error_found = true;
    }
  } else {
    pto_float = 0;
  }

  if ( (pfrom.length > 0) && (pto.length > 0) ) {
    if ( (!isNaN(pfrom_float)) && (!isNaN(pto_float)) && (pto_float < pfrom_float) ) {
      error_message = error_message + unescape(\"".os_js_lang(JS_PRICE_TO_LESS_THAN_PRICE_FROM)."\");
      error_field = document.getElementById(\"advanced_search\").pto;
      error_found = true;
    }
  }

  if (error_found == true) {
    alert(error_message);
    error_field.focus();
    return false;
  }
}

function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
}
";

return $value;
}


?>