<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require_once('includes/top.php');

if (!isset($_SESSION['language']) && !isset($_POST['LANGUAGE']))
{
	$_SESSION['language'] = 'ru';
}  

if ( isset($_SESSION['language']) && ($_SESSION['language'] == 'ru' or $_SESSION['language']=='en'))
{
	///
} 
else
{
	$_SESSION['language'] = 'ru';
}

if (isset($_POST['LANGUAGE']))
{
	$_SESSION['language'] = $_POST['LANGUAGE'];
}
include('lang/'.$_SESSION['language'].'/lang.php');

define('HTTP_SERVER','');
define('HTTPS_SERVER','');
define('DIR_WS_CATALOG','');
$text = "";

function  text_ok ($str, $type)
{
	if ($type)
	{
		$GLOBALS["text"] = $GLOBALS["text"]."<tr><td class=\"tcenter\" width=\"30\"><img src=\"images/os_folder.gif\"></td><td>$str</td><td width=\"40\" class=\"tcenter text-yes\">".TEXT_YES."</td><td></td></tr>";
	}
	else
	{
		$GLOBALS["text"] = $GLOBALS["text"]."<tr><td class=\"tcenter\" width=\"30\"><img src=\"images/os_file.gif\"></td><td>$str</td><td width=\"40\" class=\"tcenter text-yes\">".TEXT_YES."</td><td></td></tr>";
	}
}

function text_no ($str, $type)
{
	if ($type)
	{
		$GLOBALS["text"] = $GLOBALS["text"]."<tr><td class=\"tcenter\" width=\"30\"><img src=\"images/os_folder.gif\"></td><td>$str</td><td width=\"40\" class=\"tcenter text-no\">".TEXT_NO."</td><td width=\"230\">Установите права доступа 777</td></tr>";
	}
	else
	{
		$GLOBALS["text"] = $GLOBALS["text"]."<tr><td class=\"tcenter\" width=\"30\"><img src=\"images/os_file.gif\"></td><td>$str</td><td width=\"40\" class=\"tcenter text-no\">".TEXT_NO."</td><td width=\"230\">Установите права доступа 777</td></tr>";
	}   
}

$messageStack = new messageStack();
$process = false;

if (isset($_POST['action']) && ($_POST['action'] == 'process')) 
{
	$process = true;
	$_SESSION['language'] = os_db_prepare_input($_POST['LANGUAGE']);
	$error = false;


	if ($error == false) 
	{
		os_redirect(os_href_link('1.php', '', 'NONSSL'));
	}
}


$error_flag=false;
$message='';
$ok_message='';
$text = ""; 
//Проверка прав доступа к файлам  
if (!is_writeable(_CATALOG.'config.php'))
{
	$error_flag=true; 
	text_no("config.php",false); 
} 
else 
{
	text_ok("config.php", false);
}


//if (is_file(_CATALOG.'htaccess.txt')) //проверка самого наличия и прав доступа к файлу htaccess.txt
//{
if (!is_writeable(_CATALOG.'htaccess.txt'))
{
	$error_flag=true;
	text_no("htaccess.txt",false);
}
else 
{
	text_ok("htaccess.txt",false);
}   
//} 

$status = TEXT_OK;

if ($error_flag==true) 
{ 
	$color='red'; 
} 
else 
{ 
	$color = 'green'; 
}

if ($error_flag == true) 
{
	$status='<span class="errorText">' . TEXT_ERROR . '</span>';
}

$ok_message.= "<font color=\"$color\"><b>".TEXT_FILE_PERMISSIONS.'</b></font> '.$status;
$folder_flag=false;

//Проверка прав доступа к папкам

if (!is_writeable(_CATALOG.'admin/backups/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("admin/backups/",true);
}
else 
{
	text_ok("admin/backups/", true);
}

if (!is_writeable(_CATALOG.'tmp/')) 
{
	$error_flag=true;
	$folder_flag=true;
	text_no("tmp/",true);
} 
else 
{
	text_ok("tmp/",true);
}

if (!is_writeable(_CATALOG.'cache/')) 
{
	$error_flag=true;
	$folder_flag=true;
	text_no("cache/", true);
} 
else 
{
	text_ok("cache/", true);
}

if (!is_writeable(_CATALOG.'cache/system/')) 
{
	$error_flag=true;
	$folder_flag=true;
	text_no("cache/system/", true);
} 
else 
{

	text_ok("cache/system/", true);
}

if (!is_writeable(_CATALOG.'images/attribute_images/')) 
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/attribute_images/", true);
} 
else 
{
	text_ok("images/attribute_images/", true);
}


