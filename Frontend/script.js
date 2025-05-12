document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const cameraBtn = document.getElementById('camera-btn');
    const extractBtn = document.getElementById('extract-btn');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const resultText = document.getElementById('result-text');
    const uploadArea = document.getElementById('upload-area');
    
    // Action buttons
    const summarizeBtn = document.getElementById('summarize-btn');
    const quizBtn = document.getElementById('quiz-btn');
    const translateBtn = document.getElementById('translate-btn');
    const audioBtn = document.getElementById('audio-btn');
    
    // Translation elements
    const languageSelect = document.getElementById('language-select');
    
    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                previewContainer.style.display = 'block';
                extractBtn.disabled = false;
                
                // Upload the image to the server to prepare for extraction
                uploadImage(file);
            };
            
            reader.readAsDataURL(file);
        }
    });
    
    // Handle camera button
    cameraBtn.addEventListener('click', function() {
        // Check if the browser supports camera access
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Create elements for camera capture
            const videoElement = document.createElement('video');
            const canvasElement = document.createElement('canvas');
            const captureBtn = document.createElement('button');
            
            videoElement.style.width = '100%';
            videoElement.style.maxHeight = '200px';
            videoElement.style.marginBottom = '10px';
            
            captureBtn.innerText = 'Capture';
            captureBtn.className = 'btn upload-btn';
            captureBtn.style.marginBottom = '10px';
            
            // Clear upload area and add video elements
            uploadArea.innerHTML = '';
            uploadArea.appendChild(videoElement);
            uploadArea.appendChild(canvasElement);
            uploadArea.appendChild(captureBtn);
            
            // Start camera stream
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    videoElement.srcObject = stream;
                    videoElement.play();
                    
                    // Handle capture button click
                    captureBtn.addEventListener('click', function() {
                        canvasElement.width = videoElement.videoWidth;
                        canvasElement.height = videoElement.videoHeight;
                        canvasElement.getContext('2d').drawImage(videoElement, 0, 0);
                        
                        // Convert canvas to blob
                        canvasElement.toBlob(function(blob) {
                            // Stop the camera stream
                            stream.getTracks().forEach(track => track.stop());
                            
                            // Reset the upload area
                            uploadArea.innerHTML = '';
                            uploadArea.innerHTML = `
                                <p>Upload an image to extract text</p>
                                <div class="preview-container" id="preview-container">
                                    <img id="preview-image" src="${canvasElement.toDataURL()}" alt="Preview">
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
                            `;
                            
                            // Re-attach event listeners
                            document.getElementById('file-input').addEventListener('change', fileInput.onchange);
                            document.getElementById('camera-btn').addEventListener('click', cameraBtn.onclick);
                            
                            // Upload the captured image
                            const file = new File([blob], "camera-capture.jpg", { type: "image/jpeg" });
                            uploadImage(file);
                            
                            // Enable extract button
                            extractBtn.disabled = false;
                        }, 'image/jpeg');
                    });
                })
                .catch(function(error) {
                    alert('Error accessing camera: ' + error.message);
                    // Reset the upload area
                    location.reload();
                });
        } else {
            alert('Your browser does not support camera access.');
        }
    });
    
    // Handle extract button click
    extractBtn.addEventListener('click', function() {
        resultText.innerText = 'Processing...';
        
        // Send extraction request
        fetch('ocr.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultText.innerText = data.text || 'No text found in image.';
                
                // Enable action buttons if text was extracted
                if (data.text && data.text.trim() !== '') {
                    summarizeBtn.disabled = false;
                    quizBtn.disabled = false;
                    translateBtn.disabled = false;
                    audioBtn.disabled = false;
                }
            } else {
                resultText.innerText = 'An error occurred while processing your image. Please try again.';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultText.innerText = 'An error occurred while processing your image. Please try again.';
        });
    });
    
    // Function to upload image to server
    function uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);
        
        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Error uploading image.');
                extractBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error('Upload Error:', error);
            alert('Error uploading image.');
            extractBtn.disabled = true;
        });
    }
    
    // Handle summarize button
    summarizeBtn.addEventListener('click', function() {
        const textToSummarize = resultText.innerText.trim();
        
        if (textToSummarize === '' || textToSummarize === 'Processing...' || textToSummarize === 'No text found in image.') {
            alert('Please extract text first before summarizing.');
            return;
        }
        
        // Show loading state
        const originalButtonText = summarizeBtn.innerHTML;
        summarizeBtn.innerHTML = '<span class="summarize-icon">⏳</span> Processing...';
        summarizeBtn.disabled = true;
        
        // Send summarization request
        fetch('summarize.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `text=${encodeURIComponent(textToSummarize)}`
        })
        .then(response => response.json())
        .then(data => {
            summarizeBtn.innerHTML = originalButtonText;
            summarizeBtn.disabled = false;
            
            if (data.success) {
                // Get or create summary container
                let summaryContainer = document.getElementById('summary-container');
                
                // Format the summary to highlight key points
                let formattedSummary = data.summary;
                if (data.keyPoints && Array.isArray(data.keyPoints) && data.keyPoints.length > 0) {
                    formattedSummary += '<br><br><strong>Key Points:</strong><ul>';
                    data.keyPoints.forEach(point => {
                        formattedSummary += `<li>${point}</li>`;
                    });
                    formattedSummary += '</ul>';
                }
                
                // Update summary text and display it
                document.getElementById('summary-text').innerHTML = formattedSummary;
                summaryContainer.style.display = 'block';
            } else {
                alert('Summarization failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Summarization Error:', error);
            alert('An error occurred while summarizing text.');
            summarizeBtn.innerHTML = originalButtonText;
            summarizeBtn.disabled = false;
        });
    });
    
    // Handle quiz button
