<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

if (!defined("DB_PREFIX"))
{
   define('DB_PREFIX', 'os_');
}

define('TABLE_ADDRESS_BOOK', DB_PREFIX.'address_book');
define('TABLE_ADDRESS_FORMAT', DB_PREFIX.'address_format');
define('TABLE_BANNERS', DB_PREFIX.'banners');
define('TABLE_BANNERS_HISTORY', DB_PREFIX.'banners_history');
define('TABLE_CAMPAIGNS', DB_PREFIX.'campaigns');
define('TABLE_CATEGORIES', DB_PREFIX.'categories');
define('TABLE_CATEGORIES_DESCRIPTION', DB_PREFIX.'categories_description');
define('TABLE_CONFIGURATION', DB_PREFIX.'configuration');
define('TABLE_CONFIGURATION_GROUP', DB_PREFIX.'configuration_group');
define('TABLE_COUNTER', DB_PREFIX.'counter');
define('TABLE_COUNTER_HISTORY', DB_PREFIX.'counter_history');
define('TABLE_COUNTRIES', DB_PREFIX.'countries');
define('TABLE_CURRENCIES', DB_PREFIX.'currencies');
define('TABLE_CUSTOMERS', DB_PREFIX.'customers');
define('TABLE_CUSTOMERS_BASKET', DB_PREFIX.'customers_basket');
define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', DB_PREFIX.'customers_basket_attributes');
define('TABLE_CUSTOMERS_INFO', DB_PREFIX.'customers_info');
define('TABLE_CUSTOMERS_IP', DB_PREFIX.'customers_ip');
define('TABLE_CUSTOMERS_STATUS', DB_PREFIX.'customers_status');
define('TABLE_CUSTOMERS_STATUS_HISTORY', DB_PREFIX.'customers_status_history');
define('TABLE_LANGUAGES', DB_PREFIX.'languages');
define('TABLE_MANUFACTURERS', DB_PREFIX.'manufacturers');
define('TABLE_MANUFACTURERS_INFO', DB_PREFIX.'manufacturers_info');
define('TABLE_NEWSLETTER_RECIPIENTS', DB_PREFIX.'newsletter_recipients');
define('TABLE_ORDERS', DB_PREFIX.'orders');
define('TABLE_ORDERS_PRODUCTS', DB_PREFIX.'orders_products');
define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', DB_PREFIX.'orders_products_attributes');
define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', DB_PREFIX.'orders_products_download');
define('TABLE_ORDERS_STATUS', DB_PREFIX.'orders_status');
define('TABLE_ORDERS_STATUS_HISTORY', DB_PREFIX.'orders_status_history');
define('TABLE_ORDERS_TOTAL', DB_PREFIX.'orders_total');
define('TABLE_SHIPPING_STATUS', DB_PREFIX.'shipping_status');
define('TABLE_PERSONAL_OFFERS_BY',DB_PREFIX.'personal_offers_by_customers_status_');
define('TABLE_PRODUCTS', DB_PREFIX.'products');
define('TABLE_PRODUCTS_ATTRIBUTES', DB_PREFIX.'products_attributes');
define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', DB_PREFIX.'products_attributes_download');
define('TABLE_PRODUCTS_DESCRIPTION', DB_PREFIX.'products_description');
define('TABLE_PRODUCTS_NOTIFICATIONS', DB_PREFIX.'products_notifications');
define('TABLE_PRODUCTS_IMAGES', DB_PREFIX.'products_images');
define('TABLE_PRODUCTS_OPTIONS', DB_PREFIX.'products_options');
define('TABLE_PRODUCTS_OPTIONS_VALUES', DB_PREFIX.'products_options_values');
define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', DB_PREFIX.'products_options_values_to_products_options');
define('TABLE_PRODUCTS_TO_CATEGORIES', DB_PREFIX.'products_to_categories');
define('TABLE_PRODUCTS_VPE',DB_PREFIX.'products_vpe');
define('TABLE_REVIEWS', DB_PREFIX.'reviews');
define('TABLE_REVIEWS_DESCRIPTION', DB_PREFIX.'reviews_description');
define('TABLE_SESSIONS', DB_PREFIX.'sessions');
define('TABLE_SPECIALS', DB_PREFIX.'specials');
define('TABLE_TAX_CLASS', DB_PREFIX.'tax_class');
define('TABLE_TAX_RATES', DB_PREFIX.'tax_rates');
define('TABLE_GEO_ZONES', DB_PREFIX.'geo_zones');
define('TABLE_ZONES_TO_GEO_ZONES', DB_PREFIX.'zones_to_geo_zones');
define('TABLE_WHOS_ONLINE', DB_PREFIX.'whos_online');
define('TABLE_ZONES', DB_PREFIX.'zones');
define('TABLE_PRODUCTS_XSELL', DB_PREFIX.'products_xsell');
define('TABLE_PRODUCTS_XSELL_GROUPS',DB_PREFIX.'products_xsell_grp_name');
define('TABLE_CONTENT_MANAGER', DB_PREFIX.'content_manager');  
define('TABLE_PRODUCTS_CONTENT',DB_PREFIX.'products_content');
define('TABLE_COUPON_GV_CUSTOMER', DB_PREFIX.'coupon_gv_customer');
define('TABLE_COUPON_GV_QUEUE', DB_PREFIX.'coupon_gv_queue');
define('TABLE_COUPON_REDEEM_TRACK', DB_PREFIX.'coupon_redeem_track');
define('TABLE_COUPON_EMAIL_TRACK', DB_PREFIX.'coupon_email_track');
define('TABLE_COUPONS', DB_PREFIX.'coupons');
define('TABLE_COUPONS_DESCRIPTION', DB_PREFIX.'coupons_description');
define('TABLE_CAMPAIGNS_IP',DB_PREFIX.'campaigns_ip');

