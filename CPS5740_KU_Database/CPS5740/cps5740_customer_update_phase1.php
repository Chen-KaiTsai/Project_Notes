<?php

if (!isset($_COOKIE["customer_login_id"])) {
    die("Please login First.");
    //header("Location: cps5740_customer_login_phase1.php");
}
$login_id = $_COOKIE["customer_login_id"];
echo "<a href='cps5740_customer_logout_phase1.php'>Customer logout</a><br>";

include "dbconfig.php";

$conn = mysqli_connect($servername, $db_username, $db_password)
    or die("Fail to connect the server" . mysqli_connect_error());
mysqli_select_db($conn, "2021F_tsaiche")
    or die("Fail to connect the database" . mysqli_connect_error());

$result = $conn->query("SELECT customer_id, login_id, password, last_name, first_name, TEL, address, city, zipcode, state FROM CUSTOMER WHERE login_id='$login_id'");

if ($result == NULL) {
    mysqli_close($conn);

    die("Syntax error or Database deny access");
}
$num_rows = mysqli_num_rows($result);
if ($num_rows != 1) {
    mysqli_free_result($result);
    mysqli_close($conn);

    die("Database error or no result");
}

echo "<form action='cps5740_customer_update_check_phase1.php' method='post'>";

echo "<table border='1'>";
echo "<tr algin='center'>";
while ($field = $result->fetch_field())
    echo "<td>" . $field->name . "</td>";
echo "</tr>";
$row = $result->fetch_row();

echo "<tr>";
echo "<td bgcolor=yellow>$row[0]</td>";
echo "<td bgcolor=yellow>$row[1]</td>";
echo "<td><input type='text' name='password' value='$row[2]'></td>";
echo "<td><input type='text' name='last_name' value='$row[3]'></td>";
echo "<td><input type='text' name='first_name' value='$row[4]'></td>";
echo "<td><input type='text' name='tel' value='$row[5]'></td>";
echo "<td><input type='text' name='address' value='$row[6]'></td>";
echo "<td><input type='text' name='city' value='$row[7]'></td>";
echo "<td><input type='text' name='zipcode' value='$row[8]'></td>";

//states array
$states = array(
    '' => '-----',
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District of Columbia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
);

$current_state = $states[$row[9]];

echo "<td>";
echo "<select name='state'>";
echo "<option value='$row[9]' selected='selected' hidden='hidden'>$current_state</option>";
foreach ($states as $key => $value) {
    echo "<option value='$key'>$value</option>";
}
echo "</select>";
echo "</td>";

echo "</tr><br>";
echo "</table>";

echo "<input type='submit' value='Update Information'>";

echo "</form>";

// Display Customer's home page link
echo "<a href='cps5740_customer_login_pwdcheck_phase1.php'>Customer's home page</a><br>";

// Display home page link
echo "<a href='cps5740_phase1.php'>project home page</a><br>";

mysqli_free_result($result);
mysqli_close($conn);
