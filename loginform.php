<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include 'favicon-meta.php'; ?>
  <title>Login | FPL Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        
        <div class="text-center mb-4">
             <!-- You can uncomment this if you have the logo image, or use text -->
             <!-- <img src="f.p.t.1.png" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;"> -->
             <h1 class="h3 fw-bold text-primary">FPL Manager</h1>
             <p class="text-muted small">Sign in to manage your team</p>
        </div>

        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <form method="POST" action="">
                  <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase fw-bold">Email Address</label>
                    <input required type="email" class="form-control form-control-lg" name="email" placeholder="name@example.com">
                  </div>

                  <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">Password</label>
                    <input required type="password" class="form-control form-control-lg" name="password" placeholder="Enter your password">
                  </div>

                  <div class="d-grid gap-2">
                    <button type="submit" name="login_btn" class="btn btn-primary btn-lg fw-bold">Log In</button>
                  </div>

                  <div class="text-center my-3 text-muted position-relative">
                      <span class="px-2 small opacity-50">OR</span>
                  </div>

                  <div class="text-center">
                    <p class="mb-0 text-muted">Don't have an account? <a href="signin.php" class="text-primary fw-bold text-decoration-none">Sign Up</a></p>
                  </div>
                </form>
            </div>
        </div>
        
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <?php
    session_start();
    if(isset($_POST['login_btn'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $servername = "localhost";
        $username   = "u913997673_prasanna";
        $password_db   = "Ko%a/2klkcooj]@o";
        $dbname     = "u913997673_prasanna";

        $conn = new mysqli($servername, $username, $password_db, $dbname);

        if($conn->connect_error){
            die("connection failed:".$conn->connect_error);
        }

        // Use prepared statement for security
        $stmt = $conn->prepare("SELECT * FROM signin WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            $_SESSION['access'] = true;
            $_SESSION['email'] = $email;
            echo "<script>window.open('Dashboard.php','_self')</script>";
        } else{
            echo "<script>alert('Invalid Email or Password');</script>";
        }

        $stmt->close();
        $conn->close();
    }
  ?>
</body>
</html>
