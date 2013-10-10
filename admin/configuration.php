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

if (isset($_GET['action'])) 
{
	switch ($_GET['action']) 
	{
		case 'save':
			$configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from ".TABLE_CONFIGURATION." where configuration_group_id = '".(int)$_GET['gID']."' order by sort_order");
			while ($configuration = os_db_fetch_array($configuration_query))
				os_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");
			
			os_redirect(FILENAME_CONFIGURATION. '?gID='.(int)$_GET['gID']);
		break;
	}
}

$breadcrumb->add(BOX_CONFIGURATION.": ".HEAD_T);

$main->head();
$main->top_menu();
?>

<a class="btn btn-mini btn-success pull-right" onclick="document.configuration.submit()" href="#"><?php echo BUTTON_SAVE; ?></a>

<?php
if (empty($_GET['gID'])) 
{
	$cfg_group_query = os_db_query("select configuration_group_key, configuration_group_id from ".TABLE_CONFIGURATION_GROUP.'  order by sort_order;');

	while ( $cfg_group = os_db_fetch_array($cfg_group_query) )
	{
		$_group[ $cfg_group['configuration_group_id'] ] = $cfg_group['configuration_group_key'];
		$_group_id_array[] =  $cfg_group['configuration_group_id'];
		$group_array = implode (',', $_group_id_array);
	}
	?>
	<div id="tabs">
		<ul>
			<?php
			foreach ($_group as $group_id => $group_key)
			{
				if (defined(strtoupper($group_key).'_TITLE'))
				{
					echo '<li><a href="#с'.$group_id.'">'. constant(strtoupper($group_key).'_TITLE').'</a></li>';
				}
			}
			?>
		</ul>
		<?php
		$_query = os_db_query("select configuration_id, configuration_key, configuration_value, configuration_group_id, set_function from ".DB_PREFIX."configuration where configuration_group_id in (".$group_array.") order by sort_order;");

		while ( $_query_value = os_db_fetch_array($_query, false) )
		{
			$value[ $_query_value['configuration_group_id'] ] []  = $_query_value;
		}  

		foreach ($value as $group_id => $group_value)
		{
			echo '<div id="с'.$group_id.'">';
			echo '<table width="100%" border="0">'; 
			$color = '';     

			foreach ($group_value as $value)
			{
				$__title = '';

				if (defined(strtoupper($value['configuration_key'].'_TITLE')))
				{
					$__title = constant(strtoupper($value['configuration_key'].'_TITLE'));
				}

				$__desc = '';

				if (defined(strtoupper($value['configuration_key'].'_DESC')))
				{
					$__desc = constant(strtoupper($value['configuration_key'].'_DESC'));
				}
				if (os_not_null($value['use_function'])) 
				{
					$use_function = $value['use_function'];
					if (preg_match('/->/', $use_function))
					{
						$class_method = explode('->', $use_function);
						if (!is_object(${$class_method[0]}))
						{
							include(get_path('class_admin').$class_method[0].'.php');
							${$class_method[0]} = new $class_method[0]();
						}
						$cfgValue = os_call_function($class_method[1], $value['configuration_value'], ${$class_method[0]});
					}
					else
					{
						$cfgValue = os_call_function($use_function, $value['configuration_value']);
					}
				}
				else
				{
					$cfgValue = $value['configuration_value'];
				}

				if ($value['set_function']) 
				{
					eval('$value_field = '.$value['set_function'].'"'.htmlspecialchars($value['configuration_value']).'");');
				} 
				else 
				{
					$value_field = os_draw_input_field($value['configuration_key'], $value['configuration_value'],'size="15" class="round"');
				}
				echo '<tr>';
				echo '<td>'.$value_field.'</td>';
				echo '<td><b>'.$__title.'</b><br>'.$__desc.'</td></tr>';
				echo '</tr>';
			}
			echo '</table></div>';
			//print_r($group_value);
		}
		?>

</div>	
<?php } ?>

