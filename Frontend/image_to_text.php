<?php
// Start session to store uploaded file info
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image to Text Converter</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="quiz-enhancements.css">
    <!-- Add Font Awesome for professional icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
    --bg-primary: #0a0a0f;
    --bg-secondary: #121217;
    --color-pink: #fc4cca;
    --color-pink-light: #ea0f93;
    --text-primary: #f4f4f4;
    --text-secondary: #a0a0a0;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Arial', sans-serif;
    background-color: var(--bg-primary);
    color: var(--text-primary);
    line-height: 1.6;
}

/* Navigation Styles */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: rgba(10, 10, 15, 0.9);
    backdrop-filter: blur(10px);
    z-index: 1000;
    height: 100px; /* Updated navbar height */
    display: flex;
    align-items: center;
}

.navbar-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    width: 100%;
}

.logo {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--color-pink);
}

.logo img.logo-large {
    max-height: 150px; /* Increased logo size */
    width: auto;
    transition: transform 0.3s ease;
}

.logo img.logo-large:hover {
    transform: scale(1.1);
}

.nav-menu {
    display: flex;
    list-style: none;
}

.nav-menu li {
    margin: 0 15px;
}

.nav-menu a {
    text-decoration: none;
    color: var(--text-primary);
    transition: color 0.3s ease;
}

.nav-menu a:hover {
    color: var(--color-pink);
}

/* User Info and Logout Button Styles */
.user-section {
    display: flex;
    align-items: center;
}

.user-info {
    margin-right: 15px;
    color: var(--text-secondary);
}

.user-info span {
    color: var(--color-pink);
    font-weight: bold;
}

.logout-btn {
    display: flex;
    align-items: center;
    background-color: transparent;
    color: var(--text-primary);
    border: 1px solid var(--color-pink);
    border-radius: 5px;
    padding: 8px 15px;
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background-color: var(--color-pink);
    color: white;
}

.logout-btn i {
    margin-right: 5px;
}
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="logoo_new.png" alt="Study smarter Logo" class="logo-large">
            </div>
            <ul class="nav-menu">
                <li><a href="home1.php">Home</a></li>
                <li><a href="home1.php">How to Use</a></li>
                <li><a href="home1.php">Features</a></li>
            </ul>
            <!-- Added User Info and Logout Button -->
            <div class="user-section">
                <?php if(isset($_SESSION['fullname'])): ?>
                <div class="user-info">
                    Welcome, <span><?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                </div>
                <?php endif; ?>
                <a href="logout.php" class="logout-btn" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="converter-box">
            <h1>Image to Text Converter</h1>
            <div class="upload-area" id="upload-area">
                <p>Upload an image to extract text</p>
                <div class="preview-container" id="preview-container" style="display: none;">
                    <img id="preview-image" src="" alt="Preview">
                </div>
                <div class="buttons-container">
                    <label for="file-input" class="btn upload-btn">
                        <span class="plus-icon">+</span> Upload Image
                        <input type="file" id="file-input" accept="image/*" style="display: none;">
                    </label>
                    <button class="btn camera-btn" id="camera-btn">
                        <span class="camera-icon">📷</span> Use Camera
                    </button>
                </div>
            </div>
            <button class="btn extract-btn" id="extract-btn" disabled>
                <span class="extract-icon">📄</span> Extract Text
            </button>
        </div>
        
        <div class="result-box">
            <h1>Extracted Text</h1>
            <div class="text-output" id="text-output">
                <p id="result-text">Upload an image and click "Extract Text" to begin.</p>
            </div>
            
            <!-- Language selection for translation -->
            <div class="language-selection">
                <select id="language-select" class="language-select">
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="fr">French</option>
                    <option value="de">German</option>
                    <option value="it">Italian</option>
                    <option value="ja">Japanese</option>
                    <option value="ko">Korean</option>
                    <option value="zh-CN">Chinese (Simplified)</option>
                    <option value="ru">Russian</option>
                    <option value="ar">Arabic</option>
                    <option value="hi">Hindi</option>
                </select>
            </div>
            
            <div class="action-buttons">
                <button class="action-btn summarize-btn" id="summarize-btn" disabled>
                    <span class="summarize-icon">💬</span> Summarize
                </button>
                <button class="action-btn quiz-btn" id="quiz-btn" disabled>
                    <span class="quiz-icon">💡</span> Generate Quiz
                </button>
                <button class="action-btn translate-btn" id="translate-btn" disabled>
                    <span class="translate-icon">🌐</span> Translate
                </button>
                <button class="action-btn audio-btn" id="audio-btn" disabled>
                    <span class="audio-icon">🔊</span> Text to Audio
                </button>
            </div>
            
            <!-- Results containers for translation and summary -->
            <div id="summary-container" class="result-container" style="display: none;">
                <h2>Summary</h2>
                <div id="summary-text" class="text-output"></div>
            </div>
            
            <div id="translation-container" class="result-container" style="display: none;">
                <h2>Translation</h2>
                <div id="translation-text" class="text-output"></div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Additional logout confirmation if needed
        document.getElementById('logout-btn').addEventListener('click', function(e) {
    
            
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        
        });
    </script>
</body>
</html>