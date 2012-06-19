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

define('TABLE_HEADING_CONFIGURATION_TITLE', 'Title');
define('TABLE_HEADING_CONFIGURATION_VALUE', 'Value');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_INFO_DATE_ADDED', 'Date Added:');
define('TEXT_INFO_LAST_MODIFIED', 'Last Modified:');

// language definitions for config
define('STORE_NAME_TITLE' , 'Store Name');
define('STORE_NAME_DESC' , 'The name of my store');
define('STORE_OWNER_TITLE' , 'Store Owner');
define('STORE_OWNER_DESC' , 'The name of my store owner');
define('STORE_OWNER_EMAIL_ADDRESS_TITLE' , 'eMail Adress');
define('STORE_OWNER_EMAIL_ADDRESS_DESC' , 'The eMail Adress of my store owner');

define('EMAIL_FROM_TITLE' , 'eMail from');
define('EMAIL_FROM_DESC' , 'The eMail Adress used in (sent) eMails.');

define('STORE_COUNTRY_TITLE' , 'Country');
define('STORE_COUNTRY_DESC' , 'The country my store is located in <br /><br /><b>Note: Please remember to update the store zone.</b>');
define('STORE_ZONE_TITLE' , 'Zone');
define('STORE_ZONE_DESC' , 'The zone my store is located in.');

define('EXPECTED_PRODUCTS_SORT_TITLE' , 'Expected sort order');
define('EXPECTED_PRODUCTS_SORT_DESC' , 'This is the sort order used in the expected products box.');
define('EXPECTED_PRODUCTS_FIELD_TITLE' , 'Expexted sort field');
define('EXPECTED_PRODUCTS_FIELD_DESC' , 'The column to sort by in the expected products box.');

define('USE_DEFAULT_LANGUAGE_CURRENCY_TITLE' , 'Switch to default language currency');
define('USE_DEFAULT_LANGUAGE_CURRENCY_DESC' , 'Automatically switch to the languages currency when it is changed.');

define('SEND_EXTRA_ORDER_EMAILS_TO_TITLE' , 'Send extra order eMails to:');
define('SEND_EXTRA_ORDER_EMAILS_TO_DESC' , 'Send extra order eMails to the following eMail adresses, in this format: Name1 &lt;eMail@adress1&gt;, Name2 &lt;eMail@adress2&gt;');

define('SEARCH_ENGINE_FRIENDLY_URLS_TITLE' , 'Use Search-Engine Safe URLs?');
define('SEARCH_ENGINE_FRIENDLY_URLS_DESC' , 'Use search-engine safe urls for all site links.');

define('DISPLAY_CART_TITLE' , 'Display Cart After Adding a Product?');
define('DISPLAY_CART_DESC' , 'Display the shopping cart after adding a product or return back to their origin?');

define('ALLOW_GUEST_TO_TELL_A_FRIEND_TITLE' , 'Allow Guest To Tell a Friend?');
define('ALLOW_GUEST_TO_TELL_A_FRIEND_DESC' , 'Allow guests to tell a friend about a product?');

define('ADVANCED_SEARCH_DEFAULT_OPERATOR_TITLE' , 'Default Search Operator');
define('ADVANCED_SEARCH_DEFAULT_OPERATOR_DESC' , 'Default search operators.');

define('STORE_NAME_ADDRESS_TITLE' , 'Store Adress and Phone');
define('STORE_NAME_ADDRESS_DESC' , 'This is the Store Name, Adress and Phone used on printable documents and displayed online.');

define('SHOW_COUNTS_TITLE' , 'Show Category Counts');
define('SHOW_COUNTS_DESC' , 'Count recursively how many products are in each category');

define('DISPLAY_PRICE_WITH_TAX_TITLE' , 'Display Prices with Tax');
define('DISPLAY_PRICE_WITH_TAX_DESC' , 'Display prices with tax included (true) or add the tax at the end (false)');

define('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN_TITLE' , 'Customers Status of Administration Members');
define('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN_DESC' , 'Choose the customers status for Members of the Administration Team!');
define('DEFAULT_CUSTOMERS_STATUS_ID_GUEST_TITLE' , 'Customers Status Guest');
define('DEFAULT_CUSTOMERS_STATUS_ID_GUEST_DESC' , 'What would be the default customers status for a guest before logged in?');
define('DEFAULT_CUSTOMERS_STATUS_ID_TITLE' , 'Customers Status for New Customers');
define('DEFAULT_CUSTOMERS_STATUS_ID_DESC' , 'What would be the default customers status for a new customer?');

define('ALLOW_ADD_TO_CART_TITLE' , 'Allow add to cart');
define('ALLOW_ADD_TO_CART_DESC' , 'Allow customers to add products into cart if groupsetting for "show prices" is set to 0');
define('ALLOW_DISCOUNT_ON_PRODUCTS_ATTRIBUTES_TITLE' , 'Allow discount on products attribute?');
define('ALLOW_DISCOUNT_ON_PRODUCTS_ATTRIBUTES_DESC' , 'Allow customers to get discount on attribute price (if main product is not a "special" product)');
define('CURRENT_TEMPLATE_TITLE' , 'Templateset (Theme)');
define('CURRENT_TEMPLATE_DESC' , 'Choose a Templateset (Theme). The Theme must be saved before in the following folder.');

define('CC_KEYCHAIN_TITLE','CC String');
define('CC_KEYCHAIN_DESC','String to encrypt CC number (please change!)');
define('ADMIN_DROP_DOWN_NAVIGATION_TITLE','Drop-down menu style at admin side');
define('ADMIN_DROP_DOWN_NAVIGATION_DESC','Drop-down menu on the top (true) or standart menu on the left (false).');
define('AJAX_CART_TITLE','Ajax shopping');
define('AJAX_CART_DESC','Use ajax shopping cart (true) or standar shopping cart (false).');

define('ENTRY_FIRST_NAME_MIN_LENGTH_TITLE' , 'First Name');
define('ENTRY_FIRST_NAME_MIN_LENGTH_DESC' , 'Minimum length of first name');
define('ENTRY_LAST_NAME_MIN_LENGTH_TITLE' , 'Last Name');
define('ENTRY_LAST_NAME_MIN_LENGTH_DESC' , 'Minimum length of last name');
define('ENTRY_DOB_MIN_LENGTH_TITLE' , 'Date of Birth');
define('ENTRY_DOB_MIN_LENGTH_DESC' , 'Minimum length of date of birth');
define('ENTRY_EMAIL_ADDRESS_MIN_LENGTH_TITLE' , 'eMail Adress');
define('ENTRY_EMAIL_ADDRESS_MIN_LENGTH_DESC' , 'Minimum length of eMail adress');
define('ENTRY_STREET_ADDRESS_MIN_LENGTH_TITLE' , 'Street Adress');
define('ENTRY_STREET_ADDRESS_MIN_LENGTH_DESC' , 'Minimum length of street adress');
define('ENTRY_COMPANY_MIN_LENGTH_TITLE' , 'Company');
define('ENTRY_COMPANY_MIN_LENGTH_DESC' , 'Minimum length of company name');
define('ENTRY_POSTCODE_MIN_LENGTH_TITLE' , 'Post Code');
define('ENTRY_POSTCODE_MIN_LENGTH_DESC' , 'Minimum length of post code');
define('ENTRY_CITY_MIN_LENGTH_TITLE' , 'City');
define('ENTRY_CITY_MIN_LENGTH_DESC' , 'Minimum length of city');
define('ENTRY_STATE_MIN_LENGTH_TITLE' , 'State');
define('ENTRY_STATE_MIN_LENGTH_DESC' , 'Minimum length of state');
define('ENTRY_TELEPHONE_MIN_LENGTH_TITLE' , 'Telephone Number');
define('ENTRY_TELEPHONE_MIN_LENGTH_DESC' , 'Minimum length of telephone number');
define('ENTRY_PASSWORD_MIN_LENGTH_TITLE' , 'Password');
define('ENTRY_PASSWORD_MIN_LENGTH_DESC' , 'Minimum length of password');

define('CC_OWNER_MIN_LENGTH_TITLE' , 'Credit Card Owner Name');
define('CC_OWNER_MIN_LENGTH_DESC' , 'Minimum length of credit card owner name');
define('CC_NUMBER_MIN_LENGTH_TITLE' , 'Credit Card Number');
define('CC_NUMBER_MIN_LENGTH_DESC' , 'Minimum length of credit card number');

define('REVIEW_TEXT_MIN_LENGTH_TITLE' , 'Reviews');
define('REVIEW_TEXT_MIN_LENGTH_DESC' , 'Minimum length of review text');

define('MIN_DISPLAY_BESTSELLERS_TITLE' , 'Best Sellers');
define('MIN_DISPLAY_BESTSELLERS_DESC' , 'Minimum number of best sellers to display');
define('MIN_DISPLAY_ALSO_PURCHASED_TITLE' , 'Also Purchased');
define('MIN_DISPLAY_ALSO_PURCHASED_DESC' , 'Minimum number of products to display in the "This Customer Also Purchased" box');

