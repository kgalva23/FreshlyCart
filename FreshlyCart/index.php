<?php
session_start();

include 'functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

is_logged();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $dblink = db_connect();
  $email = $_POST["email"];
  $password = $_POST["password"];

  //$stmt = $dblink->prepare("SELECT * FROM user WHERE Email = ?");
  $stmt = $dblink->prepare("SELECT * FROM \"user\" WHERE Email = :email");
  //$stmt->bind_param("s", $email);
  $stmt->bindParam(":email", $email, PDO::PARAM_STR);
  $stmt->execute();
  //$result = $stmt->get_result();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);


  if ($user) {

    //$user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['profile_picture'] = loadProfilePicture($user['image_id']);
      //$_SESSION['s3url'] = "https://team15project.s3.us-east-2.amazonaws.com/";

      // Close the database connection
      $dblink = null;

      header("Location: /home.php");
      exit();
    } else {
      //$dblink->close();
      $_SESSION['error'] = "Invalid username or password";
      $dblink = null;
      header("Location: /index.php");
      exit();
    }
  } else {
    //$dblink->close();
    $_SESSION['error'] = "Invalid username or password";
    $dblink = null;
    header("Location: /index.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <title>Freshly Cart Login</title>
</head>

<body class="bg-light">
  <div class="container">
    <div class="row mt-5">
      <div class="col-lg-4 bg-white m-auto shadow-lg rounded p-3 mt-5">

        <form class="row g-3 needs-validation" novalidate action="index.php" method="post" autocomplete="on">
          <h1 class="text-center pt-3 mb-3">Freshly Cart Login</h1>

          <?php
          if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissable fade show" id="error">' . $_SESSION['error'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['error']);
          }
            if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success alert-dismissable fade show" id="success">' . $_SESSION['success'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['success']);
          }
          ?>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required />
            <label for="email">Email: </label>
            <div class="invalid-feedback">
              Please provide a valid email.
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
            <label for="password">Password: </label>
            <div class="invalid-feedback">
              Please provide a valid password.
            </div>
          </div>

          <div class="d-flex justify-content-center mb-3">
            <input type="submit" value="Login" class="btn btn-primary btn-lg" />
          </div>

          <p class="text-center">Don't have an account? <a class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" href="registration.php">Register</a></p>

        </form>

      </div>
    </div>
  </div>
  <script>
    /* Bootstrap validation (not working)*/
    (() => {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      const forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add('was-validated')
        }, false)
      })
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>