<?php
// Start the session
session_start();

// Update the last_login time in the database
if (isset($_SESSION['user_id'])) {
    require_once 'config/db_connection.php';
    
    try {
        $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
    } catch (PDOException $e) {
        // Log error if needed
        // error_log("Logout database update failed: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: home1.php");
exit();
?>