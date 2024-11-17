<?php
include("functions.php");
include("components/header.php");
include("components/footer.php");
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

not_logged();
$_SESSION["active_page"] = "Checkout";

$dblink = db_connect();

// Retrieve shopping cart data from cookies
$cartItems = json_decode($_COOKIE['shopping_cart'] ?? '{}', true);
$itemDetails = [];
$totalPrice = 0;

if (!empty($cartItems)) {
    $placeholders = implode(',', array_fill(0, count($cartItems), '?'));
    $sql = "SELECT * FROM item WHERE item_id IN ($placeholders)";
    $stmt = $dblink->prepare($sql);

    // Bind each cart item ID as a parameter
    foreach (array_keys($cartItems) as $index => $id) {
        $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $row['quantity'] = $cartItems[$row['item_id']];
        $row['total'] = $row['price'] * $row['quantity'];
        $totalPrice += $row['total'];
        $itemDetails[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/style.css" rel="stylesheet">
    <title>Checkout</title>
    <script src="/js/shopping_cart.js"></script>
    <style>
        .cart-items {
            list-style-type: none;
            padding: 0;
        }

        .cart-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
        }

        .cart-item h3 {
            margin-top: 0;
        }
    </style>
</head>

<body>
    <?php generate_header(); ?>

    <div class="container mt-5">
        <h1>Checkout Page</h1>
        <?php if (empty($itemDetails)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($itemDetails as $item): ?>
                    <?php if ($item['quantity'] > 0): ?>
                        <div class="row">
                            <div class="col-md-8 col-lg-6 mx-auto">
                                <div class="cart-item">
                                    <h3>
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </h3>
                                    <p>Quantity:
                                        <?php echo htmlspecialchars($item['quantity']); ?>
                                    </p>
                                    <p class="item-price"> Price: $<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></p>
                                    <p>Total: $<?php echo htmlspecialchars(number_format($item['total'], 2)); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="cart-total">
                    <h3>Total Payment Due: $<?php echo number_format($totalPrice, 2); ?></h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmOrderModal">
                        Confirm Order
                    </button>
                    <button type="button" class="btn btn-danger" onclick="clearCart()">Cancel Order</button>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmOrderModalLabel">Confirm Your Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="orderForm" method="post" action="orders.php">
                                <!-- User Details -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required pattern="\(\d{3}\) \d{3}-\d{4}">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <!-- Address Fields -->
                                <div class="mb-3">
                                    <label for="billingAddress" class="form-label">Billing Address</label>
                                    <textarea class="form-control" id="billingAddress" name="billingAddress" rows="2" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="shippingAddress" class="form-label">Shipping Address</label>
                                    <textarea class="form-control" id="shippingAddress" name="shippingAddress" rows="2" required></textarea>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-3">
                                    <label for="paymentMethod" class="form-label">Payment Method</label>
                                    <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="Cash on Delivery">Cash on Delivery</option>
                                    </select>
                                </div>

                                <!-- Order Total -->
                                <div class="mb-3">
                                    <label for="orderTotal" class="form-label">Order Total</label>
                                    <input type="text" class="form-control" id="orderTotal" name="orderTotal" value="$<?php echo number_format($totalPrice, 2); ?>" readonly>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-success">Place Order</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php generate_footer(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript function to clear the cart
        function clearCart() {
            document.cookie = "shopping_cart=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
            alert("Cart has been cleared!");
            location.reload();
        }
    </script>
</body>
</html>

