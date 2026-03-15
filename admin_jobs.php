<?php 
require_once 'includes/header.php';

// 1. Security Check: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "<div class='container mt-5'><div class='alert alert-danger rounded-4 shadow-sm border-0 text-center py-4'><i class='bi bi-shield-lock fs-1 d-block mb-2'></i><h4 class='fw-bold'>Access Denied</h4><p class='mb-0'>You do not have permission to view this page.</p></div></div>";
    require_once 'includes/footer.php';
    exit;
}

// 2. Fetch All Completed Jobs
$sql = "SELECT t.*, u.username AS requester_name, tech.username AS tech_name 
        FROM tickets t 
        LEFT JOIN users u ON t.user_id = u.id 
        LEFT JOIN users tech ON t.technician_id = tech.id 
        WHERE t.status = 'complete' 
        ORDER BY t.updated_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$jobs = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Global Job History</h3>
        <p class="text-muted small mb-0">System-wide log of all completed repair requests.</p>
    </div>
    <div class="bg-cit-primary text-white px-3 py-2 rounded-pill fw-bold text-uppercase small shadow-sm">
        <i class="bi bi-clipboard-data me-1"></i> <?= count($jobs) ?> Total
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-white">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Date Completed</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Room</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Issue Detail</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Personnel</th>
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
                                    <div class="text-muted small text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($job['details']) ?>">
                                        <?= htmlspecialchars($job['details']) ?>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="mb-1 small">
                                        <span class="text-muted">Tech:</span> 
                                        <span class="fw-bold text-primary"><i class="bi bi-person-gear me-1"></i><?= htmlspecialchars($job['tech_name'] ?? 'Unknown') ?></span>
                                    </div>
                                    <div class="small">
                                        <span class="text-muted">Req:</span> 
                                        <span class="fw-medium text-dark"><i class="bi bi-person me-1"></i><?= htmlspecialchars($job['requester_name'] ?? 'Unknown') ?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <?php if ($job['tech_proof_image']): ?>
                                        <a href="assets/uploads/<?= $job['tech_proof_image'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium">
                                            <i class="bi bi-image me-1"></i> View
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
                                    <i class="bi bi-inbox fs-3 text-muted"></i>
                                </div>
                                <h5 class="text-dark fw-bold">No records found</h5>
                                <p class="text-muted small mb-0">Completed jobs will populate this global ledger.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>