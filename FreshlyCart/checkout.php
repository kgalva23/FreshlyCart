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
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/style.css" rel="stylesheet">
    <title>Shopping Cart</title>
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

    <div class="container">

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
                                    <p class="item-price"> Price: $
                                        <?php echo htmlspecialchars(number_format($item['price'], 2)); ?>
                                    </p>
                                    <p>Total: $
                                        <?php echo htmlspecialchars(number_format($item['total'], 2)); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="cart-total">
                    <h3>Total Payment Due: $<?php echo number_format($totalPrice, 2); ?></h3>
                    <button class="checkout" id="confirmOrderButton"
                        onclick="window.location.href='orders.php';">Confirm Order</button>
                    <button class="checkout" id="returntoCartButton"
                        onclick="window.location.href='cart.php';">Return to Cart</button>
                </div>

            </div>
        <?php endif; ?>

    </div>

    <?php generate_footer(); ?>
</body>

</html>