

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>
<?php include 'navbar.php';?>

<div class="container py-5">
    <h2 class="mb-4">My FPL Team</h2>

    <!-- Input -->
    <div class="card p-3 mb-4">
      <label class="form-label">Enter your FPL Manager ID</label>
      <input id="managerId" class="form-control mb-3" placeholder="e.g. 1234567">
      <button id="loadBtn" class="btn btn-primary">Load Team</button>
    </div>

    <div id="managerInfo" class="mb-3"></div>

<!-- Team Table -->
<div class="card p-3">
  <h5>Your Current Squad</h5>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <td>#</td>
          <td>Player</td>
          <td>Position</td>
          <td>Team</td>
          <td>Form</td>
          <td>Points</td>
        </tr>
      </thead>
      <tbody id="teamTable"></tbody>
    </table>
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