<?php
// check if fields are not empty
if (!isset($_COOKIE["customer_login_id"])) {
    die("Please login First.");
    //header("Location: cps5740_customer_login_phase1.php");
}
$login_id = $_COOKIE["customer_login_id"];

$empty_check = false;
if ($_POST["password"] == "" || !isset($_POST["password"])) {
    echo ("Password<br>");
    $empty_check = true;
}
if ($_POST["first_name"] == "" || !isset($_POST["first_name"])) {
    echo ("First name<br>");
    $empty_check = true;
}
if ($_POST["last_name"] == "" || !isset($_POST["last_name"])) {
    echo ("Last_name<br>");
    $empty_check = true;
}
if ($_POST["tel"] == "" || !isset($_POST["tel"])) {
    echo ("TEL<br>");
    $empty_check = true;
}
if ($_POST["address"] == "" || !isset($_POST["address"])) {
    echo ("Address<br>");
    $empty_check = true;
}
if ($_POST["city"] == "" || !isset($_POST["city"])) {
    echo ("City<br>");
    $empty_check = true;
}
if ($_POST["zipcode"] == "" || !isset($_POST["zipcode"])) {
    echo ("Zipcode<br>");
    $empty_check = true;
}
if ($_POST["state"] == "-----" || !isset($_POST["state"])) {
    echo ("State<br>");
    $empty_check = true;
}
if ($empty_check) {
    echo ("should not be empty<br>");
    die("Update failed");
}

$password = $_POST["password"];
$first_name = $_POST["first_name"];
$last_name = $_POST["last_name"];
$tel = $_POST["tel"];
$address = $_POST["address"];
$city = $_POST["city"];
$zipcode = $_POST["zipcode"];
$state = $_POST["state"];

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

$sql_str = "UPDATE CUSTOMER SET password = '$password', first_name = '$first_name', last_name = '$last_name', TEL = '$tel', address = '$address', city = '$city', zipcode = '$zipcode', state = '$state' WHERE login_id = '$login_id'";

if (!$conn->query($sql_str)) {
    printf("Database Update Failed.<br>" . $conn->error);
    mysqli_close($conn);

    die();
}

echo("Customer update successfully.");
mysqli_close($conn);
?>
