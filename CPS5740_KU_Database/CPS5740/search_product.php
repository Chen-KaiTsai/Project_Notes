<?php

// check if customer already login
if (!isset($_COOKIE["customer_login_id"])) {
    die("Please login as customer<br>");
}

$login_id = $_COOKIE["customer_login_id"];

// check if field empty
if ($_GET["search_text"] == "" || !isset($_GET["search_text"])) {
    die("Please enter search text." . $footer_link);
}

// search product in database
$search_text = strtolower($_GET['search_text']);
$search_text = str_replace(" ", "|", $search_text);

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

if ($search_text == "*") {
    $sql_str =
        "SELECT P.name AS 'Product Name', P.sell_price AS 'Sell Price', P.quantity AS 'Avaliable Quantity', V.name AS 'Vendor Name'
        FROM PRODUCT P, CPS5740.VENDOR V, CPS5740.EMPLOYEE2 E
        WHERE P.vendor_id = V.vendor_id AND P.employee_id = E.employee_id AND P.quantity > 0";

    // delete the old search text in the table
    $conn->query("DELETE FROM CUSTOMER_ADVERTISEMENT WHERE login_id = '$login_id'");

    // insert search_text into the table
    if (!$conn->query("INSERT INTO CUSTOMER_ADVERTISEMENT (login_id, search_text) VALUES ('$login_id', 'Other')")) {
        echo("Database Insert Failed.<br>".$conn->error);
        mysqli_free_result($result);
        mysqli_close($conn);
        
        die();
    }
} else {
    $sql_str =
        "SELECT P.name AS 'Product Name', P.sell_price AS 'Sell Price', P.quantity AS 'Avaliable Quantity', V.name AS 'Vendor Name' 
        FROM PRODUCT P, CPS5740.VENDOR V, CPS5740.EMPLOYEE2 E
        WHERE P.vendor_id = V.vendor_id AND P.employee_id = E.employee_id AND (P.name REGEXP '$search_text' OR P.description REGEXP '$search_text') AND P.quantity > 0";

    // delete the old search text in the table
    $conn->query("DELETE FROM CUSTOMER_ADVERTISEMENT WHERE login_id = '$login_id'");

    // insert search_text into the table
    if (!$conn->query("INSERT INTO CUSTOMER_ADVERTISEMENT (login_id, search_text) VALUES ('$login_id', '$search_text')")) {
        echo("Database Insert Failed.<br>".$conn->error);
        mysqli_free_result($result);
        mysqli_close($conn);
        
        die();
    }
}

$result = $conn->query($sql_str);

if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}

$num_rows = mysqli_num_rows($result);

if ($num_rows == 0) {
    echo "No product found</br>";

    mysqli_free_result($result);
    mysqli_close($conn);
} else {
    echo "<h2>Avaliable Product list for search $search_text</h2>";

    echo "<table border='1'>";
    echo "<tr algin='center'>";

    echo "<form action='customer_order.php' method='post'>";

    $i = 0;
    while ($field = $result->fetch_field())
    {
        if ($i == 3)
            echo "<td>Order quantity</td>";
        echo "<td>" . $field->name . "</td>";
        $i++;
    }
    echo "</tr>";
    $pid = 0;
    while ($row = $result->fetch_row()) {
        echo "<tr>";
        for ($i = 0; $i < $result->field_count; $i++)
        {
            if ($i == 3)
                echo "<td><input type='text' name='product_order_quantity[$pid]' value='0'></td>";
            echo "<td>" . $row[$i] . "</td>";
        }
        echo "<input type='hidden' name='product_name[$pid]' value='$row[0]'>";
        echo "</tr>";
        $pid++;
    }

    echo "</table>";
    echo "<input type='submit' name='submit' value='Place Order'>";
    echo "</form>";

    mysqli_free_result($result);
    mysqli_close($conn);
}