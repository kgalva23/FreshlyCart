<?php
include("functions.php");
include("components/header.php");
include("components/footer.php");
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

not_logged();
$_SESSION["active_page"] = "Orders";

$dblink = db_connect(); // Ensure this connects to your Supabase instance.

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $billingAddress = $_POST['billingAddress'];
    $shippingAddress = $_POST['shippingAddress'];
    $paymentMethod = $_POST['paymentMethod'];
    $orderTotal = $_POST['orderTotal'];
    $userId = $_SESSION['user_id']; // Assuming a session variable holds the logged-in user ID.

    // Sanitize the orderTotal to ensure it's a numeric value
    $orderTotal = preg_replace('/[^0-9.]/', '', $orderTotal); // Removes all characters except digits and the period

    // Save order to the database
    $sql = "INSERT INTO \"order\" (user_id, name, phone, email, billing_address, shipping_address, payment_method, total)
            VALUES (:user_id, :name, :phone, :email, :billing_address, :shipping_address, :payment_method, :total)";
    $stmt = $dblink->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':billing_address', $billingAddress, PDO::PARAM_STR);
    $stmt->bindValue(':shipping_address', $shippingAddress, PDO::PARAM_STR);
    $stmt->bindValue(':payment_method', $paymentMethod, PDO::PARAM_STR);
    $stmt->bindValue(':total', $orderTotal, PDO::PARAM_STR);
    $stmt->execute();
}

// Fetch all previous orders for the user
$sql = "SELECT * FROM \"order\" WHERE user_id = :user_id";
$stmt = $dblink->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Orders</title>
    <style>
        /* Ensure the footer is always at the bottom */
        html, body {
            height: 100%;
        }

        .content-wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1; /* This pushes the footer to the bottom */
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <?php generate_header(); ?>
        <div class="container mt-5 main-content">
            <div class="container mt-5 pt-5">
                <h1 class="text-center mb-4" style="margin-top: 50px; visibility: visible;">Previous Orders</h1>
            </div>
            <?php if (empty($result)): ?>
                <p class="text-center">No orders found.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['order_id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td>$<?= htmlspecialchars(number_format($row['total'], 2)) ?></td>
                                <td><?= htmlspecialchars($row['order_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php generate_footer(); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
