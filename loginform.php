<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    a { text-decoration: none; color: #000; }
    a:hover { text-decoration: underline; }

    .group {
      position: relative;
      margin-bottom: 25px;
    }

    .input {
      font-size: 16px;
      padding: 10px 10px 10px 5px;
      display: block;
      width: 100%;
      border: none;
      border-bottom: 1px solid #515151;
      background: transparent;
    }

    .input:focus { outline: none; }

    label {
      color: #999;
      font-size: 18px;
      font-weight: normal;
      position: absolute;
      left: 5px;
      top: 10px;
      transition: 0.2s ease all;
      pointer-events: none;
    }

    .input:focus ~ label,
    .input:valid ~ label {
      top: -20px;
      font-size: 14px;
      color: #5264AE;
    }

    .bar {
      position: relative;
      display: block;
      width: 100%;
    }

    .bar:before, .bar:after {
      content: '';
      height: 2px;
      width: 0;
      bottom: 1px;
      position: absolute;
      background: #5264AE;
      transition: 0.2s ease all;
    }

    .bar:before { left: 50%; }
    .bar:after { right: 50%; }

    .input:focus ~ .bar:before,
    .input:focus ~ .bar:after {
      width: 50%;
    }

    .highlight {
      position: absolute;
      height: 60%;
      width: 100px;
      top: 25%;
      left: 0;
      pointer-events: none;
      opacity: 0.5;
    }

    .input:focus ~ .highlight {
      animation: inputHighlighter 0.3s ease;
    }

    @keyframes inputHighlighter {
      from { background: #5264AE; }
      to   { width: 0; background: transparent; }
    }

    button {
      width: 100%;
      height: 3.5em;
      border: 3px ridge #149CEA;
      outline: none;
      background-color: transparent;
      color: #212121;
      transition: 0.5s;
      border-radius: 0.3em;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      box-shadow: inset 0px 0px 25px #1479EA;
      color: #fff;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="text-center mb-3">
          <img src="f.p.t.1.png" alt="Logo" class="img-fluid">
        </div>

        <form method="POST" action="">
          <div class="group">
            <input required type="email" class="input" name="email">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Email</label>
          </div>

          <div class="group">
            <input required type="password" class="input" name="password">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Password</label>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" name="login_btn">Log In</button>
          </div>

          <div class="text-center my-2">or</div>

          <div class="text-center mt-2">
            Don't have an account? <a href="signin.php">Sign Up</a>
          </div>
        </form>
      </div>
    </div>
  </div>

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
