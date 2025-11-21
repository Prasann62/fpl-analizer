
<?php  session_start();
if(isset($_SESSION['access'])){
  header('location:loginform.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <style> .group {
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
    }</style>
</head>
<body>
    <div class="row mt-5">
        <div class="col-sm-2"></div>

        
        <div class="col-sm-6 mt-5 text-center">
        <img src="pl.1.png"  width="500px" height="450px" alt="fpl">
        </div>

        <!-- Login form -->
        <div class="col-sm-3 mt-5">
            <div class="text-center mb-3">
                <img src="f.p.t.1.png" alt="Logo">
            </div>

            <form method="POST" action="">
            <div class="group">
            <input required type="email" class="input" name="em">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Email</label>
          </div>
          <div class="group">
            <input required type="password" class="input" name="ps">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>password</label>
          </div>

                <div class="d-grid gap-2">
                    <button type="submit"  name="but">Log In</button>
                </div>

                <div class="text-center my-2">or</div>

                <div class="text-center mt-2">
                    <a href="#" class="text-decoration-none">Forgot password?</a>
                </div>

                <div class="text-center mt-2">
                    Don’t have an account? <a href="signin.php" class="text-decoration-none">Sign up</a>
                </div>
            </form>
        </div>
    </div>

    <?php

 if(isset($_POST['but']))
    {
       
        $email=$_POST['em'];
        $psw=$_POST['ps'];



        $servername = "localhost";
        $username = "u913997673_prasanna";
        $password = "Ko%a/2klkcooj]@o";
        $dbname = "u913997673_prasanna";


        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }

       
$sql="SELECT * FROM signin where email='$email' and password='$psw'";

    $query_run=mysqli_query($conn,$sql);

    if(mysqli_num_rows($query_run)==1){
        $_SESSION['access']=$email;

           echo "<script>window.open('index.php','_self')</script>";

    }else{


        echo "<script>alert('invalid user name')</script>";
    }


        $conn->close();

        }
?>
</body>
</html>
