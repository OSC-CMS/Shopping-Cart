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

if ( isset($_GET['action']))
{
	switch ($_GET['action'])
	{
		case 'themes_remove':
		//установка новых плагинов
		$themes_name = $_GET['themes_name'];
		$themes_plug_array = $p->get_plugins_theme();

		if (!empty($themes_plug_array))
		{
			$col = 0;
			$_plug = array();
			foreach($themes_plug_array as $_value)
			{
				//устанавливаем все плагины шаблона
				@ $p->install($_value[0], 'themes');
				$_plug[] = $_value[0];
				$col ++;
			}
		}

		if ($col > 0)
		{
			if ($col == 1)
			{
				$messageStack->add_session('Плагин шаблона успешно установлен ('.$_plug[0].').', 'success');
			}
			else
			{
				$messageStack->add_session('Плагины шаблона успешно установлены.', 'success');
				$messageStack->add_session( '<font color="red">('.$col.') '. implode(', ', $_plug).'</font>', 'success');
			}
		}
		os_redirect(FILENAME_THEMES);
		break;
	}
}

if(!empty($_SERVER['QUERY_STRING']))
{
	if (!empty($_GET['c_templates']))
	{
		$_c_templates = os_check_file_name($_GET['c_templates']);
		os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".$_c_templates."' where configuration_key='CURRENT_TEMPLATE'");
		os_redirect(FILENAME_THEMES.'?action=themes_remove&themes_name='.CURRENT_TEMPLATE);
	}
}

$breadcrumb->add(HEADING_TITLE, FILENAME_THEMES);

$main->head();
$main->top_menu();
?>

<?php

if ($dir = opendir(DIR_FS_CATALOG.'themes/')) 
{
	while (($templates = readdir($dir)) !== false)
	{
		if (is_dir(DIR_FS_CATALOG.'themes/'."//".$templates)  & ($templates != ".") && ($templates != "..") && ($templates != ".svn") )
		{
			$templates_array[] = array ('id' => $templates, 'text' => $templates);
		}
	}
	closedir($dir);
	sort($templates_array);
}
$os_shop_style = array();

foreach($templates_array as $key => $type)
{
	foreach($type as $ship)
	{
		if (!in_array($ship,$os_shop_style))
			$os_shop_style[] = $ship;
	}
}

?>

<div class="page-header">
	<h1><?php echo THEMES_H1; ?></h1>
</div>

<div class="row-fluid clearfix">
	<ul class="thumbnails">
<?php
$src = "";
foreach($os_shop_style as $str)
{
	if (!empty($str))
	{
		$src = "themes/".$str."/screenshot.jpg";
		if (!is_file(DIR_FS_CATALOG.$src)) //Nouanoaoao ee ne?eioio?
		{
			$src = 'admin/themes/'.ADMIN_TEMPLATE.'/images/themes.png';
		}
		$src = HTTP_SERVER.DIR_WS_CATALOG.$src;

		if (CURRENT_TEMPLATE == $str)
		{
			?>
			<li class="span2">
				<div class="thumbnail">
					<div class="theme-screenshot"><a href="<?php echo _HTTP_THEMES.$str; ?>/screenshot-1.jpg"><img src="<?php echo $src; ?>" alt="<?php echo $str; ?>" /></a></div>
					<div class="caption">
						<h4><?php echo ucwords($str); ?></h4>
						<a href="themes_edit.php?themes_a=<?php echo $str; ?>"><?php echo THEMES_EDIT; ?></a>
					</div>
				</div>
			</li>
			<?php
		}
	}
}
?>
	</ul>
</div>

<?php if (count($os_shop_style) > 1) { ?>
<div class="page-header">
	<h1><?php echo THEMES_H2; ?></h1>
</div>

<div class="row-fluid clearfix">
	<ul class="thumbnails">
	<?php
	$i = 0;
	foreach($os_shop_style as $str)
	{
		if (!empty($str) && CURRENT_TEMPLATE != $str)
		{
			$src = "themes/".$str."/screenshot.jpg";
			if (!is_file(DIR_FS_CATALOG.$src))
			{
				$src = 'admin/themes/'.ADMIN_TEMPLATE.'/images/themes.png';
			}
			$src = HTTP_SERVER.DIR_WS_CATALOG.$src;
			?>
			<li class="span2">
				<div class="thumbnail">
					<div class="theme-screenshot"><a href="<?php echo _HTTP_THEMES.$str; ?>/screenshot-1.jpg"><img src="<?php echo $src; ?>" alt="<?php echo $str; ?>" /></a></div>
					<div class="caption">
						<h4><?php echo ucwords($str); ?></h4>
						<p><a href="themes_edit.php?themes_a=<?php echo $str; ?>"><?php echo THEMES_EDIT; ?></a></p>
						<a class="btn btn-primary btn-block" href="?c_templates=<?php echo $str; ?>">Установить</a>
					</div>
				</div>
			</li>
			<?php
			$i++;
			if ($i == 6)
			{
				echo '</div><div class="row-fluid clearfix">';
				$i = 0;
			}
		}
	}
	?>
	</ul>
</div>
<?php } ?>

<?php $main->bottom(); ?>