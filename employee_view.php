<?php
require 'includes/auth.php';
require 'config/db.php';

$id = (int) ($_GET['id'] ?? 0);

// Load the employee together with their department name
$stmt = $pdo->prepare(
    "SELECT e.*, d.name AS department
     FROM employees e
     JOIN departments d ON e.department_id = d.id
     WHERE e.id = ?"
);
$stmt->execute([$id]);
$emp = $stmt->fetch();

if (!$emp) {                       // wrong or missing id
    header('Location: employees.php');
    exit;
}

// Yearly leave allowance (change these numbers if your company uses different ones)
$allowance = ['Annual' => 20, 'Sick' => 10];

// Days of APPROVED leave taken this year, per leave type.
// DATEDIFF gives the gap between two dates; +1 so both the first and last day count.
$stmt = $pdo->prepare(
    "SELECT leave_type, SUM(DATEDIFF(end_date, start_date) + 1) AS days
     FROM leave_requests
     WHERE employee_id = ? AND status = 'Approved' AND YEAR(start_date) = YEAR(CURDATE())
     GROUP BY leave_type"
);
$stmt->execute([$id]);
$used = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);   // e.g. ['Annual' => 10, 'Sick' => 2]

// Full leave history for this employee, newest first
$stmt = $pdo->prepare(
    "SELECT leave_type, start_date, end_date, status,
            DATEDIFF(end_date, start_date) + 1 AS days
     FROM leave_requests
     WHERE employee_id = ?
     ORDER BY start_date DESC"
);
$stmt->execute([$id]);
$history = $stmt->fetchAll();

// Badge colour for the employee status
$statusBadge = match ($emp['status']) {
    'Active'   => 'success',
    'On Leave' => 'warning',
    default    => 'secondary',
};

$pageTitle = $emp['full_name'];
require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1"><?= htmlspecialchars($emp['full_name']) ?></h3>
        <span class="text-muted">Employee ID <?= 1000 + $emp['id'] ?></span>
        <span class="badge text-bg-<?= $statusBadge ?> ms-2"><?= htmlspecialchars($emp['status']) ?></span>
    </div>
    <div>
        <a href="employees.php" class="btn btn-outline-secondary">Back</a>
        <a href="employee_edit.php?id=<?= $emp['id'] ?>" class="btn btn-primary">Edit</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Personal information -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Personal Information</h5>
                <p class="mb-2"><span class="text-muted">Email:</span> <?= htmlspecialchars($emp['email']) ?></p>
                <p class="mb-0"><span class="text-muted">Phone:</span> <?= htmlspecialchars($emp['phone'] ?: '-') ?></p>
            </div>
        </div>
    </div>

    <!-- Employment information -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Employment Information</h5>
                <p class="mb-2"><span class="text-muted">Department:</span> <?= htmlspecialchars($emp['department']) ?></p>
                <p class="mb-2"><span class="text-muted">Position:</span> <?= htmlspecialchars($emp['position']) ?></p>
                <p class="mb-2"><span class="text-muted">Joining Date:</span> <?= date('d/m/Y', strtotime($emp['joining_date'])) ?></p>
                <p class="mb-0"><span class="text-muted">Employment Type:</span> <?= htmlspecialchars($emp['employment_type']) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Leave summary -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5 class="card-title mb-3">Leave Summary (<?= date('Y') ?>)</h5>

        <?php foreach ($allowance as $type => $total): ?>
            <?php
            $taken = (int) ($used[$type] ?? 0);
            $percent = min(100, (int) round($taken / $total * 100));
            $color = $percent >= 90 ? 'danger' : ($percent >= 70 ? 'warning' : 'success');
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <span><?= $type ?> Leave</span>
                    <span class="text-muted"><?= $taken ?> / <?= $total ?> days used</span>
                </div>
                <div class="progress" style="height: 14px;">
                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percent ?>%"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Leave history -->
<div class="card shadow-sm">
    <div class="card-body pb-0">
        <h5 class="card-title">Leave History</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (!$history): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No leave requests yet.</td></tr>
            <?php endif; ?>

            <?php foreach ($history as $h): ?>
                <?php
                $leaveBadge = match ($h['status']) {
                    'Approved' => 'success',
                    'Pending'  => 'warning',
                    default    => 'danger',
                };
                ?>
                <tr>
                    <td><?= htmlspecialchars($h['leave_type']) ?></td>
                    <td><?= date('d/m/Y', strtotime($h['start_date'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($h['end_date'])) ?></td>
                    <td><?= $h['days'] ?></td>
                    <td><span class="badge text-bg-<?= $leaveBadge ?>"><?= htmlspecialchars($h['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>