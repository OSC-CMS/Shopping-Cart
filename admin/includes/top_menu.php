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

    defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

    $admin_access_query = os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . $_SESSION['customer_id'] . "'");
    $admin_access = os_db_fetch_array($admin_access_query); 


    $menu_value = array();


    global $p;
    $plug_array = $p->info;
    global $menu_value;

    $menu_value[1]['value'] = BOX_HEADING_CATALOG;    
    $menu_value[1]['url'] = FILENAME_CATEGORIES;	

    $menu_value[2]['value'] = BOX_HEADING_CUSTOMERS;    
    $menu_value[2]['url'] = FILENAME_ORDERS;

    $menu_value[2]['child'] = array (
    array('value'=> BOX_ORDERS, 'url' => FILENAME_ORDERS),
    array('value'=> BOX_CUSTOMERS, 'url' => FILENAME_CUSTOMERS),
    array('value'=> BOX_CUSTOMERS_STATUS, 'url' => FILENAME_CUSTOMERS_STATUS)
    );

    //4 Настройки
    $menu_value[3]['value'] = BOX_HEADING_CONFIGURATION;    
    $menu_value[3]['url'] = FILENAME_CONFIGURATION.'?gID=1';	
    $menu_value[3]['child'] = array (
    array('value'=> BOX_HEADING_CONFIGURATION_MAIN, 'url' => FILENAME_CONFIGURATION.'?gID=1',
    'child' => array( 

    array('value'=> BOX_CONFIGURATION_1, 'url' => FILENAME_CONFIGURATION.'?gID=1'),
    array('value'=> BOX_CONFIGURATION_2,'url' => FILENAME_CONFIGURATION.'?gID=2'),
    array('value'=> BOX_CONFIGURATION_3, 'url' => FILENAME_CONFIGURATION.'?gID=3'),
    array('value'=> BOX_CONFIGURATION_4, 'url' => FILENAME_CONFIGURATION.'?gID=4'),
    array('value'=> BOX_CONFIGURATION_5, 'url' => FILENAME_CONFIGURATION.'?gID=5'),
    array('value'=> BOX_CONFIGURATION_7, 'url' => FILENAME_CONFIGURATION.'?gID=7'),
    array('value'=> BOX_CONFIGURATION_8, 'url' => FILENAME_CONFIGURATION.'?gID=8'),
    array('value'=> BOX_CONFIGURATION_9, 'url' => FILENAME_CONFIGURATION.'?gID=9'),
    array('value'=> BOX_CONFIGURATION_10, 'url' => FILENAME_CONFIGURATION.'?gID=10'),
    array('value'=> BOX_CONFIGURATION_11, 'url' => FILENAME_CONFIGURATION.'?gID=11'),
    array('value'=> BOX_ERROR_LOG, 'url' => FILENAME_ERROR_LOG),
    array('value'=> BOX_CACHE_FILES, 'url' => FILENAME_CACHE),
    array('value'=> BOX_CONFIGURATION_12, 'url' => FILENAME_CONFIGURATION.'?gID=12'),
    array('value'=> BOX_CONFIGURATION_13, 'url' => FILENAME_CONFIGURATION.'?gID=13'),
    array('value'=> BOX_CONFIGURATION_14, 'url' => FILENAME_CONFIGURATION.'?gID=14'),
    array('value'=> BOX_CONFIGURATION_15, 'url' => FILENAME_CONFIGURATION.'?gID=15'),
    array('value'=> BOX_CONFIGURATION_22, 'url' => FILENAME_CONFIGURATION.'?gID=22'),
    array('value'=> BOX_CONFIGURATION_24, 'url' => FILENAME_CONFIGURATION.'?gID=24')
    )

    //шаблоны


    ),
    array('value'=> BOX_THEMES,'url' => FILENAME_THEMES,  
    'child' =>
    array 
    (
    array('value'=> BOX_THEMES_MENU, 'url' => FILENAME_THEMES),
    array('value'=> BOX_CONFIGURATION_30,'url' => FILENAME_CONFIGURATION.'?gID=30'),
    array('value'=> BOX_TOOLS_EMAIL_MANAGER,'url' => FILENAME_EMAIL_MANAGER),
    array('value'=> BOX_TEXT_THEMES_EDIT, 'url' => FILENAME_THEMES_EDIT)
    )
    ),
    array('value'=> BOX_CONFIGURATION_31, 'url' => FILENAME_CONFIGURATION.'?gID=31'),
    array('value'=> BOX_ORDERS_STATUS, 'url' => FILENAME_ORDERS_STATUS),
    array('value'=> BOX_SHIPPING_STATUS, 'url' => FILENAME_SHIPPING_STATUS),
    array('value'=> BOX_PRODUCTS_VPE, 'url' => FILENAME_PRODUCTS_VPE),
    array('value'=> BOX_CAMPAIGNS, 'url' => FILENAME_CAMPAIGNS),
    array('value'=> BOX_ORDERS_XSELL_GROUP, 'url' => FILENAME_XSELL_GROUPS),
    array('value'=> BOX_CONFIGURATION_23, 'url' => FILENAME_CONFIGURATION.'?gID=23'),
    array('value'=> BOX_CONFIGURATION_27, 'url' => FILENAME_CONFIGURATION.'?gID=27'),
    array('value'=> BOX_CONFIGURATION_17, 'url' => FILENAME_CONFIGURATION.'?gID=17')

    );

    $menu_value[4]['value'] = BOX_HEADING_ADDONS;	
    $menu_value[4]['url'] = FILENAME_MODULES;	
    $menu_value[4]['child'] = array (
    array('value'=> BOX_PAYMENT,'url' => FILENAME_MODULES.'?set=payment'),
    array('value'=> BOX_SHIPPING, 'url' => FILENAME_MODULES.'?set=shipping'),
    array('value'=> BOX_ORDER_TOTAL, 'url' => FILENAME_MODULES.'?set=ordertotal'),
    array('value'=> BOX_MODULES_SHIP2PAY, 'url' => FILENAME_SHIP2PAY),
    array('value'=> BOX_PLUGINS, 'url' => FILENAME_PLUGINS)
    );


    $menu_value[5]['value'] = BOX_HEADING_OTHER;	
    $menu_value[5]['url'] = FILENAME_BACKUP;	
    $menu_value[5]['child'] = array (
    array('value' => BOX_HEADING_TOOLS,'url' => FILENAME_BACKUP,  'child' =>
    array (
    array('value' =>BOX_BACKUP, 'url' => FILENAME_BACKUP),
    array('value' =>BOX_PRODUCT_EXTRA_FIELDS, 'url' => FILENAME_PRODUCTS_EXTRA_FIELDS),
    array('value' =>BOX_HEADING_CUSTOMER_EXTRA_FIELDS, 'url' => FILENAME_EXTRA_FIELDS),
    array('value' =>BOX_CONTENT, 'url' => FILENAME_CONTENT_MANAGER),
    array('value' =>BOX_MODULE_NEWSLETTER, 'url' => FILENAME_MODULE_NEWSLETTER),
    array('value' =>BOX_SERVER_INFO, 'url' => FILENAME_SERVER_INFO),
    array('value' =>BOX_CATALOG_LATEST_NEWS, 'url' => FILENAME_LATEST_NEWS),
    array('value' =>BOX_CATALOG_FAQ, 'url' => FILENAME_FAQ),
    array('value' =>BOX_WHOS_ONLINE, 'url' => FILENAME_WHOS_ONLINE),
    array('value' =>BOX_EASY_POPULATE, 'url' => FILENAME_EASYPOPULATE),
    array('value' =>BOX_IMPORT, 'url' => FILENAME_CSV_BACKEND),
    array('value' =>BOX_CATALOG_QUICK_UPDATES, 'url' => FILENAME_QUICK_UPDATES),
    array('value' =>BOX_TOOLS_RECOVER_CART, 'url' => FILENAME_RECOVER_CART_SALES)	
    )

    ),
    array('value' => BOX_HEADING_LOCALIZATION,'url' => FILENAME_CURRENCIES, 'child' =>
    array (
    array ('value' => BOX_LANGUAGES, 'url' => FILENAME_LANGUAGES),
    array ('value' => BOX_CURRENCIES, 'url' => FILENAME_CURRENCIES),
    array ('value' => BOX_COUNTRIES, 'url' => FILENAME_COUNTRIES),
    array ('value' => BOX_ZONES, 'url' => FILENAME_ZONES),
    array ('value' => BOX_GEO_ZONES, 'url' => FILENAME_GEO_ZONES),
    array ('value' => BOX_TAX_CLASSES, 'url' => FILENAME_TAX_CLASSES),
    array ('value' => BOX_TAX_RATES, 'url' => FILENAME_TAX_RATES)
    )
    ),		  

    array('value' => BOX_HEADING_GV_ADMIN,'url' => FILENAME_COUPON_ADMIN, 'child' =>
    array (
    array ('value' => BOX_COUPON_ADMIN,'url' => FILENAME_COUPON_ADMIN),
    array ('value' => BOX_GV_ADMIN_QUEUE, 'url' => FILENAME_GV_QUEUE),
    array ('value' => BOX_GV_ADMIN_MAIL, 'url' => FILENAME_GV_MAIL),
    array ('value' => BOX_GV_ADMIN_SENT, 'url' => FILENAME_GV_SENT),
    )
    ),	  

    array('value' => BOX_HEADING_STATISTICS, 'child' =>
    array (
    array ('value' => BOX_PRODUCTS_VIEWED, 'url' => FILENAME_STATS_PRODUCTS_VIEWED),
    array ('value' => BOX_PRODUCTS_PURCHASED, 'url' => FILENAME_STATS_PRODUCTS_PURCHASED),
    array ('value' => BOX_STATS_CUSTOMERS, 'url' => FILENAME_STATS_CUSTOMERS),
    array ('value' => BOX_SALES_REPORT, 'url' => FILENAME_SALES_REPORT),
    array ('value' => BOX_CAMPAIGNS_REPORT2, 'url' => FILENAME_STATS_SALES_REPORT2),
    array ('value' => BOX_CAMPAIGNS_REPORT, 'url' => FILENAME_CAMPAIGNS_REPORT),
    array ('value' => BOX_STATS_STOCK_WARNING, 'url' => FILENAME_STATS_STOCK_WARNING)
    )
    ),	  

    array('value' => BOX_HEADING_AFFILIATE,'url'=> FILENAME_AFFILIATE, 'child' =>
    array (
    array ('value' => BOX_AFFILIATE_CONFIGURATION, 'url' => FILENAME_CONFIGURATION.'?gID=28'),
    array ('value' => BOX_AFFILIATE, 'url' => FILENAME_AFFILIATE),
    array ('value' => BOX_AFFILIATE_BANNERS, 'url' => FILENAME_AFFILIATE_BANNERS),
    array ('value' => BOX_AFFILIATE_CLICKS, 'url' => FILENAME_AFFILIATE_CLICKS),
    array ('value' => BOX_AFFILIATE_CONTACT, 'url' => FILENAME_AFFILIATE_CONTACT),
    array ('value' => BOX_AFFILIATE_PAYMENT, 'url' => FILENAME_AFFILIATE_PAYMENT),
    array ('value' => BOX_AFFILIATE_SALES, 'url' => FILENAME_AFFILIATE_SALES),
    array ('value' => BOX_AFFILIATE_SUMMARY,'url' => FILENAME_AFFILIATE_SUMMARY)
    )
    )
    );

    $menu_value[6]['value'] = BOX_HEADING_HELP;    
    $menu_value[6]['url'] = 'http://osc-cms.com/';	
    //справка
    $menu_value[6]['child'] =  array(
    array('value' => BOX_HELP, 'url' => 'http://osc-cms.com/docs'),
    array('value' => BOX_SUPPORT_SITE, 'url' => 'http://osc-cms.com'),
    array('value' => BOX_THEMES_URL, 'url' => 'http://osc-cms.com/themes'),
    array('value' => BOX_SUPPORT_FORUM, 'url' => 'http://osc-cms.com/forum')
    );	

    //6 справка
    $top_menu['HELP'] = array('url' => 'http://osc-cms.com/docs');

    $menu_value[1]['child'] = 
    array(
    array('value'=> BOX_CATEGORIES, 'url' => FILENAME_CATEGORIES),
    array('value'=> BOX_ATTRIBUTES, 'url' => FILENAME_PRODUCTS_OPTIONS,
    'child' => array(
    array('value'=> BOX_PRODUCTS_OPTIONS, 'url' => FILENAME_PRODUCTS_OPTIONS),
    array('value'=> BOX_PRODUCTS_ATTRIBUTES, 'url' => FILENAME_PRODUCTS_ATTRIBUTES),
    array('value'=> BOX_ATTRIBUTES_MANAGER, 'url' => FILENAME_NEW_ATTRIBUTES)
    )
    ));

    //1 каталог

    $menu_value[1]['child'][] = array('value'=> BOX_MANUFACTURERS, 'url' => FILENAME_MANUFACTURERS);
    $menu_value[1]['child'][] = array('value'=> BOX_REVIEWS, 'url' => FILENAME_REVIEWS);
    $menu_value[1]['child'][] = array('value'=> BOX_SPECIALS, 'url' => FILENAME_SPECIALS);
    $menu_value[1]['child'][] = array('value'=> BOX_FEATURED, 'url' => FILENAME_FEATURED);
    $menu_value[1]['child'][] = array('value'=> BOX_PRODUCTS_EXPECTED, 'url'=> FILENAME_PRODUCTS_EXPECTED);
    $menu_value[1]['child'][] = array('value' => BOX_HEADING_ARTICLES, 'url' => FILENAME_ARTICLES,

    'child' =>
    array (
    array ('value' => BOX_TOPICS_ARTICLES, 'url' => FILENAME_ARTICLES),
    array ('value' => BOX_ARTICLES_CONFIG, 'url' => FILENAME_ARTICLES_CONFIG),
    array ('value' => BOX_ARTICLES_AUTHORS, 'url' => FILENAME_AUTHORS),
    array ('value' => BOX_ARTICLES_XSELL, 'url' => FILENAME_ARTICLES_XSELL)
    )
    );

     //добавление элемента меню в Дополнения-> Плагины -> $title
    function add_plug_menu($title, $url)
    {
        global $p;
        global $menu_value;

        if ($p->info[$p->name]['status'] ==1 )
        {
            $p->lang();

            if ( isset($p->lang[$p->name][$title]) )
            {
                $lang = $p->lang[$p->name][$title];
            }
            else
            {
                $lang = $title;
            }

            $menu_value[4]['child'][4]['child'][] = array('value'=>$lang, 'url'=>$url);

         /*   if ( isset($menu_value[4]['child'][4]['child']) )
            {
                $menu_value[4]['child'][4]['child'][] = array('value'=>$lang, 'url'=>$url);
            }
            else
            {
                $menu_value[4]['child'][4]['child'] = array();
                $menu_value[4]['child'][4]['child'][] = array('value'=>$lang, 'url'=>$url);
            }*/
        }

    }

    do_action('admin_menu');

    $menu_value = apply_filter('admin_menu', $menu_value);
    
  ///  print_r($menu_value);

    if (SET_WHOS_ONLINE == "false")
    {
        remove_action('admin_menu', 'WHOS_ONLINE');
    }

    global $os_remove_action;

    if ($messageStack->size > 0) 
    {
        echo $messageStack->output();
    }
    $this->crutch();

