<?php
require_once 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'technician') exit;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Tech Console</h3>
        <p class="text-muted small mb-0">Manage incoming requests and your active tasks.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <div class="bg-warning-subtle text-warning-emphasis rounded p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-inbox fs-5"></i>
                    </div>
                    Available Jobs
                </h5>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Room</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Issue</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold border-bottom-0">Media</th>
                                <th class="text-end pe-4 py-3 text-muted small text-uppercase fw-bold border-bottom-0">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php
                            $stmt = $pdo->query("SELECT * FROM tickets WHERE status = 'pending' ORDER BY created_at ASC");
                            $hasJobs = false;
                            while ($row = $stmt->fetch()):
                                $hasJobs = true;
                            ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span class="badge bg-light text-dark border border-secondary-subtle px-2 py-1 rounded-2">
                                            <?= htmlspecialchars($row['room_number']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['category']) ?></div>
                                        <div class="text-muted small text-truncate" style="max-width: 200px;">
                                            <?= htmlspecialchars($row['details']) ?>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <?php if ($row['user_image']): ?>
                                            <span class="text-primary" title="Has attached image"><i class="bi bi-image fs-5"></i></span>
                                        <?php else: ?>
                                            <span class="text-muted opacity-50">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <form method="POST" action="ticket_action.php">
                                            <input type="hidden" name="ticket_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="action" value="accept" class="btn btn-sm btn-cit rounded-pill px-4 fw-medium shadow-sm">
                                                Accept Job
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            
                            <?php if (!$hasJobs): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="bi bi-emoji-sunglasses fs-2"></i></div>
                                        <h6 class="text-dark fw-bold">Queue is empty</h6>
                                        <p class="text-muted small mb-0">There are no pending requests right now.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="d-flex align-items-center mb-3 px-2">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <div class="bg-primary-subtle text-primary-emphasis rounded p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-person-workspace fs-5"></i>
                </div>
                My Workspace
            </h5>
        </div>
        
        <div class="overflow-auto pe-2 pb-4" style="max-height: 75vh;">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM tickets WHERE technician_id = ? AND status IN ('fixing', 'waiting_material')");
            $stmt->execute([$_SESSION['user_id']]);
            $myJobs = $stmt->fetchAll();

            if (empty($myJobs)): ?>
                <div class="card border-0 shadow-sm rounded-4 border border-dashed border-secondary-subtle bg-transparent">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clipboard-x fs-2 text-muted mb-2 d-block"></i>
                        <p class="text-muted small mb-0">You have no active tasks.<br>Accept a job from the pool to begin.</p>
                    </div>
                </div>
            <?php endif;

            foreach ($myJobs as $job):
                $isWaiting = $job['status'] == 'waiting_material';
                $borderLeftColor = $isWaiting ? '#ef4444' : 'var(--cit-primary)';
                $badgeClass = $isWaiting ? 'bg-danger-subtle text-danger-emphasis' : 'bg-primary-subtle text-primary-emphasis';
                $badgeText = $isWaiting ? 'Waiting for Material' : 'In Progress';
            ?>
                <div class="card mb-3 border-0 shadow-sm rounded-4" style="border-left: 4px solid <?= $borderLeftColor ?> !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-light text-dark border border-secondary-subtle">Room <?= htmlspecialchars($job['room_number']) ?></span>
                            <span class="badge <?= $badgeClass ?> rounded-pill px-2"><?= $badgeText ?></span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($job['category']) ?></h6>
                        <p class="small text-muted mb-4"><?= htmlspecialchars($job['details']) ?></p>

                        <form method="POST" action="ticket_action.php" class="mb-3">
                            <input type="hidden" name="ticket_id" value="<?= $job['id'] ?>">
                            <div class="d-flex gap-2">
                                <?php if (!$isWaiting): ?>
                                    <button type="submit" name="action" value="wait_material" class="btn btn-outline-danger btn-sm flex-fill rounded-pill fw-medium">
                                        <i class="bi bi-pause-circle me-1"></i> Pause (Need Material)
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="resume" class="btn btn-outline-primary btn-sm flex-fill rounded-pill fw-medium">
                                        <i class="bi bi-play-circle me-1"></i> Resume Job
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>

                        <form method="POST" action="ticket_action.php" enctype="multipart/form-data" class="bg-light p-3 rounded-3 mt-3 border">
                            <input type="hidden" name="ticket_id" value="<?= $job['id'] ?>">
                            <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Finish Job & Upload Proof</label>
                            <div class="input-group">
                                <input type="file" name="proof_img" class="form-control form-control-sm bg-white" required accept="image/*">
                                <button type="submit" name="action" value="complete" class="btn btn-success btn-sm px-3 fw-bold" title="Mark as Complete">
                                    Done <i class="bi bi-check2-circle"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>