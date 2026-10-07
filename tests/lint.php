<?php
// tests/lint.php : checks every PHP file in the project for syntax errors
$root  = dirname(__DIR__);
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$checked = 0;
$errors  = 0;

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    $checked++;
    $output = [];
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $code);
    if ($code !== 0) {
        $errors++;
        echo '[FAIL] ' . $file->getPathname() . PHP_EOL . implode(PHP_EOL, $output) . PHP_EOL;
    }
}

echo PHP_EOL . "Checked $checked files. " . ($errors === 0 ? 'No syntax errors.' : "$errors file(s) with errors.") . PHP_EOL;
exit($errors === 0 ? 0 : 1);