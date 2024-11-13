<?php
session_start();

include 'functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

is_logged();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $dblink = db_connect();

  // Check if email already exists in table
  $email = $_POST["email"];
  $stmt = $dblink->prepare("SELECT * FROM \"user\" WHERE Email = :email");
  $stmt->bindParam(":email", $email, PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result) {
    // Email already exists, display an error message
    $_SESSION['error'] = "Email already exists!";
    header("Location: /registration.php");
    exit();
  } else {
    // Email does not exist, insert new user into table
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $phone_number = $_POST["phone_number"];
    $image_id = rand(1, 1);  //random img from 1 to 1

    $stmt = $dblink->prepare("INSERT INTO \"user\" (First_Name, Last_Name, Email, Password, Phone_Number, Image_ID) 
                                  VALUES (:first_name, :last_name, :email, :password, :phone_number, :image_id)");
    $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
    $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->bindParam(":password", $password, PDO::PARAM_STR);
    $stmt->bindParam(":phone_number", $phone_number, PDO::PARAM_STR);
    $stmt->bindParam(":image_id", $image_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
      $_SESSION['success'] = "Your account was created successfully! Please login to continue.";
      header("Location: /index.php");
      exit();
    } else {
      $_SESSION['error'] = "An error occurred while registering your account. Please try again.";
      header("Location: /registration.php");
      exit();
    }
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <title>Registration</title>
</head>

<body class="bg-light">
  <div class="container">
    <div class="row mt-5">
      <div class="col-lg-4 bg-white m-auto mt-5 shadow-lg rounded p-3">
        <form class="needs-validation" novalidate id="registration-form" action="registration.php" method="post">
          <h1 class="text-center pt-3 mb-3">Register</h1>

          <?php
          if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissable fade show" id="error">' . $_SESSION['error'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['error']);
          }
          ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" required />
            <label for="first_name">First name: </label>
            <div class="invalid-feedback">
              Please provide a valid first name.
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" required />
            <label for="last_name">Last name: </label>
            <div class="invalid-feedback">
              Please provide a valid last name.
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required pattern="^[^@]+@[^@]+\.[^@]+$" />
            <label for="email">Email: </label>
            <div class="invalid-feedback">
              Please provide a valid email. (Format: example@email.com)
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
            <label for="password">Password: </label>
            <div class="invalid-feedback">
              Please provide a valid password.
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Phone number" required pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" />
            <label for="phone_number">Phone number: </label>
            <div class="invalid-feedback">
              Please provide a valid phone number. (Format: 123-456-7890)
            </div>
          </div>

          <div class="d-flex justify-content-center mb-3">
            <input type="submit" value="Register" class="btn btn-primary btn-lg" />
          </div>
          <p class="text-center">Already have an account? <a class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" href="index.php">Login</a></p>

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