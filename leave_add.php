<?php
require 'includes/auth.php';
require 'config/db.php';

$employees = $pdo->query("SELECT id, full_name FROM employees ORDER BY full_name")->fetchAll();
$errors = [];

$req = ['employee_id' => '', 'leave_type' => 'Annual', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($req as $field => $value) {
        $req[$field] = trim($_POST[$field] ?? '');
    }

    // Does this employee exist?
    $stmt = $pdo->prepare("SELECT id FROM employees WHERE id = ?");
    $stmt->execute([$req['employee_id']]);
    if (!$stmt->fetch()) {
        $errors[] = 'Please choose an employee.';
    }

    if (!in_array($req['leave_type'], ['Annual', 'Sick'], true)) {
        $errors[] = 'Invalid leave type.';
    }

    $start = DateTime::createFromFormat('Y-m-d', $req['start_date']);
    $end   = DateTime::createFromFormat('Y-m-d', $req['end_date']);
    if (!$start || !$end) {
        $errors[] = 'Please enter valid dates.';
    } elseif ($end < $start) {
        $errors[] = 'The end date cannot be before the start date.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$req['employee_id'], $req['leave_type'], $req['start_date'], $req['end_date']]);

        $_SESSION['flash'] = 'Leave request submitted (Pending).';
        header('Location: leaves.php');
        exit;
    }
}

$pageTitle = 'New Leave Request';
require 'includes/header.php';
?>

<h3 class="mb-3">New Leave Request</h3>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">-- Choose --</option>
                    <?php foreach ($employees as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $req['employee_id'] == $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Leave Type</label>
                <select name="leave_type" class="form-select">
                    <?php foreach (['Annual', 'Sick'] as $t): ?>
                        <option <?= $req['leave_type'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" required value="<?= htmlspecialchars($req['start_date']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" required value="<?= htmlspecialchars($req['end_date']) ?>">
            </div>
        </div>
    </div>
    <div class="card-footer bg-white">
        <button type="submit" class="btn btn-primary">Submit Request</button>
        <a href="leaves.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<?php require 'includes/footer.php'; ?>