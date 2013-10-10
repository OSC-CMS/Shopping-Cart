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

$breadcrumb->add('SMS', 'sms.php');

if ($_GET['action'] == 'add')
{
	$breadcrumb->add('Добавить сервис', 'sms.php?action=add');
	$getSms = array();
}
elseif ($_GET['action'] == 'setting')
{
	$breadcrumb->add('Настройки', 'sms.php?action=setting');
	$getSms = array();
}
elseif ($_GET['action'] == 'edit')
{
	$getSmsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."sms WHERE id = '".(int)$_GET['id']."'");
	$getSms = os_db_fetch_array($getSmsQuery);

	$breadcrumb->add($getSms['name'], 'sms.php?action=add');
}


$main->head();
$main->top_menu();
?>

<div class="btn-group">
	<a class="btn btn-mini" href="/admin/sms.php">Список</a>
	<a class="btn btn-mini" href="/admin/sms.php?action=setting">Настройки</a>
	<a class="btn btn-mini" href="/admin/sms.php?action=add">Добавить сервис</a>
</div>

<hr>

<?php if (isset($_GET['action']) && $_GET['action'] == 'add' OR $_GET['action'] == 'edit') { ?>

	<form id="save_sms" action="" method="post">

		<?php if ($_GET['action'] == 'add') { ?>
			<input type="hidden" name="action" value="add">
		<?php } else { ?>
			<input type="hidden" name="action" value="edit">
			<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<?php } ?>

		<div class="control-group">
			<label class="control-label" for="name">Название (например: sms.ru) <span class="input-required">*</span></label>
			<div class="controls">
				<input type="text" name="sms[name]" id="name" value="<?php echo $getSms['name']; ?>" data-required="true" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="login">Логин</label>
			<div class="controls">
				<input type="text" name="sms[login]" id="login" value="<?php echo $getSms['login']; ?>" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="password">Пароль</label>
			<div class="controls">
				<input type="text" name="sms[password]" id="password" value="<?php echo $getSms['password']; ?>" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="password_md5">Шифровать пароль MD5</label>
			<div class="controls">
				<select name="sms[password_md5]" id="password_md5" class="input-block-level">
					<option value="0" <?php echo ($getSms['password_md5'] == 0) ? 'selected' : ''; ?>>Нет</option>
					<option value="1" <?php echo ($getSms['password_md5'] == 1) ? 'selected' : ''; ?>>Да</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="api_id">Идентификатор (api id)</label>
			<div class="controls">
				<input type="text" name="sms[api_id]" id="api_id" value="<?php echo $getSms['api_id']; ?>" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="api_key">Ключ (api key)</label>
			<div class="controls">
				<input type="text" name="sms[api_key]" id="api_key" value="<?php echo $getSms['api_key']; ?>" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="title">Подпись от кого СМС (В некоторых сервисах необходимо прохождение модерации подписи)</label>
			<div class="controls">
				<input type="text" name="sms[title]" id="title" value="<?php echo $getSms['title']; ?>" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="phone">Ваш телефон для получения СМС (пример: 71234567890). Если отсылать нужно на несколько номеров, то укажите их через запятую</label>
			<div class="controls">
				<input type="text" name="sms[phone]" id="phone" value="<?php echo $getSms['phone']; ?>" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="status">Статус отправки (например: TEST)</label>
			<div class="controls">
				<input type="text" name="sms[status]" id="status" value="<?php echo $getSms['status']; ?>" class="input-block-level">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="url">URL для выполнения GET запроса (без http://) <span class="input-required">*</span></label>
			<div class="controls">
				<textarea name="sms[url]" id="url" rows="3" class="input-block-level" data-required="true"><?php echo $getSms['url']; ?></textarea>
				<span class="help-block">
					Пример: api.avisosms.ru/sms/get/?username={login}&password={password}&destination_address={phone}&source_address={title}&message={text}
					<br />
					В ссылке заменить параметры на соответствующие метки:
					<ul>
						<li>{login} - Логин</li>
						<li>{password} - Пароль</li>
						<li>{api_id} - Идентификатор (api id)</li>
						<li>{api_key} - Ключ (api key)</li>
						<li>{title} - Подпись</li>
						<li>{phone} - Ваш телефон для получения СМС</li>
						<li>{status} - Статус отправки</li>
						<li>{text} - Текст (берется из шаблона письма)</li>
					</ul>
				</span>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input type="submit" class="btn btn-success ajax-save-form" data-form-action="sms_save" data-reload-page="1" name="add_sms" value="Сохранить">
		</div>
	</form>

<?php } elseif (isset($_GET['action']) && $_GET['action'] == 'setting') { ?>

<?php $getSetting = $cartet->sms->setting(); ?>

	<form id="save_setting" action="" method="post">

		<div class="control-group">
			<label class="control-label" for="sms_status">Использовать СМС оповещения в магазине?</label>
			<div class="controls">
				<select name="sms[sms_status]" id="sms_status" class="input-block-level">
					<option value="1" <?php echo ($getSetting['sms_status'] == 1) ? 'selected' : ''; ?>>Да</option>
					<option value="0" <?php echo ($getSetting['sms_status'] == 0) ? 'selected' : ''; ?>>Нет</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="sms_default_id">СМС сервис</label>
			<div class="controls">
				<select name="sms[sms_default_id]" id="sms_default_id" class="input-block-level">
				<?php
				$getSmsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."sms ORDER BY id DESC");
				if (os_db_num_rows($getSmsQuery) > 0)
				{
					while($sms = os_db_fetch_array($getSmsQuery))
					{
						?>
						<option value="<?php echo $sms['id']; ?>" <?php echo ($getSetting['sms_default_id'] == $sms['id']) ? 'selected' : ''; ?>><?php echo $sms['name']; ?></option>
						<?php
					}
				}
				?>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="sms_order_admin">Оповещать администратора при создании заказа</label>
			<div class="controls">
				<select name="sms[sms_order_admin]" id="sms_order_admin" class="input-block-level">
					<option value="1" <?php echo ($getSetting['sms_order_admin'] == 1) ? 'selected' : ''; ?>>Да</option>
					<option value="0" <?php echo ($getSetting['sms_order_admin'] == 0) ? 'selected' : ''; ?>>Нет</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="sms_order">Оповещать покупателя при создании заказа</label>
			<div class="controls">
				<select name="sms[sms_order]" id="sms_order" class="input-block-level">
					<option value="1" <?php echo ($getSetting['sms_order'] == 1) ? 'selected' : ''; ?>>Да</option>
					<option value="0" <?php echo ($getSetting['sms_order'] == 0) ? 'selected' : ''; ?>>Нет</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="sms_order_change">Оповещать покупателя при изменении заказа</label>
			<div class="controls">
				<select name="sms[sms_order_change]" id="sms_order_change" class="input-block-level">
					<option value="1" <?php echo ($getSetting['sms_order_change'] == 1) ? 'selected' : ''; ?>>Да</option>
					<option value="0" <?php echo ($getSetting['sms_order_change'] == 0) ? 'selected' : ''; ?>>Нет</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="sms_register">Оповещать о регистрации покупателя</label>
			<div class="controls">
				<select name="sms[sms_register]" id="sms_register" class="input-block-level">
					<option value="1" <?php echo ($getSetting['sms_register'] == 1) ? 'selected' : ''; ?>>Да</option>
					<option value="0" <?php echo ($getSetting['sms_register'] == 0) ? 'selected' : ''; ?>>Нет</option>
				</select>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input type="submit" class="btn btn-success ajax-save-form" data-form-action="sms_saveSetting" data-reload-page="1" name="save_setting" value="Сохранить">
		</div>
	</form>

<?php } else { ?>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th>Название</th>
				<th><span class="line"></span>Логин</th>
				<th><span class="line"></span>Пароль</th>
				<th><span class="line"></span>Пароль MD5</th>
				<th><span class="line"></span>API ID</th>
				<th><span class="line"></span>API KEY</th>
				<th><span class="line"></span>Подпись</th>
				<th><span class="line"></span>Телефон</th>
				<th><span class="line"></span>Статус</th>
				<th class="tright"><span class="line"></span>Действие</th>
			</tr>
		</thead>
		<?php
		$getSmsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."sms ORDER BY id DESC");
		if (os_db_num_rows($getSmsQuery) > 0)
		{
			while($sms = os_db_fetch_array($getSmsQuery))
			{
				?>
				<tr>
					<td><?php echo $sms['name']; ?></td>
					<td><?php echo $sms['login']; ?></td>
					<td><?php echo $sms['password']; ?></td>
					<td><?php echo ($sms['password_md5'] == 1) ? YES : NO; ?></td>
					<td><?php echo $sms['api_id']; ?></td>
					<td><?php echo $sms['api_key']; ?></td>
					<td><?php echo $sms['title']; ?></td>
					<td><?php echo $sms['phone']; ?></td>
					<td><?php echo $sms['status']; ?></td>
					<td width="100">
						<div class="btn-group pull-right">
							<a class="btn btn-mini" href="<?php echo os_href_link('sms.php', 'action=edit&id='.$sms['id']); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
							<a class="btn btn-mini" href="#" data-action="sms_delete" data-remove-parent="tr" data-id="<?php echo $sms['id']; ?>" data-confirm="Вы уверены, что хотите удалить сервис <?php echo $sms['name']; ?>?" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
						</div>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</table>

<?php } ?>

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
<?php $main->bottom(); ?>