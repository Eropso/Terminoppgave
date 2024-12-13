<?php
session_start();
include("database.php");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

    <div class="container">
        <header class="header">
            <h1 class="title">Frequently Asked Questions</h1>
        </header>

        <div class="faq-container">
            <div class="faq-item">
                <h3>What is Opsofit?</h3>
                <p>Opsofit is a fitness tracking platform that helps users track their workouts, analyze their progress, and stay motivated. You can log your exercises, track your progress over time, and visualize data like workout frequency through interactive charts.</p>
            </div>

            <div class="faq-item">
                <h3>How do I log a workout?</h3>
                <p>After logging into your account, navigate to the 'Tracker' section. There, you can input your workout details, including exercises, sets, reps, and weights. Once you enter your data, it will be saved automatically and updated on your dashboard.</p>
            </div>

            <div class="faq-item">
                <h3>How is my workout data displayed?</h3>
                <p>Your workout data is displayed in easy-to-read charts that track your progress over time. You can see how many workouts you’ve completed each month or week, and view trends in your workout habits to help you stay on track with your goals.</p>
            </div>

            <div class="faq-item">
                <h3>How can I track my workout frequency?</h3>
                <p>Opsofit tracks your workout frequency by recording each day you log a workout. This data is displayed on a graph, allowing you to see how often you train. You can view this information by navigating to the dashboard or workout frequency chart.</p>
            </div>

            <div class="faq-item">
                <h3>What happens if I miss a workout?</h3>
                <p>Don't worry! Life can get busy. If you miss a workout, simply log your next workout as usual, and it will be tracked. Consistency is key, but it's okay to have some off days.</p>
            </div>

            <div class="faq-item">
                <h3>Can I see my progress over time?</h3>
                <p>Yes! Opsofit generates charts that show your workout progress over time, allowing you to see how you're improving. You can track things like workout frequency, the number of sets/reps, and weights lifted.</p>
            </div>

            <div class="faq-item">
                <h3>Do I need to pay to use Opsofit?</h3>
                <p>Currently, Opsofit is free to use! All users can access the basic tracking and visualization features without any costs. Stay tuned for future updates regarding premium features.</p>
            </div>

            <div class="faq-item">
                <h3>How do I contact support?</h3>
                <p>If you have any questions or issues, you can contact us at <a href="mailto:phpkuben@gmail.com">phpkuben@gmail.com</a>. We're happy to assist you!</p>
            </div>
        </div>
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