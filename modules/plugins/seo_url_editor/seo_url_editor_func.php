<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

// Категории товаров
function getSEOCategoriesUrl()
{
	$urlQuery = os_db_query("
		SELECT 
			* 
		FROM 
			".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd 
		WHERE
			c.categories_id = cd.categories_id AND 
			cd.language_id = '".(int) $_SESSION['languages_id']."' 
		ORDER BY 
			c.categories_id ASC
	");

	return $urlQuery;
}

// Товары
function getSEOProductsUrl()
{
	$urlQuery = os_db_query("
		SELECT 
			* 
		FROM 
			".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
		WHERE
			p.products_id = pd.products_id AND 
			pd.language_id = '".(int)$_SESSION['languages_id']."'
		ORDER BY 
			p.products_id ASC
	");

	return $urlQuery;
}

// Новости
function getSEONewsUrl()
{
	$urlQuery = os_db_query("
		SELECT 
			* 
		FROM 
			".TABLE_LATEST_NEWS." 
		WHERE
			language = '".(int)$_SESSION['languages_id']."'
		ORDER BY 
			news_id ASC
	");

	return $urlQuery;
}

// Страницы
function getSEOPagesUrl()
{
	$urlQuery = os_db_query("
		SELECT 
			* 
		FROM 
			".TABLE_CONTENT_MANAGER." 
		ORDER BY 
			content_id ASC
	");

	return $urlQuery;
}

// Категории статей
function getSEOTopicsUrl()
{
	$urlQuery = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_TOPICS." t, ".TABLE_TOPICS_DESCRIPTION." td 
		WHERE 
			t.topics_id = td.topics_id AND 
			language_id = '".(int)$_SESSION['languages_id']."' 
		ORDER BY 
			t.topics_id 
	");

	return $urlQuery;
}

// Статьи
function getSEOArticlesUrl()
{
	$urlQuery = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_ARTICLES." a, ".TABLE_ARTICLES_DESCRIPTION." ad 
		WHERE 
			a.articles_id = ad.articles_id AND 
			language_id = '".(int)$_SESSION['languages_id']."' 
		ORDER BY 
			a.articles_id 
	");

	return $urlQuery;
}

// Вопросы и ответы
function getSEOFaqUrl()
{
	$urlQuery = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_FAQ." 
		ORDER BY 
			faq_id 
	");

	return $urlQuery;
}

// Чистим ЧПУ
function cleanSEOUrl($url)
{
	$url = os_cleanName($url);
	$url = preg_replace('~[/!;$,«»№":*^%#@\[\]&{}]+~s','-', $url);
	$url = preg_replace('~[--]+~s','-', $url);
	$url = trim($url);
	$url = strtr($url, 'ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮЁABCDEFGHIKLMNOPQRSTVXYZWUJ', 'йцукенгшщзхъфывапролджэячсмитьбюёabcdefghiklmnopqrstvxyzwuj');

	if ($url[ strlen($url)-1]  == '-')
	{
		$url = substr($url, 0,strlen($url)-1);
	}

	return $url;
}

// Проверочка ЧПУ
function getSEOAllUrls()
{
	$all_seo_url = array();
	$errors = array();

	$p_query = os_db_query("select categories_id, categories_url from ".DB_PREFIX."categories where categories_status = 1 and categories_url IS NOT NULL and categories_url <> ''");
	while ($val = os_db_fetch_array($p_query,false))
	{
		if (!isset($all_seo_url[$val['categories_url']]))
			$all_seo_url[$val['categories_url']] = 0;
		else
			$errors[] = $val['categories_url'];
	} 

	$p_query = os_db_query("select faq_id, faq_page_url from ".DB_PREFIX."faq where status = 1 and faq_page_url IS NOT NULL and faq_page_url <> ''");
	while ($val = os_db_fetch_array($p_query,false))
	{
		if (!isset($all_seo_url[$val['faq_page_url']]))
			$all_seo_url[$val['faq_page_url']] = 0;
		else
			$errors[] = $val['faq_page_url'];
	}

	$p_query = os_db_query("select content_id, content_page_url from ".DB_PREFIX."content_manager where content_page_url <> '' and content_page_url IS NOT NULL");
	while ($val = os_db_fetch_array($p_query,false))
	{
		if (!isset($all_seo_url[$val['content_page_url']]))
			$all_seo_url[$val['content_page_url']] = 0;
		else
			$errors[] = $val['content_page_url'];
	}

	$p_query = os_db_query("select news_id, news_page_url from ".DB_PREFIX."latest_news where news_page_url <> '' and news_page_url IS NOT NULL");
	while ($val = os_db_fetch_array($p_query,false))
	{
		if (!isset($all_seo_url[$val['news_page_url']]))
			$all_seo_url[$val['news_page_url']] = 0;
		else
			$errors[] = $val['news_page_url'];
	}

	$p_query = os_db_query("select topics_id, topics_page_url from ".DB_PREFIX."topics where topics_page_url <> '' and topics_page_url IS NOT NULL");
	while ($val = os_db_fetch_array($p_query,false))
	{
		if (!isset($all_seo_url[$val['topics_page_url']]))
			$all_seo_url[$val['topics_page_url']] = 0;
		else
			$errors[] = $val['topics_page_url'];
	}

	$p_query = os_db_query("select articles_id, articles_page_url from ".DB_PREFIX."articles where articles_page_url <> '' and articles_page_url IS NOT NULL");
	while ($val = os_db_fetch_array($p_query,false))
	{
		if (!isset($all_seo_url[$val['articles_page_url']]))
			$all_seo_url[$val['articles_page_url']] = 0;
		else
			$errors[] = $val['articles_page_url'];
	}

	return array(
		'seo_url' => $all_seo_url,
		'errors' => $errors
	);
}

// Язык
function getSEOLangId($lang = '')
{
	$_lang_code = $lang;

	if (empty($_lang_code)) $_lang_code = 'ru';

	$languages_query_raw = "select languages_id from ".TABLE_LANGUAGES." where code='$_lang_code'";
	$languages_query = os_db_query($languages_query_raw);
	$languages = os_db_fetch_array($languages_query);

	$_lang_id = $languages['languages_id'];

	if (empty($_lang_id)) $_lang_id = 1;

	return $_lang_id;
}

// Генератор ЧПУ категорий товара
function genSEOCategories($lang = '')
{
	$_lang_id = getSEOLangId($lang);
	$_seo_url_array = getSEOAllUrls();
	$seo_url_array = $_seo_url_array['seo_url'];

	//выборка названий всех категорий
	$cat_query = os_db_query("
		SELECT 
			* 
		FROM 
			".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd 
		WHERE 
			c.categories_id = cd.categories_id AND 
			language_id = '$_lang_id'
		");

	$cat_all_value = array();
	$seo_url_old = array();
	while ($cat_value = os_db_fetch_array($cat_query,false))  
	{
		$cat_all_value[ $cat_value['categories_id'] ] = array(
			'categories_name' => $cat_value['categories_name'],
			'categories_url' => $cat_value['categories_url']
		);

		$seo_url_old[$cat_value['categories_id']] = $cat_value['categories_url'];
	} 

	if (!empty($cat_all_value))
	{
		foreach ($cat_all_value as $cat_id => $cat_value)
		{
			$seo_url = $cat_all_value[ $cat_id ]['categories_name'];
			$seo_url = cleanSEOUrl($seo_url);

			$i = 1;
			$seo_base = $seo_url;

			//обрабатываем повторки в seo_url
			while (isset($seo_url_array[$seo_url]))
			{
				$seo_url = $seo_base.'-'.$i;
				$i++;
			}

			$seo_url_array[$seo_url] = '0';
			$seo_url = $seo_url.'.html';
			$cat_all_value[ $cat_id ]['categories_url'] = $seo_url;
		}
	}	   

	$table = '<table class="plugin-table">';
	if (!empty($cat_all_value))
	{
		foreach ($cat_all_value as $cat_id => $cat_value)
		{
			$table .= '<tr>';
			$table .= '<td class="br" align="center" width="5%">'.$cat_id.'</td>';
			$table .= '<td class="br" width="40%">'.$cat_value['categories_name'].'</td>';
			$table .= '<td class="br" width="40%">'.$cat_value['categories_url'].'</td>';

			if ($seo_url_old[$cat_id] != $cat_value['categories_url'])
			{
				os_db_query(" UPDATE ".DB_PREFIX."categories SET categories_url = '".$cat_value['categories_url']."' WHERE categories_id='".$cat_id."'");
				$table .= '<td width="15%" style="color:green;">Обновлен</td>';
			}
			else
				$table .= '<td width="15%" style="color:red;">Не обновлен</td>';

			$table .= '</tr>';
		}
	}
	$table .= '</table>';	

	set_all_cache();
	return $table;
}

// Генератор ЧПУ товара
function genSEOProducts($lang = '')
{
	$_lang_id = getSEOLangId($lang);
	$_seo_url_array = getSEOAllUrls();
	$seo_url_array = $_seo_url_array['seo_url'];

	$products = os_db_query("
		SELECT 
			p.products_id, pd.products_name, p.products_page_url 
		FROM
			".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
		WHERE 
			p.products_id = pd.products_id AND p.products_status = '1' and pd.language_id = '$_lang_id'
		ORDER BY 
			p.products_id
	");

	$table = '<table class="plugin-table">';
	if (os_db_num_rows($products,false)) 
	{
		$products_all_value = array(); 
		$seo_url_old = array();
		while ($products_value = os_db_fetch_array($products,false))  
		{
			$products_all_value[$products_value['products_id']]= array(
				'products_name' => $products_value['products_name'],
				'products_page_url' => $products_value['products_page_url']
			);
			$seo_url_old[$products_value['products_id']] = $products_value['products_page_url'];
		} 

		foreach ($products_all_value as $products_id => $products_value)
		{
			$seo_url = $products_all_value[$products_id]['products_name'];
			$seo_url = cleanSEOUrl($seo_url);

			$i = 1;
			$seo_base = $seo_url;

			while (isset($seo_url_array[$seo_url]))
			{
				$seo_url = $seo_base.'-'.$i;
				$i++;
			}

			$seo_url_array[$seo_url] = '0';
			$seo_url = $seo_url.'.html';
			$products_all_value[$products_id]['products_page_url'] = $seo_url;
		}

		foreach ($products_all_value as $products_id => $products_value)
		{
			$table .= '<tr>';
			$table .= '<td width="5%" class="br" align="center">'.$products_id.'</td>';
			$table .= '<td width="40%" class="br">'.$products_value['products_name'].'</td>';
			$table .= '<td width="40%" class="br">'.$products_value['products_page_url'].'</td>';

			if ($seo_url_old[$products_id] != $products_value['products_page_url'])
			{
				os_db_query(" UPDATE ".DB_PREFIX."products SET products_page_url = '".$products_value['products_page_url']."' WHERE products_id='".$products_id."'");
				$table .= '<td width="15%" style="color:green;">Обновлен</td>';
			}
			else
				$table .= '<td width="15%" style="color:red;">Не обновлен</td>';

			$table .= '</tr>';
		}
	}	
	$table .= '</table>';	

	set_all_cache();
	return $table;
}

// Генератор ЧПУ новостей
function genSEONews()
{
	$_seo_url_array = getSEOAllUrls();
	$seo_url_array = $_seo_url_array['seo_url'];

	$news = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_LATEST_NEWS." 
		ORDER BY 
			news_id 
	");

	$table = '<table class="plugin-table">';
	if (os_db_num_rows($news,false)) 
	{
		$news_all_value = array(); 
		$seo_url_old = array();
		while ($products_value = os_db_fetch_array($news,false))  
		{
			$news_all_value[$products_value['news_id']]= array(
				'headline' => $products_value['headline'],
				'news_page_url' => $products_value['news_page_url']
			);
			$seo_url_old[$products_value['news_id']] = $products_value['news_page_url'];
		} 

		foreach ($news_all_value as $news_id => $products_value)
		{
			$seo_url = $news_all_value[$news_id]['headline'];
			$seo_url = cleanSEOUrl($seo_url);

			$i = 1;
			$seo_base = $seo_url;

			while (isset($seo_url_array[$seo_url]))
			{
				$seo_url = $seo_base.'-'.$i;
				$i++;
			}

			$seo_url_array[$seo_url] = '0';
			$seo_url = $seo_url.'.html';
			$news_all_value[$news_id]['news_page_url'] = $seo_url;
		}

		foreach ($news_all_value as $news_id => $news_value)
		{
			$table .= '<tr>';
			$table .= '<td width="5%" class="br" align="center">'.$news_id.'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['headline'].'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['news_page_url'].'</td>';

			if ($seo_url_old[$news_id] != $news_value['news_page_url'])
			{
				os_db_query(" UPDATE ".TABLE_LATEST_NEWS." SET news_page_url = '".$news_value['news_page_url']."' WHERE news_id='".$news_id."'");
				$table .= '<td width="15%" style="color:green;">Обновлен</td>';
			}
			else
				$table .= '<td width="15%" style="color:red;">Не обновлен</td>';

			$table .= '</tr>';
		}
	}	
	$table .= '</table>';	

	set_all_cache();
	return $table;
}

// Генератор ЧПУ страниц
function genSEOPages()
{
	$_seo_url_array = getSEOAllUrls();
	$seo_url_array = $_seo_url_array['seo_url'];

	$news = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_CONTENT_MANAGER." 
		ORDER BY 
			content_id 
	");

	$table = '<table class="plugin-table">';
	if (os_db_num_rows($news,false)) 
	{
		$news_all_value = array(); 
		$seo_url_old = array();
		while ($content_value = os_db_fetch_array($news,false))  
		{
			$news_all_value[$content_value['content_id']]= array(
				'content_title' => $content_value['content_title'],
				'content_page_url' => $content_value['content_page_url']
			);
			$seo_url_old[$content_value['content_id']] = $content_value['content_page_url'];
		} 

		foreach ($news_all_value as $content_id => $content_value)
		{
			$seo_url = $news_all_value[$content_id]['content_title'];
			$seo_url = cleanSEOUrl($seo_url);

			$i = 1;
			$seo_base = $seo_url;

			while (isset($seo_url_array[$seo_url]))
			{
				$seo_url = $seo_base.'-'.$i;
				$i++;
			}

			$seo_url_array[$seo_url] = '0';
			$seo_url = $seo_url.'.html';
			$news_all_value[$content_id]['content_page_url'] = $seo_url;
		}

		foreach ($news_all_value as $content_id => $news_value)
		{
			$table .= '<tr>';
			$table .= '<td width="5%" class="br" align="center">'.$content_id.'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['content_title'].'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['content_page_url'].'</td>';

			if ($seo_url_old[$content_id] != $news_value['content_page_url'])
			{
				os_db_query(" UPDATE ".TABLE_CONTENT_MANAGER." SET content_page_url = '".$news_value['content_page_url']."' WHERE content_id='".$content_id."'");
				$table .= '<td width="15%" style="color:green;">Обновлен</td>';
			}
			else
				$table .= '<td width="15%" style="color:red;">Не обновлен</td>';

			$table .= '</tr>';
		}
	}	
	$table .= '</table>';	

	set_all_cache();
	return $table;
}

// Генератор ЧПУ категорий статей
function genSEOTopics($lang)
{
	$_lang_id = getSEOLangId($lang);
	$_seo_url_array = getSEOAllUrls();
	$seo_url_array = $_seo_url_array['seo_url'];

	$news = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_TOPICS." t, ".TABLE_TOPICS_DESCRIPTION." td 
		WHERE 
			t.topics_id = td.topics_id AND 
			language_id = '$_lang_id'
		ORDER BY 
			t.topics_id 
	");

	$table = '<table class="plugin-table">';
	if (os_db_num_rows($news,false)) 
	{
		$news_all_value = array(); 
		$seo_url_old = array();
		while ($content_value = os_db_fetch_array($news,false))  
		{
			$news_all_value[$content_value['topics_id']]= array(
				'topics_name' => $content_value['topics_name'],
				'topics_page_url' => $content_value['topics_page_url']
			);
			$seo_url_old[$content_value['topics_id']] = $content_value['topics_page_url'];
		} 

		foreach ($news_all_value as $topics_id => $content_value)
		{
			$seo_url = $news_all_value[$topics_id]['topics_name'];
			$seo_url = cleanSEOUrl($seo_url);

			$i = 1;
			$seo_base = $seo_url;

			while (isset($seo_url_array[$seo_url]))
			{
				$seo_url = $seo_base.'-'.$i;
				$i++;
			}

			$seo_url_array[$seo_url] = '0';
			$seo_url = $seo_url.'.html';
			$news_all_value[$topics_id]['topics_page_url'] = $seo_url;
		}

		foreach ($news_all_value as $topics_id => $news_value)
		{
			$table .= '<tr>';
			$table .= '<td width="5%" class="br" align="center">'.$topics_id.'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['topics_name'].'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['topics_page_url'].'</td>';

			if ($seo_url_old[$topics_id] != $news_value['topics_page_url'])
			{
				os_db_query(" UPDATE ".TABLE_TOPICS." SET topics_page_url = '".$news_value['topics_page_url']."' WHERE topics_id='".$topics_id."'");
				$table .= '<td width="15%" style="color:green;">Обновлен</td>';
			}
			else
				$table .= '<td width="15%" style="color:red;">Не обновлен</td>';

			$table .= '</tr>';
		}
	}	
	$table .= '</table>';	

	set_all_cache();
	return $table;
}

// Генератор ЧПУ статей
function genSEOArticles($lang)
{
	$_lang_id = getSEOLangId($lang);
	$_seo_url_array = getSEOAllUrls();
	$seo_url_array = $_seo_url_array['seo_url'];

	$news = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_ARTICLES." a, ".TABLE_ARTICLES_DESCRIPTION." ad 
		WHERE 
			a.articles_id = ad.articles_id AND 
			language_id = '$_lang_id'
		ORDER BY 
			a.articles_id 
	");

	$table = '<table class="plugin-table">';
	if (os_db_num_rows($news,false)) 
	{
		$news_all_value = array(); 
		$seo_url_old = array();
		while ($content_value = os_db_fetch_array($news,false))  
		{
			$news_all_value[$content_value['articles_id']]= array(
				'articles_name' => $content_value['articles_name'],
				'articles_page_url' => $content_value['articles_page_url']
			);
			$seo_url_old[$content_value['articles_id']] = $content_value['articles_page_url'];
		} 

		foreach ($news_all_value as $articles_id => $content_value)
		{
			$seo_url = $news_all_value[$articles_id]['articles_name'];
			$seo_url = cleanSEOUrl($seo_url);

			$i = 1;
			$seo_base = $seo_url;

			while (isset($seo_url_array[$seo_url]))
			{
				$seo_url = $seo_base.'-'.$i;
				$i++;
			}

			$seo_url_array[$seo_url] = '0';
			$seo_url = $seo_url.'.html';
			$news_all_value[$articles_id]['articles_page_url'] = $seo_url;
		}

		foreach ($news_all_value as $articles_id => $news_value)
		{
			$table .= '<tr>';
			$table .= '<td width="5%" class="br" align="center">'.$articles_id.'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['articles_name'].'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['articles_page_url'].'</td>';

			if ($seo_url_old[$articles_id] != $news_value['articles_page_url'])
			{
				os_db_query("UPDATE ".TABLE_ARTICLES." SET articles_page_url = '".$news_value['articles_page_url']."' WHERE articles_id='".$articles_id."'");
				$table .= '<td width="15%" style="color:green;">Обновлен</td>';
			}
			else
				$table .= '<td width="15%" style="color:red;">Не обновлен</td>';

			$table .= '</tr>';
		}
	}	
	$table .= '</table>';	

	set_all_cache();
	return $table;
}

// Генератор ЧПУ F.A.Q
function genSEOFaq()
{
	$_seo_url_array = getSEOAllUrls();
	$seo_url_array = $_seo_url_array['seo_url'];

	$news = os_db_query("
		SELECT 
			* 
		FROM
			".TABLE_FAQ." 
		ORDER BY 
			faq_id 
	");

	$table = '<table class="plugin-table">';
	if (os_db_num_rows($news,false)) 
	{
		$news_all_value = array(); 
		$seo_url_old = array();
		while ($content_value = os_db_fetch_array($news,false))  
		{
			$news_all_value[$content_value['faq_id']]= array(
				'question' => $content_value['question'],
				'faq_page_url' => $content_value['faq_page_url']
			);
			$seo_url_old[$content_value['faq_id']] = $content_value['faq_page_url'];
		} 

		foreach ($news_all_value as $faq_id => $content_value)
		{
			$seo_url = $news_all_value[$faq_id]['question'];
			$seo_url = cleanSEOUrl($seo_url);

			$i = 1;
			$seo_base = $seo_url;

			while (isset($seo_url_array[$seo_url]))
			{
				$seo_url = $seo_base.'-'.$i;
				$i++;
			}

			$seo_url_array[$seo_url] = '0';
			$seo_url = $seo_url.'.html';
			$news_all_value[$faq_id]['faq_page_url'] = $seo_url;
		}

		foreach ($news_all_value as $faq_id => $news_value)
		{
			$table .= '<tr>';
			$table .= '<td width="5%" class="br" align="center">'.$faq_id.'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['question'].'</td>';
			$table .= '<td width="40%" class="br">'.$news_value['faq_page_url'].'</td>';

			if ($seo_url_old[$faq_id] != $news_value['faq_page_url'])
			{
				os_db_query("UPDATE ".TABLE_FAQ." SET faq_page_url = '".$news_value['faq_page_url']."' WHERE faq_id='".$faq_id."'");
				$table .= '<td width="15%" style="color:green;">Обновлен</td>';
			}
			else
				$table .= '<td width="15%" style="color:red;">Не обновлен</td>';

			$table .= '</tr>';
		}
	}	
	$table .= '</table>';	

	set_all_cache();
	return $table;
}













?>