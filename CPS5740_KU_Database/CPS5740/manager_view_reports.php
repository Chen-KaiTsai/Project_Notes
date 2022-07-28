<?php

// check if login as employee
if (!isset($_COOKIE["employee_login_id"])) {
    die("Authentication error, please login.");
}

// no need to check for NULL POST

$report_period = $_POST["report_period"];
$report_type = $_POST["report_type"];

// different type of type period
if ($report_period == "all")
    $report_period_sql_str = "AND True";
elseif ($report_period == "past_week")
    $report_period_sql_str = "AND DATE(O.date) BETWEEN DATE_SUB(now(), INTERVAL 1 WEEK) AND now()";
elseif ($report_period == "past_month")
    $report_period_sql_str = "AND DATE(O.date) BETWEEN DATE_SUB(now(), INTERVAL 1 MONTH) AND now()";
elseif ($report_period == "past_year")
    $report_period_sql_str = "AND DATE(O.date) BETWEEN DATE_SUB(now(), INTERVAL 1 YEAR) AND now()";
elseif ($report_period == "current_month")
    $report_period_sql_str = "AND year(DATE(O.date)) = year(now()) AND month(DATE(O.date))=month(now())";

// access database
include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

// different type or report
if ($report_type == "all_sales") {
    $sql_str = "SELECT P.name AS 'Product Name', V.name AS 'Vendor Name', P.cost AS 'Unit Cost', P.quantity AS 'Current Quantity', PO.quantity AS 'Sold Quantity', P.sell_price AS 'Sold Unit Price', (PO.quantity * P.sell_price) AS 'Sub Total', (PO.quantity * (P.sell_price - P.cost)) AS 'Profit', concat(C.first_name, ' ', C.last_name) AS 'Customer Name', O.date AS 'Order Date'
    FROM PRODUCT P, CPS5740.VENDOR V, CUSTOMER C, PRODUCT_ORDER PO, `ORDER` O
    WHERE PO.product_id = P.id AND PO.order_id = O.id AND P.vendor_id = V.vendor_id AND O.customer_id = C.customer_id ".$report_period_sql_str;

    $count = 1;
    $total = 0;
    $total_profit = 0;
    
    $result = $conn->query($sql_str);

    if ($result == NULL) {
        mysqli_close($conn);

        die("Syntax error or Database deny access");
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        echo "No order history";

        mysqli_free_result($result);
        mysqli_close($conn);
    } else {
        echo "<table border='1'>";
        echo "<tr algin='center'>";

        echo "<td>#</td>";
        while ($field = $result->fetch_field())
            echo "<td>" . $field->name . "</td>";
        echo "</tr>";
        while ($row = $result->fetch_row()) {
            echo "<tr>";
            echo "<td>$count</td>";
            for ($i = 0; $i < $result->field_count; $i++)
                echo "<td>" . $row[$i] . "</td>";
            echo "</tr>";
            
            $count++;
            $total += $row[6];
            $total_profit += $row[7];
        }
        echo "<tr><td>Total</td><td colspan=6></td><td>$total</td><td>$total_profit</td></tr>";
        echo "</table>";
    }

} elseif ($report_type == "products") {
    $sql_str = "SELECT P.name AS 'Product Name', V.name AS 'Vendor Name', P.cost AS 'Unit Cost', P.quantity AS 'Current Quantity', sum(PO.quantity) AS 'Sold Quantity', P.sell_price AS 'Sold Unit Price', (sum(PO.quantity) * P.sell_price) AS 'Sub Total', (sum(PO.quantity) * (P.sell_price - P.cost)) AS 'Profit'
    FROM PRODUCT AS P, PRODUCT_ORDER AS PO, CPS5740.VENDOR AS V, `ORDER` AS O
    WHERE P.id = PO.product_id AND V.vendor_id = P.vendor_id AND O.id = PO.order_id " . $report_period_sql_str . " GROUP BY P.name";

    $total = 0;
    $total_profit = 0;
    $count = 1;

    $result = $conn->query($sql_str);

    if ($result == NULL) {
        mysqli_close($conn);

        die("Syntax error or Database deny access");
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        echo "No order history";

        mysqli_free_result($result);
        mysqli_close($conn);
    } else {
        echo "<table border='1'>";
        echo "<tr algin='center'>";

        echo "<td>#</td>";
        while ($field = $result->fetch_field())
            echo "<td>" . $field->name . "</td>";
        echo "</tr>";
        while ($row = $result->fetch_row()) {
            echo "<tr>";
            echo "<td>$count</td>";
            for ($i = 0; $i < $result->field_count; $i++)
                echo "<td>" . $row[$i] . "</td>";
            echo "</tr>";

            $total += $row[6];
            $total_profit += $row[7];
            $count++;
        }
        echo "<tr><td>Total</td><td colspan=6></td><td>$total</td><td>$total_profit</td></tr>";
        echo "</table>";
    }
} elseif ($report_type == "vendors") {
    //echo "TODO: Vendors for report type";
    /*
    $sql_str = "SELECT V.name, sum(P.quantity) AS 'Quantity in Stock', sum(P.cost * PO.quantity) AS 'Amount to Vendor', sum(PO.quantity) AS 'Sold Quantity', sum(P.sell_price * PO.quantity) AS 'Sub Total Sale', sum((P.sell_price - P.cost) * PO.quantity) AS 'Profit'
    FROM CPS5740.VENDOR V, PRODUCT P, PRODUCT_ORDER PO, `ORDER` O
    WHERE P.vendor_id = V.vendor_id AND P.id = PO.product_id AND PO.order_id = O.id AND ". $report_period_sql_str ." GROUP BY V.name";
    */

    $sql_str = "SELECT V.name, sum(P.quantity) AS 'Quantity in Stock', sum(P.cost * PO.quantity) AS 'Amount to Vendor', sum(PO.quantity) AS 'Sold Quantity', sum(P.sell_price * PO.quantity) AS 'Sub Total Sale', sum((P.sell_price - P.cost) * PO.quantity) AS 'Profit'
    FROM (CPS5740.VENDOR V
    LEFT JOIN 
    PRODUCT P 
    ON P.vendor_id = V.vendor_id
    LEFT JOIN 
    PRODUCT_ORDER PO
    ON P.id = PO.product_id
    LEFT JOIN
    `ORDER` O 
    ON PO.order_id = O.id) WHERE " . $report_period_sql_str . " GROUP BY V.name";

    $total_to_vendor = 0;
    $sub_total_sell = 0;
    $total_profit = 0;
    $count = 1;

    $result = $conn->query($sql_str);

    if ($result == NULL) {
        mysqli_close($conn);

        die("Syntax error or Database deny access");
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        echo "No order history";

        mysqli_free_result($result);
        mysqli_close($conn);
    } else {
        echo "<table border='1'>";
        echo "<tr algin='center'>";

        echo "<td>#</td>";
        while ($field = $result->fetch_field())
            echo "<td>" . $field->name . "</td>";
        echo "</tr>";
        while ($row = $result->fetch_row()) {
            echo "<tr>";
            echo "<td>$count</td>";
            for ($i = 0; $i < $result->field_count; $i++)
                echo "<td>" . $row[$i] . "</td>";
            echo "</tr>";

            $total_to_vendor += $row[2];
            $sub_total_sell += $row[4];
            $total_profit += $row[5];
            $count++;
        }
        echo "<tr><td>Total</td><td colspan=2></td><td>$total_to_vendor</td><td></td><td>$sub_total_sell</td><td>$total_profit</td></tr>";
        echo "</table>";
    }
}