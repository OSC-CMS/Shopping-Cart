<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/articles.php';

require_once(CLS_NEW.'articles.class.php');
$articles = new articles();

// Получаем данные категории статей
if (isset($_GET['action']) && $_GET['action'] == 'topic_move')
{
	$articlesData = $articles->getTopicById($_GET['t_id']);
}

// Получаем данные статьи
if (isset($_GET['action']) && ($_GET['action'] == 'article_move' OR $_GET['action'] == 'article_copy'))
{
	$articlesData = $articles->getArticleById($_GET['a_id']);
}

if ($_GET['action'] == 'topic_move') { ?>

	<?php echo $cartet->html->form('articles_form', 'ajax.php', 'ajax_action=articles_categoryMove', 'post', array('id' => 'articles_form', 'class' => 'form-inline')); ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_MOVE_TOPIC; ?> - <?php echo $articlesData['topics_name']; ?>
			</h4>
		</div>
		<div class="modal-body">

			<?php echo os_draw_hidden_field('topics_id', $articlesData['topics_id']); ?>

			<p><?php echo TEXT_MOVE_TOPICS_INTRO; ?></p>

			<?php echo os_draw_pull_down_menu('move_to_topic_id', os_get_topic_tree(), $_GET['t_id']); ?>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('topic_move', BUTTON_MOVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'articles_categoryMove', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'article_copy') { ?>

	<?php echo $cartet->html->form('articles_form', 'ajax.php', 'ajax_action=articles_articleCopy', 'post', array('id' => 'articles_form')); ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_COPY; ?> - <?php echo $articlesData['articles_name']; ?>
			</h4>
		</div>
		<div class="modal-body">

			<?php echo os_draw_hidden_field('articles_id', $articlesData['articles_id']); ?>
			<?php echo os_draw_hidden_field('current_topic_id', $_GET['t_id']); ?>

			<h5><?php echo TEXT_INFO_COPY_TO_INTRO; ?></h5>
			<p><?php echo os_draw_pull_down_menu('topics_id', os_get_topic_tree(), $_GET['t_id']); ?></p>

			<h5><?php echo TEXT_HOW_TO_COPY; ?></h5>
			<label class="radio">
				<?php echo os_draw_radio_field('copy_as', 'duplicate', true); ?>
				<?php echo TEXT_COPY_AS_DUPLICATE; ?>
			</label>
			<label class="radio">
				<?php echo os_draw_radio_field('copy_as', 'link'); ?>
				<?php echo TEXT_COPY_AS_LINK; ?>
			</label>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('article_copy', BUTTON_COPY, array('class' => 'btn btn-success save-form', 'data-form-action' => 'articles_articleCopy')); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'article_move') { ?>

	<?php echo $cartet->html->form('articles_form', 'ajax.php', 'ajax_action=articles_articleMove', 'post', array('id' => 'articles_form', 'class' => 'form-inline')); ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_MOVE_ARTICLE; ?> - <?php echo $articlesData['articles_name']; ?>
			</h4>
		</div>
		<div class="modal-body">

			<?php echo os_draw_hidden_field('articles_id', $articlesData['articles_id']); ?>
			<?php echo os_draw_hidden_field('current_topic_id', $_GET['t_id']); ?>

			<p><?php echo TEXT_MOVE_ARTICLES_INTRO; ?></p>
			<p><?php echo TEXT_INFO_CURRENT_TOPICS.'<br />'.os_output_generated_topic_path($articlesData['articles_id'], 'article'); ?></p>

			<?php echo os_draw_pull_down_menu('move_to_topic_id', os_get_topic_tree(), $_GET['t_id']); ?>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('article_move', BUTTON_MOVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'articles_articleMove', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'category_delete') { ?>

	<?php echo $cartet->html->form('articles_form', 'ajax.php', 'ajax_action=articles_categoryDelete', 'post', array('id' => 'articles_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="topics_id" value="<?php echo $_GET['t_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_TOPIC; ?>
			</h4>
		</div>
		<div class="modal-body">
			<?php
				$topic_childs = os_childs_in_topic_count($_GET['t_id']);
				$topic_articles = os_articles_in_topic_count($_GET['t_id']);

				if ($topic_childs OR $topic_articles) {
			?>
			<div class="alert alert-info">
				<?php echo sprintf(TEXT_DELETE_WARNING_CHILDS, $topic_childs); ?><br />
				<?php echo sprintf(TEXT_DELETE_WARNING_ARTICLES, $topic_articles); ?>
			</div>
			<?php } echo TEXT_DELETE_TOPIC_INTRO; ?>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('topic_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'articles_categoryDelete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php echo $cartet->html->form('articles_form', 'ajax.php', 'ajax_action=articles_articleDelete', 'post', array('id' => 'articles_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="articles_id" value="<?php echo $_GET['a_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_ARTICLE; ?>
			</h4>
		</div>
		<div class="modal-body">
			<?php echo TEXT_DELETE_ARTICLE_INTRO; ?>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('topic_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'articles_articleDelete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>