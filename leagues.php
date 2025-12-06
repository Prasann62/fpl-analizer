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
    <title>Leagues | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="container py-5">
    <!-- Hero Header -->
    <div class="hero-header shadow-lg mb-5 text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="z-1">
            <h1 class="display-4 fw-extrabold mb-2">Leagues</h1>
            <p class="lead opacity-75 mb-0">View your league standings and status.</p>
        </div>
        <div class="mt-4 mt-md-0 z-1">
            <i class="bi bi-trophy-fill display-1 opacity-25"></i>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Input Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Load Leagues</h5>
                    <div class="input-group input-group-lg">
                        <input type="number" id="managerIdInput" class="form-control" placeholder="Enter Manager ID" aria-label="Manager ID">
                        <button class="btn btn-primary" type="button" id="fetchLeaguesBtn">
                            <i class="bi bi-search me-2"></i>Get Leagues
                        </button>
                    </div>
                </div>
            </div>

            <div id="loadingSpinner" class="text-center d-none py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div id="leaguesContainer" class="d-none">
                <!-- Classic Leagues -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold text-primary">Classic Leagues</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">League Name</th>
                                        <th class="text-center">Rank</th>
                                        <th class="text-center">Last Rank</th>
                                        <th class="text-center pe-4">Movement</th>
                                    </tr>
                                </thead>
                                <tbody id="classicLeaguesBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- H2H Leagues -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold text-primary">Head-to-Head Leagues</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">League Name</th>
                                        <th class="text-center">Rank</th>
                                        <th class="text-center">Last Rank</th>
                                        <th class="text-center pe-4">Movement</th>
                                    </tr>
                                </thead>
                                <tbody id="h2hLeaguesBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="errorAlert" class="alert alert-danger d-none mt-3" role="alert"></div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const managerIdInput = document.getElementById('managerIdInput');
    const fetchLeaguesBtn = document.getElementById('fetchLeaguesBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const leaguesContainer = document.getElementById('leaguesContainer');
    const classicLeaguesBody = document.getElementById('classicLeaguesBody');
    const h2hLeaguesBody = document.getElementById('h2hLeaguesBody');
    const errorAlert = document.getElementById('errorAlert');

    // Auto-load if ID is present
    const urlParams = new URLSearchParams(window.location.search);
    const urlManagerId = urlParams.get('id');
    if (urlManagerId) {
        managerIdInput.value = urlManagerId;
        fetchLeagues(urlManagerId);
    }

    fetchLeaguesBtn.addEventListener('click', () => {
        const managerId = managerIdInput.value.trim();
        if (managerId) {
            fetchLeagues(managerId);
        }
    });

    async function fetchLeagues(managerId) {
        showLoading(true);
        hideError();
        leaguesContainer.classList.add('d-none');

        try {
            const response = await fetch(`api.php?endpoint=entry/${managerId}/`);
            if (!response.ok) {
                if (response.status === 404) throw new Error('Manager not found.');
                throw new Error('Failed to fetch manager data.');
            }
            const data = await response.json();

            renderLeagues(data.leagues);
        } catch (error) {
            console.error(error);
            showError(error.message);
        } finally {
            showLoading(false);
        }
    }

    function renderLeagues(leagues) {
        classicLeaguesBody.innerHTML = '';
        h2hLeaguesBody.innerHTML = '';

        // Classic
        if (leagues.classic.length === 0) {
            classicLeaguesBody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">No classic leagues found</td></tr>';
        } else {
            leagues.classic.forEach(league => {
                const movement = league.entry_rank - league.entry_last_rank;
                let moveIcon = '<i class="bi bi-dash text-secondary"></i>';
                if (movement < 0) moveIcon = '<i class="bi bi-arrow-up-short text-success"></i>'; // Lower rank is better
                else if (movement > 0) moveIcon = '<i class="bi bi-arrow-down-short text-danger"></i>';

                const row = `
                    <tr>
                        <td class="ps-4">
                            <a href="league-standings.php?id=${league.id}&type=classic&manager_id=${managerIdInput.value}" class="fw-bold text-decoration-none text-primary">
                                ${league.name}
                            </a>
                        </td>
                        <td class="text-center fw-bold">${league.entry_rank}</td>
                        <td class="text-center text-muted">${league.entry_last_rank}</td>
                        <td class="text-center pe-4 h5 mb-0 position-relative z-2">${moveIcon}</td>
                    </tr>
                `;
                classicLeaguesBody.innerHTML += row;
            });
        }

        // H2H
        if (leagues.h2h.length === 0) {
            h2hLeaguesBody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">No H2H leagues found</td></tr>';
        } else {
            leagues.h2h.forEach(league => {
                const movement = league.entry_rank - league.entry_last_rank;
                let moveIcon = '<i class="bi bi-dash text-secondary"></i>';
                if (movement < 0) moveIcon = '<i class="bi bi-arrow-up-short text-success"></i>';
                else if (movement > 0) moveIcon = '<i class="bi bi-arrow-down-short text-danger"></i>';

                const row = `
                    <tr>
                        <td class="ps-4">
                            <a href="league-standings.php?id=${league.id}&type=h2h&manager_id=${managerIdInput.value}" class="fw-bold text-decoration-none text-primary">
                                ${league.name}
                            </a>
                        </td>
                        <td class="text-center fw-bold">${league.entry_rank}</td>
                        <td class="text-center text-muted">${league.entry_last_rank}</td>
                        <td class="text-center pe-4 h5 mb-0 position-relative z-2">${moveIcon}</td>
                    </tr>
                `;
                h2hLeaguesBody.innerHTML += row;
            });
        }

        leaguesContainer.classList.remove('d-none');
    }

    function showLoading(show) {
        if (show) {
            loadingSpinner.classList.remove('d-none');
            fetchLeaguesBtn.disabled = true;
            fetchLeaguesBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        } else {
            loadingSpinner.classList.add('d-none');
            fetchLeaguesBtn.disabled = false;
            fetchLeaguesBtn.innerHTML = '<i class="bi bi-search me-2"></i>Get Leagues';
        }
    }

    function showError(msg) {
        errorAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${msg}`;
        errorAlert.classList.remove('d-none');
    }

    function hideError() {
        errorAlert.classList.add('d-none');
    }
});
</script>
</body>
</html>
