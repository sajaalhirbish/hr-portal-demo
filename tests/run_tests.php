<?php
// tests/run_tests.php : simple automated checks.
// Exit code 0 = everything passed, 1 = something failed.
require __DIR__ . '/../config/db.php';          // gives us $pdo
require __DIR__ . '/../includes/functions.php'; // gives us validate_employee()

$failures = 0;

function check(string $name, bool $ok): void
{
    global $failures;
    echo ($ok ? '[PASS] ' : '[FAIL] ') . $name . PHP_EOL;
    if (!$ok) {
        $failures++;
    }
}

// ---- 1. All four tables exist and can be read ----
foreach (['users', 'departments', 'employees', 'leave_requests'] as $table) {
    try {
        $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        check("table $table is readable", true);
    } catch (PDOException $e) {
        check("table $table is readable", false);
    }
}

// ---- 2. The settings in db.php do their job ----
$row = $pdo->query("SELECT full_name, joining_date FROM employees WHERE ROWNUM = 1")->fetch();
check('column names come back in lowercase', is_array($row) && array_key_exists('full_name', $row));
check('dates come back as YYYY-MM-DD', is_array($row) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $row['joining_date']) === 1);

// ---- 3. The admin account exists and its password hash is valid ----
$admin = $pdo->query("SELECT password_hash FROM users WHERE username = 'admin'")->fetch();
check('admin user exists and password verifies', $admin && password_verify('Admin@123', $admin['password_hash']));

// ---- 4. The validation function rejects bad data ----
$deptId        = $pdo->query("SELECT MIN(id) FROM departments")->fetchColumn();
$existingEmail = $pdo->query("SELECT MIN(email) FROM employees")->fetchColumn();

$valid = [
    'full_name' => 'Test User', 'email' => 'test.user@example.com', 'phone' => '0500000000',
    'department_id' => $deptId, 'position' => 'Tester', 'joining_date' => '2026-01-15',
    'employment_type' => 'Full-time', 'status' => 'Active',
];

check('valid employee passes validation', validate_employee($valid, $pdo) === []);
check('empty name is rejected', count(validate_employee(array_merge($valid, ['full_name' => '']), $pdo)) > 0);
check('invalid email is rejected', count(validate_employee(array_merge($valid, ['email' => 'not-an-email']), $pdo)) > 0);
check('duplicate email is rejected', count(validate_employee(array_merge($valid, ['email' => $existingEmail]), $pdo)) > 0);
check('invalid date is rejected', count(validate_employee(array_merge($valid, ['joining_date' => '2026-13-45']), $pdo)) > 0);
check('invalid status is rejected', count(validate_employee(array_merge($valid, ['status' => 'Banana']), $pdo)) > 0);

// ---- 5. Adding an employee works, and the test leaves no data behind ----
$pdo->beginTransaction();
$before = (int) $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();

$stmt = $pdo->prepare(
    "INSERT INTO employees (full_name, email, department_id, position, joining_date) VALUES (?, ?, ?, ?, ?)"
);
$stmt->execute(['Rollback Test', 'rollback.test@example.com', $deptId, 'Tester', '2026-01-15']);
$during = (int) $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();

$pdo->rollBack();   // undo the insert, so the database stays exactly as it was
$after = (int) $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();

check('inserting an employee adds one row', $during === $before + 1);
check('rollback leaves no test data behind', $after === $before);

// ---- Result ----
echo PHP_EOL . ($failures === 0 ? 'ALL TESTS PASSED' : "$failures TEST(S) FAILED") . PHP_EOL;
exit($failures === 0 ? 0 : 1);