<?php
session_start();
include("database.php");

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle 2FA toggle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_2fa'])) {
    $user_id = $_SESSION['user_id'];
    $current_status = $_POST['current_status'];

    // Toggle the 2FA status
    $new_status = $current_status == 1 ? 0 : 1;

    // Update the database
    $sql = "UPDATE users SET is_2fa_enabled = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $new_status, $user_id);
    mysqli_stmt_execute($stmt);

    // Update the session variable
    $_SESSION['is_2fa_enabled'] = $new_status;

    // Redirect to the same page to see the changes
    header("Location: settings.php");
    exit();
}

// Fetch the current 2FA status
$user_id = $_SESSION['user_id'];
$sql = "SELECT is_2fa_enabled FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$current_2fa_status = $user['is_2fa_enabled'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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

    <h2>Settings</h2>
    <form method="POST" action="">
        <label for="2fa">Two-Factor Authentication:</label>
        <input type="hidden" name="current_status" value="<?php echo $current_2fa_status; ?>">
        <button type="submit" name="toggle_2fa">
            <?php echo $current_2fa_status ? 'Disable 2FA' : 'Enable 2FA'; ?>
        </button>
    </form>

    <script src="script.js"></script>
</body>
</html>