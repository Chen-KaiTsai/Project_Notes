<?php

// check if customer already login
if (!isset($_COOKIE["customer_login_id"])) {
    die("Please login as customer<br>");
}

$login_id = $_COOKIE["customer_login_id"];
$sql_str = "SELECT customer_id FROM CUSTOMER WHERE login_id = '$login_id'";

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

// Get customer id with customer login_id
$result = $conn->query($sql_str);
if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if ($num_rows <= 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Authentication error, please login as customer");
} elseif ($num_rows > 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error");
}
$row = $result->fetch_row();

$customer_id = $row[0];

echo "<a href='cps5740_customer_logout_phase1.php'>Customer logout</a><br>";

// Join Product, Order, Product_Order

$sql_str =
    "SELECT O.id AS 'Order ID', P.name AS 'Product Name', PO.quantity AS 'Order Quantity', P.sell_price AS 'Unit Price', (PO.quantity * P.sell_price) AS 'Sub total', O.date 
FROM `ORDER` AS O, PRODUCT AS P, PRODUCT_ORDER AS PO
WHERE 
O.id = PO.order_id AND PO.product_id = P.id AND O.customer_id = $customer_id
ORDER BY O.id";

$result = $conn->query($sql_str);

if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}

$num_rows = mysqli_num_rows($result);

if ($num_rows == 0) {
    echo "Customer has no order history";

    mysqli_free_result($result);
    mysqli_close($conn);
} else {

    $current_order_id;
    $current_order_total = 0;
    $all_order_total = 0;

    $field_itr = 0;
    while ($field = $result->fetch_field()) {
        $field_names[$field_itr] = $field->name;
        $field_itr++;
    }
        
    while ($row = $result->fetch_row()) {
        //echo $current_order_id. " ". $row[0]." ".$row[4]."<br>";
        if ($row[0] != $current_order_id) {
            
            // Echo the summary of a order
            if($current_order_id != NULL)
            {
                echo "<tr><td></td><td>Order Paid</td><td colspan=3>$current_order_total</td>";
                echo "</table><br>";
                $all_order_total += $current_order_total;
                $current_order_total = 0;
            }
            
            echo "<table border='1'>";
            echo "<tr algin='center'>";

            for($i = 0; $i < count($field_names); $i++)
                echo "<td>" . $field_names[$i] . "</td>";
            echo "</tr>";

            $current_order_id = $row[0];
            //echo $current_order_id."<br>";
        }
        echo "<tr>";
        for ($i = 0; $i < $result->field_count; $i++)
            echo "<td>" . $row[$i] . "</td>";
        echo "</tr>";
        $current_order_total += $row[4];
    }
    echo "</table>";
    $all_order_total += $current_order_total;
    echo "<br><table border='1'><tr><td>Total Paid</td><td>$all_order_total</td></tr></table>";

    mysqli_free_result($result);
    mysqli_close($conn);
}

echo "<a href='customer_check_p2.php'>Customer homepage</a><br>";
echo "<a href='cps5740_phase2.php'>Project homepage</a><br>";