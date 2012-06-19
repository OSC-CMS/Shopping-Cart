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

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

include(get_path('lang_admin').$_SESSION['language_admin'].'/main.php');
?>
<table class="adminform" style="padding:5px;">
						<tr>
							<td width="55%" valign="top">
		<div id="cpanel">
		<div style="float:left;">
			<div class="icon">
				<a href="../index.php">
					<img border="0" src="<?php echo http_path('images_admin').'header/'; ?>icon-48-frontpage.png" />					
					<span><?php echo MAIN_SHOP; ?></span></a>
			</div>
		</div>
				
		<div style="float:left;">
			<div class="icon">
				<a href="categories.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>icon-48-category.png" />					
					<span><?php echo MAIN_CATEGORIES; ?></span></a>
			</div>
		</div>
		
        <div style="float:left;">
			<div class="icon">
				<a href="orders.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>basket.png" />					
					<span><?php echo MAIN_ORDERS; ?></span></a>
			</div>
		</div>			

		<div style="float:left;">
			<div class="icon">
				<a href="specials.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>cal.png" />					
					<span><?php echo MAIN_SPECIALS; ?></span></a>
			</div>
		</div>

		<div style="float:left;">
			<div class="icon">
				<a href="languages.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>language.png" />					
					<span><?php echo MAIN_LANGUAGES; ?></span></a>
			</div>
		</div>
		
		<div style="float:left;">
			<div class="icon">
				<a href="customers.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>user.png" />					
					<span><?php echo MAIN_CUSTOMERS; ?></span></a>
			</div>
		</div>		

		<div style="float:left;">
			<div class="icon">
				<a href="modules.php?set=shipping">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>modules.png" />					
					<span><?php echo MAIN_SHIPPING; ?></span></a>
			</div>
		</div>

		<div style="float:left;">
			<div class="icon">
				<a href="modules.php?set=payment">
					<img  border="0" src="<?php echo http_path('images_admin').'header/'; ?>modules.png" />					
					<span><?php echo MAIN_PAYMENT; ?></span></a>
			</div>
		</div>
			
		
		<div style="float:left;">
			<div class="icon">
				<a href="modules.php?set=ordertotal">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>modules.png"  />					
					<span><?php echo MAIN_ORDERTOTAL; ?></span></a>
			</div>
		</div>
		
		<div style="float:left;">
			<div class="icon">
				<a href="articles.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>articles.png" />					
					<span><?php echo MAIN_ARTICLES; ?></span></a>
			</div>
		</div>
		
		<div style="float:left;">
			<div class="icon">
				<a href="coupon_admin.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>coupon.png"  />					
					<span><?php echo MAIN_COUPONS; ?></span></a>
			</div>
		</div>		

		<div style="float:left;">
			<div class="icon">
				<a href="stats_products_viewed.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>statics.png" />					
					<span><?php echo MAIN_STATS; ?></span></a>
			</div>
		</div>		
		
		<div style="float:left;">
			<div class="icon">
				<a href="reviews.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>Comment.png"  />					
					<span><?php echo MAIN_REVIEWS; ?></span></a>
			</div>
		</div>		
		
		<div style="float:left;">
			<div class="icon">
				<a href="themes.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>themes.png" />
					<span><?php echo TEXT_THEMES; ?></span></a>
			</div>
		</div>
		

		<div style="float:left;">
			<div class="icon">
				<a href="../logoff.php">
					<img border="0"  src="<?php echo http_path('images_admin').'header/'; ?>Exit.png" />					
					<span><?php echo MAIN_LOGOFF; ?></span></a>
			</div>
		</div>
			
	</div>
</td></tr></table>