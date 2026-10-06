<?php
session_start();
require 'config/db.php';

$error = '';

// Run only when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Look for a user with this username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Check the typed password against the stored hash
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);          // security best practice
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Portal - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center" style="min-height:100vh; background: linear-gradient(135deg, #1e293b, #3c45c7);">
    <div class="container" style="max-width:400px;">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="fs-1 text-primary"><i class="bi bi-people-fill"></i></div>
                    <h4 class="mb-0">HR Management Portal</h4>
                    <small class="text-muted">Please log in to continue</small>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Log in</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 