<?php

$guest = 'guest';
setcookie("guest", $guest, time() + 60); // Store email in cookie live for 1min

echo "accessing product system as a guest";

//header('Location: productpage');

?>