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
    <title>Team | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Hero Header -->
        <div class="hero-header text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="z-1">
                <h1 class="display-5 fw-bold mb-2">Team Analyzer</h1>
                <p class="lead opacity-75 mb-0">Deep dive into any manager's selection.</p>
            </div>
            <div class="mt-4 mt-md-0 z-1">
                <i class="bi bi-graph-up-arrow display-1 opacity-25"></i>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Analyze Manager</h5>
                        <div class="input-group input-group-lg">
                            <input type="number" id="managerIdInput" class="form-control" placeholder="Enter Manager ID" aria-label="Manager ID">
                            <button class="btn btn-primary" type="button" id="fetchTeamBtn">
                                <i class="bi bi-search me-2"></i>Get Team
                            </button>
                        </div>
                    </div>
                </div>

                <div id="loadingSpinner" class="text-center d-none py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div id="teamContainer" class="d-none">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-primary" id="teamNameHeader">Team Picks</h5>
                            <span class="badge bg-primary text-dark">Active Squad</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Pos</th>
                                            <th>Player</th>
                                            <th>Team</th>
                                            <th>Role</th>
                                            <th class="text-end pe-4">Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody id="teamTableBody">
                                        <!-- Players will be inserted here -->
                                    </tbody>
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
    const fetchTeamBtn = document.getElementById('fetchTeamBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const teamContainer = document.getElementById('teamContainer');
    const teamTableBody = document.getElementById('teamTableBody');
    const errorAlert = document.getElementById('errorAlert');
    const teamNameHeader = document.getElementById('teamNameHeader');

    let staticData = null;

    // Fetch bootstrap-static data on load
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

    fetchStaticData().then(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const urlManagerId = urlParams.get('id');
        if (urlManagerId) {
            managerIdInput.value = urlManagerId;
            fetchManagerTeam(urlManagerId);
        }
    });

    fetchTeamBtn.addEventListener('click', () => {
        const managerId = managerIdInput.value.trim();
        if (managerId) {
            fetchManagerTeam(managerId);
        }
    });

    async function fetchManagerTeam(managerId) {
        if (!staticData) {
            showError('System is still initializing. Please wait a moment.');
            return;
        }

        showLoading(true);
        hideError();
        teamContainer.classList.add('d-none');

        try {
            // 1. Get current gameweek
            const currentEvent = staticData.events.find(e => e.is_current);
            if (!currentEvent) {
                throw new Error('No current gameweek found.');
            }
            const gw = currentEvent.id;

            // 2. Fetch Manager Picks
            const response = await fetch(`api.php?endpoint=entry/${managerId}/event/${gw}/picks/`);
            if (!response.ok) {
                if (response.status === 404) throw new Error('Manager or Gameweek data not found.');
                throw new Error('Failed to fetch team picks.');
            }
            const data = await response.json();

            renderTeam(data.picks);
        } catch (error) {
            console.error(error);
            showError(error.message);
        } finally {
            showLoading(false);
        }
    }

    function renderTeam(picks) {
        teamTableBody.innerHTML = '';
        
        picks.forEach(pick => {
            const player = staticData.elements.find(e => e.id === pick.element);
            const team = staticData.teams.find(t => t.id === player.team);
            const type = staticData.element_types.find(t => t.id === player.element_type);

            const row = document.createElement('tr');
            
            // Highlight captain/vice-captain
            let roleBadge = '';
            if (pick.is_captain) roleBadge = '<span class="badge bg-warning text-dark">C</span>';
            else if (pick.is_vice_captain) roleBadge = '<span class="badge bg-secondary text-white">VC</span>';

            row.innerHTML = `
                <td class="ps-4"><span class="badge bg-light text-dark border">${type.singular_name_short}</span></td>
                <td>
                    <div class="fw-bold text-dark">${player.web_name}</div>
                </td>
                <td>${team.short_name}</td>
                <td>${roleBadge}</td>
                <td class="text-end pe-4 fw-bold">£${(player.now_cost / 10).toFixed(1)}</td>
            `;
            teamTableBody.appendChild(row);
        });

        teamContainer.classList.remove('d-none');
    }

    function showLoading(show) {
        if (show) {
            loadingSpinner.classList.remove('d-none');
            fetchTeamBtn.disabled = true;
            fetchTeamBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        } else {
            loadingSpinner.classList.add('d-none');
            fetchTeamBtn.disabled = false;
            fetchTeamBtn.innerHTML = '<i class="bi bi-search me-2"></i>Get Team';
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
</body>
</html>