<?php 
require_once 'includes/header.php';
if ($_SESSION['role'] != 'admin') die("Access Denied");

// Handle Role Update
if (isset($_POST['update_role'])) {
    $uid = $_POST['user_id'];
    $newRole = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$newRole, $uid]);
}

// Handle Ban/Unban
if (isset($_POST['toggle_ban'])) {
    $uid = $_POST['user_id'];
    $currentStatus = $_POST['current_ban_status'];
    $newStatus = ($currentStatus == 1) ? 0 : 1; // Toggle
    
    $stmt = $pdo->prepare("UPDATE users SET is_banned = ? WHERE id = ?");
    $stmt->execute([$newStatus, $uid]);
}
?>

<div class="container mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>User Management</h5>
            <span class="badge bg-secondary">Admin Access</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
                        foreach($users as $u): ?>
                        <tr class="<?= $u['is_banned'] ? 'table-danger' : '' ?>">
                            <td class="ps-4">
                                <div class="fw-bold"><?= htmlspecialchars($u['username']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($u['email']) ?></div>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="user" <?= $u['role']=='user'?'selected':'' ?>>User</option>
                                        <option value="technician" <?= $u['role']=='technician'?'selected':'' ?>>Technician</option>
                                        <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="update_role" value="1">
                                </form>
                            </td>
                            <td>
                                <?php if($u['is_banned']): ?>
                                    <span class="badge bg-danger">Banned</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="current_ban_status" value="<?= $u['is_banned'] ?>">
                                    <button type="submit" name="toggle_ban" class="btn btn-sm <?= $u['is_banned'] ? 'btn-outline-success' : 'btn-outline-danger' ?>">
                                        <?= $u['is_banned'] ? 'Unban' : 'Ban User' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>