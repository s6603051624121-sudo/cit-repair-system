<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php'; 

// Get current page for active state highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIT Fix It | Service Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-white d-flex align-items-center" href="index.php">
        <i class="bi bi-tools me-2 fs-4"></i>
        <span>CIT Fix It</span>
    </a>

    <button class="navbar-toggler border-0 text-white focus-ring focus-ring-light" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
        <i class="bi bi-list fs-1"></i>
    </button>
    
    <div class="collapse navbar-collapse" id="navContent">
      
      <?php if(isset($_SESSION['user_id'])): ?>
        <div class="mobile-user-header d-lg-none d-flex align-items-center mb-3">
            <div class="bg-white text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem;">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </div>
            <div>
                <div class="text-white fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></div>
                <div class="text-white-50 small"><?= ucfirst($_SESSION['role']) ?> Account</div>
            </div>
        </div>
      <?php endif; ?>

      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <li class="nav-item text-white-50 d-none d-lg-block small">
                Hello, <span class="text-white fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></span>
            </li>

            <?php if($_SESSION['role'] == 'user'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'create_ticket.php' ? 'active' : '' ?>" href="create_ticket.php">
                        <i class="bi bi-plus-circle me-2 d-lg-none"></i>Report Issue
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'my_tickets.php' ? 'active' : '' ?>" href="my_tickets.php">
                        <i class="bi bi-clock-history me-2 d-lg-none"></i>History
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if($_SESSION['role'] == 'technician'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'job_pool.php' ? 'active' : '' ?>" href="job_pool.php">
                        <i class="bi bi-inbox me-2 d-lg-none"></i>Job Pool
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'tech_history.php' ? 'active' : '' ?>" href="tech_history.php">
                        <i class="bi bi-journal-check me-2 d-lg-none"></i>Work Log
                    </a>
                </li>
            <?php endif; ?>

            <?php if($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'admin_jobs.php' ? 'active' : '' ?>" href="admin_jobs.php">
                        <i class="bi bi-clipboard-check me-2 d-lg-none"></i>Job Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'admin_users.php' ? 'active' : '' ?>" href="admin_users.php">
                        <i class="bi bi-people me-2 d-lg-none"></i>Users
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item mt-3 mt-lg-0">
                <a class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm w-100 w-lg-auto" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </li>

        <?php else: ?>
            <li class="nav-item"><a href="login.php" class="btn btn-outline-light rounded-pill px-4 me-2 w-100 w-lg-auto mb-2 mb-lg-0">Login</a></li>
            <li class="nav-item"><a href="register.php" class="btn btn-light rounded-pill px-4 text-primary fw-bold w-100 w-lg-auto">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4 flex-grow-1">