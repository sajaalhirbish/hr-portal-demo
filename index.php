<?php
require 'includes/auth.php';   // must be logged in
require 'config/db.php';       // gives us $pdo
date_default_timezone_set('Asia/Riyadh');

// ---- Numbers for the cards (each query returns ONE value) ----
$totalEmployees = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();

$onLeave = $pdo->query("SELECT COUNT(*) FROM employees WHERE status = 'On Leave'")->fetchColumn();

$newHires = $pdo->query(
    "SELECT COUNT(*) FROM employees
     WHERE MONTH(joining_date) = MONTH(CURDATE())
       AND YEAR(joining_date) = YEAR(CURDATE())"
)->fetchColumn();

$pendingLeaves = $pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending'")->fetchColumn();

// ---- Data for the chart: number of employees in each department ----
// LEFT JOIN keeps departments even if they have 0 employees
$deptRows = $pdo->query(
    "SELECT d.name, COUNT(e.id) AS total
     FROM departments d
     LEFT JOIN employees e ON e.department_id = d.id
     GROUP BY d.id, d.name
     ORDER BY d.name"
)->fetchAll();

$deptLabels = array_column($deptRows, 'name');
$deptCounts = array_map('intval', array_column($deptRows, 'total'));

// ---- Greeting based on the time of day ----
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

$pageTitle = 'Dashboard';
require 'includes/header.php';
?>

<h3 class="mb-4"><?= $greeting ?>, <?= htmlspecialchars($_SESSION['full_name']) ?></h3>

<!-- Summary cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card shadow-sm"><div class="card-body">
            <div class="text-muted small">Total Employees</div>
            <div class="fs-2 fw-bold"><?= $totalEmployees ?></div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card shadow-sm"><div class="card-body">
            <div class="text-muted small">On Leave</div>
            <div class="fs-2 fw-bold"><?= $onLeave ?></div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card shadow-sm"><div class="card-body">
            <div class="text-muted small">New Hires This Month</div>
            <div class="fs-2 fw-bold"><?= $newHires ?></div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card shadow-sm"><div class="card-body">
            <div class="text-muted small">Pending Leave Requests</div>
            <div class="fs-2 fw-bold"><?= $pendingLeaves ?></div>
        </div></div>
    </div>
</div>

<!-- Chart -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Employees by Department</h5>
        <canvas id="deptChart" height="90"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // PHP hands the database results to JavaScript as JSON
    const labels = <?= json_encode($deptLabels) ?>;
    const counts = <?= json_encode($deptCounts) ?>;

    new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{ label: 'Employees', data: counts, backgroundColor: '#3c45c7' }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
</script>

<?php require 'includes/footer.php'; ?>