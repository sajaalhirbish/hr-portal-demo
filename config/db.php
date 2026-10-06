<?php
// Database settings (XAMPP defaults)
$host = 'localhost';
$dbname = 'hr_db';
$username = 'root';
$password = '';   // XAMPP's root user has no password by default

try {
    // PDO is PHP's safe, modern way to talk to MySQL
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    // If something goes wrong, show an error instead of failing silently
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Return rows as named arrays, e.g. $row['full_name']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}