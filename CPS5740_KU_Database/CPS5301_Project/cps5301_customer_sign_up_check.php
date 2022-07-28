<?php
// check if fields are not empty

$empty_check=false;
if($_POST["c_email"]=="" || !isset($_POST["c_email"])) {
    echo("Email<br>");
    $empty_check=true;
}
if($_POST["c_password"]=="" || !isset($_POST["c_password"])) {
    echo("Password<br>");
    $empty_check=true;
}
if($_POST["c_retype_password"]=="" || !isset($_POST["c_retype_password"])) {
    echo("Retype password<br>");
    $empty_check=true;
}
if($_POST["c_first_name"]=="" || !isset($_POST["c_first_name"])) {
    echo("First name<br>");
    $empty_check=true;
}
if($_POST["c_last_name"]=="" || !isset($_POST["c_last_name"])) {
    echo("Last_name<br>");
    $empty_check=true;
}
if($_POST["c_tel"]=="" || !isset($_POST["c_tel"])) {
    echo("TEL<br>");
    $empty_check=true;
}
if($_POST["c_address"]=="" || !isset($_POST["c_address"])) {
    echo("Address<br>");
    $empty_check=true;
}
if($_POST["c_city"]=="" || !isset($_POST["c_city"])) {
    echo("City<br>");
    $empty_check=true;
}
if($_POST["c_zipcode"]=="" || !isset($_POST["c_zipcode"])) {
    echo("Zipcode<br>");
    $empty_check=true;
}
if($_POST["c_state"]=="" || !isset($_POST["c_state"])) {
    echo("State<br>");
    $empty_check=true;
}
if($empty_check) {
    echo("should not be empty<br>");
    die("Sign up failed");
}

function isValidZipCode($zipCode) {
    return (preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $zipCode)) ? true : false;
}

function isDigits(string $s, int $minDigits = 9, int $maxDigits = 14): bool {
    return preg_match('/^[0-9]{'.$minDigits.','.$maxDigits.'}\z/', $s);
}

function isValidTelephoneNumber(string $telephone, int $minDigits = 10, int $maxDigits = 10): bool {
    // remove white space, dots, hyphens and brackets
    $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone); 

    // are we left with digits only?
    return isDigits($telephone, $minDigits, $maxDigits); 
}

// Check if the password are matched
if($_POST["c_password"] != $_POST["c_retype_password"])
    die("Retype_password doese not match the password");

if (!isValidTelephoneNumber($_POST["c_tel"]))
    die("Invalid phone number");

if (!isValidZipCode($_POST["c_zipcode"]))
    die("Invalid zipcode");


// put post values into variables
$c_email = $_POST["c_email"];
$c_password = $_POST["c_password"];
$c_first_name = $_POST["c_first_name"];
$c_last_name = $_POST["c_last_name"];
$c_tel = $_POST["c_tel"];
$c_address = $_POST["c_address"];
$c_city = $_POST["c_city"];
$c_zipcode = $_POST["c_zipcode"];
$c_state = $_POST["c_state"];


//print($c_email." ".$c_password." ".$c_first_name." ".$c_last_name." ".$c_tel." ".$c_address." ".$c_city." ".$c_zipcode." ".$c_state."<br>");

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

// Check if login_id is duplicated
$result = $conn->query("SELECT email FROM CPS5301_Customer_test WHERE email='$c_email'");
if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Email ".$c_email." already used.");
}

$sql_str="INSERT INTO CPS5301_Customer_test (email, password, first_name, last_name, TEL, address, city, zipcode, state)
VALUES ('$c_email', '$c_password', '$c_first_name', '$c_last_name', '$c_tel', '$c_address', '$c_city', '$c_zipcode', '$c_state');";

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