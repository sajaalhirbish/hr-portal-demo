<?php
require 'includes/auth.php';
require 'config/db.php';

$id = (int) ($_GET['id'] ?? 0);

// Load the department
$stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
$stmt->execute([$id]);
$dept = $stmt->fetch();

if (!$dept) {                      // wrong or missing id
    header('Location: departments.php');
    exit;
}

// All employees in this department
$stmt = $pdo->prepare(
    "SELECT id, full_name, email, position, joining_date, status
     FROM employees
     WHERE department_id = ?
     ORDER BY full_name"
);
$stmt->execute([$id]);
$employees = $stmt->fetchAll();

// How many employees in each status (for the small summary cards)
$counts = ['Active' => 0, 'On Leave' => 0, 'Inactive' => 0];
foreach ($employees as $e) {
    $counts[$e['status']]++;
}

$pageTitle = $dept['name'];
require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1"><?= htmlspecialchars($dept['name']) ?></h3>
        <span class="text-muted"><?= count($employees) ?> employee<?= count($employees) == 1 ? '' : 's' ?></span>
    </div>
    <a href="departments.php" class="btn btn-outline-secondary">Back</a>
</div>

<!-- Status summary -->
<div class="row g-3 mb-3">
    <?php foreach ($counts as $label => $n): ?>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm"><div class="card-body">
                <div class="text-muted small"><?= $label ?></div>
                <div class="fs-3 fw-bold"><?= $n ?></div>
            </div></div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Employees table -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Name</th><th>Position</th><th>Joining Date</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (!$employees): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No employees in this department yet.</td></tr>
            <?php endif; ?>

            <?php foreach ($employees as $e): ?>
                <?php
                $badge = match ($e['status']) {
                    'Active'   => 'success',
                    'On Leave' => 'warning',
                    default    => 'secondary',
                };
                ?>
                <tr>
                    <td><?= 1000 + $e['id'] ?></td>
                    <td>
                        <a href="employee_view.php?id=<?= $e['id'] ?>" class="fw-semibold text-decoration-none"><?= htmlspecialchars($e['full_name']) ?></a><br>
                        <small class="text-muted"><?= htmlspecialchars($e['email']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($e['position']) ?></td>
                    <td><?= date('d/m/Y', strtotime($e['joining_date'])) ?></td>
                    <td><span class="badge text-bg-<?= $badge ?>"><?= htmlspecialchars($e['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>