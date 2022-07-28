<?php

if (!isset($_COOKIE["admin_login"])) {
    die("Authentication error, please login.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add_Product</title>
</head>

<body>
    <h2>Add Product</h2>

    <form action="cps5301_admin_addproduct_check.php" method="post" enctype="multipart/form-data">
        Product Name: <input type="text" name="product_name"></br>
        Select Image File: <input type="file" name="image"></br>
        Product Price: <input type="text" name="product_price"></br>
        Product Class:
        <select name="product_class">
        <option value="PAP">Papers</option>
        <option value="TSS">Teacher & Student Supplies</option>
        <option value="WRS">Writing Supplies</option>
        <option value="COM">Computer & Accessories</option>
        </select></br>
        Product Stock: <input type="text" name="product_stock"></br>
        Product Description </br>
        <textarea name="product_decription" rows="10" cols="80"></textarea></br>

        <input type="submit" name="submit" value="Submit">
    </form>

</body>
</html>