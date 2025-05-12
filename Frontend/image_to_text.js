document.addEventListener('DOMContentLoaded', () => {
    const fileUploadBtn = document.getElementById('fileUploadBtn');
    const cameraBtn = document.getElementById('cameraBtn');
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const removeImageBtn = document.getElementById('removeImage');
    const convertBtn = document.getElementById('convertBtn');
    const extractedText = document.getElementById('extractedText');

    // Simulated OCR API function
    async function performOCR(imageFile) {
        return new Promise((resolve, reject) => {
            // Simulate OCR processing time
            setTimeout(() => {
                // This would typically use an actual OCR API in a real application
                const sampleTexts = [
                    "TextVision is an advanced image-to-text conversion tool that uses AI to extract text from various image types.",
                    "Modern optical character recognition (OCR) technology enables quick and accurate text extraction from images.",
                    "Artificial intelligence has revolutionized the way we process and understand visual information.",
                    "Machine learning algorithms continue to improve text recognition accuracy across multiple languages and fonts."
                ];
                
                const randomText = sampleTexts[Math.floor(Math.random() * sampleTexts.length)];
                resolve(randomText);
            }, 1500); // Simulate 1.5 second processing
        });
    }

    // File Upload Button
    fileUploadBtn.addEventListener('click', () => {
        imageUpload.click();
    });

    // Image Upload Handler
    imageUpload.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                previewImage.src = event.target.result;
                imagePreview.style.display = 'block';
                convertBtn.disabled = false;
                convertBtn.textContent = 'Get Text';
            };
            reader.readAsDataURL(file);
        }
    });

    // Camera Button (Simulate File Upload for now)
    cameraBtn.addEventListener('click', () => {
        // In a real app, this would use device camera
        alert('Camera functionality will be implemented in a full version.');
    });

    // Remove Image
    removeImageBtn.addEventListener('click', () => {
        previewImage.src = '';
        imagePreview.style.display = 'none';
        imageUpload.value = ''; // Clear file input
        convertBtn.disabled = true;
        convertBtn.textContent = 'Upload Image First';
        extractedText.value = '';
    });

    // Convert Button Handler
    convertBtn.addEventListener('click', async () => {
        if (!previewImage.src) {
            alert('Please upload an image first.');
            return;
        }

        // Disable convert button and show loading state
        convertBtn.disabled = true;
        convertBtn.textContent = 'Processing...';

        try {
            // Convert image to file for OCR
            const response = await fetch(previewImage.src);
            const blob = await response.blob();
            const file = new File([blob], 'uploaded-image.png', { type: 'image/png' });

            // Perform OCR
            const extractedContent = await performOCR(file);

            // Update text area
            extractedText.value = extractedContent;

            // Re-enable convert button
            convertBtn.textContent = 'Text Extracted';
        } catch (error) {
            console.error('OCR Processing Error:', error);
            alert('Failed to extract text. Please try again.');
            convertBtn.textContent = 'Get Text';
        } finally {
            // Re-enable convert button after short delay
            setTimeout(() => {
                convertBtn.disabled = false;
                convertBtn.textContent = 'Get Text';
            }, 2000);
        }
    });

    // Action Buttons
    const summarizeBtn = document.getElementById('summarizeBtn');
    const quizBtn = document.getElementById('quizBtn');
    const translateBtn = document.getElementById('translateBtn');
    const videoBtn = document.getElementById('videoBtn');

    // Disable action buttons initially
    [summarizeBtn, quizBtn, translateBtn, videoBtn].forEach(btn => {
        btn.disabled = true;
    });

    // Text Processing Utilities
    function processButtonAction(actionType) {
        const text = extractedText.value.trim();
        
        if (!text) {
            alert('No text available. Please extract text first.');
            return;
        }

        // Disable all buttons during processing
        [summarizeBtn, quizBtn, translateBtn, videoBtn].forEach(btn => {
            btn.disabled = true;
        });

        // Simulate processing with a delay
        return new Promise((resolve) => {
            setTimeout(() => {
                let result;
                switch(actionType) {
                    case 'summarize':
                        result = simulateSummarization(text);
                        break;
                    case 'quiz':
                        result = simulateQuizGeneration(text);
                        break;
                    case 'translate':
                        result = simulateTranslation(text);
                        break;
                    case 'video':
                        result = 'Text-to-Video feature coming soon!';
                        break;
                }

                // Re-enable buttons
                [summarizeBtn, quizBtn, translateBtn, videoBtn].forEach(btn => {
                    btn.disabled = false;
                });

                resolve(result);
            }, 1000);
        });
    }

    // Attach event listeners to action buttons
    summarizeBtn.addEventListener('click', async () => {
        const summary = await processButtonAction('summarize');
        extractedText.value = summary;
    });

    quizBtn.addEventListener('click', async () => {
        const quiz = await processButtonAction('quiz');
        extractedText.value = quiz;
    });

    translateBtn.addEventListener('click', async () => {
        const translation = await processButtonAction('translate');
        extractedText.value = translation;
    });

    videoBtn.addEventListener('click', async () => {
        const videoMessage = await processButtonAction('video');
        alert(videoMessage);
    });

    // Simulation Functions
    function simulateSummarization(text) {
        const sentences = text.split(/[.!?]+/).filter(s => s.trim().length > 0);
        const summary = sentences.slice(0, Math.min(2, sentences.length)).join('. ') + '.';
        return `Summary: ${summary}\n\n(AI-generated summary)`;
    }

    function simulateQuizGeneration(text) {
        const sentences = text.split(/[.!?]+/).filter(s => s.trim().length > 0);
        if (sentences.length === 0) return 'Could not generate quiz from the text.';

        const quizQuestions = [
            `Multiple Choice Question:\nWhich of the following best describes the main idea?\n1) ${sentences[0]}\n2) ${sentences[Math.min(1, sentences.length-1)]}\n3) Unknown\n`,
            `True/False Question:\n"${sentences[0]}" is an accurate statement.\nTrue / False\n`
        ];

        return `Quiz Generated:\n\n${quizQuestions.join('\n\n')}`;
    }

    function simulateTranslation(text) {
        const languages = ['Spanish', 'French', 'German', 'Chinese'];
        const selectedLanguage = languages[Math.floor(Math.random() * languages.length)];
        
        // Very basic translation simulation
        const translations = {
            'Spanish': text.replace(/\b(the|of|and)\b/g, 'de'),
            'French': text.replace(/\b(the|of|and)\b/g, 'du'),
            'German': text.replace(/\b(the|of|and)\b/g, 'der'),
            'Chinese': text.replace(/\b(the|of|and)\b/g, '的')
        };

        return `Translated to ${selectedLanguage}:\n${translations[selectedLanguage]}`;
    }

    // Initial state setup
    convertBtn.disabled = true;
    convertBtn.textContent = 'Upload Image First';
});