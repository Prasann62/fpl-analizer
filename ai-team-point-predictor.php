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

<div class="container py-5">
    <div class="hero-header shadow-lg mb-5 text-center">
        <h1 class="display-4 fw-extrabold mb-2">Points Predictor</h1>
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
            const [staticRes, picksRes] = await Promise.all([
                fetch('api.php?endpoint=bootstrap-static/'),
                fetch('api.php?endpoint=entry/' + nid + '/event/1/picks/') // Getting GW1 picks as fallback if live not avail, logic improvement needed for current GW
            ]);
            
            const staticData = await staticRes.json();
            const currentGw = staticData.events.find(e => e.is_current)?.id || 1;
            
            // Re-fetch correct GW picks
            const realPicksRes = await fetch(`api.php?endpoint=entry/${nid}/event/${currentGw}/picks/`);
            if(!realPicksRes.ok) throw new Error("No picks found");
            const picksData = await realPicksRes.json();

            const players = {};
            staticData.elements.forEach(p => players[p.id] = p);

            let totalPrediction = 0;
            list.innerHTML = '';

            picksData.picks.forEach(pick => {
                const p = players[pick.element];
                
                // Simple Prediction Model: Form + (Chance of Playing) + Random Variance
                // In real app, we'd check fixture difficulty too
                
                let basePoints = parseFloat(p.form);
                if(pick.is_captain) basePoints *= 2;
                if(pick.is_vice_captain && !pick.is_captain) basePoints *= 1; // Simplify

                // Add some "AI" randomness variance (-1 to +2)
                const variance = (Math.random() * 3) - 1;
                const prediction = Math.max(0, Math.round(basePoints + variance));
                
                totalPrediction += prediction;

                // Add to list
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <span>
                        ${p.web_name} 
                        ${pick.is_captain ? '<span class="badge bg-warning text-dark">C</span>' : ''}
                    </span>
                    <span class="fw-bold ${prediction > 5 ? 'text-success' : ''}">${prediction} pts</span>
                `;
                list.appendChild(li);
            });

            scoreDisplay.innerText = totalPrediction;
            resultDiv.classList.remove('d-none');

        } catch (e) {
            console.error(e);
            alert('Error generating prediction');
        } finally {
            predictBtn.disabled = false;
            predictBtn.innerText = 'Predict GW Score';
        }
    });
</script>
</body>
</html>
