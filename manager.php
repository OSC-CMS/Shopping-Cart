<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include("includes/functions/include.php");
include("includes/functions/admin.include.php");

$URI_elements = explode("?", ltrim($_SERVER['REQUEST_URI'], '/'));

$requests = array();
if (isset($URI_elements[1]) && (strlen($URI_elements[1]) > 0))
{
	$requests = explode("&", $URI_elements[1]);
}

if (sizeof($requests) > 0)
{
	for ($i = 0, $n = sizeof($requests); $i < $n; $i++)
	{
		$param = explode("=", $requests[$i]);
		$_GET[$param[0]] = $param[1];
	}
}

if (isset($URI_elements[0]) && (strlen($URI_elements[0]) > 0))
{
	require_once('config.php');
	require_once('includes/database.php');
	$db_l = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
	mysql_select_db(DB_DATABASE);

	$categories_array = array();

	$path_elements = explode("/", $URI_elements[0]);
	$URI_elements[0] = $path_elements[sizeof($path_elements) - 1];
	$URI_elements[0] = urldecode(os_db_prepare_input($URI_elements[0]));

	// Категория товара
	$query = 'select categories_id from '.TABLE_CATEGORIES.' where categories_url="'.$URI_elements[0].'"';
	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$cId = $row['categories_id'];
		$matched = true;
	}
	else
		$matched = false;

	// Если есть категория
	if ($matched)
	{
		$HTTP_GET_VARS['cat'] = $cId;
		$_GET['cat'] = $cId;

		mysql_free_result($result);
		mysql_close();
		$PHP_SELF = '/index.php';
		include('index.php');
	}
	// Если нет категории, то ищем товар
	else
	{
		mysql_free_result($result);
		$query = 'select products_id from ' . TABLE_PRODUCTS . ' where products_page_url="'.$URI_elements[0].'"';
		$result = mysql_query($query);
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$pId = $row['products_id'];
			$matched = true;
		}
		else
			$matched = false;

		// Если есть товар
		if ($matched)
		{
			$HTTP_GET_VARS['products_id']  = $pId;
			$_GET['products_id']  = $pId;

			mysql_free_result($result);
			mysql_close();
			$PHP_SELF = '/product_info.php';
			include('product_info.php');
		}
		// Если нет товара, то ищем инфо. страницу
		else
		{
			mysql_free_result($result);
			$query = 'select content_id from ' . TABLE_CONTENT_MANAGER . ' where content_page_url="'.$URI_elements[0].'"';
			$result = mysql_query($query);
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				$coID = $row['content_id'];
				$matched = true;
			}
			else
				$matched = false;

			// Если есть инфо. страница
			if ($matched) 
			{
				$HTTP_GET_VARS['coID']  = $coID;
				$_GET['coID']  = $coID;
				mysql_free_result($result);
				mysql_close();
				$PHP_SELF = '/shop_content.php';
				include('shop_content.php');
			} 
			// Если нет инфо. страницы, то ищем статью
			else
			{
				mysql_free_result($result);
				$query = 'select articles_id from ' . TABLE_ARTICLES . ' where articles_page_url="'.$URI_elements[0].'"';
				$result = mysql_query($query);
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_array($result, MYSQL_ASSOC);
					$aID = $row['articles_id'];
					$matched = true;
				}
				else
					$matched = false;

				// Если есть статья
				if ($matched)
				{
					$HTTP_GET_VARS['articles_id']  = $aID;
					$_GET['articles_id']  = $aID;
					mysql_free_result($result);
					mysql_close();
					$PHP_SELF = '/article_info.php';
					include('article_info.php');
				}
				// Если нет статьи, то ищем категорию статей
				else
				{
					mysql_free_result($result);
					$query = 'select topics_id from ' . TABLE_TOPICS . ' where topics_page_url="'.$URI_elements[0].'"';
					$result = mysql_query($query);
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result, MYSQL_ASSOC);
						$tID = $row['topics_id'];
						$matched = true;
					}
					else
						$matched = false;

					// Если есть категория статей
					if ($matched)
					{
						$HTTP_GET_VARS['tPath']  = $tID;
						$_GET['tPath']  = $tID;
						mysql_free_result($result);
						mysql_close();
						$PHP_SELF = '/articles.php';
						include('articles.php');
					}
					// Если нет категории статей, то ищем новость
					else
					{
						mysql_free_result($result);
						$query = 'select news_id from ' . TABLE_LATEST_NEWS . ' where news_page_url="'.$URI_elements[0].'"';
						$result = mysql_query($query);
						if (mysql_num_rows($result) > 0)
						{
							$row = mysql_fetch_array($result, MYSQL_ASSOC);
							$nID = $row['news_id'];
							$matched = true;
						}
						else
							$matched = false;

						// Если есть новость
						if ($matched)
						{
							$HTTP_GET_VARS['news_id']  = $nID;
							$_GET['news_id']  = $nID;
							mysql_free_result($result);
							mysql_close();
							$PHP_SELF = '/news.php';
							include('news.php');
						}
						// Если нет новости, то ищем faq
						else
						{
							mysql_free_result($result);
							$query = 'select faq_id from '.TABLE_FAQ.' where faq_page_url="'.$URI_elements[0].'"';
							$result = mysql_query($query);
							if (mysql_num_rows($result) > 0)
							{
								$row = mysql_fetch_array($result, MYSQL_ASSOC);
								$fID = $row['faq_id'];
								$matched = true;
							}
							else
								$matched = false;

							if ($matched)
							{
								$HTTP_GET_VARS['faq_id']  = $fID;
								$_GET['faq_id']  = $fID;
								mysql_free_result($result);
								mysql_close();
								$PHP_SELF = '/faq.php';
								include('faq.php');
							}
							// Если нет faq, то инклудим главную
							else
							{
								mysql_free_result($result);
								mysql_close();
								header('HTTP/1.1 404 Not Found');
								$PHP_SELF = '/index.php';
								include('index.php');
							}
						}
					}        
				}
			}
		}
	}
}
// Если пусто, то инклудим главную
else
{
	$PHP_SELF = '/index.php';
	include('index.php');
}

?>