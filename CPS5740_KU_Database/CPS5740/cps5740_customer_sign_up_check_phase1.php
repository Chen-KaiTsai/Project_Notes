<?php
// check if fields are not empty
$empty_check=false;
if($_POST["login_id"]=="" || !isset($_POST["login_id"])) {
    echo("Login ID<br>");
    $empty_check=true;
}
if($_POST["password"]=="" || !isset($_POST["password"])) {
    echo("Password<br>");
    $empty_check=true;
}
if($_POST["retype_password"]=="" || !isset($_POST["retype_password"])) {
    echo("Retype password<br>");
    $empty_check=true;
}
if($_POST["first_name"]=="" || !isset($_POST["first_name"])) {
    echo("First name<br>");
    $empty_check=true;
}
if($_POST["last_name"]=="" || !isset($_POST["last_name"])) {
    echo("Last_name<br>");
    $empty_check=true;
}
if($_POST["tel"]=="" || !isset($_POST["tel"])) {
    echo("TEL<br>");
    $empty_check=true;
}
if($_POST["address"]=="" || !isset($_POST["address"])) {
    echo("Address<br>");
    $empty_check=true;
}
if($_POST["city"]=="" || !isset($_POST["city"])) {
    echo("City<br>");
    $empty_check=true;
}
if($_POST["zipcode"]=="" || !isset($_POST["zipcode"])) {
    echo("Zipcode<br>");
    $empty_check=true;
}
if($_POST["state"]=="" || !isset($_POST["state"])) {
    echo("State<br>");
    $empty_check=true;
}
if($empty_check) {
    echo("should not be empty<br>");
    die("Sign up failed");
}

if($_POST["password"] != $_POST["retype_password"])
    die("Retype_password doese not match the password");


$login_id = strtolower($_POST["login_id"]);
$password = $_POST["password"];
$first_name = $_POST["first_name"];
$last_name = $_POST["last_name"];
$tel = $_POST["tel"];
$address = $_POST["address"];
$city = $_POST["city"];
$zipcode = $_POST["zipcode"];
$state = $_POST["state"];


//print($login_id." ".$password." ".$first_name." ".$last_name." ".$tel." ".$address." ".$city." ".$zipcode." ".$state."<br>");

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

// Check if login_id is duplicated
$result = $conn->query("SELECT login_id FROM CUSTOMER WHERE login_id='$login_id'");
if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Login ID ".$login_id." already exist in the database.");
}

$sql_str="INSERT INTO CUSTOMER (login_id, password, first_name, last_name, TEL, address, city, zipcode, state )
VALUES ('$login_id', '$password', '$first_name', '$last_name', '$tel', '$address', '$city', '$zipcode', '$state');";

if (!$conn->query($sql_str)) {
    echo("Database Insert Failed.<br>".$conn->error);
    mysqli_free_result($result);
    mysqli_close($conn);

    die();
}

echo("Customer sign up successfully.");

mysqli_free_result($result);
mysqli_close($conn);
?>
