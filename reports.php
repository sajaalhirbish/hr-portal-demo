<?php
require 'includes/auth.php';
require 'config/db.php';

// ---- 1. Employees by department (LEFT JOIN keeps departments with 0 employees) ----
$byDept = $pdo->query(
    "SELECT d.name, COUNT(e.id) AS total
     FROM departments d
     LEFT JOIN employees e ON e.department_id = d.id
     GROUP BY d.id, d.name
     ORDER BY total DESC, d.name"
)->fetchAll();

// ---- 2. Employees by status ----
$byStatus = ['Active' => 0, 'On Leave' => 0, 'Inactive' => 0];
foreach ($pdo->query("SELECT status, COUNT(*) AS n FROM employees GROUP BY status")->fetchAll() as $r) {
    $byStatus[$r['status']] = (int) $r['n'];
}

// ---- 3. Employees by employment type ----
$byType = ['Full-time' => 0, 'Part-time' => 0, 'Contract' => 0];
foreach ($pdo->query("SELECT employment_type, COUNT(*) AS n FROM employees GROUP BY employment_type")->fetchAll() as $r) {
    $byType[$r['employment_type']] = (int) $r['n'];
}

// ---- 4. Leave requests by status ----
$byLeave = ['Pending' => 0, 'Approved' => 0, 'Rejected' => 0];
foreach ($pdo->query("SELECT status, COUNT(*) AS n FROM leave_requests GROUP BY status")->fetchAll() as $r) {
    $byLeave[$r['status']] = (int) $r['n'];
}

$totalEmployees = array_sum($byStatus);
$maxDept = max(1, max(array_column($byDept, 'total')));   // longest bar = 100% width (min 1 avoids dividing by 0)

$pageTitle = 'Reports';
require 'includes/header.php';
?>

<h3 class="mb-4">Reports</h3>

<div class="row g-3 mb-3">
    <!-- Employees by department: horizontal bars -->
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Employees by Department</h5>
                <?php foreach ($byDept as $d): ?>
                    <?php $width = round($d['total'] / $maxDept * 100); ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span><?= htmlspecialchars($d['name']) ?></span>
                            <span class="fw-semibold"><?= $d['total'] ?></span>
                        </div>
                        <div class="progress" style="height: 14px;">
                            <div class="progress-bar" style="width: <?= $width ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Employment status: doughnut chart -->
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Employment Status</h5>
                <canvas id="statusChart" height="200"></canvas>
                <div class="mt-3 small text-muted text-center">Total: <?= $totalEmployees ?> employees</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Employment type table -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Employment Type</h5>
                <table class="table mb-0">
                    <?php foreach ($byType as $label => $n): ?>
                        <tr>
                            <td><?= $label ?></td>
                            <td class="text-end fw-semibold"><?= $n ?></td>
                            <td class="text-end text-muted" style="width:80px;">
                                <?= $totalEmployees ? round($n / $totalEmployees * 100) : 0 ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Leave requests table -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Leave Requests</h5>
                <table class="table mb-0">
                    <?php foreach ($byLeave as $label => $n): ?>
                        <tr>
                            <td><?= $label ?></td>
                            <td class="text-end fw-semibold"><?= $n ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($byStatus)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($byStatus)) ?>,
                backgroundColor: ['#47a177', '#e4c465', '#7c858d']
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
</script>

<?php require 'includes/footer.php'; ?>