<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ  не допускается.');
?>
<div class="well well-box well-nice">
	<div class="navbar">
		<div class="navbar-inner">
			<h4 class="title">Заметки администратора</h4>
			<div class="well-right-btn">
				<a href="javascript:;" class="btn  btn-mini btn-success pull-right ajax-load-page" data-load-page="notes" data-toggle="modal">Добавить заметку</a>
			</div>
		</div>
	</div>
	<div class="well-box-content well-max-height well-small-font">
		<table class="table table-striped table-condensed table-content well-table">
			<tbody>
			<?php
			// заметки
			$getLatestNotes = os_db_query("SELECT
				*
			FROM
				".DB_PREFIX."admin_notes
					LEFT JOIN ".TABLE_CUSTOMERS." ON (customers_id = customer)
			order by
				id desc");

			if (os_db_num_rows($getLatestNotes) > 0)
			{
				while ($notes = os_db_fetch_array($getLatestNotes))
				{
					?>
					<tr>
						<td class="bold"><?php echo $notes['customers_firstname']; ?> <?php echo $notes['customers_lastname']; ?></td>
						<td width="140"><?php echo $notes['date_added']; ?></td>
						<td width="20"><span class="pull-right"><a class="btn btn-mini" href="javascript:;" data-action="notes_delete" data-remove-parent="tr" data-id="<?php echo $notes['id']; ?>" data-confirm="Вы действительно хотите удалить эту заметку?" title="Удалить"><i class="icon-trash"></i></a></span></td>
					</tr>
					<tr>
						<td colspan="3"><div class="table-big-text"><?php echo nl2br($notes['note']); ?></div></td>
					</tr>
				<?php
				}
			}
			else
			{
				echo '<tr><td colspan="3">Заметок нет</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>
</div>


