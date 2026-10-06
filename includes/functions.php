<?php
// Checks the employee form data. Returns a list of error messages (empty list = all good).
function validate_employee(array $emp, PDO $pdo, int $ignoreId = 0): array
{
    $errors = [];

    if ($emp['full_name'] === '') {
        $errors[] = 'Full name is required.';
    }

    if (!filter_var($emp['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        // Make sure no OTHER employee already uses this email
        $stmt = $pdo->prepare("SELECT id FROM employees WHERE email = ? AND id != ?");
        $stmt->execute([$emp['email'], $ignoreId]);
        if ($stmt->fetch()) {
            $errors[] = 'This email is already used by another employee.';
        }
    }

    // Make sure the chosen department really exists
    $stmt = $pdo->prepare("SELECT id FROM departments WHERE id = ?");
    $stmt->execute([$emp['department_id']]);
    if (!$stmt->fetch()) {
        $errors[] = 'Please choose a department.';
    }

    if ($emp['position'] === '') {
        $errors[] = 'Position is required.';
    }

    $d = DateTime::createFromFormat('Y-m-d', $emp['joining_date']);
    if (!$d || $d->format('Y-m-d') !== $emp['joining_date']) {
        $errors[] = 'Please enter a valid joining date.';
    }

    if (!in_array($emp['employment_type'], ['Full-time', 'Part-time', 'Contract'], true)) {
        $errors[] = 'Invalid employment type.';
    }
    if (!in_array($emp['status'], ['Active', 'On Leave', 'Inactive'], true)) {
        $errors[] = 'Invalid status.';
    }

    return $errors;
}