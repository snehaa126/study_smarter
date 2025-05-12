<?php
session_start();
require_once 'config/db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fullname = trim(htmlspecialchars($_POST["fullname"]));
    $email = trim(htmlspecialchars($_POST["email"]));
    $password = $_POST["password"];
    
    if (empty($fullname) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: register.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: register.php");
        exit();
    }
    
    if (strlen($password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long";
        header("Location: register.php");
        exit();
    }
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already exists. Please login.";
            header("Location: register.php");
            exit();
        }
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, created_at) VALUES (:fullname, :email, :password, NOW())");
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
        
        $_SESSION['success'] = "Registration successful! You can now login.";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>