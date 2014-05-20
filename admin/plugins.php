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

include( dir_path_admin('func').'plugin.php'); 

if (isset($_GET['plugin']) && !empty($_GET['plugin'])) 
{
	$p->module = $_GET['plugin'];
	$p->name = $_GET['plugin'];
}

$aPlugins = $p->plug_array();
$plugins = $p->info;

// Получаем кнопки
$getReadonly = $p->getReadonly();
// Получаем все опции плагинов
$getAllOption = $p->getAllOption();

if (isset($_GET['group']) && !empty($_GET['group'])) 
{
	$p->group = $_GET['group'];
}

// Фильтрация плагинов
function sortPlugins($aPlugins = array(), $plugins = array(), $key, $val)
{
	$_plug_array = array();

	if ($key == 'status')
	{
		foreach ($aPlugins as $_group => $_plug_value)
		{
			foreach ($_plug_value as $_value)
			{
				if (isset($plugins[$_value['name']][$key]) && ($plugins[$_value['name']][$key] == $val))
				{
					$_plug_array[$_group][] = $_value;
				}
			}
		}
	}
	else
	{
		foreach ($aPlugins as $_group => $_plug_value)
		{
			if ($_group == $val)
			{
				foreach ($_plug_value as $_value)
				{
					$_plug_array[$_group][] = $_value;
				}
			}
		}
	}
	return $_plug_array;
}

$url = '';
$urlGroup = (isset($_GET['group'])) ? '&group='.$_GET['group'] : '';

// Фильтруем по статусу
if (isset($_GET['status']))
{
	$_plug_array = sortPlugins($aPlugins, $plugins, 'status', $_GET['status']);
	$url = '?status='.$_GET['status'];
}
// Фильтруем по группам
elseif (isset($_GET['group']))
{
	$_plug_array = sortPlugins($aPlugins, $plugins, 'group', $_GET['group']);
	$url = '?group='.$_GET['group'];
}
// Если нет фильтра
else
{
	$_plug_array = $aPlugins;
}

if (isset($_GET['action']))
{
	switch ($_GET['action'])
	{
		case 'install':
			$p->install($_GET['plugin']);
			os_redirect(FILENAME_PLUGINS.$url);
		break;

		case 'remove':
			$p->remove();
			os_redirect(FILENAME_PLUGINS.$url);
		break;

		// TODO: добавить возможность выключать\включать плагины без удаления инфы из БД.
		case 'update_status':
			$p->updatePluginStatus();
			os_redirect(FILENAME_PLUGINS.$url);
		break;

		case 'process':
			$p->process();
			os_redirect(FILENAME_PLUGINS.$url);
		break;
	
		case 'save':
			$p->save_options();
			os_redirect(FILENAME_PLUGINS.'?act=setting&plugin='.$p->module);//.'&group='.$p->info[$p->module]['group']
		break;

		case 'multi_action':
			$p->multi_action();
			os_redirect(FILENAME_PLUGINS.$url);
		break;
	}
}

if (isset($_GET['action']) && $_GET['action'] == 'process_true')
{
	$messageStack->add_session('Плагин '.$_GET['module'].' успешно выполнен', 'success');
	os_redirect(FILENAME_PLUGINS);
}

$breadcrumb->add(HEADING_TITLE, FILENAME_PLUGINS);

if ($_GET['act'] == 'setting' && !empty($_GET['plugin']))
{
	$getOptions = $p->option();
	$breadcrumb->add($p->info[$p->module]['title'], FILENAME_PLUGINS.'?act=setting&plugin='.$p->module);
}

$checkPluginsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."plugins GROUP BY plugins_key ORDER BY plugins_id ASC");
$aCheckPlugins = array();
if (os_db_num_rows($checkPluginsQuery) > 0)
{
	$p_count = 0;
	while($checkPlugins = os_db_fetch_array($checkPluginsQuery))
	{
		$pFile = dir_path('plug').$checkPlugins['plugins_key'].'.php';
		$pDir = dir_path('plug').$checkPlugins['plugins_key'].'/'.$checkPlugins['plugins_key'].'.php';

		if (!is_file($pFile) && !is_file($pDir))
		{
			$aCheckPlugins[$checkPlugins['plugins_key']] = array('file' => $pFile, 'dir' => $pDir);
			$p_count++;
		}
	}

	if (isset($_GET['act']) && $_GET['act'] == 'delete_plugins_error')
	{
		if ($aCheckPlugins)
		{
			foreach($aCheckPlugins AS $plugin_del => $plugin_del_values)
			{
				os_db_query("DELETE FROM ".DB_PREFIX."plugins WHERE plugins_key = '".os_db_input($plugin_del)."'");
			}
			os_redirect(FILENAME_PLUGINS.'?act=plugins_error');
		}
	}
}

$main->head();
$main->top_menu();
?>

<?php
if ($_GET['act'] == 'setting' && !empty($_GET['plugin']))
{
	if ($getOptions)
	{
	?>
	<form action="<?php echo FILENAME_PLUGINS.'?action=save&plugin='.$p->module; ?>" method="post">
		<?php
		if ($getOptions['options'])
		{
			foreach ($getOptions['options'] AS $o)
			{
			?>
				<div class="control-group">
					<label class="control-label" for="<?php echo ($o['option']) ? $o['option'] : ''; ?>"><?php echo $o['name']; ?></label>
					<div class="controls">
						<?php echo $o['value']; ?>
						<?php if ($o['desc']) { ?><span class="help-block"><?php echo $o['desc']; ?></span><?php } ?>
					</div>
				</div>
			<?php
			}
		}
		?>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo PLUGINS_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_PLUGINS); ?>"><?php echo BUTTON_BACK; ?></a>
		</div>

	</form>
	<?php } ?>

