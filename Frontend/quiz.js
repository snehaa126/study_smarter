// Function to convert text to speech
function convertTextToSpeech() {
    // Get the text and language
    const text = resultText.textContent || resultText.innerText;
    const language = languageSelect.value;
    
    if (!text || text.trim() === '' || text === 'Upload an image and click "Extract Text" to begin.' || text === 'Processing...') {
        alert('Please extract text from an image first.');
        return;
    }
    
    // Show loading state
    const originalButtonText = audioBtn.innerHTML;
    audioBtn.innerHTML = '<span class="audio-icon">⏳</span> Processing...';
    audioBtn.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('text', text);
    formData.append('language', language);
    
    // Send request to PHP script
    fetch('text_to_speech.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        // Reset button state
        audioBtn.innerHTML = originalButtonText;
        audioBtn.disabled = false;
        
        if (data.success) {
            // Show audio player
            audioPlayer.style.display = 'block';
            audioPlayer.src = data.audio_url;
            audioPlayer.play();
            
            // Create a download link
            const downloadContainer = document.createElement('div');
            downloadContainer.className = 'download-container';
            downloadContainer.style.marginTop = '10px';
            downloadContainer.style.textAlign = 'center';
            
            const downloadLink = document.createElement('a');
            downloadLink.href = data.audio_url;
            downloadLink.download = 'speech.mp3'; // Default filename
            downloadLink.className = 'download-btn';
            downloadLink.textContent = 'Download Audio';
            downloadLink.style.display = 'inline-block';
            downloadLink.style.padding = '8px 16px';
            downloadLink.style.backgroundColor = 'var(--color-pink)';
            downloadLink.style.color = 'white';
            downloadLink.style.borderRadius = '4px';
            downloadLink.style.textDecoration = 'none';
            
            // Remove existing download link if any
            const existingDownload = document.querySelector('.download-container');
            if (existingDownload) {
                existingDownload.remove();
            }
            
            downloadContainer.appendChild(downloadLink);
            audioPlayer.insertAdjacentElement('afterend', downloadContainer);
        } else if (data.use_browser_tts) {
            // Fallback to browser's Speech Synthesis
            useBrowserTTS(text, language);
        } else {
            alert('Failed to generate audio: ' + (data.message || 'Unknown error'));
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        console.error('Text-to-Speech Error:', error);
        // Try browser TTS as a fallback for any error
        useBrowserTTS(text, language);
        audioBtn.innerHTML = originalButtonText;
        audioBtn.disabled = false;
    });
}
// Function to use browser's Speech Synthesis API as fallback
function useBrowserTTS(text, language) {
    // Check if browser supports speech synthesis
    if (!('speechSynthesis' in window)) {
        alert("Sorry, your browser doesn't support text to speech!");
        return;
    }
    
    // Create utterance with the text
    const utterance = new SpeechSynthesisUtterance(text);
    
    // Map our language codes to BCP 47 language tags
    const langMap = {
        'en': 'en-US',
        'es': 'es-ES',
        'fr': 'fr-FR',
        'de': 'de-DE',
        'it': 'it-IT',
        'ja': 'ja-JP',
        'zh-CN': 'zh-CN',
        'ru': 'ru-RU',
        'ar': 'ar-SA',
        'hi': 'hi-IN'
    };
    
    // Set language
    utterance.lang = langMap[language] || 'en-US';
    
    // Get audio player container
    let browserAudioContainer = document.getElementById('browser-audio-container');
    
    // Create container if it doesn't exist
    if (!browserAudioContainer) {
        browserAudioContainer = document.createElement('div');
        browserAudioContainer.id = 'browser-audio-container';
        browserAudioContainer.className = 'browser-audio-container';
        browserAudioContainer.style.marginTop = '10px';
        browserAudioContainer.style.textAlign = 'center';
        document.querySelector('.result-box').appendChild(browserAudioContainer);
    }
    
    // Clear previous controls
    browserAudioContainer.innerHTML = '';
    
    // Create controls
    const controlsDiv = document.createElement('div');
    controlsDiv.className = 'tts-controls';
    
    // Create play button
    const playBtn = document.createElement('button');
    playBtn.textContent = 'Play Audio';
    playBtn.className = 'btn';
    playBtn.style.marginRight = '10px';
    playBtn.addEventListener('click', () => {
        if (window.speechSynthesis.speaking) {
            // Already speaking - do nothing
            return;
        }
        window.speechSynthesis.speak(utterance);
    });
    
    // Create pause button
    const pauseBtn = document.createElement('button');
    pauseBtn.textContent = 'Pause';
    pauseBtn.className = 'btn';
    pauseBtn.style.marginRight = '10px';
    pauseBtn.addEventListener('click', () => {
        if (window.speechSynthesis.speaking) {
            window.speechSynthesis.pause();
        }
    });
    
    // Create resume button
    const resumeBtn = document.createElement('button');
    resumeBtn.textContent = 'Resume';
    resumeBtn.className = 'btn';
    resumeBtn.style.marginRight = '10px';
    resumeBtn.addEventListener('click', () => {
        if (window.speechSynthesis.paused) {
            window.speechSynthesis.resume();
        }
    });
    
    // Create stop button
    const stopBtn = document.createElement('button');
    stopBtn.textContent = 'Stop';
    stopBtn.className = 'btn';
    stopBtn.addEventListener('click', () => {
        window.speechSynthesis.cancel();
    });
    
    // Add note about browser TTS
    const noteDiv = document.createElement('div');
    noteDiv.className = 'tts-note';
    noteDiv.textContent = 'Using browser text-to-speech (no download available)';
    noteDiv.style.marginTop = '10px';
    noteDiv.style.fontSize = '0.8em';
    noteDiv.style.color = '#666';
    
    // Add all elements to the controls
    controlsDiv.appendChild(playBtn);
    controlsDiv.appendChild(pauseBtn);
    controlsDiv.appendChild(resumeBtn);
    controlsDiv.appendChild(stopBtn);
    
    // Add controls and note to container
    browserAudioContainer.appendChild(controlsDiv);
    browserAudioContainer.appendChild(noteDiv);
    
    // Display the container
    browserAudioContainer.style.display = 'block';
    
    // Start speaking
    window.speechSynthesis.speak(utterance);
    
    // Add event listeners for utterance events
    utterance.onend = () => {
        console.log('Speech synthesis finished');
    };
    
    utterance.onerror = (event) => {
        console.error('Speech synthesis error:', event);
        alert('Error occurred during speech synthesis');
    };
}

// The audio button event listener
if (audioBtn) {
    audioBtn.addEventListener('click', convertTextToSpeech);
}