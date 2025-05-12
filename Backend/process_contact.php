<?php
// Include database connection
require_once 'config/db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            
            // Set success message
            $success = "Thank you! Your message has been sent successfully.";
            
            // Optional: Redirect back to home page with success parameter
            header("Location: home1.php?contact_success=true#contact");
            exit();
            
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
    
    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $error_string = implode(",", $errors);
        header("Location: home1.php?contact_error=" . urlencode($error_string) . "#contact");
        exit();
    }
}
?>