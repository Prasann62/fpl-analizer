<!-- Navbar -->
<link href="style.css" rel="stylesheet">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">MyFPL</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="team.php">Team</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="fixtures.php">Fixtures</a>
          </li>
          <li class="nav-item">                                                
            <a class="nav-link" href="players.php">Players</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="leagues.php">Leagues</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="rank.php">Live Rank</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="live-score.php">Live Score</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">LOG OUT</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="aiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              AI Hub
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="aiDropdown">
              <li><a class="dropdown-item" href="ai-team-rating.php">Team Rating</a></li>
              <li><a class="dropdown-item" href="ai-team-picker.php">Team Picker</a></li>
              <li><a class="dropdown-item" href="ai-team-improver.php">Team Improver</a></li>
              <li><a class="dropdown-item" href="ai-team-point-predictor.php">Point Predictor</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>