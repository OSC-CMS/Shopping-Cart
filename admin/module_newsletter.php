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
//require_once(_FUNC_ADMIN.'wysiwyg_tiny.php');
require _LIB . 'phpmailer/PHPMailerAutoload.php';
include_once (_LIB.'phpmailer/func.mail.php');

$customers_status=os_get_customers_statuses();

switch (@$_GET['action'])
{
	case 'save': 

		$id = os_db_prepare_input((int)$_POST['ID']);
		$status_all = os_db_prepare_input(@$_POST['status_all']);
		if (@$newsletter_title=='') $newsletter_title='no title';

		$rzp='';
		for ($i=0,$n=sizeof($customers_status);$i<$n; $i++) {
		if (os_db_prepare_input(@$_POST['status'][$i])=='yes') {
		if ($rzp!='') $rzp.=',';
		$rzp.=$customers_status[$i]['id'];
		}
		}

		if (os_db_prepare_input(@$_POST['status_all'])=='yes') $rzp.=',all';

		$error=false;
		if ($error == false) {

		$sql_data_array = array( 'title'=> os_db_prepare_input($_POST['title']),
		'status' => '0',
		'bc'=>$rzp,
		'cc'=>os_db_prepare_input($_POST['cc']),
		'date' => 'now()',
		'body' => os_db_prepare_input($_POST['newsletter_body']));

		if ($id!='') {
		os_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array, 'update', "newsletter_id = '".$id."'");
		os_db_query("DROP TABLE IF EXISTS ".TABLE_NEWSLETTER_TEMP.$id);
		os_db_query("CREATE TABLE ".TABLE_NEWSLETTER_TEMP.$id."
		(
		id int(11) NOT NULL auto_increment,
		customers_id int(11) NOT NULL default '0',
		customers_status int(11) NOT NULL default '0',
		customers_firstname varchar(64) NOT NULL default '',
		customers_lastname varchar(64) NOT NULL default '',
		customers_email_address text NOT NULL,
		mail_key varchar(32) NOT NULL,
		date datetime NOT NULL default '0000-00-00 00:00:00',
		comment varchar(64) NOT NULL default '',
		PRIMARY KEY  (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");
		} else {
		os_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array);
		$id=os_db_insert_id();
		os_db_query("DROP TABLE IF EXISTS ".TABLE_NEWSLETTER_TEMP.$id);
		os_db_query("CREATE TABLE ".TABLE_NEWSLETTER_TEMP.$id."
		(
		id int(11) NOT NULL auto_increment,
		customers_id int(11) NOT NULL default '0',
		customers_status int(11) NOT NULL default '0',
		customers_firstname varchar(64) NOT NULL default '',
		customers_lastname varchar(64) NOT NULL default '',
		customers_email_address text NOT NULL,
		mail_key varchar(32) NOT NULL,
		date datetime NOT NULL default '0000-00-00 00:00:00',
		comment varchar(64) NOT NULL default '',
		PRIMARY KEY  (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");
		}

		$flag='';
		if (!strpos($rzp,'all')) $flag='true';
		$rzp=str_replace(',all','',$rzp);
		$groups=explode(',',$rzp);
		$sql_data_array='';

		for ($i=0,$n=sizeof($groups);$i<$n;$i++) {

		if (os_db_prepare_input(@$_POST['status_all'])=='yes') {
		$customers_query=os_db_query("SELECT
		customers_id,
		customers_firstname,
		customers_lastname,
		customers_email_address
		FROM ".TABLE_CUSTOMERS."
		WHERE
		customers_status='".$groups[$i]."'");
		} else {
		$customers_query=os_db_query("SELECT
		customers_email_address,
		customers_id,
		customers_firstname,
		customers_lastname,
		mail_key        
		FROM ".TABLE_NEWSLETTER_RECIPIENTS."
		WHERE
		customers_status='".$groups[$i]."' and
		mail_status='1'");
		}
		while ($customers_data=os_db_fetch_array($customers_query)){
		$sql_data_array=array(
		'customers_id'=>$customers_data['customers_id'],
		'customers_status'=>$groups[$i],
		'customers_firstname'=>$customers_data['customers_firstname'],
		'customers_lastname'=>$customers_data['customers_lastname'],
		'customers_email_address'=>$customers_data['customers_email_address'],
		'mail_key'=>$customers_data['mail_key'],
		'date'=>'now()');

		os_db_perform(TABLE_NEWSLETTER_TEMP.$id, $sql_data_array);
		}


		}

		os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER));
		}

	break;

	case 'delete':

		os_db_query("DELETE FROM ".TABLE_MODULE_NEWSLETTER." WHERE   newsletter_id='".(int)$_GET['ID']."'");
		os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER));

	break;

	case 'send':
		$package_size='30';
		os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER,'send=0,'.$package_size.'&ID='.(int)$_GET['ID']));
	break;
}

