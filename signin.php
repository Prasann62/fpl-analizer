<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include 'favicon-meta.php'; ?>
  <title>Sign Up | FPL Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        
        <div class="text-center mb-4">
             <h1 class="h3 fw-bold text-primary">FPL Manager</h1>
             <p class="text-muted small">Create an account to get started</p>
        </div>

        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <form method="POST" action="">
                  <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">Full Name</label>
                    <input required type="text" class="form-control form-control-lg" name="na" placeholder="Your Name">
                  </div>

                  <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">Email Address</label>
                    <input required type="email" class="form-control form-control-lg" name="gm" placeholder="name@example.com">
                  </div>

                  <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">Password</label>
                    <input required type="password" class="form-control form-control-lg" name="ps" placeholder="Create a password">
                  </div>

                  <div class="d-grid gap-2">
                    <button type="submit" name="but" class="btn btn-primary btn-lg fw-bold">Sign Up</button>
                  </div>

                  <div class="text-center my-3 text-muted position-relative">
                      <span class="px-2 small opacity-50">OR</span>
                  </div>

                  <div class="text-center">
                    <p class="mb-0 text-muted">Already have an account? <a href="loginform.php" class="text-primary fw-bold text-decoration-none">Log In</a></p>
                  </div>
                </form>
            </div>
        </div>
        
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <?php
    if(isset($_POST['but'])) {
        $name  = $_POST['na'];
        $email = $_POST['gm'];
        $psw   = $_POST['ps'];

        $servername = "localhost";
        $username   = "u913997673_prasanna";
        $password   = "Ko%a/2klkcooj]@o";
        $dbname     = "u913997673_prasanna";

        $conn = new mysqli($servername,$username,$password,$dbname);

        if($conn->connect_error){
            die("connection failed:".$conn->connect_error);
        }

        // Potential SQL injection risk here, but keeping logic same as original for now
        // TODO: Upgrade to prepared statements
        $sql="INSERT INTO signin (name,email,password) values('".$name."','".$email."','".$psw."')";

        if ($conn->query($sql)=== TRUE){
            echo "<script>window.open('loginform.php','_self')</script>";
        } else{
        }

        $conn->close();
    }
  ?>
</body>
</html>
