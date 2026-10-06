<?php
// Which file is open right now? Used to highlight the sidebar link.
$currentPage = basename($_SERVER['PHP_SELF']);
if (!isset($pageTitle)) { $pageTitle = 'HR Portal'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> - HR Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar { width: 240px; min-height: 100vh; flex-shrink: 0;
                   background: linear-gradient(180deg, #1e293b, #0f172a) !important; }
        .sidebar .nav-link { color: #cbd5e1; border-radius: 8px; margin-bottom: 4px; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar .nav-link.active { background: #3c45c7; color: #fff; }
        .card { border: 0; border-radius: 12px; }
        .stat-card { border-left: 5px solid #3c45c7; transition: transform .15s, box-shadow .15s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.12) !important; }
    </style>
</head>
<body>
<div class="d-flex">

    <!-- Sidebar -->
    <nav class="sidebar bg-dark text-white p-3 d-flex flex-column">
        <h5 class="mb-4"><i class="bi bi-people-fill"></i> HR Portal</h5>

        <ul class="nav flex-column mb-auto">
            <li><a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="employees.php" class="nav-link <?= in_array($currentPage, ['employees.php','employee_add.php','employee_edit.php','employee_view.php']) ? 'active' : '' ?>">
                <i class="bi bi-person-lines-fill"></i> Employees</a></li>
            <li><a href="departments.php" class="nav-link <?= in_array($currentPage, ['departments.php','department_view.php']) ? 'active' : '' ?>">
                <i class="bi bi-diagram-3"></i> Departments</a></li>
            <li><a href="leaves.php" class="nav-link <?= $currentPage === 'leaves.php' ? 'active' : '' ?>">
                <i class="bi bi-calendar-check"></i> Leave</a></li>
            <li><a href="reports.php" class="nav-link <?= $currentPage === 'reports.php' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Reports</a></li>
        </ul>

        <hr>
        <div class="small mb-2"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['full_name']) ?></div>
        <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Log out</a>
    </nav>

    <!-- Main content starts here -->
    <main class="flex-grow-1 bg-light p-4">