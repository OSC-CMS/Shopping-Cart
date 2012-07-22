<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

global $main;

$main->head();
$main->top_menu();
?>
<div class="wrap-con">
	<div class="page-header">
		<h1>Редактор ЧПУ</h1>
	</div>
<a class="btn" href="plugins_page.php?page=seo_url_editor_page">Главная</a>
	<div class="clear"></div>
	<?php
		$languages_query_raw = "select languages_id, name, code, image, directory, sort_order,status,language_charset from " . TABLE_LANGUAGES . " order by sort_order";
		$languages_query = os_db_query($languages_query_raw);
		$lang = '';
		while ($languages = os_db_fetch_array($languages_query)) 
		{
			$lang .= '<option value="'.$languages['code'].'">'.$languages['code'].'</option>';
		}

		$countCategories = os_db_num_rows(getSEOCategoriesUrl());
		$countProducts = os_db_num_rows(getSEOProductsUrl());
		$countNews = os_db_num_rows(getSEONewsUrl());
		$countPages = os_db_num_rows(getSEOPagesUrl());
		$countTopics = os_db_num_rows(getSEOTopicsUrl());
		$countArticles = os_db_num_rows(getSEOArticlesUrl());
		$countFaq = os_db_num_rows(getSEOFaqUrl());
	?>

	<h2>Автоматическая генерация ЧПУ</h2>
	<ul class="plugin-main-menu">
		<li>
			<form method="post" action="">
			Генерировать на основе языка: <select class="round" name="lang">
			<?php
				echo $lang;
			?>
			</select> | 
			<input class="btn <?php echo ($countCategories == 0) ? 'disabled' : ''; ?>" type="submit" name="categories" value="Категории товаров (<?php echo $countCategories; ?>)" <?php echo ($countCategories == 0) ? 'disabled' : ''; ?> />
			</form>
		</li>
		<li>
			<form method="post" action="">
			Генерировать на основе языка: <select class="round" name="lang">
			<?php
				echo $lang;
			?>
			</select> | 
			<input class="btn <?php echo ($countProducts == 0) ? 'disabled' : ''; ?>" type="submit" name="products" value="Товары (<?php echo $countProducts; ?>)" <?php echo ($countProducts == 0) ? 'disabled' : ''; ?> />
			</form>
		</li>
		<li>
			<form method="post" action="">
			<input class="btn <?php echo ($countNews == 0) ? 'disabled' : ''; ?>" type="submit" name="news" value="Новости (<?php echo $countNews; ?>)" <?php echo ($countNews == 0) ? 'disabled' : ''; ?> />
			</form>
		</li>
		<li>
			<form method="post" action="">
			<input class="btn <?php echo ($countPages == 0) ? 'disabled' : ''; ?>" type="submit" name="pages" value="Информационные страницы (<?php echo $countPages; ?>)" <?php echo ($countPages == 0) ? 'disabled' : ''; ?> />
			</form>
		</li>
		<li>
			<form method="post" action="">
			Генерировать на основе языка: <select class="round" name="lang">
			<?php
				echo $lang;
			?>
			</select> | 
			<input class="btn <?php echo ($countTopics == 0) ? 'disabled' : ''; ?>" type="submit" name="topics" value="Категории статей (<?php echo $countTopics; ?>)" <?php echo ($countTopics == 0) ? 'disabled' : ''; ?> />
			</form>
		</li>
		<li>
			<form method="post" action="">
			Генерировать на основе языка: <select class="round" name="lang">
			<?php
				echo $lang;
			?>
			</select> | 
			<input class="btn <?php echo ($countArticles == 0) ? 'disabled' : ''; ?>" type="submit" name="articles" value="Статьи (<?php echo $countArticles; ?>)" <?php echo ($countArticles == 0) ? 'disabled' : ''; ?> />
			</form>
		</li>
		<li>
			<form method="post" action="">
			<input class="btn <?php echo ($countFaq == 0) ? 'disabled' : ''; ?>" type="submit" name="faq" value="Вопросы и ответы (<?php echo $countFaq; ?>)" <?php echo ($countFaq == 0) ? 'disabled' : ''; ?> />
			</form>
		</li>
		<div class="clear"></div>
	</ul>

	<?php if (empty($_POST)) { ?>
	<p class="plugin-alert">
		<b>Внимание! Перед использованием плагина делайте <a class="btn btn-small" href="backup.php" target="_blank">Резервное копирование БД</a></b>
		<br /><br />
		Внимание! ЧПУ ссылки генерируются автоматически, а это значит, что введенные вручную ссылки будут изменены на новые.
		<br /><br />
		Внимание! Если много товаров, категорий, новостей и т.д..., то процесс генерации может занять некоторе время. Пожалуйста, не перезагружайте страницу до полной генерации!
	</p>
	<?php } ?>

	<div style="max-height:300px;overflow:auto;">
	<?php
		if (isset($_POST['categories']) && !empty($_POST['categories']))
		{
			echo genSEOCategories($_POST['lang']);
		}
		elseif (isset($_POST['products']) && !empty($_POST['products']))
		{
			echo genSEOProducts($_POST['lang']);
		}
		elseif (isset($_POST['news']) && !empty($_POST['news']))
		{
			echo genSEONews();
		}
		elseif (isset($_POST['pages']) && !empty($_POST['pages']))
		{
			echo genSEOPages();
		}
		elseif (isset($_POST['topics']) && !empty($_POST['topics']))
		{
			echo genSEOTopics($_POST['lang']);
		}
		elseif (isset($_POST['articles']) && !empty($_POST['articles']))
		{
			echo genSEOArticles($_POST['lang']);
		}
		elseif (isset($_POST['faq']) && !empty($_POST['faq']))
		{
			echo genSEOFaq();
		}
	?>
	</div>

	<div class="plugin-copy">
		Редактор ЧПУ <?php echo SEO_URL_EDITOR_VERSION; ?><br />
		Разработка - <a href="http://osc-cms.com" target="_blank" title="OSC-CMS - бесплатный скрипт интернет-магазина">OSC-CMS</a>
	</div>
</div>
<?php $main->bottom(); ?>