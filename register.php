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
                $success = "Registration Successful! You can now <a href='login.php' class='fw-bold'>Login here</a>.";
            } else {
                $error = "Database error.";
            }
        }
    }
}
?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <h3 class="fw-bold text-cit-primary text-center mb-4">Create Account</h3>

                <?php if($error): ?>
                    <div class="alert alert-danger py-2 small"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success py-3 text-center"><?= $success ?></div>
                <?php else: ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Email (@email.kmutnb.ac.th)</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label small text-muted">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-cit w-100 py-3 fw-bold rounded-3">Register</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>