<?php
$host = 'localhost';
$dbname = 'image_to_text_db'; // Replace with your actual DB name
$username = 'root';             // Use your DB username
$password = 'admin123';                 // Use your DB password ('' if no password)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
