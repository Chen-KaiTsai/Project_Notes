<?php

if (!isset($_COOKIE["admin_login"])) {
    die("Authentication error, please login.");
}

$login_id = $_COOKIE["admin_login"];

// Access database
include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

$result = $conn->query("SELECT name FROM CPS5301_Administrator_test WHERE login_id='$login_id'");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access.</br>");
}
$num_rows = mysqli_num_rows($result);
if($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Authentication error, please login.</br>");
}
elseif($num_rows > 1)
{
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error.");
}
$row = $result->fetch_row();

// display welcome message

echo "Admin: ".$row[0]." ";

$d = new DateTime('now');
$d->setTimezone(new DateTimeZone('UTC'));
echo $d->format('Y-m-d H:i:s');

if (!empty($_SERVER['HTTP_CLIENT_IP']))
$ip_address = $_SERVER['HTTP_CLIENT_IP'];
//whether ip is from proxy
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
//whether ip is from remote address
else
$ip_address = $_SERVER['REMOTE_ADDR'];

echo " IP: ".$ip_address."</br>";

echo "<a href='cps5301_admin_addproduct.php'>Add item</a>";

echo "<form action='cps5301_admin_product_search.php' method='post'>";
echo "Search<input type='text' name='search_text'><br>";
echo "<input type='submit' value='Search'>";
?>