<?php } else { ?>

	<ul class="nav nav-tabs">
		<li <?php echo (!isset($_GET['status']) && !isset($_GET['group']) && !isset($_GET['act'])) ? 'class="active"' : ''; ?>><a href="<?php echo os_href_link(FILENAME_PLUGINS); ?>">Все</a></li>
		<li <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'class="active"' : ''; ?>><a href="<?php echo os_href_link(FILENAME_PLUGINS, 'status=1'); ?>">Активные</a></li>
		<li <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'class="active"' : ''; ?>><a href="<?php echo os_href_link(FILENAME_PLUGINS, 'status=0'); ?>">Неактивные</a></li>
		
		<li class="dropdown <?php echo (isset($_GET['group']) && !empty($_GET['group'])) ? 'active' : ''; ?>">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Группы <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<?php
				foreach ($aPlugins as $_group => $_plug_value)
				{
					echo '<li '.((isset($_GET['group']) && $_GET['group'] == $_group) ? 'class="active"' : '').'><a href="'.os_href_link(FILENAME_PLUGINS, 'group='.$_group).'">'.$_group.'</a></li>';
				}
				?>
			</ul>
		</li>
		<li class="pull-right"><a href=""><i class="icon-plus"></i> Добавить</a></li>
		<li class="pull-right <?php echo (isset($_GET['act']) && $_GET['act'] == 'plugins_error') ? 'active' : ''; ?>"><a href="<?php echo os_href_link(FILENAME_PLUGINS, 'act=plugins_error'); ?>">Ошибки<?php if ($p_count) { ?> <span class="badge badge-important"><?php echo $p_count; ?></span><?php } ?></a></li>
	</ul>

	<?php if (isset($_GET['act']) && $_GET['act'] == 'plugins_error') { ?>

		<?php if ($aCheckPlugins) { ?>
			<div class="alert alert-info">Приведенные ниже плагины не существуют, либо не доступен для чтения. Но записи о них остались в базе и могут нарушать работу плагинной системы!</div>
			<table class="table">
				<thead>
					<tr>
						<th>Название в базе</th>
						<th>Возможное место расположения</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($aCheckPlugins AS $plugin_name => $plugins_values)
					{
						?>
						<tr>
							<td><div class="bold"><?php echo $plugin_name; ?></div></td>
							<td>
								<?php echo $plugins_values['dir']; ?>
								<br />
								<?php echo $plugins_values['file']; ?>
							</td>
						</tr>
					<?php
					}
				?>
				</tbody>
			</table>

			<div class="tcenter footer-btn">
				<a class="btn btn-success" href="<?php echo os_href_link(FILENAME_PLUGINS, 'act=delete_plugins_error'); ?>">Очистить таблицу плагинов от старых данных</a>
			</div>
		<?php } else { ?>
			<div class="alert alert-info">Ошибок в базе нет.</div>
		<?php } ?>

	<?php } else { ?>
		<form name="multi_action_form" action="<?php echo FILENAME_PLUGINS; ?>?action=multi_action" method="post">

			<div class="row-fluid">
				<div class="span6">
					<input type="search" placeholder="Найти плагин..." id="find-plugin" tabindex="0"/>
				</div>
				<div class="span6">
					<div class="pull-right">
						<select name="action" dir="ltr" onchange="this.form.submit();">
							<option value="<?php echo PLUGINS_SELECTED; ?>" selected="selected"><?php echo PLUGINS_SELECTED; ?></option>
							<option value="install" ><?php echo PLUGINS_INSTALL;?></option>
							<option value="remove" ><?php echo PLUGINS_REMOVE;?></option>
						</select>
					</div>
				</div>
			</div>

			<table id="simple-filter-table" class="table table-condensed table-big-list">
				<thead>
					<tr>
						<th width="40" class="tcenter"><input type="checkbox" name="plug_all" onClick="javascript:SwitchCheck();" title="<?php echo PLUGINS_SWITCH_ALL; ?>" /></th>
						<th width="450" colspan="2"><span class="line"></span><?php echo TABLE_HEADING_PLUGINS; ?></th>
						<th><span class="line"></span><?php echo TABLE_HEADING_FILENAME; ?></th>
						<?php //echo TABLE_HEADING_STATUS; ?>
						<?php //echo TABLE_HEADING_ACTION; ?>
					</tr>
				</thead>
				<tbody>
				<?php
				$i = 0;
				foreach ($_plug_array as $_group => $_plug_value)
				{
					$p->group = $_group;

					if ($_group == 'themes')
					{
						$_group = HEADING_TITLE_THEMES.' ('.CURRENT_TEMPLATE.')';
					}

					if ($_group == 'update')
						$_group = PLUGINS_UPDATE;

					if (!isset($_GET['group']))
					{
						?>
						<tr>
							<td class="bold" colspan="6"><?php echo $_group; ?></td>
						</tr>
						<?php
					}

					if (!empty($_plug_value))
					{

						foreach ($_plug_value as $_value)
						{
							$p->name = $_value['name'];

							if (empty($p->module)) $p->module = $p->name;
							$p->group = $p->info[$p->name]['group']; // текущая группа плагина -> main | themes | update

							//определяем обновление установлено уже или нет
							if ($p->check_update()) $_through = ''; else $_through = ' text-decoration: line-through; ';

							//if ($p->module == $p->name)
							?>
							<tr <?php if (isset($plugins[$p->name]['status']) && ($plugins[$p->name]['status'] == 1)) { ?>class="success"<?php } ?>>
								<td class="tcenter"><input type="checkbox" name="plugins[]" value="<?php echo $p->name; ?>" /></td>
								<td width="50"><?php echo $_value['icon'];?></td>
								<td width="400">
									<span class="bold"><?php echo $_value['title'];?></span><br />
									<?php echo $_value['name']; ?>
									<div class="pt10">
										<?php if (isset($plugins[$p->name]['status']) && ($plugins[$p->name]['status'] == 1)) { ?>
											<a class="btn btn-mini btn-danger" onclick="return confirm('Действительно хотите удалить плагин?');" href="<?php echo FILENAME_PLUGINS.'?action=remove&plugin='.$p->name.$urlGroup; ?>" title="<?php echo IMAGE_ICON_STATUS_RED_LIGHT; ?>">Удалить</a>
										<?php } else { ?>
											<a class="btn btn-mini" href="<?php echo FILENAME_PLUGINS.'?action=install&plugin='.$p->name.$urlGroup; ?>" title="<?php echo IMAGE_ICON_STATUS_GREEN_LIGHT; ?>">Установить</a>
										<?php } ?>

										<?php if (isset($plugins[$p->name]['process']) && count($plugins[$p->name]['process']) > 0 && $plugins[$p->name]['status'] == 1) { ?>
											<a class="btn btn-mini" href="<?php echo FILENAME_PLUGINS.'?plugin='.$p->name; ?>&action=process" title="<?php echo PLUGINS_PROCESS; ?>"><?php echo PLUGINS_PROCESS; ?></a>
										<?php } ?>

										<?php if (isset($plugins[$p->name]['status']) && ($plugins[$p->name]['status'] == 1) && $getAllOption[$p->name]) { ?>
											<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_PLUGINS, 'act=setting&plugin='.$p->name.$urlGroup); ?>"><i class="icon-edit"></i></a>
										<?php } ?>

										<?php
										if (isset($plugins[$p->name]['status']) && ($plugins[$p->name]['status'] == 1)) {
											if ($getReadonly[$p->name])
											{
												if (is_array($getReadonly[$p->name]))
												{
													echo implode(' ', $getReadonly[$p->name]);
												}
												else
													echo $getReadonly[$p->name];
											}
										}
										?>
									</div>
								</td>
								<td>
									<?php if ($_value['desc']) { ?>
									<?php echo $_value['desc']; ?><br />
									<?php } ?>
									<?php echo TABLE_HEADING_VERSION; ?>: <span class="label label-info"><?php echo (!empty($_value['version'])? $_value['version'] : '0'); ?></span>
									<?php echo PLUGINS_AUTHOR; ?>: <a href="<?php echo $_value['author_uri']; ?>" target="_blank"><?php echo $_value['author']; ?></a>
									<?php if ($_value['plugin_uri']) { ?>
										 | <a href="<?php echo $_value['plugin_uri']; ?>" target="_blank">Страница плагина</a><br />
									<?php } ?>
								</td>
							</tr>
							<?php
						}
					}
				}
				?>
				</tbody>
			</table>

			<hr>

			<div class="row-fluid">
				<div class="span6"></div>
				<div class="span6">
					<div class="pull-right">
						<select name="action" dir="ltr" onchange="this.form.submit();">
							<option value="<?php echo PLUGINS_SELECTED; ?>" selected="selected"><?php echo PLUGINS_SELECTED; ?></option>
							<option value="install" ><?php echo PLUGINS_INSTALL;?></option>
							<option value="remove" ><?php echo PLUGINS_REMOVE;?></option>
						</select>
					</div>
				</div>
			</div>

		</form>
	<?php } ?>

	<script>
		function filterTable(value){
			if(value){
				$('#simple-filter-table tbody tr:not(:contains("'+value+'"))').hide();
				$('#simple-filter-table tbody  tr:contains("'+value+'")').show();
			}
			else {
				$('#simple-filter-table tbody tr').show();
			}
		}
		$('#find-plugin').bind({
			keyup: function(){
				filterTable($(this).val());
			},
			change: function(){
				filterTable($(this).val());
			},
			clearFields: function(){
				filterTable($(this).val());
			}
		});
	</script>
<?php } ?>

<?php $main->bottom(); ?>