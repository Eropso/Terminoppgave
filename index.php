<?php
session_start();
include("database.php");

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $user_id = $_SESSION['user_id']; 

    // Query to count workouts per week
    $query = "
        SELECT WEEK(workout_date) AS workout_week, YEAR(workout_date) AS workout_year, COUNT(*) AS workout_count
        FROM workout_days
        WHERE user_id = ?
        GROUP BY workout_year, workout_week
        ORDER BY workout_year DESC, workout_week DESC
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Arrays to store the weeks and workout counts
    $labels = [];
    $workout_counts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        // Format the week label as "Week X - Year"
        $labels[] = 'Week ' . $row['workout_week'] . ' - ' . $row['workout_year'];
        $workout_counts[] = $row['workout_count'];
    }

    // Prepare the chart data
    $chart_data = [
        'labels' => $labels,  
        'datasets' => [[
            'label' => 'Workouts Logged',
            'data' => $workout_counts,
            'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'borderWidth' => 2,
            'fill' => false 
        ]]
    ];

    $chart_data_json = json_encode($chart_data);
} else {
    $chart_data_json = json_encode([]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Hub</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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



    <div class="hero">
            <div class="hero_text">
                <h1 class="title_hero">
                    Track Your Workout Today
                </h1>


                <hr class="line_home">
                <ul class="short_about">
                    <li>Track Your Workout</li>
                    <li>Log Your Progress</li>
                    <li>Get Your Results</li>
                </ul>


                <a class="button_hero"href="tracker.php">
                    <button class="Join">Join</button>
                </a>
            </div>

            
            <div class="bodybuilder">
                <img src="images/bodybuilder.jpg" alt="bodybuilder">
            </div>

        </div>
    


    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>

        <div class="container">
            <div class="chart-container">
                <h2>Your Workout Frequency (Weekly)</h2>
                <canvas id="workoutFrequencyChart"></canvas>
            </div>
        </div>
    <?php endif; ?>            

    
    <script>
        const chartData = <?php echo $chart_data_json; ?>;

        if (chartData.labels && chartData.datasets) {
            const ctx = document.getElementById('workoutFrequencyChart').getContext('2d');

            const workoutFrequencyChart = new Chart(ctx, {
                type: 'line',  
                data: chartData,  
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Week/Year'
                            },
                            reverse: true 
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Workouts'
                            }
                        }
                    },
                    elements: {
                        line: {
                            tension: 0.3,
                            borderWidth: 2
                        },
                        point: {
                            radius: 5,
                            backgroundColor: 'rgba(75, 192, 192, 1)'
                        }
                    }
                }
            });
        }
    </script>

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
