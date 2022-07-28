<?php

// check if employee login and provide correct logout link

if (!isset($_COOKIE["employee_login_id"])) {
    die("Authentication error, please login.");
}

$login_id = $_COOKIE["employee_login_id"];

include "dbconfig.php";
$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "CPS5740")
    or die("Fail to connect the database". mysqli_connect_error());

$result = $conn->query("SELECT employee_id, role FROM EMPLOYEE2 WHERE login='$login_id'");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Authentication error, please login.");
}
elseif($num_rows > 1)
{
    mysqli_free_result($result);
    mysqli_close($conn);
    
    die("database error");
}
$row = $result->fetch_row();

if($row[1]=="M")
    $role="Manager";
elseif($row[1]=="E")
    $role="Employee";

$employee_id = $row[0];

mysqli_free_result($result);
mysqli_close($conn);

// check if fields are not empty
$empty_check=false;
if($_POST["product_name"]=="" || !isset($_POST["product_name"])) {
    echo("Product Name<br>");
    $empty_check=true;
}
if($_POST["product_description"]=="" || !isset($_POST["product_description"])) {
    echo("Product Description<br>");
    $empty_check=true;
}
if($_POST["product_cost"]=="" || !isset($_POST["product_cost"])) {
    echo("Product Cost<br>");
    $empty_check=true;
}
if($_POST["product_price"]=="" || !isset($_POST["product_price"])) {
    echo("Product Price<br>");
    $empty_check=true;
}
if($_POST["product_stock"]=="" || !isset($_POST["product_stock"])) {
    echo("Product Stock<br>");
    $empty_check=true;
}
if($_POST["product_vendor"]=="" || !isset($_POST["product_vendor"])) {
    echo("Product Vendor<br>");
    $empty_check=true;
}
if($empty_check) {
    echo("should not be empty<br>");
    die("Add product failed");
}

echo "<a href='cps5740_employee_logout_phase1.php'>".$role." Logout</a></br>";
$footer_link = "<br><br><a href='cps5740_employee_login_pwdcheck_phase2.php'>Employee home page</a><br><a href='cps5740_phase2.php'>Project home page</a><br>";

$product_name = $_POST["product_name"];
$product_description = $_POST["product_description"];
$product_cost = $_POST["product_cost"];
$product_price = $_POST["product_price"];
$product_stock = $_POST["product_stock"];
$product_vendor = $_POST["product_vendor"];

//echo $product_name." ".$product_description." ".$product_cost." ".$product_price." ".$product_stock." ".$product_vandor."<br>";

// check if fields satisfied restriction

if($product_cost > $product_price)
    die("The product cost is more than the product sell price, add product failed".$footer_link);
if($product_stock < 0)
    die("The product stock is less than 0".$footer_link);

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

$result = $conn->query("SELECT name FROM PRODUCT WHERE name='$product_name'");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access".$footer_link);
}
$num_rows = mysqli_num_rows($result);
if($num_rows != 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Product name duplicated, please provide other product name".$footer_link);
}

//echo "TODO add product";

$sql_str="INSERT INTO PRODUCT (description, name, vendor_id, cost, sell_price, quantity, employee_id)
VALUES ('$product_description', '$product_name', '$product_vendor', '$product_cost', '$product_price', '$product_stock', '$employee_id');";

//echo $sql_str;

if (!$conn->query($sql_str)) {
    echo("Database Insert Failed.<br>".$conn->error);
    mysqli_free_result($result);
    mysqli_close($conn);
    
    die($footer_link);
}

echo "Successfully insert the product: ".$product_name;
echo $footer_link;

mysqli_free_result($result);
mysqli_close($conn);

?>