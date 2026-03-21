<?php
/**
 * Authentication Check
 * Include at top of protected pages
 */

session_start();
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
?>
