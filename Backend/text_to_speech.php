<?php
// Disable all warnings and notices
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

// Include configuration file
require_once 'config/config.php';
// Start session
session_start();

// Clean any output that might have been generated
ob_clean();

// Set correct content type
header('Content-Type: application/json');

// Get the text from POST request
$text = isset($_POST['text']) ? $_POST['text'] : '';
$language = isset($_POST['language']) ? $_POST['language'] : 'en';

// Validate input
if (empty($text)) {
    echo json_encode([
        'success' => false,
        'message' => 'No text provided'
    ]);
    exit;
}

// Debug flag - set to true while debugging, false in production
$debug = false;

// Create audio directory if it doesn't exist
$audioDir = 'audio/';
if (!is_dir($audioDir)) {
    mkdir($audioDir, 0777, true);
}

// Generate unique filename
$timestamp = time();
$filename = 'speech_' . $timestamp . '.mp3';
$audioPath = $audioDir . $filename;

// Try different TTS methods in order of preference
$success = false;

// Try using eSpeak as primary method
$success = generateWithEspeak($text, $language, $audioDir, $timestamp, $audioPath);
$api_method = 'espeak';

// Get full URL to audio file
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$fullAudioUrl = $protocol . "://" . $host . $path . "/" . $audioPath;

// If server-side TTS was successful
if ($success) {
    // Return success response with audio URL
    echo json_encode([
        'success' => true,
        'message' => 'Audio generated successfully',
        'audio_url' => $fullAudioUrl,
        'api_method' => $api_method,
        'download_filename' => 'text_to_speech_' . date('Y-m-d') . '.mp3',
        'use_browser_tts' => false
    ]);
} else {
    // Fallback to browser TTS
    echo json_encode([
        'success' => true,
        'use_browser_tts' => true,
        'message' => 'Using browser text-to-speech'
    ]);
}
exit;

// Function to generate audio with eSpeak
function generateWithEspeak($text, $language, $audioDir, $timestamp, $audioPath) {
    global $debug;
    
    // Limit text length to prevent command line issues
    if (strlen($text) > 5000) {
        $text = substr($text, 0, 5000) . '...';
    }
    
    // Escape special characters to prevent command injection
    $safeText = escapeshellarg($text);
    
    // Set eSpeak parameters based on language
    $voiceOptions = '';
    switch ($language) {
        case 'es': // Spanish
            $voiceOptions = '-v es';
            break;
        case 'fr': // French
            $voiceOptions = '-v fr';
            break;
        case 'de': // German
            $voiceOptions = '-v de';
            break;
        case 'it': // Italian
            $voiceOptions = '-v it';
            break;
        case 'ja': // Japanese
            $voiceOptions = '-v mb-jp1';
            break;
        case 'zh-CN': // Chinese
            $voiceOptions = '-v zh';
            break;
        case 'ru': // Russian
            $voiceOptions = '-v ru';
            break;
        case 'ar': // Arabic
            $voiceOptions = '-v ar';
            break;
        case 'hi': // Hindi
            $voiceOptions = '-v hi';
            break;
        default: // Default English
            $voiceOptions = '-v en';
    }
    
    // Options for eSpeak
    $speed = '150'; // Words per minute
    $pitchAdjustment = '50'; // 0-99
    
    // Create WAV first since eSpeak outputs WAV by default
    $wavFile = $audioDir . 'temp_' . $timestamp . '.wav';
    
    // First check if eSpeak is installed
    exec('which espeak 2>&1', $checkOutput, $checkReturnCode);
    
    if ($debug) {
        error_log("eSpeak check return code: $checkReturnCode");
    }
    
    if ($checkReturnCode !== 0) {
        if ($debug) {
            error_log("eSpeak is not installed or not accessible");
        }
        return false;
    }
    
    // Command to generate WAV file with eSpeak
    $cmd = "espeak $voiceOptions -s $speed -p $pitchAdjustment $safeText -w $wavFile 2>&1";
    
    if ($debug) {
        error_log("eSpeak command: $cmd");
    }
    
    // Execute the command
    exec($cmd, $output, $returnCode);
    
    if ($debug) {
        error_log("eSpeak output: " . implode("\n", $output));
        error_log("eSpeak return code: $returnCode");
    }
    
    // Check if WAV was created successfully
    if ($returnCode !== 0 || !file_exists($wavFile)) {
        // Log error
        if ($debug) {
            error_log("eSpeak Error: " . implode("\n", $output));
        }
        return false;
    }
    
    // Convert WAV to MP3 using FFmpeg (if FFmpeg is installed)
    exec('which ffmpeg 2>&1', $ffmpegOutput, $ffmpegReturnCode);
    $ffmpegInstalled = ($ffmpegReturnCode === 0);
    
    if ($ffmpegInstalled) {
        $cmd = "ffmpeg -i $wavFile -acodec libmp3lame -q:a 2 $audioPath 2>&1";
        
        if ($debug) {
            error_log("FFmpeg command: $cmd");
        }
        
        exec($cmd, $output, $returnCode);
        
        if ($debug) {
            error_log("FFmpeg output: " . implode("\n", $output));
            error_log("FFmpeg return code: $returnCode");
        }
        
        // Delete temporary WAV file
        if (file_exists($wavFile)) {
            unlink($wavFile);
        }
        
        // Check if MP3 was created
        if ($returnCode !== 0 || !file_exists($audioPath)) {
            // Fallback to WAV if MP3 conversion fails
            rename($wavFile, $audioPath);
        } else {
            return true;
        }
    } else {
        // Just use WAV if FFmpeg is not available
        rename($wavFile, $audioPath);
        
        if ($debug) {
            error_log("FFmpeg not installed, using WAV file instead");
        }
    }
    
    return file_exists($audioPath);
}
?>