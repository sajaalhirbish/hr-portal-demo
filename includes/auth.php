<?php
// Start the session (PHP's way of remembering who is logged in)
session_start();

// If nobody is logged in, send them to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}