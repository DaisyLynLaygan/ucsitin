<?php
session_start();
session_destroy();
header("Location: index.php");//redirected to landing page
exit();
?>
