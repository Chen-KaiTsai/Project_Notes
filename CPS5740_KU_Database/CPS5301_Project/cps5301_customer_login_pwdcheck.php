<?php

// check if fields are not empty
$empty_check = false;
if ($_POST["customer_email"] == "" || !isset($_POST["customer_email"])) {
    echo "Email ";
    $empty_check = true;
}
if ($_POST["customer_password"] == "" || !isset($_POST["customer_password"])) {
    echo "Password ";
    $empty_check = true;
}
if ($empty_check) {
    echo "should not be empty<br>";
    die("Login failed");
}

$customer_email = $_POST["customer_email"];
$customer_password = $_POST["customer_password"];

$sql_str = "SELECT password FROM CPS5301_Customer_test WHERE email = '$customer_email'";
// access database

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

$result = $conn->query($sql_str);
if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if ($num_rows <= 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Authentication error, please try again.");
} elseif ($num_rows > 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error");
}
$row = $result->fetch_row();

if ($row[0] == $customer_password) {
    setcookie("customer_login", $customer_email, time() + 60); // Store email in cookie live for 1min
    echo "Customer login successfully cookie set";
    //header('Location: productpage');
}
else {
    echo "Authentication error, please try again";
}
mysqli_free_result($result);
mysqli_close($conn);