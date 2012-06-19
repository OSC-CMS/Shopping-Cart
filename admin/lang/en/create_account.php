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

define('NAVBAR_TITLE', 'Create an Account');

define('HEADING_TITLE', 'Customer Account Admin');

define('TEXT_ORIGIN_LOGIN', '<font color="#FF0000"><small><b>NOTE:</b></font></small> If you already have an account with us, please login at the <a href="%s"><u>login page</u></a>.');

define('EMAIL_SUBJECT', 'Welcome to ' . STORE_NAME);
define('EMAIL_GREET_MR', 'Dear Mr. ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n\n");
define('EMAIL_GREET_MS', 'Dear Ms. ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n\n");
define('EMAIL_GREET_NONE', 'Dear ' . stripslashes($HTTP_POST_VARS['firstname']) . ',' . "\n\n");
define('EMAIL_WELCOME', 'We welcome you to <b>' . STORE_NAME . '</b>.' . "\n\n");
define('EMAIL_TEXT', 'You can now take part in the <b>various services</b> we have to offer you. Some of these services include:' . "\n\n" . '<li><b>Permanent Cart</b> - Any products added to your online cart remain there until you remove them, or check them out.' . "\n" . '<li><b>Address Book</b> - We can now deliver your products to another address other than yours! This is perfect to send birthday gifts direct to the birthday persons themselves.' . "\n" . '<li><b>Order History</b> - View your history of purchases that you have made with us.' . "\n" . '<li><b>Products Reviews</b> - Share your opinions on products with our other customers.' . "\n\n");
define('EMAIL_CONTACT', 'For help with any of our online services, please eMail the store-owner: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_WARNING', '<b>Note:</b> This eMail address was given to us by one of our customers. If you did not signup to be a member, please send an eMail to ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n");
define('ENTRY_PAYMENT_UNALLOWED','Unallowed paymentmodules:');
define('ENTRY_SHIPPING_UNALLOWED','Unallowed shippingmodules:');

define('CATEGORY_EXTRA_FIELDS','Additional information');
define('ENTRY_SECOND_NAME', 'Отчество');
?>