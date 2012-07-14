<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

?>
<link href="<?php echo _HTTP_THEMES_C; ?>javascript/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo _HTTP_THEMES_C; ?>javascript/css/bootstrap-responsive.css" rel="stylesheet">
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-transition.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-alert.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-modal.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-dropdown.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-scrollspy.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-tab.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-tooltip.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-popover.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-button.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-collapse.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-carousel.js"></script>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/js/bootstrap-typeahead.js"></script>
<script>
$(document).ready(function () {
	$('#modalLogin').modal('hide');
	$('#modalSearchHelp').modal('hide');
	$(".alert").alert();
	$('.myTooltip').tooltip('hide');
});
</script>
<?php if (isset($_GET['coID']) && $_GET['coID'] == 8) { ?>
<script src="<?php echo _HTTP_THEMES_C; ?>javascript/masonry.js"></script>
<?php } ?>