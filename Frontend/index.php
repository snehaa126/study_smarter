<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Smarter - Transform Images to Text</title>
    <link rel="stylesheet" href="home.css">
    <!-- Add Font Awesome for social media icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/SocialIcons/1.0.1/soc.min.js">
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            width: 100% !important;
            background-color: rgba(10, 10, 15, 0.95) !important;
            z-index: 1000 !important;
            height: 80px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
        }
        .hero-section {
            margin-top: 0 !important;
            padding: 80px 20px;
            background: linear-gradient(135deg, #0a0a0f 0%, #1a1a25 100%);
        }
        
        /* Updated hero section */
        .hero-text {
            max-width: 600px;
        }
        
        .hero-text h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #FF55C9, #FF8ADA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        
        .hero-text p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #e0e0e0;
        }
        
        .hero-badge {
            display: inline-block;
            background: rgba(255, 85, 201, 0.2);
            color: #FF55C9;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: #FF55C9;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        /* Improved step section with better spacing */
        .how-to-use {
            padding: 100px 20px;
            background-color: #0a0a0f;
        }
        
        .how-to-use h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #fff;
        }
        
        .how-to-use-intro {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 50px;
            color: #ccc;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .steps-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 80px;
            flex-wrap: wrap;
        }
        
        .step {
            flex: 0 0 30%;
            background: rgba(20, 20, 30, 0.5);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .step:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(255, 85, 201, 0.2);
        }
        
        .step-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #FF55C9, #FF8ADA);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            margin-bottom: 20px;
        }
        
        .step h3 {
            font-size: 1.4rem;
            margin: 0 0 15px 0;
            color: #fff;
        }
        
        .step p {
            font-size: 1rem;
            line-height: 1.5;
            color: #ccc;
            margin: 0;
        }
        
        /* Video section improvements */
        .tutorial-section-header {
            text-align: center;
            margin-bottom: 40px;
            
        }
        
        .tutorial-section-header h3 {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 15px;
        }
        
        .tutorial-section-header p {
            color: #ccc;
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.5;
        }
        
        .tutorial-video-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto 80px;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 2px solid #FF55C9; 
            border-radius: px; 
        }
        
        .tutorial-video {
            width: 100%;
            display: block;
            cursor: pointer;
        }
        
        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background-color: rgba(255, 85, 201, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .play-button:hover {
            background-color: #FF30BB;
            transform: translate(-50%, -50%) scale(1.1);
        }
        
        .play-button i {
            color: white;
            font-size: 32px;
            margin-left: 6px; /* Offset for play icon */
        }
        
        /* Rest of your styles remain the same */
        #contact-form input:focus, #contact-form textarea:focus {
            outline: none;
            border-color: #FF55C9;
            box-shadow: 0 0 8px rgba(255, 85, 201, 0.6);
        }

        #contact-form button:hover {
            background: #FF30BB;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(255, 85, 201, 0.5);
        }
        
        /* Smaller contact form styles */
        #contact-form {
            max-width: 400px; 
            padding: 20px; 
            background: #121217; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); 
            margin: 0 10px;
            position: relative;
        }
        
        #contact-form h3 {
            color: white; 
            text-align: center; 
            margin-bottom: 15px; 
            font-size: 22px;
        }
        
        #contact-form input {
            width: 100%; 
            padding: 8px; 
            border: 2px solid #FF55C9; 
            border-radius: 6px; 
            background: rgba(255, 255, 255, 0.9); 
            color: #000; 
            font-size: 16px; 
            box-sizing: border-box; 
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }
        
        #contact-form textarea {
            width: 100%; 
            padding: 8px; 
            border: 2px solid #FF55C9; 
            border-radius: 6px; 
            background: rgba(255, 255, 255, 0.9); 
            color: #000; 
            font-size: 16px; 
            min-height: 60px; 
            max-height: 60px;
            box-sizing: border-box; 
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }
        
        #contact-form button {
            width: 100%; 
            padding: 10px; 
            background: #FF55C9; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            box-shadow: 0 4px 8px rgba(255, 85, 201, 0.3);
        }
        
        /* Improved footer layout */
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .footer-logo {
            flex: 0 0 20%;
        }
        
        .footer-links {
            flex: 0 0 20%;
            display: flex;
            align-items: flex-start;
        }
        
        .footer-contact {
            flex: 0 0 40%;
        }
        
        .footer-social {
            flex: 0 0 15%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        
        .footer-column {
            display: flex;
            flex-direction: column;
        }
        
        .footer-column h4 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        /* Form submission message styles */
        .form-success-message {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(18, 18, 23, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.5s ease;
            z-index: 10;
        }
        
        .form-success-message.show {
            opacity: 1;
            visibility: visible;
        }
        
        .form-success-message h4 {
            color: white;
            font-size: 22px;
            margin-bottom: 10px;
        }
        
        .form-success-message p {
            color: #ccc;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .form-success-emoji {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        /* Video Tutorial Section Styles */
        .video-complete-message {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(10, 10, 15, 0.9);
            color: white;
            padding: 15px;
            text-align: center;
            transform: translateY(100%);
            transition: transform 0.5s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .video-complete-message.show {
            transform: translateY(0);
        }
        
        .video-complete-message h3 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        
        .video-complete-message p {
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        
        .video-cta {
            display: inline-block;
            padding: 10px 20px;
            background: #FF55C9;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 10px;
        }
        
        .video-cta:hover {
            background: #FF30BB;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(255, 85, 201, 0.5);
        }
        
        /* Features section enhancement */
        .features-section {
            padding: 100px 20px;
            background: linear-gradient(135deg, #0a0a0f 0%, #1a1a25 100%);
        }
        
        @media (max-width: 768px) {
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .steps-container {
                flex-direction: column;
            }
            
            .step {
                flex: 0 0 100%;
                margin-bottom: 30px;
            }
            
            .footer-logo, .footer-links, .footer-contact, .footer-social {
                flex: 0 0 100%;
                margin-bottom: 20px;
            }
            
            .footer-social {
                justify-content: center;
            }
            
            .tutorial-video-container {
                max-width: 95%;
            }
            
            .play-button {
                width: 60px;
                height: 60px;
            }
            
            .play-button i {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="logoo_new.png" alt="Study Smarter" class="logo-large">
            </div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#how-to-use">How to Use</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="login.php" class="btn btn-login">Login</a>
                <a href="login.php" class="btn btn-primary">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <span class="hero-badge">Next-Gen Study Tool</span>
                <h1>Transform Images into <br>Readable Text</h1>
                <div class="hero-subtitle">Save time. Study smarter. Achieve more.</div>
                <p> Experience powerful text extraction with cutting-edge technology. Convert images to text, generate summaries, translate effortlessly, listen to audio, and enhance learning with quizzes—all in one place.</p>
                <div class="hero-cta-group">
                    <a href="login.php" class="btn btn-primary">Start Converting</a>
                    <a href="#features" class="btn btn-secondary">Learn More</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">99%</span>
                        <span class="stat-label">Accuracy</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="image-container">
                    <img src="herooo.png" alt="Image to Text Conversion">
                </div>
            </div>
        </div>
    </section>

    <!-- How to Use Section -->
    <section id="how-to-use" class="how-to-use">
        <h2>How It Works</h2>
        <div class="how-to-use-intro">
            <p>Study Smarter makes learning from textbooks, handouts, and notes easier than ever. Our simple three-step process transforms any text image into digital content you can use in multiple ways.</p>
        </div>
        <div class="steps-container">
            <div class="step">
                <div class="step-icon">1</div>
                <h3>Upload Image</h3>
                <p>Select an image from your device or take a photo of your textbook, notes, or documents. Our platform supports all common image formats.</p>
            </div>
            <div class="step">
                <div class="step-icon">2</div>
                <h3>Extract Text</h3>
                <p>Our advanced technology instantly recognizes and extracts text from your image with near-perfect accuracy, even with handwritten notes.</p>
            </div>
            <div class="step">
                <div class="step-icon">3</div>
                <h3>Enhance & Use</h3>
                <p>Transform the extracted text with powerful features - create summaries, translate to other languages, convert to audio, or generate quizzes to test your knowledge.</p>
            </div>
        </div>
        
        <!-- New Tutorial Video Section with better spacing and text -->
        <div class="tutorial-section-header">
            <h3>See Study Smarter in Action</h3>
            <p>Watch our quick tutorial to see how easy it is to transform your learning experience with Study Smarter. This short video walks you through all the features of our platform.</p>
        </div>
        <div class="tutorial-video-container">
            <video id="tutorialVideo" class="tutorial-video" poster="video-thumbnail.jpg">
                <source src="tutorial-videoo.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="play-button" id="playButton">
                <i class="fas fa-play"></i>
            </div>
            <div class="video-complete-message" id="videoCompleteMessage">
                <h3>Awesome! You've Got It! 🎉</h3>
                <a href="login.php" class="video-cta">Try It Now!</a>
            </div>
        </div>
    </section>

<!-- Features Section -->
<section id="features" class="features-section">
    <h2>Powerful Features</h2>
    <div class="how-to-use-intro">
        <p>Study Smarter offers a suite of powerful tools designed to enhance your learning experience. Extract text from images is just the beginning - our platform helps you process and utilize that information in multiple ways.</p>
    </div>
    <div class="features-grid">
        <div class="feature">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <h3>Summarization</h3>
            <p>Get concise summaries of extracted text to quickly grasp key concepts and save study time</p>
        </div>
        <div class="feature">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>
            <h3>Translation</h3>
            <p>Translate text to multiple languages instantly, making learning accessible regardless of the original content language</p>
        </div>
        <div class="feature">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24">
                    <path d="M12 2a9 9 0 0 0-9 9c0 1.16.26 2.29.07 3.15L2 14.5 4.5 17H9v-3H7l-.5-1.5"></path>
                </svg>
            </div>
            <h3>Quiz Generation</h3>
            <p>Create interactive quizzes from any text to test your knowledge and reinforce your learning with active recall</p>
        </div>
        <div class="feature">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24">
                    <path d="M3 10v4h4l5 5V5L7 10H3zm13.5 2a2.5 2.5 0 0 1-1.5-4.5V7a4.5 4.5 0 0 0 0 9v-1.5a2.5 2.5 0 0 1 1.5-4.5z"></path>
                </svg>
            </div>
            <h3>Text to Audio</h3>
            <p>Convert text to clear, natural-sounding audio for auditory learning or studying on the go - perfect for multitasking</p>
        </div>
    </div>
</section>

    <!-- Footer -->
<footer id="contact">
    <div class="footer-content">
        <div class="footer-logo">
            <img src="logoo_new.png" alt="Study Smarter" class="footer-logo-large">
        </div>
        <div class="footer-links">
            <div class="footer-column">
                <h4>Sitemap</h4>
                <a href="#home">Home</a>
                <a href="#how-to-use">How to Use</a>
                <a href="#features">Features</a>
                <a href="image_to_text.php">Convert</a>
                <a href="#contact">Contact Us</a>
            </div>
        </div>
        
        <div class="footer-contact">
            <form id="contact-form" action="process_contact.php" method="POST">
                <h3>Get in Touch</h3>
                <div>
                    <input type="text" name="name" placeholder="Your name" required>
                </div>
                <div>
                    <input type="email" name="email" placeholder="Your email address" required>
                </div>
                <div>
                    <textarea name="message" placeholder="Type your message here..." required></textarea>
                </div>
                <button type="submit">Send Message</button>
                
                <!-- Success Message Overlay (Hidden by default) -->
                <div class="form-success-message" id="formSuccessMessage">
                    <div class="form-success-emoji">✅</div>
                    <h4>Yayy! Message Sent Successfully!</h4>
                    <p>Thanks for reaching out! We'll get back to you soon. 😊</p>
                </div>
            </form>
        </div>
        
        <div class="footer-social">
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Study Smarter. All Rights Reserved.</p>
        <div class="footer-social-mobile">
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
        </div>
    </div>
</footer>
    <script src="home.js"></script>
    
    <script>
        // Form submission handling with confirmation message
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            
            // Get form data
            const formData = new FormData(this);
            
            // Send form data via AJAX
            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Show success message regardless of response
                // In a production environment, you'd want to check the response
                const successMessage = document.getElementById('formSuccessMessage');
                successMessage.classList.add('show');
                
                // Reset the form
                this.reset();
                
                // Hide the success message after 3 seconds
                setTimeout(() => {
                    successMessage.classList.remove('show');
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                // You could show an error message here
            });
        });

        // Tutorial Video Player
        document.addEventListener('DOMContentLoaded', function() {
            const tutorialVideo = document.getElementById('tutorialVideo');
            const playButton = document.getElementById('playButton');
            const videoCompleteMessage = document.getElementById('videoCompleteMessage');
            
            // Play button click handler
            playButton.addEventListener('click', function() {
                tutorialVideo.play();
                playButton.style.display = 'none';
            });
            
            // Video play/pause toggle when clicking on video
            tutorialVideo.addEventListener('click', function() {
                if (tutorialVideo.paused) {
                    tutorialVideo.play();
                    playButton.style.display = 'none';
                } else {
                    tutorialVideo.pause();
                    playButton.style.display = 'flex';
                }
            });
            
            // Show completion message when video ends
            tutorialVideo.addEventListener('ended', function() {
                videoCompleteMessage.classList.add('show');
                
                // Hide the message after 10 seconds
                setTimeout(() => {
                    videoCompleteMessage.classList.remove('show');
                }, 10000);
            });
            
            // Reset video and UI when video is rewound
            tutorialVideo.addEventListener('seeked', function() {
                if (tutorialVideo.currentTime < 0.5) {
                    videoCompleteMessage.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
