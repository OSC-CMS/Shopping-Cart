<?php
 
 function get_all_seo_url()
  {
     $all_seo_url = array();
	 $errors = array();
	 
	 $p_query = os_db_query("select categories_id, categories_url from ".DB_PREFIX."categories where categories_status = 1 and categories_url IS NOT NULL and categories_url <> ''");
     
	 while ($val = os_db_fetch_array($p_query,false))  
     {
		if (!isset($all_seo_url[$val['categories_url']]))
        {
		   $all_seo_url[$val['categories_url']] = 0;   
		}
		else
		{
		   $errors[] = $val['categories_url'];
		}
     } 
	 
	 $p_query = os_db_query("select faq_id, faq_page_url from ".DB_PREFIX."faq where status = 1 and faq_page_url IS NOT NULL and faq_page_url <> ''");
     
	 while ($val = os_db_fetch_array($p_query,false))  
     {
		if (!isset($all_seo_url[$val['faq_page_url']]))
        {
		   $all_seo_url[$val['faq_page_url']] = 0;   
		}
		else
		{
		   $errors[] = $val['faq_page_url'];
		}     
	} 
	 
	 $p_query = os_db_query("select content_id, content_page_url from ".DB_PREFIX."content_manager where content_page_url <> '' and content_page_url IS NOT NULL");
     
	 while ($val = os_db_fetch_array($p_query,false))  
     {
		if (!isset($all_seo_url[$val['content_page_url']]))
        {
		   $all_seo_url[$val['content_page_url']] = 0;   
		}
		else
		{
		   $errors[] = $val['content_page_url'];
		}    
	} 
	 
	 $p_query = os_db_query("select news_id, news_page_url from ".DB_PREFIX."latest_news where news_page_url <> '' and news_page_url IS NOT NULL");
     
	 while ($val = os_db_fetch_array($p_query,false))  
     {
		if (!isset($all_seo_url[$val['news_page_url']]))
        {
		   $all_seo_url[$val['news_page_url']] = 0;   
		}
		else
		{
		   $errors[] = $val['news_page_url'];
		}       
	 }
	 
	 $p_query = os_db_query("select topics_id, topics_page_url from ".DB_PREFIX."topics where topics_page_url <> '' and topics_page_url IS NOT NULL");
     
	 while ($val = os_db_fetch_array($p_query,false))  
     {
		if (!isset($all_seo_url[$val['topics_page_url']]))
        {
		   $all_seo_url[$val['topics_page_url']] = 0;   
		}
		else
		{
		   $errors[] = $val['topics_page_url'];
		}       
	 } 
	 
	 $p_query = os_db_query("select articles_id, articles_page_url from ".DB_PREFIX."articles where articles_page_url <> '' and articles_page_url IS NOT NULL");
    
   	 while ($val = os_db_fetch_array($p_query,false))  
     {
		if (!isset($all_seo_url[$val['articles_page_url']]))
        {
		   $all_seo_url[$val['articles_page_url']] = 0;   
		}
		else
		{
		   $errors[] = $val['articles_page_url'];
		}       
	 } 
	 
	 return array('seo_url' => $all_seo_url, 'errors' =>$errors);
  }
 
?>