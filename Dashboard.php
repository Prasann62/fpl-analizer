<?php
session_start();
if(!isset($_SESSION['access'])){
  header('location:loginform.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'favicon-meta.php'; ?>
    <title>Dashboard | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Hero Header -->
        <div class="hero-header text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="z-1">
                <h1 class="display-5 fw-bold mb-2">My Team</h1>
                <p class="lead opacity-75 mb-0">Track your squad's performance in real-time.</p>
            </div>
            <div class="mt-4 mt-md-0 z-1">
                <i class="bi bi-shield-shaded display-1 opacity-25"></i>
            </div>
        </div>

        <div class="row g-4">
            <!-- Input Section -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-4">Load Manager</h5>
                        <div class="mb-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Manager ID</label>
                            <input id="managerId" type="number" class="form-control form-control-lg" placeholder="e.g. 1234567">
                        </div>
                        <button id="loadBtn" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-search me-2"></i>Load Team
                        </button>
                        
                        <div id="managerInfo" class="mt-4"></div>
                    </div>
                </div>
            </div>

            <!-- Team Table Section -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">Current Squad</h5>
                        <span class="badge bg-primary text-dark">Gameweek Live</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Player</th>
                                        <th>Pos</th>
                                        <th>Team</th>
                                        <th class="text-center">Form</th>
                                        <th class="text-end pe-4">Points</th>
                                    </tr>
                                </thead>
                                <tbody id="teamTable">
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-people display-4 d-block mb-3 opacity-25"></i>
                                            Enter a Manager ID to view the squad
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
<script>
    const loadBtn = document.getElementById('loadBtn');
    const managerIdInput = document.getElementById('managerId');
    const managerInfo = document.getElementById('managerInfo');
    const teamTable = document.getElementById('teamTable');

    loadBtn.addEventListener('click', async () => {
        const managerId = managerIdInput.value.trim();
        if (!managerId) {
            alert('Please enter a Manager ID');
            return;
        }

        const originalBtnText = loadBtn.innerHTML;
        loadBtn.disabled = true;
        loadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        
        try {
            // 1. Fetch Bootstrap Static (Events & Elements)
            const bootstrapRes = await fetch('api.php?endpoint=bootstrap-static/');
            const bootstrapData = await bootstrapRes.json();
            
            // Find current gameweek
            const currentEvent = bootstrapData.events.find(e => e.is_current) || bootstrapData.events[0];
            const gw = currentEvent.id;

            // Map players
            const players = {};
            bootstrapData.elements.forEach(p => {
                players[p.id] = p;
            });
            
            // Map teams
            const teams = {};
            bootstrapData.teams.forEach(t => {
                teams[t.id] = t;
            });

            // Map positions
            const positions = {1: 'GKP', 2: 'DEF', 3: 'MID', 4: 'FWD'};

            // 2. Fetch Manager Info
            const managerRes = await fetch(`api.php?endpoint=entry/${managerId}/`);
            if (!managerRes.ok) throw new Error('Manager not found');
            const managerData = await managerRes.json();

            managerInfo.innerHTML = `
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mt-4">
                    <h6 class="fw-bold text-gray-800 mb-1 fs-5">${managerData.player_first_name} ${managerData.player_last_name}</h6>
                    <div class="text-gray-500 small mb-3">${managerData.name}</div>
                    <div class="d-flex justify-content-start gap-3 align-items-center">
                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 px-3 py-2 rounded-md">GW${gw}: ${managerData.summary_event_points}</span>
                        <span class="badge bg-gray-100 text-gray-700 border border-gray-200 px-3 py-2 rounded-md">Total: ${managerData.summary_overall_points}</span>
                    </div>
                </div>
            `;

            // 3. Fetch Team Picks
            const picksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${gw}/picks/`);
            if (!picksRes.ok) throw new Error('Gameweek data not found');
            const picksData = await picksRes.json();

            // 4. Render Table
            teamTable.innerHTML = '';
            picksData.picks.forEach((pick, index) => {
                const player = players[pick.element];
                const team = teams[player.team];
                const position = positions[player.element_type];
                
                let roleBadge = '';
                if (pick.is_captain) roleBadge = '<span class="badge bg-warning text-dark ms-2">C</span>';
                else if (pick.is_vice_captain) roleBadge = '<span class="badge bg-secondary text-white ms-2">V</span>';

                const row = `
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">${player.web_name}</div>
                            ${roleBadge}
                        </td>
                        <td><span class="badge bg-light text-dark border">${position}</span></td>
                        <td>${team.short_name}</td>
                        <td class="text-center">
                            <span class="fw-bold ${parseFloat(player.form) > 5 ? 'text-success' : ''}">${player.form}</span>
                        </td>
                        <td class="text-end pe-4 fw-bold">${player.event_points}</td>
                    </tr>
                `;
                teamTable.innerHTML += row;
            });

        } catch (error) {
            console.error(error);
            managerInfo.innerHTML = `
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>${error.message}</div>
                </div>
            `;
            teamTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5 text-danger">
                        <i class="bi bi-x-circle display-4 d-block mb-3"></i>
                        Failed to load data
                    </td>
                </tr>
            `;
        } finally {
            loadBtn.disabled = false;
            loadBtn.innerHTML = originalBtnText;
        }
    });
</script>
</body>
</html>