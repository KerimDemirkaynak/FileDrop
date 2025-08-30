<?php
// logout.php
session_start();
$_SESSION = [];
session_destroy();

if (isset($_COOKIE['remember_me'])) {
    unset($_COOKIE['remember_me']);
    setcookie('remember_me', '', time() - 3600, '/'); 
}

header('Location: login.php');
exit();