if (@$_GET['send'])
{
	$limits = explode(',',$_GET['send']);
	$limit_low = $limits['0'];
	$limit_up = $limits['1'];

	$limit_query = os_db_query("SELECT count(*) as count FROM ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']."");
	$limit_data = os_db_fetch_array($limit_query);

	$email_query=os_db_query("SELECT customers_firstname, customers_lastname, customers_email_address, mail_key, id FROM ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']." LIMIT ".$limit_low.",".$limit_up);

	$email_data = array();
	while ($email_query_data=os_db_fetch_array($email_query))
	{
		$email_data[] = array(
			'id' => $email_query_data['id'],
			'firstname'=>$email_query_data['customers_firstname'],
			'lastname'=>$email_query_data['customers_lastname'],
			'email'=>$email_query_data['customers_email_address'],
			'key'=>$email_query_data['mail_key']
		);
	}

	$package_size='30';
	$break='0';
	if ($limit_data['count']<$limit_up)
	{
		$limit_up=$limit_data['count'];
		$break='1';
	}
	$max_runtime=$limit_up-$limit_low;
	$newsletters_query = os_db_query("SELECT title, body, bc, cc FROM ".TABLE_MODULE_NEWSLETTER." WHERE newsletter_id='".(int)$_GET['ID']."'");
	$newsletters_data = os_db_fetch_array($newsletters_query);

	for ($i=1;$i<=$max_runtime;$i++)
	{
		$link1 = chr(13).chr(10).chr(13).chr(10).TEXT_NEWSLETTER_REMOVE.chr(13).chr(10).chr(13).chr(10).HTTP_CATALOG_SERVER.DIR_WS_CATALOG.FILENAME_CATALOG_NEWSLETTER.'?action=remove&email='.$email_data[$i-1]['email'].'&key='.$email_data[$i-1]['key'];

		$link2 = $link2 = '<br /><br /><hr>'.TEXT_NEWSLETTER_REMOVE.'<br /><a href="'.HTTP_CATALOG_SERVER.DIR_WS_CATALOG.FILENAME_CATALOG_NEWSLETTER.'?action=remove&email='.$email_data[$i-1]['email'].'&key='.$email_data[$i-1]['key'].'">'.TEXT_REMOVE_LINK.'</a>';

		os_php_mail(
			EMAIL_SUPPORT_ADDRESS,
			EMAIL_SUPPORT_NAME,
			$email_data[$i-1]['email'] ,
			$email_data[$i-1]['lastname'].' '.$email_data[$i-1]['firstname'] ,
			'',
			EMAIL_SUPPORT_REPLY_ADDRESS,
			EMAIL_SUPPORT_REPLY_ADDRESS_NAME,
			'',
			'',
			$newsletters_data['title'],
			$newsletters_data['body'].$link2,
			$newsletters_data['body'].$link1
		);

		os_db_query("UPDATE ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']." SET comment='send' WHERE id = '".$email_data[$i-1]['id']."'");
	}

	if ($break=='1')
	{
		$limit1_query=os_db_query("SELECT count(*) as count FROM ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']." WHERE comment='send'");
		$limit1_data=os_db_fetch_array($limit1_query);

		if ($limit1_data['count']-$limit_data['count']<=0)
		{
			os_db_query("UPDATE ".TABLE_MODULE_NEWSLETTER." SET status='1' WHERE newsletter_id='".(int)$_GET['ID']."'");
			os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER));
		}
		else
		{
			echo '<b>'.$limit1_data['count'].'<b> emails send<br />';
			echo '<b>'.$limit1_data['count']-$limit_data['count'].'<b> emails left';
		}
	}
	else
	{
		$limit_low = $limit_up+1;
		$limit_up = $limit_low+$package_size;
		os_redirect(os_href_link(FILENAME_MODULE_NEWSLETTER,'send='.$limit_low.','.$limit_up.'&ID='.(int)$_GET['ID']));
	}
}

$breadcrumb->add(HEADING_TITLE, FILENAME_MODULE_NEWSLETTER);

if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$newsletters_query = os_db_query("SELECT title,body,cc,bc FROM ".TABLE_MODULE_NEWSLETTER." WHERE newsletter_id='".(int)$_GET['ID']."'");
	$newsletters_data = os_db_fetch_array($newsletters_query);

	$breadcrumb->add(TEXT_EDIT.': '.$newsletters_data['title'], FILENAME_MODULE_NEWSLETTER);
}
elseif (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(BUTTON_NEW_NEWSLETTER, FILENAME_MODULE_NEWSLETTER);
}

$main->head();
$main->top_menu();
?>

<?php
switch (@$_GET['action'])
{
	default:
		$customer_group_query=os_db_query("SELECT customers_status_name, customers_status_id, customers_status_image FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id='".$_SESSION['languages_id']."'");
		$customer_group=array();
		while ($customer_group_data=os_db_fetch_array($customer_group_query))
		{
			$group_query=os_db_query("SELECT count(*) as count FROM ".TABLE_NEWSLETTER_RECIPIENTS." WHERE mail_status='1' and customers_status='".$customer_group_data['customers_status_id']."'");
			$group_data=os_db_fetch_array($group_query);

			$customer_group[] = array(
				'ID'=>$customer_group_data['customers_status_id'],
				'NAME'=>$customer_group_data['customers_status_name'],
				'IMAGE'=>$customer_group_data['customers_status_image'],
				'USERS'=>$group_data['count']
			);
		}
		?>

		<br />

		<div class="second-page-nav">
			<div class="row-fluid">
				<div class="span8"></div>
				<div class="span4">
					<div class="pull-right">
						<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=new'); ?>"><?php echo BUTTON_NEW_NEWSLETTER; ?></a>
					</div>
				</div>
			</div>
		</div>

		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th colspan="2"><?php echo TITLE_CUSTOMERS; ?></th>
					<th><span class="line"></span><?php echo TITLE_STK; ?></th>
				</tr>
			</thead>
			<?php for ($i=0,$n=sizeof($customer_group); $i<$n; $i++) { ?>
			<tr>
				<td width="50" class="tcenter"><?php echo ($customer_group[$i]['IMAGE'] != '') ? os_image(GROUP_ICONS_HTTP.$customer_group[$i]['IMAGE'], $customer_group[$i]['NAME']) : '';?></td>
				<td><?php echo $customer_group[$i]['NAME']; ?></td>
				<td><?php echo $customer_group[$i]['USERS']; ?></td>
			</tr>
			<?php } ?>
		</table>

		<?php
		$newsletters_query = os_db_query("SELECT newsletter_id,date,title FROM ".TABLE_MODULE_NEWSLETTER." WHERE status='0'");
		$news_data = array();
		while ($newsletters_data=os_db_fetch_array($newsletters_query))
		{
			$news_data[] = array(
				'id' => $newsletters_data['newsletter_id'],
				'date' => $newsletters_data['date'],
				'title' => $newsletters_data['title']
			);
		}
		?>

		<br />
		<br />

		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th><?php echo TITLE_NOT_SEND; ?></th>
					<th><span class="line"></span><?php echo TITLE_DATE; ?></th>
				</tr>
			</thead>
		<?php
		for ($i=0,$n=sizeof($news_data); $i<$n; $i++)
		{
			if ($news_data[$i]['id'] != '')
			{ ?>
				<tr>
					<td><a href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'ID='.$news_data[$i]['id']); ?>"><b><?php echo $news_data[$i]['title']; ?></b></a></td>
					<td><?php echo $news_data[$i]['date']; ?></td>
				</tr>
				<?php
				if (@$_GET['ID']!='' && @$_GET['ID'] == @$news_data[$i]['id'])
				{
					$total_query=os_db_query("SELECT count(*) as count FROM ".TABLE_NEWSLETTER_TEMP.(int)$_GET['ID']."");
					$total_data=os_db_fetch_array($total_query);
					?>
					<tr>
						<td>
							<?php echo TEXT_SEND_TO.$total_data['count']; ?><br />
							<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=edit&ID='.$news_data[$i]['id']); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
							<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=delete&ID='.$news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
							<br /><br />
							<a class="btn" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=send&ID='.$news_data[$i]['id']); ?>"><?php echo BUTTON_SEND.'</a>'; ?>
						</td>
						<td>
							<?php
							$newsletters_query = os_db_query("SELECT title, body, cc, bc FROM ".TABLE_MODULE_NEWSLETTER." WHERE newsletter_id = '".(int)$_GET['ID']."'");
							$newsletters_data = os_db_fetch_array($newsletters_query);

							echo TEXT_TITLE.' '.$newsletters_data['title'].'<br />';

							for ($i = 0,$n = sizeof($customers_status); $i<$n; $i++)
							{
								$newsletters_data['bc'] = str_replace($customers_status[$i]['id'], $customers_status[$i]['text'], $newsletters_data['bc']);
							}

							echo TEXT_TO.' '.$newsletters_data['bc'].'<br />';
							echo TEXT_CC.' '.$newsletters_data['cc'].'<br /><br />'.TEXT_PREVIEW;
							echo '<div class="alert">'.$newsletters_data['body'].'</div>';
							?>
						</td>
					</tr>
					<?php
				}
			}
		}
		?>
		</table>

		<br />
		<br />

		<?php
		$newsletters_query=os_db_query("SELECT newsletter_id,date,title FROM ".TABLE_MODULE_NEWSLETTER." WHERE status = '1'");
		$news_data = array();
		while ($newsletters_data=os_db_fetch_array($newsletters_query))
		{
			$news_data[] = array(
				'id' => $newsletters_data['newsletter_id'],
				'date'=>$newsletters_data['date'],
				'title'=>$newsletters_data['title']
			);
		}
		?>
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th><?php echo TITLE_SEND; ?></th>
					<th><span class="line"></span><?php echo TITLE_ACTION; ?></th>
				</tr>
			</thead>
		<?php
		for ($i=0,$n=sizeof($news_data); $i<$n; $i++)
		{
			if ($news_data[$i]['id']!='')
			{
			?>
			<tr>
				<td><?php echo $news_data[$i]['date'].'    '; ?><b><?php echo $news_data[$i]['title']; ?></b></td>
				<td>
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=edit&ID='.$news_data[$i]['id']); ?>" title="<?php echo TEXT_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER,'action=delete&ID='.$news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')" title="<?php echo TEXT_DELETE; ?>"><i class="icon-trash"></i></a>
				</td>
			</tr>
			<?php
			}
		}
		?>
		</table>

		<?php

	break;       // end default page

	case 'edit':
	case 'safe':
	case 'new':  // action for NEW newsletter!

		echo os_draw_form('edit_newsletter',FILENAME_MODULE_NEWSLETTER,'action=save','post','enctype="multipart/form-data"').
			os_draw_hidden_field('ID',@$_GET['ID']);
		?>
		<div class="control-group">
			<label class="control-label" for="title"><?php echo TEXT_TITLE; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="title" name="title" value="<?php echo $newsletters_data['title']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for=""><?php echo TEXT_TO; ?></label>
			<div class="controls">
		<?php
		for ($i=0,$n=sizeof($customers_status);$i<$n; $i++)
		{
			$group_query = os_db_query("SELECT count(*) as count FROM ".TABLE_NEWSLETTER_RECIPIENTS." WHERE mail_status='1' and customers_status='".$customers_status[$i]['id']."'");
			$group_data = os_db_fetch_array($group_query);

			$group_query=os_db_query("SELECT count(*) as count FROM ".TABLE_CUSTOMERS." WHERE customers_status='".$customers_status[$i]['id']."'");
			$group_data_all = os_db_fetch_array($group_query);

			$bc_array = explode(',', @$newsletters_data['bc']);

			echo '<label class="checkbox">'.os_draw_checkbox_field('status['.$i.']','yes', in_array($customers_status[$i]['id'], $bc_array)).' '.$customers_status[$i]['text'].'  '.$group_data['count'].' '.TEXT_USERS.$group_data_all['count'].TEXT_CUSTOMERS.'</label>';
		}
		echo '<label class="checkbox">'.os_draw_checkbox_field('status_all', 'yes', in_array('all', $bc_array)).' '.TEXT_NEWSLETTER_ONLY.'</label>';
		?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="cc"><?php echo TEXT_CC; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="cc" name="cc" value="<?php echo $newsletters_data['cc']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="newsletter_body"><?php echo TEXT_BODY; ?></label>
			<div class="controls">
				<textarea name="newsletter_body" id="newsletter_body" class="input-block-level textarea_big"><?php echo stripslashes(@$newsletters_data['body']); ?></textarea>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_MODULE_NEWSLETTER); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>

		</form>
		<?php

	break;
} // end switch
?>
<?php $main->bottom(); ?>