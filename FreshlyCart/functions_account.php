<?php

function loadUser()
{
    $dblink = db_connect();
    $user_id = $_SESSION['user_id'];
    $stmt = $dblink->prepare("SELECT * FROM \"user\" WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}

function change_first_name($first_name)
{
    $dblink = db_connect();
    $user_id = $_SESSION['user_id'];
    $stmt = $dblink->prepare("UPDATE \"user\" SET first_name = :first_name WHERE user_id = :user_id");
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

function change_last_name($last_name)
{
    $dblink = db_connect();
    $user_id = $_SESSION['user_id'];
    $stmt = $dblink->prepare("UPDATE \"user\" SET last_name = :last_name WHERE user_id = :user_id");
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

function change_email($email)
{
    $dblink = db_connect();
    $stmt = $dblink->prepare("SELECT * FROM \"user\" WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email already exists!";
    } else {
        $user_id = $_SESSION['user_id'];
        $stmt = $dblink->prepare("UPDATE \"user\" SET email = :email WHERE user_id = :user_id");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

function change_password($password)
{
    $dblink = db_connect();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];
    $stmt = $dblink->prepare("UPDATE \"user\" SET password = :password WHERE user_id = :user_id");
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

function change_phone_number($phone_number)
{
    $dblink = db_connect();
    $user_id = $_SESSION['user_id'];
    $stmt = $dblink->prepare("UPDATE \"user\" SET phone_number = :phone_number WHERE user_id = :user_id");
    $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

function change_profile_picture($image_id)
{
    $dblink = db_connect();
    $user_id = $_SESSION['user_id'];
    $stmt = $dblink->prepare("UPDATE \"user\" SET image_id = :image_id WHERE user_id = :user_id");
    $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

function change_preset_profile_picture($image)
{
    $dblink = db_connect();
    $stmt = $dblink->prepare("SELECT * FROM image WHERE image = :image");
    $stmt->bindParam(':image', $image, PDO::PARAM_STR);
    $stmt->execute();
    $image_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($image_data) {
        $image_id = $image_data['image_id'];
        $user_id = $_SESSION['user_id'];
        $stmt = $dblink->prepare("UPDATE \"user\" SET image_id = :image_id WHERE user_id = :user_id");
        $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

function loadProfilePictures()
{
    $dblink = db_connect();
    $stmt = $dblink->prepare("SELECT * FROM image WHERE image_id > 0 AND image_id < 34");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function delete_account()
{
    $dblink = db_connect();
    $user_id = $_SESSION['user_id'];
    $stmt = $dblink->prepare("DELETE FROM \"user\" WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['success'] = "Account deleted successfully!";
}
