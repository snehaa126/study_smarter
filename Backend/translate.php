<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = isset($_POST['text']) ? $_POST['text'] : '';
    $targetLang = isset($_POST['target_lang']) ? $_POST['target_lang'] : 'en'; // Match JavaScript parameter name

    if (empty($text)) {
        echo json_encode(["success" => false, "message" => "No text provided"]);
        exit;
    }

    // More reliable approach using cURL for Google Translate
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" . urlencode($targetLang) . "&dt=t&q=" . urlencode($text);

    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    
    // Execute cURL request
    $response = curl_exec($ch);
    $err = curl_error($ch);
    
    // Close cURL session
    curl_close($ch);

    if ($err) {
        echo json_encode(["success" => false, "message" => "cURL Error: " . $err]);
        exit;
    }

    if ($response) {
        $translatedData = json_decode($response, true);
        
        // Extract full translated text from Google's response format
        $translatedText = "";
        if (is_array($translatedData) && isset($translatedData[0])) {
            foreach ($translatedData[0] as $text_chunk) {
                if (is_array($text_chunk) && isset($text_chunk[0])) {
                    $translatedText .= $text_chunk[0];
                }
            }
        }
        
        if (empty($translatedText)) {
            echo json_encode(["success" => false, "message" => "Failed to parse translation response"]);
            exit;
        }
        
        echo json_encode([
            "success" => true,
            "translation" => $translatedText  // Match JavaScript expected property name
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Translation failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>