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
    <title>Player Comparison | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-4">
        <!-- Header -->
        <div class="hero-header mb-4 text-center">
            <h1 class="display-5 fw-bold mb-1">Player Comparison</h1>
            <p class="lead opacity-75 mb-0">Head-to-head stats analysis.</p>
        </div>

        <!-- Search Selection -->
        <div class="card bg-transparent border-0 mb-5">
            <div class="row justify-content-center g-3">
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <label class="form-label fw-bold small text-muted">PLAYER A</label>
                            <input type="text" id="inputA" class="form-control mb-2" placeholder="Search Player A...">
                            <div id="resultsA" class="list-group position-absolute w-100" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                            <div id="selectedA" class="mt-2 text-center d-none">
                                <div class="bg-light rounded p-2 border">
                                    <h5 class="m-0 fw-bold" id="nameA"></h5>
                                    <span class="badge bg-secondary" id="teamA"></span>
                                    <button class="btn btn-sm btn-close float-end" onclick="clearSelection('A')"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <div class="bg-primary rounded-circle p-3 shadow text-dark fw-bold">VS</div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <label class="form-label fw-bold small text-muted">PLAYER B</label>
                            <input type="text" id="inputB" class="form-control mb-2" placeholder="Search Player B...">
                            <div id="resultsB" class="list-group position-absolute w-100" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                             <div id="selectedB" class="mt-2 text-center d-none">
                                <div class="bg-light rounded p-2 border">
                                    <h5 class="m-0 fw-bold" id="nameB"></h5>
                                    <span class="badge bg-secondary" id="teamB"></span>
                                    <button class="btn btn-sm btn-close float-end" onclick="clearSelection('B')"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-primary px-5 fw-bold" id="compareBtn" disabled>Compare Players</button>
            </div>
        </div>
        
        <!-- Comparison Result -->
        <div id="comparisonResult" class="d-none">
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold" id="headA">Player A</span>
                    <span class="badge bg-light text-dark">VS</span>
                    <span class="fw-bold" id="headB">Player B</span>
                </div>
                <div class="card-body" id="compareBody">
                    <!-- Injected JS -->
                </div>
            </div>

            <!-- Seasonality Chart -->
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Points Over Gameweeks</h6>
                </div>
                <div class="card-body">
                    <canvas id="seasonalityChart" height="200"></canvas>
                </div>
            </div>

            <!-- Template Overlap -->
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Template Overlap</h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6" id="overlapA">
                            <!-- Player A template info -->
                        </div>
                        <div class="col-md-6" id="overlapB">
                            <!-- Player B template info -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let players = [];
    let playerA = null;
    let playerB = null;
    let teamsData = {};

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

    function getTeamLogoHtml(teamName, size = 18) {
        const logoPath = getTeamLogo(teamName);
        if (logoPath) {
            return `<img src="${logoPath}" alt="${teamName}" style="height: ${size}px; width: ${size}px; object-fit: contain;" class="me-1">`;
        }
        return '';
    }

    // Fetch players
    fetch('api.php?endpoint=bootstrap-static/')
        .then(r => r.json())
        .then(data => {
            data.teams.forEach(t => teamsData[t.id] = t);
            players = data.elements.map(p => ({
                ...p,
                team_name: data.teams.find(t => t.id === p.team).name,
                team_short: data.teams.find(t => t.id === p.team).short_name
            }));
        });

    // Valid metrics
    const metrics = [
        { key: 'now_cost', label: 'Price (£m)', fmt: v => '£' + (v/10).toFixed(1) + 'm' },
        { key: 'total_points', label: 'Total Points' },
        { key: 'form', label: 'Form (Last 30 days)' },
        { key: 'selected_by_percent', label: 'Ownership' , fmt: v => v + '%' },
        { key: 'goals_scored', label: 'Goals' },
        { key: 'assists', label: 'Assists' },
        { key: 'clean_sheets', label: 'Clean Sheets' },
        { key: 'minutes', label: 'Minutes Played' },
        { key: 'ict_index', label: 'ICT Index' },
        { key: 'points_per_game', label: 'Points Per Game' }
    ];

    function setupSearch(inputId, resultsId, side) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);

        input.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            if(val.length < 2) { results.innerHTML = ''; return; }
            
            const matches = players.filter(p => (p.first_name + ' ' + p.second_name).toLowerCase().includes(val)).slice(0, 5);
            
            results.innerHTML = '';
            matches.forEach(p => {
                const btn = document.createElement('button');
                btn.className = 'list-group-item list-group-item-action text-start';
                btn.innerHTML = `<span class="d-flex align-items-center gap-1">${getTeamLogoHtml(p.team_name)}<strong>${p.web_name}</strong> <small class='text-muted'>${p.team_short}</small></span>`;
                btn.onclick = () => selectPlayer(p, side);
                results.appendChild(btn);
            });
        });
    }
    
    setupSearch('inputA', 'resultsA', 'A');
    setupSearch('inputB', 'resultsB', 'B');

    function selectPlayer(p, side) {
        if(side === 'A') playerA = p;
        else playerB = p;

        document.getElementById(`input${side}`).classList.add('d-none');
        document.getElementById(`results${side}`).innerHTML = '';
        document.getElementById(`selected${side}`).classList.remove('d-none');
        document.getElementById(`name${side}`).innerText = p.web_name;
        document.getElementById(`team${side}`).innerText = p.team_name;

        checkCompare();
    }

    window.clearSelection = function(side) {
        if(side === 'A') playerA = null;
        else playerB = null;
        
        document.getElementById(`input${side}`).value = '';
        document.getElementById(`input${side}`).classList.remove('d-none');
        document.getElementById(`selected${side}`).classList.add('d-none');
        document.getElementById('compareBtn').disabled = true;
        document.getElementById('comparisonResult').classList.add('d-none');
    };

    function checkCompare() {
        if(playerA && playerB) document.getElementById('compareBtn').disabled = false;
    }

    document.getElementById('compareBtn').onclick = () => {
        const compareBody = document.getElementById('compareBody');
        compareBody.innerHTML = '';
        
        document.getElementById('headA').innerText = playerA.web_name;
        document.getElementById('headB').innerText = playerB.web_name;
        
        metrics.forEach(m => {
            const valA = parseFloat(playerA[m.key]) || 0;
            const valB = parseFloat(playerB[m.key]) || 0;
            
            const displayA = m.fmt ? m.fmt(valA) : valA;
            const displayB = m.fmt ? m.fmt(valB) : valB;
            
            // Calculate bar widths (percentage)
            const total = valA + valB;
            const pctA = total > 0 ? (valA / total) * 100 : 50;
            const pctB = total > 0 ? (valB / total) * 100 : 50;
            
            // Determine winner colors
            const colorA = valA >= valB ? '#0d6efd' : '#6c757d';
            const colorB = valB >= valA ? '#dc3545' : '#6c757d';
            const winnerA = valA > valB ? '<i class="bi bi-trophy-fill text-warning ms-1"></i>' : '';
            const winnerB = valB > valA ? '<i class="bi bi-trophy-fill text-warning ms-1"></i>' : '';

            const row = `
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold" style="color: ${colorA}">${displayA}${winnerA}</span>
                        <span class="text-muted small text-uppercase fw-bold">${m.label}</span>
                        <span class="fw-bold" style="color: ${colorB}">${displayB}${winnerB}</span>
                    </div>
                    <div class="d-flex" style="height: 12px; border-radius: 6px; overflow: hidden; background: #e9ecef;">
                        <div style="width: ${pctA}%; background: ${colorA}; transition: width 0.5s ease;"></div>
                        <div style="width: ${pctB}%; background: ${colorB}; transition: width 0.5s ease;"></div>
                    </div>
                </div>
            `;
            compareBody.innerHTML += row;
        });
        
        document.getElementById('comparisonResult').classList.remove('d-none');
        
        // Render seasonality chart
        renderSeasonalityChart(playerA, playerB);
        
        // Render template overlap
        renderTemplateOverlap(playerA, playerB);
    };

    let seasonalityChart = null;
    
    async function renderSeasonalityChart(pA, pB) {
        // Fetch player history
        const [histA, histB] = await Promise.all([
            fetchPlayerHistory(pA.id),
            fetchPlayerHistory(pB.id)
        ]);
        
        const labels = histA.map(h => `GW${h.round}`);
        const dataA = histA.map(h => h.total_points);
        const dataB = histB.map(h => h.total_points);
        
        const ctx = document.getElementById('seasonalityChart').getContext('2d');
        
        // Destroy existing chart
        if(seasonalityChart) seasonalityChart.destroy();
        
        seasonalityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: pA.web_name,
                        data: dataA,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: pB.web_name,
                        data: dataB,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Points' }
                    }
                }
            }
        });
    }
    
    async function fetchPlayerHistory(playerId) {
        try {
            const res = await fetch(`api.php?endpoint=element-summary/${playerId}/`);
            const data = await res.json();
            return data.history || [];
        } catch(e) {
            console.error('Failed to fetch history:', e);
            return [];
        }
    }
    
    function renderTemplateOverlap(pA, pB) {
        const overlapA = document.getElementById('overlapA');
        const overlapB = document.getElementById('overlapB');
        
        // Calculate template characteristics
        const ownA = parseFloat(pA.selected_by_percent) || 0;
        const ownB = parseFloat(pB.selected_by_percent) || 0;
        
        const templateThreshold = 10; // >10% is template
        
        overlapA.innerHTML = `
            <div class="p-3 rounded ${ownA > templateThreshold ? 'bg-primary bg-opacity-10 border border-primary' : 'bg-light'}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <strong class="text-primary">${pA.web_name}</strong>
                    ${ownA > templateThreshold ? '<span class="badge bg-primary">Template</span>' : '<span class="badge bg-secondary">Differential</span>'}
                </div>
                <div class="mb-2">
                    <small class="text-muted">Ownership</small>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: ${ownA}%;"></div>
                    </div>
                    <small class="fw-bold">${ownA}%</small>
                </div>
                <p class="small text-muted mb-0">
                    ${ownA > 30 ? 'Very high ownership - essential pick' : ownA > 15 ? 'Solid template player' : 'Differential pick - could gain ranks'}
                </p>
            </div>
        `;
        
        overlapB.innerHTML = `
            <div class="p-3 rounded ${ownB > templateThreshold ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-light'}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <strong class="text-danger">${pB.web_name}</strong>
                    ${ownB > templateThreshold ? '<span class="badge bg-danger">Template</span>' : '<span class="badge bg-secondary">Differential</span>'}
                </div>
                <div class="mb-2">
                    <small class="text-muted">Ownership</small>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: ${ownB}%;"></div>
                    </div>
                    <small class="fw-bold">${ownB}%</small>
                </div>
                <p class="small text-muted mb-0">
                    ${ownB > 30 ? 'Very high ownership - essential pick' : ownB > 15 ? 'Solid template player' : 'Differential pick - could gain ranks'}
                </p>
            </div>
        `;
    }

</script>
</body>
</html>
