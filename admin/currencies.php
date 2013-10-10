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

require(_CLASS_ADMIN.'currencies.php');
$currencies = new currencies();

if ($_GET['action'] && $_GET['action'] == 'update')
{
	$server_used = CURRENCY_SERVER_PRIMARY;
	$currency_query = os_db_query("select currencies_id, code, title from ".TABLE_CURRENCIES);
	while ($currency = os_db_fetch_array($currency_query))
	{
		$quote_function = 'quote_'.CURRENCY_SERVER_PRIMARY.'_currency';
		$rate = $quote_function($currency['code']);
		if ((!$rate) && (CURRENCY_SERVER_BACKUP != ''))
		{
			$quote_function = 'quote_'.CURRENCY_SERVER_BACKUP.'_currency';
			$rate = $quote_function($currency['code']);
			$server_used = CURRENCY_SERVER_BACKUP;
		}
		if ($rate)
		{
			os_db_query("update ".TABLE_CURRENCIES." set value = '".$rate."', last_updated = now() where currencies_id = '".$currency['currencies_id']."'");
			$messageStack->add_session(sprintf(TEXT_INFO_CURRENCY_UPDATED, $currency['title'], $currency['code'], $server_used), 'success');
		}
		else
			$messageStack->add_session(sprintf(ERROR_CURRENCY_INVALID, $currency['title'], $currency['code'], $server_used), 'error');
	}
	set_default_cache();
	os_redirect(os_href_link(FILENAME_CURRENCIES));
}

$breadcrumb->add(HEADING_TITLE, FILENAME_CURRENCIES);

if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(TEXT_INFO_HEADING_NEW_CURRENCY, os_href_link(FILENAME_CURRENCIES, 'action=new'));
	$currencies_query = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$currencies_query_raw = os_db_query("select * from ".TABLE_CURRENCIES." WHERE currencies_id = '".(int)$_GET['cID']."'");
	$currencies_query = os_db_fetch_array($currencies_query_raw);

	$breadcrumb->add($currencies_query['title'], os_href_link(FILENAME_LANGUAGES, 'action=new'));
}

$main->head();
$main->top_menu();
?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'new' OR $_GET['action'] == 'edit')) { ?>

<form id="currencies" name="currencies" action="<?php echo os_href_link(FILENAME_CURRENCIES); ?>" method="post">

	<?php if (isset($_GET['cID']) && !empty($_GET['cID'])) { ?>
		<input type="hidden" name="cID" value="<?php echo $_GET['cID']; ?>">
	<?php } ?>
	<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

	<div class="control-group">
		<label class="control-label" for="title"><?php echo TEXT_INFO_CURRENCY_TITLE; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="title" name="title" data-required="true" value="<?php echo $currencies_query['title']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="code"><?php echo TEXT_INFO_CURRENCY_CODE; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="code" name="code" data-required="true" value="<?php echo $currencies_query['code']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="symbol_left"><?php echo TEXT_INFO_CURRENCY_SYMBOL_LEFT; ?></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="symbol_left" name="symbol_left" value="<?php echo $currencies_query['symbol_left']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="symbol_right"><?php echo TEXT_INFO_CURRENCY_SYMBOL_RIGHT; ?></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="symbol_right" name="symbol_right" value="<?php echo $currencies_query['symbol_right']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="decimal_point"><?php echo TEXT_INFO_CURRENCY_DECIMAL_POINT; ?></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="decimal_point" name="decimal_point" value="<?php echo $currencies_query['decimal_point']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="thousands_point"><?php echo TEXT_INFO_CURRENCY_THOUSANDS_POINT; ?></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="thousands_point" name="thousands_point" value="<?php echo $currencies_query['thousands_point']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="decimal_places"><?php echo TEXT_INFO_CURRENCY_DECIMAL_PLACES; ?></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="decimal_places" name="decimal_places" value="<?php echo $currencies_query['decimal_places']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="value"><?php echo TEXT_INFO_CURRENCY_VALUE; ?> <span class="input-required">*</span></label>
		<div class="controls">
			<input class="input-block-level" type="text" id="value" name="value" data-required="true" value="<?php echo $currencies_query['value']; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for=""></label>
		<div class="controls">
			<label class="checkbox"><input type="checkbox" name="default" value="on" <?php echo (DEFAULT_CURRENCY == $currencies_query['code']) ? 'checked' : ''; ?>> <?php echo TEXT_INFO_SET_AS_DEFAULT; ?></label>
		</div>
	</div>

	<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success ajax-save-form" data-form-action="currencies_save" data-reload-page="1" type="submit" value="<?php echo BUTTON_INSERT; ?>">
		<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CURRENCIES, 'page='.$_GET['page']); ?>"><?php echo BUTTON_CANCEL; ?></a>
	</div>

</form>

<? } else { ?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6"></div>
		<div class="span6">
			<div class="btn-group pull-right">
				<?php if (CURRENCY_SERVER_PRIMARY) { ?>
					<a class="btn btn-primary btn-mini" href="<?php echo os_href_link(FILENAME_CURRENCIES, 'page='.$_GET['page'].'&action=update'); ?>"><?php echo BUTTON_UPDATE; ?></a>
				<?php } ?>
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_CURRENCIES, 'page='.$_GET['page'].'&action=new'); ?>"><?php echo BUTTON_NEW_CURRENCY; ?></a>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th><?php echo TABLE_HEADING_CURRENCY_NAME; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CURRENCY_CODES; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_CURRENCY_VALUE; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_CURRENCY_SYMBOL_LEFT; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_CURRENCY_SYMBOL_RIGHT; ?></th>
			<th><span class="line"></span><?php echo TEXT_INFO_CURRENCY_LAST_UPDATED; ?></th>
			<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
	<?php
	$currency_query_raw = "select * from ".TABLE_CURRENCIES." order by title";
	$currency_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $currency_query_raw, $currency_query_numrows);
	$currency_query = os_db_query($currency_query_raw);

	while ($currency = os_db_fetch_array($currency_query))
	{
		if (DEFAULT_CURRENCY == $currency['code'])
			$name = '<b>'.$currency['title'].' ('.TEXT_DEFAULT.')</b>';
		else
			$name = $currency['title'];

		$_price = @number_format($currency['value'], $currency['decimal_places']);
	?>
	<tr>
		<td><?php echo $name; ?></td>
		<td><?php echo $currency['code']; ?></td>
		<td><?php echo $_price; ?></td>
		<td><?php echo $currency['symbol_left']; ?></td>
		<td><?php echo $currency['symbol_right']; ?></td>
		<td><?php echo $currency['last_updated']; ?></td>
		<td width="100">
			<div class="btn-group pull-right">
				<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_CURRENCIES, 'page='.$_GET['page'].'&cID='.$currency['currencies_id'].'&action=edit'); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
				<?php if ($languages['code'] != DEFAULT_LANGUAGE) { ?>
					<a class="btn btn-mini" href="#" data-action="currencies_delete" data-remove-parent="tr" data-id="<?php echo $currency['currencies_id']; ?>" data-confirm="<?php echo TEXT_INFO_DELETE_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				<?php } ?>
			</div>
		</td>
	</tr>
	<?php } ?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td><?php echo $currency_split->display_count($currency_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CURRENCIES); ?></td>
<td><?php echo $currency_split->display_links($currency_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
</tr>
</table>

<? } ?>

<?php $main->bottom(); ?>