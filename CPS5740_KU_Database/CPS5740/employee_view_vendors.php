<!DOCTYPE html>
<html>

<head>
    <title>View All Vendors</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      #map-canvas {
        height: 600px;
        margin: 0px;
        padding: 0px
      }
    </style>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <script src="https://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
</head>

<body>
    <div style="margin:auto;  width: 720px; ">
    <?php

    if (!isset($_COOKIE["employee_login_id"]))
        die("This page is for employee only. Please login as a employee or manager.");

    include "dbconfig.php";

    $conn = mysqli_connect($servername, $db_username, $db_password)
        or die("Fail to connect the server" . mysqli_connect_error());
    mysqli_select_db($conn, "CPS5740")
        or die("Fail to connect the database" . mysqli_connect_error());

    $login_id = $_COOKIE["employee_login_id"];

    $result = $conn->query("SELECT * FROM EMPLOYEE2 WHERE login='$login_id'");

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

    $result = $conn->query("SELECT vendor_id AS ID, name AS Name, address AS Address, city AS City, state AS State, zipcode AS Zipcode, concat('(', latitude, ',', Longitude, ')') AS 'Location(Latitude, Longtitude)' FROM CPS5740.VENDOR");

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

    echo "<h2>The following vendors are in the database.</h2>";

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

    ?>


<script>

    var i = 0;

    function initialize() {
        var mapOptions = {
                zoom: 4,

                center: new google.maps.LatLng(39.521741, -96.848224),
                mapTypeId: google.maps.MapTypeId.ROADMAP
       };

       var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

       var infowindow = new google.maps.InfoWindow();

	var markerIcon = {
  		scaledSize: new google.maps.Size(80, 80),
		  origin: new google.maps.Point(0, 0),
		  anchor: new google.maps.Point(32,65),
		  labelOrigin: new google.maps.Point(40,33)
	};
        var location;
        var mySymbol;
        var marker, m;
        var MarkerLocations= [
            <?php
                $result = $conn->query("SELECT vendor_id, name, latitude, Longitude FROM CPS5740.VENDOR");

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

                for ($i = 0; $i < $num_rows; $i++) {
                    $row = $result -> fetch_row();
                    echo "['$row[0]','$row[1]',".$row[2].",".$row[3]."]";
                    if($i != $num_rows - 1)
                        echo ", \n";
                }

                mysqli_free_result($result);
                mysqli_close($conn);
            ?>
        ];

for (m = 0; m < MarkerLocations.length; m++) {

        location = new google.maps.LatLng(MarkerLocations[m][2], MarkerLocations[m][3]),
        marker = new google.maps.Marker({ 
	    map: map, 
	    position: location, 
	    icon: markerIcon,	
	    label: {
	   	text: MarkerLocations[m][0] ,
		color: "black",
    		fontSize: "16px",
    		fontWeight: "bold"
	    }
	});

      google.maps.event.addListener(marker, 'click', (function(marker, m) {
        return function() {
          infowindow.setContent("Vendor Name: " + MarkerLocations[m][1]);
          infowindow.open(map, marker);
        }
      })(marker, m));
 }
}
  google.maps.event.addDomListener(window, 'load', initialize);;

  </script>
  </head>
<br>
    <div id="map-canvas" style="height: 400px; width: 720px;"></div>
    </div>


</body>

</html>