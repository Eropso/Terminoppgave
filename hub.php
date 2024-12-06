<?php
session_start();
include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav>
        <ul class="sidebar">
            <li onclick=hideSidebar()><a href="#"><img src="close.svg" alt=""></a></li>
            <li><a href="herre.html">Herre</a></li>
            <li><a href="dame.html">Dame</a></li>
            <li><a href="mailto:eropsogt@gmail.com">Contact</a></li>
        </ul>
        
        <ul>
            <li><a class="opsofit-logo" href="hub.php"><p>Opsofit</p></a></li>
            <li class="hideOnMobile"><a href="#">About</a></li>
            <li class="hideOnMobile"><a href="#">FAQ</a></li>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <div class="dropdown">
                    <img class="profile" src="defaultprofile.svg" alt="defaultprofile" onclick="myFunction()">
                    <div id="myDropdown" class="dropdown-content">
                        <a href="logout.php" class="logout-button">Logout</a>
                        <a href="settings.php">Settings</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-button">Login</a>
            <?php endif; ?>            
            <li class="menu-button" onclick=showSidebar()><a href="#"><img src="menu.svg" alt=""></a></li>
        </ul>
    </nav>

    
    <div class="container">
        <header class="header">
            <h1 class="title">Opsofit</h1>

        </header>

        <a href="tracker.php">Tracker</a>
    </div>

    <script src="script.js"></script>

</body>
</html>
