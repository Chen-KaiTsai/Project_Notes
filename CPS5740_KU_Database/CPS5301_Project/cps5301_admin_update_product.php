<?php

if (!isset($_COOKIE["admin_login"])) {
    die("Authentication error, please login.");
}

//echo $_POST['product_id']."</br>";

$product_id = $_POST['product_id'];

//connect to database

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

$result = $conn->query("SELECT pid, name, price, class, stocks, description FROM CPS5301_Product_test WHERE pid = $product_id");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access.</br>");
}
$num_rows = mysqli_num_rows($result);
if($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Database error.</br>");
}
elseif($num_rows > 1)
{
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Database error.</br>");
}

$row = $result->fetch_row();

$classes = array( 
    'PAP' => 'Papers',
    'TSS' => 'Teacher & Student Supplies',
    'WRS' => 'Writing Supplies',
    'COM' => 'Computer & Accessories'
);

$current_class = $classes[$row[3]];

echo "<form action='cps5301_admin_updateproduct_check.php' method='post' enctype='multipart/form-data'>";
echo "<input type='hidden' name='product_id' value=$row[0]>";
echo "Product Name: <input type='text' name='product_name' value=$row[1]></br>";
echo "Select Image File: <input type='file' name='image'></br>";
echo "Product Price: <input type='text' name='product_price' value=$row[2]></br>";
echo "Product Class:";
echo "<select name='product_class'>";
echo "<option value='$row[3]' selected='selected' hidden='hidden'>$current_class</option>";
foreach ($classes as $key => $value) {
    echo "<option value='$key'>$value</option>";
}
echo "</select></br>";
echo "Product Stock: <input type='text' name='product_stock' value=$row[4]></br>";
echo "Product Description </br>";
echo "<textarea name='product_decription' rows='10' cols='80'>$row[5]</textarea></br>";
echo "<input type='submit' name='submit' value='submit'>";
echo"</form>";

mysqli_free_result($result);
mysqli_close($conn);
?>