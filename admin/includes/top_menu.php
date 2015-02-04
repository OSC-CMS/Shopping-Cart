<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

$admin_access_query = os_db_query("SELECT * FROM ".TABLE_ADMIN_ACCESS." WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
$admin_access = os_db_fetch_array($admin_access_query); 

$menu_value = array();

global $p, $menu_value, $breadcrumb;
$plug_array = $p->info;

/*if (SET_WHOS_ONLINE == "false")
{
	remove_action('admin_menu', 'WHOS_ONLINE');
}*/

global $os_remove_action;
?>

<div id="ajax-modal" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false"></div>

<!--
<div class="lang-menu">
<?php $this->lang_menu();?>
</div>
-->

<div class="navbar navbar-inverse navbar-static-top">
	<div class="navbar-inner">
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></a>

		<a class="head-logo" href="<?php echo os_href_link(FILENAME_DEFAULT, '', 'NONSSL'); ?>" title="<?php echo TEXT_HEADER_DEFAULT; ?>">CartET</a>
		<ul class="nav full pull-left">
			<li class="dropdown user-avatar">
				<?php
				if (is_file(dir_path('catalog').'VERSION'))
					$_version = @file_get_contents(dir_path('catalog').'VERSION');
				else
					$_version = ' --- ';
				?>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo BOX_VERSION; ?> <?php echo $this->service->getVersion(); ?> <i class="icon-caret-down"></i></span></span></a>
				<ul class="dropdown-menu">
					<li><a href="http://osc-cms.com" target="_blank" title="CartET">CartET</a></li>
					<li><a href="http://osc-cms.com/docs" target="_blank"><?php echo BOX_HELP; ?></a></li>
					<li><a href="http://osc-cms.com/extend" target="_blank"><?php echo BOX_EXTEND; ?></a></li>
					<li><a href="http://osc-cms.com/forum" target="_blank"><?php echo BOX_SUPPORT_FORUM; ?></a></li>
				</ul>
			</li>
			<?php $update = $this->service->checkUpdate(true); ?>
			<?php if (!empty($update['version'])) { ?>
				<li><a href="update.php">Доступно обновление <?php echo $update['version']; ?></a></li>
			<?php } else { ?>
				<li><a href="update.php">Проверить обновления</a></li>
			<?php } ?>
		</ul>

		<div class="nav-collapse">
			<?php
			// Запрос на выбору данных о покупателе
			$profile = array();
			if (ACCOUNT_PROFILE == 'true')
			{
				$profileQuery = os_db_query("SELECT * FROM ".DB_PREFIX."customers_profile WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
				$profile = os_db_fetch_array($profileQuery);
			}
			$avatarImg = (!empty($profile['customers_avatar'])) ? $profile['customers_avatar'] : 'noavatar.gif';
			$avatar = http_path('images').'avatars/'.$avatarImg;
			?>
			<ul class="nav full pull-right">
				<li class="dropdown user-avatar">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><img class="menu-avatar" src="<?php echo $avatar; ?>" /> <span><?php echo $_SESSION['customer_first_name']; ?> <i class="icon-caret-down"></i></span></span></a>
					<ul class="dropdown-menu">
						<li class="with-image">
							<div class="avatar"><img src="<?php echo $avatar; ?>" /></div>
							<span><?php echo $_SESSION['customer_first_name'].' '.$_SESSION['customer_last_name']; ?></span>
						</li>

						<li class="divider"></li>

						<li><a href="customers.php?cID=<?php echo $_SESSION['customer_id']; ?>&action=edit"><i class="icon-user"></i> <span>Редактировать аккаунт</span></a></li>
						<li><a href="../logoff.php"><i class="icon-off"></i> <?php echo BOX_HEADING_LOGOFF; ?></a></li>
					</ul>
				</li>
				<li><a href="../index.php" target="_blank"><i class="icon-home"></i> <?php echo TEXT_HEADER_SHOP; ?></a></li>
			</ul>
		</div>
	</div>
</div>

<?php
//добавление элемента меню в Плагины -> $title
function add_plug_menu($title, $url)
{
	global $p, $menu_value;

	if ($p->info[$p->name]['status'] == 1)
	{
		$p->lang();

		$menu_value[6]['child'][] = array
		(
			'value' => (isset($p->lang[$p->name][$title])) ? $p->lang[$p->name][$title] : $title,
			'url' => $url
		);
	}
}
?>

<div class="container-fluid clearfix" id="main-container">

	<div id="sidebar">
		<?php
		$menu = $this->menu->getByGroupId(array('group_id' => 1, 'status' => true));

		if ($menu)
		{
			foreach ($menu as $row)
			{
				if ($this->tree->data[$row['menu_id']] && $row['menu_parent_id'] != 0)
					$sub = '<b class="arrow icon-angle-right icon-large pull-right"></b>';
				else
					$sub = '';

				$item = '
					'.((!empty($row['menu_class_icon'])) ? '' : '').'
					<b class="glow"></b>
					<a '.((!empty($row['menu_class_icon'])) ? 'href="#" class="dropdown-toggle"' : 'href="'.$row['menu_url'].'"').'>
						'.((!empty($row['menu_class_icon'])) ? '<i class="'.$row['menu_class_icon'].'"></i>' : '').'
						'.((!empty($row['menu_class_icon'])) ? '<span>'.$row['lang_title'].'</span>' : $row['lang_title']).'
						'.((!empty($row['menu_class_icon'])) ? '<b class="arrow icon-angle-down"></b></i>' : '').'
						'.$sub.'
					</a>
				';

				$_access = explode('.php', $row['menu_url']);

				if (!empty($row['menu_url']) && in_array($_access[0], $admin_access))
				{
					if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access[$_access[0]] == '1'))
					{
						$this->tree->addItem($row['menu_id'], $row['menu_parent_id'], '', $item);
					}
				}
				else
				{
					$this->tree->addItem($row['menu_id'], $row['menu_parent_id'], '', $item);
				}
				
			}

			echo $this->tree->get(array('group' => 1, 'parent' => 0, 'ul' => 'id="mainMenu" class="nav nav-list"'));
			$this->tree->clear();
		}
		?>

		<hr class="divider nomargin">
		<div id="sidebar-collapse"><i class="icon-exchange"></i></div>
		<hr class="divider nomargin">
	</div><!--/#sidebar-->

	<div id="main-content" class="clearfix">

		<?php global $PHP_SELF; if (!strstr($PHP_SELF, 'index2.php')) { ?>
		<div class="breadcrumbs">
			<?php _e($breadcrumb->trail()); ?>
		</div>
		<?php } ?>
		<div id="page-content" class="clearfix">