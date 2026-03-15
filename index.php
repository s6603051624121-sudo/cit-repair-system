<?php 
require_once 'includes/header.php';

// Fetch all tickets for the dashboard
$stmt = $pdo->query("SELECT * FROM tickets ORDER BY updated_at DESC");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Dashboard Summaries
$total = count($tickets);
$pending = count(array_filter($tickets, fn($t) => $t['status'] === 'pending'));
$material = count(array_filter($tickets, fn($t) => $t['status'] === 'waiting_material'));
$fixing = count(array_filter($tickets, fn($t) => $t['status'] === 'fixing'));
$complete = count(array_filter($tickets, fn($t) => $t['status'] === 'complete'));

// Helper for Kanban columns
function renderColumn($title, $icon, $headerColorClass, $statusKey, $tickets) {
    $filtered = array_filter($tickets, fn($t) => $t['status'] === $statusKey);
    
    // Map status to our new CSS strip colors
    $statusColorClass = match($statusKey) {
        'pending' => 'status-pending',
        'waiting_material' => 'status-material',
        'fixing' => 'status-fixing',
        'complete' => 'status-complete',
        default => ''
    };
    ?>
    <div class="col-xl-3 col-lg-6 mb-4">
        <div class="rounded-4 border h-100 d-flex flex-column shadow-sm" style="background-color: #f1f5f9;">
            <div class="p-3 d-flex justify-content-between align-items-center border-bottom bg-white rounded-top-4">
                <div class="fw-bold d-flex align-items-center gap-2 <?= $headerColorClass ?>">
                    <i class="<?= $icon ?> fs-5"></i> <?= $title ?>
                </div>
                <span class="badge bg-light text-dark border rounded-pill shadow-sm px-2 py-1"><?= count($filtered) ?></span>
            </div>
            
            <div class="p-3 flex-grow-1 overflow-auto" style="max-height: 480px;">
                <?php if (empty($filtered)): ?>
                    <div class="text-center text-muted py-5 small border rounded-3 bg-white" style="border-style: dashed !important;">
                        <i class="bi bi-inbox fs-3 d-block mb-2 text-black-50"></i>
                        No tasks in this queue
                    </div>
                <?php else: ?>
                    <?php foreach($filtered as $t): ?>
                        <div class="card mb-3 hover-card border-0 shadow-sm position-relative">
                            <div class="status-strip <?= $statusColorClass ?> position-absolute top-0 start-0 w-100 rounded-top" style="height: 4px;"></div>
                            <div class="card-body p-3 pt-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-light text-secondary border border-secondary-subtle rounded-pill px-2">
                                        Room <?= htmlspecialchars($t['room_number']) ?>
                                    </span>
                                    <small class="text-muted fw-medium" style="font-size: 0.75rem;">
                                        <i class="bi bi-calendar3 me-1"></i><?= date('M d', strtotime($t['created_at'])) ?>
                                    </small>
                                </div>
                                <h6 class="card-title fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($t['category']) ?>">
                                    <?= htmlspecialchars($t['category']) ?>
                                </h6>
                                <?php if (!empty($t['details'])): ?>
                                    <p class="small text-muted mb-0 text-truncate" style="max-width: 100%;">
                                        <?= htmlspecialchars($t['details']) ?>
                                    </p>
                                <?php endif; ?>
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark m-0">Live Dashboard</h3>
    <span class="text-muted small"><i class="bi bi-arrow-clockwise me-1"></i>Auto-updates</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 p-3 border-start border-4" style="border-left-color: var(--cit-primary) !important;">
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Issues</div>
            <div class="fs-3 fw-bold text-dark"><?= $total ?></div>
        </div>
    </div>
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 p-3 border-start border-4" style="border-left-color: #f59e0b !important;">
            <div class="text-muted small fw-bold text-uppercase mb-1">Pending</div>
            <div class="fs-3 fw-bold text-dark"><?= $pending ?></div>
        </div>
    </div>
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 p-3 border-start border-4" style="border-left-color: #ef4444 !important;">
            <div class="text-muted small fw-bold text-uppercase mb-1">Mat. Wait</div>
            <div class="fs-3 fw-bold text-dark"><?= $material ?></div>
        </div>
    </div>
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100 p-3 border-start border-4" style="border-left-color: #3b82f6 !important;">
            <div class="text-muted small fw-bold text-uppercase mb-1">In Progress</div>
            <div class="fs-3 fw-bold text-dark"><?= $fixing ?></div>
        </div>
    </div>
    <div class="col-md col-12">
        <div class="card border-0 shadow-sm h-100 p-3 border-start border-4" style="border-left-color: #10b981 !important;">
            <div class="text-muted small fw-bold text-uppercase mb-1">Completed</div>
            <div class="fs-3 fw-bold text-dark"><?= $complete ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <?php 
    renderColumn('Waiting', 'bi-hourglass-split', 'text-warning', 'pending', $tickets);
    renderColumn('Material', 'bi-box-seam', 'text-danger', 'waiting_material', $tickets);
    renderColumn('In Progress', 'bi-tools', 'text-primary', 'fixing', $tickets);
    renderColumn('Done', 'bi-check-circle-fill', 'text-success', 'complete', $tickets);
    ?>
</div>

<?php require_once 'includes/footer.php'; ?>