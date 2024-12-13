<?php
session_start();
include("database.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: authentication/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle new workout day creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_workout_day'])) {
    $workout_date = filter_input(INPUT_POST, "workout_date", FILTER_SANITIZE_STRING);
    
    $sql = "INSERT INTO workout_days (user_id, workout_date) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $workout_date);
    mysqli_stmt_execute($stmt);
}

// Handle workout logging (exercise insertion)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $exercise = filter_input(INPUT_POST, "exercise", FILTER_SANITIZE_SPECIAL_CHARS);
    $workout_day_id = filter_input(INPUT_POST, "workout_day_id", FILTER_SANITIZE_NUMBER_INT);

    // Insert workout log into the database
    $sql = "INSERT INTO workout_logs (user_id, workout_day_id, exercise) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $workout_day_id, $exercise);
    mysqli_stmt_execute($stmt);

    // Fetch exercises again after insertion
    $sql = "SELECT * FROM workout_logs WHERE workout_day_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $workout_day_id, $user_id);
    mysqli_stmt_execute($stmt);
    $exercises_result = mysqli_stmt_get_result($stmt);
}

// Handle saving the sets (insertion, not update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finish_sets'])) {
    $exercise_id = filter_input(INPUT_POST, "exercise_id", FILTER_SANITIZE_NUMBER_INT);

    foreach ($_POST['weight'] as $index => $weight) {
        $reps_value = $_POST['reps'][$index];
        $note_value = $_POST['note'][$index];

        // Get the next set_number for the exercise
        $sql = "SELECT MAX(set_number) AS max_set_number FROM sets WHERE exercise_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $exercise_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        $next_set_number = $row['max_set_number'] ? $row['max_set_number'] + 1 : 1;

        // Insert the new set
        $sql = "INSERT INTO sets (exercise_id, weight, reps, note, set_number) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "idisi", $exercise_id, $weight, $reps_value, $note_value, $next_set_number);
        mysqli_stmt_execute($stmt);
    }

    header("Location: tracker.php?success=1");
    exit();
}



// Fetch workout days
$sql = "SELECT * FROM workout_days WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$workout_days_result = mysqli_stmt_get_result($stmt);

// Check if a workout day is selected
$selected_workout_day_id = isset($_GET['workout_day_id']) ? $_GET['workout_day_id'] : null;

// Fetch exercises for the selected workout day
$exercises_result = [];
if ($selected_workout_day_id) {
    $sql = "SELECT * FROM workout_logs WHERE workout_day_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $selected_workout_day_id, $user_id);
    mysqli_stmt_execute($stmt);
    $exercises_result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workout Tracker</title>
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
    <div class="tracker-page">
        <h2>Add Workout Day</h2>
        <form method="POST" action="">
            <label for="workout_date">Workout Date:</label>
            <input type="date" name="workout_date" required>
            <button type="submit" name="add_workout_day">Add Workout Day</button>
        </form>

        <h2>Your Workout Days</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($workout_day = mysqli_fetch_assoc($workout_days_result)): ?>
            <tr>
                <td><?php echo $workout_day['workout_date']; ?></td>
                <td>
                    <a href="?workout_day_id=<?php echo $workout_day['id']; ?>">View Exercises</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if ($selected_workout_day_id): ?>
        <h2>Add Exercise</h2>
        <form method="POST" action="">
            <input type="hidden" name="workout_day_id" value="<?php echo $selected_workout_day_id; ?>">
            <label for="exercise">Exercise Name:</label>
            <input type="text" name="exercise" required>
            <button type="submit" name="submit">Add Exercise</button>
        </form>
        <h2>Exercises for Selected Day</h2>
        <table>
            <tr>
                <th>Exercise</th>
                <th>Actions</th>
            </tr>
            <?php while ($exercise = mysqli_fetch_assoc($exercises_result)): ?>
            <tr>
                <td><?php echo $exercise['exercise']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="exercise_id" value="<?php echo $exercise['id']; ?>">
                        <div id="sets-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Set</th>
                                        <th>Weight (kg)</th>
                                        <th>Reps</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM sets WHERE exercise_id = ?";
                                    $stmt = mysqli_prepare($conn, $sql);
                                    mysqli_stmt_bind_param($stmt, "i", $exercise['id']);
                                    mysqli_stmt_execute($stmt);
                                    $sets_result = mysqli_stmt_get_result($stmt);
                                    while ($set = mysqli_fetch_assoc($sets_result)): ?>
                                        <tr>
                                            <td>Set <?php echo $set['set_number']; ?></td>
                                            <td><input type="number" name="weight" data-set-id="<?php echo $set['id']; ?>" value="<?php echo $set['weight']; ?>" step="0.1" required style="width: 60px;" class="auto-save"></td>
                                            <td><input type="number" name="reps" data-set-id="<?php echo $set['id']; ?>" value="<?php echo $set['reps']; ?>" required style="width: 60px;" class="auto-save"></td>
                                            <td><input type="text" name="note" data-set-id="<?php echo $set['id']; ?>" value="<?php echo $set['note']; ?>" style="width: 200px;" class="auto-save"></td>
                                        </tr>
                                    <?php endwhile; ?>
                                    
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-set">Add Another Set</button>
                        <button type="submit" name="finish_sets">Finish</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html>
