<?php 
require_once 'includes/db.php'; 
require_once 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = $_POST['login_input']; // Can be username or email
    $password = $_POST['password'];

    // SQL to check both Username OR Email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$input, $input]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        
        // Check if user is Banned
        if ($user['is_banned'] == 1) {
            $error = "Your account has been suspended. Please contact Admin.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'technician') header("Location: job_pool.php");
            elseif ($user['role'] == 'admin') header("Location: admin_users.php");
            else header("Location: index.php");
            exit;
        }
    } else {
        $error = "Invalid username/email or password.";
    }
}
?>

<div class="row justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-tools text-cit-primary" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold text-cit-primary mt-2">Welcome Back</h3>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger py-2 small rounded-3"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="login_input" class="form-control" id="floatingInput" placeholder="User or Email" required>
                        <label for="floatingInput">Username or Email</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                    </div>
                    <button type="submit" class="btn btn-cit w-100 py-3 fw-bold rounded-3">Sign In</button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">New here?</span>
                    <a href="register.php" class="text-cit-primary fw-bold text-decoration-none">Create Account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>