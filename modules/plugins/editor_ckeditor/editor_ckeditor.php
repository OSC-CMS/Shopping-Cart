<?php
/*
	Plugin Name: CKEditor
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description: Редактор CKEditor
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Editors
*/

add_action('head_admin', 'editor_ckeditor_admin_head');

function editor_ckeditor_admin_head()
{
	_e('<script src="'.plugurl().'ckeditor/ckeditor.js"></script>');
	_e('<script>
		$(document).ready(function () {
			$(".textarea_big").addClass("ckeditor");
		});
	</script>');
}

function editor_ckeditor_install()
{
}

?>