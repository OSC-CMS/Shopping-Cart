<?php
/*
	Plugin Name: ЧПУ генератор
	Plugin URI: http://osc-cms.com/
	Description: Генерирует ЧПУ URL ссылку для каждого товара. Проверяет повторки.
	Version: 1.8
	Author: Матецкий Евгений
	Author URI: http://osc-cms.com
	Plugin Group: Products
*/

  defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

  add_action('page_admin', 'page_admin_seo_url');
  add_action('page_admin', 'page_admin_seo_url_cat');
  add_action('page_admin', 'page_admin_seo_url_products_clean');
  add_action('page_admin', 'page_admin_seo_url_cat_clean');
  
  include(dirname(__FILE__).'/func.php');
  
  function page_admin_seo_url()
  {
      global $os_action_plug;

	  $_lang_id =  get_lang_id();
	  
	  $products_all_value = array(); 
      
      $_seo_url_array = get_all_seo_url();
	  $seo_url_array = $_seo_url_array['seo_url'];

	  $products_query = "SELECT p.products_id, pd.products_name, p.products_page_url FROM
	                                         ".TABLE_PRODUCTS." p,
	                                         ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE
	                                         p.products_id=pd.products_id 
	                                         and p.products_status = '1' and pd.language_id = '$_lang_id'
	                                         order by p.products_id ";
	 $products = os_db_query($products_query);							 
	
	  echo '<table border="0" width="100%" cellspacing="2" cellpadding="2">';				
       if (os_db_num_rows($products,false)) 
	   {
	       $seo_url_old = array();
		   
		   while ($products_value = os_db_fetch_array($products,false))  
           {
		       $products_all_value[$products_value['products_id']]= array('products_name'=>$products_value['products_name'], 'products_page_url'=>$products_value['products_page_url']);
	           $seo_url_old[$products_value['products_id']] = $products_value['products_page_url'];
           } 

		   foreach ($products_all_value as $products_id => $products_value)
		   {   
			   $seo_url = $products_all_value[$products_id]['products_name'];
			   $seo_url = os_cleanName($seo_url);
			   $seo_url = preg_replace('~[/!;$,«»№":*^%#@\[\]&{}]+~s','-',$seo_url);
			   $seo_url = preg_replace('~[--]+~s','-',$seo_url);
			   $seo_url = trim($seo_url);
               $seo_url = strtr( $seo_url, 'ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮЁABCDEFGHIKLMNOPQRSTVXYZWUJ', 'йцукенгшщзхъфывапролджэячсмитьбюёabcdefghiklmnopqrstvxyzwuj' );
			   
			   if ($seo_url[ strlen($seo_url)-1]  == '-')
			   {
			      $seo_url =  substr($seo_url, 0,strlen($seo_url)-1);
			   }
			   
			   $i = 1;
			   $seo_base = $seo_url;
			   
			   //обрабатываем повторки в seo_url
			   while (isset($seo_url_array[$seo_url]))
			   {
			      //echo $i.'<br>';
			
			      $seo_url = $seo_base.'-'.$i;
				 
				  $i++;
			   }
			   
			   $seo_url_array[$seo_url] = '0';
			   $seo_url = $seo_url.'.html';
			   $products_all_value[$products_id]['products_page_url'] = $seo_url;
		   }
		   
		  
		   foreach ($products_all_value as $products_id => $products_value)
		   {
		       $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
			   
			   echo '<tr style="background-color:'.$color.'">';
			   echo '<td align="center" width="25%">'.$products_id.'</td>';
			   echo '<td align="center" width="25%">'.$products_value['products_name'].'</td>';
			   echo '<td align="center" width="25%">'.$products_value['products_page_url'].'</td>';
			  
			   if ($seo_url_old[$products_id] != $products_value['products_page_url'])
			   {
			      os_db_query(" UPDATE ".DB_PREFIX."products SET products_page_url = '".$products_value['products_page_url']."' WHERE products_id='".$products_id."'");
                  echo '<td align="center" width="25%"><font color="green">Обновлен</font></td>';
			   }
			   else
			   {
                  echo '<td align="center" width="25%"><font color="red">Не обновлен</font></td>';			   
			   }
			   echo '</tr>';
		   }
	
	   }	
       echo '</table>';	
	   
	   set_all_cache();
	   
      //os_redirect(os_href_link(FILENAME_PLUGINS, 'module=' . $os_action_plug[__FUNCTION__]));
  }
  
  function get_lang_id()
  {
    $_lang_code = get_option('seo_url_lang');
	  
	  if (empty($_lang_code)) $_lang_code = 'ru';
	  
	  $languages_query_raw = "select languages_id from " . TABLE_LANGUAGES . " where code='$_lang_code'";
      $languages_query = os_db_query($languages_query_raw);
      $languages = os_db_fetch_array($languages_query);
     
	  $_lang_id =  $languages['languages_id'];
	  
	 
      if (empty($_lang_id)) $_lang_id = 1;
	  
	  return $_lang_id;
  }
  //генератор чпу для категорий
  function page_admin_seo_url_cat()
  {
      global $os_action_plug;
	  
	  $_lang_id =  get_lang_id();
	  
	  $products_all_value = array(); 
   
	  //выборка всех существующих ЧПУ ссылок. чтобы не создать повторки
      $_seo_url_array = get_all_seo_url();
	  $seo_url_array = $_seo_url_array['seo_url'];

	  //выборка названий всех категорий
	  $cat_query = os_db_query("SELECT categories_id, categories_name FROM ".DB_PREFIX."categories_description where language_id = '$_lang_id'");
									
    while ($cat_value = os_db_fetch_array($cat_query,false))  
    {
		   $cat_all_value[ $cat_value['categories_id'] ] = array('categories_name' => $cat_value['categories_name']);
    } 
	
    if ( !empty($cat_all_value) )
    {	
	    foreach ($cat_all_value as $cat_id => $cat_value)
	    {   
			   $seo_url = $cat_all_value[ $cat_id ]['categories_name'];
			   $seo_url = os_cleanName($seo_url);
			   $seo_url = preg_replace('~[/!;$,«»№":*^%#@\[\]&{}]+~s','-',$seo_url);
			   $seo_url = preg_replace('~[--]+~s','-',$seo_url);
			   $seo_url = trim($seo_url);
               $seo_url = strtr( $seo_url, 'ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮЁABCDEFGHIKLMNOPQRSTVXYZWUJ', 'йцукенгшщзхъфывапролджэячсмитьбюёabcdefghiklmnopqrstvxyzwuj' );
               
			   if ($seo_url[ strlen($seo_url)-1]  == '-')
			   {
			      $seo_url =  substr($seo_url, 0,strlen($seo_url)-1);
			   }
			   
			   $i = 1;
			   $seo_base = $seo_url;
			   
			   //обрабатываем повторки в seo_url
			   while (isset($seo_url_array[$seo_url]))
			   {
			      //echo $i.'<br>';
			
			      $seo_url = $seo_base.'-'.$i;
				 
				  $i++;
			   }
			   
			   $seo_url_array[$seo_url] = '0';
			   $seo_url = $seo_url.'.html';
			   $cat_all_value[ $cat_id ]['categories_url'] = $seo_url;
	    }
	}	   
	 
 
	//print_r($cat_all_value);
       echo '<table border="0" width="100%" cellspacing="2" cellpadding="2">';				
     
		  if (!empty($cat_all_value))
		  {
		   foreach ($cat_all_value as $cat_id => $cat_value)
		   {
		       $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
			   
			   echo '<tr style="background-color:'.$color.'">';
			   echo '<td align="center" width="25%">'.$cat_id.'</td>';
			   echo '<td align="center" width="25%">'.$cat_value['categories_name'].'</td>';
			   echo '<td align="center" width="25%">'.$cat_value['categories_url'].'</td>';
			  
			  os_db_query(" UPDATE ".DB_PREFIX."categories SET categories_url = '".$cat_value['categories_url']."' WHERE categories_id='".$cat_id."';");
			  
			  /* if ($seo_url_old[$products_id] != $products_value['products_page_url'])
			   {
			      os_db_query(" UPDATE ".DB_PREFIX."products SET products_page_url = '".$products_value['products_page_url']."' WHERE products_id='".$products_id."';");
                  echo '<td align="center" width="25%"><font color="green">Обновлен</font></td>';
			   }
			   else
			   {
                  echo '<td align="center" width="25%"><font color="red">Не обновлен</font></td>';			   
			   }
			   */
			   echo '</tr>';
		   }
		   }
	
	
       echo '</table>';	
	   
	   set_all_cache();
	   
      //os_redirect(os_href_link(FILENAME_PLUGINS, 'module=' . $os_action_plug[__FUNCTION__]));
	  
  }
  
  //страница с удалением ЧПУ товаров
  function page_admin_seo_url_products_clean()
  {
      global $messageStack;
	  global $os_action_plug;
	  
      os_db_query(" UPDATE ".DB_PREFIX."products SET products_page_url = ''");
      set_all_cache();
	  $messageStack->add_session('ЧПУ товаров успешно удалено.', 'success');
	  
	  os_redirect(os_href_link(FILENAME_PLUGINS, 'module=' . $os_action_plug[__FUNCTION__]));
  }  
  
  //страница с удалением ЧПУ категорий
  function page_admin_seo_url_cat_clean()
  {
      global $messageStack;
	  global $os_action_plug;
	  
      os_db_query(" UPDATE ".DB_PREFIX."categories SET categories_url = ''");
      set_all_cache();
	  $messageStack->add_session('ЧПУ категорий успешно удалено.', 'success');
	  
	  os_redirect(os_href_link(FILENAME_PLUGINS, 'module=' . $os_action_plug[__FUNCTION__]));
  }
  
  function seo_url_product_generator_readonly()
  {
    _e('<center>'.add_button('page', 'page_admin_seo_url', 'Генератор ЧПУ для товаров' ).'</center>');
    _e('<center>'.add_button('page', 'page_admin_seo_url_cat', 'Генератор ЧПУ для категорий' ).'</center>');
    _e('<hr style="border-color: #ddebeb;border-style: dotted;" />');
    _e('<center>'.add_button('page', 'page_admin_seo_url_products_clean', 'Очистка ЧПУ для товаров' ).'</center>');
    _e('<center>'.add_button('page', 'page_admin_seo_url_cat_clean', 'Очистка ЧПУ для категорий' ).'</center>');
  }
  
  function seo_url_install()
  {
      add_option('seo_url_product_generator', '', 'readonly');
	  
	  
	   $languages_query_raw = "select languages_id, name, code, image, directory, sort_order,status,language_charset from " . TABLE_LANGUAGES . " order by sort_order";
       $languages_query = os_db_query($languages_query_raw);
	   
	   $lang = array();
	   
       while ($languages = os_db_fetch_array($languages_query)) 
       {
             $lang[ $languages['languages_id'] ] = $languages['code'];
	   }
	   
	   if (!empty($lang))
	   { 
	        $i = 1;
	        foreach($lang as $id => $code)
			{
			  if ($i == 1) 
			  {
			     $_array = "array('".$code."'"; 
			  }
			  else 
			  {
			     $_array .= ",'".$code."'";
			  }
			  $i++;
			}
			$_array  .= ")";
	   }
	   
	  add_option('seo_url_lang', $_SESSION['language_code'], 'checkbox', "$_array");
  }
  
  add_action('admin_menu', 'seo_url_admin_menu');

  function seo_url_admin_menu()
  {
        add_plug_menu('Генератор ЧПУ для товаров', 'plugins_page.php?page=page_admin_seo_url');
        add_plug_menu('Генератор ЧПУ для категорий', 'plugins_page.php?page=page_admin_seo_url_cat');
  }
?>