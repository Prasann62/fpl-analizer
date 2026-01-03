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
    <title>Live Score | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .player-card-bg {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.2s;
        }
        .player-card-bg:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.1);
        }
        .hero-header {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9) 0%, rgba(5, 150, 105, 0.95) 100%), url('f_logo/football_pitch.svg');
            background-size: cover;
            background-position: center;
            border-radius: 1rem;
            padding: 2rem;
            color: white;
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Header -->
        <div class="hero-header text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
            <div class="z-1">
                <h1 class="display-5 fw-bold mb-2">Live Score</h1>
                <p class="lead opacity-75 mb-0">Real-time performance stats for the current gameweek.</p>
            </div>
            <div class="mt-4 mt-md-0 z-1">
                <i class="bi bi-activity display-1 opacity-25"></i>
            </div>
        </div>

        <!-- Manager Input and GW Selector -->
        <div class="row mb-4">
            <div class="col-md-9 mx-auto">
                <div class="input-group input-group-lg shadow-sm">
                    <input type="number" id="managerId" class="form-control" placeholder="Enter Manager ID" aria-label="Manager ID">
                    <select class="form-select" id="gwSelect" style="max-width: 170px;">
                        <option value="" selected>Auto (Live)</option>
                        <!-- Options populated by JS -->
                    </select>
                    <button class="btn btn-primary fw-bold px-4" type="button" id="loadBtn">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Load
                    </button>
                    <button class="btn btn-outline-success" type="button" id="autoRefreshBtn" title="Auto-refresh every 60s">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
                <div id="autoRefreshStatus" class="text-center mt-2 d-none">
                    <span class="badge bg-success"><i class="bi bi-broadcast me-1"></i>Auto-refresh ON</span>
                    <span class="text-muted small ms-2">Next update in <span id="countdown">60</span>s</span>
                </div>
            </div>
        </div>

        <!-- Live Content -->
        <div id="liveContent" class="d-none">
            
            <!-- Loading Overlay (Visible during updates without hiding content) -->
            <div id="loadingOverlay" class="d-none position-absolute top-50 start-50 translate-middle badge bg-dark p-3 shadow-lg z-3">
                 <span class="spinner-border spinner-border-sm me-2"></span> Updating Live Data...
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Score</h6>
                            <h2 class="display-2 fw-bold mb-0" id="totalPoints">0</h2>
                            <i class="bi bi-trophy-fill opacity-25 position-absolute top-0 end-0 m-3 fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #198754 0%, #157347 100%);">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white-50 text-uppercase fw-bold mb-2">Gameweek Rank</h6>
                            <h2 class="display-4 fw-bold mb-0" id="gwRank">-</h2>
                            <div class="small text-white-50 mt-2">GW: <span id="currentGW" class="fw-bold text-white"></span></div>
                        </div>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                             <div>
                                <h6 class="text-muted text-uppercase fw-bold mb-2">Manager Info</h6>
                                 <h4 class="fw-bold mb-1" id="managerName"></h4>
                                 <p class="text-muted mb-0 small" id="teamName"></p>
                             </div>
                             <i class="bi bi-person-badge-fill fs-1 text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Row: Mini-League & Captain Comparison -->
            <div class="row g-3 mb-4">
                <!-- Mini-League Live -->
                <div class="col-lg-6">
                    <div class="card h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
                        <div class="card-header bg-transparent border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-white fw-bold"><i class="bi bi-trophy me-2"></i>Mini-League Live</h6>
                                <select class="form-select form-select-sm bg-dark border-secondary text-white" id="leagueSelect" style="width: auto;">
                                    <option value="">Select League</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="miniLeagueTable" class="text-center py-4">
                                <small class="text-white-50">Enter Manager ID to load leagues</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Captain Comparison -->
                <div class="col-lg-6">
                    <div class="card h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 text-white fw-bold"><i class="bi bi-person-badge me-2"></i>Captain Points vs Rivals</h6>
                        </div>
                        <div class="card-body">
                            <div id="captainComparison" class="text-center py-3">
                                <small class="text-white-50">Load data to see captain comparison</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autosub Preview -->
            <div class="card mb-4" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%); border: 1px solid rgba(245, 158, 11, 0.3);">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 text-warning fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Autosub Simulation</h6>
                </div>
                <div class="card-body py-2">
                    <div id="autosubPreview" class="d-flex flex-wrap gap-2 justify-content-center">
                        <small class="text-white-50">Autosubs will appear here if applicable</small>
                    </div>
                </div>
            </div>

            <!-- Players Table -->
            <div class="card position-relative">
                <div class="card-header py-3">
                     <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-ul me-2"></i>Lineup & Stats</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-start ps-4">Player</th>
                                    <th>Min</th>
                                    <th>G</th>
                                    <th>A</th>
                                    <th>CS</th>
                                    <th>GC</th>
                                    <th>Sav</th>
                                    <th>BPS</th>
                                    <th>Cards</th>
                                    <th>ICT</th>
                                    <th class="text-end pe-4">Pts</th>
                                </tr>
                            </thead>
                            <tbody id="lineupTable">
                                <!-- Rows injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
         <!-- Initial Loading State (only for first load) -->
        <div id="initialLoadingState" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted fw-bold">Fetching Data...</p>
        </div>

         <!-- Error State -->
        <div id="errorState" class="d-none alert alert-danger shadow-sm mt-4 text-center">
             <i class="bi bi-exclamation-triangle-fill me-2"></i> <span id="errorMessage"></span>
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
    const managerInput = document.getElementById('managerId');
    const gwSelect = document.getElementById('gwSelect');
    const liveContent = document.getElementById('liveContent');
    const initialLoadingState = document.getElementById('initialLoadingState');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    // UI Elements
    const totalPointsEl = document.getElementById('totalPoints');
    const gwRankEl = document.getElementById('gwRank');
    const currentGWEl = document.getElementById('currentGW');
    const managerNameEl = document.getElementById('managerName');
    const teamNameEl = document.getElementById('teamName');
    const lineupTable = document.getElementById('lineupTable');
    
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

    function getTeamLogoHtml(team, size = 18) {
        const logoPath = getTeamLogo(team?.name);
        if (logoPath) {
            return `<img src="${logoPath}" alt="${team?.name}" style="height: ${size}px; width: ${size}px; object-fit: contain;" class="me-1">`;
        }
        return '';
    }

    // Load ID from storage
    const storedId = localStorage.getItem('fpl_manager_id');
    if(storedId) {
        managerInput.value = storedId;
    }

    // Initialize - populate GW dropdown
    init();

    async function init() {
         try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            staticData = await res.json();
            
            // Populate Dropdown
            let html = '<option value="" selected>Auto (Live)</option>';
            staticData.events.forEach(e => {
                if(e.id <= 38) {
                     const label = e.is_current ? `GW ${e.id} (Live)` : (e.is_next ? `GW ${e.id} (Next)` : `GW ${e.id}`);
                     html += `<option value="${e.id}">${label}</option>`;
                }
            });
            gwSelect.innerHTML = html;
        } catch(e) {
            console.error(e);
        }
    }

    loadBtn.addEventListener('click', loadData);

    async function loadData() {
        const id = managerInput.value.trim();
        if(!id) return alert('Please enter a Manager ID');
        
        localStorage.setItem('fpl_manager_id', id);
        
        // Reset Error
        errorState.classList.add('d-none');
        
        // Loading States
        const isFirstLoad = liveContent.classList.contains('d-none');
        if (isFirstLoad) {
            initialLoadingState.classList.remove('d-none');
        } else {
             if(loadingOverlay) loadingOverlay.classList.remove('d-none');
             loadBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
        
        loadBtn.disabled = true;

        try {
            // Ensure static data
            if(!staticData) {
                 const staticRes = await fetch('api.php?endpoint=bootstrap-static/');
                 staticData = await staticRes.json();
            }
            
            // Determine Gameweek
            let gw = gwSelect.value;
            if (!gw) {
                const currentEvent = staticData.events.find(e => e.is_current) || staticData.events[0];
                gw = currentEvent.id;
            }
            currentGWEl.textContent = gw;

            // Mappings
            const players = {};
            staticData.elements.forEach(p => players[p.id] = p);
            const teams = {};
            staticData.teams.forEach(t => teams[t.id] = t);

             // 2. Get Manager Info
            const entryRes = await fetch(`api.php?endpoint=entry/${id}/`);
            if(!entryRes.ok) throw new Error('Manager not found');
            const entryData = await entryRes.json();
            
            managerNameEl.textContent = `${entryData.player_first_name} ${entryData.player_last_name}`;
            teamNameEl.textContent = entryData.name;
            gwRankEl.textContent = entryData.summary_event_rank ? entryData.summary_event_rank.toLocaleString() : '-';

             // 3. Get Picks
            const picksRes = await fetch(`api.php?endpoint=entry/${id}/event/${gw}/picks/`);
            if(!picksRes.ok) throw new Error('Picks not found for this GW');
            const picksData = await picksRes.json();

            // 4. Get Live Stats
            const liveRes = await fetch(`api.php?endpoint=event/${gw}/live/`);
            const liveData = await liveRes.json();
            const liveStats = {};
            liveData.elements.forEach(el => liveStats[el.id] = el.stats);

            // Calculate and Render
            let totalScore = 0;
            let html = '';

            picksData.picks.forEach(pick => {
                const player = players[pick.element];
                const stats = liveStats[pick.element] || {}; // Live stats might be missing if not played
                const team = teams[player.team];
                
                // Multiplier Logic (Captain/Vice/Bench Boost etc.)
                const mult = pick.multiplier;
                const isCap = pick.is_captain;
                const isVice = pick.is_vice_captain;
                
                // Points calculation
                const rawPoints = stats.total_points || 0;
                const finalPoints = rawPoints * mult;
                if(mult > 0) totalScore += finalPoints; // Only add active players

                // Helpers for zeros
                const s = (val) => val !== undefined ? val : 0;

                // Status badges
                let badges = '';
                if(isCap) badges += '<span class="badge bg-warning text-dark ms-1">C</span>';
                if(isVice) badges += '<span class="badge bg-secondary ms-1">V</span>';
                if(mult === 0) badges += '<span class="badge bg-light text-muted border ms-1">Bench</span>';

                // Cards visualization
                let cards = '';
                if(s(stats.yellow_cards) > 0) cards += `<span class="badge bg-warning text-dark me-1" title="Yellow Card"></span>`;
                if(s(stats.red_cards) > 0) cards += `<span class="badge bg-danger" title="Red Card"></span>`;

                html += `
                    <tr class="${mult === 0 ? 'bg-light opacity-75' : ''}">
                        <td class="text-start ps-4">
                            <div class="fw-bold text-dark">${player.web_name} ${badges}</div>
                            <div class="small text-muted d-flex align-items-center">${getTeamLogoHtml(team)}${team.short_name} &bull; ${player.element_type === 1 ? 'GKP' : player.element_type === 2 ? 'DEF' : player.element_type === 3 ? 'MID' : 'FWD'}</div>
                        </td>
                        <td>${s(stats.minutes)}'</td>
                        <td>${s(stats.goals_scored) > 0 ? '<span class="text-success fw-bold">'+stats.goals_scored+'</span>' : '0'}</td>
                        <td>${s(stats.assists) > 0 ? '<span class="text-primary fw-bold">'+stats.assists+'</span>' : '0'}</td>
                        <td>${s(stats.clean_sheets) > 0 ? '<i class="bi bi-check-lg text-success"></i>' : '-'}</td>
                        <td class="${s(stats.goals_conceded) > 1 ? 'text-danger' : ''}">${s(stats.goals_conceded)}</td>
                        <td>${s(stats.saves)}</td>
                        <td class="fw-bold text-primary">${s(stats.bps)}</td>
                        <td>${cards || '-'}</td>
                        <td>${s(stats.ict_index)}</td>
                        <td class="text-end pe-4">
                            <span class="badge ${finalPoints >= 10 ? 'bg-success' : 'bg-primary'} fs-6">${finalPoints}</span>
                        </td>
                    </tr>
                `;
            });
            
            // Subtract transfer hits?
            if(picksData.entry_history && picksData.entry_history.event_transfers_cost) {
                 totalScore -= picksData.entry_history.event_transfers_cost;
                 // maybe show this in UI?
            }

            totalPointsEl.textContent = totalScore;
            lineupTable.innerHTML = html;
            
            liveContent.classList.remove('d-none');

            // Load mini-leagues
            loadMiniLeagues(id);
            
            // Render captain comparison
            renderCaptainComparison(picksData, liveStats, players);
            
            // Render autosub simulation
            renderAutosubPreview(picksData, liveStats, players);

        } catch (err) {
            console.error(err);
            errorMessage.textContent = err.message || "Failed to load data.";
            errorState.classList.remove('d-none');
            // Keep content visible if possible, usually better UX
        } finally {
            initialLoadingState.classList.add('d-none');
            if(loadingOverlay) loadingOverlay.classList.add('d-none');
            loadBtn.disabled = false;
            loadBtn.innerHTML = '<i class="bi bi-lightning-charge-fill me-2"></i>Load';
        }
    }

    // Mini-League Functions
    let managerLeagues = [];
    
    async function loadMiniLeagues(managerId) {
        try {
            const res = await fetch(`api.php?endpoint=entry/${managerId}/`);
            const data = await res.json();
            
            // Get classic leagues
            managerLeagues = data.leagues?.classic || [];
            
            // Populate dropdown
            const leagueSelect = document.getElementById('leagueSelect');
            leagueSelect.innerHTML = '<option value="">Select League</option>';
            
            managerLeagues.slice(0, 10).forEach(league => {
                if(league.league_type === 'x') {
                    leagueSelect.innerHTML += `<option value="${league.id}">${league.name}</option>`;
                }
            });
            
            // Auto-select first private league
            const privateLeague = managerLeagues.find(l => l.league_type === 'x');
            if(privateLeague) {
                leagueSelect.value = privateLeague.id;
                loadLeagueStandings(privateLeague.id, managerId);
            }
            
        } catch(e) {
            console.error('Failed to load leagues:', e);
        }
    }
    
    document.getElementById('leagueSelect').addEventListener('change', function() {
        if(this.value) {
            loadLeagueStandings(this.value, managerInput.value);
        }
    });
    
    async function loadLeagueStandings(leagueId, currentManagerId) {
        const container = document.getElementById('miniLeagueTable');
        container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
        
        try {
            const res = await fetch(`api.php?endpoint=leagues-classic/${leagueId}/standings/`);
            const data = await res.json();
            
            const standings = data.standings?.results || [];
            const top5 = standings.slice(0, 5);
            
            let html = '<table class="table table-sm table-borderless mb-0">';
            html += '<thead><tr class="text-white-50 small"><th>#</th><th class="text-start">Manager</th><th>GW</th><th>Total</th></tr></thead><tbody>';
            
            top5.forEach((entry, idx) => {
                const isYou = entry.entry == currentManagerId;
                const rankChange = entry.last_rank - entry.rank;
                const changeIcon = rankChange > 0 ? '<i class="bi bi-arrow-up-short text-success"></i>' : 
                                   rankChange < 0 ? '<i class="bi bi-arrow-down-short text-danger"></i>' : '';
                
                html += `
                    <tr class="${isYou ? 'bg-primary bg-opacity-25' : ''}">
                        <td class="text-white">${entry.rank} ${changeIcon}</td>
                        <td class="text-start text-white text-truncate" style="max-width: 120px;">
                            ${entry.player_name}
                            ${isYou ? '<span class="badge bg-primary ms-1">You</span>' : ''}
                        </td>
                        <td class="text-white fw-bold">${entry.event_total}</td>
                        <td class="text-white-50">${entry.total}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
            
        } catch(e) {
            container.innerHTML = '<small class="text-danger">Failed to load</small>';
        }
    }
    
    // Captain Comparison
    function renderCaptainComparison(picksData, liveStats, playersMap) {
        const container = document.getElementById('captainComparison');
        
        const captainPick = picksData.picks.find(p => p.is_captain);
        if(!captainPick) {
            container.innerHTML = '<small class="text-white-50">No captain found</small>';
            return;
        }
        
        const captainPlayer = playersMap[captainPick.element];
        const captainStats = liveStats[captainPick.element] || {};
        const captainPoints = (captainStats.total_points || 0) * captainPick.multiplier;
        
        // Get top template captains
        const topCaptains = staticData.elements
            .filter(p => parseFloat(p.selected_by_percent) > 15)
            .sort((a, b) => parseFloat(b.selected_by_percent) - parseFloat(a.selected_by_percent))
            .slice(0, 3);
        
        let html = `
            <div class="mb-3 p-2 rounded" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.5);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-warning text-dark me-2">C</span>
                        <strong class="text-white">${captainPlayer.web_name}</strong>
                    </div>
                    <span class="text-success fw-bold fs-5">${captainPoints} pts</span>
                </div>
            </div>
            <div class="small text-white-50 mb-2">Template Captain Comparison:</div>
        `;
        
        topCaptains.forEach(player => {
            const stats = liveStats[player.id] || {};
            const points = (stats.total_points || 0) * 2; // Assuming captain
            const diff = captainPoints - points;
            const diffColor = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-white-50';
            const diffIcon = diff > 0 ? '↑' : diff < 0 ? '↓' : '=';
            
            html += `
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-secondary">
                    <span class="text-white-50">${player.web_name} (${player.selected_by_percent}%)</span>
                    <span class="text-white">${points} pts <span class="${diffColor}">${diffIcon}${Math.abs(diff)}</span></span>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Autosub Preview
    function renderAutosubPreview(picksData, liveStats, playersMap) {
        const container = document.getElementById('autosubPreview');
        
        const starters = picksData.picks.filter(p => p.multiplier > 0);
        const bench = picksData.picks.filter(p => p.multiplier === 0);
        
        // Find starters who didn't play
        const noPlayStarters = starters.filter(p => {
            const stats = liveStats[p.element] || {};
            return (stats.minutes || 0) === 0;
        });
        
        if(noPlayStarters.length === 0) {
            container.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>All starters played - No autosubs needed</span>';
            return;
        }
        
        let html = '';
        noPlayStarters.forEach(starter => {
            const starterPlayer = playersMap[starter.element];
            
            // Find first valid bench player
            const benchSub = bench.find(b => {
                const benchPlayer = playersMap[b.element];
                const benchStats = liveStats[b.element] || {};
                return (benchStats.minutes || 0) > 0;
            });
            
            if(benchSub) {
                const benchPlayer = playersMap[benchSub.element];
                const benchStats = liveStats[benchSub.element] || {};
                
                html += `
                    <div class="d-flex align-items-center gap-2 bg-dark rounded px-3 py-2">
                        <span class="text-danger text-decoration-line-through">${starterPlayer.web_name}</span>
                        <i class="bi bi-arrow-right text-warning"></i>
                        <span class="text-success">${benchPlayer.web_name}</span>
                        <span class="badge bg-success">+${benchStats.total_points || 0}</span>
                    </div>
                `;
            } else {
                html += `
                    <div class="d-flex align-items-center gap-2 bg-dark rounded px-3 py-2">
                        <span class="text-danger">${starterPlayer.web_name}</span>
                        <span class="badge bg-warning text-dark">No valid sub</span>
                    </div>
                `;
            }
        });
        
        container.innerHTML = html || '<span class="text-white-50">Calculating autosubs...</span>';
    }

    // Auto-refresh functionality
    let autoRefreshInterval = null;
    let countdownInterval = null;
    let countdown = 60;
    const autoRefreshBtn = document.getElementById('autoRefreshBtn');
    const autoRefreshStatus = document.getElementById('autoRefreshStatus');
    const countdownEl = document.getElementById('countdown');

    autoRefreshBtn.addEventListener('click', toggleAutoRefresh);

    function toggleAutoRefresh() {
        if (autoRefreshInterval) {
            // Turn OFF
            clearInterval(autoRefreshInterval);
            clearInterval(countdownInterval);
            autoRefreshInterval = null;
            autoRefreshBtn.classList.remove('btn-success');
            autoRefreshBtn.classList.add('btn-outline-success');
            autoRefreshBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
            autoRefreshStatus.classList.add('d-none');
        } else {
            // Turn ON
            if (!managerInput.value.trim()) {
                alert('Please enter a Manager ID first');
                return;
            }
            autoRefreshBtn.classList.remove('btn-outline-success');
            autoRefreshBtn.classList.add('btn-success');
            autoRefreshBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
            autoRefreshStatus.classList.remove('d-none');
            countdown = 60;
            countdownEl.textContent = countdown;

            countdownInterval = setInterval(() => {
                countdown--;
                countdownEl.textContent = countdown;
                if (countdown <= 0) countdown = 60;
            }, 1000);

            autoRefreshInterval = setInterval(() => {
                countdown = 60;
                loadData();
            }, 60000);
        }
    }

    // Auto-load if manager ID is stored
    window.addEventListener('DOMContentLoaded', () => {
        const storedId = localStorage.getItem('fpl_manager_id');
        if(storedId) {
            managerInput.value = storedId;
            setTimeout(() => loadData(), 300);
        }
    });
</script>
</body>
</html>
