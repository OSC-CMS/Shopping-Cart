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

define('TEXT_EDIT_STATUS', 'Status');
define('HEADING_TITLE', 'Categories / Products');
define('HEADING_TITLE_SEARCH', 'Search: ');
define('HEADING_TITLE_GOTO', 'Go To:');

define('TABLE_HEADING_ID', 'ID');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', 'Categories / Products');
define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_STARTPAGE', 'TOP');
define('TABLE_HEADING_STOCK','Stock Warning');
define('TABLE_HEADING_MENU','Menu');
define('TABLE_HEADING_SORT','Sort');
define('TABLE_HEADING_EDIT','');

define('TEXT_ACTIVE_ELEMENT','Active Element');
define('TEXT_INFORMATIONS','Informations');
define('TEXT_MARKED_ELEMENTS','Marked Elements');
define('TEXT_INSERT_ELEMENT','New Element');

define('TEXT_WARN_MAIN','0');
define('TEXT_NEW_PRODUCT', 'New Product in &quot;%s&quot;');
define('TEXT_CATEGORIES', 'Categories:');
define('TEXT_PRODUCTS', 'Products:');
define('TEXT_PRODUCTS_PRICE_INFO', 'Price:');
define('TEXT_PRODUCTS_TAX_CLASS', 'Tax Class:');
define('TEXT_PRODUCTS_AVERAGE_RATING', 'Average Rating:');
define('TEXT_PRODUCTS_QUANTITY_INFO', 'Quantity:');
define('TEXT_PRODUCTS_DISCOUNT_ALLOWED_INFO', 'Max. allowed Discount:');
define('TEXT_DATE_ADDED', 'Date Added:');
define('TEXT_DATE_AVAILABLE', 'Date Available:');
define('TEXT_LAST_MODIFIED', 'Last Modified:');
define('TEXT_IMAGE_NONEXISTENT', 'IMAGE DOES NOT EXIST');
define('TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS', 'Please insert a new category or product in <br />&nbsp;<br /><b>%s</b>');
define('TEXT_PRODUCT_MORE_INFORMATION', 'For more information, please visit this products <a href="http://%s" target="blank"><u>webpage</u></a>.');
define('TEXT_PRODUCT_DATE_ADDED', 'This product was added to our catalog on %s.');
define('TEXT_PRODUCT_DATE_AVAILABLE', 'This product will be in stock on %s.');
define('TEXT_CHOOSE_INFO_TEMPLATE', 'Product-Info Template:');
define('TEXT_CHOOSE_OPTIONS_TEMPLATE', 'Product-Optionen Template:');
define('TEXT_SELECT', 'Please select:');

define('TEXT_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_EDIT_CATEGORIES_ID', 'Category ID:');
define('TEXT_EDIT_CATEGORIES_NAME', 'Category Name:');
define('TEXT_EDIT_CATEGORIES_HEADING_TITLE', 'Category Heading:');
define('TEXT_EDIT_CATEGORIES_DESCRIPTION', 'Category Description:');
define('TEXT_EDIT_CATEGORIES_IMAGE', 'Category Image:');

define('TEXT_EDIT_SORT_ORDER', 'Sort Order:');

define('TEXT_INFO_COPY_TO_INTRO', 'Please choose a new category you wish to copy this product to');
define('TEXT_INFO_CURRENT_CATEGORIES', 'Current Categories:');

define('TEXT_INFO_HEADING_NEW_CATEGORY', 'New Category');
define('TEXT_INFO_HEADING_EDIT_CATEGORY', 'Edit Category');
define('TEXT_INFO_HEADING_DELETE_CATEGORY', 'Delete Category');
define('TEXT_INFO_HEADING_MOVE_CATEGORY', 'Move Category');
define('TEXT_INFO_HEADING_DELETE_PRODUCT', 'Delete Product');
define('TEXT_INFO_HEADING_MOVE_PRODUCT', 'Move Product');
define('TEXT_INFO_HEADING_COPY_TO', 'Copy To');
define('TEXT_INFO_HEADING_MOVE_ELEMENTS', 'Move Elements');
define('TEXT_INFO_HEADING_DELETE_ELEMENTS', 'Delete Elements');

define('TEXT_DELETE_CATEGORY_INTRO', 'Are you sure you want to delete this category?');
define('TEXT_DELETE_PRODUCT_INTRO', 'Are you sure you want to permanently delete this product?');

