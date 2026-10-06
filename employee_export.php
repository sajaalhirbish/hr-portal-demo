<?php
require 'includes/auth.php';
require 'config/db.php';

// Tell the browser this is a file to download, not a page to display
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="employees_' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");   // lets Excel read Arabic and other UTF-8 text correctly

fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Department', 'Position',
               'Joining Date', 'Type', 'Status'], ',', '"', '');

$rows = $pdo->query(
    "SELECT e.id, e.full_name, e.email, e.phone, d.name AS department, e.position,
            e.joining_date, e.employment_type, e.status
     FROM employees e
     JOIN departments d ON e.department_id = d.id
     ORDER BY e.id"
)->fetchAll();

foreach ($rows as $r) {
    $r['id'] = 1000 + $r['id'];   // same employee ID format as the list
    fputcsv($out, $r, ',', '"', '');
}
fclose($out);