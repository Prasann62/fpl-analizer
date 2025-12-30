<?php
session_start();

// Strict Access Control
if(!isset($_SESSION['access']) || $_SESSION['role'] !== 'admin'){
  header('location:loginform.php');
  exit();
}

$servername = "localhost";
$username   = "u913997673_prasanna";
$password_db = "Ko%a/2klkcooj]@o";
$dbname     = "u913997673_prasanna";

try {
    $conn = new mysqli($servername, $username, $password_db, $dbname);
    if($conn->connect_error){
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Count Users
    $user_sql = "SELECT COUNT(*) as total FROM signin WHERE role != 'admin' OR role IS NULL";
    $user_result = $conn->query($user_sql);
    $total_users = 0;
    if($user_result && $user_result->num_rows > 0) {
        $row = $user_result->fetch_assoc();
        $total_users = $row['total'];
    }

    // Count Admins
    $admin_sql = "SELECT COUNT(*) as total FROM signin WHERE role = 'admin'";
    $admin_result = $conn->query($admin_sql);
    $total_admins = 0;
    if($admin_result && $admin_result->num_rows > 0) {
        $row = $admin_result->fetch_assoc();
        $total_admins = $row['total'];
    }

    $conn->close();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'favicon-meta.php'; ?>
    <title>Admin Dashboard | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?= time(); ?>" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <i class="bi bi-shield-lock-fill text-warning me-2"></i>
                FPL Manager <span class="badge bg-warning text-dark ms-2 text-xs">ADMIN</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <span class="nav-link text-white-50">Logged in as <?= htmlspecialchars($_SESSION['email']); ?></span>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="logout.php" class="btn btn-outline-danger btn-sm px-3">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-12">
                <h1 class="display-5 fw-bold text-dark mb-2">Platform Overview</h1>
                <p class="text-muted lead">Real-time statistics for FPL Manager system.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <!-- Total Users Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3">
                                <i class="bi bi-people-fill fs-3"></i>
                            </div>
                            <h5 class="card-title text-muted fw-bold mb-0 text-uppercase small ls-1">Total Users</h5>
                        </div>
                        <h2 class="display-4 fw-bold mb-0 text-dark"><?= number_format($total_users); ?></h2>
                        <div class="mt-3 text-success small fw-semibold">
                            <i class="bi bi-graph-up-arrow me-1"></i> Active Community
                        </div>
                        <!-- Decorative background icon -->
                        <i class="bi bi-people-fill position-absolute text-muted opacity-10" style="font-size: 8rem; right: -20px; bottom: -20px; transform: rotate(-15deg);"></i>
                    </div>
                </div>
            </div>

            <!-- Total Admins Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                                <i class="bi bi-shield-check fs-3"></i>
                            </div>
                            <h5 class="card-title text-muted fw-bold mb-0 text-uppercase small ls-1">Administrators</h5>
                        </div>
                        <h2 class="display-4 fw-bold mb-0 text-dark"><?= number_format($total_admins); ?></h2>
                        <div class="mt-3 text-muted small fw-semibold">
                            System Managers
                        </div>
                        <!-- Decorative background icon -->
                        <i class="bi bi-shield-check position-absolute text-muted opacity-10" style="font-size: 8rem; right: -20px; bottom: -20px; transform: rotate(-15deg);"></i>
                    </div>
                </div>
            </div>
             
             <!-- System Status Card -->
             <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden bg-gradient-to-br from-slate-800 to-black text-white">
                    <div class="card-body p-4 position-relative" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white;">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-success bg-opacity-20 text-success p-3 rounded-3 me-3">
                                <i class="bi bi-hdd-rack-fill fs-3"></i>
                            </div>
                            <h5 class="card-title text-white-50 fw-bold mb-0 text-uppercase small ls-1">System Status</h5>
                        </div>
                        <h2 class="display-6 fw-bold mb-0">Operational</h2>
                        <div class="mt-3 text-success small fw-semibold">
                            <i class="bi bi-check-circle-fill me-1"></i> All systems nominal
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Inline Tailwind for background helper -->
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
