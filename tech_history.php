<?php 
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'technician') {
    die("Access Denied");
}

$stmt = $pdo->prepare("
    SELECT t.*, u.username as requester_name 
    FROM tickets t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.technician_id = ? AND t.status = 'complete' 
    ORDER BY t.updated_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$jobs = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">My Work Log</h3>
        <p class="text-muted small mb-0">History of all your completed repair jobs.</p>
    </div>
    <div class="bg-success-subtle text-success-emphasis px-3 py-2 rounded-pill fw-bold text-uppercase small shadow-sm">
        <i class="bi bi-check-circle-fill me-1"></i> <?= count($jobs) ?> Jobs Done
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-white">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Finished At</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Room</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Issue</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Requester</th>
                        <th class="text-end pe-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Proof</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (count($jobs) > 0): ?>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="text-dark fw-medium"><?= date('M d, Y', strtotime($job['updated_at'])) ?></span>
                                    <div class="text-muted small"><?= date('H:i A', strtotime($job['updated_at'])) ?></div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-light text-dark border border-secondary-subtle px-2 py-1 rounded-2">
                                        <?= htmlspecialchars($job['room_number']) ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($job['category']) ?></div>
                                    <div class="text-muted small text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($job['details']) ?>">
                                        <?= htmlspecialchars($job['details']) ?>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary-subtle text-secondary-emphasis rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.75rem; font-weight: bold;">
                                            <?= strtoupper(substr($job['requester_name'], 0, 1)) ?>
                                        </div>
                                        <span class="text-dark fw-medium small"><?= htmlspecialchars($job['requester_name']) ?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <?php if ($job['tech_proof_image']): ?>
                                        <a href="assets/uploads/<?= $job['tech_proof_image'] ?>" target="_blank" class="d-inline-block position-relative rounded overflow-hidden shadow-sm border" style="width: 45px; height: 45px;">
                                            <img src="assets/uploads/<?= $job['tech_proof_image'] ?>" alt="Proof" style="width: 100%; height: 100%; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-2 py-1">Missing</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-archive fs-3 text-muted"></i>
                                </div>
                                <h5 class="text-dark fw-bold">No completed jobs yet</h5>
                                <p class="text-muted small mb-0">Your completed tasks will appear here.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>