<?php
require_once 'includes/header.php';
// Check logic...
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'technician') exit;
?>

<div class="container-fluid px-lg-5">
    <h2 class="fw-bold text-cit-primary mb-4">Technician Console</h2>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-warning fw-bold"><i class="bi bi-inbox me-2"></i>Job Pool</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Room</th>
                                    <th>Issue</th>
                                    <th>Img</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM tickets WHERE status = 'pending' ORDER BY created_at ASC");
                                while ($row = $stmt->fetch()):
                                ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?= htmlspecialchars($row['room_number']) ?></td>
                                        <td><?= htmlspecialchars($row['category']) ?></td>
                                        <td><?= $row['user_image'] ? '<i class="bi bi-image"></i>' : '-' ?></td>
                                        <td class="text-end pe-4">
                                            <form method="POST" action="ticket_action.php">
                                                <input type="hidden" name="ticket_id" value="<?= $row['id'] ?>">
                                                <button type="submit" name="action" value="accept" class="btn btn-sm btn-outline-primary rounded-pill">Accept</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 bg-cit-primary text-white h-100">
                <div class="card-header border-white border-opacity-25 py-3">
                    <h5 class="card-title mb-0 text-white"><i class="bi bi-person-workspace me-2"></i>My Workspace</h5>
                </div>
                <div class="card-body overflow-auto" style="max-height: 80vh;">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE technician_id = ? AND status IN ('fixing', 'waiting_material')");
                    $stmt->execute([$_SESSION['user_id']]);
                    $myJobs = $stmt->fetchAll();

                    if (empty($myJobs)) echo "<p class='text-center text-white-50 mt-4'>No active jobs.</p>";

                    foreach ($myJobs as $job):
                    ?>
                        <div class="card mb-3 border-0 shadow-lg text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-dark">Room <?= htmlspecialchars($job['room_number']) ?></span>
                                    <span class="badge <?= $job['status'] == 'waiting_material' ? 'bg-danger' : 'bg-primary' ?>">
                                        <?= $job['status'] == 'waiting_material' ? 'Waiting Material' : 'In Progress' ?>
                                    </span>
                                </div>
                                <h6 class="fw-bold"><?= htmlspecialchars($job['category']) ?></h6>
                                <p class="small text-muted mb-3"><?= htmlspecialchars($job['details']) ?></p>

                                <form method="POST" action="ticket_action.php" class="mb-3">
                                    <input type="hidden" name="ticket_id" value="<?= $job['id'] ?>">
                                    <div class="d-flex gap-2">
                                        <?php if ($job['status'] == 'fixing'): ?>
                                            <button type="submit" name="action" value="wait_material" class="btn btn-warning btn-sm flex-fill">Pause Job</button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="resume" class="btn btn-info btn-sm flex-fill text-white">Resume Job</button>
                                        <?php endif; ?>
                                    </div>
                                </form>

                                <form method="POST" action="ticket_action.php" enctype="multipart/form-data" class="border-top pt-2">
                                    <input type="hidden" name="ticket_id" value="<?= $job['id'] ?>">
                                    <label class="small text-muted mb-1">Upload Proof & Finish</label>
                                    <div class="input-group input-group-sm">

                                        <input type="file" name="proof_img" class="form-control" required accept="image/*">

                                        <button type="submit" name="action" value="complete" class="btn btn-success" title="Complete Job">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>