<?php
require 'includes/auth.php';
require 'config/db.php';

// Read the search/filter values from the URL (?search=...&department=...&status=...)
$search = trim($_GET['search'] ?? '');
$dept   = $_GET['department'] ?? '';
$status = $_GET['status'] ?? '';

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();

// Build the query step by step: start with everything, then add conditions only if used.
// WHERE 1=1 is a trick so every extra condition can simply start with AND.
$sql = "SELECT e.id, e.full_name, e.email, e.position, e.status, d.name AS department
        FROM employees e
        JOIN departments d ON e.department_id = d.id
        WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (e.full_name LIKE ? OR e.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($dept !== '') {
    $sql .= " AND e.department_id = ?";
    $params[] = $dept;
}
if ($status !== '') {
    $sql .= " AND e.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY e.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$employees = $stmt->fetchAll();

// One-time message (e.g. "Employee added") saved by another page
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$pageTitle = 'Employees';
require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Employees</h3>
    <div class="d-flex gap-2">
        <a href="employee_export.php" class="btn btn-outline-success"><i class="bi bi-download"></i> Export CSV</a>
        <a href="employee_add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Employee</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<!-- Search and filters -->
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
               value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="department" class="form-select">
            <option value="">All Departments</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>" <?= $dept == $d['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <?php foreach (['Active', 'On Leave', 'Inactive'] as $s): ?>
                <option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-dark flex-grow-1">Search</button>
        <a href="employees.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Table -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th><th>Name</th><th>Department</th><th>Position</th><th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$employees): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No employees found.</td></tr>
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
                    <td><?= htmlspecialchars($e['department']) ?></td>
                    <td><?= htmlspecialchars($e['position']) ?></td>
                    <td><span class="badge text-bg-<?= $badge ?>"><?= htmlspecialchars($e['status']) ?></span></td>
                    <td class="text-end">
                        <a href="employee_edit.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="employee_delete.php" class="d-inline"
                              onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($e['full_name'])) ?>? This cannot be undone.');">
                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>