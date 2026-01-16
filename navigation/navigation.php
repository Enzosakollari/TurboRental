<?php
require_once __DIR__ . '/../config/db.php';


// Check if there is an ID in the session
$userImage = '/Makina/images/profileImage.jpg';  // Default profile image

if (isset($_SESSION['id'])) {
    // Connect to the database (adjust your database connection parameters)
    // Create connection
    $conn = db_connect();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // include 'header.php';


    // Get the user ID from the session
    $userId = $_SESSION['id'];

    // Prepare and execute the query to get the user's profile image
    $sql = "SELECT profile_image FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId); // Bind the user ID to the query
    $stmt->execute();
    $stmt->bind_result($profileImage); // Bind the result (image path)

    // Check if the image is found
    if ($stmt->fetch() && !empty($profileImage)) {
        // If a profile image is found, update the profile image path
        $userImage = "/Makina/images/" . $profileImage;
    }
        

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Makina/navigation/css/header.css">
    
    <title></title>
</head>
<body>
    <div id="topBar">
            <div class="topbar-left">
                <span>+1 (555) 123-4567</span>
                <span>rentals@turborentals.com</span>
            </div>
            <div class="topbar-right">Open 24/7</div>
        </div>
        <div id="header">
            <div class="brand">
                <div class="brand-icon">
                    <img src="/Makina/images/logo.png" alt="Turbo Rentals logo">
                </div>
                <div class="brand-text">
                    <span>Turbo Rentals</span>
                    <small>Premium Rentals</small>
                </div>
            </div>
            <div id="navigationBar">
                <a id="home">Home</a>
                <a id="makinat">Vehicles</a>
                <a id="motorcycles">Motorcycles</a>
                <a id="rrethNesh">About Us</a>
                <a id="kontakto" >Contact</a>
                <a id="FAQ">FAQ</a>
            </div>
            <div class="header-actions">
                <a class="book-now" href="/Makina/makinat/makinat.php">Book Now</a>
                <div id="profileImageContainer">
                    <img id="profileImage" src="<?php echo $userImage; ?>" alt="Profile Image">
                </div>
            </div>
        </div>
</body>

<script>
            var userId;
            <?php if (isset($_SESSION['id'])): ?>
                userId = '<?php echo $_SESSION['id']; ?>';
            <?php else: ?>
                var userId = null;
            <?php endif; ?>

            document.getElementById("profileImage").addEventListener("click", function() {
                event.preventDefault();
                console.log(userId);

                if (!userId) {
                    window.location.href = "/Makina/registerLogin/login.html";  // Redirect to login page
                    
                } else {
                    window.location.href = "/Makina/profile/profile.php";  // Redirect to profile page
                }
            });

            document.addEventListener("DOMContentLoaded", () => {
                if (!userId) {
                    document.getElementById("makinat").href = "/Makina/registerLogin/login.html";
                    document.getElementById("motorcycles").href = "/Makina/registerLogin/login.html";
                    document.getElementById("rrethNesh").href = "/Makina/registerLogin/login.html";
                    document.getElementById("kontakto").href = "/Makina/registerLogin/login.html";
                    document.getElementById("FAQ").href = "/Makina/registerLogin/login.html";

                } else {
                    document.getElementById("makinat").href = "/Makina/makinat/makinat.php";
                    document.getElementById("motorcycles").href = "/Makina/motorcycles/motorcycles.php";
                    document.getElementById("rrethNesh").href = "/Makina/navigation/rrethNesh.php";
                    document.getElementById("kontakto").href = "/Makina/navigation/kontakto.php";
                    document.getElementById("FAQ").href = "/Makina/navigation/pyetjeFAQ.php";
                }
                document.getElementById("home").href = "/Makina/navigation/index.php";

                
            });
        </script>

</html>
