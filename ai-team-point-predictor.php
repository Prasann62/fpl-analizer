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
    <title>AI Point Predictor | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <div class="hero-header mb-5 text-center">
            <h1 class="display-5 fw-bold mb-2">Points Predictor</h1>
            <p class="lead opacity-75">Forecast your upcoming gameweek score.</p>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <div class="input-group input-group-lg">
                    <input type="number" id="managerId" class="form-control" placeholder="Enter Manager ID">
                    <button class="btn btn-primary" id="predictBtn">Predict GW Score</button>
                </div>
            </div>
        </div>

        <div id="predictionResult" class="d-none text-center">
            <div class="display-1 fw-bold text-primary mb-3" id="predictedScore">--</div>
            <p class="h4 text-muted mb-5">Predicted Points</p>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-white fw-bold">Player Breakdown</div>
                        <ul class="list-group list-group-flush text-start" id="breakdownList">
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const predictBtn = document.getElementById('predictBtn');
    const managerIdInput = document.getElementById('managerId');
    const resultDiv = document.getElementById('predictionResult');
    const scoreDisplay = document.getElementById('predictedScore');
    const list = document.getElementById('breakdownList');

    predictBtn.addEventListener('click', async () => {
        const nid = managerIdInput.value.trim();
        if(!nid) return;

        predictBtn.disabled = true;
        predictBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Simulating...';
        
        try {
            // 1. Fetch Static Data
            const staticRes = await fetch('api.php?endpoint=bootstrap-static/');
            const staticData = await staticRes.json();
            
            const currentEvent = staticData.events.find(e => e.is_current) || staticData.events[0];
            const nextEvent = staticData.events.find(e => e.is_next) || currentEvent;
            const gw = nextEvent.id; // Predict for NEXT gameweek usually, or current if live
            
            // 2. Fetch User Picks
            const picksRes = await fetch(`api.php?endpoint=entry/${nid}/event/${currentEvent.id}/picks/`);
            if(!picksRes.ok) throw new Error("No picks found");
            const picksData = await picksRes.json();

            // 3. Fetch Fixtures for target GW
            const fixturesRes = await fetch(`api.php?endpoint=fixtures/?event=${gw}`);
            const fixtures = await fixturesRes.json();

            // Map data
            const players = {};
            staticData.elements.forEach(p => players[p.id] = p);
            
            const teamFixtures = {}; // teamId -> difficulty
            fixtures.forEach(f => {
                teamFixtures[f.team_h] = f.team_h_difficulty;
                teamFixtures[f.team_a] = f.team_a_difficulty;
            });

            let totalPrediction = 0;
            list.innerHTML = '';

            picksData.picks.forEach(pick => {
                const p = players[pick.element];
                const difficulty = teamFixtures[p.team] || 3; // Default to 3 if no fixture (blank GW)
                
                // Logic:
                // Base = Form (if Form < 0.5, assume 2.0 base for playing)
                let form = parseFloat(p.form);
                if(form < 1.0) form = 1.5; // Minimum baseline for starter
                
                // Difficulty Factor: 
                // 1 (Easy) -> 1.2x
                // 3 (Avg) -> 1.0x
                // 5 (Hard) -> 0.8x
                // Formula: 1 + (3 - Diff) * 0.1
                const diffFactor = 1 + (3 - difficulty) * 0.1;
                
                let predictedPoints = form * diffFactor;
                
                // Random Variance (Luck): +/- 20%
                const variance = (Math.random() * 0.4) + 0.8; 
                predictedPoints *= variance;

                // Captaincy
                if(pick.is_captain) predictedPoints *= 2;
                if(pick.is_vice_captain && !pick.is_captain) predictedPoints *= 1; 

                const finalPoints = Math.round(predictedPoints);
                totalPrediction += finalPoints;

                // Color based on difficulty to show logic
                const diffColor = difficulty <= 2 ? 'text-success' : (difficulty >= 4 ? 'text-danger' : 'text-muted');

                // Add to list
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <span>
                        <div class="fw-bold">${p.web_name} ${pick.is_captain ? '(C)' : ''}</div>
                        <div class="small ${diffColor}" style="font-size: 0.7rem;">FDR: ${difficulty} • Form: ${p.form}</div>
                    </span>
                    <span class="fw-bold ${finalPoints >= 6 ? 'text-success' : ''}">${finalPoints} pts</span>
                `;
                list.appendChild(li);
            });

            scoreDisplay.innerText = totalPrediction;
            resultDiv.classList.remove('d-none');

        } catch (e) {
            console.error(e);
            alert('Error generating prediction: ' + e.message);
        } finally {
            predictBtn.disabled = false;
            predictBtn.innerText = 'Predict GW Score';
        }
    });
</script>
</body>
</html>
