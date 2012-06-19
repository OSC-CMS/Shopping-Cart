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

define('HEADING_TITLE', 'Customer Groups');

define('ENTRY_CUSTOMERS_FSK18','Lock buy-function for FSK18 Products?');
define('ENTRY_CUSTOMERS_FSK18_DISPLAY','Display FSK18 Products?');
define('ENTRY_CUSTOMERS_STATUS_ADD_TAX','Show tax in order total');
define('ENTRY_CUSTOMERS_STATUS_MIN_ORDER','Minimum order value:');
define('ENTRY_CUSTOMERS_STATUS_MAX_ORDER','Maximum order value:');
define('ENTRY_CUSTOMERS_STATUS_BT_PERMISSION','Via Bank Collection');
define('ENTRY_CUSTOMERS_STATUS_CC_PERMISSION','Via Credit Card');
define('ENTRY_CUSTOMERS_STATUS_COD_PERMISSION','Via Cash on Delivery');
define('ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES','Discount');
define('ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED','Enter not allowed Payment Methods');
define('ENTRY_CUSTOMERS_STATUS_PUBLIC','Public');
define('ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED','Enter not allowed Shipping Modules');
define('ENTRY_CUSTOMERS_STATUS_SHOW_PRICE','Price');
define('ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX','Prices incl. Tax');
define('ENTRY_CUSTOMERS_STATUS_WRITE_REVIEWS','Customer group is allowed to write reviews?');
define('ENTRY_CUSTOMERS_STATUS_READ_REVIEWS','Customer group is allowed to read reviews?');
define('ENTRY_CUSTOMERS_STATUS_READ_REVIEWS_DISPLAY','Customer group is allowed to read reviews?');
define('ENTRY_GRADUATED_PRICES','Graduated Prices');
define('ENTRY_NO','No');
define('ENTRY_OT_XMEMBER', 'Customer Discount on order total ? :');
define('ENTRY_YES','Yes');

define('ERROR_REMOVE_DEFAULT_CUSTOMER_STATUS', 'Error: You can not delete the default customer group. Please set another group to default customer group and try again.');
define('ERROR_REMOVE_DEFAULT_CUSTOMERS_STATUS','ERROR! You cant delete a standardgroup');
define('ERROR_STATUS_USED_IN_CUSTOMERS', 'Error: This customer group is actually in use.');
define('ERROR_STATUS_USED_IN_HISTORY', 'Error: This customer group is actually in use for order history.');

define('TABLE_HEADING_ACTION','Action');
define('TABLE_HEADING_CUSTOMERS_GRADUATED','Graduated Price');
define('TABLE_HEADING_CUSTOMERS_STATUS','Customers Group');
define('TABLE_HEADING_CUSTOMERS_UNALLOW','not allowed Paymentmethods');
define('TABLE_HEADING_CUSTOMERS_UNALLOW_SHIPPING','not allowed Shipping');
define('TABLE_HEADING_DISCOUNT','Discount');
define('TABLE_HEADING_TAX_PRICE','Price / Tax');

define('TAX_NO','excl');
define('TAX_YES','incl');

define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS_STATUS', 'Existing customer groups:');

define('TEXT_INFO_CUSTOMERS_FSK18_DISPLAY_INTRO','<b>FSK18 Products</b>');
define('TEXT_INFO_CUSTOMERS_FSK18_INTRO','<b>FSK18 Lock</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO','<b>If prices incl. tax = set to "No"</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_MIN_ORDER_INTRO','Define a minimum order value or leave the field empty.');
define('TEXT_INFO_CUSTOMERS_STATUS_MAX_ORDER_INTRO','Define a maximum order value or leave the field empty.');
define('TEXT_INFO_CUSTOMERS_STATUS_BT_PERMISSION_INTRO', '<b>Shall we allow customers of this group to pay via bank collection?</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_CC_PERMISSION_INTRO', '<b>Shall we allow customers of this group to pay with credit cards?</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_COD_PERMISSION_INTRO', '<b>Shall we allow customers of this group to pay COD?</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO','<b>Discount on product attributes</b><br>(Max. % Discount on single product)');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO','<b>Discount on total order</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE', 'Discount (0 to 100%):');
define('TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO', 'Please define a discount between 0 and 100% which is used on each displayed product.');
define('TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO','<b>Graduated Prices</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_IMAGE', 'Customers Group Image:');
define('TEXT_INFO_CUSTOMERS_STATUS_NAME','<b>Groupname</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO','<b>not allowed Payment Methods</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO','<b>Show Public ?</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO','<b>not allowed Shipping Modules</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO','<b>Show price in shop</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO', 'Do you want to display prices inclusive or exclusive tax?');
define('TEXT_INFO_CUSTOMERS_STATUS_WRITE_REVIEWS_INTRO','<b>Productsreview write</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_READ_REVIEWS_INTRO', '<b>Productsreview read</b>');

define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this customer group?');
define('TEXT_INFO_EDIT_INTRO', 'Please make all neccessary changes');
define('TEXT_INFO_INSERT_INTRO', 'Please create a new customer group within all neccessary values.');

define('TEXT_INFO_HEADING_DELETE_CUSTOMERS_STATUS', 'Delete Customer Group');
define('TEXT_INFO_HEADING_EDIT_CUSTOMERS_STATUS','Edit Group Data');
define('TEXT_INFO_HEADING_NEW_CUSTOMERS_STATUS', 'New Customer Group');

define('TEXT_INFO_CUSTOMERS_STATUS_BASE', '<b>Base price for group</b>');
define('ENTRY_CUSTOMERS_STATUS_BASE', 'What price will be shown for this group.');

// 

define('TEXT_PUBLIC',', public');
define('TABLE_HEADING_ICON','Icon');
define('TABLE_HEADING_USERS','Users');

define('TEXT_INFO_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_INTRO', '<b>Accumulated limit</b>');
define('ENTRY_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_DISPLAY','');

define('TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_INTRO', '<b>Accumulated orders status:</b>');
define('TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_DISPLAY', '');

?>