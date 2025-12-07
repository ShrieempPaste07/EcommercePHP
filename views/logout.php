<?php
session_start();
session_unset(); // removes all session variables
session_destroy(); // destroy session

header("Location: userLogin.php"); 
exit;
?>
