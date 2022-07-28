<?php

if (!isset($_COOKIE["employee_login_id"])) {
    die("Authentication error, please login.");
}

$employee_login_id = $_COOKIE["employee_login_id"];

include "dbconfig.php";
$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "CPS5740")
    or die("Fail to connect the database". mysqli_connect_error());

$result = $conn->query("SELECT role, name FROM EMPLOYEE2 WHERE login='$employee_login_id'");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Authentication error, please login.");
}
elseif($num_rows > 1)
{
    mysqli_free_result($result);
    mysqli_close($conn);
    
    die("database error");
}
$row = $result->fetch_row();

if($row[0]=="M")
    $role="Manager";
elseif($row[0]=="E")
    $role="Employee";

echo "<a href='cps5740_employee_logout_phase1.php'>".$role." Logout</a></br>";

mysqli_free_result($result);
mysqli_close($conn);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Employee Search Products</title>
</head>

<body>
    <h2>Search Products</h2>

    <form action="employee_display_product.php" method="post">
        Search Product: </br><input type="text", name="search_text">
        <input type="submit" name="submit" value="Submit">
    </form>

</body>
</html>