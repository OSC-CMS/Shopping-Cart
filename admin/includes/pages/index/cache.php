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
define('CACHE_DIR', 'Директория');
define('CACHE_CLEAN_OK', 'Очищен кэш');
define('CACHE_UP', 'Обновить');
define('CACHE_UP_ALL', 'Обновить весь кэш');
define('CACHE_CLEAN_ALL', 'Очистить');


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

			function CleanCache(cacheType)
			{
				$.ajax({
					type: "POST",
					url: "ajax.php?ajax_action=cache_cleanDir",
					data: {type: cacheType},
					cache: false,
					dataType: "json",
					success: function(returnData)
					{
						$.jnotify(returnData.msg, returnData.type);
						$(".cache_"+cacheType).html(0);
					}
				});

			}
			$('.clean_cache').click(function()
			{
				var cacheType = $(this).data('type');
				CleanCache(cacheType);
				return false;
		    });
		});
		</script>
		<div id="cache">
			<table class="table table-striped table-condensed table-content well-table">
				<tbody>
				<tr>
					<td><?php echo CACHE_DIR; ?> cache</td>
					<td><?php echo USE_CACHE; ?></td>
					<td width="100"><span class="label label-warning bold cache_cache"><?php echo os_format_filesize(os_spaceUsed(_CACHE.'cache/')); ?></span></td>
					<td width="30" class="tright"><a class="btn btn-mini clean_cache" data-type="cache" href="#" title="<?php echo CACHE_CLEAN_ALL; ?>"><i class="icon-trash"></i></td>
				</tr>
				<tr>
					<td><?php echo CACHE_DIR; ?> database</td>
					<td><?php echo DB_CACHE; ?></td>
					<td width="100"><span class="label label-warning bold cache_database"><?php echo os_format_filesize(os_spaceUsed(_CACHE.'database/')); ?></span></td>
					<td width="30" class="tright"><a class="btn btn-mini clean_cache" data-type="database" href="#" title="<?php echo CACHE_CLEAN_ALL; ?>"><i class="icon-trash"></i></a></td>
				</tr>
				<tr>
					<td><?php echo CACHE_DIR; ?> url</td>
					<td><?php echo DB_CACHE_PRO; ?></td>
					<td width="100"><span class="label label-warning bold cache_url"><?php echo os_format_filesize(os_spaceUsed(_CACHE.'url/')); ?></span></td>
					<td width="30" class="tright"><a class="btn btn-mini clean_cache" data-type="url" href="#" title="<?php echo CACHE_CLEAN_ALL; ?>"><i class="icon-trash"></i></a></td>
				</tr>
				</tbody>
			</table>
			<br />
			<table class="table table-striped table-condensed table-content well-table">
				<tbody>
				<tr>
					<td><?php echo CACHE_DIR; ?> compiled</td>
					<td width="100"><span class="label label-warning bold cache_compiled"><?php echo os_format_filesize(os_spaceUsed(_CACHE.'compiled/')); ?></span></td>
					<td width="30" class="tright"><a class="btn btn-mini clean_cache" data-type="compiled" href="#" title="<?php echo CACHE_CLEAN_ALL; ?>"><i class="icon-trash"></i></a></td>
				</tr>
				<tr>
					<td><?php echo CACHE_DIR; ?> system</td>
					<td width="100"><span class="label label-warning bold cache_system"><?php echo os_format_filesize(os_spaceUsed(_CACHE.'system/')); ?></span></td>
					<td width="30" class="tright"><a class="btn btn-mini clean_cache" data-type="system" href="#" title="<?php echo CACHE_CLEAN_ALL; ?>"><i class="icon-trash"></i></a></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>