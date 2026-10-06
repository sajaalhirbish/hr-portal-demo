<?php
require 'includes/auth.php';
require 'config/db.php';

// Each department with its number of employees.
// LEFT JOIN keeps departments that have 0 employees.
$departments = $pdo->query(
    "SELECT d.id, d.name, COUNT(e.id) AS total
     FROM departments d
     LEFT JOIN employees e ON e.department_id = d.id
     GROUP BY d.id, d.name
     ORDER BY d.name"
)->fetchAll();

$pageTitle = 'Departments';
require 'includes/header.php';
?>

<h3 class="mb-3">Departments</h3>

<div class="row g-3">
    <?php foreach ($departments as $d): ?>
        <div class="col-md-6 col-xl-4">
            <a href="department_view.php?id=<?= $d['id'] ?>" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-5 fw-semibold text-dark"><?= htmlspecialchars($d['name']) ?></div>
                            <div class="text-muted small">View employees</div>
                        </div>
                        <div class="text-end">
                            <div class="fs-2 fw-bold text-dark"><?= $d['total'] ?></div>
                            <div class="text-muted small">employee<?= $d['total'] == 1 ? '' : 's' ?></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php require 'includes/footer.php'; ?>