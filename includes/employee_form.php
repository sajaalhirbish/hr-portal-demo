<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required
                       value="<?= htmlspecialchars($emp['full_name']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required
                       value="<?= htmlspecialchars($emp['email']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control"
                       value="<?= htmlspecialchars($emp['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select" required>
                    <option value="">-- Choose --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $emp['department_id'] == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Position</label>
                <input type="text" name="position" class="form-control" required
                       value="<?= htmlspecialchars($emp['position']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Joining Date</label>
                <input type="date" name="joining_date" class="form-control" required
                       value="<?= htmlspecialchars($emp['joining_date']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Employment Type</label>
                <select name="employment_type" class="form-select">
                    <?php foreach (['Full-time', 'Part-time', 'Contract'] as $t): ?>
                        <option <?= $emp['employment_type'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['Active', 'On Leave', 'Inactive'] as $s): ?>
                        <option <?= $emp['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white">
        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($buttonText) ?></button>
        <a href="employees.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>