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
    <title>Fixtures | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .fdr-table {
            font-size: 0.8rem;
        }
        .fdr-cell {
            text-align: center;
            color: white;
            font-weight: bold;
            padding: 4px;
            border-radius: 4px;
        }
        .fdr-1 { background-color: #375523; }
        .fdr-2 { background-color: #01fc7a; color: black; }
        .fdr-3 { background-color: #e7e7e7; color: black; }
        .fdr-4 { background-color: #ff1751; }
        .fdr-5 { background-color: #80072d; }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Hero Header -->
        <div class="hero-header mb-5 text-center">
            <h1 class="display-5 fw-bold mb-2">Match Centre</h1>
            <p class="lead opacity-75 mb-0">Upcoming fixtures and schedule.</p>
        </div>

        <!-- FDR Ticker Section -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                <span>Fixture Difficulty Ticker (Next 5 GWs)</span>
                <span class="badge bg-secondary">FDR</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 fdr-table">
                        <thead>
                            <tr>
                                <th class="ps-3">Team</th>
                                <th class="text-center" id="fdr-gw-1">GW-</th>
                                <th class="text-center" id="fdr-gw-2">GW-</th>
                                <th class="text-center" id="fdr-gw-3">GW-</th>
                                <th class="text-center" id="fdr-gw-4">GW-</th>
                                <th class="text-center" id="fdr-gw-5">GW-</th>
                            </tr>
                        </thead>
                        <tbody id="fdrTableBody">
                            <tr><td colspan="6" class="text-center py-3"><span class="spinner-border spinner-border-sm"></span> Loading Ticker...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="controlPanel" class="row mb-4 justify-content-center">
            <div class="col-md-6 text-center">
                 <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white fw-bold">Gameweek</span>
                    <select class="form-select text-center fw-bold" id="gwSelect">
                         <option value="" selected>Auto (Smart)</option>
                         <!-- Options populated by JS -->
                    </select>
                    <button class="btn btn-primary" id="loadBtn">
                        <i class="bi bi-arrow-clockwise me-1"></i> Load
                    </button>
                 </div>
            </div>
        </div>

        <div id="fixtures" class="row g-4">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
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
  
<!-- Lineups Modal -->
<div class="modal fade" id="lineupsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title fw-bold">Match Lineups</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="lineupsModalBody">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    const fixturesContainer = document.getElementById('fixtures');
    const gwSelect = document.getElementById('gwSelect');
    const loadBtn = document.getElementById('loadBtn');
    
    let staticData = null; // Store static data globally
    const lineupsModal = new bootstrap.Modal(document.getElementById('lineupsModal'));
    const lineupsModalBody = document.getElementById('lineupsModalBody');

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
            'newcastle': null,
            "nott'm forest": 'forest.svg',
            'sheffield utd': null,
            'spurs': 'spurs.svg',
            'tottenham': 'spurs.svg',
            'luton': null,
            'west ham': 'west ham.svg',
            'wolves': 'wolves.svg',
            'leicester': null,
            'southampton': null,
            'ipswich': null
        };
        return map[name] ? 'f_logo/' + map[name] : null;
    }

    function getTeamLogoHtml(team, size = 24) {
        const logoPath = getTeamLogo(team?.name);
        if (logoPath) {
            return `<img src="${logoPath}" alt="${team?.name}" style="height: ${size}px; width: ${size}px; object-fit: contain;">`;
        }
        return `<span class="badge bg-light text-dark border" style="font-size: 0.7rem;">${team?.short_name || ''}</span>`;
    }

    // Init
    init();

    async function init() {
         await loadStaticData(); // Load first
         loadFixtures(); // Then load default
         loadFDRTicker(); // Load Ticker
    }
    
    loadBtn.addEventListener('click', () => loadFixtures(gwSelect.value));
    gwSelect.addEventListener('change', () => loadFixtures(gwSelect.value));

    async function loadStaticData() {
        try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            staticData = await res.json();
            
            // Populate Dropdown
            let html = '<option value="" selected>Auto (Smart)</option>';
            staticData.events.forEach(e => {
                 if(e.id <= 38) {
                      let label = `Gameweek ${e.id}`;
                      if(e.is_current) label += " (Live)";
                      else if(e.is_next) label += " (Next)";
                      html += `<option value="${e.id}">${label}</option>`;
                 }
            });
            gwSelect.innerHTML = html;
        } catch(e) {
            console.error("Static data failed", e);
        }
    }

    async function loadFDRTicker() {
        try {
            // Get current and next 4 GWs
            const currentEvent = staticData.events.find(e => e.is_next) || staticData.events.find(e => e.is_current);
            const startGw = currentEvent.id;
            const endGw = Math.min(38, startGw + 4);
            
            // Update Headers
            for(let i=0; i<5; i++) {
                const gw = startGw + i;
                document.getElementById(`fdr-gw-${i+1}`).innerText = gw <= 38 ? `GW${gw}` : '-';
            }

            // Fetch Future Fixtures
            const res = await fetch('api.php?endpoint=fixtures/?future=1');
            const fixtures = await res.json();
            
            const tbody = document.getElementById('fdrTableBody');
            tbody.innerHTML = '';

            // Group by team
            staticData.teams.forEach(team => {
                const row = document.createElement('tr');
                const logoHtml = getTeamLogoHtml(team, 20);
                let cells = `<td class="fw-bold ps-3 d-flex align-items-center gap-2">${logoHtml} ${team.short_name}</td>`;
                
                for(let i=0; i<5; i++) {
                    const gw = startGw + i;
                    if(gw > 38) {
                        cells += '<td></td>';
                        continue;
                    }
                    
                    // Find fixture(s) for this team in this GW
                    const teamFixtures = fixtures.filter(f => f.event === gw && (f.team_h === team.id || f.team_a === team.id));
                    
                    if(teamFixtures.length === 0) {
                        cells += '<td class="text-center text-muted small">-</td>';
                    } else {
                        // Handle multiple fixtures (DGW)
                        let cellContent = '';
                        teamFixtures.forEach(fix => {
                            const isHome = fix.team_h === team.id;
                            const oppId = isHome ? fix.team_a : fix.team_h;
                            const opp = staticData.teams.find(t => t.id === oppId);
                            const difficulty = isHome ? fix.team_h_difficulty : fix.team_a_difficulty;
                            
                            // Format: "ARS (H)" or "LIV (A)"
                            const text = `${opp.short_name} (${isHome ? 'H' : 'A'})`;
                            cellContent += `<div class="fdr-cell fdr-${difficulty} mb-1" title="Difficulty: ${difficulty}">${text}</div>`;
                        });
                        cells += `<td class="align-middle">${cellContent}</td>`;
                    }
                }
                
                row.innerHTML = cells;
                tbody.appendChild(row);
            });

        } catch(e) {
            console.error("FDR Error", e);
            document.getElementById('fdrTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading ticker</td></tr>';
        }
    }

    async function loadFixtures(specificGw = null) {
        fixturesContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>`;
            
        try {
            if(!staticData) await loadStaticData();
            
            let event;
            
            if (specificGw) {
                // Manual Selection
                event = staticData.events.find(e => e.id == specificGw);
            } else {
                // Smart Auto Logic
                const current = staticData.events.find(e => e.is_current);
                const next = staticData.events.find(e => e.is_next);
                
                // If current event is explicitly marked 'finished', switch to next.
                if (current && current.finished) {
                     event = next || current;
                } else {
                     event = current || next || staticData.events[0];
                }
            }

            if (!event) {
                fixturesContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-calendar-x display-4 d-block mb-3"></i>
                            No active or upcoming fixtures found.
                        </div>
                    </div>
                `;
                if(!specificGw) gwSelect.value = ""; 
                return;
            }
            
            if(!specificGw) gwSelect.value = event.id; 

            const teams = {};
            staticData.teams.forEach(t => {
                teams[t.id] = t;
            });

            const fixturesRes = await fetch(`api.php?endpoint=fixtures/?event=${event.id}`);
            const fixtures = await fixturesRes.json();

            let html = `
                <div class="col-12 mb-2">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div class="h-px bg-secondary flex-grow-1" style="height: 2px; opacity: 0.5;"></div>
                        <h3 class="fw-bold text-primary m-0">Gameweek ${event.id} <span class="badge bg-primary text-dark fs-6 align-middle ms-2">${event.finished ? 'FINISHED' : (event.is_current ? 'LIVE' : 'UPCOMING')}</span></h3>
                        <div class="h-px bg-secondary flex-grow-1" style="height: 2px; opacity: 0.5;"></div>
                    </div>
                </div>
            `;
            
            fixtures.forEach(match => {
                const homeTeam = teams[match.team_h];
                const awayTeam = teams[match.team_a];
                const date = new Date(match.kickoff_time);
                const dateStr = date.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
                const timeStr = date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                const pulseId = match.code; 

                let statusBadge = '';
                let scoreDisplay = '';
                let borderColor = 'border-0'; 

                if (match.finished) {
                    statusBadge = '<span class="badge bg-secondary">FT</span>';
                    scoreDisplay = `<div class="h2 fw-bold mb-0">${match.team_h_score} - ${match.team_a_score}</div>`;
                } else if (match.started) {
                    statusBadge = `<span class="badge bg-danger spinner-grow-sm">LIVE ${match.minutes}'</span>`;
                    scoreDisplay = `<div class="h2 fw-bold mb-0 text-danger">${match.team_h_score} - ${match.team_a_score}</div>`;
                    borderColor = 'border border-danger border-2'; 
                } else {
                    statusBadge = `<span class="badge bg-light text-dark border">${timeStr}</span>`;
                    scoreDisplay = `<div class="h2 fw-bold mb-0 text-muted mx-3">vs</div>`;
                }

                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-hover ${borderColor} overflow-hidden">
                            <div class="card-body position-relative pb-2">
                                <a href="match-details.php?id=${match.id}&event=${match.event}" class="text-decoration-none text-dark stretched-link" style="z-index: 1;"></a>
                                <div class="position-absolute top-0 start-0 w-100 h-100" 
                                     style="background: linear-gradient(45deg, rgba(55,0,60,0.02) 0%, rgba(0,255,133,0.02) 100%); z-index: 0;">
                                </div>
                                <div class="position-relative z-1 text-center pointer-events-none">
                                    <div class="school-date text-muted small mb-2 fw-bold text-uppercase">${dateStr}</div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-center w-25">
                                            ${getTeamLogoHtml(homeTeam, 40)}
                                            <div class="fw-bold text-dark h6 mb-0 mt-1">${homeTeam.short_name}</div>
                                        </div>
                                        <div class="w-50 d-flex flex-column align-items-center justify-content-center">
                                            ${scoreDisplay}
                                            <div class="mt-2">${statusBadge}</div>
                                        </div>
                                        <div class="text-center w-25">
                                            ${getTeamLogoHtml(awayTeam, 40)}
                                            <div class="fw-bold text-dark h6 mb-0 mt-1">${awayTeam.short_name}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0 pb-3 text-center position-relative" style="z-index: 2;">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-4" 
                                        onclick="openLineups(${match.id}, ${match.event}, ${match.team_h}, ${match.team_a}, ${match.started}, ${match.pulse_id || match.code})">
                                    <i class="bi bi-people-fill me-1"></i> Lineups
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            fixturesContainer.innerHTML = html;

        } catch (error) {
            console.error(error);
            fixturesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Error loading fixtures: ${error.message}
                    </div>
                </div>
            `;
        }
    }

    async function openLineups(fixtureId, eventId, homeTeamId, awayTeamId, started, pulseId) {
        lineupsModal.show();
        lineupsModalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        const homeTeam = staticData.teams.find(t => t.id === homeTeamId);
        const awayTeam = staticData.teams.find(t => t.id === awayTeamId);

        if (!started) {
             lineupsModalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Official FPL lineup data is only available after kickoff.
                    </div>
                    <p class="mb-4">Confirmed lineups are usually available 1 hour before kickoff on the official Premier League website.</p>
                    <a href="https://www.premierleague.com/match/${pulseId}" target="_blank" class="btn btn-primary">
                        <i class="bi bi-box-arrow-up-right me-2"></i> View Confirmed Lineups
                    </a>
                    <div class="mt-3 text-muted small">Opens in a new tab</div>
                </div>
            `;
            return;
        }

        try {
            const liveRes = await fetch(`api.php?endpoint=event/${eventId}/live/`);
            if (!liveRes.ok) throw new Error('Failed to load live data');
            const liveData = await liveRes.json();

            const getPlayerStats = (id) => liveData?.elements?.find(e => e.id === id)?.stats;

            const getTeamLineup = (teamId) => {
                return staticData.elements
                    .filter(p => p.team === teamId)
                    .map(p => {
                        const stats = getPlayerStats(p.id);
                        return { player: p, stats: stats };
                    })
                    .filter(item => item.stats && item.stats.minutes > 0)
                    .sort((a, b) => a.player.element_type - b.player.element_type); 
            };

            const homeLineup = getTeamLineup(homeTeamId);
            const awayLineup = getTeamLineup(awayTeamId);

             lineupsModalBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-center mb-3 text-primary">${homeTeam.name}</h6>
                        <ul class="list-group list-group-flush list-group-sm">
                            ${renderLineupList(homeLineup)}
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-center mb-3 text-primary">${awayTeam.name}</h6>
                        <ul class="list-group list-group-flush list-group-sm">
                            ${renderLineupList(awayLineup)}
                        </ul>
                    </div>
                </div>
            `;

        } catch (error) {
             console.error(error);
             lineupsModalBody.innerHTML = `
                <div class="alert alert-danger text-center">
                    Could not load lineup data. <br> ${error.message}
                </div>
            `;
        }
    }

    function renderLineupList(lineup) {
        if (lineup.length === 0) return '<li class="list-group-item text-muted text-center small">No live data yet</li>';
        
        const posMap = {1: 'GK', 2: 'DEF', 3: 'MID', 4: 'FWD'};
        
        return lineup.map(item => `
            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                <div class="d-flex align-items-center text-truncate">
                    <span class="badge bg-light text-secondary border me-2 small" style="min-width: 35px;">${posMap[item.player.element_type]}</span>
                    <span class="small fw-500 text-truncate" style="max-width: 120px;" title="${item.player.web_name}">${item.player.web_name}</span>
                </div>
                <div class="small fw-bold ms-1" style="font-size: 0.85rem;">
                     ${item.stats.total_points}
                </div>
            </li>
        `).join('');
    }
</script>
</body>
</html>