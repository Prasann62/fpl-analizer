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
    <title>Live Rank | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Hero Header -->
        <div class="hero-header mb-5 text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="z-1">
                <h1 class="display-5 fw-bold mb-2">Live Rank</h1>
                <p class="lead opacity-75 mb-0">Real-time gameweek performance tracking.</p>
            </div>
            <div class="mt-4 mt-md-0 z-1">
                <i class="bi bi-bar-chart-line-fill display-1 opacity-25"></i>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Input Section -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Track Manager</h5>
                        <div class="input-group input-group-lg">
                            <input type="number" id="managerIdInput" class="form-control" placeholder="Enter Manager ID" aria-label="Manager ID">
                            <button class="btn btn-primary" type="button" id="fetchRankBtn">
                                <i class="bi bi-speedometer2 me-2"></i>Track Live
                            </button>
                        </div>
                    </div>
                </div>

                <div id="loadingSpinner" class="text-center d-none py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Live Content Wrapper -->
                <div id="rankContainer" class="d-none position-relative">
                    
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" class="d-none position-absolute top-50 start-50 translate-middle badge bg-dark bg-opacity-75 p-3 shadow-lg z-3 border border-secondary">
                        <span class="spinner-border spinner-border-sm me-2"></span> Updating Rank...
                    </div>

                    <!-- Live Stats Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <h6 class="opacity-75 text-uppercase small fw-bold">Live Points</h6>
                                    <h2 class="display-3 fw-extrabold mb-0" id="livePoints">-</h2>
                                    <small class="opacity-75">Gameweek Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-white h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <h6 class="text-muted text-uppercase small fw-bold">Overall Rank</h6>
                                    <h2 class="display-4 fw-bold mb-0 text-dark" id="overallRank">-</h2>
                                    <small class="text-muted" id="rankMovement"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Players Table -->
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-primary">Live Player Points</h5>
                            <span class="badge bg-danger animate-pulse">LIVE</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Player</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Points</th>
                                        </tr>
                                    </thead>
                                    <tbody id="livePlayersBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="errorAlert" class="alert alert-danger d-none mt-3" role="alert"></div>
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
document.addEventListener('DOMContentLoaded', () => {
    const managerIdInput = document.getElementById('managerIdInput');
    const fetchRankBtn = document.getElementById('fetchRankBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const rankContainer = document.getElementById('rankContainer');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const livePointsEl = document.getElementById('livePoints');
    const overallRankEl = document.getElementById('overallRank');
    const livePlayersBody = document.getElementById('livePlayersBody');
    const errorAlert = document.getElementById('errorAlert');

    let staticData = null;

    async function fetchStaticData() {
        try {
            const response = await fetch('api.php?endpoint=bootstrap-static/');
            if (!response.ok) throw new Error('Failed to fetch static data');
            staticData = await response.json();
        } catch (error) {
            console.error('Error fetching static data:', error);
            showError('Failed to load FPL data. Please try again later.');
        }
    }

    fetchStaticData();

    fetchRankBtn.addEventListener('click', () => {
        const managerId = managerIdInput.value.trim();
        if (managerId) {
            fetchLiveRank(managerId);
        }
    });

    async function fetchLiveRank(managerId) {
        if (!staticData) {
            showError('System is still initializing. Please wait a moment.');
            return;
        }

        showLoading(true);
        hideError();
        
        // Don't hide container, use overlay if already waiting
        // rankContainer.classList.add('d-none'); 

        try {
            // 1. Get current gameweek
            const currentEvent = staticData.events.find(e => e.is_current) || staticData.events[0];
            const gw = currentEvent.id;

            // 2. Fetch Manager Info (for Rank)
            const managerRes = await fetch(`api.php?endpoint=entry/${managerId}/`);
            if (!managerRes.ok) throw new Error('Manager not found.');
            const managerData = await managerRes.json();

            // 3. Fetch Picks (for Live Points calculation)
            const picksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${gw}/picks/`);
            if (!picksRes.ok) throw new Error('Gameweek picks not found.');
            const picksData = await picksRes.json();

            // 4. Fetch Live Stats (using event/{gw}/live/)
            const liveRes = await fetch(`api.php?endpoint=event/${gw}/live/`);
            if (!liveRes.ok) throw new Error('Live data not available.');
            const liveData = await liveRes.json();
            
            // Map live elements
            const liveElements = {};
            liveData.elements.forEach(el => {
                liveElements[el.id] = el.stats;
            });

            renderLiveStats(managerData, picksData.picks, liveElements);
            
            // Show container if hidden (first load)
            rankContainer.classList.remove('d-none');

        } catch (error) {
            console.error(error);
            showError(error.message);
        } finally {
            showLoading(false);
        }
    }

    function renderLiveStats(manager, picks, liveStats) {
        // ... (render logic unchanged) ...
        // Update Rank
        overallRankEl.textContent = manager.summary_overall_rank ? manager.summary_overall_rank.toLocaleString() : '-';
        
        // Calculate Live Points
        let totalPoints = 0;
        livePlayersBody.innerHTML = '';

        picks.forEach(pick => {
            const player = staticData.elements.find(e => e.id === pick.element);
            const stats = liveStats[pick.element];
            const points = stats ? stats.total_points : 0;
            
            // Multiplier logic
            const multiplier = pick.multiplier;
            const finalPoints = points * multiplier;
            
            if (multiplier > 0) { // Only count active players
                totalPoints += finalPoints;
            }

            let roleBadge = '';
            if (pick.is_captain) roleBadge = '<span class="badge bg-warning text-dark ms-2">C</span>';
            else if (pick.is_vice_captain) roleBadge = '<span class="badge bg-secondary text-white ms-2">V</span>';
            
            // Bench logic
            const isBench = multiplier === 0;
            const rowClass = isBench ? 'table-light opacity-75' : '';

            const row = `
                <tr class="${rowClass}">
                    <td class="ps-4">
                        <div class="fw-bold text-dark">${player.web_name}</div>
                        ${roleBadge}
                        ${isBench ? '<span class="badge bg-secondary ms-2">Bench</span>' : ''}
                    </td>
                    <td>
                        ${stats && stats.minutes === 0 ? '<span class="text-muted small">Yet to play</span>' : '<span class="text-success small fw-bold">Playing</span>'}
                    </td>
                    <td class="text-end pe-4 fw-bold h5 mb-0">${finalPoints}</td>
                </tr>
            `;
            livePlayersBody.innerHTML += row;
        });

        livePointsEl.textContent = totalPoints - (picks.entry_history ? picks.entry_history.event_transfers_cost : 0); 
    }

    function showLoading(show) {
        if (show) {
            const isFirst = rankContainer.classList.contains('d-none');
            if(isFirst) {
                 loadingSpinner.classList.remove('d-none');
            } else {
                 if(loadingOverlay) loadingOverlay.classList.remove('d-none');
            }
            fetchRankBtn.disabled = true;
            fetchRankBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        } else {
            loadingSpinner.classList.add('d-none');
            if(loadingOverlay) loadingOverlay.classList.add('d-none');
            fetchRankBtn.disabled = false;
            fetchRankBtn.innerHTML = '<i class="bi bi-speedometer2 me-2"></i>Track Live';
        }
    }

    function showError(msg) {
        errorAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${msg}`;
        errorAlert.classList.remove('d-none');
    }

    function hideError() {
        errorAlert.classList.add('d-none');
    }
});
</script>

<style>
.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
</body>
</html>
