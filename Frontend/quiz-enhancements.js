// This script should be added to your existing JavaScript file
document.addEventListener('DOMContentLoaded', function() {
    // Add data-option attributes to labels (A, B, C, D)
    function addOptionLetters() {
        const questions = document.querySelectorAll('.quiz-display h3');
        
        questions.forEach((question, qIndex) => {
            const options = question.nextElementSibling.querySelectorAll('.quiz-option');
            
            options.forEach((option, oIndex) => {
                const label = option.querySelector('label') || option;
                const letter = String.fromCharCode(65 + oIndex); // 65 = 'A'
                label.setAttribute('data-option', letter + '.');
            });
        });
    }
    
    // Function to show the correct answer when checked
    function setupAnswerReveal() {
        const checkButtons = document.querySelectorAll('.check-answer-btn');
        
        checkButtons.forEach(button => {
            button.addEventListener('click', function() {
                const questionContainer = this.closest('.question-container') || 
                                         this.parentElement;
                                         
                const correctAnswer = questionContainer.querySelector('.correct-answer');
                if (correctAnswer) {
                    correctAnswer.style.display = 'block';
                }
                
                // Highlight selected and correct options
                const options = questionContainer.querySelectorAll('.quiz-option');
                const correctOption = questionContainer.querySelector('.correct-option') || 
                                    Array.from(options).find(o => 
                                        o.textContent.includes(correctAnswer.textContent.replace('Correct Answer: ', '')));
                
                options.forEach(option => {
                    const radioInput = option.querySelector('input[type="radio"]');
                    
                    if (radioInput && radioInput.checked) {
                        if (option === correctOption || option.textContent.includes(correctAnswer.textContent.replace('Correct Answer: ', ''))) {
                            option.classList.add('correct-selection');
                        } else {
                            option.classList.add('wrong-selection');
                        }
                    } else if (option === correctOption || option.textContent.includes(correctAnswer.textContent.replace('Correct Answer: ', ''))) {
                        option.classList.add('correct-option');
                    }
                });
                
                // Disable the button after clicking
                this.disabled = true;
            });
        });
    }
    
    // Add highlight to selected options
    function setupOptionSelection() {
        const options = document.querySelectorAll('.quiz-option');
        
        options.forEach(option => {
            const radioInput = option.querySelector('input[type="radio"]');
            
            if (radioInput) {
                radioInput.addEventListener('change', function() {
                    // Remove selected class from all options in this question group
                    const name = this.getAttribute('name');
                    document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
                        input.closest('.quiz-option').classList.remove('selected-option');
                    });
                    
                    // Add selected class to this option
                    option.classList.add('selected-option');
                });
            } else {
                // If no radio input found, make the whole option clickable
                option.addEventListener('click', function() {
                    const questionContainer = this.closest('.question-container') || 
                                             this.parentElement;
                    
                    const options = questionContainer.querySelectorAll('.quiz-option');
                    options.forEach(opt => opt.classList.remove('selected-option'));
                    
                    option.classList.add('selected-option');
                    
                    // Find and check the radio input if it exists
                    const radio = option.querySelector('input[type="radio"]');
                    if (radio) radio.checked = true;
                });
            }
        });
    }
    
    // Initialize all enhancements
    function initQuizEnhancements() {
        addOptionLetters();
        setupAnswerReveal();
        setupOptionSelection();
    }
    
    // Call initialization or set it to run when quiz is generated
    // If quiz is generated dynamically, you might need to call this 
    // function after your quiz generation completes
    initQuizEnhancements();
    
    // For dynamically generated quizzes, you might need to add a 
    // mutation observer or hook into your quiz generation function
    // Example:
    // document.addEventListener('quizGenerated', initQuizEnhancements);
});