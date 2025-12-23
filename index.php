<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include 'favicon-meta.php'; ?>
  <title>MyFPL - Fantasy Premier League Manager</title>
  <!-- Google Fonts: Outfit -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom CSS (Cache Busted) -->
  <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

    <?php include 'navbar.php';?>

  <div class="main-content">
      <!-- Hero Section -->
      <section class="hero-section text-center">
        <div class="container animate-fade-in">
          <h1 class="hero-title mb-4">Master Your FPL Team</h1>
          <p class="lead mb-5">Analyze stats, track fixtures, and optimize your fantasy team like a pro.</p>
          <a href="Dashboard.php" class="btn btn-premium btn-lg">Go to Dashboard</a>
        </div>
      </section>

      <!-- Features Section -->
      <div class="container my-5">
          <div class="row g-4">
              <div class="col-md-4">
                  <div class="card h-100 text-center p-4">
                      <div class="card-body">
                          <i class="bi bi-bar-chart-fill text-primary fs-1 mb-3"></i>
                          <h3 class="card-title">Live Stats</h3>
                          <p class="card-text">Get real-time updates on player performance and points.</p>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="card h-100 text-center p-4">
                      <div class="card-body">
                          <i class="bi bi-calendar-event-fill text-success fs-1 mb-3"></i>
                          <h3 class="card-title">Fixtures</h3>
                          <p class="card-text">Plan ahead with our detailed fixture difficulty tracker.</p>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="card h-100 text-center p-4">
                      <div class="card-body">
                          <i class="bi bi-people-fill text-danger fs-1 mb-3"></i>
                          <h3 class="card-title">Player Analysis</h3>
                          <p class="card-text">Deep dive into player form, value, and potential returns.</p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <?php include 'footer.php';?>
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
