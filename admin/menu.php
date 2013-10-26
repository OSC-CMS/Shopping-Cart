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

// Сохраняем-добавляем группу
if (isset($_POST['save_group']))
{
	if (isset($_GET['action']) && $_GET['action'] == 'add_group')
		$cartet->menu->addGroup($_POST);
	else
		$cartet->menu->saveGroup($_POST);

	if ($_GET['group_id'])
		os_redirect(os_href_link('menu.php', 'group_id='.$_GET['group_id']));
	else
		os_redirect(os_href_link('menu.php'));
}
// Сохраняем-добавляем меню
if (isset($_POST['save_menu']))
{
	if (isset($_GET['action']) && $_GET['action'] == 'add_menu')
		$cartet->menu->add($_POST);
	else
		$cartet->menu->save($_POST);

	if ($_POST['menu_group_id'])
		os_redirect(os_href_link('menu.php', 'group_id='.$_POST['menu_group_id']));
	else
		os_redirect(os_href_link('menu.php'));
}

// Массово сохраняем меню
if (isset($_POST['save_mass_items']))
{
	$cartet->menu->add($_POST, 1);
	if ($_POST['menu_group_id'])
		os_redirect(os_href_link('menu.php', 'group_id='.$_POST['menu_group_id']));
	else
		os_redirect(os_href_link('menu.php'));
}

// ID группы
$groupId = (!empty($_GET['group_id'])) ? $_GET['group_id'] : 1;

// Название группы
$groupName = $cartet->menu->getGroupTitleById($groupId);

// Хлебные крошки
$breadcrumb->add('Меню', 'menu.php');

$menuEdit = array();
// Редактирование меню
if ($_GET['action'] == 'edit_menu')
{
	$menuEdit = $cartet->menu->byId($_GET['menu_id']);
	$breadcrumb->add($groupName, os_href_link('menu.php', 'group_id='.$groupId));
	$breadcrumb->add($menuEdit['menu_langs'][$_SESSION['languages_id']]['lang_title']);
}
// Добавление меню
elseif ($_GET['action'] == 'add_menu')
{
	$breadcrumb->add($groupName, os_href_link('menu.php', 'group_id='.$groupId));
	$breadcrumb->add('Добавление');
}
// Добавление меню
elseif ($_GET['action'] == 'add_group')
{
	$breadcrumb->add('Добавление группы');
}
// Редактирование группы
elseif ($_GET['action'] == 'edit_group')
{
	$groupEdit = $cartet->menu->groupById($_GET['group_id']);
	$breadcrumb->add($groupEdit['group_langs'][$_SESSION['languages_id']]['lang_title']);
}

$main->head();
$main->top_menu();

// Получаем доступные языки
$languages = $cartet->language->get();
?>

<?php if ($_GET['action'] == 'edit_menu' OR $_GET['action'] == 'add_menu') { ?>

	<div class="row-fluid">
		<div class="span6">
			<h4>Добавление</h4>
			<hr>
			<form method="post" action="">
				<div class="control-group">
					<label class="control-label" for="">Название</label>
					<div class="controls">
						<ul class="nav nav-tabs default-tabs">
							<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
							<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
							<?php } ?>
						</ul>
						<div class="tab-content">
							<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
							<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
								<input type="text" name="lang[<?php echo $lang['languages_id']; ?>]" value="<?php echo $menuEdit['menu_langs'][$lang['languages_id']]['lang_title']; ?>" />
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="">УРЛ (about.html)</label>
					<div class="controls">
						<input type="text" name="menu_url" value="<?php echo $menuEdit['menu_url'] ;?>" class="span12" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="">CSS Класс</label>
					<div class="controls">
						<input type="text" name="menu_class" value="<?php echo $menuEdit['menu_class'] ;?>" class="span12" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="">CSS Класс иконки</label>
					<div class="controls">
						<input type="text" name="menu_class_icon" value="<?php echo $menuEdit['menu_class_icon'] ;?>" class="span12" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="">Статус</label>
					<div class="controls">
						<select name="menu_status">
							<option value="1" <?php echo ($menuEdit['menu_status'] == 1) ? 'selected' : '' ;?>>Да</option>
							<option value="0" <?php echo ($menuEdit['menu_status'] == 0) ? 'selected' : '' ;?>>Нет</option>
						</select>
					</div>
				</div>
				<hr>
				<div class="tcenter footer-btn">
					<input type="hidden" name="menu_group_id" value="<?php echo $groupId; ?>">
					<?php if (isset($menuEdit['menu_id'])) { ?>
					<input type="hidden" name="menu_id" value="<?php echo $menuEdit['menu_id']; ?>">
					<?php } ?>
					<input name="save_menu" class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
					<a class="btn btn-link" href="<?php echo os_href_link('menu.php', 'group_id='.$groupId); ?>"><?php echo BUTTON_CANCEL; ?></a>
				</div>
			</form>
		</div>
		<div class="span6">
			<?php if (!isset($menuEdit['menu_id'])) { ?>
				<h4>Массовое добавление</h4>
				<hr>
				<form method="post" action="">

					<div class="control-group">
						<label class="control-label" for="">Каждое меню с новой строки. Пример: <strong>Статьи|articles.php</strong></label>
						<div class="controls">
							<textarea name="items" class="span12"></textarea>
						</div>
					</div>
					<hr>
					<div class="tcenter footer-btn">
						<input type="hidden" name="menu_group_id" value="<?php echo $groupId; ?>">
						<input name="save_mass_items" class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
						<a class="btn btn-link" href="<?php echo os_href_link('menu.php', 'group_id='.$groupId); ?>"><?php echo BUTTON_CANCEL; ?></a>
					</div>
				</form>
			<?php } ?>
		</div>
	</div>

