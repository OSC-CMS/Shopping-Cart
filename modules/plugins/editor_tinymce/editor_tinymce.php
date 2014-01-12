<?php
/*
	Plugin Name: TinyMCE
	Plugin URI: http://osc-cms.com/store/plugins
	Version: 1.2
	Description: Редактор TinyMCE
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Editors
*/

add_action('head_admin', 'editor_tinymce_admin_head');

function editor_tinymce_admin_head()
{
	if (isPageFunc('content_manager') OR isPageFunc('faq') OR isPageFunc('categories') OR isPageFunc('articles') OR isPageFunc('latest_news') OR isPageFunc('module_newsletter'))
	{
		$selector = get_option('editor_tinymce_selector');

		_e('<script type="text/javascript" src="'.plugurl().'tinymce/tinymce.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

		<link rel="stylesheet" type="text/css" media="screen" href="'.plugurl().'elfinder/css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="'.plugurl().'elfinder/css/theme.css">
		<script type="text/javascript" src="'.plugurl().'elfinder/js/elfinder.min.js"></script>
		<script type="text/javascript" src="'.plugurl().'elfinder/js/i18n/elfinder.'.$_SESSION['language'].'.js"></script>

		<script type="text/javascript">
		tinymce.init({
			selector: "'.$selector.'",
			language : "'.$_SESSION['language'].'", // change language here
			theme: "modern",
			file_browser_callback : elFinderBrowser,
			plugins: [
				"advlist autolink lists link image charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars code fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons template paste"
			],
			toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons",
		});

		function elFinderBrowser (field_name, url, type, win) {
		  tinymce.activeEditor.windowManager.open({
			file: \''.plugurl().'elfinder/elfinder.php\',// use an absolute path!
			title: \'elFinder 2.0\',
			width: 900,  
			height: 450,
			resizable: \'yes\'
		  }, {
			setUrl: function (url) {
			  win.document.getElementById(field_name).value = url;
			}
		  });
		  return false;
		}
		</script>');
	}
}

function editor_tinymce_install()
{
	add_option('editor_tinymce_selector', '.textarea_big', 'input');
}

function isPageFunc( $name )
{
   global $PHP_SELF;
   
   $_php_self = $PHP_SELF;
   $_php_self = str_replace('.php', '', trim($_php_self) );
   $_php_self = str_replace(CATALOG.'admin/', '', $_php_self );
   $_php_self = str_replace(CATALOG, '', $_php_self );
   $_php_self = str_replace('/', '', $_php_self );

   if ($name == $_php_self) return true;
   else false;
}
?>