<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');

$breadcrumb->add('Update', 'update.php');

$main->head();
$main->top_menu();
?>

<?php $update = $cartet->service->checkUpdate(); ?>

<?php if (!empty($update['version'])) { ?>

	<h4>Версия: <?php echo $update['version']; ?></h4>
	<p class="muted">Дата релиза: <?php echo $update['date']; ?></p>
	<p><a href="<?php echo $update['url']; ?>" target="_blank">Перейти на страницу загрузки</a></p>

	<?php if (!function_exists('curl_init')){ ?>
		<p>
			Автоматическая загрузка не возможна, поскольку на сервере отсутствует CURL.<br>
			Скачайте архив с обновлением вручную и установите через <a href="update.php?action=install">установку дополнений</a>
		</p>
		<p><a class="btn" href="<?php echo $update['url'];?>">Скачать обновление</a></p>

	<?php } else { ?>

		<div class="alert alert-info">Автоматическое обновление заменит файлы скрипта, что приведет к потере изменений которые вы вносили!</div>

		<p>
			<a class="btn disabled" href="update.php?action=install" onclick="return installUpdate(this)">Загрузить обновление</a>
			<span class="load" style="display:none">Загружаю...</span>
		</p>

		<script>
			function installUpdate(link)
			{
				link = $(link);
				link.parent('p').addClass('loading').find('.load').show();
				link.hide();
				window.location.href = link.attr('href');
				return false;
			}
		</script>
	<?php } ?>

<?php } else { ?>
	<div class="alert alert-info">На данный момент обновлений нет.</div>
<?php } ?>



<?php $main->bottom(); ?>