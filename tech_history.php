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

<div class="container">
    <h2 class="mb-4 text-cit-primary fw-bold"><i class="bi bi-archive me-2"></i>My Completed Jobs</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Finished At</th>
                            <th>Room</th>
                            <th>Issue</th>
                            <th>Requester</th>
                            <th class="text-end pe-4">Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= date('d/m/Y H:i', strtotime($job['updated_at'])) ?></td>
                                <td><span class="fw-bold"><?= htmlspecialchars($job['room_number']) ?></span></td>
                                <td><?= htmlspecialchars($job['category']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle text-center me-2" style="width:25px; height:25px; line-height:25px;">
                                            <i class="bi bi-person small"></i>
                                        </div>
                                        <?= htmlspecialchars($job['requester_name']) ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($job['tech_proof_image']): ?>
                                        <a href="assets/uploads/<?= $job['tech_proof_image'] ?>" target="_blank">
                                            <img src="assets/uploads/<?= $job['tech_proof_image'] ?>" alt="Proof" class="rounded shadow-sm" style="height: 40px; width: auto;">
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Missing</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($jobs) == 0): ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No completed jobs found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>