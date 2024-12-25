<?php
session_start();
if (isset($_COOKIE['username']) && ($_COOKIE['username'])) {
    setcookie("username", "", time() - 30 * 24 * 60 * 60, '/');
    setcookie("password", "", time() - 30 * 24 * 60 * 60, '/');
    unset($_SESSION['user']);
    header('Location: ../index.php');
}
