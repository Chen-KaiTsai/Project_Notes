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

// Start transaction
$conn->begin_transaction();

// Insert order info to the order table
$sql_str = "INSERT INTO `ORDER` (customer_id, date) VALUES ($customer_id, now())";

if (!$conn->query($sql_str)) {
    echo ("Database Insert Failed.<br>" . $conn->error);
    mysqli_free_result($result);
    mysqli_close($conn);
}

// Get order id
$order_id = mysqli_insert_id($conn);

$order_total = 0;
$order_status = True;
for ($i = 0; $i < count($_POST['product_name']); $i++) {
    // check product_order_quantity = 0 or NULL or ''
    if ($_POST["product_order_quantity"][$i] == '' || $_POST["product_order_quantity"][$i] == 0 || !isset($_POST["product_order_quantity"][$i])) {
        //echo "This product no order<br>";
        continue;
    }

    $product_name = $_POST['product_name'][$i];
    $product_order_quantity = $_POST['product_order_quantity'][$i];

    //echo $product_name . "<br>";
    //echo $product_order_quantity . "<br><br>";

    $sql_str = "SELECT id, quantity, sell_price FROM PRODUCT WHERE name='$product_name'";

    $result = $conn->query($sql_str);
    if ($result == NULL) {
        mysqli_close($conn);

        $conn->rollback();
        die("Syntax error or Database deny access");
    }
    $num_rows = mysqli_num_rows($result);
    if ($num_rows <= 0) {
        mysqli_free_result($result);
        mysqli_close($conn);

        $conn->rollback();
        die("No such product");
    } elseif ($num_rows > 1) {
        mysqli_free_result($result);
        mysqli_close($conn);

        $conn->rollback();
        die("database error");
    }
    $row = $result->fetch_row();

    $product_id = $row[0];
    $product_quantity = $row[1];
    $product_price = $row[2];

    //echo $product_id."<br>";
    //echo $product_quantity."<br>";
    //echo $product_price."<br><br>";

    // Product order quantity should not be bigger than product quantity
    if ($product_quantity < $product_order_quantity) {
        echo "NOT enough quantity for " . $product_name . "<br>This order did not go through<br>";
        $order_status = False;
        break;
    }

    $product_quantity -= $product_order_quantity;

    // Update the product quantity
    $sql_str = "UPDATE PRODUCT SET quantity = $product_quantity WHERE id = $product_id";

    if (!$conn->query($sql_str)) {
        echo "Database Update Failed.<br>" . $conn->error;

        $conn->rollback();
        mysqli_close($conn);
        die();
    }

    // Insert into product_order
    $sql_str = "INSERT INTO PRODUCT_ORDER (order_id,  product_id, quantity) VALUES ($order_id, $product_id, $product_order_quantity)";

    if (!$conn->query($sql_str)) {
        echo ("Database Insert Failed.<br>" . $conn->error);

        $conn->rollback();
        mysqli_close($conn);
        die();
    }

    $order_total += $product_price * $product_order_quantity;
}

if ($order_status) {
    $conn->commit();
    //echo "Order go through<br>";

    // Print out the order summary
    $sql_str = "SELECT P.name AS 'Product name', P.sell_price AS 'Unit price', PO.quantity AS 'Quantity', (PO.quantity * P.sell_price) AS 'Sub total'
    FROM PRODUCT P, PRODUCT_ORDER PO
    WHERE P.id = PO.product_id AND PO.order_id = $order_id";

    $result = $conn->query($sql_str);

    if ($result == NULL) {
        mysqli_close($conn);

        die("Syntax error or Database deny access");
    }

    echo "<h2>Your order list.</h2>";

    echo "<table border='1'>";
    echo "<tr algin='center'>";
    while ($field = $result->fetch_field())
        echo "<td>" . $field->name . "</td>";
    echo "</tr>";
    while ($row = $result->fetch_row()) {
        echo "<tr>";
        for ($i = 0; $i < $result->field_count; $i++)
            echo "<td>" . $row[$i] . "</td>";
        echo "</tr>";
    }
    echo "<tr><td colspan=3>Total</td><td>$order_total</td></tr>";
    echo "</table>";

    mysqli_free_result($result);
    mysqli_close($conn);
} else {
    $conn->rollback();
    //echo "Order did not go through<br>";
}

echo "<a href='customer_check_p2.php'>Customer homepage</a><br>";
echo "<a href='cps5740_phase2.php'>Project homepage</a><br>";