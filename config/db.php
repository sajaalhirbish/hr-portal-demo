<?php
// Database settings (XAMPP defaults)
   // Settings come from environment variables (servers) or config.local.php (your laptop)
   $configFile = __DIR__ . '/config.local.php';
   $cfg = file_exists($configFile) ? require $configFile : [];

   $host     = getenv('DB_HOST') ?: ($cfg['db_host'] ?? 'localhost');
   $dbname   = getenv('DB_NAME') ?: ($cfg['db_name'] ?? 'hr_db');
   $username = getenv('DB_USER') ?: ($cfg['db_user'] ?? 'root');
   $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($cfg['db_pass'] ?? '');
   
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