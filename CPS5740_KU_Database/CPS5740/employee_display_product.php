<?php

if (!isset($_COOKIE["employee_login_id"])) {
    die("Authentication error, please login.");
}

$employee_login_id = $_COOKIE["employee_login_id"];

include "dbconfig.php";
$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "CPS5740")
    or die("Fail to connect the database" . mysqli_connect_error());

$result = $conn->query("SELECT role, name FROM EMPLOYEE2 WHERE login='$employee_login_id'");

if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if ($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Authentication error, please login.");
} elseif ($num_rows > 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error");
}
$row = $result->fetch_row();

if ($row[0] == "M")
    $role = "Manager";
elseif ($row[0] == "E")
    $role = "Employee";

echo "<a href='cps5740_employee_logout_phase1.php'>" . $role . " Logout</a></br>";

mysqli_free_result($result);
mysqli_close($conn);

$footer_link = "<br><br><a href='cps5740_employee_login_pwdcheck_phase2.php'>Employee home page</a><br><a href='cps5740_phase2.php'>Project home page</a><br>";

// check if field empty
if ($_POST["search_text"] == "" || !isset($_POST["search_text"])) {
    die("Please enter search text." . $footer_link);
}

// search product in database
$search_text = strtolower($_POST['search_text']);
$search_text = str_replace(" ", "|", $search_text);

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

// Store Vendor id and Names for 

$result = $conn->query("SELECT vendor_id, name FROM CPS5740.VENDOR");

if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}

while ($row = $result->fetch_row()) {
    $vendor_info[$row[0]] = $row[1];
}

/*
foreach ($vendor_info as $key => $value) {
    echo $key." ".$value;
}
*/

// search database with search_text
if ($search_text=="*") {
$sql_str =
"SELECT P.id AS 'Product ID', P.name AS 'Product Name', P.description AS 'Dscription', P.cost AS Cost, P.sell_price AS 'Sell Price', P.quantity AS 'Avaliable Quantity', V.name AS 'Vendor Name', E.name AS 'Last Update By' 
FROM PRODUCT P, CPS5740.VENDOR V, CPS5740.EMPLOYEE2 E
WHERE P.vendor_id = V.vendor_id AND P.employee_id = E.employee_id";
}
else {
$sql_str =
"SELECT P.id AS 'Product ID', P.name AS 'Product Name', P.description AS 'Dscription', P.cost AS Cost, P.sell_price AS 'Sell Price', P.quantity AS 'Avaliable Quantity', V.name AS 'Vendor Name', E.name AS 'Last Update By' 
FROM PRODUCT P, CPS5740.VENDOR V, CPS5740.EMPLOYEE2 E
WHERE P.vendor_id = V.vendor_id AND P.employee_id = E.employee_id AND (P.name REGEXP '$search_text' OR P.description REGEXP '$search_text')";
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
}

echo "<table border='1'>";
echo "<tr algin='center'>";
while ($field = $result->fetch_field())
    echo "<td>" . $field->name . "</td>";
echo "</tr>";

echo "<form action='cps5740_product_update_check.php' method='post'>";
// counter for number of the product
$i = 0;
while ($row = $result->fetch_row()) {
    echo "<tr>";
    echo "<td>" . $row[0] . "</td>";
    echo "<input type='hidden' name='product_id[$i]' value='$row[0]'>";
    echo "<td><input type='text' name='product_name[$i]' value='$row[1]'></td>";
    echo "<td><input type='text' name='product_description[$i]' value='$row[2]'></td>";
    echo "<td><input type='text' name='product_cost[$i]' value='$row[3]'></td>";
    echo "<td><input type='text' name='product_price[$i]' value='$row[4]'></td>";
    echo "<td><input type='text' name='product_stock[$i]' value='$row[5]'></td>";

    // vendor name drop down
    $current_vendor = array_search($row[6], $vendor_info);
    echo "<td>";
    echo "<select name='product_vendor[$i]'>";
    echo "<option value='$current_vendor' selected='selected' hidden='hidden'>$row[6]</option>";
    foreach ($vendor_info as $key => $value) {
        echo "<option value='$key'>$value</option>";
    }
    echo "</select>";
    echo "</td>";

    echo "<td>". $row[7] . "</td>";
    echo "<input type='hidden' name='product_imployee_name[$i]' value='$row[7]'>";
    echo "</tr>";

    $i += 1; // increase i for next product
}
echo "</table>";
echo "<input type='submit' value='Update Product'>";
echo "</form>";

echo $footer_link;