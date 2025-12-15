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
    <title>Expert Team Reveals | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-4">
        <div class="hero-header mb-4 text-center">
            <h1 class="display-5 fw-bold mb-1">Expert Reveals</h1>
            <p class="lead opacity-75 mb-0">See how the top managers are setting up.</p>
        </div>

        <div class="row g-4">
            <!-- Expert 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                         <div class="d-flex align-items-center mb-3">
                             <img src="https://resources.premierleague.com/premierleague/photos/players/250x250/p223340.png" class="rounded-circle border me-3" width="60" height="60" alt="Expert">
                             <div>
                                 <h5 class="fw-bold mb-0">The Scout</h5>
                                 <small class="text-muted">Official FPL Expert</small>
                             </div>
                         </div>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">"Haaland is an essential captain this week against a weak defense. I'm moving funds out of defense to upgrade midfield."</p>
                        <hr>
                        <h6 class="fw-bold text-uppercase small text-muted mb-2">Key Transfers</h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-1"><i class="bi bi-arrow-right text-success me-2"></i> IN: <strong>Salah</strong> (£12.8m)</li>
                            <li class="mb-1"><i class="bi bi-arrow-left text-danger me-2"></i> OUT: <strong>Son</strong> (£9.8m)</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-4">
                        <button class="btn btn-outline-primary w-100 btn-sm">View Full Team</button>
                    </div>
                </div>
            </div>

            <!-- Expert 2 -->
             <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                         <div class="d-flex align-items-center mb-3">
                             <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center fw-bold text-dark border me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">K</div>
                             <div>
                                 <h5 class="fw-bold mb-0">FPL King</h5>
                                 <small class="text-success fw-bold">Top 1k Rank</small>
                             </div>
                         </div>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">"Taking a -4 hit to bring in Palmer. His underlying stats are insane. Benching my Newcastle defenders this week."</p>
                         <hr>
                        <h6 class="fw-bold text-uppercase small text-muted mb-2">Captaincy</h6>
                         <div class="d-flex align-items-center">
                             <div class="badge bg-warning text-dark me-2">C</div> <strong>C. Palmer</strong> (vs SHU)
                         </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-4">
                        <button class="btn btn-outline-primary w-100 btn-sm">View Full Team</button>
                    </div>
                </div>
            </div>
            
             <!-- Expert 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                         <div class="d-flex align-items-center mb-3">
                             <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center fw-bold text-white border me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">AI</div>
                             <div>
                                 <h5 class="fw-bold mb-0">Algorithm XI</h5>
                                 <small class="text-primary fw-bold">Optimum Solver</small>
                             </div>
                         </div>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">The algorithm projects 78 points for this gameweek. Double defense for Arsenal is the differential play.</p>
                         <hr>
                         <h6 class="fw-bold text-uppercase small text-muted mb-2">Wildcard Active</h6>
                        <span class="badge bg-success">Yes</span> - Template Reset
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-4">
                        <button class="btn btn-outline-primary w-100 btn-sm">View Full Team</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
