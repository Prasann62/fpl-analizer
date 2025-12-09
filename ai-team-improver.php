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
        .transfer-card {
            border-left: 4px solid var(--accent-color);
            transition: transform 0.2s;
        }
        .transfer-card:hover {
            transform: translateX(5px);
        }
        .player-out {
            background: rgba(220, 53, 69, 0.1);
        }
        .player-in {
            background: rgba(25, 135, 84, 0.1);
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
                        <div class="text-muted small text-uppercase">Suggested Moves</div>
                        <h3 class="fw-bold text-success" id="movesCount">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Suggestions -->
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold d-flex align-items-center">
                <i class="bi bi-arrow-left-right me-2"></i>
                Recommended Transfers
            </div>
            <div class="card-body" id="transferList">
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
                .filter(([id, count]) => !myPlayerIds.has(parseInt(id)) && count >= scanned * 0.4)
                .map(([id, count]) => ({ player: players[id], ownership: count / scanned }))
                .sort((a, b) => b.ownership - a.ownership);

            // My players with low ownership (potential sells)
            const lowOwnershipMine = mySquad
                .filter(p => {
                    const own = playerOwnership[p.id] || 0;
                    return (own / scanned) < 0.2 && parseFloat(p.form) < 4.0;
                })
                .sort((a, b) => parseFloat(a.form) - parseFloat(b.form));

            // F. Render Transfers
            updateProgress(100, 'Done!');
            renderTransfers(missingHighOwnership, lowOwnershipMine, scanned, playerOwnership, players);

            loading.classList.add('d-none');
            results.classList.remove('d-none');

        } catch (e) {
            console.error(e);
            alert('Error: ' + e.message);
            loading.classList.add('d-none');
            step1.classList.remove('d-none');
        }
    });

    function renderTransfers(toBuy, toSell, total, ownership, players) {
        const container = document.getElementById('transferList');
        container.innerHTML = '';

        let movesCount = 0;

        // Match sells to buys by position
        const maxMoves = Math.min(3, toSell.length, toBuy.length);

        for (let i = 0; i < maxMoves; i++) {
            const outPlayer = toSell[i];
            // Find a buy in same position
            const inPlayer = toBuy.find(b => b.player.element_type === outPlayer.element_type && b.player.now_cost <= outPlayer.now_cost + 10);
            
            if (inPlayer) {
                movesCount++;
                const card = document.createElement('div');
                card.className = 'transfer-card card mb-3';
                card.innerHTML = `
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-5 p-3 player-out">
                                <div class="small text-danger text-uppercase fw-bold">Transfer Out</div>
                                <div class="fw-bold fs-5">${outPlayer.web_name}</div>
                                <div class="text-muted small">Form: ${outPlayer.form} | £${outPlayer.now_cost/10}m</div>
                                <div class="ownership-bar mt-2">
                                    <div class="ownership-fill bg-danger" style="width: ${Math.round((ownership[outPlayer.id]||0)/total*100)}%"></div>
                                </div>
                                <div class="small text-muted mt-1">${Math.round((ownership[outPlayer.id]||0)/total*100)}% league ownership</div>
                            </div>
                            <div class="col-2 text-center">
                                <i class="bi bi-arrow-right fs-2 text-muted"></i>
                            </div>
                            <div class="col-5 p-3 player-in">
                                <div class="small text-success text-uppercase fw-bold">Transfer In</div>
                                <div class="fw-bold fs-5">${inPlayer.player.web_name}</div>
                                <div class="text-muted small">Form: ${inPlayer.player.form} | £${inPlayer.player.now_cost/10}m</div>
                                <div class="ownership-bar mt-2">
                                    <div class="ownership-fill bg-success" style="width: ${Math.round(inPlayer.ownership*100)}%"></div>
                                </div>
                                <div class="small text-muted mt-1">${Math.round(inPlayer.ownership*100)}% league ownership</div>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(card);

                // Remove used buy
                toBuy = toBuy.filter(b => b.player.id !== inPlayer.player.id);
            }
        }

        if (movesCount === 0) {
            container.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Your team is already well-optimized for this league! No critical transfers needed.</div>';
        }

        document.getElementById('movesCount').innerText = movesCount;

        // Ownership Insights
        const ownershipList = document.getElementById('ownershipList');
        ownershipList.innerHTML = '';

        // Top 5 most owned
        const topOwned = Object.entries(ownership)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 5);

        topOwned.forEach(([id, count]) => {
            const p = players[id];
            const pct = Math.round(count / total * 100);
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span><strong>${p.web_name}</strong> <span class="text-muted small">(${['GK','DEF','MID','FWD'][p.element_type-1]})</span></span>
                <span class="badge bg-primary">${pct}%</span>
            `;
            ownershipList.appendChild(li);
        });
    }
</script>
</body>
</html>
