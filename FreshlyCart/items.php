<?php
include("functions.php");
include("components/header.php");
include("components/footer.php");
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

not_logged();
$_SESSION["active_page"] = "Items";

// Connect to the database
$dblink = db_connect();

// Determine sorting order based on the query parameter
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$ascending = ($sortOrder === 'price_low_high') ? 'ASC' : 'DESC';

try {
    $sql = "SELECT item.*, image.image AS ImagePath
            FROM item
            LEFT JOIN image ON item.image_id = image.image_id::text
            ORDER BY item.price $ascending";
    $stmt = $dblink->prepare($sql);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="/components/footer.css" rel="stylesheet">
    <link href="/style.css" rel="stylesheet">
    <title>Items Page</title>
    <style>
        .card-flex {
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .card-flex img {
            width: 150px;
            height: auto;
            margin-right: 15px;
        }
        .card-body {
            flex-grow: 1;
        }
        .openModalBtn {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        .openModalBtn:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: #fff;
        }
    </style>

    <script>
        // Initial items data passed from PHP to JavaScript
        var items = <?php echo json_encode($items); ?>;

        // Function to display items based on a filtered list
        function displayItems(itemsToDisplay) {
            const itemContainer = document.getElementById("itemContainer");
            itemContainer.innerHTML = ""; // Clear existing items

            itemsToDisplay.forEach(function (item) {
                const imageUrl = item.ImagePath
                    ? item.ImagePath
                    : `images/${item.name}.jpg`;

                const itemDiv = document.createElement("div");
                itemDiv.className = "col-md-6 mb-4";
                itemDiv.innerHTML = `
                    <div class="card card-flex">
                        <img src="${imageUrl}" class="card-img-left" alt="Item Image" width="150" height="auto">
                        <div class="card-body">
                            <h5 class="card-title">${item.name}</h5>
                            <p class="card-text">${item.description}</p>
                            <p class="card-text">Company: ${item.company}</p>
                            <p class="card-text">Price: $${parseFloat(item.price).toFixed(2)}</p>
                            <p class="card-text">Available: ${item.stock}</p>
                            <button class="btn openModalBtn" data-item-id="${item.item_id}">Add to Cart</button>
                        </div>
                    </div>
                `;
                itemContainer.appendChild(itemDiv);
            });
        }

        // Event listener for sorting
        document.addEventListener("DOMContentLoaded", function () {
            const sortSelect = document.getElementById("sortSelect");
            if (sortSelect) {
                sortSelect.addEventListener("change", function () {
                    const selectedSort = sortSelect.value;
                    window.location.href = `?sort=${selectedSort}`;
                });
            }

            // Display all items initially
            displayItems(items);

            // Search functionality
            const searchInput = document.getElementById("searchItems");
            searchInput.addEventListener("input", function () {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredItems = items.filter(item =>
                    item.name.toLowerCase().includes(searchTerm) ||
                    item.description.toLowerCase().includes(searchTerm)
                );
                displayItems(filteredItems);
            });

            // Event delegation for "Add to Cart" buttons
            document.getElementById("itemContainer").addEventListener("click", function (e) {
                if (e.target.classList.contains("openModalBtn")) {
                    const itemId = e.target.getAttribute("data-item-id");
                    addToCart(itemId);
                    console.log(`Item ${itemId} added to cart.`);
                    
                    var myModal = new bootstrap.Modal(document.getElementById('myModal'));
                    myModal.show();
                }
            });
        });

        // addToCart function
        function addToCart(itemId) {
            var cart = getShoppingCart();
            if (cart[itemId]) {
                cart[itemId] += 1;
            } else {
                cart[itemId] = 1;
            }
            setShoppingCart(cart);
        }

        // Functions to get and set the cart in a cookie
        function getShoppingCart() {
            var cart = getCookie('shopping_cart');
            return cart ? JSON.parse(cart) : {};
        }

        function setShoppingCart(cart) {
            setCookie('shopping_cart', JSON.stringify(cart), 7);
        }

        function setCookie(name, value, daysToLive) {
            var date = new Date();
            date.setTime(date.getTime() + (daysToLive * 24 * 60 * 60 * 1000));
            var expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i].trim();
                if (c.indexOf(name + "=") === 0) {
                    return c.substring(name.length + 1, c.length);
                }
            }
            return "";
        }
    </script>

</head>
<body class="bg-light min-vh-100">
    <?php generate_header(); ?>
    <div class="container min-vw-75 min-vh-100 bg-white shadow-lg pt-3">
        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" id="searchItems" class="form-control" placeholder="Search items...">
                </div>
                <div class="col-md-6">
                    <select id="sortSelect" class="form-select">
                        <option value="default" <?php echo ($sortOrder === 'default') ? 'selected' : ''; ?>>Sort By...</option>
                        <option value="price_low_high" <?php echo ($sortOrder === 'price_low_high') ? 'selected' : ''; ?>>Price Low to High</option>
                        <option value="price_high_low" <?php echo ($sortOrder === 'price_high_low') ? 'selected' : ''; ?>>Price High to Low</option>
                    </select>
                </div>
            </div>

            <div class="row" id="itemContainer">
                <!-- JavaScript dynamically fills this container with items -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Item added to cart!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Click the 'Close' button to continue!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>

                </div>
            </div>
        </div>
    </div>

    <?php generate_footer(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