define('MAX_ADDRESS_BOOK_ENTRIES_TITLE' , 'Address Book Entries');
define('MAX_ADDRESS_BOOK_ENTRIES_DESC' , 'Maximum address book entries a customer is allowed to have');
define('MAX_DISPLAY_SEARCH_RESULTS_TITLE' , 'Search Results');
define('MAX_DISPLAY_SEARCH_RESULTS_DESC' , 'Amount of products to list');
define('MAX_DISPLAY_ADMIN_PAGE_TITLE' , 'Products per page in admin');
define('MAX_DISPLAY_ADMIN_PAGE_DESC' , 'Maximum products per page in admin');
define('MAX_DISPLAY_PAGE_LINKS_TITLE' , 'Page Links');
define('MAX_DISPLAY_PAGE_LINKS_DESC' , 'Number of "number" links use for page-sets');
define('MAX_DISPLAY_SPECIAL_PRODUCTS_TITLE' , 'Special Products');
define('MAX_DISPLAY_SPECIAL_PRODUCTS_DESC' , 'Maximum number of products on special to display');
define('MAX_DISPLAY_FEATURED_PRODUCTS_TITLE' , 'Featured Products');
define('MAX_DISPLAY_FEATURED_PRODUCTS_DESC' , 'Maximum number of products on featured to display');
define('MAX_DISPLAY_NEW_PRODUCTS_TITLE' , 'New Products Module');
define('MAX_DISPLAY_NEW_PRODUCTS_DESC' , 'Maximum number of new products to display in a category');
define('MAX_DISPLAY_UPCOMING_PRODUCTS_TITLE' , 'Products Expected');
define('MAX_DISPLAY_UPCOMING_PRODUCTS_DESC' , 'Maximum number of products expected to display');
define('MAX_DISPLAY_MANUFACTURERS_IN_A_LIST_TITLE' , 'Manufacturers List');
define('MAX_DISPLAY_MANUFACTURERS_IN_A_LIST_DESC' , 'Used in manufacturers box; when the number of manufacturers exceeds this number, a drop-down list will be displayed instead of the default list');
define('MAX_MANUFACTURERS_LIST_TITLE' , 'Manufacturers Select Size');
define('MAX_MANUFACTURERS_LIST_DESC' , 'Used in manufacturers box; when this value is "1" the classic drop-down list will be used for the manufacturers box. Otherwise, a list-box with the specified number of rows will be displayed.');
define('MAX_DISPLAY_MANUFACTURER_NAME_LEN_TITLE' , 'Length of Manufacturers Name');
define('MAX_DISPLAY_MANUFACTURER_NAME_LEN_DESC' , 'Used in manufacturers box; maximum length of manufacturers name to display');
define('MAX_DISPLAY_NEW_REVIEWS_TITLE' , 'New Reviews');
define('MAX_DISPLAY_NEW_REVIEWS_DESC' , 'Maximum number of new reviews to display');
define('MAX_RANDOM_SELECT_REVIEWS_TITLE' , 'Selection of Random Reviews');
define('MAX_RANDOM_SELECT_REVIEWS_DESC' , 'How many records to select from to choose one random product review');
define('MAX_RANDOM_SELECT_NEW_TITLE' , 'Selection of Random New Products');
define('MAX_RANDOM_SELECT_NEW_DESC' , 'How many records to select from to choose one random new product to display');
define('MAX_RANDOM_SELECT_SPECIALS_TITLE' , 'Selection of Products on Special');
define('MAX_RANDOM_SELECT_SPECIALS_DESC' , 'How many records to select from to choose one random product special to display');
define('MAX_RANDOM_SELECT_FEATURED_TITLE' , 'Selection of Products on Featured');
define('MAX_RANDOM_SELECT_FEATURED_DESC' , 'How many records to select from to choose one random product featured to display');
define('MAX_DISPLAY_CATEGORIES_PER_ROW_TITLE' , 'Categories To List Per Row');
define('MAX_DISPLAY_CATEGORIES_PER_ROW_DESC' , 'How many categories to list per row');
define('MAX_DISPLAY_PRODUCTS_NEW_TITLE' , 'New Products Listing');
define('MAX_DISPLAY_PRODUCTS_NEW_DESC' , 'Maximum number of new products to display in new products page');
define('MAX_DISPLAY_BESTSELLERS_TITLE' , 'Best Sellers');
define('MAX_DISPLAY_BESTSELLERS_DESC' , 'Maximum number of best sellers to display');
define('MAX_DISPLAY_ALSO_PURCHASED_TITLE' , 'Also Purchased');
define('MAX_DISPLAY_ALSO_PURCHASED_DESC' , 'Maximum number of products to display in the "This Customer Also Purchased" box');
define('MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX_TITLE' , 'Customer Order History Box');
define('MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX_DESC' , 'Maximum number of products to display in the customer order history box');
define('MAX_DISPLAY_ORDER_HISTORY_TITLE' , 'Order History');
define('MAX_DISPLAY_ORDER_HISTORY_DESC' , 'Maximum number of orders to display in the order history page');
define('MAX_PRODUCTS_QTY_TITLE', 'Maximum Quantity');
define('MAX_PRODUCTS_QTY_DESC', 'Maximum quantity input length');
define('MAX_DISPLAY_NEW_PRODUCTS_DAYS_TITLE' , 'Maximum days for new products');
define('MAX_DISPLAY_NEW_PRODUCTS_DAYS_DESC' , 'Maximum quantity of days new products to display');


define('PRODUCT_IMAGE_THUMBNAIL_ACTIVE_TITLE' , 'Allow to use GD library for Product-Thumbnails');
define('PRODUCT_IMAGE_THUMBNAIL_ACTIVE_DESC' , 'Allow to use GD library for Product-Thumbnails. If false, upload product images into images directory manually by ftp.');
define('PRODUCT_IMAGE_THUMBNAIL_WIDTH_TITLE' , 'Width of Product-Thumbnails');
define('PRODUCT_IMAGE_THUMBNAIL_WIDTH_DESC' , 'Maximal Width of Product-Thumbnails in Pixel');
define('PRODUCT_IMAGE_THUMBNAIL_HEIGHT_TITLE' , 'Height of Product-Thumbnails');
define('PRODUCT_IMAGE_THUMBNAIL_HEIGHT_DESC' , 'Maximal Height of Product-Thumbnails in Pixel');

define('PRODUCT_IMAGE_INFO_ACTIVE_TITLE' , 'Allow to use GD library for Product-Info Images');
define('PRODUCT_IMAGE_INFO_ACTIVE_DESC' , 'Allow to use GD library for Product-Info Images. If false, upload product images into images directory manually by ftp.');
define('PRODUCT_IMAGE_INFO_WIDTH_TITLE' , 'Width of Product-Info Images');
define('PRODUCT_IMAGE_INFO_WIDTH_DESC' , 'Maximal Width of Product-Info Images in Pixel');
define('PRODUCT_IMAGE_INFO_HEIGHT_TITLE' , 'Height of Product-Info Images');
define('PRODUCT_IMAGE_INFO_HEIGHT_DESC' , 'Maximal Height of Product-Info Images in Pixel');

define('PRODUCT_IMAGE_POPUP_ACTIVE_TITLE' , 'Allow to use GD library for Popup Images');
define('PRODUCT_IMAGE_POPUP_ACTIVE_DESC' , 'Allow to use GD library for Popup Images. If false, upload product images into images directory manually by ftp.');
define('PRODUCT_IMAGE_POPUP_WIDTH_TITLE' , 'Width of Popup Images');
define('PRODUCT_IMAGE_POPUP_WIDTH_DESC' , 'Maximal Width of Popup Images in Pixel');
define('PRODUCT_IMAGE_POPUP_HEIGHT_TITLE' , 'Height of Popup Images');
define('PRODUCT_IMAGE_POPUP_HEIGHT_DESC' , 'Maximal Height of Popup Images in Pixel');

define('SMALL_IMAGE_WIDTH_TITLE' , 'Small Image Width');
define('SMALL_IMAGE_WIDTH_DESC' , 'The pixel width of small images');
define('SMALL_IMAGE_HEIGHT_TITLE' , 'Small Image Height');
define('SMALL_IMAGE_HEIGHT_DESC' , 'The pixel height of small images');

define('HEADING_IMAGE_WIDTH_TITLE' , 'Heading Image Width');
define('HEADING_IMAGE_WIDTH_DESC' , 'The pixel width of heading images');
define('HEADING_IMAGE_HEIGHT_TITLE' , 'Heading Image Height');
define('HEADING_IMAGE_HEIGHT_DESC' , 'The pixel height of heading images');

define('SUBCATEGORY_IMAGE_WIDTH_TITLE' , 'Subcategory Image Width');
define('SUBCATEGORY_IMAGE_WIDTH_DESC' , 'The pixel width of subcategory images');
define('SUBCATEGORY_IMAGE_HEIGHT_TITLE' , 'Subcategory Image Height');
define('SUBCATEGORY_IMAGE_HEIGHT_DESC' , 'The pixel height of subcategory images');

define('CONFIG_CALCULATE_IMAGE_SIZE_TITLE' , 'Calculate Image Size');
define('CONFIG_CALCULATE_IMAGE_SIZE_DESC' , 'Calculate the size of images?');

define('IMAGE_REQUIRED_TITLE' , 'Image Required');
define('IMAGE_REQUIRED_DESC' , 'Enable to display broken images. Good for development.');

//This is for the Images showing your products for preview. All the small stuff.

