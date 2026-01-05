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
    <title>FPL Draft | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-4">
        <div class="hero-header mb-5 text-center">
            <h1 class="display-5 fw-bold mb-2">FPL Draft Mode</h1>
            <p class="lead opacity-75 mb-4">A different way to play Fantasy Premier League.</p>
            <a href="https://draft.premierleague.com" target="_blank" class="btn btn-primary fw-bold px-4">Official Draft Site <i class="bi bi-box-arrow-up-right ms-2"></i></a>
        </div>

        <div class="row mb-5 align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold mb-3">What is Draft?</h3>
                <p class="text-muted">Unlike the standard game, there are <strong>no player prices</strong> and <strong>no budgets</strong>. </p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Unique Players:</strong> Only one manager in the league can own Haaland.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Snake Draft:</strong> Take turns picking players at the start of the season.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Trading:</strong> Trade players with other managers in your league.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <!-- Mock Simulator Visual -->
                <div class="card shadow-lg border-0">
                    <div class="card-header border-bottom border-light border-opacity-10 d-flex justify-content-between">
                        <span class="fw-bold text-primary">Mock Draft Room</span>
                        <span class="badge bg-danger rounded-pill">Round 1 / Pick 4</span>
                    </div>
                    <div class="card-body">
                         <div class="d-flex mb-3 align-items-center opacity-50">
                             <span class="me-3 small text-muted">Pick 1 (Alice)</span>
                             <span class="badge bg-light text-dark">E. Haaland</span>
                         </div>
                         <div class="d-flex mb-3 align-items-center opacity-50">
                             <span class="me-3 small text-muted">Pick 2 (Bob)</span>
                             <span class="badge bg-light text-dark">M. Salah</span>
                         </div>
                         <div class="d-flex mb-3 align-items-center opacity-50">
                             <span class="me-3 small text-muted">Pick 3 (Charlie)</span>
                             <span class="badge bg-secondary text-white border border-light border-opacity-25">B. Saka</span>
                         </div>
                         <div class="d-flex mb-3 align-items-center">
                             <span class="me-3 small fw-bold text-primary">Pick 4 (You)</span>
                             <select class="form-select form-select-sm w-50">
                                 <option>Select Player...</option>
                                 <option>C. Palmer</option>
                                 <option>Destiny Udogie</option>
                                 <option>Ollie Watkins</option>
                             </select>
                             <button class="btn btn-sm btn-success ms-2">Draft</button>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
             <div class="col-12">
                 <div class="card shadow-sm border-0">
                     <div class="card-body text-center py-5">
                         <i class="bi bi-tools display-4 text-muted mb-3 d-block"></i>
                         <h4 class="fw-bold">Draft Tools Coming Soon</h4>
                         <p class="text-muted">We are building a suite of tools for Draft Mode including Waiver Analytics and Trade Analyzer.</p>
                     </div>
                 </div>
             </div>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
