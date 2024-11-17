<?php
function generate_nav_bar()
{
    $pages = array(
        "Items" => "items.php",
        "Shopping Cart" => "cart.php",
        "Checkout" => "checkout.php",
        "Orders" => "orders.php",
        "Account" => "account.php",
        "Logout" => "logout.php",
        "Contact" => "contact.php",
    );

    echo '<nav class="navbar navbar-expand-lg navbar-fixed-top bg-body tertiary bg-white shadow-lg border rounded" style="height: 60px;">
<div class="container-fluid d-flex justify-content-between">
    <a class="navbar-brand" href="home.php">Home</a>
    <div class="d-flex">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">';
    if ($_SESSION["role"] == "Admin") {
        if ("Admin" == $_SESSION["active_page"]) {
            echo "<li class='nav-item'>
                    <a class='nav-link active' href=admin.php>Admin</a>
                  </li>";
        } else {
            echo "<li class='nav-item'>
                    <a class='nav-link' href=admin.php>Admin</a>
                  </li>";
        }
    }
    foreach ($pages as $page => $url) {
        if ($page == $_SESSION["active_page"]) {
            echo "<li class='nav-item'>
                    <a class='nav-link active' href=\"$url\">$page</a>
                  </li>";
        } else {
            echo "<li class='nav-item'>
                    <a class='nav-link' href=\"$url\">$page</a>
                  </li>";
        }
    }
    echo "<li class='nav-item'> 
        <a class='navbar-brand' href='account.php'>";

if (isset($item['ImagePath']) && !empty($item['ImagePath'])) {
    echo "<img src='" . htmlspecialchars($item['ImagePath']) . "' alt='Item Image' height='50' width='50' class='rounded-circle'>";
} else {
    echo "<img src='images/smileyface.jpg' alt='Default Image' height='50' width='50' class='rounded-circle'>";
}

echo "  </a>
      </li>
    </ul>
  </div>
</div>
</div>
</nav>";

}
