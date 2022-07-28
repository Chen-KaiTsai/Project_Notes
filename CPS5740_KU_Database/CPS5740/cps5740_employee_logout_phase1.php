<?php
    //echo $_COOKIE["login_id"];
    setcookie("employee_login_id", "", time() - 6000);
    header('Location: index.html');
?>
