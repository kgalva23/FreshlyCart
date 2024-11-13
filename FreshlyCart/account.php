<?php
session_start();

include 'functions.php';
include 'functions_account.php';
include("components/header.php");
include("components/footer.php");
//include 's3bucket.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

not_logged();

$_SESSION["active_page"] = "Account";

$user = loadUser();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    switch ($_POST['action']) {
        case 'change_first_name':
            if ($_POST['first_name'] === $user['First_Name']) {
                $_SESSION['error'] = "First name cannot be the same as the current first name";
            } else {
                change_first_name($_POST['first_name']);
                $_SESSION['success'] = "First name successfully changed!";
            }
            break;
        case 'change_last_name':
            if ($_POST['last_name'] === $user['Last_Name']) {
                $_SESSION['error'] = "Last name cannot be the same as the current last name";
            } else {
                change_last_name($_POST['last_name']);
                $_SESSION['success'] = "Last name successfully changed!";
            }
            break;
        case 'change_email':
            if ($_POST['email'] === $user['Email']) {
                $_SESSION['error'] = "Email cannot be the same as the current email";
            } else {
                change_email($_POST['email']);
                $_SESSION['success'] = "Email successfully changed!";
            }
            break;
        case 'change_password':
            if (password_verify($_POST['password'], $user['Password'])) {
                $_SESSION['error'] = "Password cannot be the same as the current password";
            } else {
                change_password($_POST['password']);
                $_SESSION['success'] = "Password successfully changed!";
            }
            break;
        case 'change_phone_number':
            if ($_POST['phone_number'] === $user['Phone_Number']) {
                $_SESSION['error'] = "Phone number cannot be the same as the current phone number";
            } else {
                change_phone_number($_POST['phone_number']);
                $_SESSION['success'] = "Phone number successfully changed!";
            }
            break;
        case 'change_profile_picture':
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                if ($_FILES['profile_picture']['size'] > 1024 * 1024) {
                    $_SESSION['error'] = "File size can not be greater than 1MB!";
                } else {
                    $uploadfile = tempnam(sys_get_temp_dir(), sha1($_FILES['profile_picture']['name']));
                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadfile)) {
                        if (uploadToS3($uploadfile, $_FILES['profile_picture']['name'])) {
                            $image_id = addImage($_FILES['profile_picture']['name']);
                            $_SESSION['profile_picture'] = $_FILES['profile_picture']['name'];
                            change_profile_picture($image_id);
                            $_SESSION['success'] = "Profile picture successfully changed!";
                        }
                    } else {
                        $_SESSION['error'] = "Failed to move uploaded file!";
                    }
                }
            } else {
                $_SESSION['error'] = "Error uploading file!";
            }
            break;
        case 'change_preset-profile_picture':
            change_preset_profile_picture($_POST['profile_picture']);
            $_SESSION['profile_picture'] = $_POST['profile_picture'];
            $_SESSION['success'] = "Profile picture successfully changed!";
            break;

        case 'delete_account':
            delete_account();
            break;
    }

    header("Location: account.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/components/footer.css" rel="stylesheet">
    <link href="/style.css" rel="stylesheet">
    <title>Account Settings</title>
</head>

<body class="bg-light min-vh-100">
    <?php generate_header(); ?>
    <div class="container min-vw-75  min-vh-100 bg-white shadow-lg pt-3">


        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6">

                    <h1 class="text-center mb-5">Account Settings</h1>

                    <?php
                    if (isset($_SESSION['error']) && isset($_SESSION['success'])) {
                        echo '<div class="alert alert-danger alert-dismissable fade show" id="error">' . $_SESSION['error'] . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
                        unset($_SESSION['error']);
                        unset($_SESSION['success']);
                    } elseif (isset($_SESSION['error'])) {
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

                    <form id="first-name-form" action="account.php" method="post" class="col">
                        <input type="hidden" name="action" value="change_first_name">
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text">First name</span>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name'] ?>" />
                            <button class="btn btn-primary" type="submit" id="button-addon2">Change</button>
                        </div>
                    </form>

                    <form id="last-name-form" action="account.php" method="post">
                        <input type="hidden" name="action" value="change_last_name">
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text">Last name</span>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name'] ?>" />
                            <button class="btn btn-primary" type="submit" id="button-addon2">Change</button>
                        </div>
                    </form>

                    <form id="email-form" action="account.php" method="post">
                        <input type="hidden" name="action" value="change_email">
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text">Email</span>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email'] ?>" />
                            <button class="btn btn-primary" type="submit" id="button-addon2">Change</button>
                        </div>
                    </form>

                    <form id="password-form" action="account.php" method="post">
                        <input type="hidden" name="action" value="change_password">
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text">Password</span>
                            <input type="password" class="form-control" id="password" name="password" />
                            <button class="btn btn-primary" type="submit" id="button-addon2">Change</button>
                        </div>
                    </form>

                    <form id="phone-number-form" action="account.php" method="post">
                        <input type="hidden" name="action" value="change_phone_number">
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text">Phone number</span>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo $user['phone_number'] ?>" />
                            <button class="btn btn-primary" type="submit" id="button-addon2">Change</button>
                        </div>
                    </form>

                    <form id="profile-picture-form" action="account.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="change_profile_picture">
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text">Profile Picture</span>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" />
                            <button class="btn btn-primary" type="submit" id="button-addon2">Change</button>
                        </div>
                    </form>

                    <form id="preset-profile-picture-form" action="account.php" method="post">
                        <input type="hidden" name="action" value="change_preset-profile_picture">
                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text">Preset Profile Picture</span>
                            <select class="form-select" id="profile_picture" name="profile_picture">

                                <option selected aria-label="disabled">Choose preset profile picture</option>
                                <?php
                                foreach (loadProfilePictures() as $preset_profile_picture) {
                                    echo '<option value="' . $preset_profile_picture['image'] . '">' . $preset_profile_picture['image'] . '</option>';
                                }
                                ?>
                            </select>
                            <button class="btn btn-primary" type="submit" id="button-addon2">Change</button>
                        </div>
                    </form>

                    <form id="delete-account-form" action="account.php" method="post">
                        <input type="hidden" name="action" value="delete_account">
                        <div class="d-flex justify-content-center">
                            <input type="button" value="Delete Account" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#deleteAccountModal" />
                        </div>

                        <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content bg-white shadow-lg border">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5 text-center" id="deleteAccountModalLabel">Are you sure you want to delete your account? <strong>This action cannot be undone.</strong></h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex justify-content-center mt-3">
                                        <button type="button" class="btn btn-secondary btn-lg m-auto" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger btn-lg m-auto">Confirm Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="col-lg-6">
                    <h1 class="text-center mb-5">Profile Picture</h1>
                    <img src="<?php echo isset($item['ImagePath']) ? htmlspecialchars($item['ImagePath']) : 'images/smileyface.jpg'; ?>" alt="Item Image" height="350" width="350" class="rounded-circle" style="float: middle; margin-left: 140px;">
                </div>
            </div>
        </div>


    </div>
    <?php generate_footer(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>