define('TEXT_DELETE_WARNING_CHILDS', '<b>WARNING:</b> There are %s (Child-)Categories still linked to this category!');
define('TEXT_DELETE_WARNING_PRODUCTS', '<b>WARNING:</b> There are %s products still linked to this category!');

define('TEXT_MOVE_WARNING_CHILDS', '<b>Info:</b> There are %s (Child-)Categories still linked to this category!');
define('TEXT_MOVE_WARNING_PRODUCTS', '<b>Info:</b> There are %s products still linked to this category!');

define('TEXT_MOVE_PRODUCTS_INTRO', 'Please select which category you wish <b>%s</b> to reside in');
define('TEXT_MOVE_CATEGORIES_INTRO', 'Please select which category you wish <b>%s</b> to reside in');
define('TEXT_MOVE', 'Move <b>%s</b> to:');
define('TEXT_MOVE_ALL', 'Move all to:');

define('TEXT_NEW_CATEGORY_INTRO', 'Please fill out the following information for the new category.');
define('TEXT_CATEGORIES_NAME', 'Category Name:');
define('TEXT_CATEGORIES_IMAGE', 'Category Image:');

define('TEXT_META_TITLE', 'Meta Title:');
define('TEXT_META_DESCRIPTION', 'Meta Description:');
define('TEXT_META_KEYWORDS', 'Meta Keywords:');

define('TEXT_SORT_ORDER', 'Sort Order:');

define('TEXT_PRODUCTS_STATUS', 'Products Status:');
define('TEXT_PRODUCTS_STARTPAGE', 'Show on startpage:');
define('TEXT_PRODUCTS_STARTPAGE_YES', 'Yes');
define('TEXT_PRODUCTS_STARTPAGE_NO', 'No');
define('TEXT_PRODUCTS_STARTPAGE_SORT', 'Sort order (startpage):');
define('TEXT_PRODUCTS_DATE_AVAILABLE', 'Date Available:');
define('TEXT_PRODUCT_AVAILABLE', 'In Stock');
define('TEXT_PRODUCT_NOT_AVAILABLE', 'Out of Stock');
define('TEXT_PRODUCTS_MANUFACTURER', 'Products Manufacturer:');
define('TEXT_PRODUCTS_NAME', 'Products Name:');
define('TEXT_PRODUCTS_DESCRIPTION', 'Products Description:');
define('TEXT_PRODUCTS_QUANTITY', 'Products Quantity:');
define('TEXT_PRODUCTS_MODEL', 'Products Model:');
define('TEXT_PRODUCTS_IMAGE', 'Products Image:');
define('TEXT_PRODUCTS_URL', 'Products URL:');
define('TEXT_PRODUCTS_URL_WITHOUT_HTTP', '<small>(without http://)</small>');
define('TEXT_PRODUCTS_PRICE', 'Products Price:');
define('TEXT_PRODUCTS_WEIGHT', 'Products Weight:');
define('TEXT_PRODUCTS_EAN','Barcode/EAN');
define('TEXT_PRODUCT_LINKED_TO','Linked to:');

define('TEXT_DELETE', 'Delete');

define('EMPTY_CATEGORY', 'Empty Category');

define('TEXT_HOW_TO_COPY', 'Copy Method:');
define('TEXT_COPY_AS_LINK', 'Link product');
define('TEXT_COPY_AS_DUPLICATE', 'Duplicate product');

define('ERROR_CANNOT_LINK_TO_SAME_CATEGORY', 'Error: Can not link products in the same directory.');
define('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Error: Catalog images directory is not writeable: ');
define('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Error: Catalog images directory does not exist: ');

define('TEXT_PRODUCTS_DISCOUNT_ALLOWED','Discount allowed:');
define('HEADING_PRICES_OPTIONS','<b>Price options</b>');
define('HEADING_PRODUCT_IMAGES','<b>Products Images</b>');
define('TEXT_PRODUCTS_WEIGHT_INFO','<small>(kg)</small>');
define('TEXT_PRODUCTS_SHORT_DESCRIPTION','Short description:');
define('TEXT_PRODUCTS_KEYWORDS', 'Extra words for Search:');
define('TXT_STK','Pcs: ');
define('TXT_PRICE','a :');
define('TXT_NETTO','Net price: ');
define('TEXT_NETTO','Net: ');
define('TXT_STAFFELPREIS','Graduated Price');

