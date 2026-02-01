<?php
session_start();
// Clear user session
unset($_SESSION['user_email'], $_SESSION['user_name'], $_SESSION['logged_in_user']);
session_destroy();
header('Location: index.php');
exit();
