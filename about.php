<?php
session_start();
include("database.php");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul class="sidebar">
            <li onclick=hideSidebar()><a href="#"><img src="images/close.svg" alt=""></a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="faq.php">FAQ</a></li>
            <li><a href="tracker.php">Tracker</a></li>
            <li><a href="mailto:phpkuben@gmail.com">Contact</a></li>
        </ul>
        
        <ul>
            <li><a class="opsofit-logo" href="index.php"><p>Opsofit</p></a></li>
            <li class="hideOnMobile"><a href="about.php">About</a></li>
            <li class="hideOnMobile"><a href="faq.php">FAQ</a></li>
            <li class="hideOnMobile"><a href="tracker.php">Tracker</a></li>

            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <div class="dropdown">
                    <img class="profile" src="images/defaultprofile.svg" alt="defaultprofile" onclick="myFunction()">
                    <div id="myDropdown" class="dropdown-content">
                        <a href="settings.php"><img src="images/settings.svg" alt="">Settings</a>
                        <a href="authentication/logout.php" class="logout-button"><img src="images/logout.svg" alt="">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="authentication/login.php" class="login-button"><img src="images/person_white.svg" alt="">Login</a>
            <?php endif; ?>            
            <li class="menu-button" onclick=showSidebar()><a href="#"><img src="images/menu.svg" alt=""></a></li>
        </ul>
    </nav>

    <div class="about_container">
        <h1>About</h1>
        <p>
            Opsofit is a fitness tracking platform that helps users track their workouts, analyze their progress, and stay motivated. You can log your exercises, track your progress over time, and visualize data like workout frequency through interactive charts.
        </p>
    </div>






    <footer>
        <p>&copy; Opsofit 2024</p>
        <div class="footer-info">
            <a href="#">PRIVACY POLICY</a>
            <a href="#">TERMS AND CONDITION</a>
            <a href="#">CONTACT US</a>
        </div>
    </footer>



    <script src="script.js"></script>
</body>
</html>