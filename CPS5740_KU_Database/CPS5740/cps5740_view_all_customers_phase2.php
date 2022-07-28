<!DOCTYPE html>
<html>

<head>
    <title>View All Customers</title>
</head>

<body>

    <?php

    if (!isset($_COOKIE["employee_login_id"])) {
        die("This page is for employee only. Please login as a employee or manager.");
        //header("Location: cps5740_employee_login_phase1.php");
    }

    include "dbconfig.php";

    $conn = mysqli_connect($servername, $db_username, $db_password)
        or die("Fail to connect the server" . mysqli_connect_error());
    mysqli_select_db($conn, "2021F_tsaiche")
        or die("Fail to connect the database" . mysqli_connect_error());

    $login_id = $_COOKIE["employee_login_id"];

    $result = $conn->query("SELECT * FROM CPS5740.EMPLOYEE2 WHERE login='$login_id'");

    if ($result == NULL) {
        mysqli_close($conn);

        die("Syntax error or Database deny access");
    }
    $num_rows = mysqli_num_rows($result);
    if ($num_rows == 0) {
        mysqli_free_result($result);
        mysqli_close($conn);

        die("This page is for employee only. Please login as an employee or manager.</br> Login ID " . $login_id . " doesn't exist in the database.");
    } elseif ($num_rows > 1) {
        mysqli_free_result($result);
        mysqli_close($conn);

        die("database error");
    }

    $result = $conn->query("SELECT * FROM CUSTOMER");

    if ($result == NULL) {
        mysqli_close($conn);

        die("Syntax error or Database deny access");
    }
    $num_rows = mysqli_num_rows($result);
    if ($num_rows == 0) {
        mysqli_free_result($result);
        mysqli_close($conn);

        die("Database error or no result");
    }

    echo "The following customer are in the database.</br>";

    echo "<table border='1'>";
    echo "<tr algin='center'>";
    while ($field = $result->fetch_field())
        echo "<td>" . $field->name . "</td>";
    echo "</tr>";
    while ($row = $result->fetch_row()) {
        echo "<tr>";
        for ($i = 0; $i < $result->field_count; $i++)
            echo "<td>" . $row[$i] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    mysqli_free_result($result);
    mysqli_close($conn);

    ?>

</body>

</html>