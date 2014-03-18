<?php
/*
	Plugin Name: Летающие картинки в корзину
	Plugin URI: http://osc-cms.com/store/plugins/fly-to-basket
	Version: 1.0
	Description: Летающие картинки в корзину
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Products
*/

add_action('head', 'head_fly_to_basket');
function head_fly_to_basket()
{
	_e("
		<script>
		/*
			Add to cart fly effect with jQuery. - May 05, 2013
			(c) 2013 @ElmahdiMahmoud - fikra-masri.by
			license: http://www.opensource.org/licenses/mit-license.php
		*/   
		$(document).ready(function() {
			$('.add_to_cart_action').on('click', function () {
				var cart = $('#divShoppingCart');
				var imgtodrag = $(this).closest('".get_option('ftb_product_item')."').find('img');

				if (imgtodrag) {
					var imgclone = imgtodrag.clone()
						.offset({
						top: imgtodrag.offset().top,
						left: imgtodrag.offset().left
					})
						.css({
						'opacity': '".get_option('ftb_opacity')."',
							'position': 'absolute',
							'height': '".get_option('ftb_height')."',
							'width': '".get_option('ftb_width')."',
							'z-index': '".get_option('ftb_z_index')."'
					})
						.appendTo($('body'))
						.animate({
						'top': cart.offset().top + 10,
							'left': cart.offset().left + 10,
							'width': 75,
							'height': 75
					}, ".get_option('ftb_time').");
					imgclone.animate({
						'width': 0,
							'height': 0
					}, function () {
						$(this).detach()
					});
				}
			});
		});
		</script>
	");
}

add_filter('button_cart_big', 'button_cart_big_plug_fly_to_basket');
add_filter('button_buy_now', 'button_cart_big_plug_fly_to_basket');
add_filter('button_in_cart', 'button_cart_big_plug_fly_to_basket');

function button_cart_big_plug_fly_to_basket($_value)
{
	$_value['code'] = buttonSubmit($_value['img'], $_value['href'], $_value['alt'], '', 'add_to_cart_action');
    return $_value;
}

function fly_to_basket_install()
{
	add_option('ftb_product_item', '.product-list-item', 'input');
	add_option('ftb_width', '100px', 'input');
	add_option('ftb_height', '100px', 'input');
	add_option('ftb_z_index', '100', 'input');
	add_option('ftb_time', '1000', 'input');
	add_option('ftb_opacity', '0.4', 'input');
}