<?php
session_start();
require_once 'config/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim(htmlspecialchars($_POST["email"]));
    $password = $_POST["password"];
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Both email and password are required";
        header("Location: login.php");
        exit();
    }
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                
                $_SESSION['success'] = "Congrats! Login successful.";
                header("Location: image_to_text.php");
                exit();
            } else {
                $_SESSION['error'] = "Invalid password. Please try again.";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Email not found. Please register.";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Login failed: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
}
?>