<?php } elseif ($_GET['action'] == 'add_group' OR $_GET['action'] == 'edit_group') { ?>

	<form method="post" action="">
		<div class="control-group">
			<label class="control-label" for="">Название</label>
			<div class="controls">
				<ul class="nav nav-tabs default-tabs">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
					<?php } ?>
				</ul>
				<div class="tab-content">
					<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
					<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
						<input type="text" name="lang[<?php echo $lang['languages_id']; ?>]" value="<?php echo $groupEdit['group_langs'][$lang['languages_id']]['lang_title']; ?>" />
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="">Статус</label>
			<div class="controls">
				<select name="group_status">
					<option value="1" <?php echo ($groupEdit['group_status'] == 1) ? 'selected' : '' ;?>>Да</option>
					<option value="0" <?php echo ($groupEdit['group_status'] == 0) ? 'selected' : '' ;?>>Нет</option>
				</select>
			</div>
		</div>
		<hr>
		<div class="tcenter footer-btn">
			<?php if (isset($_GET['group_id']) && !empty($_GET['group_id'])) { ?>
			<input type="hidden" name="group_id" value="<?php echo $_GET['group_id']; ?>" />
			<?php } ?>
			<input type="submit" name="save_group" class="btn btn-success" value="<?php echo BUTTON_SAVE; ?>" />
		</div>
	</form>

<?php } else { ?>

	<?php foreach ((array)$cartet->menu->getGroups() as $id => $title) { ?>
		<div class="btn-group">
			<a class="btn btn-mini" href="menu.php?group_id=<?php echo $id; ?>"><?php echo $title; ?></a>
			<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="menu.php?action=edit_group&group_id=<?php echo $id; ?>">Редактировать</a></li>
				<?php if ($id != 1) { ?>
				<li><a href="#" data-action="menu_deleteGroup" data-remove-parent="li" data-id="<?php echo $id; ?>" data-confirm="Вы уверены, что хотите удалить эту группу? Будут удалены все ее меню!">Удалить</a></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>
	<a class="btn btn-mini" href="menu.php?action=add_group" title="Добавить группу"><i class="icon-plus"></i></a>
	<div class="btn-group pull-right">
		<a class="btn btn-mini" href="menu.php?action=add_menu&group_id=<?php echo $groupId; ?>">Добавить пункт</a>
		<a href="javascript://" class="btn btn-mini" data-toggle="collapse" data-target="#getCode">Получить код</a>
	</div>

	<div id="getCode" class="collapse textarea-checkout">
		<br />
		<div class="well well-small">
			<div class="bold">Для шаблонов</div>
			<p>{menu id="<?php echo ($_GET['group_id']) ? $_GET['group_id'] : 1 ; ?>" class="nav"}</p>
		</div>
	</div>

	<div class="menu-item" id="menu-header">
		<div class="menu-actions">Actions</div>
		<div class="menu-class">Icon</div>
		<div class="menu-url">URL</div>
		<div class="menu-title">Title</div>
	</div>
	<?php
	$menu = $cartet->menu->getByGroupId(array('group_id' => $groupId));
	if ($menu)
	{
		foreach ($menu as $row)
		{
			$delete = ($groupId > 1) ? '<a href="#" data-action="menu_delete" data-remove-parent="li" data-id="'.$row['menu_id'].'" data-confirm="Вы уверены, что хотите удалить это меню?"><i class="icon-trash"></i></a>' : '';

			$item ='
			<div class="menu-item"><div class="menu-move"><i class="icon-move"></i></div>
				<div class="menu-title">'.$row['lang_title'].'</div>
				<div class="menu-status">
					<a '.(($row['menu_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$row['menu_id'].'_0_menu_status" data-column="menu_status" data-action="menu_status" data-id="'.$row['menu_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>
					<a '.(($row['menu_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$row['menu_id'].'_1_menu_status" data-column="menu_status" data-action="menu_status" data-id="'.$row['menu_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>
				</div>
				<div class="menu-url">'.$row['menu_url'].'</div>
				<div class="menu-class">'.$row['menu_class_icon'].'</div>
				<div class="menu-actions">
					<a href="menu.php?action=edit_menu&group_id='.$row['menu_group_id'].'&menu_id='.$row['menu_id'].'"><i class="icon-edit"></i></a>
					'.$delete.'
				</div>
			</div><ul></ul>
			';

			$cartet->tree->addItem($row['menu_id'], $row['menu_parent_id'], ' data-id="'.$row['menu_id'].'"', $item);
		}

		echo $cartet->tree->get(array(
			'parent' => 0,
			'ul' => 'id="admin_menu_list"'
		));
		$cartet->tree->clear();
	}
	?>

<?php } ?>

<?php $main->bottom(); ?>