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
require(_CLASS.'price.php');

$osPrice = new osPrice($_SESSION['currency'],''); 
switch ( @$_GET['set'] ) 
{
	case 'shipping':
		$module_type = 'shipping';
		$module_directory = _MODULES.'shipping/';
		$module_key = 'MODULE_SHIPPING_INSTALLED';
	break;

	case 'ordertotal':
		$module_type = 'order_total';
		$module_directory = _MODULES. 'order_total/';
		$module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
	break;

	case 'payment':
	default:
		$module_type = 'payment';
		$module_directory = _MODULES. 'payment/';
		$module_key = 'MODULE_PAYMENT_INSTALLED';
		if (isset($_GET['error'])) 
		{
			$messageStack->add($_GET['error'], 'error');
		}
	break;
}

switch (@$_GET['action']) 
{
	case 'save':
		while (list($key, $value) = each($_POST['configuration']))
		{
			if ($key == "MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER") 
			{
				$value = str_replace(" ", "", $value);
				if ($value == "")
					$value ="0";
			}

			os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".$value."' where configuration_key = '".$key."'");
		}
		///set_configuration_cache(); 
		os_redirect(os_href_link(FILENAME_MODULES, 'set='.$_GET['set'].'&module='.$_GET['module']));
	break;

	case 'install':
	case 'remove':
		//$file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
		$class = basename($_GET['module']);
		if (file_exists($module_directory.$_GET['module'].'/'.$_GET['module'].'.php')) 
		{
			include($module_directory.$_GET['module'].'/'.$_GET['module'].'.php');
			$module = new $class(0);
			if ($_GET['action'] == 'install')
				$module->install();
			elseif ($_GET['action'] == 'remove')
				$module->remove();
		}
		///set_configuration_cache(); 
		os_redirect(os_href_link(FILENAME_MODULES, 'set='.$_GET['set'].'&module='.$class));
	break;
}

$breadcrumb->add(HEADING_TITLE, os_href_link(FILENAME_MODULES, 'set='.$_GET['set']));

// Редактирование модуля
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['module']))
{
	$file = $_GET['module'];

	if (is_file($module_directory.$file.'/'.$file.'.php'))
	{
		include($module_directory.$file.'/'.$file.'.php');
	}

	if (is_file($module_directory.$file.'/'.$_SESSION['language_admin'].'.php'))
	{
		include($module_directory.$file.'/'.$_SESSION['language_admin'].'.php');
	}   
	else
	{
		if (is_file($module_directory.$file.'/ru.php'))
		{
			include($module_directory.$file.'/ru.php');
		}
	}

	$class = $file;
	$module = new $class();

	$module_info = array(
		'code' => $module->code,
		'title' => $module->title,
		'description' => $module->description,
		'status' => $module->check()
	);

	$module_keys = $module->keys();

	$keys_extra = array();
	for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++)
	{
		$key_value_query = os_db_query("select configuration_key,configuration_value, use_function, set_function from ".TABLE_CONFIGURATION." where configuration_key = '".$module_keys[$j]."'");
		$key_value = os_db_fetch_array($key_value_query);

		if ($key_value['configuration_key'] !='')
			$keys_extra[$module_keys[$j]]['title'] = constant(strtoupper($key_value['configuration_key'] .'_TITLE'));

		$keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
		if ($key_value['configuration_key'] !='')
			$keys_extra[$module_keys[$j]]['description'] = constant(strtoupper($key_value['configuration_key'] .'_DESC'));

		$keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
		$keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
	}

	$module_info['keys'] = $keys_extra;

	$mInfo = new objectInfo($module_info);
	
	$breadcrumb->add($mInfo->title, os_href_link(FILENAME_MODULES, 'set='.$_GET['set'].'&module='.$_GET['module'].'&action=save'));
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['module'])) { ?>

	<form name="modules" action="<?php echo os_href_link(FILENAME_MODULES, 'set='.$_GET['set'].'&module='.$_GET['module'].'&action=save'); ?>" method="post">
		<?php
		reset($mInfo->keys);
		while (list($key, $value) = each($mInfo->keys))
		{ ?>
		<div class="control-group">
			<label class="control-label" for="file_comment"><?php echo $value['title']; ?></label>
			<div class="controls">
				<?php
				if ($value['set_function'])
					eval('$keys = '.$value['set_function']."'".$value['value']."', '".$key."');");
				else
					$keys = os_draw_input_field('configuration['.$key.']', $value['value'], 'class="input-block-level"');
				echo $keys;
				?>
				<span class="help-block"><?php echo $value['description']; ?></span>
			</div>
		</div>
		<?php } ?>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_UPDATE; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_MODULES, 'set='.$_GET['set']); ?>"><?php echo BUTTON_BACK; ?></a>
		</div>

	</form>

