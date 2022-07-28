<?php

// check if field empty
if ($_POST["search_text"] == "" || !isset($_POST["search_text"])) {
    die("Please enter search text." . $footer_link);
}

echo "<a href='cps5740_customer_login_phase2.php'>Customer login</a><br>";

// search product in database
$search_text = strtolower($_POST['search_text']);
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
} else {
    $sql_str =
        "SELECT P.name AS 'Product Name', P.sell_price AS 'Sell Price', P.quantity AS 'Avaliable Quantity', V.name AS 'Vendor Name' 
        FROM PRODUCT P, CPS5740.VENDOR V, CPS5740.EMPLOYEE2 E
        WHERE P.vendor_id = V.vendor_id AND P.employee_id = E.employee_id AND (P.name REGEXP '$search_text' OR P.description REGEXP '$search_text') AND P.quantity > 0";
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
    while ($field = $result->fetch_field())
        echo "<td>" . $field->name . "</td>";
    echo "</tr>";
    while ($row = $result->fetch_row()) {
        echo "<tr>";
        for ($i = 0; $i < $result->field_count; $i++)
            echo "<td>" . $row[$i] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    mysqli_free_result($result);
    mysqli_close($conn);
}

echo "<br><a href='cps5740_phase2.php'>Project homepage</a><br>"

?>