define('TABLE_LATEST_NEWS', DB_PREFIX.'latest_news');
define('TABLE_FEATURED', DB_PREFIX.'featured');

define('TABLE_ARTICLES', DB_PREFIX.'articles');
define('TABLE_ARTICLES_DESCRIPTION', DB_PREFIX.'articles_description');
define('TABLE_ARTICLES_TO_TOPICS', DB_PREFIX.'articles_to_topics');
define('TABLE_TOPICS', DB_PREFIX.'topics');
define('TABLE_TOPICS_DESCRIPTION', DB_PREFIX.'topics_description');
define('TABLE_ARTICLES_XSELL', DB_PREFIX.'articles_xsell');

define('TABLE_MONEYBOOKERS',DB_PREFIX.'payment_moneybookers');
define('TABLE_MONEYBOOKERS_COUNTRIES',DB_PREFIX.'payment_moneybookers_countries');
define('TABLE_MONEYBOOKERS_CURRENCIES',DB_PREFIX.'payment_moneybookers_currencies');
define('TABLE_NEWSLETTER_TEMP',DB_PREFIX.'module_newsletter_temp_');
define('TABLE_PERSONAL_OFFERS',DB_PREFIX.'personal_offers_by_customers_status_');

define('TABLE_SHIP2PAY',DB_PREFIX.'ship2pay');

define('TABLE_PRODUCTS_EXTRA_FIELDS', DB_PREFIX.'products_extra_fields'); 
define('TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS', DB_PREFIX.'products_to_products_extra_fields'); 

define('TABLE_FAQ', DB_PREFIX.'faq');
define('TABLE_HELP', DB_PREFIX.'help');
  
define('TABLE_COMPANIES', DB_PREFIX.'companies');
define('TABLE_PERSONS', DB_PREFIX.'persons');

define('TABLE_EXTRA_FIELDS',DB_PREFIX.'extra_fields');
define('TABLE_EXTRA_FIELDS_INFO',DB_PREFIX.'extra_fields_info');
define('TABLE_CUSTOMERS_TO_EXTRA_FIELDS',DB_PREFIX.'customers_to_extra_fields');

?>