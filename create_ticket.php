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

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="d-flex align-items-center mb-4 gap-3">
            <div class="bg-white text-cit-primary rounded-circle shadow-sm d-flex justify-content-center align-items-center" style="width: 48px; height: 48px;">
                <i class="bi bi-pencil-square fs-4"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark mb-0">Report an Issue</h3>
                <p class="text-muted small mb-0">Help us keep everything running smoothly.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Room Number</label>
                            <input type="text" name="room" class="form-control bg-light border-0" placeholder="e.g. 305" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-muted small fw-bold text-uppercase">Issue Category</label>
                            <input type="text" name="category" class="form-control bg-light border-0" placeholder="e.g. Air Con, Lights, Network" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Details</label>
                        <textarea name="detail" class="form-control bg-light border-0" rows="4" placeholder="Please describe the problem in detail..." required></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-muted small fw-bold text-uppercase">Attach Photo <span class="fw-normal text-black-50">(Optional)</span></label>
                        <input type="file" name="image" class="form-control bg-light border-0" accept="image/*">
                        <div class="form-text small mt-2"><i class="bi bi-info-circle me-1"></i>A photo helps our technicians prepare the right tools.</div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-3 pt-3 border-top">
                        <a href="index.php" class="btn text-muted fw-medium px-4 hover-bg-light rounded-pill">Cancel</a>
                        <button type="submit" class="btn btn-cit px-5 rounded-pill shadow-sm fw-bold d-flex align-items-center gap-2">
                            Submit Request <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>