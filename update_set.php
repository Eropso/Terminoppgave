<?php
session_start();
include("database.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $set_id = filter_input(INPUT_POST, 'set_id', FILTER_SANITIZE_NUMBER_INT);
    $field = filter_input(INPUT_POST, 'field', FILTER_SANITIZE_STRING);
    $value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_SPECIAL_CHARS);

    if (in_array($field, ['weight', 'reps', 'note'])) {
        // Update the specific field in the database
        $sql = "UPDATE sets SET $field = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $value, $set_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid field']);
    }
}
?>
