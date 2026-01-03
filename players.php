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
    <title>Players | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Hero Header -->
        <div class="hero-header mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">Player Database</h1>
                    <p class="lead opacity-75 mb-0">Scout the best talents for your team.</p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="bi bi-people-fill display-1 opacity-25"></i>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted fw-bold text-uppercase">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchBox" class="form-control border-start-0 ps-0" placeholder="Find player by name...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted fw-bold text-uppercase">Position</label>
                        <select id="positionFilter" class="form-select">
                            <option value="">All Positions</option>
                            <option value="1">Goalkeeper</option>
                            <option value="2">Defender</option>
                            <option value="3">Midfielder</option>
                            <option value="4">Forward</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted fw-bold text-uppercase">Team</label>
                        <select id="teamFilter" class="form-select">
                            <option value="">All Teams</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Player Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">Statistics</h5>
                <small class="text-muted"><i class="bi bi-arrow-down-up me-1"></i>Click headers to sort</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Pos</th>
                                <th>Team</th>
                                <th class="sortable" data-sort="now_cost" style="cursor:pointer;">Price <i class="bi bi-arrow-down-up text-muted"></i></th>
                                <th class="sortable text-center" data-sort="form" style="cursor:pointer;">Form <i class="bi bi-arrow-down-up text-muted"></i></th>
                                <th class="sortable text-center" data-sort="total_points" style="cursor:pointer;">Pts <i class="bi bi-arrow-down-up text-muted"></i></th>
                                <th class="sortable text-center" data-sort="expected_goals" style="cursor:pointer;" title="Expected Goals">xG <i class="bi bi-arrow-down-up text-muted"></i></th>
                                <th class="sortable text-center" data-sort="expected_assists" style="cursor:pointer;" title="Expected Assists">xA <i class="bi bi-arrow-down-up text-muted"></i></th>
                                <th class="sortable text-center" data-sort="ict_index" style="cursor:pointer;" title="ICT Index">ICT <i class="bi bi-arrow-down-up text-muted"></i></th>
                                <th class="text-center">Next 5</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody id="playersTableBody">
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
    const searchBox = document.getElementById('searchBox');
    const positionFilter = document.getElementById('positionFilter');
    const teamFilter = document.getElementById('teamFilter');
    const tbody = document.getElementById('playersTableBody');

    let allPlayers = [];
    let teamsMap = {};
    let positionsMap = {1: 'GKP', 2: 'DEF', 3: 'MID', 4: 'FWD'};

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

    async function init() {
        try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            const data = await res.json();
            staticData = data; // Store globally

            // Load fixtures for FDR display
            await loadFixtures();

            // Map teams
            data.teams.forEach(t => {
                teamsMap[t.id] = t;
                const option = document.createElement('option');
                option.value = t.id;
                option.textContent = t.name;
                teamFilter.appendChild(option);
            });

            allPlayers = data.elements;
            renderPlayers(allPlayers.slice(0, 50)); // Initial render top 50

            // Event Listeners
            searchBox.addEventListener('input', filterPlayers);
            positionFilter.addEventListener('change', filterPlayers);
            teamFilter.addEventListener('change', filterPlayers);

        } catch (error) {
            console.error(error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-5 text-danger">
                        <i class="bi bi-x-circle display-4 d-block mb-3"></i>
                        Failed to load players
                    </td>
                </tr>
            `;
        }
    }

    let currentSort = { key: 'total_points', dir: 'desc' };

    function filterPlayers() {
        const searchTerm = searchBox.value.toLowerCase();
        const position = positionFilter.value;
        const team = teamFilter.value;

        let filtered = allPlayers.filter(p => {
            const matchesSearch = (p.first_name + ' ' + p.second_name).toLowerCase().includes(searchTerm);
            const matchesPosition = position ? p.element_type == position : true;
            const matchesTeam = team ? p.team == team : true;
            return matchesSearch && matchesPosition && matchesTeam;
        });

        // Apply sorting
        filtered.sort((a, b) => {
            let valA = parseFloat(a[currentSort.key]) || 0;
            let valB = parseFloat(b[currentSort.key]) || 0;
            return currentSort.dir === 'desc' ? valB - valA : valA - valB;
        });

        // Limit to 50 for performance unless searching
        const limit = searchTerm ? 100 : 50;
        renderPlayers(filtered.slice(0, limit));
    }

    // Sortable columns
    document.querySelectorAll('.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const sortKey = th.dataset.sort;
            if (currentSort.key === sortKey) {
                currentSort.dir = currentSort.dir === 'desc' ? 'asc' : 'desc';
            } else {
                currentSort.key = sortKey;
                currentSort.dir = 'desc';
            }
            // Update icons
            document.querySelectorAll('.sortable i').forEach(icon => {
                icon.className = 'bi bi-arrow-down-up text-muted';
            });
            const icon = th.querySelector('i');
            icon.className = currentSort.dir === 'desc' ? 'bi bi-arrow-down text-primary' : 'bi bi-arrow-up text-primary';
            filterPlayers();
        });
    });

    function renderPlayers(players) {
        tbody.innerHTML = '';
        if (players.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-5 text-muted">
                        No players found matching your criteria
                    </td>
                </tr>
            `;
            return;
        }

        players.forEach(p => {
            const team = teamsMap[p.team];
            const position = positionsMap[p.element_type];
            
            // Status Logic
            let statusIcon = 'bi-check-circle-fill text-success';
            let statusTitle = 'Available';
            
            if (p.status === 'd') {
                statusIcon = 'bi-exclamation-circle-fill text-warning';
                statusTitle = p.news || 'Doubtful';
            } else if (p.status === 'i' || p.status === 'u') {
                statusIcon = 'bi-x-circle-fill text-danger';
                statusTitle = p.news || 'Unavailable';
            }

            // xG and xA 
            const xG = parseFloat(p.expected_goals) || 0;
            const xA = parseFloat(p.expected_assists) || 0;
            const ict = parseFloat(p.ict_index) || 0;

            // Get next 5 fixtures FDR
            const next5Fixtures = getNext5Fixtures(p.team);

            const row = `
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">${p.first_name} ${p.second_name}</div>
                        <small class="text-muted">${p.selected_by_percent}% owned</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">${position}</span></td>
                    <td><span class="d-flex align-items-center gap-1">${getTeamLogoHtml(team)}${team.short_name}</span></td>
                    <td class="fw-bold">£${(p.now_cost / 10).toFixed(1)}m</td>
                    <td class="text-center">
                        <span class="fw-bold ${parseFloat(p.form) > 5 ? 'text-success' : ''}">${p.form}</span>
                    </td>
                    <td class="text-center fw-bold">${p.total_points}</td>
                    <td class="text-center ${xG > 3 ? 'text-success fw-bold' : ''}">${xG.toFixed(1)}</td>
                    <td class="text-center ${xA > 2 ? 'text-primary fw-bold' : ''}">${xA.toFixed(1)}</td>
                    <td class="text-center ${ict > 100 ? 'text-warning fw-bold' : ''}">${ict.toFixed(1)}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            ${next5Fixtures}
                        </div>
                    </td>
                    <td class="text-center pe-4">
                        <i class="bi ${statusIcon}" data-bs-toggle="tooltip" title="${statusTitle}"></i>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    }

    // FDR color helper
    function getFDRColor(fdr) {
        switch(fdr) {
            case 1: return '#00ff85'; // Very easy - bright green
            case 2: return '#01fc7a'; // Easy - green
            case 3: return '#e7e7e7'; // Medium - grey
            case 4: return '#ff6961'; // Hard - light red
            case 5: return '#dc3545'; // Very hard - red
            default: return '#6c757d';
        }
    }

    function getFDRTextColor(fdr) {
        return fdr <= 2 ? '#000' : (fdr >= 4 ? '#fff' : '#000');
    }

    // Get next 5 fixtures for a team
    let fixturesData = [];
    
    async function loadFixtures() {
        try {
            const res = await fetch('api.php?endpoint=fixtures/');
            fixturesData = await res.json();
        } catch(e) {
            console.error('Failed to load fixtures');
        }
    }

    function getNext5Fixtures(teamId) {
        const currentGW = staticData?.events?.find(e => e.is_current || e.is_next)?.id || 1;
        
        const teamFixtures = fixturesData
            .filter(f => (f.team_h === teamId || f.team_a === teamId) && f.event >= currentGW)
            .slice(0, 5);

        if(teamFixtures.length === 0) {
            return '<span class="badge bg-secondary">-</span>';
        }

        return teamFixtures.map(f => {
            const isHome = f.team_h === teamId;
            const oppTeamId = isHome ? f.team_a : f.team_h;
            const oppTeam = teamsMap[oppTeamId];
            const fdr = isHome ? f.team_h_difficulty : f.team_a_difficulty;
            
            return `<span class="badge" style="background:${getFDRColor(fdr)};color:${getFDRTextColor(fdr)};font-size:9px;min-width:28px;" 
                          title="GW${f.event}: ${oppTeam?.name || 'TBD'} (${isHome ? 'H' : 'A'})"
                          data-bs-toggle="tooltip">${oppTeam?.short_name || '?'}${isHome ? '' : ''}</span>`;
        }).join('');
    }

    // Store staticData globally
    let staticData = null;
    
    async function loadStaticData() {
        const res = await fetch('api.php?endpoint=bootstrap-static/');
        staticData = await res.json();
        return staticData;
    }

    init();
</script>
</body>
</html>