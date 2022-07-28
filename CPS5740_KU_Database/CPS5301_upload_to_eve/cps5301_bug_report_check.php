<?php

$empty_check=false;
if($_POST["c_email"]=="" || !isset($_POST["c_email"])) {
    echo("Email<br>");
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
if($_POST["subject"]=="" || !isset($_POST["subject"])) {
    echo("The subject<br>");
    $empty_check=true;
}
if($_POST["comment"]=="" || !isset($_POST["comment"])) {
    echo("Comment<br>");
    $empty_check=true;
}
if($_POST["c_tel"]=="" || !isset($_POST["c_tel"])) {
    echo("TEL<br>");
    $empty_check=true;
}
if($empty_check) {
    echo("should not be empty<br>");
    die("Please try again");
}

$c_email = $_POST["c_email"];
$c_first_name = $_POST["c_first_name"];
$c_last_name = $_POST["c_last_name"];
$c_tel = $_POST["c_tel"];
$subject = $_POST["subject"];
$comment = $_POST["comment"];

// Insert into the feedback table
include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

$sql_str="INSERT INTO CPS5301_Feedback_test (review, email, first_name, last_name, TEL, title)
VALUES ('$comment', '$c_email', '$c_first_name', '$c_last_name', '$c_tel', '$subject');";

if (!$conn->query($sql_str)) {
    echo("Database Insert Failed.<br>".$conn->error);
    mysqli_close($conn);

    die();
}

echo("Thank you for your report.");

// Email the developer
// Currently use Chen-Kai Tsai Kean account

$admin_email = 'tsaiche@kean.edu';
$report_id = mysqli_insert_id($conn);

//Send Email

$message = "Receive Report: ".$report_id. "\r\nThe subject: ".$subject."\r\nCustomerName: ".$c_first_name." ".$c_last_name."\r\nEmail: ".$c_email."\r\nTEL: ".$c_tel;

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
mail($admin_email, 'Bug report with id '.$report_id, $message);

mysqli_close($conn);
?>