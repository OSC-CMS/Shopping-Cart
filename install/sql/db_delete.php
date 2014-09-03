<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

os_db_query("drop table if exists ".
DB_PREFIX."menu,".
DB_PREFIX."menu_group,".
DB_PREFIX."menu_lang,".
DB_PREFIX."admin_notes,".
DB_PREFIX."address_book,".
DB_PREFIX."address_format, ".
DB_PREFIX."admin_access, ".
DB_PREFIX."affiliate_affiliate, ".
DB_PREFIX."affiliate_banners, ".
DB_PREFIX."affiliate_banners_history, ".
DB_PREFIX."affiliate_clickthroughs, ".
DB_PREFIX."affiliate_payment, ".
DB_PREFIX."affiliate_payment_status, ".
DB_PREFIX."affiliate_payment_status_history, ".
DB_PREFIX."affiliate_sales, ".
DB_PREFIX."articles, ".
DB_PREFIX."articles_description, ".
DB_PREFIX."articles_to_topics, ".
DB_PREFIX."articles_xsell, ".
DB_PREFIX."campaigns, ".
DB_PREFIX."campaigns_ip, ".
DB_PREFIX."cm_file_flags, ".
DB_PREFIX."companies, ".
DB_PREFIX."content_manager, ".
DB_PREFIX."coupon_email_track, ".
DB_PREFIX."coupon_gv_customer, ".
DB_PREFIX."coupon_gv_queue, ".
DB_PREFIX."coupon_redeem_track, ".
DB_PREFIX."coupons, ".
DB_PREFIX."coupons_description, ".
DB_PREFIX."customers_ip, ".
DB_PREFIX."customers_memo, ".
DB_PREFIX."customers_status, ".
DB_PREFIX."customers_status_history, ".
DB_PREFIX."customers_status_orders_status, ".
DB_PREFIX."customers_to_extra_fields, ".
DB_PREFIX."extra_fields, ".
DB_PREFIX."extra_fields_info, ".
DB_PREFIX."faq, ".
DB_PREFIX."featured, ".
DB_PREFIX."languages, ".
DB_PREFIX."latest_news, ".
DB_PREFIX."media_content, ".
DB_PREFIX."module_newsletter, ".
DB_PREFIX."module_newsletter_temp_1, ".
DB_PREFIX."module_newsletter_temp_2, ".
DB_PREFIX."newsletter_recipients, ".
DB_PREFIX."newsletters, ".
DB_PREFIX."newsletters_history, ".
DB_PREFIX."orders_recalculate, ".
DB_PREFIX."orders_total, ".
DB_PREFIX."payment_moneybookers, ".
DB_PREFIX."payment_moneybookers_countries, ".
DB_PREFIX."payment_moneybookers_currencies, ".
DB_PREFIX."payment_qenta, ".
DB_PREFIX."personal_offers_by_customers_status_0, ".
DB_PREFIX."personal_offers_by_customers_status_1, ".
DB_PREFIX."personal_offers_by_customers_status_2, ".
DB_PREFIX."personal_offers_by_customers_status_3, ".
DB_PREFIX."persons, ".
DB_PREFIX."post_index, ".
DB_PREFIX."products_content, ".
DB_PREFIX."products_description, ".
DB_PREFIX."products_bundles, ".
DB_PREFIX."products_extra_fields, ".
DB_PREFIX."products_graduated_prices, ".
DB_PREFIX."products_images, ".
DB_PREFIX."products_notifications, ".
DB_PREFIX."products_to_products_extra_fields, ".
DB_PREFIX."products_vpe, ".
DB_PREFIX."products_xsell, ".
DB_PREFIX."products_xsell_grp_name, ".
DB_PREFIX."scart, ".
DB_PREFIX."ship2pay, ".
DB_PREFIX."shipping_status, ".
DB_PREFIX."special_category, ".
DB_PREFIX."special_product, ".
DB_PREFIX."topics, ".
DB_PREFIX."topics_description, ".
DB_PREFIX."banners, ".
DB_PREFIX."banners_history, ".
DB_PREFIX."categories, ".
DB_PREFIX."categories_description, ".
DB_PREFIX."configuration, ".
DB_PREFIX."configuration_group, ".
DB_PREFIX."counter, ".
DB_PREFIX."counter_history, ".
DB_PREFIX."countries, ".
DB_PREFIX."currencies, ".
DB_PREFIX."customers, ".
DB_PREFIX."customers_profile, ".
DB_PREFIX."customers_basket, ".
DB_PREFIX."customers_basket_attributes, ".
DB_PREFIX."customers_info, ".
DB_PREFIX."manufacturers, ".
DB_PREFIX."manufacturers_info, ".
DB_PREFIX."orders, ".
DB_PREFIX."orders_products, ".
DB_PREFIX."orders_status, ".
DB_PREFIX."orders_status_history, ".
DB_PREFIX."orders_products_attributes, ".
DB_PREFIX."orders_products_download, ".
DB_PREFIX."products, ".
DB_PREFIX."products_attributes, ".
DB_PREFIX."products_options_images, ".
DB_PREFIX."products_attributes_download, ".
DB_PREFIX."products_options, ".
DB_PREFIX."products_options_values, ".
DB_PREFIX."products_options_values_to_products_options, ".
DB_PREFIX."products_to_categories, ".
DB_PREFIX."reviews, ".
DB_PREFIX."reviews_description, ".
DB_PREFIX."sessions, ".
DB_PREFIX."specials, ".
DB_PREFIX."tax_class, ".
DB_PREFIX."tax_rates, ".
DB_PREFIX."geo_zones, ".
DB_PREFIX."whos_online, ".
DB_PREFIX."zones, ".
DB_PREFIX."modules, ".
DB_PREFIX."plugins, ".
DB_PREFIX."zones_to_geo_zones");
?>