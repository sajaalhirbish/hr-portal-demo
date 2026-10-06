<?php
require 'includes/auth.php';
require 'config/db.php';
require 'includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);

// Load the employee being edited
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$id]);
$emp = $stmt->fetch();

if (!$emp) {                       // wrong or missing id
    header('Location: employees.php');
    exit;
}

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['full_name', 'email', 'phone', 'department_id', 'position',
              'joining_date', 'employment_type', 'status'] as $field) {
        $emp[$field] = trim($_POST[$field] ?? '');
    }

    $errors = validate_employee($emp, $pdo, $id);

    if (!$errors) {
        $stmt = $pdo->prepare(
            "UPDATE employees
             SET full_name = ?, email = ?, phone = ?, department_id = ?, position = ?,
                 joining_date = ?, employment_type = ?, status = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $emp['full_name'], $emp['email'], $emp['phone'] ?: null, $emp['department_id'],
            $emp['position'], $emp['joining_date'], $emp['employment_type'], $emp['status'], $id,
        ]);

        $_SESSION['flash'] = 'Employee updated successfully.';
        header('Location: employees.php');
        exit;
    }
}

$pageTitle = 'Edit Employee';
$buttonText = 'Save Changes';
require 'includes/header.php';
?>

<h3 class="mb-3">Edit Employee</h3>
<?php require 'includes/employee_form.php'; ?>

<?php require 'includes/footer.php'; ?>