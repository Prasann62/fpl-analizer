<?php
// SECURITY: Use secure session handler
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/security.php';

// SECURITY: Require admin role
SecureSession::requireRole('admin', 'loginform.php');

$message = '';
$messageType = '';

// SECURITY: Generate CSRF token for forms
$csrfToken = Security::generateCSRFToken();

try {
    // SECURITY: Use secure database wrapper
    $db = Database::getInstance();

    // Handle Delete Action
    if(isset($_GET['delete']) && !empty($_GET['delete'])) {
        $delete_email = Security::sanitizeInput(urldecode($_GET['delete']), 'email', 255);
        
        // SECURITY: Prevent self-deletion
        if(isset($_SESSION['email']) && $delete_email == $_SESSION['email']) {
            $message = "You cannot delete your own account!";
            $messageType = "danger";
            
            Security::logSecurityEvent('Admin attempted self-deletion', [
                'email' => $delete_email
            ]);
        } else {
            $db->execute("DELETE FROM signin WHERE email = ?", 's', [$delete_email]);
            $message = "User deleted successfully!";
            $messageType = "success";
            
            Security::logSecurityEvent('Admin deleted user', [
                'deleted_email' => $delete_email
            ]);
        }
    }

    // Handle Edit Action (POST)
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
        // SECURITY: Verify CSRF token
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('CSRF validation failed');
        }

        $original_email = Security::sanitizeInput($_POST['original_email'], 'email', 255);
        $edit_name = Security::sanitizeInput($_POST['full_name'], 'string', 100);
        $edit_email = Security::sanitizeInput($_POST['email'], 'email', 255);
        $edit_role = $_POST['role'] === 'admin' ? 'admin' : null;
        $new_password = $_POST['new_password'] ?? '';

        if(!empty($new_password)) {
            // SECURITY: Hash password before storage
            $hashed_password = Security::hashPassword($new_password);
            $db->execute(
                "UPDATE signin SET name = ?, email = ?, role = ?, password = ? WHERE email = ?",
                'sssss',
                [$edit_name, $edit_email, $edit_role, $hashed_password, $original_email]
            );
        } else {
            $db->execute(
                "UPDATE signin SET name = ?, email = ?, role = ? WHERE email = ?",
                'ssss',
                [$edit_name, $edit_email, $edit_role, $original_email]
            );
        }
        
        $message = "User updated successfully!";
        $messageType = "success";
        
        Security::logSecurityEvent('Admin updated user', [
            'original_email' => $original_email,
            'new_email' => $edit_email
        ]);
    }

    // Handle Add New User (POST)
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
        // SECURITY: Verify CSRF token
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('CSRF validation failed');
        }

        $add_name = Security::sanitizeInput($_POST['full_name'], 'string', 100);
        $add_email = Security::sanitizeInput($_POST['email'], 'email', 255);
        $add_password = $_POST['password'];
        $add_role = $_POST['role'] === 'admin' ? 'admin' : null;

        // SECURITY: Validate email
        if (!Security::validateEmail($add_email)) {
            throw new Exception('Invalid email address');
        }

        // SECURITY: Validate password
        $passwordValidation = Security::validatePassword($add_password);
        if (!$passwordValidation['valid']) {
            throw new Exception(implode(', ', $passwordValidation['errors']));
        }

        // Check if email already exists
        $existing = $db->selectOne("SELECT email FROM signin WHERE email = ?", 's', [$add_email]);
        
        if($existing) {
            $message = "Email already exists!";
            $messageType = "danger";
        } else {
            // SECURITY: Hash password before storage
            $hashed_password = Security::hashPassword($add_password);
            
            $db->execute(
                "INSERT INTO signin (name, email, password, role) VALUES (?, ?, ?, ?)",
                'ssss',
                [$add_name, $add_email, $hashed_password, $add_role]
            );
            
            $message = "User added successfully!";
            $messageType = "success";
            
            Security::logSecurityEvent('Admin added new user', [
                'email' => $add_email,
                'role' => $add_role ?? 'user'
            ]);
        }
    }

    // Fetch users with filtering
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $search = isset($_GET['search']) ? Security::sanitizeInput($_GET['search'], 'string', 255) : '';

    // Build query based on filter
    $query = "SELECT * FROM signin WHERE 1=1";
    $params = [];
    $types = '';

    if ($filter === 'admins') {
        $query .= " AND role = ?";
        $types .= 's';
        $params[] = 'admin';
    } elseif ($filter === 'users') {
        $query .= " AND (role IS NULL OR role != ?)";
        $types .= 's';
        $params[] = 'admin';
    }

    if (!empty($search)) {
        $query .= " AND (name LIKE ? OR email LIKE ?)";
        $types .= 'ss';
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    // Removed ORDER BY id DESC as the column is missing in current schema
    //$query .= " ORDER BY id DESC";

    $users = $db->selectAll($query, $types, $params);
    
    // Count stats
    $total_users = 0;
    $total_admins = 0;
    foreach($users as $user) {
        if(isset($user['role']) && $user['role'] === 'admin') {
            $total_admins++;
        } else {
            $total_users++;
        }
    }

} catch (Exception $e) {
    Security::logSecurityEvent('Admin users page error', [
        'error' => $e->getMessage()
    ]);
    
    $message = "Error: " . $e->getMessage();
    $messageType = "danger";
    $users = [];
    $total_users = 0;
    $total_admins = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'favicon-meta.php'; ?>
    <title>Manage Users | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0f0f23;
            --bg-secondary: #1a1a2e;
            --bg-card: rgba(30, 30, 50, 0.7);
            --accent-purple: #8b5cf6;
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
            --accent-yellow: #f59e0b;
            --accent-red: #ef4444;
            --accent-pink: #ec4899;
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-muted: rgba(255, 255, 255, 0.4);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            color: var(--text-primary);
        }

        .bg-animated {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                var(--bg-primary);
        }

        .admin-navbar {
            background: rgba(15, 15, 35, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--accent-yellow), #f97316);
            color: #000;
            font-size: 0.65rem;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-back {
            background: rgba(139, 92, 246, 0.2);
            border: 1px solid var(--accent-purple);
            color: var(--accent-purple);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-back:hover {
            background: var(--accent-purple);
            color: white;
        }

        .page-header {
            padding: 2rem 0;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .card-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .card-title i { color: var(--accent-purple); }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
        }

        .filter-tab {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .filter-tab:hover, .filter-tab.active {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: white;
            border-color: transparent;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            color: white;
            width: 250px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--accent-purple);
            background: rgba(255, 255, 255, 0.08);
        }

        .search-box i {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .users-table {
            width: 100%;
        }

        .users-table th {
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            padding: 1rem 1.2rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .users-table td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--glass-border);
            vertical-align: middle;
        }

        .users-table tbody tr {
            transition: all 0.3s ease;
        }

        .users-table tbody tr:hover {
            background: rgba(139, 92, 246, 0.08);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .user-avatar.admin {
            background: linear-gradient(135deg, var(--accent-yellow), #f97316);
            color: #000;
        }

        .user-avatar.user {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .user-name { font-weight: 600; }
        .user-email { font-size: 0.85rem; color: var(--text-muted); }

        .role-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(249, 115, 22, 0.2));
            color: var(--accent-yellow);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .role-badge.user {
            background: rgba(139, 92, 246, 0.15);
            color: var(--accent-purple);
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .action-btn {
            padding: 0.4rem 0.7rem;
            border-radius: 8px;
            border: none;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 0.3rem;
        }

        .action-btn.edit {
            background: rgba(59, 130, 246, 0.15);
            color: var(--accent-blue);
        }

        .action-btn.edit:hover {
            background: var(--accent-blue);
            color: white;
        }

        .action-btn.delete {
            background: rgba(239, 68, 68, 0.15);
            color: var(--accent-red);
        }

        .action-btn.delete:hover {
            background: var(--accent-red);
            color: white;
        }

        .btn-add-user {
            background: linear-gradient(135deg, var(--accent-green), #84cc16);
            color: #000;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-user:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.4);
            color: #000;
        }

        .stats-mini {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-mini-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-mini-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-mini-icon.purple { background: rgba(139, 92, 246, 0.2); color: var(--accent-purple); }
        .stat-mini-icon.yellow { background: rgba(245, 158, 11, 0.2); color: var(--accent-yellow); }

        .stat-mini-value { font-size: 1.5rem; font-weight: 800; }
        .stat-mini-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; }

        /* Modal Styling */
        .modal-content {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            color: var(--text-primary);
        }

        .modal-header {
            border-bottom: 1px solid var(--glass-border);
            padding: 1.2rem 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--glass-border);
            padding: 1rem 1.5rem;
        }

        .modal-title {
            font-weight: 700;
            color: var(--text-primary);
        }

        .btn-close {
            filter: invert(1);
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: white;
            padding: 0.7rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
            color: white;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .form-select option {
            background: var(--bg-secondary);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .alert-custom {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-custom.success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-custom.danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--accent-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        @media (max-width: 768px) {
            .card-header { flex-direction: column; align-items: flex-start; }
            .search-box input { width: 100%; }
            .filter-tabs { flex-wrap: wrap; }
            .stats-mini { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="bg-animated"></div>

    <!-- Admin Navbar -->
    <nav class="admin-navbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="navbar-brand mb-0">
                        <i class="bi bi-shield-lock-fill me-2"></i>FPL Manager
                    </span>
                    <span class="admin-badge">Admin</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="admin_dashboard.php" class="btn-back">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="page-title"><i class="bi bi-people-fill me-2"></i>Manage Users</h1>
                    <p class="text-secondary mb-0">View, edit, and delete users and administrators</p>
                </div>
                <button class="btn btn-add-user" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle me-2"></i>Add New User
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if(!empty($message)): ?>
        <div class="alert-custom <?= $messageType ?>">
            <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <!-- Debug Info (remove after fixing) -->
        <?php if(!empty($debug_info)): ?>
        <div class="alert-custom" style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #3b82f6;">
            <i class="bi bi-bug me-2"></i>
            <strong>Debug:</strong> <?= htmlspecialchars($debug_info) ?>
        </div>
        <?php endif; ?>

        <!-- Mini Stats -->
        <div class="stats-mini">
            <div class="stat-mini-card">
                <div class="stat-mini-icon purple">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-mini-value"><?= $total_users ?></div>
                    <div class="stat-mini-label">Total Users</div>
                </div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-icon yellow">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="stat-mini-value"><?= $total_admins ?></div>
                    <div class="stat-mini-label">Administrators</div>
                </div>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="glass-card">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="card-title">
                        <i class="bi bi-table"></i>
                        All Accounts
                    </div>
                    <div class="filter-tabs">
                        <a href="?filter=all&search=<?= urlencode($search) ?>" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">All</a>
                        <a href="?filter=users&search=<?= urlencode($search) ?>" class="filter-tab <?= $filter === 'users' ? 'active' : '' ?>">Users</a>
                        <a href="?filter=admins&search=<?= urlencode($search) ?>" class="filter-tab <?= $filter === 'admins' ? 'active' : '' ?>">Admins</a>
                    </div>
                </div>
                <form method="GET" class="search-box">
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
                </form>
            </div>

            <?php if(count($users) > 0): ?>
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; foreach($users as $user): ?>
                        <tr>
                            <td><span class="text-muted"><?= $counter++ ?></span></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar <?= $user['role'] === 'admin' ? 'admin' : 'user' ?>">
                                        <?= strtoupper(substr($user['name'] ?? $user['email'], 0, 1)) ?>
                                    </div>
                                    <span class="user-name"><?= htmlspecialchars($user['name'] ?? 'Unknown') ?></span>
                                </div>
                            </td>
                            <td class="user-email"><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="role-badge <?= $user['role'] === 'admin' ? 'admin' : 'user' ?>">
                                    <?= $user['role'] === 'admin' ? 'Admin' : 'User' ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : '-' ?>
                            </td>
                            <td>
                                <button class="action-btn edit" onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn delete" onclick="confirmDelete('<?= urlencode($user['email']) ?>', '<?= htmlspecialchars($user['name'] ?? $user['email']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-inbox d-block"></i>
                <h5>No users found</h5>
                <p class="mb-0">Try adjusting your search or filter criteria</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- SECURITY: CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="add_user" value="1">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Enter password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-add-user">
                            <i class="bi bi-plus-circle me-1"></i>Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- SECURITY: CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="edit_user" value="1">
                        <input type="hidden" name="original_email" id="edit_original_email">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: var(--accent-blue); color: white;">
                            <i class="bi bi-check-circle me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete <strong id="delete_user_name"></strong>?</p>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="delete_confirm_btn" class="btn" style="background: var(--accent-red); color: white;">
                        <i class="bi bi-trash me-1"></i>Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(user) {
            document.getElementById('edit_original_email').value = user.email;
            document.getElementById('edit_full_name').value = user.name || '';
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role === 'admin' ? 'admin' : 'user';
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        function confirmDelete(userEmail, userName) {
            document.getElementById('delete_user_name').textContent = userName;
            document.getElementById('delete_confirm_btn').href = '?delete=' + userEmail + '&filter=<?= $filter ?>&search=<?= urlencode($search) ?>';
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
