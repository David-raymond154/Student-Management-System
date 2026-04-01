<?php
session_start();

session_unset();
session_destroy();

if (isset($_COOKIE['remember_username'])) {
    setcookie('remember_username', '', time() - 3600, '/');
}

header('Location: login.php');

