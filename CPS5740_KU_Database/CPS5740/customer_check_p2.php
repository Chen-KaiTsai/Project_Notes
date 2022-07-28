<?php

// check if customer already login
if (isset($_COOKIE["customer_login_id"])) {
    $login_id = $_COOKIE["customer_login_id"];
    $sql_str = "SELECT first_name, last_name, address, city, zipcode, state FROM CUSTOMER WHERE login_id = '$login_id'";
    $need_cookie=false;
}
else {
    // check if fields are not empty
    $empty_check = false;
    if ($_POST["login_id"] == "" || !isset($_POST["login_id"])) {
        echo ("Login id ");
        $empty_check = true;
    }
    if ($_POST["password"] == "" || !isset($_POST["password"])) {
        echo ("Password ");
        $empty_check = true;
    }
    if ($empty_check) {
        echo ("should not be empty<br>");
        die("Login failed");
    }

    // Login ID is not case sensitive => strtolower
    $login_id = strtolower($_POST["login_id"]);
    $password = $_POST["password"];

    $sql_str = "SELECT first_name, last_name, address, city, zipcode, state FROM CUSTOMER WHERE login_id = '$login_id' AND password = '$password'";
    $need_cookie=true;
}
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

    die("Authentication error, please login as customer");
    
} elseif ($num_rows > 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error");
}
$row = $result->fetch_row();

//echo $row[0];
//echo $result->field_count;
//echo $login_id;
//echo $password;
if($need_cookie)
    setcookie("customer_login_id", $login_id, time() + 6000); // Store login_id in cookie live for 100min

// display welcome message

echo "Welcome customer: <b>" . $row[0] . " " . $row[1] . "</b><br>";
echo $row[2] . " ," . $row[3] . " ," . $row[5] . " " . $row[4] . "</br>";

if (!empty($_SERVER['HTTP_CLIENT_IP']))
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
//whether ip is from proxy
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
//whether ip is from remote address
else
    $ip_address = $_SERVER['REMOTE_ADDR'];

echo "Your IP: " . $ip_address . "<br>";

$from_kean = false;
$ip_strings = explode(".", $ip_address);

if ($ip_strings[0] == "10")
    $from_kean = true;
elseif ($ip_strings[0] == "131" && $ip_strings[1] == "125")
    $from_kean = true;

if ($from_kean)
    echo "You are from Kean University<br>";
else
    echo "You are NOT from Kean University<br>";

/*
echo "Address: " . $row[2] . "<br>";
echo "city: " . $row[3] . "<br>";
echo "zipcode: " . $row[4] . "<br>";
*/

// Display logout link
echo "<a href='cps5740_customer_logout_phase1.php'>Customer logout</a><br>";
// Display update link
echo "<a href='cps5740_customer_update_phase2.php'>Update my data</a><br>";
// View order history
echo "<a href='customer_order_history.php'>View my order history</a><br>";

?>

<!DOCTYPE html>
<html>

<head>
    <title>Customer Homepage</title>
</head>

<body>
    <form action="search_product.php" method="get">
        Search Product:<br> (* for all, multiple keyword seperate by the space.)<br>
        </br><input type="text", name="search_text">
        <input type="submit" name="submit" value="Submit">
    </form>
<?php
    //echo "<br>TODO: Display advertisement here<br>";

    // Extract last search text
    $result = $conn->query("SELECT search_text FROM CUSTOMER_ADVERTISEMENT WHERE login_id='$login_id'");
    if ($result == NULL) {
        mysqli_close($conn);
    
        die("Syntax error or Database deny access");
    }
    $num_rows = mysqli_num_rows($result);
    if ($num_rows <= 0) {
        // Search for other
        $last_search_text = 'Other';
    }
    else {
        // Search for 
        $row = $result->fetch_row();
        $last_search_text = $row[0];
    }

    // Search Advertisement
    $sql_str = "SELECT image, description, url FROM CPS5740.Advertisement WHERE category REGEXP '$last_search_text' OR description REGEXP '$last_search_text' ORDER BY id";
    $result = $conn->query($sql_str);
    if ($result == NULL) {
        mysqli_close($conn);
    
        die("Syntax error or Database deny access");
    }

    $row = $result->fetch_row();
    if ($row[2] != NULL) {
        echo "<a href='$row[2]' target='_blank'>";
        echo "<img src='data:image/jpeg;base64,".base64_encode($row[0])."'/>";
        echo "</a>";
        echo "<br>'$row[1]'";
    } else {
        echo "<img src='data:image/jpeg;base64,".base64_encode($row[0])."'/>";
        echo "<br>'$row[1]'";
    }
?>
    <br><a href="cps5740_phase2.php">Project homepage</a><br>

</body>
</html>