define('PRODUCT_IMAGE_THUMBNAIL_BEVEL_TITLE' , 'Products-Thumbnails: Bevel<br />');
define('PRODUCT_IMAGE_THUMBNAIL_BEVEL_DESC' , 'Products-Thumbnails: Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br /><br />Usage:<br />(edge width,hex light colour,hex dark colour)');
define('PRODUCT_IMAGE_THUMBNAIL_GREYSCALE_TITLE' , 'Products-Thumbnails: Greyscale<br />');
define('PRODUCT_IMAGE_THUMBNAIL_GREYSCALE_DESC' , 'Products-Thumbnails: Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br /><br />Usage:<br />(int red,int green,int blue)');
define('PRODUCT_IMAGE_THUMBNAIL_ELLIPSE_TITLE' , 'Products-Thumbnails: Ellipse<br />');
define('PRODUCT_IMAGE_THUMBNAIL_ELLIPSE_DESC' , 'Products-Thumbnails: Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br /><br />Usage:<br />(hex background colour)');
define('PRODUCT_IMAGE_THUMBNAIL_ROUND_EDGES_TITLE' , 'Products-Thumbnails: Round-edges<br />');
define('PRODUCT_IMAGE_THUMBNAIL_ROUND_EDGES_DESC' , 'Products-Thumbnails: Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br /><br />Usage:<br />(edge_radius,background colour,anti-alias width)');
define('PRODUCT_IMAGE_THUMBNAIL_MERGE_TITLE' , 'Products-Thumbnails: Merge<br />');
define('PRODUCT_IMAGE_THUMBNAIL_MERGE_DESC' , 'Products-Thumbnails: Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br /><br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity, transparent colour on merge image)');
define('PRODUCT_IMAGE_THUMBNAIL_FRAME_TITLE' , 'Products-Thumbnails: Frame<br />');
define('PRODUCT_IMAGE_THUMBNAIL_FRAME_DESC' , 'Products-Thumbnails: Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br /><br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('PRODUCT_IMAGE_THUMBNAIL_DROP_SHADDOW_TITLE' , 'Products-Thumbnails: Drop-Shadow<br />');
define('PRODUCT_IMAGE_THUMBNAIL_DROP_SHADDOW_DESC' , 'Products-Thumbnails: Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br /><br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('PRODUCT_IMAGE_THUMBNAIL_MOTION_BLUR_TITLE' , 'Products-Thumbnails: Motion-Blur<br />');
define('PRODUCT_IMAGE_THUMBNAIL_MOTION_BLUR_DESC' , 'Products-Thumbnails: Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br /><br />Usage:<br />(int number of lines,hex background colour)');

//And this is for the Images showing your products in single-view

define('PRODUCT_IMAGE_INFO_BEVEL_TITLE' , 'Product-Images: Bevel');
define('PRODUCT_IMAGE_INFO_BEVEL_DESC' , 'Product-Images: Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br /><br />Usage:<br />(edge width, hex light colour, hex dark colour)');
define('PRODUCT_IMAGE_INFO_GREYSCALE_TITLE' , 'Product-Images: Greyscale');
define('PRODUCT_IMAGE_INFO_GREYSCALE_DESC' , 'Product-Images: Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br /><br />Usage:<br />(int red, int green, int blue)');
define('PRODUCT_IMAGE_INFO_ELLIPSE_TITLE' , 'Product-Images: Ellipse');
define('PRODUCT_IMAGE_INFO_ELLIPSE_DESC' , 'Product-Images: Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br /><br />Usage:<br />(hex background colour)');
define('PRODUCT_IMAGE_INFO_ROUND_EDGES_TITLE' , 'Product-Images: Round-edges');
define('PRODUCT_IMAGE_INFO_ROUND_EDGES_DESC' , 'Product-Images: Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br /><br />Usage:<br />( edge_radius, background colour, anti-alias width)');
define('PRODUCT_IMAGE_INFO_MERGE_TITLE' , 'Product-Images: Merge');
define('PRODUCT_IMAGE_INFO_MERGE_DESC' , 'Product-Images: Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br /><br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity,transparent colour on merge image)');
define('PRODUCT_IMAGE_INFO_FRAME_TITLE' , 'Product-Images: Frame');
define('PRODUCT_IMAGE_INFO_FRAME_DESC' , 'Product-Images: Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br /><br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('PRODUCT_IMAGE_INFO_DROP_SHADDOW_TITLE' , 'Product-Images: Drop-Shadow');
define('PRODUCT_IMAGE_INFO_DROP_SHADDOW_DESC' , 'Product-Images: Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br /><br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('PRODUCT_IMAGE_INFO_MOTION_BLUR_TITLE' , 'Product-Images: Motion-Blur');
define('PRODUCT_IMAGE_INFO_MOTION_BLUR_DESC' , 'Product-Images: Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br /><br />Usage:<br />(int number of lines,hex background colour)');

//so this image is the biggest in the shop this

define('PRODUCT_IMAGE_POPUP_BEVEL_TITLE' , 'Product-Popup-Images: Bevel');
define('PRODUCT_IMAGE_POPUP_BEVEL_DESC' , 'Product-Popup-Images: Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br /><br />Usage:<br />(edge width,hex light colour,hex dark colour)');
define('PRODUCT_IMAGE_POPUP_GREYSCALE_TITLE' , 'Product-Popup-Images: Greyscale');
define('PRODUCT_IMAGE_POPUP_GREYSCALE_DESC' , 'Product-Popup-Images: Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br /><br />Usage:<br />(int red,int green,int blue)');
define('PRODUCT_IMAGE_POPUP_ELLIPSE_TITLE' , 'Product-Popup-Images: Ellipse');
define('PRODUCT_IMAGE_POPUP_ELLIPSE_DESC' , 'Product-Popup-Images: Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br /><br />Usage:<br />(hex background colour)');
define('PRODUCT_IMAGE_POPUP_ROUND_EDGES_TITLE' , 'Product-Popup-Images: Round-edges');
define('PRODUCT_IMAGE_POPUP_ROUND_EDGES_DESC' , 'Product-Popup-Images: Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br /><br />Usage:<br />(edge_radius,background colour,anti-alias width)');
define('PRODUCT_IMAGE_POPUP_MERGE_TITLE' , 'Product-Popup-Images: Merge');
define('PRODUCT_IMAGE_POPUP_MERGE_DESC' , 'Product-Popup-Images: Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br /><br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity,transparent colour on merge image)');
define('PRODUCT_IMAGE_POPUP_FRAME_TITLE' , 'Product-Popup-Images: Frame');
define('PRODUCT_IMAGE_POPUP_FRAME_DESC' , 'Product-Popup-Images: Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br /><br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('PRODUCT_IMAGE_POPUP_DROP_SHADDOW_TITLE' , 'Product-Popup-Images: Drop-Shadow');
define('PRODUCT_IMAGE_POPUP_DROP_SHADDOW_DESC' , 'Product-Popup-Images: Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br /><br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('PRODUCT_IMAGE_POPUP_MOTION_BLUR_TITLE' , 'Product-Popup-Images: Motion-Blur');
define('PRODUCT_IMAGE_POPUP_MOTION_BLUR_DESC' , 'Product-Popup-Images: Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br /><br />Usage:<br />(number of lines,hex background colour)');

define('MO_PICS_TITLE','Number of products images');
define('MO_PICS_DESC','if this number is set > 0 , you will be able to upload/display more images per product');

define('CATEGORIES_IMAGE_THUMBNAIL_ACTIVE_TITLE' , 'Allow to use GD library for Category Images');
define('CATEGORIES_IMAGE_THUMBNAIL_ACTIVE_DESC' , 'Allow to use GD library for Category Images. If false, upload product images into images directory manually by ftp.');
define('CATEGORIES_IMAGE_THUMBNAIL_WIDTH_TITLE' , 'Width of Category Images');
define('CATEGORIES_IMAGE_THUMBNAIL_WIDTH_DESC' , 'Maximal Width of Category Images in Pixel');
define('CATEGORIES_IMAGE_THUMBNAIL_HEIGHT_TITLE' , 'Height of Category Images');
define('CATEGORIES_IMAGE_THUMBNAIL_HEIGHT_DESC' , 'Maximal Height of Category Images in Pixel');

define('CATEGORIES_IMAGE_THUMBNAIL_BEVEL_TITLE' , 'Category Images: Bevel<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_BEVEL_DESC' , 'Category Images: Bevel<br /><br />Default-values: (8,FFCCCC,330000)<br /><br />shaded bevelled edges<br /><br />Usage:<br />(edge width,hex light colour,hex dark colour)');
define('CATEGORIES_IMAGE_THUMBNAIL_GREYSCALE_TITLE' , 'Category Images: Greyscale<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_GREYSCALE_DESC' , 'Category Images: Greyscale<br /><br />Default-values: (32,22,22)<br /><br />basic black n white<br /><br />Usage:<br />(int red,int green,int blue)');
define('CATEGORIES_IMAGE_THUMBNAIL_ELLIPSE_TITLE' , 'Category Images: Ellipse<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_ELLIPSE_DESC' , 'Category Images: Ellipse<br /><br />Default-values: (FFFFFF)<br /><br />ellipse on bg colour<br /><br />Usage:<br />(hex background colour)');
define('CATEGORIES_IMAGE_THUMBNAIL_ROUND_EDGES_TITLE' , 'Category Images: Round-edges<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_ROUND_EDGES_DESC' , 'Category Images: Round-edges<br /><br />Default-values: (5,FFFFFF,3)<br /><br />corner trimming<br /><br />Usage:<br />(edge_radius,background colour,anti-alias width)');
define('CATEGORIES_IMAGE_THUMBNAIL_MERGE_TITLE' , 'Category Images: Merge<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_MERGE_DESC' , 'Category Images: Merge<br /><br />Default-values: (overlay.gif,10,-50,60,FF0000)<br /><br />overlay merge image<br /><br />Usage:<br />(merge image,x start [neg = from right],y start [neg = from base],opacity, transparent colour on merge image)');
define('CATEGORIES_IMAGE_THUMBNAIL_FRAME_TITLE' , 'Category Images: Frame<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_FRAME_DESC' , 'Category Images: Frame<br /><br />Default-values: (FFFFFF,000000,3,EEEEEE)<br /><br />plain raised border<br /><br />Usage:<br />(hex light colour,hex dark colour,int width of mid bit,hex frame colour [optional - defaults to half way between light and dark edges])');
define('CATEGORIES_IMAGE_THUMBNAIL_DROP_SHADDOW_TITLE' , 'Category Images: Drop-Shadow<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_DROP_SHADDOW_DESC' , 'Category Images: Drop-Shadow<br /><br />Default-values: (3,333333,FFFFFF)<br /><br />more like a dodgy motion blur [semi buggy]<br /><br />Usage:<br />(shadow width,hex shadow colour,hex background colour)');
define('CATEGORIES_IMAGE_THUMBNAIL_MOTION_BLUR_TITLE' , 'Category Images: Motion-Blur<br />');
define('CATEGORIES_IMAGE_THUMBNAIL_MOTION_BLUR_DESC' , 'Category Images: Motion-Blur<br /><br />Default-values: (4,FFFFFF)<br /><br />fading parallel lines<br /><br />Usage:<br />(int number of lines,hex background colour)');

define('IMAGE_MANIPULATOR_TITLE','GDlib processing');
define('IMAGE_MANIPULATOR_DESC','Image Manipulator for GD2 or GD1');

define('ACCOUNT_SECOND_NAME_TITLE' , 'Отчество');
define('ACCOUNT_SECOND_NAME_DESC', 'Показывать поле Отчество при регистрации покупателя в магазине и в адресной книге');
define('ACCOUNT_LAST_NAME_TITLE' , 'Фамилия');
define('ACCOUNT_LAST_NAME_DESC', 'Показывать поле Фамилия при регистрации покупателя в магазине и в адресной книге');

define('ACCOUNT_GENDER_TITLE' , 'Gender');
define('ACCOUNT_GENDER_DESC' , 'Display gender in the customers account');
define('ACCOUNT_DOB_TITLE' , 'Date of Birth');
define('ACCOUNT_DOB_DESC' , 'Display date of birth in the customers account');
define('ACCOUNT_COMPANY_TITLE' , 'Company');
define('ACCOUNT_COMPANY_DESC' , 'Display company in the customers account');
define('ACCOUNT_STREET_ADDRESS_TITLE' , 'Street address');
define('ACCOUNT_STREET_ADDRESS_DESC', 'Display street address in the customers account');
define('ACCOUNT_CITY_TITLE' , 'City');
define('ACCOUNT_CITY_DESC', 'Display city in the customers account');
define('ACCOUNT_POSTCODE_TITLE' , 'Postcode/ZIP');
define('ACCOUNT_POSTCODE_DESC', 'Display postcode/ZIP in the customers account');
define('ACCOUNT_COUNTRY_TITLE' , 'Country');
define('ACCOUNT_COUNTRY_DESC', 'Display country in the customers account');
define('ACCOUNT_TELE_TITLE' , 'Telephone');
define('ACCOUNT_TELE_DESC', 'Display telephone in the customers account');
define('ACCOUNT_FAX_TITLE' , 'Fax');
define('ACCOUNT_FAX_DESC', 'Display fax in the customers account');
define('ACCOUNT_SUBURB_TITLE' , 'Suburb');
define('ACCOUNT_SUBURB_DESC' , 'Display suburb in the customers account');
define('ACCOUNT_STATE_TITLE' , 'State');
define('ACCOUNT_STATE_DESC' , 'Display state in the customers account');

define('DEFAULT_CURRENCY_TITLE' , 'Default Currency');
define('DEFAULT_CURRENCY_DESC' , 'Currency which is used as default');
define('DEFAULT_LANGUAGE_TITLE' , 'Default Language');
define('DEFAULT_LANGUAGE_DESC' , 'Language which is used as default');
define('DEFAULT_ORDERS_STATUS_ID_TITLE' , 'Default Order Status');
define('DEFAULT_ORDERS_STATUS_ID_DESC' , 'Default order status when a new order is placed.');

define('SHIPPING_ORIGIN_COUNTRY_TITLE' , 'Country of Origin');
define('SHIPPING_ORIGIN_COUNTRY_DESC' , 'Select the country of origin to be used in shipping quotes.');
define('SHIPPING_ORIGIN_ZIP_TITLE' , 'Postal Code');
define('SHIPPING_ORIGIN_ZIP_DESC' , 'Enter the Postal Code (ZIP) of the Store to be used in shipping quotes.');
define('SHIPPING_MAX_WEIGHT_TITLE' , 'Enter the Maximum Package Weight you will ship');
define('SHIPPING_MAX_WEIGHT_DESC' , 'Carriers have a max weight limit for a single package. This is a common one for all.');
define('SHIPPING_BOX_WEIGHT_TITLE' , 'Package Tare weight.');
define('SHIPPING_BOX_WEIGHT_DESC' , 'What is the weight of typical packaging of small to medium packages?');
define('SHIPPING_BOX_PADDING_TITLE' , 'Larger packages - percentage increase.');
define('SHIPPING_BOX_PADDING_DESC' , 'For 10% enter 10');
define('SHOW_SHIPPING_DESC' , 'Show shippingcosts link in product infos');
define('SHOW_SHIPPING_TITLE' , 'Shippingcosts in product infos');
define('SHIPPING_INFOS_DESC' , 'Group ID of shippingcosts content.');
define('SHIPPING_INFOS_TITLE' , 'Group ID');

define('PRODUCT_LIST_FILTER_TITLE' , 'Display Category/Manufacturer Filter (0=disable; 1=enable)');
define('PRODUCT_LIST_FILTER_DESC' , 'Do you want to display the Category/Manufacturer Filter?');

define('STOCK_CHECK_TITLE' , 'Check stock level');
define('STOCK_CHECK_DESC' , 'Check to see if sufficent stock is available');

define('ATTRIBUTE_STOCK_CHECK_TITLE' , 'Check attribute-stock level');
define('ATTRIBUTE_STOCK_CHECK_DESC' , 'Check to see if sufficent attribute-stock is available');

define('STOCK_LIMITED_TITLE' , 'Subtract stock');
define('STOCK_LIMITED_DESC' , 'Subtract product in stock by product orders');
define('STOCK_ALLOW_CHECKOUT_TITLE' , 'Allow Checkout');
define('STOCK_ALLOW_CHECKOUT_DESC' , 'Allow customer to checkout even if there is insufficient stock');
define('STOCK_MARK_PRODUCT_OUT_OF_STOCK_TITLE' , 'Mark product out of stock');
define('STOCK_MARK_PRODUCT_OUT_OF_STOCK_DESC' , 'Display something on screen so customer can see which product has insufficient stock');
define('STOCK_REORDER_LEVEL_TITLE' , 'Stock Re-order level');
define('STOCK_REORDER_LEVEL_DESC' , 'Define when stock needs to be re-ordered');

define('STORE_PAGE_PARSE_TIME_TITLE' , 'Store Page Parse Time');
define('STORE_PAGE_PARSE_TIME_DESC' , 'Store the time it takes to parse a page');
define('STORE_PAGE_PARSE_TIME_LOG_TITLE' , 'Log Destination');
define('STORE_PAGE_PARSE_TIME_LOG_DESC' , 'Directory and filename of the page parse time log');
define('STORE_PARSE_DATE_TIME_FORMAT_TITLE' , 'Log Date Format');
define('STORE_PARSE_DATE_TIME_FORMAT_DESC' , 'The date format');

define('DISPLAY_PAGE_PARSE_TIME_TITLE' , 'Display The Page Parse Time');
define('DISPLAY_PAGE_PARSE_TIME_DESC' , 'Display the page parse time (store page parse time must be enabled)');

define('STORE_DB_TRANSACTIONS_TITLE' , 'Store Database Queries');
define('STORE_DB_TRANSACTIONS_DESC' , 'Store the database queries in the page parse time log (PHP4 only)');

define('USE_CACHE_TITLE' , 'Use Cache');
define('USE_CACHE_DESC' , 'Use caching features');

define('DIR_FS_CACHE_TITLE' , 'Cache Directory');
define('DIR_FS_CACHE_DESC' , 'The directory where the cached files are saved');

define('ACCOUNT_OPTIONS_TITLE','Account Options');
define('ACCOUNT_OPTIONS_DESC','How do you want to manage the login management of your store ?<br />You can choose between Customer Accounts and "One Time Orders" without creating a Customer Account (an account will be created but the customer won\'t be informed about that)');

define('EMAIL_TRANSPORT_TITLE' , 'eMail Transport Method');
define('EMAIL_TRANSPORT_DESC' , 'Defines if this server uses a local connection to sendmail or uses an SMTP connection via TCP/IP. Servers running on Windows and MacOS should change this setting to SMTP.');

define('EMAIL_LINEFEED_TITLE' , 'eMail Linefeeds');
define('EMAIL_LINEFEED_DESC' , 'Defines the character sequence used to separate mail headers.');
define('EMAIL_USE_HTML_TITLE' , 'Use MIME HTML When Sending eMails');
define('EMAIL_USE_HTML_DESC' , 'Send eMails in HTML format');
define('ENTRY_EMAIL_ADDRESS_CHECK_TITLE' , 'Verify eMail Addresses Through DNS');
define('ENTRY_EMAIL_ADDRESS_CHECK_DESC' , 'Verify eMail address through a DNS server');
define('SEND_EMAILS_TITLE' , 'Send eMails');
define('SEND_EMAILS_DESC' , 'Send out eMails');
define('SENDMAIL_PATH_TITLE' , 'The Path to sendmail');
define('SENDMAIL_PATH_DESC' , 'If you use sendmail, you should give us the right path (default: /usr/bin/sendmail):');
define('SMTP_MAIN_SERVER_TITLE' , 'Adress of the SMTP Server');
define('SMTP_MAIN_SERVER_DESC' , 'Please enter the adress of your main SMTP Server.');
define('SMTP_BACKUP_SERVER_TITLE' , 'Adress of the SMTP Backup Server');
define('SMTP_BACKUP_SERVER_DESC' , 'Please enter the adress of your Backup SMTP Server.');
define('SMTP_USERNAME_TITLE' , 'SMTP Username');
define('SMTP_USERNAME_DESC' , 'Please enter the username of your SMTP Account.');
define('SMTP_PASSWORD_TITLE' , 'SMTP Password');
define('SMTP_PASSWORD_DESC' , 'Please enter the password of your SMTP Account.');
define('SMTP_AUTH_TITLE' , 'SMTP AUTH');
define('SMTP_AUTH_DESC' , 'Does your SMTP Server needs secure authentication?');
define('SMTP_PORT_TITLE' , 'SMTP Port');
define('SMTP_PORT_DESC' , 'Please enter the SMTP port of your SMTP server(default: 25)?');

define('SMTP_SECURE_TITLE' , 'Enable ssl');
define('SMTP_SECURE_DESC' , '');

define('MAIL_PARAMS_TITLE' , 'Указывать адрес отправителя');
define('MAIL_PARAMS_DESC' , 'Если не отправляет почта способом mail, Вы можете попробовать отключить эту функцию.');

//Constants for contact_us
define('CONTACT_US_EMAIL_ADDRESS_TITLE' , 'Contact Us - eMail address');
define('CONTACT_US_EMAIL_ADDRESS_DESC' , 'Please enter an eMail Address used for normal "Contact Us" messages via shop to your office');
define('CONTACT_US_NAME_TITLE' , 'Contact Us - eMail address, name');
define('CONTACT_US_NAME_DESC' , 'Please Enter a name used for normal "Contact Us" messages sentded via shop to your office');
define('CONTACT_US_FORWARDING_STRING_TITLE' , 'Contact Us - forwaring addresses');
define('CONTACT_US_FORWARDING_STRING_DESC' , 'Please enter eMail addresses (seperated by , ) where "Contact Us" messages, sent via shop to your office, should be forwarded to.');
define('CONTACT_US_REPLY_ADDRESS_TITLE' , 'Contact Us - reply address');
define('CONTACT_US_REPLY_ADDRESS_DESC' , 'Please enter an eMail address where customers can reply to.');
define('CONTACT_US_REPLY_ADDRESS_NAME_TITLE' , 'Contact Us - reply address , name');
define('CONTACT_US_REPLY_ADDRESS_NAME_DESC' , 'Sender name for reply eMails.');
define('CONTACT_US_EMAIL_SUBJECT_TITLE' , 'Contact Us - eMail subject');
define('CONTACT_US_EMAIL_SUBJECT_DESC' , 'Please enter an eMail Subject for the contact-us messages via shop to your office.');

//Constants for support system
define('EMAIL_SUPPORT_ADDRESS_TITLE' , 'Technical Support - eMail adress');
define('EMAIL_SUPPORT_ADDRESS_DESC' , 'Please enter an eMail adress for sending eMails over the <b>Support System</b> (account creation, password changes).');
define('EMAIL_SUPPORT_NAME_TITLE' , 'Technical Support - eMail adress, name');
define('EMAIL_SUPPORT_NAME_DESC' , 'Please enter a name for sending eMails over the <b>Support System</b> (account creation, password changes).');
define('EMAIL_SUPPORT_FORWARDING_STRING_TITLE' , 'Technical Support - Forwarding adresses');
define('EMAIL_SUPPORT_FORWARDING_STRING_DESC' , 'Please enter forwarding adresses for the mails of the <b>Support System</b> (seperated by , )');
define('EMAIL_SUPPORT_REPLY_ADDRESS_TITLE' , 'Technical Support - reply adress');
define('EMAIL_SUPPORT_REPLY_ADDRESS_DESC' , 'Please enter an eMail adress for replies of your customers.');
define('EMAIL_SUPPORT_REPLY_ADDRESS_NAME_TITLE' , 'Technical Support - reply adress, name');
define('EMAIL_SUPPORT_REPLY_ADDRESS_NAME_DESC' , 'Please enter a sender name for the eMail adress for replies of your customers.');
define('EMAIL_SUPPORT_SUBJECT_TITLE' , 'Technical Support - eMail subject');
define('EMAIL_SUPPORT_SUBJECT_DESC' , 'Please enter an eMail subject for the <b>Support System</b> messages via shop to your office.');

//Constants for Billing system
define('EMAIL_BILLING_ADDRESS_TITLE' , 'Billing - eMail adress');
define('EMAIL_BILLING_ADDRESS_DESC' , 'Please enter an eMail adress for sending eMails over the <b>Billing system</b> (order confirmations, status changes,..).');
define('EMAIL_BILLING_NAME_TITLE' , 'Billing - eMail adress, name');
define('EMAIL_BILLING_NAME_DESC' , 'Please enter a name for sending eMails over the <b>Billing System</b> (order confirmations, status changes,..).');
define('EMAIL_BILLING_FORWARDING_STRING_TITLE' , 'Billing - Forwarding adresses');
define('EMAIL_BILLING_FORWARDING_STRING_DESC' , 'Please enter forwarding adresses for the mails of the <b>Billing System</b> (seperated by , )');
define('EMAIL_BILLING_REPLY_ADDRESS_TITLE' , 'Billing - reply adress');
define('EMAIL_BILLING_REPLY_ADDRESS_DESC' , 'Please enter an eMail adress for replies of your customers.');
define('EMAIL_BILLING_REPLY_ADDRESS_NAME_TITLE' , 'Billing - reply adress, name');
define('EMAIL_BILLING_REPLY_ADDRESS_NAME_DESC' , 'Please enter a name for the eMail adress for replies of your customers.');
define('EMAIL_BILLING_SUBJECT_TITLE' , 'Billing - eMail subject');
define('EMAIL_BILLING_SUBJECT_DESC' , 'Please enter an eMail Subject for the <b>Billing</b> messages via shop to your office.');
define('EMAIL_BILLING_SUBJECT_ORDER_TITLE','Billing - Ordermail subject');
define('EMAIL_BILLING_SUBJECT_ORDER_DESC','Please enter a subject for ordermails generated from xtc. (like <b>our order {$nr},{$date}</b>) ps: you can use, {$nr},{$date},{$firstname},{$lastname}');


define('DOWNLOAD_ENABLED_TITLE' , 'Enable download');
define('DOWNLOAD_ENABLED_DESC' , 'Enable the products download functions.');
define('DOWNLOAD_BY_REDIRECT_TITLE' , 'Download by redirect');
define('DOWNLOAD_BY_REDIRECT_DESC' , 'Use browser redirection for download. Disable on non-Unix systems.');
define('DOWNLOAD_MAX_DAYS_TITLE' , 'Expiry delay (days)');
define('DOWNLOAD_MAX_DAYS_DESC' , 'Set number of days before the download link expires. 0 means no limit.');
define('DOWNLOAD_MAX_COUNT_TITLE' , 'Maximum number of downloads');
define('DOWNLOAD_MAX_COUNT_DESC' , 'Set the maximum number of downloads. 0 means no download authorized.');

define('GZIP_COMPRESSION_TITLE' , 'Enable GZip Compression');
define('GZIP_COMPRESSION_DESC' , 'Enable HTTP GZip compression.');
define('GZIP_LEVEL_TITLE' , 'Compression Level');
define('GZIP_LEVEL_DESC' , 'Use a compression level from 0-9 (0 = minimum, 9 = maximum).');

define('SESSION_WRITE_DIRECTORY_TITLE' , 'Session Directory');
define('SESSION_WRITE_DIRECTORY_DESC' , 'If sessions are file based, store them in this directory.');
define('SESSION_FORCE_COOKIE_USE_TITLE' , 'Force Cookie Use');
define('SESSION_FORCE_COOKIE_USE_DESC' , 'Force the use of sessions when cookies are only enabled.');
define('SESSION_CHECK_SSL_SESSION_ID_TITLE' , 'Check SSL Session ID');
define('SESSION_CHECK_SSL_SESSION_ID_DESC' , 'Validate the SSL_SESSION_ID on every secure HTTPS page request.');
define('SESSION_CHECK_USER_AGENT_TITLE' , 'Check User Agent');
define('SESSION_CHECK_USER_AGENT_DESC' , 'Validate the clients browser user agent on every page request.');
define('SESSION_CHECK_IP_ADDRESS_TITLE' , 'Check IP Address');
define('SESSION_CHECK_IP_ADDRESS_DESC' , 'Validate the clients IP address on every page request.');
define('SESSION_RECREATE_TITLE' , 'Recreate Session');
define('SESSION_RECREATE_DESC' , 'Recreate the session to generate a new session ID when the customer logs on or creates an account (PHP >=4.1 needed).');

define('DISPLAY_CONDITIONS_ON_CHECKOUT_TITLE' , 'Display conditions check on checkout');
define('DISPLAY_CONDITIONS_ON_CHECKOUT_DESC' , 'Display and Signing the Conditions in the Order Process');

define('META_MIN_KEYWORD_LENGTH_TITLE' , 'Min. meta-keyword lenght');
define('META_MIN_KEYWORD_LENGTH_DESC' , 'min. length of a single keyword (generated from products description)');
define('META_KEYWORDS_NUMBER_TITLE' , 'Number of meta-keywords');
define('META_KEYWORDS_NUMBER_DESC' , 'number of keywords');
define('META_AUTHOR_TITLE' , 'author');
define('META_AUTHOR_DESC' , '<meta name="author">');
define('META_PUBLISHER_TITLE' , 'publisher');
define('META_PUBLISHER_DESC' , '<meta name="publisher">');
define('META_COMPANY_TITLE' , 'company');
define('META_COMPANY_DESC' , '<meta name="conpany">');
define('META_TOPIC_TITLE' , 'page-topic');
define('META_TOPIC_DESC' , '<meta name="page-topic">');
define('META_REPLY_TO_TITLE' , 'reply-to');
define('META_REPLY_TO_DESC' , '<meta name="reply-to">');
define('META_REVISIT_AFTER_TITLE' , 'revisit-after');
define('META_REVISIT_AFTER_DESC' , '<meta name="revisit-after">');
define('META_ROBOTS_TITLE' , 'robots');
define('META_ROBOTS_DESC' , '<meta name="robots">');
define('META_DESCRIPTION_TITLE' , 'Description');
define('META_DESCRIPTION_DESC' , '<meta name="description">');
define('META_KEYWORDS_TITLE' , 'Keywords');
define('META_KEYWORDS_DESC' , '<meta name="keywords">');

define('MODULE_PAYMENT_INSTALLED_TITLE' , 'Installed Payment Modules');
define('MODULE_PAYMENT_INSTALLED_DESC' , 'List of payment module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: cc.php;cod.php;paypal.php)');
define('MODULE_ORDER_TOTAL_INSTALLED_TITLE' , 'Installed OrderTotal-Modules');
define('MODULE_ORDER_TOTAL_INSTALLED_DESC' , 'List of order_total module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php)');
define('MODULE_SHIPPING_INSTALLED_TITLE' , 'Installed Shipping Modules');
define('MODULE_SHIPPING_INSTALLED_DESC' , 'List of shipping module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ups.php;flat.php;item.php)');

define('CACHE_LIFETIME_TITLE','Cache Lifetime');
define('CACHE_LIFETIME_DESC','This is the number of seconds cached content will persist');
define('CACHE_CHECK_TITLE','Check if cache modified');
define('CACHE_CHECK_DESC','If true, then If-Modified-Since headers are respected with cached content, and appropriate HTTP headers are sent. This way repeated hits to a cached page do not send the entire page to the client every time.');

define('DB_CACHE_TITLE','DB Cache');
define('DB_CACHE_DESC','Cache SELECT query results in files to gain more speed for slow databases.');

define('DB_CACHE_EXPIRE_TITLE','DB Cache lifetime');
define('DB_CACHE_EXPIRE_DESC','Time in seconds to rebuld cached resulst.');

define('PRODUCT_REVIEWS_VIEW_TITLE','Reviews in Productdetails');
define('PRODUCT_REVIEWS_VIEW_DESC','Number of displayed reviews in the productdetails page');

define('DELETE_GUEST_ACCOUNT_TITLE','Deleting Guest Accounts');
define('DELETE_GUEST_ACCOUNT_DESC','Shold guest accounts be deleted after placing orders ? (Order data will be saved)');

define('PRICE_IS_BRUTTO_TITLE','Gross Admin');
define('PRICE_IS_BRUTTO_DESC','Usage of prices with tax in Admin');

define('PRICE_PRECISION_TITLE','Gross/Net precision');
define('PRICE_PRECISION_DESC','Gross/Net precision');
define('CHECK_CLIENT_AGENT_TITLE','Prevent Spider Sessions');
define('CHECK_CLIENT_AGENT_DESC','Prevent known spiders from starting a session.');
define('SHOW_IP_LOG_TITLE','IP-Log in Checkout?');
define('SHOW_IP_LOG_DESC','Show Text "Your IP will be saved", in checkout?');

define('ACTIVATE_GIFT_SYSTEM_TITLE','Activate Gift Voucher System');
define('ACTIVATE_GIFT_SYSTEM_DESC','Activate Gift Voucher System');

define('ACTIVATE_SHIPPING_STATUS_TITLE','Display Shippingstatus');
define('ACTIVATE_SHIPPING_STATUS_DESC','Show shippingstatus? (Different dispatch times can be specified for individual products. After activation appear a new point <b>Delivery Status</b> at product input)');

define('IMAGE_QUALITY_TITLE','Image Quality');
define('IMAGE_QUALITY_DESC','Image quality (0= highest compression, 100=best quality)');

define('GROUP_CHECK_TITLE','Customerstatus Check');
define('GROUP_CHECK_DESC','Only allow specified customergroups access to individual categories,products and Contentelements (after activation, input fields in categories, products and in Contentmanager will appear');

define('ACTIVATE_REVERSE_CROSS_SELLING_TITLE','Reverse Cross-selling');
define('ACTIVATE_REVERSE_CROSS_SELLING_DESC','Activate reverse Cross-selling?');

define('ACTIVATE_NAVIGATOR_TITLE','activate productnavigator?');
define('ACTIVATE_NAVIGATOR_DESC','activate/deactivate productnavigator in product_info, (deaktivate for better performance with lots of articles in system)');

define('QUICKLINK_ACTIVATED_TITLE','activate multilink/copyfunction');
define('QUICKLINK_ACTIVATED_DESC','The multilink/copyfunction, changes the handling for the "copy product to" action, it allows to select multiple categories to copy/link a product with 1 click');

define('DOWNLOAD_UNALLOWED_PAYMENT_TITLE', 'Download Paymentmodules');
define('DOWNLOAD_UNALLOWED_PAYMENT_DESC', 'Not allowed Payment modules for downloads. List, seperated by comma, e.g. {banktransfer,cod,invoice,moneyorder}');
define('DOWNLOAD_MIN_ORDERS_STATUS_TITLE', 'Min. Orderstatus');
define('DOWNLOAD_MIN_ORDERS_STATUS_DESC', 'Min. orderstatus to allow download of files.');

// Vat Check
define('STORE_OWNER_VAT_ID_TITLE' , 'VAT ID of Shop Owner');
define('STORE_OWNER_VAT_ID_DESC' , 'The VAT ID of the Shop Owner');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_TITLE' , 'Customer-group - correct VAT ID (Foreign country)');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_DESC' , 'Customers-group for customers with correct VAT ID, Shop country != customers country');
define('ACCOUNT_COMPANY_VAT_CHECK_TITLE' , 'Validate VAT ID');
define('ACCOUNT_COMPANY_VAT_CHECK_DESC' , 'Validate VAT ID (check correct syntax)');
define('ACCOUNT_COMPANY_VAT_LIVE_CHECK_TITLE' , 'Validate VAT ID Live');
define('ACCOUNT_COMPANY_VAT_LIVE_CHECK_DESC' , 'Validate VAT ID live (if no syntax check available for country), live check will use validation gateway of germans "Bundesamt fпїЅr Finanzen"');
define('ACCOUNT_COMPANY_VAT_GROUP_TITLE' , 'automatic pruning ?');
define('ACCOUNT_COMPANY_VAT_GROUP_DESC' , 'Set to true, the customer-group will be changed automatically if a correct VAT ID is used.');
define('ACCOUNT_VAT_BLOCK_ERROR_TITLE' , 'Allow wrong UST ID?');
define('ACCOUNT_VAT_BLOCK_ERROR_DESC' , 'Set to true, only validated VAT IDs are acceptet.');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL_TITLE','Customer-group - correct VAT ID (Shop country)');
define('DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL_DESC','Customers-group for customers with correct VAT ID, Shop country = customers country');

// Search-Options
define('SEARCH_IN_DESC_TITLE','Search in Products Descriptions');
define('SEARCH_IN_DESC_DESC','Activate to enable search in Products Descriptions');
define('SEARCH_IN_ATTR_TITLE','Search in Products Attributes');
define('SEARCH_IN_ATTR_DESC','Activate to enable search in Products Attributes');

define('SEARCH_ENGINE_FRIENDLY_URLSX_TITLE' , 'Allow to use URL SEFLT');
define('SEARCH_ENGINE_FRIENDLY_URLSX_DESC' , 'Allow to use URL SEFLT');

// 

// Яндекс маркет

define('YML_NAME_TITLE' , 'Store name');
define('YML_COMPANY_TITLE' , 'Store owner');
define('YML_DELIVERYINCLUDED_TITLE' , 'Delivery included');
define('YML_AVAILABLE_TITLE' , 'Product availability');
define('YML_AUTH_USER_TITLE' , 'Login');
define('YML_AUTH_PW_TITLE' , 'Password');
define('YML_REFERER_TITLE' , 'Referer');
define('YML_STRIP_TAGS_TITLE' , 'Strip tags');
define('YML_UTF8_TITLE' , 'Encode to windows-1251');

define('YML_NAME_DESC' , 'Store name for Yandex-Market. STORE_NAME used if this field empty.');
define('YML_COMPANY_DESC' , 'Store owner for Yandex-Market. STORE_OWNER used if this field empty.');
define('YML_DELIVERYINCLUDED_DESC' , 'Delivery included?');
define('YML_AVAILABLE_DESC' , 'Product availability?');
define('YML_AUTH_USER_DESC' , 'Login for YML');
define('YML_AUTH_PW_DESC' , 'Password for YML');
define('YML_REFERER_DESC' , 'Add referer to product link (ip or user agent)?');
define('YML_STRIP_TAGS_DESC' , 'Strip html tags?');
define('YML_UTF8_DESC' , 'Encode to UTF-8?');

// Изменение цен

define('DISPLAY_MODEL_TITLE' , 'Display the model');
define('MODIFY_MODEL_TITLE' , 'Modify the model');
define('MODIFY_NAME_TITLE' , 'Modify the name of the products');
define('DISPLAY_STATUT_TITLE' , 'Modify the statut of the products');
define('DISPLAY_WEIGHT_TITLE' , 'Modify the weight of the products');
define('DISPLAY_QUANTITY_TITLE' , 'Modify the quantity of the products');
define('DISPLAY_IMAGE_TITLE' , 'Modify the image of the products');
define('DISPLAY_XML_TITLE' , 'Modify the yandex market status of the products');
define('DISPLAY_SORT_TITLE' , 'Modify the sort order of the products');
define('DISPLAY_MANUFACTURER_TITLE' , 'Display the manufacturer');
define('MODIFY_MANUFACTURER_TITLE' , 'Modify the manufacturer of the products');
define('DISPLAY_TAX_TITLE' , 'Display the tax');
define('MODIFY_TAX_TITLE' , 'Modify the class of tax of the products');
define('DISPLAY_TVA_OVER_TITLE' , 'Display price with all included of tax');
define('DISPLAY_TVA_UP_TITLE' , 'Display price with all included of tax');
define('DISPLAY_PREVIEW_TITLE' , 'Display the link towards the products information page');
define('DISPLAY_EDIT_TITLE' , 'Display the link towards the page where you will be able to edit the product');
define('ACTIVATE_COMMERCIAL_MARGIN_TITLE' , 'Activate or desactivate the commercial margin');

define('DISPLAY_MODEL_DESC', 'Enable/Disable the model displaying');
define('MODIFY_MODEL_DESC', 'Allow/Disallow the model modification');
define('MODIFY_NAME_DESC', 'Allow/Disallow the name modification?');
define('DISPLAY_STATUT_DESC', 'Allow/Disallow the Statut displaying and modification');
define('DISPLAY_WEIGHT_DESC', 'Allow/Disallow the Weight displaying and modification?');
define('DISPLAY_QUANTITY_DESC', 'Allow/Disallow the Quantity displaying and modification?');
define('DISPLAY_IMAGE_DESC', 'Allow/Disallow the Image displaying and modification?');
define('DISPLAY_XML_DESC', 'Allow/Disallow the yandex market displaying and modification?');
define('DISPLAY_SORT_DESC', 'Allow/Disallow the sort order displaying and modification?');
define('MODIFY_MANUFACTURER_DESC', 'Allow/Disallow the Manufacturer displaying and modification');
define('MODIFY_TAX_DESC', 'Allow/Disallow the Class of tax displaying and modification');
define('DISPLAY_TVA_OVER_DESC', 'Enable/Disable the displaying of the Price with all tax included when your mouse is over a product');
define('DISPLAY_TVA_UP_DESC', 'Enable/Disable the displaying of the Price with all tax included when you are typing the price?');
define('DISPLAY_PREVIEW_DESC', 'Enable/Disable the display of the link towards the products information page');
define('DISPLAY_EDIT_DESC', 'Enable/Disable the display of the link towards the page where you will be able to edit the product');
define('DISPLAY_MANUFACTURER_DESC', 'Do you want just display the manufacturer ?');
define('DISPLAY_TAX_DESC', 'Do you want just display the tax ?');
define('ACTIVATE_COMMERCIAL_MARGIN_DESC', 'Do you want taht the commercial margin be activate or not ?');

define('REVOCATION_ID_TITLE','Revocation ID');
define('REVOCATION_ID_DESC','Revocation content ID');
define('DISPLAY_REVOCATION_ON_CHECKOUT_TITLE','Display revocation on checkout confirmation page?');
define('DISPLAY_REVOCATION_ON_CHECKOUT_DESC','Display revocation on checkout confirmation page?');

define('MAX_DISPLAY_LATEST_NEWS_TITLE' , 'News box');
define('MAX_DISPLAY_LATEST_NEWS_DESC' , 'Maximum number of news in news box');
define('MAX_DISPLAY_LATEST_NEWS_PAGE_TITLE' , 'News per page');
define('MAX_DISPLAY_LATEST_NEWS_PAGE_DESC' , 'Maximum number of news per page');
define('MAX_DISPLAY_LATEST_NEWS_CONTENT_TITLE' , 'Short description');
define('MAX_DISPLAY_LATEST_NEWS_CONTENT_DESC' , 'Number of symbols displayed in news preview');
define('MAX_DISPLAY_CART_TITLE' , 'Length of products name in shopping cart box');
define('MAX_DISPLAY_CART_DESC' , 'Length of products name in shopping cart box');
define('MAX_DISPLAY_SHORT_DESCRIPTION_TITLE' , 'Length of short description');
define('MAX_DISPLAY_SHORT_DESCRIPTION_DESC' , 'Length of short description');

// Установка модулей

define('DIR_FS_CIP_TITLE' , 'Contribution Directory');
define('DIR_FS_CIP_DESC' , 'Location of contribution files');
define('ALLOW_SQL_BACKUP_TITLE' , 'Backup Database Before Install Each CIP');
define('ALLOW_SQL_BACKUP_DESC' , 'Choose TRUE and database will be backuped before each CIP install.<br />Do backup if database isn\'t huge or for debugging.');
define('ALLOW_SQL_RESTORE_TITLE' , 'Restore Database When Remove Each CIP');
define('ALLOW_SQL_RESTORE_DESC' , 'Choose TRUE and files will be restored from backup.<br />Backup doesn\'t contain changes made after CIP installation.<br />Use restoring only when build a new store or debug.');
define('ALLOW_FILES_BACKUP_TITLE' , 'Backup Files Before Install Each CIP');
define('ALLOW_FILES_BACKUP_DESC' , 'Choose TRUE and files will be backuped.<br>Backup contain only files which CIP will modify.<br />We recommend to do a files backup.');
define('ALLOW_FILES_RESTORE_TITLE' , 'Restore Files When Remove Each CIP');
define('ALLOW_FILES_RESTORE_DESC' , 'Choose TRUE and files will be restored from backup.<br />Backup doesn\'t contain changes made after CIP installation.<br />Use restoring only when build a new store or debug.');
define('ALLOW_OVERWRITE_MODIFIED_TITLE' , 'Allow Overwrite Existing Modified Files');
define('ALLOW_OVERWRITE_MODIFIED_DESC' , 'Choose TRUE and ADDFILE will overwrite even files with changes.<br />All changes will be lost. Use only for testing and debugging.');
define('TEXT_LINK_FORUM_TITLE' , 'Forum Link');
define('TEXT_LINK_FORUM_DESC' , 'URL for support forum at osc-cms.com');
define('TEXT_LINK_CONTR_TITLE' , 'URL to the Contribution\'s page');
define('TEXT_LINK_CONTR_DESC' , 'URL for contrib\'s page at osc-cms.com');
define('ALWAYS_DISPLAY_REMOVE_BUTTON_TITLE' , 'Always Display Remove-Button');
define('ALWAYS_DISPLAY_REMOVE_BUTTON_DESC' , 'Choose TRUE and REMOVE button will be displayed for both installed and NOT installed CIPs.');
define('ALWAYS_DISPLAY_INSTALL_BUTTON_TITLE' , 'Always Display Install-Button');
define('ALWAYS_DISPLAY_INSTALL_BUTTON_DESC' , 'Choose TRUE and INSTALL button will be displayed for both installed and NOT installed CIPs.');
define('SHOW_PERMISSIONS_COLUMN_TITLE' , 'Show Permissions Column');
define('SHOW_PERMISSIONS_COLUMN_DESC' , 'Choose TRUE and permissions column will be shown.');
define('SHOW_USER_GROUP_COLUMN_TITLE' , 'Show User/Group Column');
define('SHOW_USER_GROUP_COLUMN_DESC' , 'Choose TRUE and User/Group column will be shown.');
define('SHOW_UPLOADER_COLUMN_TITLE' , 'Show Uploader Column');
define('SHOW_UPLOADER_COLUMN_DESC' , 'Choose TRUE and Uploader column will be shown.');
define('SHOW_UPLOADED_COLUMN_TITLE' , 'Show Date Uploaded Column');
define('SHOW_UPLOADED_COLUMN_DESC' , 'Choose TRUE and Date Uploaded column will be shown.');
define('SHOW_SIZE_COLUMN_TITLE' , 'Show Size Column');
define('SHOW_SIZE_COLUMN_DESC' , 'Choose TRUE and Size column will be shown.');
define('USE_LOG_SYSTEM_TITLE' , 'Use Log System');
define('USE_LOG_SYSTEM_DESC' , 'Choose TRUE and all actions will be logged into file in backups folder.');
define('MAX_UPLOADED_FILESIZE_TITLE' , 'Maximum filesize for uploaded CIP');
define('MAX_UPLOADED_FILESIZE_DESC' , 'Set maximum filesize in bytes for cip archives you can upload.');

define('USE_EP_IMAGE_MANIPULATOR_TITLE','Use image manipulator for easypopulate');
define('USE_EP_IMAGE_MANIPULATOR_DESC','Use image manipulator for product images when your data file imported to shop');

define('LOGIN_NUM_TITLE','Safe login - number of attempts');
define('LOGIN_NUM_DESC','Number of wrong login attempts before captcha displayed.');
define('LOGIN_TIME_TITLE','Safe login - time');
define('LOGIN_TIME_DESC','Time (in seconds).');

define('ENABLE_TABS_TITLE','Use tabs in admin');
define('ENABLE_TABS_DESC','Enable tabs in admin');

define('YML_VENDOR_TITLE','Generate vendor');
define('YML_VENDOR_DESC','Generate vendor tag?');
define('YML_REF_ID_TITLE','Link');
define('YML_REF_ID_DESC','Add parameter to the product link.');
define('YML_REF_IP_TITLE','IP Link');
define('YML_REF_IP_DESC','Add ip address to the product link.');

define('SESSION_TIMEOUT_ADMIN_TITLE','Admin session timeout');
define('SESSION_TIMEOUT_ADMIN_DESC','Set admin session timeout (seconds).');
define('SESSION_TIMEOUT_CATALOG_TITLE','Customer session timeout');
define('SESSION_TIMEOUT_CATALOG_DESC','Set customer session timeout (seconds).');

define('STAY_PAGE_EDIT_TITLE','Stay on edit page');
define('STAY_PAGE_EDIT_DESC','Stay on edit page after saving changes in category/product.');

define('MAX_THUMB_WIDTH_TITLE','Maximal option thumb width');
define('MAX_THUMB_WIDTH_DESC','Maximal option thumb width');

define('MAX_THUMB_HEIGHT_TITLE','Maximal option thumb height');
define('MAX_THUMB_HEIGHT_DESC','Maximal option thumb height');

define('MAX_ADMIN_WIDTH_TITLE','Maximal option admin image width');
define('MAX_ADMIN_WIDTH_DESC','Maximal option admin image width');

define('MAX_ADMIN_HEIGHT_TITLE','Maximal option admin image height');
define('MAX_ADMIN_HEIGHT_DESC','Maximal option admin image height');

define('MAX_BYTE_SIZE_TITLE','Maximal option byte size');
define('MAX_BYTE_SIZE_DESC','Maximal option byte size');

define('MASTER_PASS_TITLE','Master Password');
define('MASTER_PASS_DESC','This password will allow you to login to any customers account.');

define('DOWN_FOR_MAINTENANCE_TITLE','Down for Maintenance');
define('DOWN_FOR_MAINTENANCE_DESC','Down for Maintenance.');
define('EXCLUDE_ADMIN_IP_FOR_MAINTENANCE_TITLE','Down For Maintenance (exclude this IP-Address)');
define('EXCLUDE_ADMIN_IP_FOR_MAINTENANCE_DESC','This IP Address is able to access the website while it is Down For Maintenance (like webmaster)');

define('MAX_DISPLAY_FAQ_TITLE' , 'Faq box');
define('MAX_DISPLAY_FAQ_DESC' , 'Maximum number of faqs in faq box');
define('MAX_DISPLAY_FAQ_PAGE_TITLE' , 'Faqs per page');
define('MAX_DISPLAY_FAQ_PAGE_DESC' , 'Maximum number of faqs per page');
define('MAX_DISPLAY_FAQ_ANSWER_TITLE' , 'Short answer');
define('MAX_DISPLAY_FAQ_ANSWER_DESC' , 'Number of symbols displayed in faq preview');

//Мета теги

define('CHECK_META_ROBOTS_TITLE','Показывать мета тег <i>Robots</i>');
define('CHECK_META_ROBOTS_DESC','Содержит указания для роботов поисковых машин');

define('CHECK_META_COMPANY_TITLE','Показывать мета тег <i>Company</i>');
define('CHECK_META_COMPANY_DESC','');

define('CHECK_META_AUTHOR_TITLE','Показывать мета тег <i>Author</i>');
define('CHECK_META_AUTHOR_DESC','');

define('CHECK_META_PUBLISHER_TITLE','Показывать мета тег <i>Publisher</i>');
define('CHECK_META_PUBLISHER_DESC','');

define('CHECK_META_DISTRIB_TITLE','Показывать мета тег <i>Distribution</i>');
define('CHECK_META_DISTRIB_DESC','');

define('CHECK_META_REPLY_TO_TITLE','Показывать мета тег <i>Reply to</i>');
define('CHECK_META_REPLY_TO_DESC','');

define('CHECK_META_REVISIT_AFTER_TITLE','Показывать мета тег <i>Revisit after</i>');
define('CHECK_META_REVISIT_AFTER_DESC','');

define('CHECK_META_REVISIT_DESCRIPTION_TITLE','Показывать мета тег <i>Description</i>');
define('CHECK_META_REVISIT_DESCRIPTION_DESC','Служит для краткого описания странички.');

define('CHECK_META_REVISIT_KEYWORDS_TITLE','Показывать мета тег <i>Keywords</i>');
define('CHECK_META_REVISIT_KEYWORDS_DESC','&lt;meta name="robots">');

require_once(DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin']. '/affiliate_configuration.php');

define('CG_MY_SHOP_TITLE', 'Мой магазин');
define('CG_MINIMAL_VALUES_TITLE', 'Минимальные');
define('CG_MAXIMAL_VALUES_TITLE', 'Максимальные');
define('CG_PICTURES_PARAMETERS_TITLE', 'Картинки');
define('CG_CUSTOMERS_TITLE', 'Данные покупателя');
define('CG_MODULES_TITLE', 'Модули');
define('CG_SHIPPING_TITLE', 'Доставка/Упаковка');
define('CG_PRODUCTS_TITLE', 'Вывод товара');
define('CG_WAREHOUSE_TITLE', 'Склад');
define('CG_LOGGING_TITLE', 'Логи');
define('CG_CACHE_TITLE', 'Кэш');
define('CG_EMAIL_TITLE', 'Настройка E-Mail');
define('CG_DOWNLOAD_TITLE', 'Скачивание');
define('CG_MY_GZIP_TITLE', 'GZip компрессия');
define('CG_MY_SESSIONS_TITLE', 'Сессии');
define('CG_META_TAGS_TITLE', 'Мета теги');
define('CG_VAT_ID_TITLE', 'Vat');
define('CG_GOOGLE_TITLE', 'Google Analytics');
define('CG_IMPORT_EXPORT_TITLE', 'Импорт/Экспорт');
define('CG_SEARCH_TITLE', 'Настройки поиска');
define('CG_YANDEX_MARKET_TITLE', 'Яндекс-Маркет');
define('CG_QUICK_PRICE_UPDATES_TITLE', 'Изменение цен');
define('CG_CIP_MANAGER_TITLE', 'Установка модулей');
define('CG_MAINTENANCE_TITLE', 'Тех. обслуживание');
define('CG_AFFILIATE_PROGRAM_TITLE', 'Партнёрская программа');
define('_TITLE', 'Разное');

define('STORE_TELEPHONE_TITLE','Телефон');
define('STORE_TELEPHONE_DESC','Телефон магазина.');
define('STORE_ICQ_TITLE','ICQ');
define('STORE_ICQ_DESC','ICQ магазина.');
define('STORE_SKYPE_TITLE','Skype');
define('STORE_SKYPE_DESC','Skype магазина.');

define('QUICK_CHECKOUT_TITLE','Быстрое оформление заказа');
define('QUICK_CHECKOUT_DESC','Разрешить модуль быстрого оформления заказа.');


define('VIS_BOX_WHATSNEW_TITLE','Новинки');
define('VIS_BOX_WHATSNEW_DESC','Выводит новинки интернет-магазина (BOX_WHATSNEW).');

define('VIS_BOX_SPECIALS_TITLE','Скидки');
define('VIS_BOX_SPECIALS_DESC','Выводит товары с установленной скидкой (BOX_SPECIALS).');

define('VIS_BOX_SEARCH_TITLE','Поиск');
define('VIS_BOX_SEARCH_DESC','Показывать блок поиска (BOX_SEARCH).');

define('VIS_BOX_REVIEWS_TITLE','Отзывы');
define('VIS_BOX_REVIEWS_DESC','Выводит случайный отзыв на товар (BOX_REVIEWS).');

define('VIS_BOX_ORDER_HISTORY_TITLE','История заказов покупателя');
define('VIS_BOX_ORDER_HISTORY_DESC','Выводит список всех заказов, оформленных покупателем (BOX_ORDER_HISTORY).');


define('VIS_BOX_NEWSLETTER_TITLE','Рассылка');
define('VIS_BOX_NEWSLETTER_DESC','Показывать блок рассылки (BOX_NEWSLETTER).');

define('VIS_BOX_MANUFACTURERS_INFO_TITLE','Информация о производителе');
define('VIS_BOX_MANUFACTURERS_INFO_DESC','Выводит информацию о производителе текущего товара (BOX_MANUFACTURERS_INFO).');

define('VIS_BOX_MANUFACTURERS_TITLE','Производители');
define('VIS_BOX_MANUFACTURERS_DESC','Выводится список производителей магазина (BOX_MANUFACTURERS)');

define('VIS_BOX_LOGIN_TITLE','Вход в админку');
define('VIS_BOX_LOGIN_DESC','Показывать блок входа в амдинку (BOX_LOGIN).');


define('VIS_BOX_LATEST_NEWS_TITLE','Последние новости');
define('VIS_BOX_LATEST_NEWS_DESC','Показывает последние новости магазина BOX_LATEST_NEWS');


define('VIS_BOX_LAST_VIEWED_TITLE','Просмотренные товары');
define('VIS_BOX_LAST_VIEWED_DESC','Показывать блок просмотренных товаров (BOX_LAST_VIEWED).');

define('VIS_BOX_LANGUAGES_TITLE','Языки');
define('VIS_BOX_LANGUAGES_DESC','Показывать блок выбора языка магазина (BOX_LANGUAGES).');

define('VIS_BOX_INFORMATION_TITLE','Информационные страницы (information)');
define('VIS_BOX_INFORMATION_DESC','Показывать блок с информационными страницами information (BOX_INFORMATION).');

define('VIS_BOX_INFOBOX_TITLE','Информация о группе');
define('VIS_BOX_INFOBOX_DESC','Показывать блок информации о группе (BOX_INFOBOX).');

define('VIS_BOX_FEATURED_TITLE','Рекомендуемые товары');
define('VIS_BOX_FEATURED_DESC','Показывать блок рекомендуемые товары (BOX_FEATURE).');

define('VIS_BOX_FAQ_TITLE','Вопросы / Ответы');
define('VIS_BOX_FAQ_DESC','Показывать блок вопросы-ответы (BOX_FAQ).');

define('VIS_BOX_CURRENCIES_TITLE','Валюты');
define('VIS_BOX_CURRENCIES_DESC','Показывать блок валют (BOX_CURRENCIES).');

define('VIS_BOX_CONTENT_TITLE','Информационные страницы (content)');
define('VIS_BOX_CONTENT_DESC','Показывать блок с информационными страницами content (BOX_CONTENT).');

define('VIS_BOX_CATEGORIES_TITLE','Список категорий');
define('VIS_BOX_CATEGORIES_DESC','Показывать список категорий магазина (BOX_CATEGORIES).');

define('VIS_BOX_CART_TITLE','Корзина');
define('VIS_BOX_CART_DESC','Показывать корзину (BOX_CART).');

define('VIS_BOX_BEST_SELLERS_TITLE','Категории товаров');
define('VIS_BOX_BEST_SELLERS_DESC','Выводит список 10 самых продаваемых товаров магазина (BOX_BEST_SELLERS).');

define('VIS_BOX_AUTHORS_TITLE','Категории товаров');
define('VIS_BOX_AUTHORS_DESC','Выводит авторов статей в виде drop-down меню  (BOX_AUTHORS).');

define('VIS_BOX_ARTICLES_NEW_TITLE','Новые статьи');
define('VIS_BOX_ARTICLES_NEW_DESC','Выводит список новых статей (BOX_ARTICLES_NEW).');

define('VIS_BOX_ARTICLES_TITLE','Статьи');
define('VIS_BOX_ARTICLES_DESC','Выводит доступные категории статей (BOX_ARTICLES).');

define('VIS_BOX_AFFILIATE_TITLE','Статьи');
define('VIS_BOX_AFFILIATE_DESC','Показывать блок статей (BOX_AFFILIATE).');

define('VIS_BOX_ADMIN_TITLE','Бокс администратора');
define('VIS_BOX_ADMIN_DESC','Показывать блок администратора (BOX_ADMIN).');

define('VIS_BOX_ADD_A_QUICKIE_TITLE','Быстрый заказ');
define('VIS_BOX_ADD_A_QUICKIE_DESC','Показывать блок быстрого заказа (BOX_ADD_A_QUICKIE).');

define('VIS_BOX_DOWNLOADS_TITLE','Мои загрузки');
define('VIS_BOX_DOWNLOADS_DESC','Выводит ссылку на загрузку заказанных виртуальных товаров (BOX_DOWNLOAD).');

define('SEO_URL_PRODUCT_GENERATOR_TITLE' , 'Автогенератор ЧПУ URL для товаров');
define('SEO_URL_PRODUCT_GENERATOR_DESC' , 'Автоматическое создание ЧПУ URL ссылки при редактировании/создании товара.');

define('SEO_URL_CATEGORIES_GENERATOR_TITLE' , 'Автогенератор ЧПУ URL для категорий');
define('SEO_URL_CATEGORIES_GENERATOR_DESC' , 'Автоматическое создание ЧПУ URL ссылки при редактировании/создании категорий.');

define('SEO_URL_PRODUCT_GENERATOR_IMPORT_TITLE' , 'Автогенератор ЧПУ URL при импорте товаров');
define('SEO_URL_PRODUCT_GENERATOR_IMPORT_DESC' , '');

define('SEO_URL_NEWS_GENERATOR_TITLE' , 'Автогенератор ЧПУ URL для новостей');
define('SEO_URL_NEWS_GENERATOR_DESC' , 'Автоматическое создание ЧПУ URL ссылки при редактировании/создании новостей.');

define('SEO_URL_ARTICLES_GENERATOR_TITLE' , 'Автогенератор ЧПУ URL для статей');
define('SEO_URL_ARTICLES_GENERATOR_DESC' , 'Автоматическое создание ЧПУ URL ссылки при редактировании/создании статей.');

define('BUTTON_DEFAULT', 'Default');

define('VIS_BOX_NEWS_TITLE', 'Новости');
define('VIS_BOX_NEWS_DESC','Показывать блок новостей (BOX_LATESTNEWS).');

define('SET_WHOS_ONLINE_TITLE', 'Кто сейчас в магазине');
define('SET_WHOS_ONLINE_DESC', 'Включить функцию кто сейчас в магазине');

define('DISPLAY_DB_QUERY_TITLE', 'Показывать выполняемые sql запросы');
define('DISPLAY_DB_QUERY_DESC', '');

define('VIS_BANNER_TITLE','Banners');
define('VIS_BANNER_DESC','');

define('DB_CACHE_PRO_TITLE','Cache Pro');
define('DB_CACHE_PRO_DESC','');

define('TEMPLATE_COMPILE_CHECK_TITLE','Проверка перекомпиляции шаблона');
define('TEMPLATE_COMPILE_CHECK_DESC','Проверка изменения шаблонов необходима только на стадии содания шаблона. На рабочих сайтах, для повышения быстродействия, проверку необходимо отключать.');

require_once(DIR_FS_ADMIN .'lang/'. $_SESSION['language_admin']. '/affiliate_configuration.php');
?>