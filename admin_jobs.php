<?php 
require_once 'includes/header.php';

// 1. Security Check: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Access Denied</div></div>";
    require_once 'includes/footer.php';
    exit;
}

// 2. Fetch All Completed Jobs
// We join the table twice: once for the Requester (u), once for the Technician (tech)
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

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-cit-primary"><i class="bi bi-clipboard-check me-2"></i>Global Job History</h2>
        <span class="badge bg-success fs-6">Total Completed: <?= count($jobs) ?></span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-4">Date Completed</th>
                            <th>Room</th>
                            <th>Issue Category</th>
                            <th>Technician</th>
                            <th>Requester</th>
                            <th class="text-end pe-4">Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($jobs) > 0): ?>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td class="ps-4 text-muted small">
                                        <?= date('d M Y, H:i', strtotime($job['updated_at'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($job['room_number']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($job['category']) ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">
                                            <?= htmlspecialchars($job['details']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold">
                                            <i class="bi bi-person-gear me-1"></i><?= htmlspecialchars($job['tech_name'] ?? 'Unknown') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($job['requester_name'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($job['tech_proof_image']): ?>
                                            <a href="assets/uploads/<?= $job['tech_proof_image'] ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-image me-1"></i>View
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No completed jobs found in the system.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>