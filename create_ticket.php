<?php 
require_once 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room = $_POST['room'];
    $cat = $_POST['category']; // Now text input
    $detail = $_POST['detail'];
    $imgName = null;

    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/uploads/$imgName");
    }

    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, room_number, category, details, user_image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $room, $cat, $detail, $imgName]);
    echo "<script>window.location='my_tickets.php';</script>";
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-cit-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Report a Problem</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Room Number</label>
                                <input type="text" name="room" class="form-control" placeholder="e.g. 305" required>
                            </div>
                            <div class="col-md-8 mt-3 mt-md-0">
                                <label class="form-label fw-bold">Category</label>
                                <input type="text" name="category" class="form-control" placeholder="Type category (e.g. Air Con, Lights)" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Describe the Issue</label>
                            <textarea name="detail" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Attach Photo (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <a href="index.php" class="btn btn-outline-secondary px-4 d-flex align-items-center justify-content-center" style="height: 45px;">Cancel</a>
                            <button type="submit" class="btn btn-cit px-5 d-flex align-items-center justify-content-center" style="height: 45px;">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>