if (!is_writeable(_CATALOG.'images/attribute_images/mini/')) 
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/attribute_images/mini/", true);
} 
else 
{
	text_ok("images/attribute_images/mini/", true);
}


if (!is_writeable(_CATALOG.'images/attribute_images/original/')) 
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/attribute_images/original/", true);
} 
else 
{
	text_ok("images/attribute_images/original/", true);
}

if (!is_writeable(_CATALOG.'images/attribute_images/thumbs/')) 
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/attribute_images/thumbs/", true);
} 
else 
{
	text_ok("images/attribute_images/thumbs/", true);
}


if (!is_writeable(_CATALOG.'media/export/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("media/export/", true);
}
else 
{
	text_ok("media/export/", true);
}   


if (!is_writeable(_CATALOG.'media/products/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("media/products/", true);
}
else 
{
	text_ok("media/products/", true);
}   

if (!is_writeable(_CATALOG.'media/import/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("media/import/", true);
}
else 
{
	text_ok("media/import/", true);
}   

if (!is_writeable(_CATALOG.'images/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/", true);
}
else 
{
	text_ok("images/", true);
}   

if (!is_writeable(_CATALOG.'images/categories/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/categories/", true);
} 
else 
{
	text_ok("images/categories/", true); 
}

if (!is_writeable(_CATALOG.'images/avatars/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/avatars/", true);
} 
else 
{
	text_ok("images/avatars/", true); 
}

if (!is_writeable(_CATALOG.'images/banner/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/banner/", true);
}
else 
{
	text_ok("images/banner/",true);
}   

if (!is_writeable(_CATALOG.'images/product_images/info_images/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/product_images/info_images/", true);
}
else 
{
	text_ok("images/product_images/info_images/", true);
}   

if (!is_writeable(_CATALOG.'images/product_images/original_images/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no('images/product_images/original_images/', true);
} 
else 
{
	text_ok('images/product_images/original_images/', true);
}

if (!is_writeable(_CATALOG.'images/product_images/popup_images/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/product_images/popup_images/", true);
}
else 
{
	text_ok("images/product_images/popup_images/", true);
}	

if (!is_writeable(_CATALOG.'images/product_images/thumbnail_images/'))
{
	$error_flag=true;
	$folder_flag=true;
	text_no("images/product_images/thumbnail_images/", true);
} 
else 
{
	text_ok("images/product_images/thumbnail_images/", true);
}

$status = TEXT_OK;
if ($folder_flag==true)
{
	$color='red'; 
} 
else 
{ 
	$color = 'green'; 
}

if ($error_flag==true & $folder_flag==true) 
{
	$status='<span class="errorText">' . TEXT_ERROR . '</span>';
}   

$ok_message.= "<br><font color=\"$color\"><b>" . TEXT_FOLDER_PERMISSIONS . '</b></font> ' . $status;

$php_flag=false;
if (os_check_version()!=1)
{
	$error_flag=true;
	$php_flag=true;
	$message .= PHP_VERSION_ERROR;
}

$status = TEXT_OK;

if ($php_flag==true) 
{
	@$color='red'; 
} 
else 
{ 
	@$color = 'green'; 
}

if ($php_flag==true) 
{
	$status='<span class="errorText">' . TEXT_ERROR . '</span>';
}

$ok_message.= "<br><font color=\"$color\"><b>" . TEXT_PHP_VERSION . ': </b>'.TEXT_OK.' ('.PHP_VERSION.')</font> ';

if (function_exists('gd_info'))
{
	$gd = gd_info();
	if (empty($gd['GD Version'])) 
	{
		$gd['GD Version']='<span class="errorText">пусто' . TEXT_GD_LIB_NOT_FOUND . '</span>';
	}

	$ok_message.= '<br><font color="green"><b>' . TEXT_GD_LIB_VERSION1 . ': </b> '.TEXT_OK.' ('.$gd['GD Version'].')</font>';
	if ($gd['GIF Read Support']==1 or $gd['GIF Support']==1) 
	{
		$status = TEXT_OK;
		$color = 'green';
	} 
	else 
	{
		$status = TEXT_GD_LIB_GIF_SUPPORT_ERROR;
		$color = 'red';
	}
	$ok_message.= "<br><font color=\"$color\"><b>" . TEXT_GD_LIB_GIF_SUPPORT . ':</b></font> ' . $status;
}
else
{
	$error_flag= true;
	$status='<span class="errorText">' . TEXT_ERROR . '</span>';
	$ok_message.= '<br><font color="red"><b>' . TEXT_GD_LIB_VERSION1 . ': </b></font>' .TEXT_ERROR;
	$ok_message.= "<br><font color=\"red\"><b>" . TEXT_GD_LIB_GIF_SUPPORT . ':</b></font> ' . $status;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_SETUP_INDEX; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<style type='text/css' media='all'>@import url('includes/install.css');</style>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<script src="bootstrap/jquery.js"></script>
<script src="bootstrap/js/bootstrap-transition.js"></script>
<script src="bootstrap/js/bootstrap-alert.js"></script>
<script src="bootstrap/js/bootstrap-modal.js"></script>
<script src="bootstrap/js/bootstrap-dropdown.js"></script>
<script src="bootstrap/js/bootstrap-scrollspy.js"></script>
<script src="bootstrap/js/bootstrap-tab.js"></script>
<script src="bootstrap/js/bootstrap-tooltip.js"></script>
<script src="bootstrap/js/bootstrap-popover.js"></script>
<script src="bootstrap/js/bootstrap-button.js"></script>
<script src="bootstrap/js/bootstrap-collapse.js"></script>
<script src="bootstrap/js/bootstrap-carousel.js"></script>
<script src="bootstrap/js/bootstrap-typeahead.js"></script>
</head>
<body>
<div class="container">
	<p></p>
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				<a class="brand" href=""><?php echo TEXT_SETUP_INDEX; ?></a>

				<?php echo lang_menu_index(); ?>
			</div>
		</div>
	</div>

	<!-- FORM START -->
	<form name="language" id="languag" method="post">
		<input type="hidden" name="LANGUAGE" id="lang_a" value="<?php echo $_SESSION['language']; ?>" />
		<input type="hidden" name="action" id="action" value="process" />
		<input type="hidden" name="task" value="" />

		<div class="page-header">

			<span class="pull-right">
				<a class="btn" href="index.php" title="<?php echo IMAGE_RETRY; ?>"><?php echo IMAGE_RETRY ; ?></a>
				<a class="btn btn-success" <?php if ($error_flag == false) { ?>onclick="document.language.submit();"<?php } ?>><?php echo IMAGE_CONTINUE;?></a>
			</span>

			<h1><?php echo STEPS ;?> <?php echo START; ?></h1>
		</div>

		<div class="progress progress-striped active">
			<div class="bar" style="width:12.5%;"></div>
		</div>

		<div class="row-fluid">
			<div class="span3">
				<div class="well well-small"><?php echo TEXT_INSTALL_INDEX; ?></div>
				<div class="well well-small">
					<h4><?php echo TEXT_CHECKING; ?></h4>
					<br />
					<?php				
					if ($error_flag == true)
					{
						echo "$message".'<br />';
					}

					if ($ok_message != '')
					{
						echo $ok_message;
					}

					if ($messageStack->size('index') > 0)
					{
						echo $messageStack->output('index');
					}
					?>
			</div>
			</div>

			<div class="span9">
				<table class="table table-bordered table-striped">
					<?php echo "$text"; ?>
				</table>
			</div>

		</div>

		<span class="pull-right">
			<a class="btn" href="index.php" title="<?php echo IMAGE_RETRY; ?>"><?php echo IMAGE_RETRY ; ?></a>
			<a class="btn btn-success" <?php if ($error_flag == false) { ?>onclick="document.language.submit();"<?php } ?>><?php echo IMAGE_CONTINUE;?></a>
		</span>
		<div class="clear"></div>

	</form>
	<!-- FORM END -->

	<hr>

	<footer>
		<p><?php echo _copy(); ?></p>
	</footer>
</div>
</body>
</html>