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
                    <!-- View Toggle -->
                    <div class="btn-group mb-3 w-100" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="tableViewBtn">
                            <i class="bi bi-table me-1"></i>Table View
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="pitchViewBtn">
                            <i class="bi bi-grid-3x3 me-1"></i>Pitch View
                        </button>
                    </div>

                    <!-- Team Value Card -->
                    <div class="card bg-dark text-white mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center py-2">
                            <span><i class="bi bi-wallet2 me-2"></i>Squad Value</span>
                            <span class="fw-bold fs-5" id="squadValue">£0.0m</span>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div id="tableView" class="card shadow-sm">
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

                    <!-- Pitch View -->
                    <div id="pitchView" class="d-none">
                        <div class="pitch-container" id="pitchContainer">
                            <!-- Players on pitch -->
                        </div>
                    </div>
                </div>
                
                <div id="errorAlert" class="alert alert-danger d-none mt-3" role="alert"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .pitch-container {
        background: url('f_logo/football_pitch.svg') center center;
        background-size: cover;
        border-radius: 12px;
        min-height: 450px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .pitch-row {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .player-chip {
        background: rgba(255,255,255,0.95);
        border-radius: 10px;
        padding: 8px 12px;
        text-align: center;
        min-width: 80px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
        position: relative;
        transition: transform 0.2s;
    }
    .player-chip:hover {
        transform: scale(1.1);
        z-index: 10;
    }
    .player-chip .name {
        font-size: 0.75rem;
        font-weight: 700;
        color: #1a1a2e;
    }
    .player-chip .price {
        font-size: 0.65rem;
        color: #10b981;
        font-weight: 600;
    }
    .player-chip .captain-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ffc107;
        color: #000;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    .bench-section {
        background: rgba(0,0,0,0.3);
        border-radius: 8px;
        padding: 10px;
        margin-top: 15px;
    }
    .bench-section .title {
        color: white;
        font-size: 0.7rem;
        text-transform: uppercase;
        margin-bottom: 8px;
        opacity: 0.7;
    }
</style>

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

    // Team Logo Helper
    function getTeamLogo(teamName) {
        if(!teamName) return null;
        const name = teamName.toLowerCase();
        const map = {
            'arsenal': 'arsenal.svg',
            'aston villa': 'aston villa.svg',
            'bournemouth': 'boumemouth.svg',
            'brentford': 'brentford.svg',
            'brighton': 'brighton.svg',
            'burnley': 'burnley.svg',
            'chelsea': 'chelsea.svg',
            'crystal palace': 'crystal palace.svg',
            'everton': 'everton.svg',
            'fulham': 'fulham.svg',
            'liverpool': 'liverpool.svg',
            'man city': 'man city.svg',
            'man utd': 'man utd.svg',
            'newcastle': 'newcastle.svg',
            "nott'm forest": 'forest.svg',
            'sheffield utd': 'sunderland.svg',
            'spurs': 'spurs.svg',
            'tottenham': 'spurs.svg',
            'luton': 'sunderland.svg',
            'west ham': 'west ham.svg',
            'wolves': 'wolves.svg',
            'leicester': 'leicester.png',
            'southampton': 'southampton.png',
            'ipswich': 'ipswich.png'
        };
        return map[name] ? 'f_logo/' + map[name] : null;
    }

    function getTeamLogoHtml(team, size = 20) {
        const logoPath = getTeamLogo(team?.name);
        if (logoPath) {
            return `<img src="${logoPath}" alt="${team?.name}" style="height: ${size}px; width: ${size}px; object-fit: contain;" class="me-1">`;
        }
        return '';
    }

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
        let totalValue = 0;
        
        // Group players by position for pitch view
        const grouped = { 1: [], 2: [], 3: [], 4: [], bench: [] };
        
        picks.forEach((pick, index) => {
            const player = staticData.elements.find(e => e.id === pick.element);
            const team = staticData.teams.find(t => t.id === player.team);
            const type = staticData.element_types.find(t => t.id === player.element_type);
            
            totalValue += player.now_cost;

            // Table row
            const row = document.createElement('tr');
            let roleBadge = '';
            if (pick.is_captain) roleBadge = '<span class="badge bg-warning text-dark">C</span>';
            else if (pick.is_vice_captain) roleBadge = '<span class="badge bg-secondary text-white">VC</span>';

            row.innerHTML = `
                <td class="ps-4"><span class="badge bg-light text-dark border">${type.singular_name_short}</span></td>
                <td><div class="fw-bold text-dark">${player.web_name}</div></td>
                <td><span class="d-flex align-items-center gap-1">${getTeamLogoHtml(team)}${team.short_name}</span></td>
                <td>${roleBadge}</td>
                <td class="text-end pe-4 fw-bold">£${(player.now_cost / 10).toFixed(1)}</td>
            `;
            teamTableBody.appendChild(row);
            
            // Group for pitch view (first 11 are starters)
            const playerData = { player, pick, team };
            if (index < 11) {
                grouped[player.element_type].push(playerData);
            } else {
                grouped.bench.push(playerData);
            }
        });

        // Update squad value
        document.getElementById('squadValue').textContent = `£${(totalValue / 10).toFixed(1)}m`;
        
        // Render pitch view
        renderPitchView(grouped);

        teamContainer.classList.remove('d-none');
    }
    
    function renderPitchView(grouped) {
        const pitchContainer = document.getElementById('pitchContainer');
        const posColors = { 1: '#0d6efd', 2: '#198754', 3: '#fd7e14', 4: '#dc3545' };
        
        // Order: FWD, MID, DEF, GK (top to bottom)
        const rows = [grouped[4], grouped[3], grouped[2], grouped[1]];
        
        let html = rows.map((row, idx) => {
            if (!row || row.length === 0) return '';
            const posType = [4, 3, 2, 1][idx];
            return `
                <div class="pitch-row">
                    ${row.map(p => `
                        <div class="player-chip" style="border-top: 4px solid ${posColors[posType]};">
                            ${p.pick.is_captain ? '<div class="captain-badge">C</div>' : ''}
                            ${p.pick.is_vice_captain ? '<div class="captain-badge" style="background:#6c757d;color:#fff;">V</div>' : ''}
                            <div class="name">${p.player.web_name}</div>
                            <div class="price">£${(p.player.now_cost / 10).toFixed(1)}m</div>
                        </div>
                    `).join('')}
                </div>
            `;
        }).join('');
        
        // Add bench
        html += `
            <div class="bench-section">
                <div class="title text-center">Bench</div>
                <div class="pitch-row">
                    ${grouped.bench.map(p => `
                        <div class="player-chip" style="opacity: 0.8;">
                            <div class="name">${p.player.web_name}</div>
                            <div class="price">£${(p.player.now_cost / 10).toFixed(1)}m</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        pitchContainer.innerHTML = html;
    }
    
    // View toggle
    document.getElementById('tableViewBtn').addEventListener('click', () => {
        document.getElementById('tableView').classList.remove('d-none');
        document.getElementById('pitchView').classList.add('d-none');
        document.getElementById('tableViewBtn').classList.add('active');
        document.getElementById('pitchViewBtn').classList.remove('active');
    });
    
    document.getElementById('pitchViewBtn').addEventListener('click', () => {
        document.getElementById('tableView').classList.add('d-none');
        document.getElementById('pitchView').classList.remove('d-none');
        document.getElementById('pitchViewBtn').classList.add('active');
        document.getElementById('tableViewBtn').classList.remove('active');
    });

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
