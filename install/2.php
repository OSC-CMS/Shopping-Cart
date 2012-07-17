<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('includes/top.php');
if (!isset($_SESSION['language']) )
{
   $_SESSION['language'] = 'ru';
}   

if (isset($_POST['LANGUAGE']))
{
   $_SESSION['language'] = $_POST['LANGUAGE'];
}
if (isset($_POST['DB_PREFIXX']) && !empty($_POST['DB_PREFIXX']))
{
    $_SESSION['DB_PREFIX'] = $_POST['DB_PREFIXX'].'_';
}
else
{
    $_SESSION['DB_PREFIX'] = $_POST['DB_PREFIXX'];
}

include('lang/'.$_SESSION['language'].'/lang.php');

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

				<?php echo lang_menu(); ?>
			</div>
		</div>
	</div>

	<form action="" method="post" name="language">
	<input type="hidden" name="LANGUAGE" id="lang_a" value="" />
	</form>

	<div class="page-header">

		<span class="pull-right">
			<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
			<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE;?>"><?php echo IMAGE_CONTINUE;?></a>
		</span>

		<h1><?php echo STEPS ;?> <?php echo STEP2; ?></h1>
	</div>

	<div class="progress progress-striped active">
		<div class="bar" style="width:37.5%;"></div>
	</div>


<!-- echo TITLE_STEP2; ?>

< echo TEXT_WELCOME_STEP2;-->

<?php
if (os_in_array('database', $_POST['install']))
{   
	$db = array();
	$db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
	$db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
	$db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
	$db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));
	$db_error = false;  
	os_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

	if (!$db_error) 
	{
		db_test_create_db_permission($db['DB_DATABASE']);
	}

	if ($db_error) 
	{
		?>
		<div class="well tcenter bold"><?php echo TEXT_CONNECTION_ERROR; ?></div>

		<h3><?php echo TEXT_DB_ERROR; ?></h3>

		<blockquote class="text-no"><?php echo $db_error; ?></blockquote>

		<p><?php echo TEXT_DB_ERROR_1; ?></p>
		<p class="text-no"><?php echo TEXT_DB_ERROR_2; ?></p>

		<form name="install" action="1.php" method="post">
		<?php
		reset($_POST);
		while (list($key, $value) = each($_POST)) 
		{
			if ($key != 'x' && $key != 'y') 
			{
				if (is_array($value)) 
				{
					for ($i=0; $i<sizeof($value); $i++) 
					{
						echo os_draw_hidden_field_installer($key . '[]', $value[$i]);
					}
				} 
				else 
				{
					echo os_draw_hidden_field_installer($key, $value);
				}
			}
		}
		?>
		</form>
		<?php
	}
	else
	{
	?>
		<h3><?php echo TEXT_CONNECTION_SUCCESS; ?></h3>
		<hr>
		<p><?php echo TEXT_PROCESS_1; ?></p>
		<p><?php echo TEXT_PROCESS_2; ?></p>

		<form name="install" action="3.php" method="post">
			<hr>
			<p>
				<span class="pull-left"><input type="checkbox" name="OS_TEST_BASE" id="OS_TEST_BASE" checked></span>
				<span class="pull-left" style="padding-left:5px;"><label for="OS_TEST_BASE" class="bold"><?php echo STEP2_TEST;?></label></span>
			</p>

			<?php
			reset($_POST);
			while (list($key, $value) = each($_POST)) 
			{
				if ($key != 'x' && $key != 'y') 
				{
					if (is_array($value)) 
					{
						for ($i=0; $i<sizeof($value); $i++) 
						{
							echo os_draw_hidden_field_installer($key . '[]', $value[$i]);
						}
					} 
					else 
					{
						echo os_draw_hidden_field_installer($key, $value);
					}
				}
			}
			?>
			<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
}	  
?>

	<span class="pull-right">
		<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
		<a class="btn btn-success" onclick="document.install.submit();" title="<?php echo IMAGE_CONTINUE;?>"><?php echo IMAGE_CONTINUE;?></a>
	</span>
	<div class="clear"></div>

	<hr>

	<footer>
		<p><?php echo _copy(); ?></p>
	</footer>
</div>
</body>
</html>