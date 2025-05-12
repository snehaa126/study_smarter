<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TextVision</title>
    <link rel="stylesheet" href="home.css">
    <style>
        
        .login-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-primary);
            padding: 100px 20px;
        }

        .login-container {
            background-color: var(--bg-secondary);
            border-radius: 10px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .login-container h2 {
            text-align: center;
            color: var(--color-pink);
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-secondary);
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--text-secondary);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--color-pink);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--text-secondary);
        }

        .login-footer a {
            color: var(--color-pink);
            text-decoration: none;
            margin-left: 5px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-error {
            background-color: rgba(255, 0, 0, 0.1);
            color: #ff3333;
            border: 1px solid #ff3333;
        }
        
        .alert-success {
            background-color: rgba(0, 255, 0, 0.1);
            color: #33cc33;
            border: 1px solid #33cc33;
        }
    </style>
</head>
<body>

    <!-- Login Section -->
    <section class="login-section">
        <div class="login-container">
            <h2>Login to study Smarter</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="log_db.php" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary btn-submit">Log In</button>
            </form>
            <div class="login-footer">
                Don't have an account? 
                <a href="register.php">Register here</a>
            </div>
        </div>
    </section>
</body>
</html>