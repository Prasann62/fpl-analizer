<?php
// SECURITY: Use secure session handler
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/security.php';

// SECURITY: Require admin role
SecureSession::requireRole('admin', 'loginform.php');

$db = Database::getInstance();

// Count Users
try {
    $user_result = $db->selectOne("SELECT COUNT(*) as total FROM signin WHERE role != 'admin' OR role IS NULL");
    $total_users = $user_result ? (int)$user_result['total'] : 0;
} catch (Exception $e) {
    $total_users = 0;
    Security::logSecurityEvent('Admin dashboard: total users fetch failed', ['error' => $e->getMessage()]);
}

// Count Admins
try {
    $admin_result = $db->selectOne("SELECT COUNT(*) as total FROM signin WHERE role = 'admin'");
    $total_admins = $admin_result ? (int)$admin_result['total'] : 0;
} catch (Exception $e) {
    $total_admins = 0;
    Security::logSecurityEvent('Admin dashboard: total admins fetch failed', ['error' => $e->getMessage()]);
}

// Get Recent Users (last 5)
try {
    // Falls back to no ordering if 'id' is missing
    $recent_users = $db->selectAll("SELECT * FROM signin WHERE role != 'admin' OR role IS NULL LIMIT 5");
} catch (Exception $e) {
    $recent_users = [];
    Security::logSecurityEvent('Admin dashboard: recent users fetch failed', ['error' => $e->getMessage()]);
}

// Get user registration stats for chart (last 7 days)
try {
    $chart_data = $db->selectAll("SELECT DATE(created_at) as date, COUNT(*) as count FROM signin WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY date ASC");
} catch (Exception $e) {
    $chart_data = [];
    Security::logSecurityEvent('Admin dashboard: chart data fetch failed', ['error' => $e->getMessage()]);
}

// Total account count
$total_accounts = $total_users + $total_admins;

// Get greeting based on time
$hour = date('H');
if ($hour < 12) {
    $greeting = "Good Morning";
    $greeting_icon = "☀️";
} elseif ($hour < 17) {
    $greeting = "Good Afternoon";
    $greeting_icon = "🌤️";
} else {
    $greeting = "Good Evening";
    $greeting_icon = "🌙";
}

