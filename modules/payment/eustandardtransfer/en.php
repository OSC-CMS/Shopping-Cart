<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

  define('MODULE_PAYMENT_EUTRANSFER_TEXT_TITLE', 'EU-Standard Bank Transfer');
  define('MODULE_PAYMENT_EUSTANDARDTRANSFER_TEXT_TITLE', 'EU-Standard Bank Transfer');
  define('MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION', '<br />Please use the following details to transfer your total order value:<br />' .
                                                         '<br />Bank Name: ' . MODULE_PAYMENT_EUTRANSFER_BANKNAM .
                                                         '<br />Branch: ' . MODULE_PAYMENT_EUTRANSFER_BRANCH .
                                                         '<br />Account Name: ' . MODULE_PAYMENT_EUTRANSFER_ACCNAM .
                                                         '<br />Account No.: ' . MODULE_PAYMENT_EUTRANSFER_ACCNUM .
                                                         '<br />IBAN:: ' . MODULE_PAYMENT_EUTRANSFER_ACCIBAN .
                                                         '<br />BIC/SWIFT: ' . MODULE_PAYMENT_EUTRANSFER_BANKBIC .
//                                                         '<br />Sort Code: ' . MODULE_PAYMENT_EUTRANSFER_SORTCODE .
                                                         '<br /><br />Your order will not ship until we receive payment in the above account.<br />');
  define('MODULE_PAYMENT_EUTRANSFER_TEXT_EMAIL_FOOTER', str_replace('<br />','\n',MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION));

    define('MODULE_PAYMENT_EUTRANSFER_STATUS_TITLE','Allow Bank Transfer Payment');
  define('MODULE_PAYMENT_EUTRANSFER_STATUS_DESC','Do you want to accept bank transfer order payments?');
define('MODULE_PAYMENT_EUTRANSFER_TEXT_INFO','');
  define('MODULE_PAYMENT_EUTRANSFER_BRANCH_TITLE','Branch Location');
  define('MODULE_PAYMENT_EUTRANSFER_BRANCH_DESC','The brach where you have your account.');

  define('MODULE_PAYMENT_EUTRANSFER_BANKNAM_TITLE','Bank Name');
  define('MODULE_PAYMENT_EUTRANSFER_BANKNAM_DESC','Your full bank name');

  define('MODULE_PAYMENT_EUTRANSFER_ACCNAM_TITLE','Bank Account Name');
  define('MODULE_PAYMENT_EUTRANSFER_ACCNAM_DESC','The name associated with the account.');

  define('MODULE_PAYMENT_EUTRANSFER_ACCNUM_TITLE','Bank Account No.');
  define('MODULE_PAYMENT_EUTRANSFER_ACCNUM_DESC','Your account number.');

  define('MODULE_PAYMENT_EUTRANSFER_ACCIBAN_TITLE','Bank Account IBAN');
  define('MODULE_PAYMENT_EUTRANSFER_ACCIBAN_DESC','International account id.<br />(ask your bank if you don\'t know it)');

  define('MODULE_PAYMENT_EUTRANSFER_BANKBIC_TITLE','Bank Bic');
  define('MODULE_PAYMENT_EUTRANSFER_BANKBIC_DESC','International bank id.<br />(ask your bank if you don\'t know it)');

  define('MODULE_PAYMENT_EUTRANSFER_SORT_ORDER_TITLE','Module Sort order of display.');
  define('MODULE_PAYMENT_EUTRANSFER_SORT_ORDER_DESC','Sort order of display. Lowest is displayed first.');

  define('MODULE_PAYMENT_EUSTANDARDTRANSFER_ALLOWED_TITLE' , 'Erlaubte Zonen');
 define('MODULE_PAYMENT_EUSTANDARDTRANSFER_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');


?>