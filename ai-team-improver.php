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
    <title>AI Team Improver | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
    <style>
        .transfer-card {
            border-left: 4px solid var(--accent-color);
            transition: transform 0.2s;
        }
        .transfer-card:hover {
            transform: translateX(5px);
        }
        .transfer-card.diff-card {
            border-left-color: #fd7e14; /* Orange for diffs */
        }
        .transfer-card.block-card {
            border-left-color: var(--secondary-color); /* Teal for blocks */
        }
        .player-out {
            background: rgba(220, 53, 69, 0.05);
        }
        .player-in {
            background: rgba(25, 135, 84, 0.05);
        }
        .ownership-bar {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
        }
        .ownership-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--secondary-color);
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="container py-5">
    <div class="hero-header shadow-lg mb-5">
        <h1 class="display-4 fw-extrabold mb-2">League-Smart Transfers</h1>
        <p class="lead opacity-75">Analyze your mini-league rivals and find the winning edge.</p>
    </div>

    <!-- Step 1: Manager ID -->
    <div class="card mb-4" id="step1">
        <div class="card-body">
            <h5 class="card-title fw-bold">1. Load Your Team</h5>
            <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="number" id="managerId" class="form-control" placeholder="Enter your Manager ID">
                <button class="btn btn-primary" id="loadLeaguesBtn">Load Leagues</button>
            </div>
        </div>
    </div>

    <!-- Step 2: Select League -->
    <div class="card mb-4 d-none" id="step2">
        <div class="card-body">
            <h5 class="card-title fw-bold">2. Select Target League</h5>
            <select class="form-select form-select-lg mb-3" id="leagueSelect">
                <option selected disabled>Choose a classic league...</option>
            </select>
            <button class="btn btn-success w-100" id="analyzeBtn">
                <i class="bi bi-search me-2"></i>Analyze League & Find Transfers
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div id="loading" class="text-center py-5 d-none">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
        <h5 class="mt-3" id="loadingText">Scanning league...</h5>
        <div class="progress mt-3 mx-auto" style="width: 300px; height: 6px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%"></div>
        </div>
    </div>

    <!-- Results -->
    <div id="results" class="d-none">
        
        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Teams Scanned</div>
                        <h3 class="fw-bold text-primary" id="teamsScanned">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Your Rank</div>
                        <h3 class="fw-bold" id="yourRank">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Points Behind #1</div>
                        <h3 class="fw-bold text-danger" id="pointsBehind">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Transfers Made</div>
                        <h3 class="fw-bold text-dark" id="transfersMade">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Suggested Moves</div>
                        <h3 class="fw-bold text-success" id="movesCount">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Suggestions -->
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
                <div><i class="bi bi-arrow-left-right me-2"></i>Recommended Transfers</div>
                <div class="small fw-normal text-muted">Based on League Ownership</div>
            </div>
            <div class="card-body" id="transferList">
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <h6 class="fw-bold text-warning mb-3"><i class="bi bi-lightning-charge-fill me-2"></i>Catch Up (Differentials)</h6>
                        <p class="small text-muted">High potential players that your rivals <strong>don't</strong> own.</p>
                        <div id="diffList"></div>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Block Rivals (Template)</h6>
                        <p class="small text-muted">High ownership players you're missing out on.</p>
                        <div id="blockList"></div>
                    </div>
                </div>
                <!-- Injected -->
            </div>
        </div>

        <!-- Ownership Insights -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-pie-chart me-2"></i>League Ownership Insights
            </div>
            <ul class="list-group list-group-flush" id="ownershipList">
                <!-- Injected -->
            </ul>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const loading = document.getElementById('loading');
    const results = document.getElementById('results');
    
    const managerIdInput = document.getElementById('managerId');
    const loadLeaguesBtn = document.getElementById('loadLeaguesBtn');
    const leagueSelect = document.getElementById('leagueSelect');
    const analyzeBtn = document.getElementById('analyzeBtn');

    let bootstrapData = null;
    let managerId = null;

    // Auto-load from URL
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('manager_id')) {
        managerIdInput.value = urlParams.get('manager_id');
        // Auto click load
        setTimeout(() => loadLeaguesBtn.click(), 500);
    }

    // Step 1: Load Leagues
    loadLeaguesBtn.addEventListener('click', async () => {
        managerId = managerIdInput.value.trim();
        if(!managerId) return alert('Enter Manager ID');

        loadLeaguesBtn.disabled = true;
        loadLeaguesBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const res = await fetch(`api.php?endpoint=entry/${managerId}/`);
            if(!res.ok) throw new Error('Manager not found');
            const data = await res.json();
            
            leagueSelect.innerHTML = '<option selected disabled>Choose a classic league...</option>';
            data.leagues.classic.forEach(l => {
                const opt = document.createElement('option');
                opt.value = l.id;
                opt.text = l.name;
                leagueSelect.appendChild(opt);
            });

            step2.classList.remove('d-none');

            // Auto Select League if in URL
            if(urlParams.get('league_id')) {
                const lid = urlParams.get('league_id');
                // check if exists
                if([...leagueSelect.options].some(o => o.value == lid)) {
                    leagueSelect.value = lid;
                    setTimeout(() => analyzeBtn.click(), 500);
                }
            }
            
        } catch (e) {
            alert(e.message);
        } finally {
            loadLeaguesBtn.disabled = false;
            loadLeaguesBtn.innerHTML = 'Load Leagues';
        }
    });

    // Step 2: Analyze
    analyzeBtn.addEventListener('click', async () => {
        const leagueId = leagueSelect.value;
        if(!leagueId) return alert('Select a league');

        step1.classList.add('d-none');
        step2.classList.add('d-none');
        loading.classList.remove('d-none');
        results.classList.add('d-none');

        const updateProgress = (pct, txt) => {
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('loadingText').innerText = txt;
        };

        try {
            // A. Fetch Static Data
            updateProgress(10, 'Loading FPL database...');
            if(!bootstrapData) {
                const res = await fetch('api.php?endpoint=bootstrap-static/');
                bootstrapData = await res.json();
            }
            const players = {};
            bootstrapData.elements.forEach(p => players[p.id] = p);
            const currentGw = bootstrapData.events.find(e => e.is_current)?.id || 1;

            // B. Fetch League Standings
            updateProgress(25, 'Loading league standings...');
            const standingsRes = await fetch(`api.php?endpoint=leagues-classic/${leagueId}/standings/`);
            const standingsData = await standingsRes.json();
            const allManagers = standingsData.standings.results;

            // Find user's rank and points
            const myEntry = allManagers.find(m => m.entry == managerId);
            const leader = allManagers[0];
            document.getElementById('yourRank').innerText = myEntry ? `#${myEntry.rank}` : 'N/A';
            document.getElementById('pointsBehind').innerText = myEntry ? (leader.total - myEntry.total) : '-';
            
            // Get Transfers Made
            const entryRes = await fetch(`api.php?endpoint=entry/${managerId}/`);
            const entryData = await entryRes.json();
            document.getElementById('transfersMade').innerText = entryData.last_deadline_total_transfers || 0;

            // C. Analyze Top 20 or all if small league
            const teamsToScan = allManagers.slice(0, Math.min(20, allManagers.length));
            updateProgress(40, `Scanning ${teamsToScan.length} teams...`);

            const playerOwnership = {};
            let scanned = 0;

            for (const mgr of teamsToScan) {
                try {
                    const picksRes = await fetch(`api.php?endpoint=entry/${mgr.entry}/event/${currentGw}/picks/`);
                    if(picksRes.ok) {
                        const picksData = await picksRes.json();
                        picksData.picks.forEach(p => {
                            playerOwnership[p.element] = (playerOwnership[p.element] || 0) + 1;
                        });
                        scanned++;
                    }
                } catch(e) { console.warn('Skip', e); }
            }
            document.getElementById('teamsScanned').innerText = scanned;

            // D. Fetch My Picks
            updateProgress(70, 'Comparing with your team...');
            const myPicksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${currentGw}/picks/`);
            const myPicksData = await myPicksRes.json();
            const myPlayerIds = new Set(myPicksData.picks.map(p => p.element));
            const mySquad = myPicksData.picks.map(p => players[p.element]);

            // E. Identify Transfers
            updateProgress(85, 'Generating transfer plan...');

            // Template players I'm missing (high ownership)
            const missingHighOwnership = Object.entries(playerOwnership)
                .filter(([id, count]) => !myPlayerIds.has(parseInt(id)) && (count / scanned) >= 0.3) // >30% ownership
                .map(([id, count]) => ({ player: players[id], ownership: count / scanned }))
                .sort((a, b) => b.ownership - a.ownership);
            
            // Differentials (Low League Ownership but High Form)
            // Available in FPL but owned by < 10% of league rivals
            // Filter global player list for this? Or just checking widely
            // We use global 'players' object
            const potentialDifferentials = Object.values(players)
                .filter(p => 
                    p.status === 'a' &&
                    parseFloat(p.form) > 3.0 && // Good form
                    !myPlayerIds.has(p.id) &&   // Don't own
                    ((playerOwnership[p.id] || 0) / scanned) < 0.1 // <10% league owned
                )
                .sort((a,b) => parseFloat(b.form) - parseFloat(a.form))
                .slice(0, 10);

            // My players with low ownership/form (potential sells)
            const lowOwnershipMine = mySquad
                .filter(p => {
                    const own = playerOwnership[p.id] || 0;
                    return (own / scanned) < 0.2 && parseFloat(p.form) < 4.0;
                })
                .sort((a, b) => parseFloat(a.form) - parseFloat(b.form));

            // F. Render Transfers
            updateProgress(100, 'Done!');
            renderTransfers(missingHighOwnership, potentialDifferentials, lowOwnershipMine, scanned, playerOwnership, players);

            loading.classList.add('d-none');
            results.classList.remove('d-none');

        } catch (e) {
            console.error(e);
            alert('Error: ' + e.message);
            loading.classList.add('d-none');
            step1.classList.remove('d-none');
        }
    });

    function renderTransfers(blockers, differentials, toSell, total, ownership, players) {
        // 1. Render Transfers
        const diffList = document.getElementById('diffList');
        const blockList = document.getElementById('blockList');
        if(diffList) diffList.innerHTML = '';
        if(blockList) blockList.innerHTML = '';

        let totalMoves = 0;

        // Helper to find best sell candidate for a buy target
        const findSellFor = (buyPlayer) => {
            // Find same position, cheaper or similar price (if possible), low form/ownership
            // or just the worst player in that position
            return toSell.find(s => s.element_type === buyPlayer.element_type) || null;
        };

        // Render Blockers
        if(blockers.length === 0 && blockList) blockList.innerHTML = '<p class="text-muted small">You own all the key template players!</p>';
        blockers.forEach(item => {
            if(blockList) {
                const p = item.player;
                const sellP = findSellFor(p);
                const card = createTransferCard(p, item.ownership, 'block', sellP);
                blockList.appendChild(card);
            }
            totalMoves++;
        });

        // Render Differentials
        if(differentials.length === 0 && diffList) diffList.innerHTML = '<p class="text-muted small">No hidden gems found currently.</p>';
        differentials.forEach(p => {
             const ownCount = ownership[p.id] || 0;
             if(diffList) {
                const sellP = findSellFor(p);
                const card = createTransferCard(p, ownCount/total, 'diff', sellP);
                diffList.appendChild(card);
             }
             totalMoves++;
        });

        const movesCountEl = document.getElementById('movesCount');
        if(movesCountEl) movesCountEl.innerText = totalMoves;

        // 2. Ownership Insights
        const ownershipList = document.getElementById('ownershipList');
        if(ownershipList) {
            ownershipList.innerHTML = '';

            // Top 5 most owned
            const topOwned = Object.entries(ownership)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);

            topOwned.forEach(([id, count]) => {
                const p = players[id]; 
                if(!p) return;
                const pct = Math.round(count / total * 100);
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                const posName = ['','GK','DEF','MID','FWD'][p.element_type] || '';
                li.innerHTML = `
                    <span><strong>${p.web_name}</strong> <span class="text-muted small">(${posName})</span></span>
                    <span class="badge bg-primary">${pct}%</span>
                `;
                ownershipList.appendChild(li);
            });
        }
    }

    function createTransferCard(p, ownPct, type, sellPlayer) {
        const div = document.createElement('div');
        div.className = `transfer-card card mb-3 ${type === 'diff' ? 'diff-card' : 'block-card'}`;
        const badgeColor = type === 'diff' ? 'bg-warning text-dark' : 'bg-success';
        const badgeText = type === 'diff' ? 'DIFF' : 'BLOCK';
        
        let sellHtml = '';
        if(sellPlayer) {
            sellHtml = `
                <div class="border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between align-items-center small">
                         <span class="text-danger fw-bold"><i class="bi bi-box-arrow-right me-1"></i>Sell: ${sellPlayer.web_name}</span>
                         <span class="text-muted">£${sellPlayer.now_cost/10}m</span>
                    </div>
                </div>
            `;
        } else {
             sellHtml = `
                <div class="border-top pt-2 mt-2">
                    <div class="small text-muted text-center fst-italic">No obvious sell candidate in this position</div>
                </div>
            `;
        }

        div.innerHTML = `
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                         <div class="fw-bold text-dark fs-5">${p.web_name} <span class="badge ${badgeColor} ms-1" style="font-size:0.6rem; vertical-align: middle;">${badgeText}</span></div>
                         <div class="small text-muted">Form: ${p.form} • £${p.now_cost/10}m</div>
                    </div>
                    <div class="text-end" style="min-width: 60px;">
                        <div class="fw-bold small">${Math.round(ownPct*100)}%</div>
                        <div class="progress" style="height: 4px; width: 60px">
                            <div class="progress-bar ${type==='diff'?'bg-warning':'bg-success'}" role="progressbar" style="width: ${Math.round(ownPct*100)}%"></div>
                        </div>
                        <div class="text-muted" style="font-size: 0.65rem">Owned</div>
                    </div>
                </div>
                ${sellHtml}
            </div>
        `;
        return div;
    }
</script>
</body>
</html>