// Prepare chart data for JS
$chart_labels = [];
$chart_values = [];
foreach($chart_data as $data) {
    $chart_labels[] = date('M d', strtotime($data['date']));
    $chart_values[] = (int)$data['count'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(16, 185, 129, 0.05) 0%, transparent 70%),
                var(--bg-primary);
        }

        .bg-animated::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.03) 0%, transparent 50%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20%, 20%) scale(1.1); }
        }

        /* Navbar */
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
            letter-spacing: 0.5px;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid var(--accent-red);
            color: var(--accent-red);
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: var(--accent-red);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }

        /* Welcome Section */
        .welcome-section {
            padding: 2rem 0;
        }

        .welcome-card {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(59, 130, 246, 0.2));
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-purple), var(--accent-blue), var(--accent-green));
        }

        .greeting-text {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .greeting-icon {
            font-size: 2.5rem;
            margin-right: 1rem;
            animation: wave 2s ease-in-out infinite;
        }

        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(20deg); }
            75% { transform: rotate(-10deg); }
        }

        .live-clock {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .live-clock i {
            color: var(--accent-green);
            animation: blink 1s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.8rem;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 20px 20px 0 0;
        }

        .stat-card.purple::after { background: linear-gradient(90deg, var(--accent-purple), var(--accent-pink)); }
        .stat-card.blue::after { background: linear-gradient(90deg, var(--accent-blue), #06b6d4); }
        .stat-card.green::after { background: linear-gradient(90deg, var(--accent-green), #84cc16); }
        .stat-card.yellow::after { background: linear-gradient(90deg, var(--accent-yellow), #f97316); }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.2rem;
        }

        .stat-icon.purple { background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.2)); color: var(--accent-purple); }
        .stat-icon.blue { background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(6, 182, 212, 0.2)); color: var(--accent-blue); }
        .stat-icon.green { background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(132, 204, 22, 0.2)); color: var(--accent-green); }
        .stat-icon.yellow { background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(249, 115, 22, 0.2)); color: var(--accent-yellow); }

        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.8rem;
        }

        .stat-card.purple .stat-value { color: var(--accent-purple); }
        .stat-card.blue .stat-value { color: var(--accent-blue); }
        .stat-card.green .stat-value { color: var(--accent-green); }
        .stat-card.yellow .stat-value { color: var(--accent-yellow); }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-green);
        }

        .stat-bg-icon {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 7rem;
            opacity: 0.05;
            transform: rotate(-15deg);
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Glass Card */
        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .card-title i {
            font-size: 1.3rem;
            color: var(--accent-purple);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Chart */
        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Recent Users Table */
        .users-table {
            width: 100%;
        }

        .users-table th {
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .users-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--glass-border);
            vertical-align: middle;
        }

        .users-table tbody tr {
            transition: all 0.3s ease;
        }

        .users-table tbody tr:hover {
            background: rgba(139, 92, 246, 0.05);
        }

        .users-table tbody tr:last-child td {
            border-bottom: none;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .user-date {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .badge-new {
            background: linear-gradient(135deg, var(--accent-green), #84cc16);
            color: #000;
            font-size: 0.65rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-weight: 700;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            gap: 0.8rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.2rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .action-btn:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: var(--accent-purple);
            color: var(--text-primary);
            transform: translateX(5px);
        }

        .action-btn i {
            font-size: 1.2rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(59, 130, 246, 0.2));
            border-radius: 10px;
            color: var(--accent-purple);
        }

        .action-btn span {
            flex: 1;
        }

        .action-btn .bi-chevron-right {
            opacity: 0;
            transition: all 0.3s ease;
            background: none;
            width: auto;
            height: auto;
        }

        .action-btn:hover .bi-chevron-right {
            opacity: 1;
        }

        /* Server Status */
        .status-grid {
            display: grid;
            gap: 1rem;
        }

        .status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
        }

        .status-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .status-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .status-icon.green { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); }
        .status-icon.blue { background: rgba(59, 130, 246, 0.15); color: var(--accent-blue); }
        .status-icon.purple { background: rgba(139, 92, 246, 0.15); color: var(--accent-purple); }

        .status-label {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.online {
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent-green);
        }

        .status-badge.active {
            background: rgba(59, 130, 246, 0.15);
            color: var(--accent-blue);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Footer */
        .admin-footer {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            border-top: 1px solid var(--glass-border);
            margin-top: 2rem;
        }

        /* Animations */
        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-card {
                padding: 1.5rem;
            }
            .greeting-text {
                font-size: 1.5rem;
            }
            .stat-value {
                font-size: 2.2rem;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
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
                <div class="d-flex align-items-center gap-4">
                    <span class="text-secondary d-none d-md-block">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['email']); ?>
                    </span>
                    <a href="logout.php" class="btn-logout">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Welcome Section -->
        <div class="welcome-section fade-in-up">
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="greeting-text">
                            <span class="greeting-icon"><?= $greeting_icon ?></span>
                            <?= $greeting ?>, Admin!
                        </div>
                        <p class="text-secondary mb-0 mt-2">Welcome to your FPL Manager control center. Here's what's happening today.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="live-clock">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                            <span id="liveClock"><?= date('l, F j, Y') ?></span>
                        </div>
                        <div class="text-muted mt-1" id="liveTime"><?= date('h:i:s A') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card purple fade-in-up delay-1">
                <div class="stat-icon purple">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value" data-count="<?= $total_users ?>">0</div>
                <span class="stat-trend">
                    <i class="bi bi-graph-up"></i> Active Community
                </span>
                <i class="bi bi-people-fill stat-bg-icon"></i>
            </div>

            <div class="stat-card blue fade-in-up delay-2">
                <div class="stat-icon blue">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="stat-label">Total Accounts</div>
                <div class="stat-value" data-count="<?= $total_accounts ?>">0</div>
                <span class="stat-trend">
                    <i class="bi bi-database"></i> All Registered
                </span>
                <i class="bi bi-person-badge-fill stat-bg-icon"></i>
            </div>

            <div class="stat-card green fade-in-up delay-3">
                <div class="stat-icon green">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-label">Administrators</div>
                <div class="stat-value" data-count="<?= $total_admins ?>">0</div>
                <span class="stat-trend">
                    <i class="bi bi-check-circle"></i> System Managers
                </span>
                <i class="bi bi-shield-check stat-bg-icon"></i>
            </div>

            <div class="stat-card yellow fade-in-up delay-4">
                <div class="stat-icon yellow">
                    <i class="bi bi-hdd-rack-fill"></i>
                </div>
                <div class="stat-label">System Status</div>
                <div class="stat-value" style="font-size: 1.8rem;">Operational</div>
                <span class="stat-trend">
                    <i class="bi bi-lightning-fill"></i> All Systems Go
                </span>
                <i class="bi bi-hdd-rack-fill stat-bg-icon"></i>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Left Column - Chart & Table -->
            <div class="d-flex flex-column gap-4">
                <!-- User Registration Chart -->
                <div class="glass-card fade-in-up delay-3">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-graph-up-arrow"></i>
                            User Registrations (Last 7 Days)
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="registrationChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Users Table -->
                <div class="glass-card fade-in-up delay-4">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-person-plus"></i>
                            Recent Users
                        </div>
                        <span class="badge-new">Latest</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if(count($recent_users) > 0): ?>
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_users as $index => $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?= strtoupper(substr($user['name'] ?? $user['email'], 0, 1)) ?>
                                            </div>
                                            <span class="user-name"><?= htmlspecialchars($user['name'] ?? 'User') ?></span>
                                        </div>
                                    </td>
                                    <td class="user-email"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="user-date">
                                        <?php 
                                        if(isset($user['created_at'])) {
                                            echo date('M d, Y', strtotime($user['created_at']));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No recent users found</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions & Status -->
            <div class="d-flex flex-column gap-4">
                <!-- Quick Actions -->
                <div class="glass-card fade-in-up delay-4">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-lightning-charge"></i>
                            Quick Actions
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="admin_users.php" class="action-btn" style="border-color: var(--accent-yellow); background: rgba(245, 158, 11, 0.05);">
                                <i class="bi bi-people-fill" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(249, 115, 22, 0.2)); color: var(--accent-yellow);"></i>
                                <span>Manage Users & Admins</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="index.php" class="action-btn">
                                <i class="bi bi-house-door"></i>
                                <span>View Homepage</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="players.php" class="action-btn">
                                <i class="bi bi-people"></i>
                                <span>Manage Players</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="fixtures.php" class="action-btn">
                                <i class="bi bi-calendar-event"></i>
                                <span>View Fixtures</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="live-score.php" class="action-btn">
                                <i class="bi bi-broadcast"></i>
                                <span>Live Scores</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="ai-team-improver.php" class="action-btn">
                                <i class="bi bi-robot"></i>
                                <span>AI Team Improver</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Server Status -->
                <div class="glass-card fade-in-up delay-5">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-server"></i>
                            Server Status
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="status-grid">
                            <div class="status-item">
                                <div class="status-info">
                                    <div class="status-icon green">
                                        <i class="bi bi-database-fill"></i>
                                    </div>
                                    <span class="status-label">Database</span>
                                </div>
                                <span class="status-badge online">Online</span>
                            </div>
                            <div class="status-item">
                                <div class="status-info">
                                    <div class="status-icon blue">
                                        <i class="bi bi-globe"></i>
                                    </div>
                                    <span class="status-label">FPL API</span>
                                </div>
                                <span class="status-badge active">Active</span>
                            </div>
                            <div class="status-item">
                                <div class="status-info">
                                    <div class="status-icon purple">
                                        <i class="bi bi-cpu-fill"></i>
                                    </div>
                                    <span class="status-label">Server</span>
                                </div>
                                <span class="status-badge online">Running</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        <p class="mb-0">
            <i class="bi bi-shield-lock me-1"></i>
            FPL Manager Admin Panel &copy; <?= date('Y') ?> | Built with <i class="bi bi-heart-fill text-danger"></i>
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('liveClock').textContent = now.toLocaleDateString('en-US', options);
            document.getElementById('liveTime').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(updateClock, 1000);

        // Count Up Animation
        function animateCount(element) {
            const target = parseInt(element.getAttribute('data-count'));
            if (isNaN(target)) return;
            
            const duration = 2000;
            const frameDuration = 1000 / 60;
            const totalFrames = Math.round(duration / frameDuration);
            let frame = 0;
            
            const counter = setInterval(() => {
                frame++;
                const progress = frame / totalFrames;
                const easeOutQuad = 1 - Math.pow(1 - progress, 3);
                const currentCount = Math.round(target * easeOutQuad);
                element.textContent = currentCount.toLocaleString();
                
                if (frame === totalFrames) {
                    clearInterval(counter);
                    element.textContent = target.toLocaleString();
                }
            }, frameDuration);
        }

        // Trigger count animation when in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const countElements = entry.target.querySelectorAll('[data-count]');
                    countElements.forEach(el => animateCount(el));
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stats-grid').forEach(grid => observer.observe(grid));

        // Registration Chart
        const ctx = document.getElementById('registrationChart').getContext('2d');
        const chartLabels = <?= json_encode($chart_labels) ?>;
        const chartData = <?= json_encode($chart_values) ?>;

        // If no data, show placeholder
        const displayLabels = chartLabels.length > 0 ? chartLabels : ['No Data'];
        const displayData = chartData.length > 0 ? chartData : [0];

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(139, 92, 246, 0.5)');
        gradient.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: [{
                    label: 'New Users',
                    data: displayData,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#8b5cf6',
                    borderWidth: 3,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 30, 50, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(139, 92, 246, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 10,
                        padding: 12
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            font: {
                                family: 'Inter'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            font: {
                                family: 'Inter'
                            },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
