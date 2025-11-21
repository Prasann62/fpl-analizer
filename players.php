<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>players</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   
</head>
<body>
<?php include 'navbar.php';?>

<!-- Player Filters -->
<div class="container mt-4">
    <div class="row mb-4">
      <div class="col-md-4">
        <input type="text" id="searchBox" class="form-control" placeholder="Search player by name...">
      </div>
      <div class="col-md-4">
        <select id="positionFilter" class="form-select">
          <option value="">All Positions</option>
          <option value="1">Goalkeeper</option>
          <option value="2">Defender</option>
          <option value="3">Midfielder</option>
          <option value="4">Forward</option>
        </select>
      </div>
      <div class="col-md-4">
        <select id="teamFilter" class="form-select">
          <option value="">All Teams</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Player Stats Section -->
  <section class="container py-5">
    <h2 class="text-center mb-4">Player Statistics</h2>
    <div class="table-responsive">
      <table class="table table-striped table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Price</th>
            <th>Form</th>
            <th>Points</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dynamic Player Data Will Load Here -->
        </tbody>
      </table>
    </div>



<?php include 'footer.php';?>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>