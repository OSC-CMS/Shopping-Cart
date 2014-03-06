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

if ($_GET['action']) 
{
	switch ($_GET['action'])
	{
		case 'save':
			$admin_access_query = os_db_query("select * from ".TABLE_ADMIN_ACCESS." where customers_id = '".(int)$_GET['cID']."'");
			$admin_access = os_db_fetch_array($admin_access_query);

			$fields = mysql_list_fields(DB_DATABASE, TABLE_ADMIN_ACCESS);
			$columns = mysql_num_fields($fields);

			for ($i = 0; $i < $columns; $i++)
			{
				$field = mysql_field_name($fields, $i);
				if ($field != 'customers_id')
				{
					os_db_query("UPDATE ".TABLE_ADMIN_ACCESS." SET `".$field."` = 0 where customers_id = '".(int)$_GET['cID']."'");
				}
			}

			$access_ids = '';
			if (isset($_POST['access']))
			{
				foreach($_POST['access'] as $key)
				{
					os_db_query("UPDATE ".TABLE_ADMIN_ACCESS." SET `".$key."` = 1 where customers_id = '".(int)$_GET['cID']."'");
				}
			}

			os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID='.(int)$_GET['cID'], 'NONSSL'));
		break;
	}
}

if ($_GET['cID'] != '')
{
	if ($_GET['cID'] == 1)
	{
		os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
	}
	else
	{
		$allow_edit_query = os_db_query("select customers_status, customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." where customers_id = '" .(int)$_GET['cID']."'");
		$allow_edit = os_db_fetch_array($allow_edit_query);
		if ($allow_edit['customers_status'] != 0 || $allow_edit == '')
		{
			os_redirect(os_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
		}
	}
}

$breadcrumb->add(TEXT_ACCOUNTING.' '.$allow_edit['customers_lastname'].' '.$allow_edit['customers_firstname'], TABLE_ADMIN_ACCESS);

$main->head();
$main->top_menu();
?>

<h5><?php echo TXT_GROUPS; ?></h5>

<table class="table">
	<tr>
		<td width="30" bgcolor="FF6969">&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100%" class="main"><?php echo TXT_SYSTEM; ?></td>
	</tr>
	<tr>
		<td width="30" bgcolor="69CDFF" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100%" class="main"><?php echo TXT_PRODUCTS; ?></td>
	</tr>
	<tr>
		<td width="30" bgcolor="6BFF7F" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100%" class="main"><?php echo TXT_CUSTOMERS; ?></td>
	</tr>
	<tr>
		<td width="10" bgcolor="BFA8FF" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100%" class="main"><?php echo TXT_STATISTICS; ?></td>
	</tr>
	<tr>
		<td width="10" bgcolor="FFE6A8" >&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100%" class="main"><?php echo TXT_TOOLS; ?></td>
	</tr>
</table>

<hr/>

<form name="accounting" action="<?php echo os_href_link(FILENAME_ACCOUNTING, 'cID='.$_GET['cID'].'&action=save'); ?>" method="post" enctype="multipart/form-data">
	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th width="30px"><input name="check_all" type="checkbox" onClick="javascript:SwitchCheckAccounting();"></th>
				<th width="60px"></th>
				<th><span class="line"></span><?php echo TEXT_ACCESS; ?></th>
				<th><span class="line"></span><?php echo TEXT_ALLOWED; ?></th>
			</tr>
		</thead>
	<?php
	$admin_access='';
	$customers_id = os_db_prepare_input($_GET['cID']);
	$admin_access_query = os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
	$admin_access = os_db_fetch_array($admin_access_query);

	$group_query = os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = 'groups'");
	$group_access = os_db_fetch_array($group_query);
	if ($admin_access == '')
	{
		os_db_query("INSERT INTO " . TABLE_ADMIN_ACCESS . " (customers_id) VALUES ('" . (int)$_GET['cID'] . "')");
		$admin_access_query = os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
		$group_query=os_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = 'groups'");
		$group_access = os_db_fetch_array($admin_access_query);
		$admin_access = os_db_fetch_array($admin_access_query);
	}

	$fields = mysql_list_fields(DB_DATABASE, TABLE_ADMIN_ACCESS);
	$columns = mysql_num_fields($fields);

	$color = '';
	for ($i = 0; $i < $columns; $i++)
	{
		$field=mysql_field_name($fields, $i);
		if ($field!='customers_id')
		{
			$checked='';
			if ($admin_access[$field] == '1') $checked='checked';

			// colors
			switch ($group_access[$field])
			{
				case '1':
					$color='#FF6969';
				break;
				case '2':
					$color='#69CDFF';
				break;
				case '3':
					$color='#6BFF7F';
				break;
				case '4':
					$color='#BFA8FF';
				break;
				case '5':
					$color='#FFE6A8';
			}

			$access_name = '';
			$access_name_c = 'ACCESS_'.strtoupper($field); //constant()

			if (defined($access_name_c))
			{
				$access_name = constant($access_name_c);
			}
			?>
			<tr>
				<td><input type="checkbox" name="access[]" value="<?php echo $field; ?>" <?php echo $checked; ?>></td>
				<td style="background-color:<?php echo $color; ?>;border: 1px solid <?php echo $color; ?>;  " >&nbsp;</td>
				<td><b><?php echo $field; ?></b></td>
				<td width="100%"><?php echo $access_name; ?></td>
			</tr>
			<?php
		}
	}
	?>
	</table>


	<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success" type="submit" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')" value="<?php echo BUTTON_SAVE; ?>">
		<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CUSTOMERS); ?>"><?php echo BUTTON_CANCEL; ?></a>
	</div>
</form>
<?php $main->bottom(); ?>