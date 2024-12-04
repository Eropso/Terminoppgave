<?php
session_start();
include("database.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle new workout day creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_workout_day'])) {
    $workout_date = filter_input(INPUT_POST, "workout_date", FILTER_SANITIZE_STRING);
    
    // Insert new workout day into the database
    $sql = "INSERT INTO workout_days (user_id, workout_date) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $workout_date);
    mysqli_stmt_execute($stmt);
}

// Handle workout logging
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $exercise = filter_input(INPUT_POST, "exercise", FILTER_SANITIZE_SPECIAL_CHARS);
    $sets = filter_input(INPUT_POST, "sets", FILTER_SANITIZE_NUMBER_INT);
    $reps = filter_input(INPUT_POST, "reps", FILTER_SANITIZE_NUMBER_INT);
    $weight = filter_input(INPUT_POST, "weight", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $workout_day_id = filter_input(INPUT_POST, "workout_day_id", FILTER_SANITIZE_NUMBER_INT);

    // Insert workout log into the database
    $sql = "INSERT INTO workout_logs (user_id, workout_day_id, exercise, sets, reps, weight) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisidd", $user_id, $workout_day_id, $exercise, $sets, $reps, $weight);
    mysqli_stmt_execute($stmt);
    
    echo "Workout logged successfully!";
}

// Handle deletion of workout logs
if (isset($_GET['delete_exercise'])) {
    $exercise_id = $_GET['delete_exercise'];
    $sql = "DELETE FROM workout_logs WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $exercise_id, $user_id);
    mysqli_stmt_execute($stmt);
}

// Handle deletion of workout day
if (isset($_GET['delete_day'])) {
    $day_id = $_GET['delete_day'];
    // Delete associated workout logs first (optional)
    $sql = "DELETE FROM workout_logs WHERE workout_day_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $day_id, $user_id);
    mysqli_stmt_execute($stmt);
    
    // Now delete the workout day
    $sql = "DELETE FROM workout_days WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $day_id, $user_id);
    mysqli_stmt_execute($stmt);
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
</head>
<body>
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
                <a href="?delete_day=<?php echo $workout_day['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if ($selected_workout_day_id): ?>
    <h2>Exercises for Selected Day</h2>
    <form method="POST" action="">
        <input type="hidden" name="workout_day_id" value="<?php echo $selected_workout_day_id; ?>">
        <label for="exercise">Exercise:</label>
        <input type="text" name="exercise" required>
        <label for="sets">Sets:</label>
        <input type="number" name="sets" required>
        <label for="reps">Reps:</label>
        <input type="number" name="reps" required>
        <label for="weight">Weight:</label>
        <input type="number" step="0.1" name="weight" required>
        <button type="submit" name="submit">Log Exercise</button>
    </form>

    <h3>Logged Exercises</h3>
    <table>
        <tr>
            <th>Exercise</th>
            <th>Sets</th>
            <th>Reps</th>
            <th>Weight</th>
            <th>Actions</th>
        </tr>
        <?php while ($exercise = mysqli_fetch_assoc($exercises_result)): ?>
        <tr>
            <td><?php echo $exercise['exercise']; ?></td>
            <td><?php echo $exercise['sets']; ?></td>
            <td><?php echo $exercise['reps']; ?></td>
            <td><?php echo $exercise['weight']; ?></td>
            <td>
                <a href="?delete_exercise=<?php echo $exercise['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php endif; ?>
</body>
</html>