<?php
require 'includes/auth.php';
require 'config/db.php';
require 'includes/functions.php';

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();
$errors = [];

// Starting (empty) values for the form
$emp = [
    'full_name' => '', 'email' => '', 'phone' => '', 'department_id' => '',
    'position' => '', 'joining_date' => date('Y-m-d'),
    'employment_type' => 'Full-time', 'status' => 'Active',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Copy what the user typed into $emp
    foreach ($emp as $field => $value) {
        $emp[$field] = trim($_POST[$field] ?? '');
    }

    $errors = validate_employee($emp, $pdo);

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO employees
             (full_name, email, phone, department_id, position, joining_date, employment_type, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $emp['full_name'], $emp['email'], $emp['phone'] ?: null, $emp['department_id'],
            $emp['position'], $emp['joining_date'], $emp['employment_type'], $emp['status'],
        ]);

        $_SESSION['flash'] = 'Employee added successfully.';
        header('Location: employees.php');
        exit;
    }
}

$pageTitle = 'Add Employee';
$buttonText = 'Add Employee';
require 'includes/header.php';
?>

<h3 class="mb-3">Add Employee</h3>
<?php require 'includes/employee_form.php'; ?>

<?php require 'includes/footer.php'; ?>