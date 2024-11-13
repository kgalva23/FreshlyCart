<?php
include "nav_bar.php";

function generate_header()
{
    echo '<div class="container-fluid position-fixed top-0 bg-dark z-3 shadow pb-3">
        <div class="container w-75 ">
            <div class="col text-light">
                <a class="navbar-brand" href="/home.php">
                    <h1>Welcome to Freshly Cart</h1>
                </a>
            </div>
            <div class="col">';
    generate_nav_bar();
    echo '</div>
        </div>
    </div>';
}
