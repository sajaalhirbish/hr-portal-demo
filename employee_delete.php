<?php
require 'includes/auth.php';
require 'config/db.php';

// Only accept deletes sent from the Delete button (POST), never from a plain link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);

    $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = 'Employee deleted.';
}

header('Location: employees.php');
exit;