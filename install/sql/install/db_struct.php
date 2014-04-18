<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

os_db_query("CREATE TABLE ".DB_PREFIX."admin_notes (
	id int NOT NULL auto_increment,
	note text NOT NULL,
	customer int(11) NOT NULL,
	date_added datetime NOT NULL default '0000-00-00 00:00:00',
	status tinyint(1) NOT NULL,
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."sms_notes (
	id int NOT NULL auto_increment,
	order_id int(11) NOT NULL,
	note text NOT NULL default '',
	phone varchar(255) NOT NULL default '',
	date_added datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY (id),
	KEY order_id (order_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."address_book (
  address_book_id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  entry_gender char(1) NOT NULL default '',
  entry_company varchar(255) default '',
  entry_firstname varchar(255) NOT NULL default '',
  entry_secondname varchar(255) NOT NULL default '',
  entry_lastname varchar(255) NOT NULL default '',
  entry_street_address varchar(255) NOT NULL default '',
  entry_suburb varchar(255) default '',
  entry_postcode varchar(10) NOT NULL default '',
  entry_city varchar(255) NOT NULL default '',
  entry_state varchar(255) default '',
  entry_country_id int DEFAULT '0' NOT NULL,
  entry_zone_id int DEFAULT '0' NOT NULL,
  address_date_added datetime DEFAULT '0000-00-00 00:00:00',
  address_last_modified datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (address_book_id),
  KEY idx_address_book_customers_id (customers_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_affiliate (
  affiliate_id int(11) NOT NULL auto_increment,
  affiliate_lft int(11) NOT NULL,
  affiliate_rgt int(11) NOT NULL,
  affiliate_root int(11) NOT NULL,
  affiliate_gender char(1) NOT NULL default '',
  affiliate_firstname varchar(32) NOT NULL default '',
  affiliate_lastname varchar(32) NOT NULL default '',
  affiliate_dob datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_email_address varchar(96) NOT NULL default '',
  affiliate_telephone varchar(32) NOT NULL default '',
  affiliate_fax varchar(32) NOT NULL default '',
  affiliate_password varchar(40) NOT NULL default '',
  affiliate_homepage varchar(96) NOT NULL default '',
  affiliate_street_address varchar(64) NOT NULL default '',
  affiliate_suburb varchar(64) NOT NULL default '',
  affiliate_city varchar(32) NOT NULL default '',
  affiliate_postcode varchar(10) NOT NULL default '',
  affiliate_state varchar(32) NOT NULL default '',
  affiliate_country_id int(11) NOT NULL default '0',
  affiliate_zone_id int(11) NOT NULL default '0',
  affiliate_agb tinyint(4) NOT NULL default '0',
  affiliate_company varchar(60) NOT NULL default '',
  affiliate_company_taxid varchar(64) NOT NULL default '',
  affiliate_commission_percent DECIMAL(4,2) NOT NULL default '0.00',
  affiliate_payment_check varchar(100) NOT NULL default '',
  affiliate_payment_paypal varchar(64) NOT NULL default '',
  affiliate_payment_bank_name varchar(64) NOT NULL default '',
  affiliate_payment_bank_branch_number varchar(64) NOT NULL default '',
  affiliate_payment_bank_swift_code varchar(64) NOT NULL default '',
  affiliate_payment_bank_account_name varchar(64) NOT NULL default '',
  affiliate_payment_bank_account_number varchar(64) NOT NULL default '',
  affiliate_date_of_last_logon datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_number_of_logons int(11) NOT NULL default '0',
  affiliate_date_account_created datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_date_account_last_modified datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (affiliate_id),
  KEY affiliate_root (affiliate_root),
  KEY affiliate_rgt (affiliate_rgt),
  KEY affiliate_lft (affiliate_lft)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_banners (
  affiliate_banners_id int(11) NOT NULL auto_increment,
  affiliate_banners_title varchar(64) NOT NULL default '',
  affiliate_products_id int(11) NOT NULL default '0',
  affiliate_banners_image varchar(64) NOT NULL default '',
  affiliate_banners_group varchar(10) NOT NULL default '',
  affiliate_banners_html_text text,
  affiliate_expires_impressions int(7) default '0',
  affiliate_expires_date datetime default NULL,
  affiliate_date_scheduled datetime default NULL,
  affiliate_date_added datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_date_status_change datetime default NULL,
  affiliate_status int(1) NOT NULL default '1',
  PRIMARY KEY  (affiliate_banners_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."plugins (
  plugins_id int NOT NULL auto_increment, 
  plugins_key varchar(64) NOT NULL,
  plugins_name varchar(64) NOT NULL,
  plugins_value text(255) NOT NULL,
  sort_order int(5) NULL,
  sort_plugins int(5) NULL,
  use_function text(255) NULL,
  PRIMARY KEY (plugins_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_banners_history (
  affiliate_banners_history_id int(11) NOT NULL auto_increment,
  affiliate_banners_products_id int(11) NOT NULL default '0',
  affiliate_banners_id int(11) NOT NULL default '0',
  affiliate_banners_affiliate_id int(11) NOT NULL default '0',
  affiliate_banners_shown int(11) NOT NULL default '0',
  affiliate_banners_clicks tinyint(4) NOT NULL default '0',
  affiliate_banners_history_date date NOT NULL default '0000-00-00',
  PRIMARY KEY  (affiliate_banners_history_id,affiliate_banners_products_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_clickthroughs (
  affiliate_clickthrough_id int(11) NOT NULL auto_increment,
  affiliate_id int(11) NOT NULL default '0',
  affiliate_clientdate datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_clientbrowser varchar(200) default 'Нет данных',
  affiliate_clientip varchar(50) default 'Нет данных',
  affiliate_clientreferer varchar(200) default 'не определено (возможно прямая ссылка)',
  affiliate_products_id int(11) default '0',
  affiliate_banner_id int(11) NOT NULL default '0',
  PRIMARY KEY  (affiliate_clickthrough_id),
  KEY refid (affiliate_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_payment (
  affiliate_payment_id int(11) NOT NULL auto_increment,
  affiliate_id int(11) NOT NULL default '0',
  affiliate_payment decimal(15,2) NOT NULL default '0.00',
  affiliate_payment_tax decimal(15,2) NOT NULL default '0.00',
  affiliate_payment_total decimal(15,2) NOT NULL default '0.00',
  affiliate_payment_date datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_payment_last_modified datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_payment_status int(5) NOT NULL default '0',
  affiliate_firstname varchar(32) NOT NULL default '',
  affiliate_lastname varchar(32) NOT NULL default '',
  affiliate_street_address varchar(64) NOT NULL default '',
  affiliate_suburb varchar(64) NOT NULL default '',
  affiliate_city varchar(32) NOT NULL default '',
  affiliate_postcode varchar(10) NOT NULL default '',
  affiliate_country varchar(32) NOT NULL default '0',
  affiliate_company varchar(60) NOT NULL default '',
  affiliate_state varchar(32) NOT NULL default '0',
  affiliate_address_format_id int(5) NOT NULL default '0',
  affiliate_last_modified datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (affiliate_payment_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_payment_status (
  affiliate_payment_status_id int(11) NOT NULL default '0',
  affiliate_language_id int(11) NOT NULL default '1',
  affiliate_payment_status_name varchar(32) NOT NULL default '',
  PRIMARY KEY  (affiliate_payment_status_id,affiliate_language_id),
  KEY idx_affiliate_payment_status_name (affiliate_payment_status_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_payment_status_history (
  affiliate_status_history_id int(11) NOT NULL auto_increment,
  affiliate_payment_id int(11) NOT NULL default '0',
  affiliate_new_value int(5) NOT NULL default '0',
  affiliate_old_value int(5) default NULL,
  affiliate_date_added datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_notified int(1) default '0',
  PRIMARY KEY  (affiliate_status_history_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."affiliate_sales (
  affiliate_id int(11) NOT NULL default '0',
  affiliate_date datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_browser varchar(100) NOT NULL default '',
  affiliate_ipaddress varchar(20) NOT NULL default '',
  affiliate_orders_id int(11) NOT NULL default '0',
  affiliate_value decimal(15,2) NOT NULL default '0.00',
  affiliate_payment decimal(15,2) NOT NULL default '0.00',
  affiliate_clickthroughs_id int(11) NOT NULL default '0',
  affiliate_billing_status int(5) NOT NULL default '0',
  affiliate_payment_date datetime NOT NULL default '0000-00-00 00:00:00',
  affiliate_payment_id int(11) NOT NULL default '0',
  affiliate_percent  DECIMAL(4,2)  NOT NULL default '0.00',
  affiliate_salesman int(11) NOT NULL default '0',
  affiliate_level tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (affiliate_id,affiliate_orders_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."topics (
  topics_id int(11) NOT NULL auto_increment,
  topics_image varchar(64) default NULL,
  parent_id int(11) NOT NULL default '0',
  sort_order int(3) default NULL,
  date_added datetime default NULL,
  last_modified datetime default NULL,
  topics_page_url varchar(255),
  PRIMARY KEY  (topics_id),
  KEY idx_topics_parent_id (parent_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."topics_description (
  topics_id int(11) NOT NULL default '0',
  language_id int(11) NOT NULL default '1',
  topics_name varchar(255) NOT NULL default '',
  topics_heading_title varchar(255) default NULL,
  topics_description text,
  PRIMARY KEY  (topics_id,language_id),
  KEY idx_topics_name (topics_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."articles (
  articles_id int(11) NOT NULL auto_increment,
  articles_date_added datetime NOT NULL default '0000-00-00 00:00:00',
  articles_last_modified datetime default NULL,
  articles_date_available datetime default NULL,
  articles_status tinyint(1) NOT NULL default '0',
  articles_page_url varchar(255),
  sort_order int(4) NOT NULL default '0',
  PRIMARY KEY  (articles_id),
  KEY idx_articles_date_added (articles_date_added)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."articles_description (
  articles_id int(11) NOT NULL auto_increment,
  language_id int(11) NOT NULL default '1',
  articles_name varchar(255) NOT NULL default '',
  articles_description_short text,
  articles_description text,
  articles_url varchar(255) default NULL,
  articles_viewed int(5) default '0',
  articles_head_title_tag varchar(80) default NULL,
  articles_head_desc_tag text,
  articles_head_keywords_tag text,
  PRIMARY KEY  (articles_id,language_id),
  KEY articles_name (articles_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."articles_to_topics (
  articles_id int(11) NOT NULL default '0',
  topics_id int(11) NOT NULL default '0',
  PRIMARY KEY  (articles_id,topics_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."articles_xsell (
  ID int(10) not null auto_increment,
  articles_id int(10) unsigned default '1' not null ,
  xsell_id int(10) unsigned default '1' not null ,
  sort_order int(10) unsigned default '1' not null ,
  PRIMARY KEY (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_memo (
  memo_id int(11) NOT NULL auto_increment,
  customers_id int(11) NOT NULL default '0',
  memo_date date NOT NULL default '0000-00-00',
  memo_title text NOT NULL,
  memo_text text NOT NULL,
  poster_id int(11) NOT NULL default '0',
  PRIMARY KEY  (memo_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_extra_fields (
  products_extra_fields_id int(11) not null auto_increment,
  products_extra_fields_name varchar(255) not null ,
  products_extra_fields_order int(3) default '0' not null ,
  products_extra_fields_status tinyint(1) default '1' not null ,
  products_extra_fields_group int(11) not null ,
  languages_id int(11) default '0' not null ,
  PRIMARY KEY (products_extra_fields_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_extra_fields_groups (
  extra_fields_groups_id int(11) not null auto_increment,
  extra_fields_groups_order int(3) default '0' not null ,
  extra_fields_groups_status tinyint(1) default '1' not null ,
  PRIMARY KEY (extra_fields_groups_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_extra_fields_groups_desc (
  extra_fields_groups_desc_id int(11) not null auto_increment,
  extra_fields_groups_id int(11) not null,
  extra_fields_groups_name varchar(255) not null ,
  extra_fields_groups_languages_id int(11) default '0' not null ,
  PRIMARY KEY (extra_fields_groups_desc_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_to_products_extra_fields (
  products_id int(11) default '0' not null ,
  products_extra_fields_id int(11) default '0' not null ,
  products_extra_fields_value varchar(255) ,
  PRIMARY KEY (products_id, products_extra_fields_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_xsell (
  ID int(10) NOT NULL auto_increment,
  products_id int(10) unsigned NOT NULL default '1',
  products_xsell_grp_name_id int(10) unsigned NOT NULL default '1',
  xsell_id int(10) unsigned NOT NULL default '1',
  sort_order int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_xsell_grp_name (
  products_xsell_grp_name_id int(10) NOT NULL,
  xsell_sort_order int(10) NOT NULL default '0',
  language_id smallint(6) NOT NULL default '0',
  groupname varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."campaigns (
  campaigns_id int(11) NOT NULL auto_increment,
  campaigns_name varchar(255) NOT NULL default '',
  campaigns_refID varchar(255) default NULL,
  campaigns_leads int(11) NOT NULL default '0',
  date_added datetime default NULL,
  last_modified datetime default NULL,
  PRIMARY KEY  (campaigns_id),
  KEY IDX_CAMPAIGNS_NAME (campaigns_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE `".DB_PREFIX."admin_access` (
  `customers_id` varchar(255) NOT NULL DEFAULT '0',
  `configuration` int(1) NOT NULL DEFAULT '0',
  `modules` int(1) NOT NULL DEFAULT '0',
  `countries` int(1) NOT NULL DEFAULT '0',
  `currencies` int(1) NOT NULL DEFAULT '0',
  `zones` int(1) NOT NULL DEFAULT '0',
  `geo_zones` int(1) NOT NULL DEFAULT '0',
  `tax_classes` int(1) NOT NULL DEFAULT '0',
  `tax_rates` int(1) NOT NULL DEFAULT '0',
  `accounting` int(1) NOT NULL DEFAULT '0',
  `backup` int(1) NOT NULL DEFAULT '0',
  `server_info` int(1) NOT NULL DEFAULT '0',
  `whos_online` int(1) NOT NULL DEFAULT '0',
  `languages` int(1) NOT NULL DEFAULT '0',
  `define_language` int(1) NOT NULL DEFAULT '0',
  `orders_status` int(1) NOT NULL DEFAULT '0',
  `shipping_status` int(1) NOT NULL DEFAULT '0',
  `customers` int(1) NOT NULL DEFAULT '0',
  `customers_status` int(1) NOT NULL DEFAULT '0',
  `orders` int(1) NOT NULL DEFAULT '0',
  `campaigns` int(1) NOT NULL DEFAULT '0',
  `menu` int(1) NOT NULL DEFAULT '0',
  `coupon_admin` int(1) NOT NULL DEFAULT '0',
  `gv_queue` int(1) NOT NULL DEFAULT '0',
  `gv_mail` int(1) NOT NULL DEFAULT '0',
  `gv_sent` int(1) NOT NULL DEFAULT '0',
  `mail` int(1) NOT NULL DEFAULT '0',
  `categories` int(1) NOT NULL DEFAULT '0',
  `new_attributes` int(1) NOT NULL DEFAULT '0',
  `products_attributes` int(1) NOT NULL DEFAULT '0',
  `manufacturers` int(1) NOT NULL DEFAULT '0',
  `reviews` int(1) NOT NULL DEFAULT '0',
  `specials` int(1) NOT NULL DEFAULT '0',
  `stats_products_expected` int(1) NOT NULL DEFAULT '0',
  `stats_products_viewed` int(1) NOT NULL DEFAULT '0',
  `stats_products_purchased` int(1) NOT NULL DEFAULT '0',
  `stats_customers` int(1) NOT NULL DEFAULT '0',
  `stats_sales_report` int(1) NOT NULL DEFAULT '0',
  `stats_campaigns` int(1) NOT NULL DEFAULT '0',
  `module_newsletter` int(1) NOT NULL DEFAULT '0',
  `index2` int(1) NOT NULL DEFAULT '0',
  `content_manager` int(1) NOT NULL DEFAULT '0',
  `content_preview` int(1) NOT NULL DEFAULT '0',
  `credits` int(1) NOT NULL DEFAULT '0',
  `blacklist` int(1) NOT NULL DEFAULT '0',
  `popup_image` int(1) NOT NULL DEFAULT '0',
  `csv_backend` int(1) NOT NULL DEFAULT '0',
  `products_vpe` int(1) NOT NULL DEFAULT '0',
  `cross_sell_groups` int(1) NOT NULL DEFAULT '0',
  `fck_wrapper` int(1) NOT NULL DEFAULT '0',
  `easypopulate` int(1) NOT NULL DEFAULT '0',
  `quick_updates` int(1) NOT NULL DEFAULT '0',
  `latest_news` int(1) NOT NULL DEFAULT '0',
  `recover_cart_sales` int(1) NOT NULL DEFAULT '0',
  `featured` int(1) NOT NULL DEFAULT '0',
  `articles` int(1) NOT NULL DEFAULT '0',
  `articles_config` int(1) NOT NULL DEFAULT '0',
  `stats_sales_report2` int(1) NOT NULL DEFAULT '0',
  `chart_data` int(1) NOT NULL DEFAULT '0',
  `articles_xsell` int(1) NOT NULL DEFAULT '0',
  `email_manager` int(1) NOT NULL DEFAULT '0',
  `products_options` int(1) NOT NULL DEFAULT '0',
  `product_extra_fields` int(1) NOT NULL DEFAULT '0',
  `ship2pay` int(1) NOT NULL DEFAULT '0',
  `faq` int(1) NOT NULL DEFAULT '0',
  `affiliate_affiliates` int(1) NOT NULL DEFAULT '0',
  `affiliate_banners` int(1) NOT NULL DEFAULT '0',
  `affiliate_clicks` int(1) NOT NULL DEFAULT '0',
  `affiliate_contact` int(1) NOT NULL DEFAULT '0',
  `affiliate_invoice` int(1) NOT NULL DEFAULT '0',
  `affiliate_payment` int(1) NOT NULL DEFAULT '0',
  `affiliate_popup_image` int(1) NOT NULL DEFAULT '0',
  `affiliate_sales` int(1) NOT NULL DEFAULT '0',
  `affiliate_statistics` int(1) NOT NULL DEFAULT '0',
  `affiliate_summary` int(1) NOT NULL DEFAULT '0',
  `customer_extra_fields` int(1) NOT NULL DEFAULT '0',
  `themes` int(1) NOT NULL,
  `themes_edit` int(1) NOT NULL,
  `general_index` int(1) NOT NULL,
  `plugins` int(1) NOT NULL,
  `stats_stock_warning` int(1) NOT NULL,
  `products_expected` int(1) NOT NULL,
  `plugins_page` int(1) NOT NULL,
  `file` int(1) NOT NULL,
  `error_log` int(1) NOT NULL,
  `ajax` int(1) NOT NULL default '1',
  `sms` int(1) NOT NULL default '1',
  `cartet` int(1) NOT NULL default '1',
  `update` int(1) NOT NULL default '1',
  `content` int(1) NOT NULL default '1',
  PRIMARY KEY (`customers_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */;
");

os_db_query("CREATE TABLE ".DB_PREFIX."companies (
  orders_id int(11) NOT NULL default '0',
  name varchar(255) default NULL,
  inn varchar(255) default NULL,
  kpp varchar(255) default NULL,
  ogrn varchar(255) default NULL,
  okpo varchar(255) default NULL,
  rs varchar(255) default NULL,
  bank_name varchar(255) default NULL,
  bik varchar(255) default NULL,
  ks varchar(255) default NULL,
  address varchar(255) default NULL,
  yur_address varchar(255) default NULL,
  fakt_address varchar(255) default NULL,
  telephone varchar(255) default NULL,
  fax varchar(255) default NULL,
  email varchar(255) default NULL,
  director varchar(255) default NULL,
  accountant varchar(255) default NULL,
  KEY orders_id(orders_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."persons (
  orders_id int(11) NOT NULL default '0',
  name varchar(255) default NULL,
  address varchar(255) default NULL,
  KEY orders_id(orders_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."categories (
  categories_id int NOT NULL auto_increment,
  categories_image varchar(255),
  parent_id int DEFAULT '0' NOT NULL,
  categories_status tinyint (1)  UNSIGNED DEFAULT '1' NOT NULL,
  categories_template varchar(255),
  group_permission_0 tinyint(1) NOT NULL,
  group_permission_1 tinyint(1) NOT NULL,
  group_permission_2 tinyint(1) NOT NULL,
  group_permission_3 tinyint(1) NOT NULL,
  listing_template varchar(255),
  sort_order int(3) DEFAULT '0' NOT NULL,
  products_sorting varchar(255),
  products_sorting2 varchar(255),
  date_added datetime,
  last_modified datetime,
  yml_bid int(4) NOT NULL DEFAULT '0',
  yml_cbid int(4) NOT NULL DEFAULT '0',
  categories_url varchar(255),
  yml_enable tinyint(1) NOT NULL default '1',
  categories_count int NOT NULL default '0',
  menu tinyint(1) NOT NULL default '1',
  PRIMARY KEY (categories_id),
  KEY idx_categories_parent_id (parent_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=0;");

os_db_query("CREATE TABLE ".DB_PREFIX."categories_description (
  categories_id int DEFAULT '0' NOT NULL,
  language_id int DEFAULT '1' NOT NULL,
  categories_name varchar(255) NOT NULL,
  categories_heading_title varchar(255) NOT NULL,
  categories_description text NOT NULL,
  categories_meta_title varchar(255) NOT NULL,
  categories_meta_description varchar(255) NOT NULL,
  categories_meta_keywords varchar(255) NOT NULL,
  PRIMARY KEY (categories_id, language_id),
  KEY idx_categories_name (categories_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."configuration (
  configuration_id int NOT NULL auto_increment,
  configuration_key varchar(255) NOT NULL,
  configuration_value varchar(255) NOT NULL,
  configuration_group_id int NOT NULL,
  sort_order int(5) NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  use_function varchar(255) NULL,
  set_function varchar(255) NULL,
  PRIMARY KEY (configuration_id),
  KEY idx_configuration_group_id (configuration_group_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

/*
os_db_query("CREATE TABLE ".DB_PREFIX."modules (
  modules_id int NOT NULL auto_increment, 
  modules_key varchar(64) NOT NULL,
  modules_name varchar(64) NOT NULL,
  modules_value varchar(255) NOT NULL,
  sort_order int(5) NULL,
  sort_modules int(5) NULL,
  use_function varchar(255) NULL,
  PRIMARY KEY (modules_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
*/
os_db_query("CREATE TABLE ".DB_PREFIX."configuration_group (
  configuration_group_id int NOT NULL auto_increment,
  configuration_group_key varchar(255) NOT NULL,
  sort_order int(5) NULL,
  visible int(1) DEFAULT '1' NULL,
  PRIMARY KEY (configuration_group_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."counter (
  startdate char(8),
  counter int(12)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."counter_history (
  month char(8),
  counter int(12)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."countries (
  countries_id int NOT NULL auto_increment,
  countries_name varchar(255) NOT NULL,
  countries_iso_code_2 char(2) NOT NULL,
  countries_iso_code_3 char(3) NOT NULL,
  address_format_id int NOT NULL,
  status int(1) DEFAULT '1' NULL,  
  PRIMARY KEY (countries_id),
  KEY IDX_COUNTRIES_NAME (countries_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."currencies (
  currencies_id int NOT NULL auto_increment,
  title varchar(255) NOT NULL,
  code char(3) NOT NULL,
  symbol_left varchar(12),
  symbol_right varchar(12),
  decimal_point char(1),
  thousands_point char(1),
  decimal_places char(1),
  value float(13,8),
  last_updated datetime NULL,
  PRIMARY KEY (currencies_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers (
  customers_id int NOT NULL auto_increment,
  customers_cid varchar(255),
  customers_vat_id varchar (20) DEFAULT NULL,
  customers_vat_id_status int(2) DEFAULT '0' NOT NULL,
  customers_warning varchar(255),
  customers_status int(5) DEFAULT '1' NOT NULL,
  customers_gender char(1) DEFAULT '' NOT NULL,
  customers_firstname varchar(255) DEFAULT '' NOT NULL,
  customers_secondname varchar(255) DEFAULT '' NOT NULL,
  customers_lastname varchar(255) DEFAULT '' NOT NULL,
  customers_dob datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  customers_email_address varchar(96) NOT NULL,
  customers_default_address_id int NOT NULL,
  customers_telephone varchar(255) NOT NULL,
  customers_fax varchar(255),
  customers_password varchar(40) NOT NULL,
  customers_newsletter char(1),
  customers_newsletter_mode char( 1 ) DEFAULT '0' NOT NULL,
  member_flag char(1) DEFAULT '0' NOT NULL,
  delete_user char(1) DEFAULT '1' NOT NULL,
  account_type int(1) NOT NULL default '0',
  password_request_key varchar(255) DEFAULT '' NOT NULL,
  payment_unallowed varchar(255) default '' NOT NULL,
  shipping_unallowed varchar(255) default ''  NOT NULL,
  refferers_id int(5) DEFAULT '0' NOT NULL,
  customers_date_added datetime DEFAULT '0000-00-00 00:00:00',
  customers_last_modified datetime DEFAULT '0000-00-00 00:00:00',
  orig_reference text,
  login_reference text,
  login_tries char(2) NOT NULL default '0',
  login_time datetime NOT NULL default '0000-00-00 00:00:00',
  customers_username VARCHAR(64) DEFAULT NULL,
  customers_fid INT(5) DEFAULT NULL,
  customers_sid INT(5) DEFAULT NULL,
  PRIMARY KEY (customers_id),
  KEY customers_username (customers_username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_profile (
	customers_id int NOT NULL auto_increment,
	customers_signature varchar(255),
	show_gender tinyint NOT NULL DEFAULT 1,
	show_firstname tinyint NOT NULL DEFAULT 1,
	show_secondname tinyint NOT NULL DEFAULT 0,
	show_lastname tinyint NOT NULL DEFAULT 0,
	show_dob tinyint NOT NULL DEFAULT 1,
	show_email tinyint NOT NULL DEFAULT 0,
	show_telephone tinyint NOT NULL DEFAULT 0,
	show_fax tinyint NOT NULL DEFAULT 0,
	customers_wishlist int(11) NOT NULL DEFAULT 0,
	customers_avatar varchar(255) DEFAULT NULL,
	customers_photo varchar(255) DEFAULT NULL,
	PRIMARY KEY (customers_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_basket (
  customers_basket_id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  products_id tinytext NOT NULL,
  customers_basket_quantity int(2) NOT NULL,
  final_price decimal(15,4) NOT NULL,
  customers_basket_date_added char(8),
  PRIMARY KEY (customers_basket_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_basket_attributes (
  customers_basket_attributes_id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  products_id tinytext NOT NULL,
  products_options_id int NOT NULL,
  products_options_value_id int NOT NULL,
  products_options_value_text text,
  PRIMARY KEY (customers_basket_attributes_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_info (
  customers_info_id int NOT NULL,
  customers_info_date_of_last_logon datetime,
  customers_info_number_of_logons int(5),
  customers_info_date_account_created datetime,
  customers_info_date_account_last_modified datetime,
  global_product_notifications int(1) DEFAULT '0',
  PRIMARY KEY (customers_info_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_ip (
  customers_ip_id int(11) NOT NULL auto_increment,
  customers_id int(11) NOT NULL default '0',
  customers_ip varchar(15) NOT NULL default '',
  customers_ip_date datetime NOT NULL default '0000-00-00 00:00:00',
  customers_host varchar(255) NOT NULL default '',
  customers_advertiser varchar(30) default NULL,
  customers_referer_url varchar(255) default NULL,
  PRIMARY KEY  (customers_ip_id),
  KEY customers_id (customers_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_status (
  customers_status_id int(11) NOT NULL default '0',
  language_id int(11) NOT NULL DEFAULT '1',
  customers_status_name varchar(255) NOT NULL DEFAULT '',
  customers_status_public int(1) NOT NULL DEFAULT '1',
  customers_status_min_order int(7) DEFAULT NULL,
  customers_status_max_order int(7) DEFAULT NULL,
  customers_status_image varchar(255) DEFAULT NULL,
  customers_status_discount decimal(4,2) DEFAULT '0',
  customers_status_ot_discount_flag char(1) NOT NULL DEFAULT '0',
  customers_status_ot_discount decimal(4,2) DEFAULT '0',
  customers_status_graduated_prices varchar(1) NOT NULL DEFAULT '0',
  customers_status_show_price int(1) NOT NULL DEFAULT '1',
  customers_status_show_price_tax int(1) NOT NULL DEFAULT '1',
  customers_status_add_tax_ot  int(1) NOT NULL DEFAULT '0',
  customers_status_payment_unallowed varchar(255) NOT NULL,
  customers_status_shipping_unallowed varchar(255) NOT NULL,
  customers_status_discount_attributes  int(1) NOT NULL DEFAULT '0',
  customers_fsk18 int(1) NOT NULL DEFAULT '1',
  customers_fsk18_display int(1) NOT NULL DEFAULT '1',
  customers_status_write_reviews int(1) NOT NULL DEFAULT '1',
  customers_status_read_reviews int(1) NOT NULL DEFAULT '1',
  customers_status_accumulated_limit decimal(15,4) DEFAULT '0' ,
  PRIMARY KEY  (customers_status_id,language_id),
  KEY idx_orders_status_name (customers_status_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_status_orders_status (
  customers_status_id int(11) default '0' not null ,
  orders_status_id int(11) default '0' not null
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_status_history (
  customers_status_history_id int(11) NOT NULL auto_increment,
  customers_id int(11) NOT NULL default '0',
  new_value int(5) NOT NULL default '0',
  old_value int(5) default NULL,
  date_added datetime NOT NULL default '0000-00-00 00:00:00',
  customer_notified int(1) default '0',
  PRIMARY KEY  (customers_status_history_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."customers_to_extra_fields (
  customers_id int(11) NOT NULL default '0',
  fields_id int(11) NOT NULL default '0',
  value text
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."extra_fields (
  fields_id int(11) not null auto_increment,
  fields_input_type int(11) default '0' not null ,
  fields_input_value text NOT NULL,
  fields_status tinyint(2) default '0' not null ,
  fields_required_status tinyint(2) default '0' not null ,
  fields_size int(5) default '0' not null ,
  fields_required_email tinyint(2) default '0' not null ,
  PRIMARY KEY (fields_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."extra_fields_info (
  fields_id int(11) NOT NULL default '0',
  languages_id int(11) NOT NULL default '0',
  fields_name varchar(32) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."faq (
  faq_id int(11) NOT NULL AUTO_INCREMENT,
  question varchar(255) NOT NULL,
  answer text NOT NULL,
  date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  language int(11) NOT NULL default '1',
  status tinyint(1) DEFAULT '0' NOT NULL,
  faq_page_url varchar(256) NOT NULL,
  PRIMARY KEY (faq_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."languages (
  languages_id int NOT NULL auto_increment,
  name varchar(255)  NOT NULL,
  code char(2) NOT NULL,
  image varchar(255),
  directory varchar(255),
  sort_order int(3),
  status int(3),
  language_charset text NOT NULL,
  PRIMARY KEY (languages_id),
  KEY IDX_LANGUAGES_NAME (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."latest_news (
   news_id int(11) NOT NULL auto_increment,
   headline varchar(255) NOT NULL,
   content text NOT NULL,
   date_added datetime NOT NULL default '0000-00-00 00:00:00',
   language int(11) NOT NULL default '1',
   status tinyint(1) NOT NULL default '0',
   news_page_url varchar(255) NOT NULL,
   news_image varchar(255) NOT NULL default '',
   PRIMARY KEY (news_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."manufacturers (
  manufacturers_id int NOT NULL auto_increment,
  manufacturers_name varchar(255) NOT NULL,
  manufacturers_image varchar(255),
  date_added datetime NULL,
  last_modified datetime NULL,
  manufacturers_page_url varchar(255) NOT NULL,
  PRIMARY KEY (manufacturers_id),
  KEY IDX_MANUFACTURERS_NAME (manufacturers_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."manufacturers_info (
  manufacturers_id int NOT NULL,
  languages_id int NOT NULL,
  manufacturers_meta_title varchar(255) NOT NULL,
  manufacturers_meta_description varchar(255) NOT NULL,
  manufacturers_meta_keywords varchar(255) NOT NULL,
  manufacturers_url varchar(255) NOT NULL,
  manufacturers_description text(65534) NOT NULL,
  url_clicked int(5) NOT NULL default '0',
  date_last_click datetime NULL,
  PRIMARY KEY (manufacturers_id, languages_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."newsletters (
  newsletters_id int NOT NULL auto_increment,
  title varchar(255) NOT NULL,
  content text NOT NULL,
  module varchar(255) NOT NULL,
  date_added datetime NOT NULL,
  date_sent datetime,
  status int(1),
  locked int(1) DEFAULT '0',
  PRIMARY KEY (newsletters_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."newsletter_recipients (
  mail_id int(11) NOT NULL auto_increment,
  customers_email_address varchar(96) NOT NULL default '',
  customers_id int(11) NOT NULL default '0',
  customers_status int(5) NOT NULL default '0',
  customers_firstname varchar(255) NOT NULL default '',
  customers_lastname varchar(255) NOT NULL default '',
  mail_status int(1) NOT NULL default '0',
  mail_key varchar(255) NOT NULL default '',
  date_added datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (mail_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."newsletters_history (
  news_hist_id int(11) NOT NULL default '0',
  news_hist_cs int(11) NOT NULL default '0',
  news_hist_cs_date_sent date default NULL,
  PRIMARY KEY  (news_hist_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."orders (
  orders_id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  customers_cid varchar(255),
  customers_vat_id varchar (20) DEFAULT NULL,
  customers_status int(11),
  customers_status_name varchar(255) NOT NULL,
  customers_status_image varchar (64),
  customers_status_discount decimal (4,2),
  customers_name varchar(255) NOT NULL,
  customers_firstname varchar(255) NOT NULL,
  customers_secondname varchar(255) NOT NULL,
  customers_lastname varchar(255) NOT NULL,
  customers_company varchar(255),
  customers_street_address varchar(255) NOT NULL,
  customers_suburb varchar(255),
  customers_city varchar(255) NOT NULL,
  customers_postcode varchar(10) NOT NULL,
  customers_state varchar(255),
  customers_country varchar(255) NOT NULL,
  customers_telephone varchar(255) NOT NULL,
  customers_email_address varchar(96) NOT NULL,
  customers_address_format_id int(5) NOT NULL,
  delivery_name varchar(255) NOT NULL,
  delivery_firstname varchar(255) NOT NULL,
  delivery_secondname varchar(255) NOT NULL,
  delivery_lastname varchar(255) NOT NULL,
  delivery_company varchar(255),
  delivery_street_address varchar(255) NOT NULL,
  delivery_suburb varchar(255),
  delivery_city varchar(255) NOT NULL,
  delivery_postcode varchar(10) NOT NULL,
  delivery_state varchar(255),
  delivery_country varchar(255) NOT NULL,
  delivery_country_iso_code_2 char(2) NOT NULL,
  delivery_address_format_id int(5) NOT NULL,
  billing_name varchar(255) NOT NULL,
  billing_firstname varchar(255) NOT NULL,
  billing_secondname varchar(255) NOT NULL,
  billing_lastname varchar(255) NOT NULL,
  billing_company varchar(255),
  billing_street_address varchar(255) NOT NULL,
  billing_suburb varchar(255),
  billing_city varchar(255) NOT NULL,
  billing_postcode varchar(10) NOT NULL,
  billing_state varchar(255),
  billing_country varchar(255) NOT NULL,
  billing_country_iso_code_2 char(2) NOT NULL,
  billing_address_format_id int(5) NOT NULL,
  payment_method varchar(255) NOT NULL,
  comments varchar (255),
  last_modified datetime,
  date_purchased datetime,
  orders_status int(5) NOT NULL,
  orders_date_finished datetime,
  currency char(3),
  currency_value decimal(14,6),
  account_type int(1) DEFAULT '0' NOT NULL,
  payment_class varchar(255) NOT NULL,
  shipping_method varchar(255) NOT NULL,
  shipping_class varchar(255) NOT NULL,
  customers_ip varchar(255) NOT NULL,
  language varchar(255) NOT NULL,
  refferers_id varchar(255) NOT NULL,
  conversion_type INT(1) DEFAULT '0' NOT NULL,
  orders_ident_key varchar(255),
  orig_reference text,
  login_reference text,
  PRIMARY KEY (orders_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."orders_products (
  orders_products_id int NOT NULL auto_increment,
  orders_id int NOT NULL,
  products_id int NOT NULL,
  products_model varchar(255),
  products_name varchar(255) NOT NULL,
  products_price decimal(15,4) NOT NULL,
  products_discount_made decimal(4,2) DEFAULT NULL,
  products_shipping_time varchar(255) DEFAULT NULL,
  final_price decimal(15,4) NOT NULL,
  products_tax decimal(7,4) NOT NULL,
  products_quantity int(2) NOT NULL,
  allow_tax int(1) NOT NULL,
  bundle int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (orders_products_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."orders_status (
  orders_status_id int DEFAULT '0' NOT NULL,
  language_id int DEFAULT '1' NOT NULL,
  orders_status_name varchar(255) NOT NULL,
  PRIMARY KEY (orders_status_id, language_id),
  KEY idx_orders_status_name (orders_status_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."shipping_status (
  shipping_status_id int DEFAULT '0' NOT NULL,
  language_id int DEFAULT '1' NOT NULL,
  shipping_status_name varchar(255) NOT NULL,
  shipping_status_image varchar(255) NOT NULL,
  PRIMARY KEY (shipping_status_id, language_id),
  KEY idx_shipping_status_name (shipping_status_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."ship2pay (
  s2p_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  shipment VARCHAR( 100 ) NOT NULL ,
  payments_allowed VARCHAR( 250 ) NOT NULL ,
  zones_id int(11) default '0' not null ,
  status TINYINT NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."orders_status_history (
  orders_status_history_id int NOT NULL auto_increment,
  orders_id int NOT NULL,
  orders_status_id int(5) NOT NULL,
  date_added datetime NOT NULL,
  customer_notified int(1) DEFAULT '0',
  comments text,
  PRIMARY KEY (orders_status_history_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."orders_products_attributes (
  orders_products_attributes_id int NOT NULL auto_increment,
  orders_id int NOT NULL,
  orders_products_id int NOT NULL,
  products_options varchar(255) NOT NULL,
  products_options_values varchar(255) NOT NULL,
  options_values_price decimal(15,4) NOT NULL,
  price_prefix char(1) NOT NULL,
  attributes_model varchar(255) NULL DEFAULT '',
  PRIMARY KEY (orders_products_attributes_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."orders_products_download (
  orders_products_download_id int NOT NULL auto_increment,
  orders_id int NOT NULL default '0',
  orders_products_id int NOT NULL default '0',
  orders_products_filename varchar(255) NOT NULL default '',
  download_maxdays int(2) NOT NULL default '0',
  download_count int(2) NOT NULL default '0',
  PRIMARY KEY  (orders_products_download_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."orders_total (
  orders_total_id int unsigned NOT NULL auto_increment,
  orders_id int NOT NULL,
  title varchar(255) NOT NULL,
  text varchar(255) NOT NULL,
  value decimal(15,4) NOT NULL,
  class varchar(255) NOT NULL,
  sort_order int NOT NULL,
  PRIMARY KEY (orders_total_id),
  KEY idx_orders_total_orders_id (orders_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."orders_recalculate (
  orders_recalculate_id int(11) NOT NULL auto_increment,
  orders_id int(11) NOT NULL default '0',
  n_price decimal(15,4) NOT NULL default '0.0000',
  b_price decimal(15,4) NOT NULL default '0.0000',
  tax decimal(15,4) NOT NULL default '0.0000',
  tax_rate decimal(7,4) NOT NULL default '0.0000',
  class varchar(255) NOT NULL default '',
  PRIMARY KEY  (orders_recalculate_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products (
  products_id int NOT NULL auto_increment,
  products_ean varchar(255),
  products_quantity int(4) NOT NULL,
  products_shippingtime int(4) NOT NULL,
  products_model varchar(255),
  group_permission_0 tinyint(1) NOT NULL,
  group_permission_1 tinyint(1) NOT NULL,
  group_permission_2 tinyint(1) NOT NULL,
  group_permission_3 tinyint(1) NOT NULL,
  products_sort int(4) NOT NULL DEFAULT '0',
  products_image varchar(255),
  products_price decimal(15,4) NOT NULL,
  products_discount_allowed decimal(15,4) DEFAULT '0' NOT NULL,
  products_date_added datetime NOT NULL,
  products_last_modified datetime,
  products_date_available datetime,
  products_weight decimal(5,2) NOT NULL,
  products_status tinyint(1) NOT NULL,
  products_tax_class_id int NOT NULL,
  product_template varchar (64),
  options_template varchar (64),
  manufacturers_id int NULL,
  products_ordered int NOT NULL default '0',
  products_fsk18 int(1) NOT NULL DEFAULT '0',
  products_vpe int(11) NOT NULL,
  products_vpe_status int(1) NOT NULL DEFAULT '0',
  products_vpe_value decimal(15,4) NOT NULL,
  products_startpage int(1) NOT NULL DEFAULT '0',
  products_startpage_sort int(4) NOT NULL DEFAULT '0',
  products_to_xml tinyint(1) NOT NULL DEFAULT '1',
  yml_bid tinyint(1) NOT NULL DEFAULT '0',
  yml_cbid tinyint(1) NOT NULL DEFAULT '0',
  yml_available tinyint(1) NOT NULL DEFAULT '1',
  products_page_url varchar(255),
  stock int(1) default '1',
  products_bundle tinyint(4) NOT NULL DEFAULT '0',
  products_reviews int(1) NOT NULL DEFAULT '1',
  products_search int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (products_id),
  KEY idx_products_date_added (products_date_added)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_bundles (
  bundle_id smallint(6) NOT NULL,
  subproduct_id smallint(6) NOT NULL,
  subproduct_qty tinyint(4) NOT NULL,
  PRIMARY KEY (`bundle_id`,`subproduct_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_attributes (
  products_attributes_id int NOT NULL auto_increment,
  products_id int NOT NULL,
  options_id int NOT NULL,
  options_values_id int NOT NULL,
  options_values_price decimal(15,4) NOT NULL,
  price_prefix char(1) NOT NULL,
  attributes_model varchar(255) NULL,
  attributes_stock int(4) NULL,
  options_values_weight decimal(15,4) NOT NULL,
  weight_prefix char(1) NOT NULL,
  sortorder int(11) NULL,
  PRIMARY KEY  (products_attributes_id),
  KEY PRODUCTS_ID_INDEX (products_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."products_attributes_download (
  products_attributes_id int NOT NULL,
  products_attributes_filename varchar(255) NOT NULL default '',
  products_attributes_maxdays int(2) default '0',
  products_attributes_maxcount int(2) default '0',
  PRIMARY KEY  (products_attributes_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."products_description (
  products_id int NOT NULL auto_increment,
  language_id int NOT NULL default '1',
  products_name varchar(255) NOT NULL default '',
  products_description text,
  products_short_description text,
  products_keywords VARCHAR(255) DEFAULT NULL,
  products_meta_title text NOT NULL,
  products_meta_description text NOT NULL,
  products_meta_keywords text NOT NULL,
  products_url varchar(255) default NULL,
  products_viewed int(5) default '0',
  PRIMARY KEY  (products_id,language_id),
  KEY products_name (products_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_images (
  image_id INT NOT NULL auto_increment,
  products_id INT NOT NULL ,
  image_nr SMALLINT NOT NULL ,
  image_name VARCHAR(254) NOT NULL ,
  `text` text default '',
  PRIMARY KEY ( image_id )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_notifications (
  products_id int NOT NULL,
  customers_id int NOT NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (products_id, customers_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_options (
  products_options_id int NOT NULL default '0',
  language_id int NOT NULL default '1',
  products_options_name varchar(255) NOT NULL default '',
  products_options_length INT( 11 ) DEFAULT '32' NOT NULL ,
  products_options_size INT( 11 ) DEFAULT '32' NOT NULL ,
  products_options_rows INT( 11 ) DEFAULT '4' NOT NULL,
  products_options_type INT( 11 ) NOT NULL,
  PRIMARY KEY  (products_options_id,language_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_options_values (
  products_options_values_id int NOT NULL default '0',
  language_id int NOT NULL default '1',
  products_options_values_name varchar(255) NOT NULL default '',
  products_options_values_description text,
  products_options_values_text varchar(255) NOT NULL default '',
  products_options_values_image varchar(255) NOT NULL default '',
  products_options_values_link varchar(255) NOT NULL default '',
  PRIMARY KEY  (products_options_values_id,language_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_options_values_to_products_options (
  products_options_values_to_products_options_id int NOT NULL auto_increment,
  products_options_id int NOT NULL,
  products_options_values_id int NOT NULL,
  PRIMARY KEY (products_options_values_to_products_options_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."products_graduated_prices (
  products_id int(11) NOT NULL default '0',
  quantity int(11) NOT NULL default '0',
  unitprice decimal(15,4) NOT NULL default '0.0000',
  KEY products_id (products_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."products_to_categories (
  products_id int NOT NULL,
  categories_id int NOT NULL,
  PRIMARY KEY (products_id,categories_id),
  KEY idx_categories_id (categories_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."products_vpe (
  products_vpe_id int(11) NOT NULL default '0',
  language_id int(11) NOT NULL default '0',
  products_vpe_name varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."reviews (
  reviews_id int NOT NULL auto_increment,
  products_id int NOT NULL,
  customers_id int,
  customers_name varchar(255) NOT NULL,
  reviews_rating int(1),
  date_added datetime,
  last_modified datetime,
  reviews_read int(5) NOT NULL default '0',
  status INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (reviews_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."reviews_description (
  reviews_id int NOT NULL,
  languages_id int NOT NULL,
  reviews_text text NOT NULL,
  PRIMARY KEY (reviews_id, languages_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."scart (
  scartid INT(11) NOT NULL AUTO_INCREMENT,
  customers_id INT(11) NOT NULL ,
  dateadded VARCHAR(8) NOT NULL ,
  PRIMARY KEY (scartid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."sessions (
  sesskey varchar(255) NOT NULL,
  expiry int(11) unsigned NOT NULL,
  value text NOT NULL,
  PRIMARY KEY (sesskey)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."specials (
  specials_id int NOT NULL auto_increment,
  products_id int NOT NULL,
  specials_quantity int(4) NOT NULL,
  specials_new_products_price decimal(15,4) NOT NULL,
  specials_date_added datetime,
  specials_last_modified datetime,
  expires_date datetime,
  date_status_change datetime,
  status int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (specials_id),
  KEY idx_products_id (products_id),
  KEY PRODUCTS_ID_INDEX (products_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."special_category (
  special_id int(11) unsigned NOT NULL auto_increment,
  categ_id int(11) unsigned NOT NULL default '0',
  discount decimal(5,2) NOT NULL default '0.00',
  discount_type enum('p','f') NOT NULL default 'f',
  special_date_added datetime NOT NULL default '0000-00-00 00:00:00',
  special_last_modified datetime NOT NULL default '0000-00-00 00:00:00',
  expire_date datetime NOT NULL default '0000-00-00 00:00:00',
  date_status_change datetime NOT NULL default '0000-00-00 00:00:00',
  status tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (special_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."special_product (
  special_product_id int(11) unsigned NOT NULL auto_increment,
  special_id int(11) unsigned NOT NULL default '0',
  product_id int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (special_product_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."featured (
  featured_id int NOT NULL auto_increment,
  products_id int NOT NULL,
  featured_quantity int(4) NOT NULL,
  featured_date_added datetime,
  featured_last_modified datetime,
  expires_date datetime,
  date_status_change datetime,
  status int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (featured_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."tax_class (
  tax_class_id int NOT NULL auto_increment,
  tax_class_title varchar(255) NOT NULL,
  tax_class_description varchar(255) NOT NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (tax_class_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."tax_rates (
  tax_rates_id int NOT NULL auto_increment,
  tax_zone_id int NOT NULL,
  tax_class_id int NOT NULL,
  tax_priority int(5) DEFAULT 1,
  tax_rate decimal(7,4) NOT NULL,
  tax_description varchar(255) NOT NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (tax_rates_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."geo_zones (
  geo_zone_id int NOT NULL auto_increment,
  geo_zone_name varchar(255) NOT NULL,
  geo_zone_description varchar(255) NOT NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (geo_zone_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");


os_db_query("CREATE TABLE ".DB_PREFIX."whos_online (
  customer_id int,
  full_name varchar(255) NOT NULL,
  session_id varchar(255) NOT NULL,
  ip_address varchar(15) NOT NULL,
  time_entry varchar(14) NOT NULL,
  time_last_click varchar(14) NOT NULL,
  last_page_url varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."zones (
  zone_id int NOT NULL auto_increment,
  zone_country_id int NOT NULL,
  zone_code varchar(255) NOT NULL,
  zone_name varchar(255) NOT NULL,
  PRIMARY KEY (zone_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."zones_to_geo_zones (
   association_id int NOT NULL auto_increment,
   zone_country_id int NOT NULL,
   zone_id int NULL,
   geo_zone_id int NULL,
   last_modified datetime NULL,
   date_added datetime NOT NULL,
   PRIMARY KEY (association_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."content_manager (
  content_id int(11) NOT NULL auto_increment,
  categories_id int(11) NOT NULL default '0',
  parent_id int(11) NOT NULL default '0',
  group_ids TEXT,
  languages_id int(11) NOT NULL default '0',
  content_title text NOT NULL,
  content_heading text NOT NULL,
  content_text text NOT NULL,
  content_url text NOT NULL,
  sort_order int(4) NOT NULL default '0',
  file_flag int(1) NOT NULL default '0',
  content_file varchar(255) NOT NULL default '',
  content_status int(1) NOT NULL default '0',
  content_group int(11) NOT NULL,
  content_delete int(1) NOT NULL default '1',
  content_meta_title TEXT,
  content_meta_description TEXT,
  content_meta_keywords TEXT,
  content_page_url varchar(255),
  PRIMARY KEY  (content_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."media_content (
  file_id int(11) NOT NULL auto_increment,
  old_filename text NOT NULL,
  new_filename text NOT NULL,
  file_comment text NOT NULL,
  PRIMARY KEY  (file_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_content (
  content_id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL default '0',
  group_ids TEXT,
  content_name varchar(255) NOT NULL default '',
  content_file varchar(255) NOT NULL,
  content_link text NOT NULL,
  languages_id int(11) NOT NULL default '0',
  content_read int(11) NOT NULL default '0',
  file_comment text NOT NULL,
  PRIMARY KEY  (content_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."module_newsletter (
  newsletter_id int(11) NOT NULL auto_increment,
  title text NOT NULL,
  bc text NOT NULL,
  cc text NOT NULL,
  date datetime default NULL,
  status int(1) NOT NULL default '0',
  body text NOT NULL,
  PRIMARY KEY  (newsletter_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."cm_file_flags (
  file_flag int(11) NOT NULL,
  file_flag_name varchar(255) NOT NULL,
  PRIMARY KEY (file_flag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."payment_moneybookers_currencies (
  mb_currID char(3) NOT NULL default '',
  mb_currName varchar(255) NOT NULL default '',
  PRIMARY KEY  (mb_currID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."payment_moneybookers (
  mb_TRID varchar(255) NOT NULL default '',
  mb_ERRNO smallint(3) unsigned NOT NULL default '0',
  mb_ERRTXT varchar(255) NOT NULL default '',
  mb_DATE datetime NOT NULL default '0000-00-00 00:00:00',
  mb_MBTID bigint(18) unsigned NOT NULL default '0',
  mb_STATUS tinyint(1) NOT NULL default '0',
  mb_ORDERID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (mb_TRID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."payment_moneybookers_countries (
  osc_cID int(11) NOT NULL default '0',
  mb_cID char(3) NOT NULL default '',
  PRIMARY KEY  (osc_cID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."coupon_email_track (
  unique_id int(11) NOT NULL auto_increment,
  coupon_id int(11) NOT NULL default '0',
  customer_id_sent int(11) NOT NULL default '0',
  sent_firstname varchar(255) default NULL,
  sent_lastname varchar(255) default NULL,
  emailed_to varchar(255) default NULL,
  date_sent datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (unique_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."coupon_gv_customer (
  customer_id int(5) NOT NULL default '0',
  amount decimal(8,4) NOT NULL default '0.0000',
  PRIMARY KEY  (customer_id),
  KEY customer_id (customer_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."products_options_images (
  image_id int(11) NOT NULL auto_increment,
  products_options_values_id int(11) NOT NULL,
  image_nr smallint(6) NOT NULL,
  image_name varchar(254) NOT NULL,
  PRIMARY KEY  (image_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

os_db_query("CREATE TABLE ".DB_PREFIX."campaigns_ip (
  user_ip varchar(15) NOT NULL,
  time datetime NOT NULL,
  campaign varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

os_db_query("CREATE TABLE ".DB_PREFIX."coupon_gv_queue (
  unique_id int(5) NOT NULL auto_increment,
  customer_id int(5) NOT NULL default '0',
  order_id int(5) NOT NULL default '0',
  amount decimal(8,4) NOT NULL default '0.0000',
  date_created datetime NOT NULL default '0000-00-00 00:00:00',
  ipaddr varchar(255) NOT NULL default '',
  release_flag char(1) NOT NULL default 'N',
  PRIMARY KEY  (unique_id),
  KEY uid (unique_id,customer_id,order_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."coupon_redeem_track (
  unique_id int(11) NOT NULL auto_increment,
  coupon_id int(11) NOT NULL default '0',
  customer_id int(11) NOT NULL default '0',
  redeem_date datetime NOT NULL default '0000-00-00 00:00:00',
  redeem_ip varchar(255) NOT NULL default '',
  order_id int(11) NOT NULL default '0',
  PRIMARY KEY  (unique_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."coupons (
  coupon_id int(11) NOT NULL auto_increment,
  coupon_type char(1) NOT NULL default 'F',
  coupon_code varchar(255) NOT NULL default '',
  coupon_amount decimal(8,4) NOT NULL default '0.0000',
  coupon_minimum_order decimal(8,4) NOT NULL default '0.0000',
  coupon_start_date datetime NOT NULL default '0000-00-00 00:00:00',
  coupon_expire_date datetime NOT NULL default '0000-00-00 00:00:00',
  uses_per_coupon int(5) NOT NULL default '1',
  uses_per_user int(5) NOT NULL default '0',
  restrict_to_products varchar(255) default NULL,
  restrict_to_categories varchar(255) default NULL,
  restrict_to_customers text,
  coupon_active char(1) NOT NULL default 'Y',
  date_created datetime NOT NULL default '0000-00-00 00:00:00',
  date_modified datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (coupon_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."coupons_description (
  coupon_id int(11) NOT NULL default '0',
  language_id int(11) NOT NULL default '0',
  coupon_name varchar(255) NOT NULL default '',
  coupon_description text,
  KEY coupon_id (coupon_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."payment_qenta (
  q_TRID varchar(255) NOT NULL default '',
  q_DATE datetime NOT NULL default '0000-00-00 00:00:00',
  q_QTID bigint(18) unsigned NOT NULL default '0',
  q_ORDERDESC varchar(255) NOT NULL default '',
  q_STATUS tinyint(1) NOT NULL default '0',
  q_ORDERID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (q_TRID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."personal_offers_by_customers_status_0 (
  price_id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL,
  quantity int(11) default NULL,
  personal_offer decimal(15,4) default NULL,
  PRIMARY KEY  (price_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."personal_offers_by_customers_status_1 (
  price_id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL,
  quantity int(11) default NULL,
  personal_offer decimal(15,4) default NULL,
  PRIMARY KEY  (price_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."personal_offers_by_customers_status_2 (
  price_id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL,
  quantity int(11) default NULL,
  personal_offer decimal(15,4) default NULL,
  PRIMARY KEY  (price_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."personal_offers_by_customers_status_3 (
  price_id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL,
  quantity int(11) default NULL,
  personal_offer decimal(15,4) default NULL,
  PRIMARY KEY  (price_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."post_index (
  Id int(6) unsigned NOT NULL auto_increment,
  tax_zone_id int(1) NOT NULL default '0',
  low int(7) NOT NULL default '0',
  high int(7) NOT NULL default '0',
  mono int(7) NOT NULL default '0',
  PRIMARY KEY  (Id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."address_format (
  address_format_id int NOT NULL auto_increment,
  address_format varchar(255) NOT NULL,
  address_summary varchar(255) NOT NULL,
  PRIMARY KEY (address_format_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."menu (
  `menu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_url` varchar(255) NOT NULL DEFAULT '',
  `menu_class` varchar(255) NOT NULL DEFAULT '',
  `menu_class_icon` varchar(255) NOT NULL DEFAULT '',
  `menu_position` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_status` tinyint(3) NOT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=110 ;");

os_db_query("CREATE TABLE ".DB_PREFIX."menu_group (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_status` tinyint(3) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;");

os_db_query("CREATE TABLE ".DB_PREFIX."menu_lang (
  `lang_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang_title` varchar(255) NOT NULL DEFAULT '',
  `lang_type` tinyint(3) NOT NULL DEFAULT '0',
  `lang_type_id` int(11) NOT NULL DEFAULT '0',
  `lang_lang` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=117 ;");

os_db_query("CREATE TABLE ".DB_PREFIX."sms (
	`id` int(11) not null auto_increment,
	`name` varchar(255) not null default '',
	`login` varchar(255) not null default '',
	`password` varchar(255) not null default '',
	`password_md5` tinyint(1) NOT NULL,
	`api_id` varchar(255) not null default '',
	`api_key` varchar(255) not null default '',
	`title` varchar(255) not null default '',
	`phone` varchar(255) not null default '',
	`status` varchar(255) not null default '',
	`url` text not null default '',
	PRIMARY KEY (`id`, `name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");

os_db_query("CREATE TABLE ".DB_PREFIX."sms_setting (
	`sms_id` int(11) not null auto_increment,
	`sms_status` int(1) not null,
	`sms_default_id` int(1) not null,
	`sms_order_admin` int(1) not null,
	`sms_order` int(1) not null,
	`sms_order_change` int(1) not null,
	`sms_register` int(1) not null,
	`sms_fast_order` int(1) not null,
	PRIMARY KEY (`sms_id`, `sms_default_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
?>