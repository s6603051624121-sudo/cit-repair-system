<?php 
require_once 'includes/header.php';

// Helper for filtering
$stmt = $pdo->query("SELECT * FROM tickets ORDER BY updated_at DESC");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

function renderColumn($title, $icon, $bgClass, $statusKey, $tickets) {
    $filtered = array_filter($tickets, fn($t) => $t['status'] === $statusKey);
    ?>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="rounded-3 bg-white shadow-sm h-100 overflow-hidden d-flex flex-column">
            <div class="p-3 text-center text-white fw-bold <?php echo $bgClass; ?>">
                <i class="<?php echo $icon; ?> me-2"></i> <?php echo $title; ?>
                <span class="badge bg-white text-dark ms-2 rounded-pill"><?php echo count($filtered); ?></span>
            </div>
            
            <div class="p-2 bg-light flex-grow-1">
                <?php if (empty($filtered)): ?>
                    <div class="text-center text-muted py-4 small">No items</div>
                <?php else: ?>
                    <?php foreach($filtered as $t): ?>
                        <div class="card mb-2 hover-card border-0">
                            <div class="status-strip status-<?php echo $statusKey == 'pending' ? 'pending' : ($statusKey == 'waiting_material' ? 'material' : ($statusKey == 'fixing' ? 'fixing' : 'complete')); ?>"></div>
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($t['category']) ?></h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-secondary border">Room <?= htmlspecialchars($t['room_number']) ?></span>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?= date('M d', strtotime($t['created_at'])) ?></small>
                                </div>
                                </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
?>



<div class="row g-3">
    <?php 
    // Grammatically corrected headers
    renderColumn('Waiting for Technician', 'bi-hourglass-split', 'bg-warning', 'pending', $tickets);
    renderColumn('Waiting for Materials', 'bi-box-seam', 'bg-danger', 'waiting_material', $tickets);
    renderColumn('Work in Progress', 'bi-tools', 'bg-primary', 'fixing', $tickets);
    renderColumn('Completed', 'bi-check-circle-fill', 'bg-success', 'complete', $tickets);
    ?>
</div>

<?php require_once 'includes/footer.php'; ?>