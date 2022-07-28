<?php

if (!isset($_COOKIE["admin_login"])) {
    die("Authentication error, please login.");
}

// check if fields are not empty

$empty_check=false;
if($_POST["product_id"]=="" || !isset($_POST["product_id"])) {
    echo("ID<br>");
    $empty_check=true;
}
if($_POST["product_name"]=="" || !isset($_POST["product_name"])) {
    echo("Product<br>");
    $empty_check=true;
}
if($_POST["product_price"]=="" || !isset($_POST["product_price"])) {
    echo("Product Price<br>");
    $empty_check=true;
}
if($_POST["product_class"]=="" || !isset($_POST["product_class"])) {
    echo("Product Class<br>");
    $empty_check=true;
}
if($_POST["product_stock"]=="" || !isset($_POST["product_stock"])) {
    echo("Product Stock<br>");
    $empty_check=true;
}
if($_POST["product_decription"]=="" || !isset($_POST["product_decription"])) {
    echo("Product Description<br>");
    $empty_check=true;
}
if($empty_check) {
    echo("should not be empty<br>");
    die("Add product failed");
}

$product_id = $_POST["product_id"];
$product_name = $_POST["product_name"];
$product_price = $_POST["product_price"];
$product_class = $_POST["product_class"];
$product_stock = $_POST["product_stock"];
$product_description = $_POST["product_decription"];

//echo $product_id."</br>".$product_name."</br>".$product_price."</br>".$product_class."</br>".$product_stock."</br>".$product_description."</br>";

include "dbconfig.php";

// Connect to the database
$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

$sql_str = "UPDATE CPS5301_Product_test SET name='$product_name', price='$product_price', class='$product_class', stocks=$product_stock, description='$product_description' WHERE pid=$product_id";

//echo $sql_str;

if (!$conn->query($sql_str)) {
    echo("Database Update Failed.<br>" . $conn->error);
    mysqli_close($conn);

    die();
}

echo "Product update successfully.</br>";

// If file upload form is submitted 
$status = $statusMsg = ''; 
if(isset($_POST["submit"])){ 
    $status = 'error';
    if(!empty($_FILES["image"]["name"])) { 
        // Get file info 
        $fileName = basename($_FILES["image"]["name"]); 
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
         
        // Allow certain file formats 
        $allowTypes = array('jpg'); 
        if(in_array($fileType, $allowTypes)){ 
            $image = $_FILES['image']['tmp_name']; 
            $imgContent = addslashes(file_get_contents($image)); 
         
            // Insert image content into database 
            $insert = $conn->query("UPDATE CPS5301_Product_Images_test SET image = '$imgContent' WHERE pid=$product_id"); 
             
            if($insert){ 
                $status = 'success'; 
                $statusMsg = "File uploaded successfully."; 
            }else{ 
                $statusMsg = "File upload failed, please try again."; 
            }  
        }else{ 
            $statusMsg = 'JPG files are allowed to upload.'; 
        } 
    }else{ 
        $statusMsg = 'Please select an image file to upload.</br> Product image unchanged.'; 
    } 
} 
 
// Display status message 
echo $statusMsg;

mysqli_close($conn);
?>
