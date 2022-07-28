<?php
// check if employee login and provide correct logout link

if (!isset($_COOKIE["employee_login_id"])) {
    die("Authentication error, please login.");
}

$login_id = $_COOKIE["employee_login_id"];

include "dbconfig.php";
$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "CPS5740")
    or die("Fail to connect the database" . mysqli_connect_error());

$result = $conn->query("SELECT employee_id, role FROM EMPLOYEE2 WHERE login='$login_id'");

if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if ($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Authentication error, please login.");
} elseif ($num_rows > 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error");
}
$row = $result->fetch_row();

if ($row[1] == "M")
    $role = "Manager";
elseif ($row[1] == "E")
    $role = "Employee";

$employee_id = $row[0];

mysqli_free_result($result);
mysqli_close($conn);

echo "<a href='cps5740_employee_logout_phase1.php'>".$role." Logout</a></br>";
$footer_link = "<br><br><a href='cps5740_employee_login_pwdcheck_phase2.php'>Employee home page</a><br><a href='cps5740_phase2.php'>Project home page</a><br>";

// connect to database
$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

$update_count = 0;

// find post array size
for ($i = 0; $i < count($_POST['product_id']); $i++) {

    // check if the field empty
    $empty_check = false;
    if ($_POST["product_id"][$i] == "" || !isset($_POST["product_id"][$i])) {
        echo ("product_id ");
        $empty_check = true;
    }
    if ($_POST["product_name"][$i] == "" || !isset($_POST["product_name"][$i])) {
        echo ("Product Name ");
        $empty_check = true;
    }
    if ($_POST["product_description"][$i] == "" || !isset($_POST["product_description"][$i])) {
        echo ("Product Description ");
        $empty_check = true;
    }
    if ($_POST["product_cost"][$i] == "" || !isset($_POST["product_cost"][$i])) {
        echo ("Product Cost ");
        $empty_check = true;
    }
    if ($_POST["product_price"][$i] == "" || !isset($_POST["product_price"][$i])) {
        echo ("Product Price ");
        $empty_check = true;
    }
    if ($_POST["product_stock"][$i] == "" || !isset($_POST["product_stock"][$i])) {
        echo ("Product Stock ");
        $empty_check = true;
    }
    if ($_POST["product_vendor"][$i] == "" || !isset($_POST["product_vendor"][$i])) {
        echo ("Product Vendor ");
        $empty_check = true;
    }
    if ($_POST["product_imployee_name"][$i] == "" || !isset($_POST["product_imployee_name"][$i])) {
        echo ("product_imployee_name ");
        $empty_check = true;
    }
    if ($empty_check) {
        echo " should not be empty. ".$_POST['product_id'][$i]." no update processed</br>";
        continue;
    }

    //echo $_POST['product_id'][$i]." ready for update</br>";

    // put POST data into variables
    $product_id = $_POST['product_id'][$i];
    $product_name = $_POST["product_name"][$i];
    $product_description = $_POST["product_description"][$i];
    $product_cost = $_POST["product_cost"][$i];
    $product_price = $_POST["product_price"][$i];
    $product_stock = $_POST["product_stock"][$i];
    $product_vendor = $_POST["product_vendor"][$i];
    $product_imployee_name = $_POST["product_imployee_name"][$i];

    // check constraint of the update values
    if($product_price < 0) { 
        echo $product_id." update failed with product price value < 0<br>";
        continue;
    }
    if($product_price < 0) {
        echo $product_id." update failed with product price value < 0<br>";
        continue;
    }
    if($product_cost < 0) {
        echo $product_id." update failed with product cost value < 0<br>";
        continue;
    }
    if($product_stock < 0) {
        echo $product_id." update failed with product stock value < 0<br>";
        continue;
    }
    if($product_price < $product_cost) {
        echo $product_id." update failed with product price < product cost<br>";
        continue;
    }
    // check if item need update or not

    // Extract employee id from employee table using name
    $result = $conn->query("SELECT employee_id FROM CPS5740.EMPLOYEE2 WHERE name='$product_imployee_name'");

    if($result == NULL)
    {
        mysqli_close($conn);
        die("employee id extract failed on product".$i."<br>");
    }

    $row = $result->fetch_row();
    $product_imployee_id = $row[0];

    $sql_str = "SELECT * FROM PRODUCT 
    WHERE id=$product_id AND name='$product_name' AND description='$product_description' AND vendor_id=$product_vendor AND 
    cost=$product_cost AND sell_price=$product_price AND quantity=$product_stock AND employee_id=$product_imployee_id";

    //echo $sql_str."</br>";

    $result = $conn->query($sql_str);
    if($result == NULL)
    {
        mysqli_close($conn);
        die("Database failed on check update or not on ".$i."<br>");
    }
    $num_rows = mysqli_num_rows($result);
    if ($num_rows == 1) {
        echo $product_id.' No update needed</br>';
        continue;
    }
    elseif($num_rows > 1) {
        die("database error");
    }
    else {
        //echo $product_id." Need update</br>";
        $update_count++;
        $sql_str = "UPDATE PRODUCT SET name='$product_name', description='$product_description', vendor_id=$product_vendor,
        cost=$product_cost, sell_price=$product_price, quantity=$product_stock, employee_id=$employee_id WHERE id=$product_id";

        if (!$conn->query($sql_str)) {
            printf("Database Update Failed.<br>" . $conn->error);
            mysqli_close($conn);

            die();
        }
        echo "Successfully update product ID: ".$product_id."<br>";
    }
}

echo $update_count." products were updated";

echo $footer_link;