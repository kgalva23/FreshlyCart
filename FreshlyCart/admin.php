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

$supabaseUrl = 'https://lpffpzhkeuzebucaugvw.supabase.co';
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxwZmZwemhrZXV6ZWJ1Y2F1Z3Z3Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzE0NTA4MTQsImV4cCI6MjA0NzAyNjgxNH0.aiB2oozPPQxVKV6bxvu3sV7QjPjJZlrHOqXL3vjXUJI';

$_SESSION["active_page"] = "Admin";
$section = $_GET['section'] ?? 'default';

// Function to handle Supabase requests with extensive logging
function supabaseRequest($endpoint, $method, $data = null) {
    global $supabaseUrl, $supabaseKey;
    $url = $supabaseUrl . '/rest/v1/' . $endpoint;
    $headers = [
        'Content-Type: application/json',
        'apikey: ' . $supabaseKey,
        'Authorization: Bearer ' . $supabaseKey,
        'Prefer: return=representation' // Ensures we get the inserted record in response
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ];

    if ($method == 'POST' || $method == 'DELETE') {
        $options[CURLOPT_POSTFIELDS] = json_encode([$data]); // Wrap data in an array for single insert
        //echo "<pre>Data being sent to Supabase: " . json_encode([$data], JSON_PRETTY_PRINT) . "</pre>";
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    //echo "<pre>HTTP Response Code: $httpcode</pre>"; // Show HTTP status code
    if ($error) {
        echo "cURL Error: " . $error;
        return null;
    }

    // Output the raw response from Supabase
    //echo "<pre>Raw Response from Supabase: " . htmlspecialchars($response) . "</pre>";

    return json_decode($response, true);
}

// Handle delete request when "Remove Account" is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    if ($user_id) {
        // Use the correct column name in the endpoint (replace 'user_id' with the primary key field if different)
        $response = supabaseRequest("user?user_id=eq.$user_id", 'DELETE');
        if ($response) {
            echo "User removed successfully";
        } else {
            echo "Failed to remove user";
        }
    }
}

// Handle adding a new inventory item
if ($_SERVER["REQUEST_METHOD"] == "POST" && $section == 'addInventory') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $company = $_POST['company'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = basename($_FILES["image"]["name"]);
        $targetFilePath = "images/" . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $imagePath = $targetFilePath;
        } else {
            echo "Error uploading image.";
            $imagePath = null;
        }
    } else {
        $imagePath = null;
    }

    // Prepare data to be sent to Supabase
    $data = [
        'name' => $name,
        'description' => $description,
        'company' => $company,
        'price' => $price,
        'stock' => $stock,
        'image_id' => $imagePath ? $imageName : null  // Assuming `image_id` is a path or identifier
    ];

    // Attempt to send data to Supabase and display results
    $response = supabaseRequest("item", "POST", $data);

    if ($response && !isset($response['error'])) {
        echo "Item added successfully!";
    } else {
        echo "Failed to add item. Error: " . json_encode($response);
    }
}

?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/components/footer.css" rel="stylesheet">
    <link href="/style.css" rel="stylesheet">
    <title>Admin Page</title>
</head>

<body class="bg-light min-vh-100">
    <?php generate_header(); ?>
    <div class="container min-vw-75 min-vh-100 bg-white shadow-lg pt-3">
        <div class="container mb-3 border-bottom">
            <h2>Which admin task would you like to perform?</h2>
        </div>

        <a href="admin.php?section=modifyUsers" class="btn btn-primary">Modify User Accounts</a>
        <a href="admin.php?section=addInventory" class="btn btn-success">Add New Item to Inventory</a>

        <?php
        if ($section == 'modifyUsers') : 
        ?>
            <div id="userSection">
                <h2>Current Active Accounts:</h2>

                <table class="form-control" name="docType">
                    <?php
                    // Fetch users from Supabase
                    $response = supabaseRequest('user', 'GET');
                    if ($response) {
                        foreach ($response as $data) {
                            $user_id = $data['user_id'];  // Assuming 'id' is the unique identifier
                            $c_firstName = $data['first_name'];
                            $c_lastName = $data['last_name'];
                            $c_email = $data['email'];
                            
                            echo '<p> Name: ' . $c_firstName . ' ' . $c_lastName . '<br />Email: ' . $c_email . '</p>';
                            
                            echo "<form method='post'>
                                    <input type='hidden' name='user_id' value='$user_id'/>
                                    <input type='hidden' name='delete_user' value='1'/>
                                    <input type='submit' value='Remove Account!'/>
                                </form>";
                        }
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