define('HEADING_PRODUCTS_MEDIA','<b>Products Media</b>');
define('TABLE_HEADING_PRICE','Price');

define('TEXT_CHOOSE_INFO_TEMPLATE','Productdetail Template');
define('TEXT_SELECT','--Select--');
define('TEXT_CHOOSE_OPTIONS_TEMPLATE','Productoptions Template');
define('SAVE_ENTRY','Save ?');

define('TEXT_FSK18','FSK 18:');
define('TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE','Template for Category Listing');
define('TEXT_CHOOSE_INFO_TEMPLATE_LISTING','Template for Product Listing');
define('TEXT_PRODUCTS_SORT','Sorting:');
define('TEXT_PRODUCTS_REVIEWS','Reviews:');
define('TEXT_EDIT_PRODUCT_SORT_ORDER','Product Sorting');
define('TXT_PRICES','Price');
define('TXT_NAME','Productname');
define('TXT_ORDERED','Products ordered');
define('TXT_SORT','Sorting');
define('TXT_WEIGHT','Weight');
define('TXT_DATE_ADD','Date add');
define('TXT_QTY','On Stock');

define('TEXT_MULTICOPY','Multiple');
define('TEXT_MULTICOPY_DESC','Copy elements into following categories (If one box selected, Single settings will be ignored.)');
define('TEXT_SINGLECOPY','Single');
define('TEXT_SINGLECOPY_DESC','Copy elements into following category');
define('TEXT_SINGLECOPY_CATEGORY','Category:');

define('TEXT_PRODUCTS_VPE','Unit');
define('TEXT_PRODUCTS_VPE_VISIBLE','Show unit price:');
define('TEXT_PRODUCTS_VPE_VALUE',' Value:');

define('CROSS_SELLING','Cross selling for article');
define('CROSS_SELLING_SEARCH','Search product:');
define('BUTTON_EDIT_CROSS_SELLING','Cross selling');
define('HEADING_DEL','delete');
define('HEADING_SORTING','sorting');
define('HEADING_MODEL','model');
define('HEADING_NAME','article');
define('HEADING_CATEGORY','category');
define('HEADING_ADD','Add?');
define('HEADING_GROUP','Group');

define('IMAGE_ICON_STATUS_GREEN', 'Active');
define('IMAGE_ICON_STATUS_GREEN_STOCK', 'in stock');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'Set Active');
define('IMAGE_ICON_STATUS_RED', 'Inactive');
define('IMAGE_ICON_STATUS_RED_LIGHT', 'Set Inactive');
define('TABLE_HEADING_MAX_DISCOUNT', 'Max. discount');

define('TEXT_PRODUCTS_IMAGE_UPLOAD_DIRECTORY', 'Upload directory:');
define('TEXT_PRODUCTS_IMAGE_GET_FILE', 'Use uploaded picture:');
define('TEXT_STANDART_IMAGE', 'Product image');
define('TEXT_SELECT_DIRECTORY', '-- Select upload directory --');
define('TEXT_SELECT_IMAGE', '-- Select image --');

define('TABLE_HEADING_XML', 'XML');
define('TEXT_PRODUCTS_TO_XML', 'Yandex-Market XML:');
define('TEXT_PRODUCT_AVAILABLE_TO_XML', 'Enable');
define('TEXT_PRODUCT_NOT_AVAILABLE_TO_XML', 'Disable');

define('TEXT_EDIT','[edit]');
define('TEXT_PRODUCTS_DATA','Data');
define('TEXT_TAB_CATEGORIES_IMAGE', 'Categories image');

define('ENTRY_CUSTOMERS_ACCESS','Permissions');

define('TEXT_PAGES', 'Pages: ');
define('TEXT_TOTAL_PRODUCTS', 'Products total: ');
define('TEXT_TOTAL_SUM', 'На сумму: ');

define('TEXT_YANDEX_MARKET','<br />Yandex-market options:<br />');
define('TEXT_YANDEX_MARKET_BID','Bid:');
define('TEXT_YANDEX_MARKET_CBID','Cbid:');

// Categiries/Products URL begin
define('TEXT_EDIT_CATEGORY_URL', 'Categories SEO URL:');
define('TEXT_PRODUCTS_PAGE_URL', 'Products page SEO URL:');
define('TEXT_LAST_PAGE', 'Последняя');
// Categiries/Products URL end

define('TABLE_HEADING_STOCK', 'Stock');

?>