quizBtn.addEventListener('click', function() {
    const textToProcess = resultText.innerText.trim();
    
    if (textToProcess === '' || textToProcess === 'Processing...' || textToProcess === 'No text found in image.') {
        alert('Please extract text first before generating a quiz.');
        return;
    }
    
    // Show loading state
    const originalButtonText = quizBtn.innerHTML;
    quizBtn.innerHTML = '<span class="quiz-icon">⏳</span> Generating...';
    quizBtn.disabled = true;
    
    // Send quiz generation request
    fetch('generate_quiz.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `text=${encodeURIComponent(textToProcess)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        quizBtn.innerHTML = originalButtonText;
        quizBtn.disabled = false;
        
        if (data.success) {
            // Create or get quiz container
            let quizContainer = document.getElementById('quiz-container');
            if (!quizContainer) {
                quizContainer = document.createElement('div');
                quizContainer.id = 'quiz-container';
                quizContainer.className = 'result-container';
                
                // Create quiz header
                const quizHeader = document.createElement('h2');
                quizHeader.innerText = 'Quiz';
                quizContainer.appendChild(quizHeader);
                
                // Create quiz content container
                const quizContent = document.createElement('div');
                quizContent.id = 'quiz-content';
                quizContent.className = 'text-output quiz-display';
                quizContainer.appendChild(quizContent);
                
                // Add quiz container to the result section
                const resultSection = document.querySelector('.result-box');
                resultSection.appendChild(quizContainer);
            }
            
            // Update quiz content with the generated quiz
            const quizContent = document.getElementById('quiz-content');
            
            // Convert the quiz text to HTML with proper formatting
            const formattedQuiz = formatQuizText(data.quiz);
            quizContent.innerHTML = formattedQuiz;
            
            // Make quiz container visible
            quizContainer.style.display = 'block';
            
            // Add event listeners to quiz answers
            setupQuizInteraction();
        } else {
            alert('Quiz generation failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Quiz Generation Error:', error);
        alert('An error occurred while generating quiz: ' + error.message);
        quizBtn.innerHTML = originalButtonText;
        quizBtn.disabled = false;
    });
});

