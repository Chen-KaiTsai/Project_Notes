<?php
// check if fields are not empty
$empty_check=false;
if($_POST["login_id"]=="" || !isset($_POST["login_id"])) {
    echo("Login id ");
    $empty_check=true;
}
if($_POST["password"]=="" || !isset($_POST["password"])) {
    echo("Password ");
    $empty_check=true;
}
if($empty_check) {
    echo("should not be empty<br>");
    die("Login failed");
}

// Login ID is not case sensitive => strtolower
$login_id = strtolower($_POST["login_id"]); 
$password = $_POST["password"];

// access database

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "CPS5740")
    or die("Fail to connect the database". mysqli_connect_error());

$result = $conn->query("SELECT password, name, role FROM EMPLOYEE WHERE login='$login_id'");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Login ID ".$login_id." doesn't exist in the database.");
}
elseif($num_rows > 1)
{
    mysqli_free_result($result);
    mysqli_close($conn);
    
    die("database error");
}
$row = $result->fetch_row();

//echo $row[0];
//echo $result->field_count;
//echo $login_id;
//echo $password;

if($row[0] == $password) {
    setcookie("employee_login_id", $login_id, time() + 6000); // Store login_id in cookie live for 100min
    
    // display welcome message
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    //whether ip is from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    //whether ip is from remote address
    else
        $ip_address = $_SERVER['REMOTE_ADDR'];
    
    echo "Your IP: ".$ip_address."<br>";
    
    $from_kean = false;
    $ip_strings = explode(".", $ip_address);

    if($ip_strings[0] == "10")
        $from_kean = true;
    elseif($ip_strings[0] == "131" && $ip_strings[1]=="125")
        $from_kean = true;   
    
    if($from_kean)
        echo "You are from Kean University<br>";
    else
        echo "You are not from kean University<br>";
    
    if($row[2]=="M")
        $role="manager";
    elseif($row[2]=="E")
        $role="employee";

    echo "Welcome ".$role.": <b>".$row[1]."</b><br>";

    //Display logout link
    echo "<a href='cps5740_employee_logout_phase1.php'>".$role." logout</a>";
}
else
    die("Employee ".$login_id." exists, but password doesn't match the record in database");   

mysqli_free_result($result);
mysqli_close($conn);

?>
