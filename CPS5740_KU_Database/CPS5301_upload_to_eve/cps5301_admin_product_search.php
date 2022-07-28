<?php

// check if admin login
if (!isset($_COOKIE["admin_login"])) {
    die("Authentication error, please login.");
}

if ($_POST["search_text"] == "" || !isset($_POST["search_text"])) {
    die("Please enter search text.</br>");
}

//echo $_POST['search_text']."</br>";

$search_text = strtolower($_POST['search_text']);

$search_text = str_replace(" ", "|", $search_text);

//echo $search_text."</br>";

// connect to database
include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database". mysqli_connect_error());

// search database with search_text
$result = $conn->query("SELECT pid, name, price, class, stocks FROM CPS5301_Product_test WHERE name REGEXP '$search_text' OR description REGEXP '$search_text';");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}

$num_rows = mysqli_num_rows($result);

if($num_rows == 0)
{
    echo "No product found</br>";

    mysqli_free_result($result);
    mysqli_close($conn);
}
else // list search results
{
    //echo $num_rows."</br>";
    echo "<table border='1'>";
    echo "<tr algin='center'>";
    while($field=$result->fetch_field())
        echo "<td>".$field->name."</td>";
    echo "<td>Update</td>";
    echo "<td>Delete</td>";
    echo "</tr>";
    while($row = $result->fetch_row())
    {
        echo "<tr algin='center'>";
        for($i = 0;$i < $result->field_count;$i++)
	        echo "<td>".$row[$i]."</td>";
        echo "<td>";
        echo "<form action='cps5301_admin_update_product.php' method='post'>";
        echo "<button name='product_id' type='submit' value='$row[0]'>UPDATE</button>";
        echo "</form>";
        echo "</td>";
        echo "<td>";
        echo "<form action='cps5301_admin_delete_product.php' method='post'>";
        echo "<button name='product_id' type='submit' value='$row[0]'>DELETE</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

mysqli_free_result($result);
mysqli_close($conn);

?>