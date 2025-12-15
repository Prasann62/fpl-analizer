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
    <title>AI League Beater | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .pitch-bg {
            background: linear-gradient(0deg, #1a4d2e 0%, #2a7a4b 50%, #1a4d2e 100%);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            padding: 2rem 1rem;
            position: relative;
        }
        .pitch-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255,255,255,0.2);
        }
        .player-card {
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            padding: 0.5rem;
            text-align: center;
            font-size: 0.8rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
            margin-bottom: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        .player-card:hover {
            transform: scale(1.1);
            z-index: 10;
        }
        .player-pos {
            font-size: 0.65rem;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
        }
        .player-name {
            font-weight: 800;
            color: var(--primary-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .player-meta {
            font-size: 0.7rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }
        .differential-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--accent-color);
            color: white;
            font-size: 0.6rem;
            padding: 2px 4px;
            border-bottom-left-radius: 4px;
        }
        .template-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--secondary-color);
            color: var(--primary-color);
            font-size: 0.6rem;
            padding: 2px 4px;
            border-bottom-left-radius: 4px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <div class="hero-header row mb-5 align-items-center justify-content-between">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-2">League Beater AI</h1>
                <p class="lead opacity-75">Analyze your league's top teams and build a squad to overtake them.</p>
            </div>
            <div class="col-lg-4">
                 <div class="card bg-opacity-10 border-0" style="background: rgba(255,255,255,0.05);">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Target Strategy: Top 3 Finish</h6>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-secondary text-dark me-2">Template</span>
                            <small class="text-muted">Covering high-owned players</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-2">Differential</span>
                            <small class="text-muted">High upside, low ownership</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Manager ID -->
        <div class="card mb-4" id="step1">
            <div class="card-body">
                <h5 class="card-title fw-bold">1. Load Manager</h5>
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
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-grow-1" id="analyzeBtn">
                        <i class="bi bi-cpu me-2"></i>Analyze & Pick Team
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;"></div>
            <h4 class="mt-4 fw-bold animate-pulse" id="loadingText">Connecting to FPL...</h4>
            <div class="progress mt-3 mx-auto" style="width: 50%; height: 6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Step 3: Results -->
        <div id="results" class="d-none">
            
            <!-- Stats Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                     <div class="card h-100 text-center border-primary">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small">Teams Analyzed</h6>
                            <h2 class="fw-bold text-primary" id="countAnalyzed">0</h2>
                        </div>
                     </div>
                </div>
                 <div class="col-md-4">
                     <div class="card h-100 text-center border-secondary">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small">Template Strength</h6>
                            <h2 class="fw-bold text-secondary" id="templateScore">0%</h2>
                        </div>
                     </div>
                </div>
                 <div class="col-md-4">
                     <div class="card h-100 text-center border-danger">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small">Differentials</h6>
                            <h2 class="fw-bold text-danger" id="diffCount">0</h2>
                        </div>
                     </div>
                </div>
            </div>

            <div class="row">
                <!-- Pitch View -->
                <div class="col-lg-8 mx-auto">
                    <div class="pitch-bg shadow-lg" id="pitch">
                        <div class="pitch-line"></div>
                        <div class="row text-center mb-3 justify-content-center" id="gkpRow"></div>
                        <div class="row text-center mb-3 justify-content-center" id="defRow"></div>
                        <div class="row text-center mb-3 justify-content-center" id="midRow"></div>
                        <div class="row text-center justify-content-center" id="fwdRow"></div>
                    </div>
                </div>
                
                <!-- Bench & Details -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Selection Logic
                        </div>
                        <ul class="list-group list-group-flush" id="logicList">
                            <!-- injected -->
                        </ul>
                    </div>
                    
                    <div class="card mt-3 shadow-sm bg-light">
                        <div class="card-body text-center">
                             <small class="text-muted text-uppercase fw-bold">Total Cost</small>
                             <h3 class="fw-bold mb-0">£<span id="totalCost">0.0</span>m</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Elements
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const loading = document.getElementById('loading');
    const results = document.getElementById('results');
    
    const managerIdInput = document.getElementById('managerId');
    const loadLeaguesBtn = document.getElementById('loadLeaguesBtn');
    const leagueSelect = document.getElementById('leagueSelect');
    const analyzeBtn = document.getElementById('analyzeBtn');

    // State
    let bootstrapData = null;
    let selectedLeagueId = null;
    let managerId = null;

    // 1. Load Leagues
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

    // 2. Analyze
    analyzeBtn.addEventListener('click', async () => {
        selectedLeagueId = leagueSelect.value;
        if(!selectedLeagueId) return alert("Select a league");

        // UI Reset
        results.classList.add('d-none');
        loading.classList.remove('d-none');
        step1.classList.add('d-none');
        step2.classList.add('d-none');
        
        const updateProgress = (pct, txt) => {
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('loadingText').innerText = txt;
        };

        try {
            // A. Fetch Static Data
            updateProgress(10, 'Fetching FPL Database...');
            if(!bootstrapData) {
                const res = await fetch('api.php?endpoint=bootstrap-static/');
                bootstrapData = await res.json();
            }
            const players = {};
            bootstrapData.elements.forEach(p => players[p.id] = p);
            const currentGw = bootstrapData.events.find(e => e.is_current)?.id || 1;

            // B. Fetch League Standings
            updateProgress(30, 'Scanning League Table...');
            const standingsRes = await fetch(`api.php?endpoint=leagues-classic/${selectedLeagueId}/standings/`);
            const standingsData = await standingsRes.json();
            
            // Limit to Top 10 managers to analyze "Top" meta
            const topManagers = standingsData.standings.results.slice(0, 10);
            
            // C. Analyze Ownership
            updateProgress(50, `Profiling Top ${topManagers.length} Teams...`);
            const playerCounts = {};
            let managersScanned = 0;

            for (const mgr of topManagers) {
                try {
                    // Slight delay to be nice to API? 
                    // Javascript is async, but we want to wait for response usually.
                    const picksRes = await fetch(`api.php?endpoint=entry/${mgr.entry}/event/${currentGw}/picks/`);
                    if(picksRes.ok) {
                        const picksData = await picksRes.json();
                        picksData.picks.forEach(p => {
                            playerCounts[p.element] = (playerCounts[p.element] || 0) + 1;
                        });
                        managersScanned++;
                    }
                } catch(e) { console.warn('Skipped a manager due to error', e); }
            }

            // D. Categorize Players
            updateProgress(80, 'Calculating Optimal Squad...');
            const templatePlayers = []; // > 40% ownership in top 10
            const differentialPlayers = []; // < 20% ownership but high form

            Object.keys(playerCounts).forEach(pid => {
                pid = parseInt(pid);
                const ownership = playerCounts[pid] / managersScanned;
                const p = players[pid];
                
                if (ownership >= 0.4) {
                    p.ownPct = ownership;
                    p.isTemplate = true;
                    templatePlayers.push(p);
                }
            });

            // Find differentials (Available players, high form, NOT in template)
            const allPlayers = Object.values(players).filter(p => 
                p.status === 'a' && 
                !templatePlayers.some(tp => tp.id === p.id) &&
                parseFloat(p.form) > 3.0 // Decent form threshold
            );
            
            // Sort by form
            allPlayers.sort((a,b) => parseFloat(b.form) - parseFloat(a.form));
            // Take top 20 potential differentials
            const topDifferentials = allPlayers.slice(0, 20);

            // E. Build League Beater Team (Existing logic)
            const team = { 1:[], 2:[], 3:[], 4:[] };
            const maxLimits = { 1:2, 2:5, 3:5, 4:3 };
            let currentCost = 0;
            let teamCount = {};
            let totalPlayers = 0;
            let diffCount = 0;

            // ... (keep existing build logic mostly same, but let's re-run it for clarity or mostly rely on previous structure)
            // Re-implementing the build loop clearly here for the Replacement
            
            // 1. Force High-Owned Template
            templatePlayers.sort((a,b) => b.ownPct - a.ownPct);
            for (const p of templatePlayers) {
                if(totalPlayers >= 11) break; // Leave 4 spots for differentials/others
                if(addPlayer(p, team, maxLimits, teamCount, 10000)) {
                    totalPlayers++;
                }
            }

            // 2. Fill with Differentials
            for (const p of topDifferentials) {
                if(totalPlayers >= 15) break;
                if(addPlayer(p, team, maxLimits, teamCount, 10000)) {
                    totalPlayers++;
                    p.isDifferential = true;
                    diffCount++;
                }
            }

            // 3. Fill gaps
            if(totalPlayers < 15) {
                 allPlayers.forEach(p => {
                    if(totalPlayers >= 15) return;
                    if(addPlayer(p, team, maxLimits, teamCount, 10000)) totalPlayers++;
                 });
            }

            // --- F. New: Personal Analysis ---
            updateProgress(90, 'Comparing with your squad...');
            
            // Fetch User's Picks
            const myPicksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${currentGw}/picks/`);
            const myPicksData = await myPicksRes.json();
            const myPlayerIds = new Set(myPicksData.picks.map(p => p.element));

            // 1. Identify "Must Haves" (Template players you don't own)
            const missingTemplate = templatePlayers.filter(p => !myPlayerIds.has(p.id));
            
            // 2. Identify "Sell Candidates" (Your players not in template & low form)
            const mySquad = myPicksData.picks.map(p => players[p.element]);
            const sellCandidates = mySquad.filter(p => 
                !p.isTemplate && // Not a template player
                parseFloat(p.form) < 3.5 && // Poor form
                !topDifferentials.some(d => d.id === p.id) // Not a recognized high-potential differential
            );
            sellCandidates.sort((a,b) => parseFloat(a.form) - parseFloat(b.form)); // Worst first

            // Render Results
            updateProgress(100, 'Done!');
            renderPitch(team);
            renderTransferSuggestions(missingTemplate, sellCandidates);
            
            document.getElementById('countAnalyzed').innerText = managersScanned;
            const myTemplateCount = Object.values(team).flat().filter(p => p.isTemplate).length;
            document.getElementById('templateScore').innerText = Math.round((myTemplateCount/15)*100) + '%';
            document.getElementById('diffCount').innerText = diffCount; 
            document.getElementById('totalCost').innerText = (currentCost/10).toFixed(1);

            loading.classList.add('d-none');
            results.classList.remove('d-none');

            function addPlayer(p, teamObj, limitsObj, countsObj, maxBudget) {
                const pos = p.element_type;
                if(teamObj[pos].length >= limitsObj[pos]) return false;
                if((countsObj[p.team] || 0) >= 3) return false;
                teamObj[pos].push(p);
                countsObj[p.team] = (countsObj[p.team] || 0) + 1;
                currentCost += p.now_cost;
                return true;
            }

        } catch (e) {
            console.error(e);
            alert('Analysis failed: ' + e.message);
            loading.classList.add('d-none');
            step1.classList.remove('d-none');
        }
    });

    function renderPitch(team) {
        ['gkpRow','defRow','midRow','fwdRow'].forEach((id, idx) => {
            const pos = idx + 1;
            const container = document.getElementById(id);
            container.innerHTML = '';
            
            team[pos].forEach(p => {
                const col = document.createElement('div');
                col.className = 'col-auto mb-2'; 
                let badge = '';
                if(p.isTemplate) badge = '<div class="template-badge">TMP</div>';
                if(p.isDifferential) badge = '<div class="differential-badge">DIFF</div>';

                col.innerHTML = `
                    <div class="player-card" style="width: 100px;">
                        ${badge}
                        <div class="player-pos">${p.web_name}</div>
                        <div class="player-meta text-muted">
                            <span>£${p.now_cost/10}</span>
                            <span class="fw-bold text-dark">${p.form}</span>
                        </div>
                    </div>
                `;
                container.appendChild(col);
            });
        });
    }

    function renderTransferSuggestions(missing, selling) {
        const list = document.getElementById('logicList');
        list.innerHTML = ''; // basic logic list cleared

        const container = document.createElement('div');
        container.className = 'mt-3';
        
        // Header
        container.innerHTML = `<h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-arrow-down-up me-2"></i>Action Plan to Top 10</h6>`;
        
        // 1. Buy Logic
        if(missing.length > 0) {
            const topTarget = missing[0];
            const div = document.createElement('div');
            div.className = 'alert alert-success d-flex align-items-center mb-2 p-2';
            div.innerHTML = `
                <i class="bi bi-bag-plus-fill fs-3 me-3"></i>
                <div>
                    <div class="small text-uppercase fw-bold opacity-75">Priority Buy</div>
                    <div class="fw-bold">${topTarget.web_name}</div>
                    <div class="small">Owned by ${Math.round(topTarget.ownPct * 100)}% of Top 10</div>
                </div>
            `;
            container.appendChild(div);
            
            // Add to list log
            list.innerHTML += `<li class="list-group-item"><strong>Risk Alert:</strong> You don't own <strong>${topTarget.web_name}</strong>, which is dangerous as most top managers do.</li>`;
        }

        // 2. Sell Logic
        if(selling.length > 0) {
            const topSell = selling[0];
            const div = document.createElement('div');
            div.className = 'alert alert-danger d-flex align-items-center mb-2 p-2';
            div.innerHTML = `
                <i class="bi bi-bag-dash-fill fs-3 me-3"></i>
                <div>
                    <div class="small text-uppercase fw-bold opacity-75">Consider Selling</div>
                    <div class="fw-bold">${topSell.web_name}</div>
                    <div class="small">Poor form (${topSell.form}) & low ownership</div>
                </div>
            `;
            container.appendChild(div);

             // Add to list log
             list.innerHTML += `<li class="list-group-item"><strong>Dead Weight:</strong> <strong>${topSell.web_name}</strong> is dragging your rank down. Use funds to upgrade.</li>`;
        } else {
             list.innerHTML += `<li class="list-group-item text-success"><strong>Team Check:</strong> Your current squad actually looks quite clean! Focus on rolling transfers.</li>`;
        }

        document.getElementById('logicList').parentElement.appendChild(container);
    }

</script>
</body>
</html>
