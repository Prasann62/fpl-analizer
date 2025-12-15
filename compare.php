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
    <link href="style.css" rel="stylesheet">
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
        
        <!-- Comparison Table -->
        <div id="comparisonResult" class="d-none">
            <div class="card border-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="w-25" id="headA">Player A</th>
                                <th class="w-50">Metric</th>
                                <th class="w-25" id="headB">Player B</th>
                            </tr>
                        </thead>
                        <tbody id="compareBody">
                            <!-- Injected JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let players = [];
    let playerA = null;
    let playerB = null;

    // Fetch players
    fetch('api.php?endpoint=bootstrap-static/')
        .then(r => r.json())
        .then(data => {
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
                btn.innerHTML = `<strong>${p.web_name}</strong> <small class='text-muted'>${p.team_short}</small>`;
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
        const tbody = document.getElementById('compareBody');
        tbody.innerHTML = '';
        
        document.getElementById('headA').innerText = playerA.web_name;
        document.getElementById('headB').innerText = playerB.web_name;
        
        metrics.forEach(m => {
            const valA = parseFloat(playerA[m.key]);
            const valB = parseFloat(playerB[m.key]);
            
            const displayA = m.fmt ? m.fmt(valA) : valA;
            const displayB = m.fmt ? m.fmt(valB) : valB;
            
            // Highlight winner
            let classA = '';
            let classB = '';
            if(valA > valB) classA = 'bg-success bg-opacity-10 fw-bold text-success';
            if(valB > valA) classB = 'bg-success bg-opacity-10 fw-bold text-success';

            const row = `
                <tr>
                    <td class="${classA}">${displayA}</td>
                    <td class="fw-bold text-muted small text-uppercase">${m.label}</td>
                    <td class="${classB}">${displayB}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
        
        document.getElementById('comparisonResult').classList.remove('d-none');
    };

</script>
</body>
</html>
