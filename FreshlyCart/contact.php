<?php
include("functions.php");
include("components/header.php");
include("components/footer.php");
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

not_logged();

$_SESSION["active_page"] = "Contact";

$dblink = db_connect();

$sql = "SELECT item.*, image.image AS ImagePath 
        FROM item 
        LEFT JOIN image ON item.image_id = image.image_id::text";

$stmt = $dblink->prepare($sql);
$stmt->execute();
$items = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $items[] = $row;
}

$dblink = null; // Close the PDO connection

// Generate the header (includes navigation bar)
generate_header();
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Contact</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./contact.css" />
    <script src="/js/contact.js"></script>
</head>

<body>
    <div class="container mt-5 pt-5 mb-5 pb-5">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
				<h1 style="margin-top: 75px;">Contact Us</h1>
                <p id="invalidList"></p>
                <form method="get">
                    <label for="fname">First name:</label><br>
                    <input type="text" id="fname" name="fname" minlength="2" required pattern="[a-zA-Z\-']+"><br>
                    <label for="lname">Last name:</label><br>
                    <input type="text" id="lname" name="lname" minlength="2" required pattern="[a-zA-Z\-']+"><br>
                    <fieldset>
                        <legend>Preferred method of communication:</legend>
                        <input type="radio" id="voice" name="comm" value="Voice">
                        <label for="voice">Voice</label><br>
                        <input type="radio" id="sms" name="comm" value="SMS">
                        <label for="sms">SMS</label><br>
                        <input type="radio" id="prefEmail" name="comm" value="Email">
                        <label for="email">Email</label>
                    </fieldset>
                    <label for="email">Email:</label><br>
                    <input type="email" id="email" name="email" required><br>
                    <label for="phone">Phone Number:</label><br>
                    <input type="text" id="phone" name="phone" disabled="true" required pattern="\(\d{3}\) \d{3}-\d{4}"><br>
                    <label for="message">Message:</label><br>
                    <textarea id="message" name="message" required></textarea><br>
                    <input type="submit" id="submit" value="Submit"><br>
                    <input type="reset" value="Reset">
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>

<?php
// Generate the footer
generate_footer();
?>
