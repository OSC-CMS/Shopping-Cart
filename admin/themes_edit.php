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

if (!isset($_SESSION['themes_a']))
{
	$_SESSION['themes_a'] = "default";
	$themes = $_SESSION['themes_a'];
}
else
{  
	if (isset($_GET['themes_a']))
	{
		$themes = htmlspecialchars($_GET['themes_a']);
		$themes = str_replace('..', '', $themes);
		$_SESSION['themes_a'] =  $themes;
	}
	else
		$themes = $_SESSION['themes_a'];
}

if (isset($_GET['file_edit']))
{
	$file = htmlspecialchars(base64_decode($_GET['file_edit']));
	$file = str_replace('..', '', $file);
}
else
	$file = "index.html";

if (isset($_POST['themes_text']) && !empty($_POST['themes_text']))
{
	$cartet->themes->saveTemplateFile(array('theme' => $themes, 'file' => $file, 'content' => $_POST['themes_text']));
}

function all_file($fl, $themes, $name)
{
	echo '<select class="input-block-level" name="filename" onchange="top.location.href = this.options[this.selectedIndex].value">';
	if ($dir = opendir( dir_path('themes').$themes.$fl))
	{
		while (($templates = readdir($dir)) !== false)
		{
			if (($templates != ".") && ($templates != "..") && (substr_count($templates,".")==1))
			{
				$templates_array[] = $templates;
			}
		}
		closedir($dir);
	}

	echo "<option selected>$name</option>";
	foreach($templates_array as $type)
	{
		echo "<option value=\"?file_edit=".base64_encode($fl.$type)."\">".$type."</option>";
	}
	echo '</select>';
}

add_action('head_admin', 'headCodemirror');
function headCodemirror()
{
	_e('<link rel="stylesheet" href="/jscript/codemirror/lib/codemirror.css">');
	_e('<script src="/jscript/codemirror/lib/codemirror.js"></script>');
	_e('<script src="/jscript/codemirror/mode/smarty/smarty.js"></script>');
	_e('<script>
		$(document).ready(function () {
			var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
				lineNumbers: true,
				mode: "smarty",
				smartyVersion: 3
			});
		});
		</script>');
	_e('<style>
		.CodeMirror {padding-bottom:20px; margin-bottom:10px; border:1px solid #c0c0c0; background-color: #ffffff; height: auto; min-height: 300px; width:100%;}
		.activeline {background: #f0fcff !important;}
	</style>');
}

$breadcrumb->add(HEADING_TITLE, FILENAME_THEMES_EDIT);

$main->head();
$main->top_menu();
?>

<?php
if ($dir = opendir(dir_path('themes'))) 
{
	while (($templates = readdir($dir)) !== false)
	{
		if (is_dir( dir_path('themes')."//".$templates) && ($templates != ".") && ($templates != ".."))
		{
			$templates_array[] = $templates;
		}
	}
	closedir($dir);
	sort($templates_array);
}
?>
<div class="row-fluid">
	<div class="span9">
		<div class="alert">
			<?php
			if (is_writeable(DIR_FS_CATALOG.'/themes/'.$themes.'/'.$file))
				echo "<font color='green'><b>".THEMES_WRITEABLE_YES."</b></font> ";
			else
				echo "<font color='red'><b>".THEMES_WRITEABLE_NO."!<br />".THEMES_P."</b></font> ";
			?>
			<?php
			$st = '<span class="label label-info">/themes/'.$themes.'/'.$file.'</span>';
			$st = str_replace('//', '/',$st);
			echo ($st);
			?>
		</div>

		<form id="themes" method="post" action="">
			<input type="hidden" name="file" value="<?php echo $file; ?>">
			<input type="hidden" name="theme" value="<?php echo $themes; ?>">

			<textarea class="input-block-level" id="content" name="content"><?php echo $cartet->themes->getTemplateFileContent(array('theme' => $themes, 'file' => $file)); ?></textarea>

			<hr>

			<div class="tcenter footer-btn">
				<?php
				if (is_writeable(DIR_FS_CATALOG.'/themes/'.$themes.'/'.$file))
				{
					echo '<input class="btn btn-success ajax-save-form" data-form-action="themes_saveTemplateFile" type="submit" value="'.THEMES_SAVE.'" />';
				}
				?>
			</div>
		</form>
	</div>

	<div class="span3">
		<div class="page-header-small">
			<h1><?php echo THEMES_TEXT; ?></h1>
		</div>
		<select class="input-block-level" name="navSelect" onchange="top.location.href = this.options[this.selectedIndex].value">
			<?php
			foreach($templates_array as $type)
			{
				if (isset($_SESSION['themes_a']))
				{
					if ($_SESSION['themes_a'] == $type)
						echo '<option value="?themes_a='.$type.'" selected>'.$type.'</option>';
					else
						echo '<option value="?themes_a='.$type.'">'.$type.'</option>';
				}
				else
				{
					if ($type == 'default')
						echo '<option selected value="?themes_a='.$type.'">'.$type.'</option>';
					else
						echo '<option value="?themes_a='.$type.'">'.$type.'</option>';
				}
			}
			?>
		</select>

		<br />
		<div class="page-header-small">
			<h1>Файлы шаблона</h1>
		</div>
		<a href="?file_edit=<?php echo base64_encode('/index.html'); ?>"><span class="label label-info">index.html</span></a> <a href="?file_edit=<?php echo base64_encode('/style.css'); ?>"><span class="label label-info">style.css</span></a>
		<br /><br />
		<?php
		all_file('/boxes/',$themes, '[ Блоки ]');
		all_file('/module/',$themes, '[ Модули ]');
		all_file('/module/product_listing/',$themes, '[ Списки товара ]');
		all_file('/module/product_options/',$themes, '[ Атрибуты товара ]');
		all_file('/module/product_info/',$themes, '[ Карточка товара ]');
		all_file('/module/categorie_listing/',$themes, '[ Списки категорий ]');
		?>
	</div>
</div>

<?php $main->bottom(); ?>