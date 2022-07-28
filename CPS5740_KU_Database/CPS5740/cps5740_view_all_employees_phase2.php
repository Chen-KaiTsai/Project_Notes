<!DOCTYPE html>
<html>
<head>
    <title>View_All_Employees</title>
</head>
<body>

<?php
echo "The following employees are in the database<br>";

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server". mysqli_connect_error());
mysqli_select_db($conn, "CPS5740")
    or die("Fail to connect the database". mysqli_connect_error());

$result = $conn->query("SELECT * FROM EMPLOYEE2");

if($result == NULL)
{
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if($num_rows == 0) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Database error or no result");
}

echo "<table border='1'>";
echo "<tr algin='center'>";
while($field=$result->fetch_field())
    echo "<td>".$field->name."</td>";
echo "</tr>";
while($row = $result->fetch_row())
{
echo "<tr>";
for($i = 0;$i < $result->field_count;$i++)
	echo "<td>".$row[$i]."</td>";
echo "</tr>";
}
echo "</table>";

mysqli_free_result($result);
mysqli_close($conn);
?>

</body>
</html>