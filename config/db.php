<?php
// Settings come from environment variables (Docker/servers) or config.local.php (your laptop)
$configFile = __DIR__ . '/config.local.php';
$cfg = file_exists($configFile) ? require $configFile : [];

$host    = getenv('DB_HOST') ?: ($cfg['db_host'] ?? 'localhost');
$port    = getenv('DB_PORT') ?: ($cfg['db_port'] ?? 1521);
$service = getenv('DB_NAME') ?: ($cfg['db_name'] ?? 'FREEPDB1');
$user    = getenv('DB_USER') ?: ($cfg['db_user'] ?? 'hr_app');
$pass    = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($cfg['db_pass'] ?? '');

try {
    // "oci:" is the Oracle driver; AL32UTF8 lets Arabic text work correctly
    $pdo = new PDO("oci:dbname=//$host:$port/$service;charset=AL32UTF8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Oracle returns column names in CAPITALS (FULL_NAME). This turns them back
    // into lowercase, so $row['full_name'] keeps working everywhere.
    $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

    // Make dates read and write as 2026-10-20, like in MySQL
    $pdo->exec("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}