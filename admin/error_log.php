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

if(isset($_GET['action']) && $_GET['action']=='clean_log')
{
	$fp = @fopen(_TMP.'db_error.log', "w");
	@fclose($fp);

	if (filesize(_TMP.'db_error.log')==0)
	{
		$messageStack->add_session('Лог-файл успешно очищен', 'success');
		os_redirect(FILENAME_ERROR_LOG);
	}
}

$breadcrumb->add(HEADING_TITLE, FILENAME_ERROR_LOG);

$main->head();
$main->top_menu();
?>

<?php
if (is_file(_TMP.'db_error.log'))
{
	$fp = @fopen(_TMP.'db_error.log', "rb");
	if ($fp)
	{
		while (!feof($fp))
		{
			$st .= fread($fp, 4096);
		}
	}

	echo '<p><textarea class="round" style="width:100%; height:400px" cols="60" name="text">'.$st.'</textarea></p>';

	@fclose($fp);
}
else
	echo NO_ERRORS;
?>

<hr>

<div class="tcenter footer-btn">
	<a class="btn btn-success" href="<?php echo os_href_link(FILENAME_ERROR_LOG, 'action=clean_log'); ?>">Очистить</a>
</div>

<?php $main->bottom(); ?>