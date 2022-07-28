<?php

// check if fields are not empty
$empty_check = false;
if ($_POST["report_id"] == "" || !isset($_POST["report_id"])) {
    echo "Report ID ";
    $empty_check = true;
}
if ($empty_check) {
    echo "should not be empty<br>";
    die("Please try again");
}

$report_id = $_POST["report_id"];

$sql_str = "SELECT * FROM CPS5301_Feedback_test WHERE fid = '$report_id'";

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

    die("Don't have account associate with this email.");
} elseif ($num_rows > 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("database error");
}

$row = $result->fetch_row();

echo "<h2>Report </h2>".$row[0]."<br>";
echo "<h2>Subject</h2><br>".$row[6]."<br>";
echo "<h2>Comment</h2><br>".$row[1]."<br>";

echo "<h2>From</h2><br>email: ".$row[2]."<br>FirstName: ".$row[3]."<br>LastName: ".$row[4]."<br>TEL: ".$row[5];

?>