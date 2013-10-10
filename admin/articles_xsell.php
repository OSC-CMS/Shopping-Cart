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

if ($_POST)
{
	if ($_POST['run_update'] == true)
	{
		$query ="DELETE FROM ".TABLE_ARTICLES_XSELL." WHERE articles_id = '".$_GET['products']."'";
		if (!os_db_query($query))
		exit(TEXT_NO_DELETE);
	}

	if ($_POST['xsell_id'])
	{
		foreach ($_POST['xsell_id'] as $k => $temp)
		{
			$query = "INSERT INTO ".TABLE_ARTICLES_XSELL." VALUES ('',".$_GET['products'].",$temp,1)";
			if (!os_db_query($query))
			exit(TEXT_NO_INSERT);
		}
	}
	
	os_redirect(os_href_link(FILENAME_ARTICLES_XSELL, '', 'NONSSL'));
}



$breadcrumb->add(HEADING_TITLE, FILENAME_ARTICLES_XSELL);

$main->head();
$main->top_menu();
?>

<?php
function general_db_conct($query_1)
{
	$result_1 = os_db_query($query_1);
	$num_of_rows = mysql_num_rows($result_1);
	for ($i=0;$i<$num_of_rows;$i++)
	{
		$fields = mysql_fetch_row($result_1);
		$a_to_pass[$i]= $fields[$y=0];
		$b_to_pass[$i]= $fields[++$y];
		$c_to_pass[$i]= $fields[++$y];
		$d_to_pass[$i]= $fields[++$y];
		$e_to_pass[$i]= $fields[++$y];
		$f_to_pass[$i]= $fields[++$y];
		$g_to_pass[$i]= $fields[++$y];
		$h_to_pass[$i]= $fields[++$y];
		$i_to_pass[$i]= $fields[++$y];
		$j_to_pass[$i]= $fields[++$y];
		$k_to_pass[$i]= $fields[++$y];
		$l_to_pass[$i]= $fields[++$y];
		$m_to_pass[$i]= $fields[++$y];
		$n_to_pass[$i]= $fields[++$y];
		$o_to_pass[$i]= $fields[++$y];
	}
	return array($a_to_pass,$b_to_pass,$c_to_pass,$d_to_pass,$e_to_pass,$f_to_pass,$g_to_pass,$h_to_pass,$i_to_pass,$j_to_pass,$k_to_pass,$l_to_pass,$m_to_pass,$n_to_pass,$o_to_pass);
}

if ($_GET['products'])
{
?>
<form action="<?php os_href_link(FILENAME_ARTICLES_XSELL, '', 'NONSSL'); ?>" method="post">
	<table class="table table-condensed table-big-list border-radius-top">
		<thead>
			<tr>
				<th></th>
				<th><?php echo TEXT_PRODUCT_ID; ?></th>
				<th><span class="line"></span><?php echo HEADING_PRODUCT_NAME; ?></th>
			</tr>
		</thead>

		<?php
		$query = "select p.products_id, pd.products_name, pd.products_description, pd.products_url from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where pd.products_id = p.products_id and pd.language_id = '".(int)$_SESSION['languages_id']."' order by pd.products_name";

		list ($products_id, $products_name, $products_description, $products_url  ) = general_db_conct($query);
		$num_of_products = sizeof($products_id);
		$query = "select * from ".TABLE_ARTICLES_XSELL." where articles_id = '".$_GET['products']."'";
		list ($ID_PR, $products_id_pr, $xsell_id_pr) = general_db_conct($query);
		for ($i=0; $i < $num_of_products; $i++)
		{
			?><tr>
			<td>
			<input <?php
			$run_update = false;
			if ($xsell_id_pr)
				foreach ($xsell_id_pr as $compare_checked)
					if ($products_id[$i] === $compare_checked) {echo "checked"; $run_update=true;} ?> name="xsell_id[]" type="checkbox" value="<?php echo $products_id[$i]; ?>">
			</td>
			<?php echo "<td>".$products_id[$i]."</td>\n"."<td>".$products_name[$i]."</td>\n";
		}
		?>
	</table>
		<input type="hidden" name="run_update" value="<?php if ($run_update==true) echo "true"; else echo "false" ?>">
		<input type="hidden" name="products" value="<?php echo $_GET['products']; ?>">
		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_ARTICLES_XSELL); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
</form>
<?php
}
else
{
	$query = "select a.articles_id, ad.articles_name, ad.articles_description, ad.articles_url from ".TABLE_ARTICLES." a, ".TABLE_ARTICLES_DESCRIPTION." ad where ad.articles_id = a.articles_id and ad.language_id = '".(int)$_SESSION['languages_id']."' order by ad.articles_name";
	list ($articles_id, $articles_name, $articles_description, $articles_url) = general_db_conct($query);
	?>
	<table class="table table-condensed table-big-list border-radius-top">
		<thead>
			<tr>
				<th><?php echo TEXT_PRODUCT_ID; ?></th>
				<th><span class="line"></span><?php echo HEADING_ARTICLE_NAME; ?></th>
				<th><span class="line"></span><?php echo HEADING_CROSS_ASSOCIATION; ?></th>
				<th><span class="line"></span><?php echo HEADING_CROSS_SELL_ACTIONS; ?></th>
			</tr>
		</thead>
		<?php 
		$num_of_articles = sizeof($articles_id);
		for ($i=0; $i < $num_of_articles; $i++)
		{
			$query = "select pd.products_name, ax.xsell_id from ".TABLE_ARTICLES_XSELL." ax, ".TABLE_PRODUCTS_DESCRIPTION." pd where pd.products_id = ax.xsell_id and ax.articles_id ='".$articles_id[$i]."' and pd.language_id = '".(int)$_SESSION['languages_id']."' order by ax.sort_order";
			list ($Related_items, $xsell_ids) = general_db_conct($query);
			echo '<tr>';
			echo "<td valign=\"top\">".$articles_id[$i]."</td>\n";
			echo "<td valign=\"top\">".$articles_name[$i]."</td>\n";
			if ($Related_items)
			{
			echo "<td><ol>";
			foreach ($Related_items as $display)
			echo '<li>'. $display .'</li>';
			echo"</ol></td>\n";
			}
			else
				echo "<td>-</td>\n";
			echo '<td><a href="'.os_href_link(FILENAME_ARTICLES_XSELL, 'products='.$articles_id[$i], 'NONSSL').'">'.TEXT_ADD_PRODUCTS.'</a></td>';
			echo "</tr>\n";
			unset($Related_items);
		}
		?>
	</table>
<?php } ?>

<?php $main->bottom(); ?>