<?php } else { ?>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th></th>
			<th><?php echo TABLE_HEADING_ADDONS; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_FILENAME; ?></th>
			<?php //echo TABLE_HEADING_STATUS; ?>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
<?php
$file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
$directory_array = array();
if ($dir = @dir($module_directory)) 
{
	while ($file = $dir->read())
	{
		if (is_dir($module_directory.$file) && $file != '.' && $file != '..' && $file != '.svn')
		{
			$directory_array[] = $file;
		}
	}
	sort($directory_array);
	$dir->close();
}

$installed_modules = array();
for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) 
{
	$file = $directory_array[$i];

	// include(DIR_FS_LANGUAGES.$_SESSION['language_admin'].'/modules/'.$module_type.'/'.$file.'.php');
	if (is_file($module_directory.$file.'/'.$file.'.php'))
	{
		include($module_directory.$file.'/'.$file.'.php');
	}

	if (is_file($module_directory.$file.'/'.$_SESSION['language_admin'].'.php'))
	{
		include($module_directory.$file.'/'.$_SESSION['language_admin'].'.php');
	}   
	else
	{
		if (is_file($module_directory.$file.'/ru.php'))
		{
			include($module_directory.$file.'/ru.php');
		}
	}

	$class = $file;
	if (os_class_exists($class))
	{
		$module = new $class();
		if ($module->check() > 0)
		{
			if ($module->sort_order > 0)
			{
				if ($installed_modules[$module->sort_order] != '')
				{
					$zc_valid = false;
				}
				$installed_modules[$module->sort_order] = $file;
			}
			else
				$installed_modules[] = $file;
		}
		?>
		<tr>
		<td>
			<?php 
			if ($module_type == 'payment')
			{
				if (isset($module->icon_small) && is_file($module_directory.$module->code.'/'.$module->icon_small))
					echo '<img width="16px" height="16px" src="'.http_path('modules').$module_type.'/'.$module->code.'/'.$module->icon_small.'" border="0" />';
				else
					echo '';
			}
			?>
		</td>
		<td><?php echo $module->title; ?></td>
		<td><?php echo str_replace('.php', '', $file); ?></td>
		<td width="100">
			<div class="pull-right">
				<div class="btn-group">
					<?php if ($module->check() > 0) { ?>
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_MODULES, 'set='.$_GET['set'].'&module='.$class.'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<?php } ?>
				</div>
				<?php if (is_numeric($module->sort_order)) { ?>
					<a class="btn btn-mini btn-danger" onclick="return confirm('<?php echo BUTTON_MODULE_REMOVE; ?>');" href="<?php echo FILENAME_MODULES.'?action=remove&module='.str_replace('.php','',$file).'&set='.$_GET['set']; ?>" title="<?php echo IMAGE_ICON_STATUS_RED_LIGHT; ?>"><?php echo BUTTON_MODULE_REMOVE; ?></a>
				<?php } else { ?>
					<a class="btn btn-mini" href="<?php echo FILENAME_MODULES.'?'.'action=install&module='.str_replace('.php','',$file).'&set='.$_GET['set']; ?>" title="<?php echo IMAGE_ICON_STATUS_GREEN_LIGHT; ?>"><?php echo BUTTON_MODULE_INSTALL; ?></a>
				<?php } ?>
			</div>
		</td>
		</tr>
		<?php
	}
}
?>
</table>
<?php
ksort($installed_modules);
$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = '".$module_key."'");
if (os_db_num_rows($check_query))
{
	$check = os_db_fetch_array($check_query);
	if ($check['configuration_value'] != implode(';', $installed_modules))
	{
		os_db_query("update ".TABLE_CONFIGURATION." set configuration_value = '".implode('.php;', $installed_modules).".php', last_modified = now() where configuration_key = '".$module_key."'");
	}
}
else
{
	os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ( '".$module_key."', '".implode(';', $installed_modules)."','6', '0', now())");
}
if (isset($zc_valid) && $zc_valid == false)
{
	$messageStack->add_session(WARNING_MODULES_SORT_ORDER, 'error');
}  
?>

<?php } ?>

<?php $main->bottom(); ?>