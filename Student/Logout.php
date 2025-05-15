<?php
session_start();
session_destroy();
header("Location: Login.php");//redirected to login page
exit();
?>


