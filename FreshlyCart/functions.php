<?php
function db_connect()
{
    // Supabase Database connection configuration
    $hostname = "aws-0-us-east-2.pooler.supabase.com";
    $port = "6543";
    $dbname = "postgres";
    $username = "postgres.lpffpzhkeuzebucaugvw";
    $password = "UI_Fall#2024";

    try {
        // Create a new PDO instance
        $dblink = new PDO("pgsql:host=$hostname;port=$port;dbname=$dbname", $username, $password);
        // Set error mode to exception for debugging
        $dblink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dblink;
    } catch (PDOException $e) {
        die("Error connecting to database: " . $e->getMessage());
    }
}

function is_logged()
{
    if (isset($_SESSION['user_id'])) {
        header("Location: /home.php");
        exit();
    }
}

function not_logged()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /index.php");
        exit();
    }
}

function loadProfilePicture($image_id)
{
    $dblink = db_connect();
    $stmt = $dblink->prepare("SELECT * FROM image WHERE image_id = :image_id");
    $stmt->bindParam(":image_id", $image_id, PDO::PARAM_INT);
    $stmt->execute();
    $profile_picture = $stmt->fetch(PDO::FETCH_ASSOC);
    return $profile_picture['image'];
}

function addImage($image)
{
    $dblink = db_connect();
    $stmt = $dblink->prepare("INSERT INTO image (Image) VALUES (:image)");
    $stmt->bindParam(":image", $image, PDO::PARAM_STR);
    $stmt->execute();
}
