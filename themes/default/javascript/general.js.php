<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

?>
<script type="text/javascript">
$(document).ready(function () {

	$(".close").click(
		function () {
			$(this).parent().fadeTo(400, 0, function () {
				$(this).slideUp(400);
			});
			return false;
		}
	);
});

(function($) {
	$(function() {
		$('ul.tabs').delegate('li:not(.current)', 'hover', function() {
			$(this).addClass('current').siblings().removeClass('current')
			.parents('div.section').find('div.tbox').hide().eq($(this).index()).fadeIn(150);
		});
	})
})(jQuery);
</script>