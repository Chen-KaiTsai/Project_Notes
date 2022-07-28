<?php

// check if fields are not empty
$empty_check = false;
if ($_POST["customer_email"] == "" || !isset($_POST["customer_email"])) {
    echo "Email ";
    $empty_check = true;
}
if ($empty_check) {
    echo "should not be empty<br>";
    die("Please try again");
}

$customer_email = $_POST["customer_email"];

//Check if mail provided exist in the database

$sql_str = "SELECT * FROM CPS5301_Customer_test WHERE email = '$customer_email'";

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

    die("Don't have account associate with this email.");
} elseif ($num_rows > 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error");
}

echo "Please check your email for your new password.";

//Update database with new password

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

$new_password = randomPassword();

// echo $new_password;

$sql_str = "UPDATE CPS5301_Customer_test SET password = '$new_password' WHERE email='$customer_email'";

$result = $conn->query($sql_str);
if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
//Send Email

$message = "Your new password: ".$new_password. "\r\nPlease Login to the system with your new password and change the password.";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
mail($customer_email, 'Your new password for School Supply System', $message);

mysqli_close($conn);
?>