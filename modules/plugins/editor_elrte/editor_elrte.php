<?php
/*
	Plugin Name: elRTE
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.0
	Description: Редактор elRTE
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Editors
*/

add_action('head_admin', 'editor_elrte_admin_head');

function editor_elrte_admin_head()
{
	_e('<link href="'.plugurl().'ui/css/ui-lightness/jquery-ui-1.10.2.custom.css" rel="stylesheet">');
	_e('<script src="'.plugurl().'ui/js/jquery-ui-1.10.2.custom.js"></script>');

	_e('<link rel="stylesheet" href="'.plugurl().'elrte/css/elrte.min.css" type="text/css" media="screen" charset="utf-8">');
	_e('<script src="'.plugurl().'elrte/js/elrte.min.js" type="text/javascript" charset="utf-8"></script>');
	_e('<script src="'.plugurl().'elrte/js/i18n/elrte.ru.js" type="text/javascript" charset="utf-8"></script>');
	_e('<script type="text/javascript" charset="utf-8">
     $().ready(function() {
         var opts = {
             lang         : \'ru\',   // set your language
             styleWithCSS : false,
            height       : 400,
             toolbar      : \'maxi\'
         };
         // create editor
         $(\'.textarea_big, .textarea_small\').elrte(opts);
 
         // or this way
         // var editor = new elRTE(document.getElementById(\'our-element\'), opts);
     });
	</script>');
}



function editor_elrte_install()
{
}

?>