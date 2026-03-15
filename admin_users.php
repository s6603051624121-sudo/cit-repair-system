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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">User Management</h3>
        <p class="text-muted small mb-0">Control roles and access levels across the system.</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-white">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">User Account</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">System Role</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Status</th>
                        <th class="text-end pe-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php 
                    $users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
                    foreach($users as $u): ?>
                    <tr class="<?= $u['is_banned'] ? 'bg-danger-subtle bg-opacity-25' : '' ?>">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-secondary-subtle text-secondary-emphasis rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">
                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($u['username']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="role" class="form-select form-select-sm d-inline-block bg-light border-secondary-subtle fw-medium text-dark" style="min-width: 130px;" onchange="this.form.submit()" <?= $u['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="user" <?= $u['role']=='user'?'selected':'' ?>>User</option>
                                    <option value="technician" <?= $u['role']=='technician'?'selected':'' ?>>Technician</option>
                                    <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                                </select>
                                <input type="hidden" name="update_role" value="1">
                            </form>
                        </td>
                        <td class="py-3">
                            <?php if($u['is_banned']): ?>
                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-2 fw-semibold"><i class="bi bi-slash-circle me-1"></i> Suspended</span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-2 fw-semibold"><i class="bi bi-check-circle me-1"></i> Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4 py-3">
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="current_ban_status" value="<?= $u['is_banned'] ?>">
                                    <?php if($u['is_banned']): ?>
                                        <button type="submit" name="toggle_ban" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-medium">
                                            Restore Access
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="toggle_ban" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-medium">
                                            Suspend User
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Current Session</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>