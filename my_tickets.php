<?php 
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php"); exit;
}

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">My History</h3>
        <p class="text-muted small mb-0">Track the progress of your submitted requests.</p>
    </div>
    <a href="create_ticket.php" class="btn btn-cit rounded-pill px-4 shadow-sm fw-medium d-none d-sm-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> New Request
    </a>
</div>

<a href="create_ticket.php" class="btn btn-cit rounded-pill w-100 mb-4 d-sm-none shadow-sm fw-medium d-flex justify-content-center align-items-center gap-2">
    <i class="bi bi-plus-lg"></i> New Request
</a>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-white">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Date</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Room</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Issue</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Status</th>
                        <th class="text-end pe-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Evidence</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (count($tickets) > 0): ?>
                        <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="text-dark fw-medium"><?= date('M d', strtotime($t['created_at'])) ?></span>
                                    <div class="text-muted small"><?= date('Y', strtotime($t['created_at'])) ?></div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-light text-dark border border-secondary-subtle px-2 py-1 rounded-2">
                                        <?= htmlspecialchars($t['room_number']) ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($t['category']) ?></div>
                                    <div class="text-muted small text-truncate" style="max-width: 250px;">
                                        <?= htmlspecialchars($t['details']) ?>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <?php 
                                        $statusClass = match($t['status']) {
                                            'pending' => 'bg-warning-subtle text-warning-emphasis',
                                            'waiting_material' => 'bg-danger-subtle text-danger-emphasis',
                                            'fixing' => 'bg-primary-subtle text-primary-emphasis',
                                            'complete' => 'bg-success-subtle text-success-emphasis',
                                            default => 'bg-secondary-subtle text-secondary-emphasis'
                                        };
                                        $statusLabel = match($t['status']) {
                                            'waiting_material' => 'Waiting Material',
                                            'fixing' => 'In Progress',
                                            default => ucfirst($t['status'])
                                        };
                                    ?>
                                    <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2 fw-semibold">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <?php if ($t['status'] == 'complete' && $t['tech_proof_image']): ?>
                                        <a href="assets/uploads/<?= $t['tech_proof_image'] ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                            <i class="bi bi-image me-1"></i> View
                                        </a>
                                    <?php elseif($t['status'] == 'complete'): ?>
                                        <span class="badge bg-light text-muted border px-2 py-1">No Image</span>
                                    <?php else: ?>
                                        <span class="text-muted opacity-50">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-inbox fs-3 text-muted"></i>
                                </div>
                                <h5 class="text-dark fw-bold">No tickets yet</h5>
                                <p class="text-muted small mb-0">You haven't submitted any repair requests.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>