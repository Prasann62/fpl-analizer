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
    <title>Transfer Planner | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .player-card {
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #dee2e6;
        }
        .player-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .pitch-view {
            background: linear-gradient(180deg, #1A472A 0%, #2F6F41 50%, #1A472A 100%);
            border-radius: 8px;
            padding: 20px;
            position: relative;
        }
        /* Pitch markings */
        .pitch-view::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255,255,255,0.3);
        }
        .pitch-view::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50%;
        }

        .player-pos-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .player-box {
            background: rgba(255,255,255,0.9);
            border-radius: 6px;
            padding: 5px;
            width: 100px;
            text-align: center;
            position: relative;
        }
        .player-box.bench {
            background: #e9ecef;
        }
        .player-box .remove-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            line-height: 18px;
            cursor: pointer;
            display: none;
        }
        .player-box:hover .remove-btn {
            display: block;
        }
        .fdr-badge {
            font-size: 0.65rem;
            padding: 2px 4px;
        }
        .fdr-1 { background-color: #375523; color: white; } /* Easy */
        .fdr-2 { background-color: #01fc7a; color: black; }
        .fdr-3 { background-color: #e7e7e7; color: black; }
        .fdr-4 { background-color: #ff1751; color: white; }
        .fdr-5 { background-color: #80072d; color: white; } /* Hard */
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-4">
        <div class="hero-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold mb-1">Transfer Planner</h1>
                <p class="text-muted small mb-0">Plan your moves for upcoming gameweeks.</p>
            </div>
            <div>
                <div class="input-group">
                    <input type="number" id="managerIdInput" class="form-control" placeholder="Manager ID">
                    <button class="btn btn-primary" id="loadTeamBtn">Load Team</button>
                </div>
            </div>
        </div>

        <!-- Planner Interface -->
        <div id="plannerInterface" class="d-none">
            
            <div class="row g-4">
                <!-- Left: Pitch / Team -->
                <div class="col-lg-8">
                    <!-- Gameweek Nav -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center bg-light rounded">
                            <button class="btn btn-sm btn-outline-secondary" id="prevGwBtn"><i class="bi bi-chevron-left"></i></button>
                            <div class="text-center">
                                <h5 class="m-0 fw-bold" id="currentGwDisplay">GW --</h5>
                                <small class="text-danger" id="deadlineDisplay">Deadline: --</small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" id="nextGwBtn"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>

                    <!-- Pitch -->
                    <div class="pitch-view mb-3" id="pitchArea">
                        <!-- GK -->
                        <div class="player-pos-row" id="row-GK"></div>
                        <!-- DEF -->
                        <div class="player-pos-row" id="row-DEF"></div>
                        <!-- MID -->
                        <div class="player-pos-row" id="row-MID"></div>
                        <!-- FWD -->
                        <div class="player-pos-row" id="row-FWD"></div>
                    </div>

                    <!-- Bench -->
                    <div class="card shadow-sm bg-light">
                        <div class="card-body py-2">
                            <h6 class="text-muted small text-uppercase fw-bold mb-2">Bench</h6>
                            <div class="d-flex justify-content-center gap-3" id="benchArea"></div>
                        </div>
                    </div>

                </div>

                <!-- Right: Stats & Search -->
                <div class="col-lg-4">
                    <!-- Budget / Stats Card -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white fw-bold">Plan Summary</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Bank:</span>
                                <span class="fw-bold text-success" id="bankDisplay">£0.0m</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Team Value:</span>
                                <span class="fw-bold" id="valueDisplay">£0.0m</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Transfers:</span>
                                <span class="fw-bold" id="transfersCount">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Cost:</span>
                                <span class="fw-bold text-danger" id="transfersCost">0 pts</span>
                            </div>
                            <hr>
                            <button class="btn btn-outline-danger w-100 btn-sm" id="resetPlanBtn">Reset Plan</button>
                        </div>
                    </div>

                    <!-- Player Search -->
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">Player Search</div>
                        <div class="card-body p-2">
                            <input type="text" class="form-control mb-2" id="playerSearch" placeholder="Search player...">
                            <div class="btn-group w-100 mb-2" role="group">
                                <input type="radio" class="btn-check" name="posFilter" id="posAll" value="0" checked>
                                <label class="btn btn-outline-primary btn-sm" for="posAll">All</label>
                                
                                <input type="radio" class="btn-check" name="posFilter" id="posGK" value="1">
                                <label class="btn btn-outline-primary btn-sm" for="posGK">GK</label>
                                
                                <input type="radio" class="btn-check" name="posFilter" id="posDEF" value="2">
                                <label class="btn btn-outline-primary btn-sm" for="posDEF">DEF</label>
                                
                                <input type="radio" class="btn-check" name="posFilter" id="posMID" value="3">
                                <label class="btn btn-outline-primary btn-sm" for="posMID">MID</label>
                                
                                <input type="radio" class="btn-check" name="posFilter" id="posFWD" value="4">
                                <label class="btn btn-outline-primary btn-sm" for="posFWD">FWD</label>
                            </div>

                            <div class="list-group list-group-flush overflow-auto" style="max-height: 400px;" id="searchResults">
                                <!-- Results here -->
                                <div class="text-center text-muted small py-3">Type to search players</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Loading -->
        <div id="loading" class="text-center py-5 d-none">
             <div class="spinner-border text-primary" role="status"></div>
             <p class="mt-2 text-muted">Loading FPL Data...</p>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const loadBtn = document.getElementById('loadTeamBtn');
    const managerInput = document.getElementById('managerIdInput');
    const plannerInterface = document.getElementById('plannerInterface');
    const loading = document.getElementById('loading');
    
    const bankDisplay = document.getElementById('bankDisplay');
    const valueDisplay = document.getElementById('valueDisplay');
    const transfersCount = document.getElementById('transfersCount');
    const transfersCost = document.getElementById('transfersCost');
    const resetPlanBtn = document.getElementById('resetPlanBtn');
    
    // Search Elements
    const playerSearch = document.getElementById('playerSearch');
    const searchResults = document.getElementById('searchResults');
    const posFilters = document.querySelectorAll('input[name="posFilter"]');

    // State
    let bootstrapData = null;
    let currentTeam = []; // Array of rich player objects
    let initialTeamIds = new Set(); // To track transfers
    let bank = 0;
    let initialBank = 0;
    let currentGWId = 1;

    // --- Init ---
    // Check URL params
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('id')) {
        managerInput.value = urlParams.get('id');
        loadManager(urlParams.get('id'));
    }

    // --- Event Listeners ---
    loadBtn.addEventListener('click', () => {
        const id = managerInput.value.trim();
        if(id) loadManager(id);
    });

    resetPlanBtn.addEventListener('click', () => {
        if(confirm('Reset all changes?')) loadManager(managerInput.value.trim()); // Reload simple
    });

    playerSearch.addEventListener('input', (e) => filterPlayers(e.target.value));
    posFilters.forEach(radio => {
        radio.addEventListener('change', () => filterPlayers(playerSearch.value));
    });

    // --- Core Functions ---

    async function loadManager(id) {
        showLoading(true);
        try {
            // 1. Static Data
            if(!bootstrapData) {
                const res = await fetch('api.php?endpoint=bootstrap-static/');
                bootstrapData = await res.json();
            }
            
            // 2. Determine GW
            const currentEvent = bootstrapData.events.find(e => e.is_current) || bootstrapData.events[0];
            currentGWId = currentEvent.id;
            
            // 3. Fetch Manager Picks
            const picksRes = await fetch(`api.php?endpoint=entry/${id}/event/${currentGWId}/picks/`);
            if(picksRes.status === 404) throw new Error('Manager history not found for this GW.');
            const picksData = await picksRes.json();
            
            // 4. Set Bank
            // Note: entry_history.bank is in 10x units
            bank = picksData.entry_history ? picksData.entry_history.bank : 0;
            initialBank = bank;
            
            // 5. Map picks
            currentTeam = picksData.picks.map(pick => {
                const player = bootstrapData.elements.find(p => p.id === pick.element);
                return { ...player }; // copy
            });
            
            initialTeamIds = new Set(currentTeam.map(p => p.id));

            renderPlanner();
            updateStats();
            filterPlayers(''); // Init search list
            
        } catch(e) {
            console.error(e);
            alert('Error loading team: ' + e.message);
        } finally {
            showLoading(false);
        }
    }

    function renderPlanner() {
        plannerInterface.classList.remove('d-none');
        document.getElementById('currentGwDisplay').innerText = `Planning for GW ${currentGWId + 1}`;

        // Clear areas
        ['row-GK', 'row-DEF', 'row-MID', 'row-FWD', 'benchArea'].forEach(id => document.getElementById(id).innerHTML = '');

        // Sort: GK first, then by price descending? Or logical formation?
        // We'll separate into Pos Buckets
        const positions = { 1: [], 2: [], 3: [], 4: [] };
        currentTeam.forEach(p => positions[p.element_type].push(p));

        // Render Pitch (Start with defaults, simplified: first 1 GK, 3-5 DEF, etc)
        // For simplicity in this Planner v1, we just dump ALL players of that pos into the row
        // User manages who is "bench" mentally or we add bench logic later.
        
        // Render GK
        positions[1].forEach(p => appendPlayer(p, 'row-GK'));
        // Render DEF
        positions[2].forEach(p => appendPlayer(p, 'row-DEF'));
        // Render MID
        positions[3].forEach(p => appendPlayer(p, 'row-MID'));
        // Render FWD
        positions[4].forEach(p => appendPlayer(p, 'row-FWD'));
    }

    function appendPlayer(player, containerId) {
        const team = bootstrapData.teams.find(t => t.id === player.team);
        const div = document.createElement('div');
        div.className = 'player-box';
        
        // Remove Button
        const removeBtn = document.createElement('span');
        removeBtn.className = 'remove-btn';
        removeBtn.innerText = '×';
        removeBtn.onclick = () => removePlayer(player.id);
        
        div.innerHTML = `
            <div class="fw-bold small text-truncate" title="${player.web_name}">${player.web_name}</div>
            <div style="font-size: 0.7rem;" class="text-muted">${team.short_name}</div>
            <div class="small fw-bold">£${(player.now_cost/10).toFixed(1)}</div>
        `;
        div.appendChild(removeBtn);
        document.getElementById(containerId).appendChild(div);
    }

    window.removePlayer = function(id) {
        const p = currentTeam.find(x => x.id === id);
        if(!p) return;
        
        // Refund bank
        bank += p.now_cost;
        
        // Remove from array
        currentTeam = currentTeam.filter(x => x.id !== id);
        
        renderPlanner();
        updateStats();
    };

    window.addPlayer = function(id) {
        if(currentTeam.length >= 15) return alert('Team full (15 players). Remove someone first.');
        
        const player = bootstrapData.elements.find(x => x.id === id);
        
        // Check constraints
        if(currentTeam.find(x => x.id === id)) return alert('Player already in team.');
        if(bank < player.now_cost) return alert('Not enough bank!');
        
        const posCount = currentTeam.filter(x => x.element_type === player.element_type).length;
        const limits = {1:2, 2:5, 3:5, 4:3};
        if(posCount >= limits[player.element_type]) return alert('Too many players in this position.');

        const teamCount = currentTeam.filter(x => x.team === player.team).length;
        if(teamCount >= 3) return alert('Max 3 players per team.');

        // Add
        currentTeam.push(player);
        bank -= player.now_cost;

        renderPlanner();
        updateStats();
    };

    function updateStats() {
        document.getElementById('bankDisplay').innerText = `£${(bank/10).toFixed(1)}m`;
        
        const teamValue = currentTeam.reduce((acc, p) => acc + p.now_cost, 0);
        document.getElementById('valueDisplay').innerText = `£${(teamValue/10).toFixed(1)}m`;
        
        // Calculate Transfers Made
        // Count how many in currentTeam are NOT in initialTeamIds
        // This is a naive count. If I sell A for B, then sell B for A, count is 0. Correct.
        let transfers = 0;
        currentTeam.forEach(p => {
            if(!initialTeamIds.has(p.id)) transfers++;
        });
        document.getElementById('transfersCount').innerText = transfers;
        
        // Cost: Free hits? 
        // For now assume 1 free transfer, rest -4
        // Logic: if transfers > 1, then (transfers - 1) * 4. 
        // Simplified.
        const cost = Math.max(0, (transfers - 1) * 4);
        document.getElementById('transfersCost').innerText = `-${cost} pts`;
    }

    // --- Search Logic ---
    function filterPlayers(query) {
        if(!bootstrapData) return;
        
        const q = query.toLowerCase();
        const typeFilter = parseInt(document.querySelector('input[name="posFilter"]:checked').value);
        
        // Filter
        const results = bootstrapData.elements.filter(p => {
             const matchesName = p.web_name.toLowerCase().includes(q) || p.first_name.toLowerCase().includes(q) || p.second_name.toLowerCase().includes(q);
             const matchesType = typeFilter === 0 || p.element_type === typeFilter;
             return matchesName && matchesType;
        });

        // Sort by form or transfers_in or points? Let's do rounded form descending
        results.sort((a,b) => parseFloat(b.form) - parseFloat(a.form));

        // Render Top 50 to avoid lag
        renderSearchResults(results.slice(0, 50));
    }

    function renderSearchResults(list) {
        searchResults.innerHTML = '';
        list.forEach(p => {
            const team = bootstrapData.teams.find(t => t.id === p.team);
            const inTeam = currentTeam.some(x => x.id === p.id);
            
            const btn = document.createElement('button');
            btn.className = `list-group-item list-group-item-action d-flex justify-content-between align-items-center ${inTeam ? 'disabled opacity-50' : ''}`;
            btn.innerHTML = `
                <div>
                     <div class="fw-bold text-dark small">${p.web_name}</div>
                     <div class="small text-muted" style="font-size: 0.7rem;">${team.short_name} • ${p.form} Form</div>
                </div>
                <div class="text-end">
                     <span class="badge bg-light text-dark border">£${(p.now_cost/10).toFixed(1)}</span>
                     ${ !inTeam ? '<i class="bi bi-plus-circle-fill text-primary ms-1"></i>' : '' }
                </div>
            `;
            if(!inTeam) {
                btn.onclick = () => addPlayer(p.id);
            }
            searchResults.appendChild(btn);
        });
    }

    function showLoading(show) {
        if(show) loading.classList.remove('d-none');
        else loading.classList.add('d-none');
    }
});
</script>
</body>
</html>
