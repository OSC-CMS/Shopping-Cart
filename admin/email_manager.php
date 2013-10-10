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

$breadcrumb->add(HEADING_TITLE, FILENAME_EMAIL_MANAGER);

$main->head();
$main->top_menu();
?>

<?php
$path_parts = pathinfo($_GET['file']);
$file = _MAIL. $_SESSION['language_admin'].'/'.$path_parts['basename'];

if (is_writable($file))
	$chmod = '<span class="label label-success">'.TEXT_YES.'</span>';
else
	$chmod = '<span class="label label-important">'.TEXT_NO.'</span>';

$st = 1;
if (!empty($_GET['file']))
{
	$st =2;
	if (file_exists($file))
		$code = file_get_contents($file);
	else
		$code = TEXT_FILE_SELECT;
}


//////////////////////////////////////////////


$path_parts_admin = pathinfo($_GET['file_admin']);
$file_admin = _MAIL.'admin/'.$_SESSION['language_admin'].'/'.$path_parts_admin['basename'];

if (is_writable($file_admin))
	$chmod_admin = '<span class="label label-success">'.TEXT_YES.'</span>';
else
	$chmod_admin = '<span class="label label-important">'.TEXT_NO.'</span>';

if ((!empty($_GET['file_admin'])) and ($st ==1))
{
	if(file_exists($file_admin))
		$code_admin = file_get_contents($file_admin);
	else
		$code_admin = TEXT_FILE_SELECT;
}
?>



<div class="row-fluid">
	<div class="span6">
		<div class="page-header-small">
			<h1><?php echo TEXT_CATALOG_TEMPLATES; ?></h1>
		</div>
		<form name="select" action="<?php echo FILENAME_EMAIL_MANAGER; ?>" method="get">
			<select name="file" class="input-block-level">
			<?php
			$file_list = os_array_merge(array('0' => array('id' => '', 'text' => SELECT_FILE)), os_getFiles(_MAIL.$_SESSION['language_admin'].'/', array('.txt', '.html')));
			foreach ($file_list AS $item)
			{
				echo '<option value="'.$item['id'].'">'.$item['text'].'</option>';
			}
			?>
			</select>

			<hr>

			<div class="tcenter footer-btn">
				<input class="btn" type="submit" value="<?php echo BUTTON_EDIT; ?>">
			</div>
		</form>
	</div>
	<div class="span6">
		<div class="page-header-small">
			<h1><?php echo TEXT_ADMIN_TEMPLATES; ?></h1>
		</div>
		<form name="select" action="<?php echo FILENAME_EMAIL_MANAGER; ?>" method="get">
			<select name="file_admin" class="input-block-level">
			<?php
			$file_list_admin = os_array_merge(array('0' => array('id' => '', 'text' => SELECT_FILE)),os_getFiles(_MAIL.'admin/'.$_SESSION['language_admin'].'/',array('.txt','.html')));
			foreach ($file_list_admin AS $item)
			{
				echo '<option value="'.$item['id'].'">'.$item['text'].'</option>';
			}
			?>
			</select>

			<hr>

			<div class="tcenter footer-btn">
				<input class="btn" type="submit" value="<?php echo BUTTON_EDIT; ?>">
			</div>
		</form>
	</div>
</div>






<?php
echo os_draw_form('edit', FILENAME_EMAIL_MANAGER, os_get_all_get_params(), 'post');

if($_POST['save'] && is_file($file))
{
	echo '<div class="alert alert-success">'.TEXT_FILE_SAVED.' <a href="'.os_href_link(FILENAME_EMAIL_MANAGER).'">'.BUTTON_BACK.'</a></div>';
}
else
{
	if (isset($_GET['file']))
	{
		echo '<div class="alert alert-block">';
			echo TEXT_FILE.' '.$file.'. '.TEXT_FILE_WRITABLE.' '.$chmod;
		echo '</div>';

		echo '<textarea name="code" id="content" class="input-block-level">'.$code.'</textarea>';

		if (is_writable($file))
		{
			echo '
			<hr>
			<div class="tcenter footer-btn">
				<input class="btn btn-success" type="submit" name="save" value="'.BUTTON_SAVE.'">
			</div>
			';
		}
	}
}

if($_POST['save'] && is_file($file))
{
	if (is_writable($file))
	{
		if (!$handle = fopen($file, 'w'))
		{
			echo '<div class="alert alert-error">'.TEXT_FILE_OPEN_ERROR." ($file)</div>";
			exit;
		}

		if (fwrite($handle, stripslashes($_POST['code'])) === FALSE)
		{
			echo '<div class="alert alert-error">'.TEXT_FILE_WRITE_ERROR." ($file)</div>";
			exit;
		}

		fclose($handle);
	}
	else
		echo '<div class="alert alert-error">'.TEXT_FILE_PERMISSION_ERROR.'</div>';
}
?>
</form>     














<?php echo os_draw_form('edit_admin', FILENAME_EMAIL_MANAGER, os_get_all_get_params(), 'post');

if ($_POST['save'] && is_file($file_admin))
{
	echo '<div class="alert alert-success">'.TEXT_FILE_SAVED.' <a href="'.os_href_link(FILENAME_EMAIL_MANAGER).'">'.BUTTON_BACK.'</a></div>';
}
else
{
	if (isset($_GET['file_admin']))
	{
		echo '<div class="alert alert-block">';
		echo TEXT_FILE.' '.$file_admin.'. '.TEXT_FILE_WRITABLE.' '.$chmod_admin;
		echo '</div>';

		echo '<textarea name="code_admin" id="content" class="input-block-level">'.$code_admin.'</textarea>';

		if (is_writable($file_admin))
		{
			echo '
			<hr>
			<div class="tcenter footer-btn">
				<input class="btn btn-success" type="submit" name="save" value="'.BUTTON_SAVE.'">
			</div>
			';
		}
	}
}

if ($_POST['save'] && is_file($file_admin))
{
	if (is_writable($file_admin))
	{
		if (!$handle = fopen($file_admin, 'w'))
		{
			echo '<div class="alert alert-error">'.TEXT_FILE_OPEN_ERROR." ($file_admin)</div>";
			exit;
		}

		if (fwrite($handle, stripslashes($_POST['code_admin'])) === FALSE)
		{
			echo '<div class="alert alert-error">'.TEXT_FILE_WRITE_ERROR." ($file_admin)</div>";
			exit;
		}

		fclose($handle);
	}
	else
		echo '<div class="alert alert-error">'.TEXT_FILE_PERMISSION_ERROR."</div>";
}
?>
</form>

<?php $main->bottom(); ?>