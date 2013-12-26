<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiCache extends CartET
{
	// Объект кэша
	private $cache_type;

	// Кэширование включено
	private $cache_enabled = 1;

	// Метод кэширования
	private $cache_method = 'files';

	// количество запросов из кэша
	public $query_count = 0;

	public function __construct()
	{
		$cache_method = 'cache'.$this->cache_method;
		$this->cache_type = $this->$cache_method;
	}

	/**
	 *	Установка кэша
	 */
	public function set($key, $value, $cache_expire = false)
	{
		if (!$this->cache_enabled)
			return false;

		if (!$cache_expire)
		{
			$cache_expire = DB_CACHE_EXPIRE;
		}

		return $this->cache_type->set($key, $value, $cache_expire);
	}

	/**
	 *	Проверка на существование кэша
	 */
	public function has($key)
	{
		if (!$this->cache_enabled)
			return false;

		return $this->cache_type->has($key);
	}

	/**
	 *	Получить кэш
	 */
	public function get($key)
	{
		if (!$this->cache_enabled)
			return false;

		if (!$this->has($key))
			return false;

		$value = $this->cache_type->get($key);

		if (DISPLAY_PAGE_PARSE_TIME == 'true' && $value)
		{
			$this->query_count++;
		}

		return $value;
	}

	/**
	 *	Очистка кэша
	 */
	public function clean($key)
	{
		if (!$this->cache_enabled)
			return false;

		return $this->cache_type->clean($key);
	}

	/**
	 *	Очистка кэша определенной директории
	 */
	public function cleanDir($params)
	{
		if (empty($params))
			return false;

		$dir = $params['type'];

		set_all_cache();

		if ($d = opendir(_CACHE.$dir.'/'))
		{
			while (false !== ($file = readdir($d)))
			{
				if ($file != "." && $file != ".." && $file !=".htaccess")
				{
					os_delete_file(_CACHE.$dir.'/'.$file);
				}
			}
			closedir($d);
		}

		$data = array('msg' => 'Кэш успешно очищен!<br />Директория: '.$dir, 'type' => 'ok');

		return $data;
	}

/*	// урл товара
	public function setProductsUrl()
	{
		$p_query = osDBquery("select products_id, products_page_url from ".DB_PREFIX."products where products_status = 1 and products_page_url IS NOT NULL and products_page_url <> ''");

		$result = '';
		if (os_db_num_rows($p_query, true))
		{
			while ($products = os_db_fetch_array($p_query, true))
			{
				$result[$products['products_id']] = $products['products_page_url'];
			}
		}

		$this->set('url.products_url', $result);
		return true;
	}

	// урл категорий
	public function setCategoriesUrl()
	{
		$p_query = osDBquery("select categories_id, categories_url from ".DB_PREFIX."categories where categories_status = 1 and categories_url IS NOT NULL and categories_url <> ''");

		$result = '';
		if (os_db_num_rows($p_query,true))
		{
			while ($products = os_db_fetch_array($p_query, true))
			{
				$result[$products['categories_id']] = $products['categories_url'];
			}
		}

		$this->set('url.categories_url', $result);
		return true;
	}

	// урл faq
	public function setFaqUrl()
	{
		$p_query = osDBquery("select faq_id, faq_page_url from ".DB_PREFIX."faq where status = 1 and faq_page_url IS NOT NULL and faq_page_url <> ''");

		$result = '';
		if (os_db_num_rows($p_query,true))
		{
			while ($products = os_db_fetch_array($p_query, true))
			{
				$result[$products['faq_id']] = $products['faq_page_url'];
			}
		}

		$this->set('url.faq_url', $result);
		return true;
	}

	// урл инфо. страниц
	public function setContentUrl()
	{
		$p_query = osDBquery("select content_id, content_page_url from ".DB_PREFIX."content_manager where content_page_url <> '' and content_page_url IS NOT NULL");

		$result = '';
		if (os_db_num_rows($p_query,true))
		{
			while ($products = os_db_fetch_array($p_query, true))
			{
				$result[$products['content_id']] = $products['content_page_url'];
			}
		}

		$this->set('url.content_url', $result);
		return true;
	}

	// урл новостей
	public function setNewsUrl()
	{
		$p_query = osDBquery("select news_id, news_page_url from ".DB_PREFIX."latest_news where news_page_url <> '' and news_page_url IS NOT NULL");

		$result = '';
		if (os_db_num_rows($p_query,true))
		{
			while ($products = os_db_fetch_array($p_query, true))
			{
				$result[$products['news_id']] = $products['news_page_url'];
			}
		}

		$this->set('url.news_url', $result);
		return true;
	}

	// урл категорий статей
	public function setTopicsUrl()
	{
		$p_query = osDBquery("select topics_id, topics_page_url from ".DB_PREFIX."topics where topics_page_url <> '' and topics_page_url IS NOT NULL");

		$result = '';
		if (os_db_num_rows($p_query,true))
		{
			while ($products = os_db_fetch_array($p_query, true))
			{
				$result[$products['topics_id']] = $products['topics_page_url'];
			}
		}

		$this->set('url.topics_url', $result);
		return true;
	}

	// урл статей
	public function setArticlesUrl()
	{
		$p_query = osDBquery("select articles_id, articles_page_url from ".DB_PREFIX."articles where articles_page_url <> '' and articles_page_url IS NOT NULL");

		$result = '';
		if (os_db_num_rows($p_query,true))
		{
			while ($products = os_db_fetch_array($p_query, true))
			{
				$result[$products['articles_id']] = $products['articles_page_url'];
			}
		}

		$this->set('url.articles_url', $result);
		return true;
	}

	// разыне данные
	public function setDefault($param = false)
	{
		$result = array();

		$p_query = osDBquery("select * from ".DB_PREFIX."currencies");

		$result['currencies'] = '';

		if (os_db_num_rows($p_query, true))
		{
			while ($value = os_db_fetch_array($p_query, true))
			{
				$code = $value['code'];
				unset($value['code']);
				$result['currencies'][$code] = $value;
			}
		}

		$result['tax_class_id'] = '';
		$p_query = osDBquery("select tax_class_id as class from ".DB_PREFIX."tax_class");

		if (os_db_num_rows($p_query, true))
		{
			while ($value = os_db_fetch_array($p_query, true))
			{
				$result['tax_class_id'][$value['class']] = '';
			}
		}

		$result['shipping_status'] = '';
		// shipping_status
		$p_query = osDBquery("SELECT shipping_status_name, shipping_status_image,language_id, shipping_status_id FROM ".TABLE_SHIPPING_STATUS);

		if (os_db_num_rows($p_query, true))
		{
			while ($value = os_db_fetch_array($p_query, true))
			{
				$result['shipping_status'][$value['language_id']][$value['shipping_status_id']] =
					array (
						'shipping_status_name' => $value['shipping_status_name'],
						'shipping_status_image' => $value['shipping_status_image']
					);
			}

		}

		if ($param == 'true')
		{
			global $default_cache;
			$default_cache = $result;
		}

		$this->set('system.default', $result);
		return true;
	}*/
}