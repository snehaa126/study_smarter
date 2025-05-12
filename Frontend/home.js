document.addEventListener('DOMContentLoaded', () => {
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.style.backgroundColor = 'rgba(10, 10, 15, 0.95)';
            navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        } else {
            navbar.style.backgroundColor = 'rgba(10, 10, 15, 0.9)';
            navbar.style.boxShadow = 'none';
        }
    });

    // Feature hover effect
    const features = document.querySelectorAll('.feature');
    features.forEach(feature => {
        const icon = feature.querySelector('.feature-icon');
        feature.addEventListener('mouseenter', () => {
            icon.style.transform = 'rotate(360deg) scale(1.1)';
        });

        feature.addEventListener('mouseleave', () => {
            icon.style.transform = 'rotate(0deg) scale(1)';
        });
    });

    // Animated counter for stats
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start) + 
                (element.classList.contains('stat-number') ? '%' : '+');
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Trigger stat animations when in view
    const statsElements = document.querySelectorAll('.stat-number');
    const observerOptions = {
        threshold: 0.5
    };

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.textContent.includes('%')) {
                    animateValue(entry.target, 0, 99.9, 2000);
                } else {
                    animateValue(entry.target, 0, 1, 2000);
                }
                statsObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    statsElements.forEach(el => statsObserver.observe(el));
    
    // Contact form handling
    const contactForm = document.getElementById('contact-form');
    
    // Hide the success message after 5 seconds
    const successMessage = document.querySelector('.form-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }
    
    // Form validation
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            const nameInput = document.getElementById('contact-name');
            const emailInput = document.getElementById('contact-email');
            const messageInput = document.getElementById('contact-message');
            
            // Reset previous error states
            const errorElements = document.querySelectorAll('.input-error');
            errorElements.forEach(el => el.classList.remove('input-error'));
            
            // Validate name
            if (nameInput.value.trim() === '') {
                nameInput.classList.add('input-error');
                isValid = false;
            }
            
            // Validate email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value.trim())) {
                emailInput.classList.add('input-error');
                isValid = false;
            }
            
            // Validate message
            if (messageInput.value.trim() === '') {
                messageInput.classList.add('input-error');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});