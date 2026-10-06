<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'includes/auth.php';
require 'config/db.php';

// Only accept requests sent by the Approve/Reject buttons (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    // Turn the button into a status (anything else is ignored)
    $newStatus = match ($action) {
        'approve' => 'Approved',
        'reject'  => 'Rejected',
        default   => null,
    };

    if ($newStatus !== null) {
        // "AND status = 'Pending'" means an already-decided request can't be changed again
        $stmt = $pdo->prepare("UPDATE leave_requests SET status = ? WHERE id = ? AND status = 'Pending'");
        $stmt->execute([$newStatus, $id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['flash'] = "Leave request $newStatus.";
        }
    }
}

header('Location: leaves.php');
exit;