<?php 
require_once 'includes/db.php'; 
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $pass2 = $_POST['confirm_password'];

    if (!str_ends_with($email, '@email.kmutnb.ac.th')) {
        $error = "Registration is restricted to @email.kmutnb.ac.th only.";
    } elseif ($pass !== $pass2) {
        $error = "Passwords do not match.";
    } else {
        // Check for duplicate Username
        $checkUser = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkUser->execute([$username]);
        
        // Check for duplicate Email
        $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->execute([$email]);

        if ($checkUser->rowCount() > 0) {
            $error = "Username already taken.";
        } elseif ($checkEmail->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, password_hash($pass, PASSWORD_DEFAULT)])) {
                $success = "Registration Successful! You can now <a href='login.php' class='fw-bold text-success text-decoration-underline'>Login here</a>.";
            } else {
                $error = "Database error.";
            }
        }
    }
}
?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="bg-cit-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 64px; height: 64px;">
                        <i class="bi bi-person-plus fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Create Account</h3>
                    <p class="text-muted small">Join the CIT Fix It portal</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger py-2 small rounded-3 border-0 bg-danger-subtle text-danger text-center fw-medium">
                        <i class="bi bi-exclamation-circle me-1"></i><?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success py-3 text-center rounded-3 border-0 bg-success-subtle text-success fw-medium">
                        <i class="bi bi-check-circle-fill me-2 fs-4 d-block mb-2"></i>
                        <?= $success ?>
                    </div>
                <?php else: ?>

                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control bg-light border-0" id="regUsername" placeholder="Username" required>
                        <label for="regUsername" class="text-muted">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control bg-light border-0" id="regEmail" placeholder="Email" required>
                        <label for="regEmail" class="text-muted">Email (@email.kmutnb.ac.th)</label>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control bg-light border-0" id="regPass" placeholder="Password" required>
                                <label for="regPass" class="text-muted">Password</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" name="confirm_password" class="form-control bg-light border-0" id="regPass2" placeholder="Confirm Password" required>
                                <label for="regPass2" class="text-muted">Confirm Password</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-cit w-100 py-2 fs-5 fw-bold rounded-3 shadow-sm">Register</button>
                </form>
                <?php endif; ?>

                <?php if(!$success): ?>
                <div class="text-center mt-4 pt-3 border-top">
                    <span class="text-muted small">Already have an account?</span>
                    <a href="login.php" class="text-cit-primary fw-semibold text-decoration-none">Sign In</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>