<?php
require 'includes/auth.php';
require 'config/db.php';

// Which tab is selected? Only accept the three real statuses.
$filter = $_GET['status'] ?? '';
if (!in_array($filter, ['Pending', 'Approved', 'Rejected'], true)) {
    $filter = '';
}

// Count requests per status (for the numbers on the tabs)
$counts = ['Pending' => 0, 'Approved' => 0, 'Rejected' => 0];
foreach ($pdo->query("SELECT status, COUNT(*) AS n FROM leave_requests GROUP BY status")->fetchAll() as $r) {
    $counts[$r['status']] = (int) $r['n'];
}

// Load the requests with the employee name and department
$sql = "SELECT l.id, l.leave_type, l.start_date, l.end_date, l.status,
               (l.end_date - l.start_date) + 1 AS days,
               e.id AS employee_id, e.full_name, d.name AS department
        FROM leave_requests l
        JOIN employees e ON l.employee_id = e.id
        JOIN departments d ON e.department_id = d.id";
$params = [];

if ($filter !== '') {
    $sql .= " WHERE l.status = ?";
    $params[] = $filter;
}
// Pending requests first, so HR sees what needs action
$sql .= " ORDER BY CASE l.status WHEN 'Pending' THEN 1 WHEN 'Approved' THEN 2 ELSE 3 END, l.start_date";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

// One-time message saved by another page
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$pageTitle = 'Leave Requests';
require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Leave Requests</h3>
    <a href="leave_add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Request</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<!-- Filter tabs -->
<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $filter === '' ? 'active' : '' ?>" href="leaves.php">
            All <span class="badge text-bg-light"><?= array_sum($counts) ?></span></a>
    </li>
    <?php foreach ($counts as $label => $n): ?>
        <li class="nav-item">
            <a class="nav-link <?= $filter === $label ? 'active' : '' ?>" href="leaves.php?status=<?= $label ?>">
                <?= $label ?> <span class="badge text-bg-light"><?= $n ?></span></a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$requests): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No leave requests found.</td></tr>
            <?php endif; ?>

            <?php foreach ($requests as $r): ?>
                <?php
                $badge = match ($r['status']) {
                    'Approved' => 'success',
                    'Pending'  => 'warning',
                    default    => 'danger',
                };
                ?>
                <tr>
                    <td>
                        <a href="employee_view.php?id=<?= $r['employee_id'] ?>" class="fw-semibold text-decoration-none"><?= htmlspecialchars($r['full_name']) ?></a><br>
                        <small class="text-muted"><?= htmlspecialchars($r['department']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($r['leave_type']) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['start_date'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['end_date'])) ?></td>
                    <td><?= $r['days'] ?></td>
                    <td><span class="badge text-bg-<?= $badge ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                    <td class="text-end">
                        <?php if ($r['status'] === 'Pending'): ?>
                            <form method="POST" action="leave_action.php" class="d-inline">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <form method="POST" action="leave_action.php" class="d-inline">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button class="btn btn-sm btn-outline-danger">Reject</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">Done</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>