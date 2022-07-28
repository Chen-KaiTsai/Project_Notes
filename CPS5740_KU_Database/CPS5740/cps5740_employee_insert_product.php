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

?>

<!DOCTYPE html>
<html>

<head>
    <title>Employee add products</title>
</head>

<body>
    <h2>Add Products</h2>

    <form action="employee_insert_product.php" method="post">
        Product Name: <input type="text" name="product_name"></br>
        Description: <input type="text" name="product_description"></br>
        Cost: <input type="text" name="product_cost"></br>
        Sell Price: <input type="text" name="product_price"></br>
        Quantity: <input type="text" name="product_stock"></br>
        Select Vendor: <select name="product_vendor">
        <?php
        /*
        include "dbconfig.php";
        $conn = mysqli_connect($servername, $db_username, $db_password)
            or die("Fail to connect the server". mysqli_connect_error());
        mysqli_select_db($conn, "CPS5740")
            or die("Fail to connect the database". mysqli_connect_error());
        */
        $result = $conn->query("SELECT vendor_id, name FROM VENDOR");

        if($result == NULL)
        {
            mysqli_close($conn);

            die("Syntax error or Database deny access");
        }

        while($row = $result->fetch_row()) {
            echo "<option value='$row[0]'>$row[1]</option>";
        }

        mysqli_free_result($result);
        mysqli_close($conn);
        ?>
        </select></br>
        </br>
        <input type="submit" name="submit" value="Submit">
    </form>

</body>
</html>