_e("
<script type='text/javascript'>
$(function(){
	$('ul.dropdown li').hover(function(){
		$(this).addClass('hover');
		$('ul:first',this).css('visibility', 'visible');
	}, function(){
		$(this).removeClass('hover');
		$('ul:first',this).css('visibility', 'hidden');
	});
	$('ul.dropdown li ul li:has(ul)').find('a:first').append(' &raquo; ');
});
</script>
");

_e('<div class="top-bar"><ul class="dropdown">');

foreach ($menu_value as $val => $mass)
{
	//ссылка по умолчанию для пункта меню
	$url = $mass['url'];

	//создаем первый уровень меню
	_e('<li class="first-child"><a class="fist-level" href="'.os_href_link($url, '', 'NONSSL').'">'.$mass['value'].'<span class="arrow"></span></a>');

	//проверка. есть ли 2ой уровень у пункта меню. 
	//проверка пустой ли массиив с элементами 2ого уровня
	if (isset($mass['child']) && !empty($mass['child']))
	{
		_e('<ul>');
		//выборка элементов 2ого уровня.
		foreach ($mass['child'] as $val1 => $mass1)
		{
			if (isset($mass1['child']) && !empty($mass1['child']))
			{
				//если есть 3ий уровень
				$value = $mass1['value'];
				$url = $mass1['url'];

				if (empty($url)) $url = 'index2.php';
				if (!isset($os_remove_action['admin_menu'][$mass1['value']]))
				{
					_e('<li><a href="'.$url.'">'.$value.'</a><ul>');
				}

				foreach ($mass1['child'] as $val2 => $mass2)
				{
					$value = $mass2['value'];
					$url = $mass2['url'];

					if (empty($value)) $value = 'empty';
					if (empty($url)) $url= 'index2.php';

					$_access = explode ('.php', $url);

					if (!isset($os_remove_action['admin_menu'][$mass2['value']]))
					{
						if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access[$_access[0]] == '1'))
						{
							_e('<li><a href="'.$url.'">'.$value.'</a></li>');
						}
					}
				}
				_e('</ul></a></li>');
			}
			else
			{
				//если только 2ой уровень
				$value = $mass1['value'];
				$url = $mass1['url'];

				if (empty($value)) $value = 'empty';
				if (empty($url)) $url = 'index2.php';
				$_access = explode ('.php', $url);
				$_url = false;

				if (substr_count($url, "http:")>=1)
				{ 
					$_url = true;
				}

				if (!isset($os_remove_action['admin_menu'][$mass1['value']]))
				{
					if (($_SESSION['customers_status']['customers_status_id'] == '0') && isset($admin_access[$_access[0]]) &&($admin_access[$_access[0]] == '1') or $_url)
					{
						_e('<li><a href="'.$url.'">'.$value.'</a></li>');
					}
				}
			}
		}
		_e('</ul></a>');
	}
	_e(' </li>');
}
_e('</ul>');

if ( is_file( dir_path('catalog').'VERSION' ) )
{
    $_version = @ file_get_contents (dir_path('catalog').'VERSION');
	$_var_array = explode('/', $_version);
	//ревизия
	$_rev = $_var_array[2];
	//версия
    $_version = $_var_array[1];;
}
else
{
    $_version = '3.0.0';
}
?>
<ul class="osc-version">
    <li><img src="images/favicon.png" style="float:left" border="0" /><a href="http://osc-cms.com/" target="_blank" class="version" title="<?php echo $_rev;?>">OSC-CMS <?php echo $_version; ?></a></li>
</ul>

<ul class="admin-links">
	<li><a href="<?php echo os_href_link(FILENAME_DEFAULT, '', 'NONSSL'); ?>"><?php echo TEXT_HEADER_DEFAULT; ?></a></li>
	<li><a href="../index.php" target="_blank"><?php echo TEXT_HEADER_SHOP; ?></a></li>
	<li><a href="../logoff.php"><?php echo BOX_HEADING_LOGOFF; ?></a></li>
</ul>
<!---
<div class="lang-menu">
<?php $this->lang_menu();?>
</div>
--->
</div>

<div class="wrap">