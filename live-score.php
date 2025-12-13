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
    <title>Live Score | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
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
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="container py-5">
    <!-- Header -->
    <div class="hero-header shadow-lg text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <div class="z-1">
            <h1 class="display-4 fw-extrabold mb-2">Live Live Score</h1>
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
                <div class="card h-100 border-0 shadow-lg bg-dark text-white">
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

        <!-- Players Table -->
        <div class="card border-0 shadow-lg position-relative">
            <div class="card-header bg-dark text-white py-3">
                 <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i>Lineup & Stats</h5>
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
                            <div class="small text-muted">${team.short_name} &bull; ${player.element_type === 1 ? 'GKP' : player.element_type === 2 ? 'DEF' : player.element_type === 3 ? 'MID' : 'FWD'}</div>
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
</script>
</body>
</html>
