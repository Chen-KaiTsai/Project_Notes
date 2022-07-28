<?php

// check if fields are not empty
$empty_check = false;
if ($_POST["admin_login_id"] == "" || !isset($_POST["admin_login_id"])) {
    echo "Login ID ";
    $empty_check = true;
}
if ($_POST["admin_password"] == "" || !isset($_POST["admin_password"])) {
    echo "Password ";
    $empty_check = true;
}
if ($empty_check) {
    echo "should not be empty<br>";
    die("Login failed");
}

$admin_login_id = $_POST["admin_login_id"];
$admin_password = $_POST["admin_password"];

$sql_str = "SELECT password FROM CPS5301_Administrator_test WHERE login_id = '$admin_login_id'";
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

if ($row[0] == $admin_password) {
    setcookie("admin_login", $admin_login_id, time() + 600); // Store id in cookie live for 10min
    echo "Admin login successfully cookie set";
    header('Location: cps5301_admin_homepage.php');
}
else {
    echo "Authentication error, please try again";
}
mysqli_free_result($result);
mysqli_close($conn);