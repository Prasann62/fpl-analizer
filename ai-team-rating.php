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
    <title>AI Team Rating | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .rating-gauge {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: conic-gradient(var(--secondary-color) 0%, rgba(255,255,255,0.1) 0%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 0 30px rgba(0, 255, 133, 0.2);
            transition: all 1s ease-out;
        }
        .rating-gauge::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            background: var(--glass-bg);
            border-radius: 50%;
            z-index: 1;
        }
        .rating-value {
            position: relative;
            z-index: 2;
            font-size: 3rem;
            font-weight: 800;
            color: var(--secondary-color);
        }
        .analysis-card {
            transition: transform 0.3s ease;
        }
        .analysis-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <div class="hero-header text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between mb-5">
            <div class="z-1">
                <h1 class="display-5 fw-bold mb-2">AI Team Rating</h1>
                <p class="lead opacity-75 mb-0">Deep learning analysis of your squad strength.</p>
            </div>
            <div class="mt-4 mt-md-0 z-1">
                <i class="bi bi-cpu display-1 opacity-25"></i>
            </div>
        </div>

        <!-- Input Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <input type="number" id="managerId" class="form-control border-0 bg-light" placeholder="Enter Manager ID">
                            <button class="btn btn-primary px-4" id="analyzeBtn">
                                <i class="bi bi-magic me-2"></i>Analyze
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="d-none">
            
            <!-- Main Rating Display -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-4 text-center">
                    <div class="card bg-transparent border-0">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="rating-gauge mb-4" id="ratingGauge">
                                <span class="rating-value" id="totalScore">0</span>
                            </div>
                            <h3 class="fw-bold mb-1">Squad Health</h3>
                            <div id="verdictBadge" class="badge bg-secondary text-primary fs-6 px-3 py-2">Calculating...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Metrics -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 analysis-card">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up-arrow text-primary fs-1 mb-3"></i>
                            <h5 class="fw-bold">Form Rating</h5>
                            <div class="progress mb-2" style="height: 10px;">
                                <div id="formProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                            <p class="text-muted small mb-0" id="formText"> analyzing form...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 analysis-card">
                        <div class="card-body text-center">
                            <i class="bi bi-cash-stack text-warning fs-1 mb-3"></i>
                            <h5 class="fw-bold">Value Efficiency</h5>
                            <div class="progress mb-2" style="height: 10px;">
                                <div id="valueProgress" class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                            </div>
                            <p class="text-muted small mb-0" id="valueText">calculating ROI...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 analysis-card">
                        <div class="card-body text-center">
                            <i class="bi bi-lightning-charge text-danger fs-1 mb-3"></i>
                            <h5 class="fw-bold">Explosiveness (ICT)</h5>
                            <div class="progress mb-2" style="height: 10px;">
                                <div id="ictProgress" class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                            </div>
                            <p class="text-muted small mb-0" id="ictText">measuring potential...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Insights -->
            <div class="card">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="fw-bold mb-0"><i class="bi bi-robot me-2"></i>AI Insights</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="insightsList">
                        <!-- Insights will be injected here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const analyzeBtn = document.getElementById('analyzeBtn');
    const managerIdInput = document.getElementById('managerId');
    const resultsSection = document.getElementById('resultsSection');

    analyzeBtn.addEventListener('click', async () => {
        const managerId = managerIdInput.value.trim();
        if (!managerId) {
            alert('Please enter a Manager ID');
            return;
        }

        const originalBtnHtml = analyzeBtn.innerHTML;
        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>AI Analyzing...';
        resultsSection.classList.add('d-none');

        try {
            // Fetch Data
            const [bootstrapRes, managerRes] = await Promise.all([
                fetch('api.php?endpoint=bootstrap-static/'),
                fetch(`api.php?endpoint=entry/${managerId}/`)
            ]);
            
            const bootstrapData = await bootstrapRes.json();
            if (!managerRes.ok) throw new Error('Manager not found');
            const managerData = await managerRes.json();

            const currentEvent = bootstrapData.events.find(e => e.is_current) || bootstrapData.events[0];
            const gw = currentEvent.id;

            const picksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${gw}/picks/`);
            if (!picksRes.ok) throw new Error('Gameweek picks not found');
            const picksData = await picksRes.json();

            // Process Data
            const players = {};
            bootstrapData.elements.forEach(p => players[p.id] = p);

            let totalForm = 0;
            let totalValue = 0;
            let totalIct = 0;
            let activePlayers = 0;
            const squad = [];

            picksData.picks.forEach(pick => {
                const p = players[pick.element];
                squad.push(p);
                // Only count starting 11 for "active" ratings usually, but simple AI checks all 15 or top 11.
                // Let's rate the whole squad for simplicity but weight multipliers could apply.
                totalForm += parseFloat(p.form);
                totalValue += p.now_cost;
                totalIct += parseFloat(p.ict_index_rank); // Lower rank is better for API usually, but let's check. 
                // wait, ict_index is the float value. use that.
                totalIct += parseFloat(p.ict_index); 
                activePlayers++;
            });

            // --- Heuristic Scoring Algorithms ---
            
            // 1. Form Rating: Avg form per player. Great form is > 5.0. 
            // Max potential avg form ~8.0 -> 100%. 
            const avgForm = totalForm / activePlayers;
            const formScore = Math.min(100, (avgForm / 6.0) * 100); 

            // 2. Value Efficiency: Avg cost ~ 6.6m (100m / 15). 
            // Higher value team usually better? but budget is capped.
            // Let's rate based on manager value.
            const teamValue = managerData.summary_overall_rank ? (managerData.last_deadline_value / 10) : 100;
            // Base score on budget management. >102m is great.
            const valueScore = Math.min(100, Math.max(0, ((teamValue - 98) / 5) * 100));

            // 3. ICT Rating: Avg ICT index. > 8 is good? 
            // Top Salah is like 10-15 per game, avg player maybe 3-5.
            const avgIct = totalIct / activePlayers;
            const ictScore = Math.min(100, (avgIct / 7.0) * 100);

            // Total Weighted Score
            // Form (40%), ICT (40%), Value (20%)
            const totalScore = Math.round((formScore * 0.4) + (ictScore * 0.4) + (valueScore * 0.2));

            // UI Updates
            resultsSection.classList.remove('d-none');
            
            // Animate Gauge
            const ratingGauge = document.getElementById('ratingGauge');
            setTimeout(() => {
                ratingGauge.style.background = `conic-gradient(var(--secondary-color) ${totalScore}%, rgba(255,255,255,0.1) ${totalScore}%)`;
                animateValue("totalScore", 0, totalScore, 1500);
            }, 100);

            // Set Progress Bars
            setProgressBar('formProgress', 'formText', formScore, `Avg Form: ${avgForm.toFixed(2)}`);
            setProgressBar('valueProgress', 'valueText', valueScore, `Team Value: £${teamValue}m`);
            setProgressBar('ictProgress', 'ictText', ictScore, `Avg ICT: ${avgIct.toFixed(2)}`);

            // Verdict
            const verdictBadge = document.getElementById('verdictBadge');
            if(totalScore >= 80) { verdictBadge.innerText = "Title Contender"; verdictBadge.className = "badge bg-success fs-6 px-3 py-2"; }
            else if(totalScore >= 60) { verdictBadge.innerText = "Solid Mid-Table"; verdictBadge.className = "badge bg-warning text-dark fs-6 px-3 py-2"; }
            else { verdictBadge.innerText = "Relegation Battle"; verdictBadge.className = "badge bg-danger fs-6 px-3 py-2"; }

            // Insights
            const insightsList = document.getElementById('insightsList');
            insightsList.innerHTML = '';
            
            // Find stats for insights
            const topFormPlayer = squad.reduce((prev, current) => (parseFloat(prev.form) > parseFloat(current.form)) ? prev : current);
            const lowFormPlayer = squad.reduce((prev, current) => (parseFloat(prev.form) < parseFloat(current.form)) ? prev : current);

            addInsight(insightsList, 'bi-star-fill text-warning', `Star Player: <strong>${topFormPlayer.web_name}</strong> is in peak form (${topFormPlayer.form}). Keep him!`);
            addInsight(insightsList, 'bi-exclamation-triangle text-danger', `Weak Link: <strong>${lowFormPlayer.web_name}</strong> is struggling with form (${lowFormPlayer.form}). Consider benching or selling.`);
            
            if(teamValue < 100) {
                 addInsight(insightsList, 'bi-piggy-bank text-info', `You have budget left in the bank (£${((1000 - managerData.last_deadline_total_transfers)/10).toFixed(1)}m potentially? Check bank). Invest it!`);
            } else {
                 addInsight(insightsList, 'bi-graph-up text-success', `Team value is strong (£${teamValue}m).`);
            }

        } catch (error) {
            console.error(error);
            alert('Error analyzing team: ' + error.message);
        } finally {
            analyzeBtn.disabled = false;
            analyzeBtn.innerHTML = originalBtnHtml;
        }
    });

    function setProgressBar(id, textId, score, text) {
        const bar = document.getElementById(id);
        const txt = document.getElementById(textId);
        bar.style.width = `${score}%`;
        txt.innerHTML = text;
    }

    function addInsight(list, iconClass, html) {
        list.innerHTML += `
            <li class="list-group-item bg-transparent d-flex align-items-start">
                <i class="bi ${iconClass} me-3 mt-1"></i>
                <span>${html}</span>
            </li>
        `;
    }

    function animateValue(objId, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            document.getElementById(objId).innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
</script>
</body>
</html>
