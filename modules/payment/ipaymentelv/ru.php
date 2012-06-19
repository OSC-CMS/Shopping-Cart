<?php
/*
#####################################
# OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

  define('MODULE_PAYMENT_TYPE_PERMISSION', 'bt');

  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_TITLE', 'Lastschriftverfahren');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_DESCRIPTION', 'Lastschriftverfahren ьber Ipayment Gateway');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK', 'Bankeinzug');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_EMAIL_FOOTER', 'Hinweis: Sie k&ouml;nnen sich unser Faxformular unter ' . HTTP_SERVER . DIR_WS_CATALOG . MODULE_PAYMENT_IPAYMENTELV_URL_NOTE . ' herunterladen und es ausgef&uuml;llt an uns zur&uuml;cksenden.');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_INFO', 'Bitte beachten Sie, dass das Lastschriftverfahren <b>nur</b> von einem <b>deutschen Girokonto</b> aus m&ouml;glich ist');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_OWNER', 'Kontoinhaber:');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_NUMBER', 'Kontonummer:');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_BLZ', 'BLZ:');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_NAME', 'Bank:');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_FAX', 'Einzugserm&auml;chtigung wird per Fax best&auml;tigt');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_INFO','');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR', '<font color="#FF0000">FEHLER: </font>');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR_1', 'Kontonummer und BLZ stimmen nicht &uuml;berein!<br />Bitte &uuml;berpr&uuml;fen Sie Ihre Angaben nochmals.');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR_2', 'Fьr diese Kontonummer ist kein Pr&uuml;fziffernverfahren definiert!');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR_3', 'Kontonummer nicht pr&uuml;fbar!');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR_4', 'Kontonummer nicht pr&uuml;fbar!<br />Bitte &uuml;berpr&uuml;fen Sie Ihre Angaben nochmals.');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR_5', 'Bankleitzahl nicht gefunden!<br />Bitte &uuml;berpr&uuml;fen Sie Ihre Angaben nochmals.');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR_8', 'Fehler bei der Bankleitzahl oder keine Bankleitzahl angegeben!');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_ERROR_9', 'Keine Kontonummer angegeben!');

  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_NOTE', 'Hinweis:');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_NOTE2', 'Wenn Sie aus Sicherheitsbedenken keine Bankdaten ьber das Internet<br />&uuml;bertragen wollen, k&ouml;nnen Sie sich unser ');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_NOTE3', 'Faxformular');
  define('MODULE_PAYMENT_IPAYMENTELV_TEXT_NOTE4', ' herunterladen und uns ausgef&uuml;llt zusenden.');

  define('JS_BANK_BLZ', 'Bitte geben Sie die BLZ Ihrer Bank ein!\n');
  define('JS_BANK_NAME', 'Bitte geben Sie den Namen Ihrer Bank ein!\n');
  define('JS_BANK_NUMBER', 'Bitte geben Sie Ihre Kontonummer ein!\n');
  define('JS_BANK_OWNER', 'Bitte geben Sie den Namen des Kontobesitzers ein!\n');
  
  define('MODULE_PAYMENT_IPAYMENTELV_DATABASE_BLZ_TITLE' , 'Datenbanksuche f&uuml;r die BLZ verwenden?');
define('MODULE_PAYMENT_IPAYMENTELV_DATABASE_BLZ_DESC' , 'M&ouml;chten Sie die Datenbanksuche f&uuml;r die BLZ verwenden? Vergewissern Sie sich, daЯ der Table banktransfer_blz vorhanden und richtig eingerichtet ist!');
define('MODULE_PAYMENT_IPAYMENTELV_URL_NOTE_TITLE' , 'Fax-URL');
define('MODULE_PAYMENT_IPAYMENTELV_URL_NOTE_DESC' , 'Die Fax-Best&auml;tigungsdatei. Diese muss im Catalog-Verzeichnis liegen');
define('MODULE_PAYMENT_IPAYMENTELV_FAX_CONFIRMATION_TITLE' , 'Fax Best&auml;tigung erlauben');
define('MODULE_PAYMENT_IPAYMENTELV_FAX_CONFIRMATION_DESC' , 'M&ouml;chten Sie die Fax Best&auml;tigung erlauben?');
define('MODULE_PAYMENT_IPAYMENTELV_SORT_ORDER_TITLE' , 'Anzeigereihenfolge');
define('MODULE_PAYMENT_IPAYMENTELV_SORT_ORDER_DESC' , 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_IPAYMENTELV_ORDER_STATUS_ID_TITLE' , 'Bestellstatus festlegen');
define('MODULE_PAYMENT_IPAYMENTELV_ORDER_STATUS_ID_DESC' , 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_IPAYMENTELV_ZONE_TITLE' , 'Zahlungszone');
define('MODULE_PAYMENT_IPAYMENTELV_ZONE_DESC' , 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_IPAYMENTELV_ALLOWED_TITLE' , 'Erlaubte Zonen');
define('MODULE_PAYMENT_IPAYMENTELV_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_IPAYMENTELV_ID_TITLE' , 'Kundennummer');
define('MODULE_PAYMENT_IPAYMENTELV_ID_DESC' , 'Kundennummer, welche f&uuml;r iPayment verwendet wird');
define('MODULE_PAYMENT_IPAYMENTELV_STATUS_TITLE' , 'iPayment Modul aktivieren');
define('MODULE_PAYMENT_IPAYMENTELV_STATUS_DESC' , 'M&ouml;chten Sie Zahlungen per iPayment akzeptieren?');
define('MODULE_PAYMENT_IPAYMENTELV_PASSWORD_TITLE' , 'Benutzer-Passwort');
define('MODULE_PAYMENT_IPAYMENTELV_PASSWORD_DESC' , 'Benutzer-Passwort welches f&uuml;r iPayment verwendet wird');
define('MODULE_PAYMENT_IPAYMENTELV_USER_ID_TITLE' , 'Benutzer ID');
define('MODULE_PAYMENT_IPAYMENTELV_USER_ID_DESC' , 'Benutzer ID welche f&uuml;r iPayment verwendet wird');
define('MODULE_PAYMENT_IPAYMENTELV_CURRENCY_TITLE' , 'Transaktionswдhrung');
define('MODULE_PAYMENT_IPAYMENTELV_CURRENCY_DESC' , 'W&auml;hrung, welche f&uuml;r Transaktionen verwendet wird');
define('MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_IBAN','IBAN: (optional)');
define('MODULE_PAYMENT_IPAYMENTELV_TEXT_IBAN','Falls Sie ьber Einen IBAN Ihres Bankkontos verfьgen, kцnnen BLZ + Konto ohne Eintrag bleiben.');

// error messages
define('MODULE_PAYMENT_IPAYMENTELV_TEXT_JS_BANK_NAME','Bitte geben Sie einen Banknamen ein!\n');
define('MODULE_PAYMENT_IPAYMENTELV_TEXT_JS_BANK_OWNER','Bitte geben Sie einen Kontoinhaber an!\n');
define('MODULE_PAYMENT_IPAYMENTELV_TEXT_JS_BANK_ALL_ERROR','Bitte geben Sie entweder Ihre Kontonummer + BLZ an, oder Ihren IBAN.\n');
define('MODULE_PAYMENT_IPAYMENTELV_TEXT_JS_BANK_ACCOUNT_ERROR','Bite geben Sie Ihre Kontonummer an!\n');
define('MODULE_PAYMENT_IPAYMENTELV_TEXT_JS_BANK_BLZ_ERROR','Bitte geben Sie eine Bankleitzahl ein!\n');

?>