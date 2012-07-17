<table class="table table-bordered">
<thead>
<tr>
    <th>
        <?php
            echo TEXT_VALID_PRODUCTS_NAME.' 
            <a href="'.os_href_link(FILENAME_CONTENT, 'coID='.$_GET['coID'].'&products_name=asc').'">&uarr;</a>
            <a href="'.os_href_link(FILENAME_CONTENT, 'coID='.$_GET['coID'].'&products_name=desc').'">&darr;</a>
            ';
        ?>
    </th>
    <th>
        <?php
            echo TEXT_VALID_PRODUCTS_PRICE.' 
            <a href="'.os_href_link(FILENAME_CONTENT, 'coID='.$_GET['coID'].'&products_price=asc').'">&uarr;</a>
            <a href="'.os_href_link(FILENAME_CONTENT, 'coID='.$_GET['coID'].'&products_price=desc').'">&darr;</a>
            ';
        ?>
    </th>
</tr>
</thead>
<tbody>
<?php
    if (isset($_GET['products_name']))
    {
        trim( $_GET['products_name'] );

        if (strtolower($_GET['products_name'])=='asc')
        {
            $_orders = ' order by pd.products_name ASC';
        }
        elseif (strtolower($_GET['products_name'])=='desc')
        {
            $_orders = ' order by pd.products_name DESC';
        }
    }
    else
    {
        $_orders = ' order by pd.products_name ASC';
        if (isset($_GET['products_price']))
        {
            if (strtolower($_GET['products_price'])=='asc')
            {
                $_orders = ' order by p.products_price ASC';
            }
            elseif (strtolower($_GET['products_price'])=='desc')
            {
                $_orders = ' order by p.products_price DESC';
            }
        }
        else
        {
            $_orders = ' order by p.products_price ASC';
        }
    }
    $export_query = "select p.products_id, pd.products_name, p.products_model, p.products_price, p.products_status, p.products_tax_class_id from " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id  where p.products_status = 1".$_orders;

    $export_query = $db->query($export_query);

    while ($row = $db->fetch_array($export_query,false)) 
    {
        $products_price = $osPrice->Format( $row['products_price']*$osPrice->currencies[ $_SESSION['currency'] ]['value'] , true);

        echo "<tr>\n";
        echo "<td>"."<a href=\"".os_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .$row["products_id"])."\">" .$row["products_name"]."</a></td>\n";

        echo "<td>";
        echo $products_price; 
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</tbody></table>";

?>