function formatQuizText(quizText) {
    // Replace markdown/text formatting with HTML
    let formattedText = quizText
        .replace(/##\s+(.*)/g, '<h2>$1</h2>')
        .replace(/###\s+(.*)/g, '<h3>$1</h3>')
        .replace(/\n\n/g, '<br>')
        .replace(/Question\s+(\d+):/g, '<strong>Question $1:</strong>');
    
    // Split into question sections for better processing
    const questionSections = formattedText.split('<h3>');
    let processedQuiz = questionSections[0]; // Keep the header
    
    // Process each question section
    for (let i = 1; i < questionSections.length; i++) {
        let section = questionSections[i];
        
        // Extract the correct answer before processing options
        const correctAnswerMatch = section.match(/Correct Answer:\s+([A-D])/);
        let correctAnswer = correctAnswerMatch ? correctAnswerMatch[1] : null;
        
        // Remove the correct answer line from the display for now
        section = section.replace(/<div class="correct-answer".*?<\/div>/g, '');
        section = section.replace(/Correct Answer:\s+[A-D](<br>)?/g, '');
        
        // Process the options - display one below the other without bullet points
        section = section.replace(/([A-D])\.\s+(.*?)(?=<br>[A-D]\.|<br>Correct Answer:|$)/gs, function(match, letter, option) {
            const id = `question-${i}-option-${letter.toLowerCase()}`;
            return `<div class="quiz-option">
                <div class="option-wrapper">
                    <input type="radio" id="${id}" name="question-${i}" value="${letter}">
                    <label for="${id}">${option.trim()}</label>
                </div>
            </div>`;
        });
        
        // Add hidden correct answer and feedback area
        if (correctAnswer) {
            section += `
            <div class="answer-feedback" style="display:none;">
                <div class="feedback-text"></div>
                <div class="correct-answer">The correct answer is: ${correctAnswer}</div>
            </div>`;
        }
        
        // Add check answer button
        section += `
        <div class="quiz-controls">
            <button class="btn check-answer-btn" data-question="${i}" data-correct="${correctAnswer}">Check Answer</button>
        </div>`;
        
        processedQuiz += '<h3>' + section;
    }
    
    return processedQuiz;
}
// Improved quiz interaction with feedback
function setupQuizInteraction() {
    // Add event listeners to all check answer buttons
    document.querySelectorAll('.check-answer-btn').forEach(button => {
        button.addEventListener('click', function() {
            const questionNum = this.getAttribute('data-question');
            const correctAnswer = this.getAttribute('data-correct');
            
            // Find the closest question section
            const questionSection = this.closest('div').parentElement;
            const selectedOption = questionSection.querySelector(`input[name="question-${questionNum}"]:checked`);
            const feedbackArea = questionSection.querySelector('.answer-feedback');
            const feedbackText = questionSection.querySelector('.feedback-text');
            
            if (!selectedOption) {
                alert('Please select an answer first!');
                return;
            }
            
            // Show the feedback area
            if (feedbackArea) {
                feedbackArea.style.display = 'block';
            }
            
            // Check if the selected answer is correct
            if (selectedOption.value === correctAnswer) {
                selectedOption.parentElement.classList.add('correct-selection');
                if (feedbackText) {
                    feedbackText.innerHTML = '<span style="color: green; font-weight: bold;">✓ Correct!</span>';
                    feedbackText.classList.add('correct-feedback');
                }
            } else {
                selectedOption.parentElement.classList.add('wrong-selection');
                if (feedbackText) {
                    feedbackText.innerHTML = '<span style="color: red; font-weight: bold;">✗ Incorrect</span>';
                    feedbackText.classList.add('wrong-feedback');
                }
                
                // Highlight the correct option
                const correctOptionId = `question-${questionNum}-option-${correctAnswer.toLowerCase()}`;
                const correctOptionEl = questionSection.querySelector('#' + correctOptionId);
                if (correctOptionEl) {
                    correctOptionEl.parentElement.classList.add('correct-option');
                }
            }
            
            // Disable all options for this question after answering
            questionSection.querySelectorAll(`input[name="question-${questionNum}"]`).forEach(input => {
                input.disabled = true;
            });
            
            // Disable the button after checking
            this.disabled = true;
            this.textContent = 'Answered';
        });
    });
}
    translateBtn.addEventListener('click', function() {
        const textToTranslate = resultText.innerText.trim();
        
        if (textToTranslate === '' || textToTranslate === 'Processing...' || textToTranslate === 'No text found in image.') {
            alert('Please extract text first before translating.');
            return;
        }
        
        const languageSelect = document.getElementById('language-select');
        if (!languageSelect) {
            console.error('Language select element not found');
            alert('Translation feature is currently unavailable.');
            return;
        }
        
        const targetLanguage = languageSelect.value;
        
        // Show loading state
        const originalButtonText = translateBtn.innerHTML;
        translateBtn.innerHTML = '<span class="translate-icon">⏳</span> Translating...';
        translateBtn.disabled = true;
        
        // Send translation request
        fetch('translate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `text=${encodeURIComponent(textToTranslate)}&target_lang=${encodeURIComponent(targetLanguage)}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            translateBtn.innerHTML = originalButtonText;
            translateBtn.disabled = false;
            
            if (data.success) {
                // Get or create translation container
                let translationContainer = document.getElementById('translation-container');
                if (!translationContainer) {
                    translationContainer = document.createElement('div');
                    translationContainer.id = 'translation-container';
                    translationContainer.className = 'result-container';
                    
                    const translationHeader = document.createElement('h2');
                    translationHeader.innerText = 'Translation';
                    translationContainer.appendChild(translationHeader);
                    
                    const translationText = document.createElement('div');
                    translationText.id = 'translation-text';
                    translationText.className = 'text-output';
                    translationContainer.appendChild(translationText);
                    
                    // Add translation container to the result section
                    const resultSection = document.querySelector('.result-box');
                    resultSection.appendChild(translationContainer);
                }
                
                // Display translation container
                translationContainer.style.display = 'block';
                
                // Update translation text
                document.getElementById('translation-text').innerText = data.translation;
            } else {
                alert('Translation failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Translation Error:', error);
            alert('An error occurred while translating text: ' + error.message);
            translateBtn.innerHTML = originalButtonText;
            translateBtn.disabled = false;
        });

    });
    audioBtn.addEventListener('click', function() {
        const textToSpeak = resultText.innerText.trim();
        
        if (textToSpeak === '' || textToSpeak === 'Processing...' || textToSpeak === 'No text found in image.') {
            alert('Please extract text first before converting to audio.');
            return;
        }
        
        // Show loading state
        const originalButtonText = audioBtn.innerHTML;
        audioBtn.innerHTML = '<span class="audio-icon">⏳</span> Processing...';
        audioBtn.disabled = true;
        
        // Get selected language for voice
        const languageSelect = document.getElementById('language-select');
        const voiceLanguage = languageSelect ? languageSelect.value : 'en';
        
        // Create or get audio container first to prevent UI jumps
        let audioContainer = document.getElementById('audio-container');
        if (!audioContainer) {
            audioContainer = document.createElement('div');
            audioContainer.id = 'audio-container';
            audioContainer.className = 'result-container';
            
            const audioHeader = document.createElement('h2');
            audioHeader.innerText = 'Audio';
            audioContainer.appendChild(audioHeader);
            
            const audioElementContainer = document.createElement('div');
            audioElementContainer.id = 'audio-element-container';
            audioElementContainer.className = 'audio-element-wrapper';
            audioContainer.appendChild(audioElementContainer);
            
            // Add audio container to the result section
            const resultSection = document.querySelector('.result-box');
            resultSection.appendChild(audioContainer);
        }
        
        // Show the container immediately with loading message
        audioContainer.style.display = 'block';
        document.getElementById('audio-element-container').innerHTML = '<div style="padding:15px;background:#333;color:#fff;border-radius:8px;">Preparing audio...</div>';
        
        // Skip server request and use browser TTS directly for better reliability
        audioBtn.innerHTML = originalButtonText;
        audioBtn.disabled = false;
        
        // Use the improved browser TTS function
        createBrowserTTSInterface(
            document.getElementById('audio-element-container'), 
            textToSpeak, 
            voiceLanguage
        );
        
        // Make sure container is visible and scroll to it
        audioContainer.style.display = 'block';
        audioContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
    
    function createBrowserTTSInterface(container, text, language, notice) {
        // Clear container
        container.innerHTML = '';
        
        // Create browser TTS wrapper with dark background
        const browserTTSWrapper = document.createElement('div');
        browserTTSWrapper.className = 'browser-tts-wrapper';
        browserTTSWrapper.style.padding = '15px';
        browserTTSWrapper.style.backgroundColor = '#2a2a2a'; // Dark background
        browserTTSWrapper.style.borderRadius = '8px';
        browserTTSWrapper.style.border = '1px solid #444';
        
        // Create visible message
        const browserTTSNotice = document.createElement('div');
        browserTTSNotice.className = 'browser-tts-notice';
        browserTTSNotice.textContent = notice || 'Using browser speech synthesis';
        browserTTSNotice.style.marginBottom = '10px';
        browserTTSNotice.style.fontStyle = 'italic';
        browserTTSNotice.style.color = notice ? '#ff6b6b' : '#ccc'; // Lighter text for dark background
        browserTTSNotice.style.fontWeight = '500';
        
        // Create text preview with dark theme
        const textDisplay = document.createElement('div');
        textDisplay.className = 'text-to-speech-text';
        textDisplay.style.marginBottom = '15px';
        textDisplay.style.maxHeight = '150px';
        textDisplay.style.overflow = 'auto';
        textDisplay.style.padding = '12px';
        textDisplay.style.border = '1px solid #555';
        textDisplay.style.borderRadius = '4px';
        textDisplay.style.backgroundColor = '#333'; // Dark background
        textDisplay.style.color = '#fff'; // White text
        textDisplay.textContent = text.substring(0, 300) + (text.length > 300 ? '...' : '');
        
        // Create buttons container
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'tts-buttons';
        buttonContainer.style.display = 'flex';
        buttonContainer.style.justifyContent = 'space-between';
        
        // Create play button
        const playButton = document.createElement('button');
        playButton.className = 'btn play-audio-btn';
        playButton.innerHTML = '<span>🔊</span> Play Text';
        playButton.style.flex = '1';
        playButton.style.marginRight = '10px';
        
        // Create save audio button
        const saveButton = document.createElement('button');
        saveButton.className = 'btn save-audio-btn';
        saveButton.innerHTML = '<span>⬇️</span> Save Audio';
        saveButton.style.flex = '1';
        saveButton.style.backgroundColor = '#4CAF50';
        saveButton.style.color = 'white';
        
        // Add stop button
        const stopButton = document.createElement('button');
        stopButton.className = 'btn stop-audio-btn';
        stopButton.innerHTML = '<span>⏹️</span> Stop';
        stopButton.style.marginLeft = '10px';
        
        // Text chunking function to handle long text
        function chunkText(text, maxLength = 200) {
            const chunks = [];
            let currentChunk = "";
            
            // Split by sentences to maintain natural breaks
            const sentences = text.split(/(?<=[.!?])\s+/);
            
            for (const sentence of sentences) {
                if (currentChunk.length + sentence.length > maxLength) {
                    chunks.push(currentChunk.trim());
                    currentChunk = sentence;
                } else {
                    currentChunk += (currentChunk ? " " : "") + sentence;
                }
            }
            
            if (currentChunk.trim()) {
                chunks.push(currentChunk.trim());
            }
            
            return chunks;
        }
        
        // Setup speech synthesis for play button with text chunking
        let currentChunkIndex = 0;
        let textChunks = [];
        let isSpeaking = false;
        
        // Setup speech synthesis for play button
        playButton.onclick = function() {
            // Make sure speech synthesis is available
            if (!window.speechSynthesis) {
                alert('Your browser does not support speech synthesis.');
                return;
            }
            
            // Cancel any ongoing speech
            window.speechSynthesis.cancel();
            
            if (isSpeaking) {
                // If already speaking, just stop
                isSpeaking = false;
                playButton.innerHTML = '<span>🔊</span> Play Text';
                return;
            }
            
            // Split text into manageable chunks
            textChunks = chunkText(text);
            currentChunkIndex = 0;
            isSpeaking = true;
            
            // Update button text
            playButton.innerHTML = '<span>⏳</span> Speaking...';
            playButton.disabled = true;
            
            // Begin speaking the first chunk
            speakNextChunk();
        };
        
        function speakNextChunk() {
            if (!isSpeaking || currentChunkIndex >= textChunks.length) {
                // Done speaking or stopped
                isSpeaking = false;
                playButton.innerHTML = '<span>🔊</span> Play Text';
                playButton.disabled = false;
                return;
            }
            
            const chunk = textChunks[currentChunkIndex];
            const utterance = new SpeechSynthesisUtterance(chunk);
            utterance.lang = language;
            
            // Get available voices
            let voices = window.speechSynthesis.getVoices();
            
            // If voices are available, try to find a matching one
            if (voices.length > 0) {
                // Try to find a voice that matches the language
                const voice = voices.find(v => v.lang.startsWith(language));
                if (voice) {
                    utterance.voice = voice;
                }
            }
            
            // When speech ends
            utterance.onend = function() {
                currentChunkIndex++;
                // Add a small pause between chunks for a more natural sound
                setTimeout(speakNextChunk, 250);
            };
            
            // If speech fails
            utterance.onerror = function(e) {
                console.error('Speech synthesis error:', e);
                isSpeaking = false;
                playButton.innerHTML = '<span>🔊</span> Play Text';
                playButton.disabled = false;
                alert('Speech synthesis failed. Please try again.');
            };
            
            // Start speaking
            window.speechSynthesis.speak(utterance);
        }
        
        // Setup stop button
        stopButton.onclick = function() {
            window.speechSynthesis.cancel();
            isSpeaking = false;
            playButton.innerHTML = '<span>🔊</span> Play Text';
            playButton.disabled = false;
        };
        
        // Setup save button
        saveButton.onclick = function() {
            // Create a downloadable text file with instructions
            const blob = new Blob([text], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            
            // Set download attributes
            link.href = url;
            link.download = 'speech_text_' + new Date().getTime() + '.txt';
            
            // Trigger download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Clean up
            URL.revokeObjectURL(url);
            
            // Show a helpful message
            const timestamp = new Date().toLocaleTimeString();
            saveButton.innerHTML = '<span>✓</span> Text Saved';
            setTimeout(() => {
                saveButton.innerHTML = '<span>⬇️</span> Save Audio';
            }, 3000);
            
            // Show instructions for using the saved text
            const instructionsDiv = document.createElement('div');
            instructionsDiv.className = 'save-instructions';
            instructionsDiv.style.marginTop = '10px';
            instructionsDiv.style.padding = '8px';
            instructionsDiv.style.backgroundColor = '#21321e'; // Darker green for dark theme
            instructionsDiv.style.border = '1px solid #2e4c2e';
            instructionsDiv.style.borderRadius = '4px';
            instructionsDiv.style.color = '#e8f5e9'; // Light text for dark background
            instructionsDiv.innerHTML = `
                <p style="margin: 0 0 5px 0; font-weight: bold;">Text saved successfully at ${timestamp}</p>
                <p style="margin: 0; font-size: 0.85em;">
                    Your browser cannot directly download the audio. Instead, we've saved the text that you can use with tools like:
                </p>
                <ul style="margin: 5px 0; font-size: 0.85em;">
                    <li>Google Text-to-Speech (https://cloud.google.com/text-to-speech)</li>
                    <li>Online text-to-speech converters</li>
                    <li>Desktop applications like Balabolka or Natural Reader</li>
                </ul>
            `;
            
            // Add instructions to container if they don't already exist
            if (!document.querySelector('.save-instructions')) {
                browserTTSWrapper.appendChild(instructionsDiv);
            }
        };
        
        // Add all elements to container
        browserTTSWrapper.appendChild(browserTTSNotice);
        browserTTSWrapper.appendChild(textDisplay);
        
        buttonContainer.appendChild(playButton);
        buttonContainer.appendChild(saveButton);
        buttonContainer.appendChild(stopButton);
        browserTTSWrapper.appendChild(buttonContainer);
        
        container.appendChild(browserTTSWrapper);
    }

// Replace the existing recordBrowserSpeech function with this improved version
// This function will be called when the user clicks the "Save Audio" button
function downloadSpeechText(text, language) {
    // Create a downloadable text file
    const blob = new Blob([text], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    
    // Set download attributes
    link.href = url;
    link.download = 'speech_text_' + new Date().getTime() + '.txt';
    
    // Trigger download
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Clean up
    URL.revokeObjectURL(url);
    
    // Show notification
    alert('Text saved successfully. You can use this text with any text-to-speech tool.');
}
});