<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

define('HEADING_TITLE', 'Контроль кэша');

define('TEXT_FILES', 'Файлы: ');
define('TEXT_CACHE_DIRECTORY', 'Директория кеша: ');
define('TEXT_RESET_CACHE','Сбросить кеш');
define('TEXT_TOTAL_FILES', 'Всего файлов: ');
define('TEXT_NOCACHE_FILES', 'Кеш-файлов не существует.');
define('USED_SPACE', 'Использованное место: ');
define('ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST', 'Ошибка: Директория кеша не существует.');
define('ERROR_CACHE_DIRECTORY_NOT_WRITEABLE', 'Ошибка: Директоря Кеша защищена от записи.');

define('CACHE_CLEAN', 'Очистить');
define('CACHE_CLEAN_OK', 'Очищен кэш');
define('CACHE_UP', 'Обновить');
define('CACHE_UP_ALL', 'Обновить весь кэш');
define('CACHE_CLEAN_ALL', 'Очистить кэш');


if (!is_dir(_CACHE)) $messageStack->add(ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST, 'error');
if (!is_writeable(_CACHE)) $messageStack->add(ERROR_CACHE_DIRECTORY_NOT_WRITEABLE, 'error');

defined('_VALID_OS') or die('Прямой доступ  не допускается.');
?>

<div class="well well-box well-nice">
	<div class="navbar">
		<div class="navbar-inner">
			<h4 class="title"><?php echo TABLE_HEADING_CACHE; ?></h4>
			<div class="well-right-btn">
				<a class="btn btn-success btn-mini pull-right" href="<?php echo os_href_link(FILENAME_CONFIGURATION.'?gID=11', '', 'NONSSL'); ?>"><?php echo TEXT_SETTING; ?></a>
			</div>
		</div>
	</div>
	<div class="well-box-content well-max-height well-small-font">
		<script type="text/javascript">
		$(document).ready(function(){

			function CleanCache(){
				$.ajax({
					type: "POST",
					url: "ajax.php?ajax_action=cache_clean",
					data: "",
					cache: false,
					dataType: "json",
					success: function(returnData)
					{
						$.jnotify(returnData.msg, returnData.type);
						$("#cache_size").html(0);
					}
				});
			}
			$('#clean_cache').click(function()
			{
		       CleanCache();
		    });
		});
		</script>
		<div id="cache">
			<a class="button" id="clean_cache" href="#" title="<?php echo TABLE_CACHE_CLEAN; ?>"><div id="total_cache"><?php echo CACHE_CLEAN_ALL; ?></div></a>
			<br />
			<?php echo USED_SPACE; ?> <b><span id="cache_size"><?php echo os_format_filesize($total); ?></span></b>
		</div>
	</div>
</div>