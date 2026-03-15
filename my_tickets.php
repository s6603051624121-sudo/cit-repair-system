<?php 
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php"); exit;
}

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll();
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-cit-primary fw-bold">My Repair History</h2>
        <a href="create_ticket.php" class="btn btn-cit btn-sm"><i class="bi bi-plus-lg me-1"></i>New Report</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Room</th>
                            <th>Category & Details</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Evidence</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tickets) > 0): ?>
                            <?php foreach ($tickets as $t): ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?= date('d M Y', strtotime($t['created_at'])) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($t['room_number']) ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($t['category']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($t['details']) ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                            $statusClass = match($t['status']) {
                                                'pending' => 'bg-warning text-dark',
                                                'waiting_material' => 'bg-danger',
                                                'fixing' => 'bg-primary',
                                                'complete' => 'bg-success',
                                                default => 'bg-secondary'
                                            };
                                            $statusLabel = ucfirst(str_replace('_', ' ', $t['status']));
                                        ?>
                                        <span class="badge <?= $statusClass ?> rounded-pill"><?= $statusLabel ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($t['status'] == 'complete' && $t['tech_proof_image']): ?>
                                            <a href="assets/uploads/<?= $t['tech_proof_image'] ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-card-image me-1"></i>Proof
                                            </a>
                                        <?php elseif($t['status'] == 'complete'): ?>
                                            <span class="text-muted small">No Image</span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">You haven't submitted any tickets yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>