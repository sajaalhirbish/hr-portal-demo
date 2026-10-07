<?php
// tests/setup_db.php : builds a database from the SQL scripts in /database.
// Used by the pipeline on a fresh, empty TEST database.
// NEVER run it on production: the seed scripts contain demo data.
require __DIR__ . '/../config/db.php';   // gives us $pdo

$files = glob(__DIR__ . '/../database/*.sql');
sort($files);   // 001, 002, 003 ... in order

foreach ($files as $file) {
    echo 'Running ' . basename($file) . PHP_EOL;

    // remove comment lines, then split into single statements at each ";"
    $sql = preg_replace('/^\s*--.*$/m', '', file_get_contents($file));

    foreach (explode(';', $sql) as $statement) {
        $statement = trim($statement);
        if ($statement === '' || strcasecmp($statement, 'COMMIT') === 0) {
            continue;   // PDO already saves each statement automatically
        }
        $pdo->exec($statement);   // an error here stops the script with a failure code
    }
}

echo 'Database ready.' . PHP_EOL;