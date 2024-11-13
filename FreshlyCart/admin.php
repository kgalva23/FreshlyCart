<?php
include("functions.php");
include("components/header.php");
include("components/footer.php");
include "s3bucket.php";
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

not_logged();

$_SESSION["active_page"] = "Admin";
$section = $_GET['section'] ?? 'default';
$dblink = db_connect();



if ($_SERVER["REQUEST_METHOD"] == "POST" && $section == 'addInventory') {
    $uploadfile = tempnam(sys_get_temp_dir(), sha1($_FILES['image']['name']));
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
        if (uploadToS3($uploadfile, $_FILES['image']['name'])) {
            $image_id = addImage($_FILES['image']['name']);
            $name = $dblink->real_escape_string($_POST['name']);
            $description = $dblink->real_escape_string($_POST['description']);
            $company = $dblink->real_escape_string($_POST['company']);
            $price = $dblink->real_escape_string($_POST['price']);
            $stock = $dblink->real_escape_string($_POST['stock']);

            $itemInsertSql = "INSERT INTO item (name, description, company, price, stock, image_id) VALUES ('$name', '$description', '$company', '$price', '$stock', '$image_id')";
            if ($dblink->query($itemInsertSql) === TRUE) {
                echo "New item added successfully";
            }
        }
    }



    /*$target_dir = "./images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $uploadOk = 1;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

        $imageInsertSql = "INSERT INTO image (Image) VALUES ('" . basename($_FILES["image"]["name"]) . "')";
        if ($dblink->query($imageInsertSql) === TRUE) {
            $last_image_id = $dblink->insert_id;
        }
    }*/
}


if ($section == 'modifyInventory') {
    $sql = "SELECT item.*, image.image AS ImagePath FROM item LEFT JOIN image ON item.image_id = image.image_id";
    $result = $dblink->query($sql);

    $items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
}

?>

<html>
<header>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/components/footer.css" rel="stylesheet">
    <link href="/style.css" rel="stylesheet">
    <title>Admin Page</title>
</header>

<body class="bg-light min-vh-100">
    <?php generate_header(); ?>
    <div class="container min-vw-75  min-vh-100 bg-white shadow-lg pt-3">
        <div class="container mb-3 border-bottom">
            <h2>Which admin task would you like to perform?</h2>
        </div>


        <a href="admin.php?section=modifyUsers" class="btn btn-primary">Modify User Accounts</a>
        <a href="admin.php?section=addInventory" class="btn btn-success">Add New Item to Inventory</a>

        <?php if ($section == 'modifyUsers') : ?>
            <div id="userSection">
                <h2>Current Active Accounts:</h2>

                <table class="form-control" name="docType">
                    <?php


                    $sql = "SELECT * FROM user";
                    $result = $dblink->query($sql) or die("Something went wrong with: $sql<br>" . $dblink_->error);
                    $counter = 1;

                    //While loop to go through each account in the database
                    while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
                        $buttonName = "button" . (string)$counter;
                        $c_firstName = $data['first_name'];
                        $c_lastName = $data['last_name'];
                        $c_email = $data['email'];
                        echo '<p> Name: ' . $c_firstName . ' ' . $c_lastName . '<br />Email: ' . $c_email . '</p>';
                        echo "<form method='post'> 
                    <input type='submit' name=$buttonName value='Remove Account!'/>";

                        //check to see which button is clicked
                        if (isset($_POST[$buttonName])) {
                            $dblink->query("DELETE FROM user WHERE First_Name='$c_firstName'");
                        }
                        $counter++;
                    }
                    ?>
                </table>
            </div>
        <?php elseif ($section == 'addInventory') : ?>
            <div id="addInventorySection">
                <h2>Add New Inventory Item</h2>
                <form action="admin.php?section=addInventory" method="post" enctype="multipart/form-data">
                    Name: <input type="text" name="name" required><br>
                    Description: <input type="text" name="description" required><br>
                    Company: <input type="text" name="company" required><br>
                    Price: <input type="number" step="0.01" name="price" required><br>
                    Stock: <input type="number" name="stock" required><br>
                    Image: <input type="file" name="image" required><br>
                    <input type="submit" value="Add Item" class="btn btn-primary">
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php generate_footer(); ?>

    <script src="/js/modify_items.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>