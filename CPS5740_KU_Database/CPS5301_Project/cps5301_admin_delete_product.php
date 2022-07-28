<?php

if (!isset($_COOKIE["admin_login"])) {
    die("Authentication error, please login.");
}

$product_id = $_POST['product_id'];

include "dbconfig.php";

// Connect to the database
$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());


$sql_str="DELETE FROM CPS5301_Product_test WHERE pid=$product_id;";

//echo $sql_str."</br>";

if (!$conn->query($sql_str)) {
    echo("Product table delete Failed.<br>".$conn->error);
    mysqli_close($conn);

    die();
}
else
    echo "Product delete success.<br>";

$sql_str="DELETE FROM CPS5301_Product_Images_test WHERE pid=$product_id;";

echo $sql_str."</br>";

if (!$conn->query($sql_str)) {
    echo("Product Image table delete Failed.<br>".$conn->error);
    mysqli_close($conn);

    die();
}
else
    echo "Product Image delete success.<br>";