<?php echo os_draw_form('configuration', FILENAME_CONFIGURATION, 'gID='.(int)$_GET['gID'].'&action=save'); ?>

	<table class="table">
	<?php
	$configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from ".TABLE_CONFIGURATION." where configuration_group_id = '".(int)$_GET['gID']."' order by sort_order");
	while ($configuration = os_db_fetch_array($configuration_query))
	{
		if ($_GET['gID'] == 6)
		{
			switch ($configuration['configuration_key'])
			{
				case 'MODULE_PAYMENT_INSTALLED':
					if ($configuration['configuration_value'] != '')
					{
						$payment_installed = explode(';', $configuration['configuration_value']);
						for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++)
						{
							include(_MODULES.'payment/'.substr($payment_installed[$i], 0, strrpos($payment_installed[$i], '.')).'/'.$_SESSION['language'].'.php');
						}
					}
				break;

				case 'MODULE_SHIPPING_INSTALLED':
					if ($configuration['configuration_value'] != '')
					{
						$shipping_installed = explode(';', $configuration['configuration_value']);
						for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++)
						{
							include(_MODULES.'/shipping/'.substr($shipping_installed[$i], 0, strrpos($shipping_installed[$i], '.')).'/'.$_SESSION['language'].'.php');                       
						}
					}
				break;

				case 'MODULE_ORDER_TOTAL_INSTALLED':
					if ($configuration['configuration_value'] != '')
					{
						$ot_installed = explode(';', $configuration['configuration_value']);
						for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++)
						{
							include(_MODULES.'/order_total/'. substr($ot_installed[$i], 0, strrpos($ot_installed[$i], '.')).'/'.$_SESSION['language'].'.php');                      
						}
					}
				break;
			}
		}

		if (os_not_null($configuration['use_function']))
		{
			$use_function = $configuration['use_function'];
			if (preg_match('/->/', $use_function))
			{
				$class_method = explode('->', $use_function);
				if (!is_object(${$class_method[0]}))
				{
					include(get_path('class_admin').$class_method[0].'.php');
					${$class_method[0]} = new $class_method[0]();
				}
				$cfgValue = os_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
			}
			else
				$cfgValue = os_call_function($use_function, $configuration['configuration_value']);
		}
		else
			$cfgValue = $configuration['configuration_value'];

		if (isset($_GET['cID']))
		{
			if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new'))
			{
				$cfg_extra_query = os_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from ".TABLE_CONFIGURATION." where configuration_id = '".$configuration['configuration_id']."'");
				$cfg_extra = os_db_fetch_array($cfg_extra_query);
				$cInfo_array = os_array_merge($configuration, $cfg_extra);
				$cInfo = new objectInfo($cInfo_array);
			}
		}

		if ($configuration['set_function']) 
		{
			eval('$value_field = '.$configuration['set_function'].'"'.htmlspecialchars($configuration['configuration_value']).'");');
		} 
		else 
		{
			$value_field = os_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size="15" class="round"');
		}

		// add
		$chet = 1;

		if (strstr($value_field,'configuration_value')) $value_field = str_replace('configuration_value',$configuration['configuration_key'],$value_field);
		{
			$__title = '';

			if (defined(strtoupper($configuration['configuration_key'].'_TITLE')))
			{
				$__title = constant(strtoupper($configuration['configuration_key'].'_TITLE'));
			}

			$__desc = '';

			if (defined(strtoupper($configuration['configuration_key'].'_DESC')))
			{
				$__desc = constant(strtoupper($configuration['configuration_key'].'_DESC'));
			}

			echo '<tr>
				<td width="200">'.$value_field.'</td>
				<td><b>'.$__title.'</b><br>'.$__desc.'</td>
			</tr>';
		}
	}
	?>
	</table>

	<hr>
	<div class="tcenter footer-btn">
		<a class="btn btn-success" onclick="document.configuration.submit()" href="#"><?php echo BUTTON_SAVE; ?></a>
		<?php if (is_file(dir_path('catalog').'admin/includes/pages/default/sql/configuration_'.(int)$_GET['gID'].'.php')) { ?>
		<a class="btn btn-danger" onClick="return confirm('Установить настройки по умолчанию?')" href="index.php?action=default&name=configuration&param=<?php echo $_GET['gID']; ?>"><?php echo BUTTON_DEFAULT;  ?></a>
		<?php } ?>
	</div>

</form>
<?php $main->bottom(); ?>